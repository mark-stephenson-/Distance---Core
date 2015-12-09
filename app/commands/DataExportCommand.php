<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DataExportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'core:data-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all prase submission data.';

    /**
     * The directory where archives are stored.
     */
    protected $directory;

    /**
     * The directory where PID files are stored.
     */
    protected $pidDirectory;

    /**
     * The collection ID of the archive currently being created.
     */
    protected $appId;
    protected $collectionId;

    protected $records;

    /**
     * The data export file.
     */
    protected $export;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function fire($job = null, $data = null)
    {
        Log::debug('data-export', ['message' => 'Time limit: '.ini_get('max_execution_time')]);
        set_time_limit(0);
        Log::debug('data-export', ['message' => 'Time limit: '.ini_get('max_execution_time')]);

        $data['user-id'] = !empty($data['user-id']) ? $data['user-id'] : $this->argument('user-id');

        $skip = isset($data['skip']) ? ($data['skip'] ?: 0) : 0;
        $take = isset($data['take']) ? ($data['take'] ?: PRRecord::count()) : PRRecord::count();

        $this->records = PRRecord::orderBy('created_at', 'desc')->skip($skip)->take($take)->get();

        Log::debug('data-export', ['message' => 'Data export has been started.', 'job' => $job]);
        Log::debug('data-export', compact('data'));

        try {
            $qv = $this->questionnaireView();
            $elg = $this->enrolmentLogGood();
            $elc = $this->enrolmentLogConcern();
            $key = $this->key();

            Log::debug('data-export', ['message' => 'Data has been parsed into arrays.']);

            $this->appId = !empty($data['app-id']) ? $data['app-id'] : $this->argument('app-id');
            $this->collectionId = !empty($data['collection-id']) ? $data['collection-id'] : $this->argument('collection-id');
            $this->directory = storage_path().'/exports/'.$this->collectionId;
            $this->pidDirectory = $this->directory.'/pid';

            if (!is_dir($this->directory)) {
                mkdir($this->directory, 0777, true);
            }

            // Ensure the PID directory exists
            if (!is_dir($this->pidDirectory)) {
                mkdir($this->pidDirectory, 0777);
            }

            $pidFileName = $this->pidDirectory.'/'.getmypid();

            $pidFile = fopen($pidFileName, 'w');
            fclose($pidFile);

            // Clean up PID files when we exit/are complete
            register_shutdown_function(function () use ($pidFileName) {
                unlink($pidFileName);
            });

            // Make the zip archive
            $zip = new \ZipArchive();
            $filename = $data['user-id'].time();
            $zip->open($this->directory.'/'.$filename.'.zip', \ZipArchive::CREATE);
            $zip->addFromString('QuestionnaireView.csv', $this->createCSV($qv));
            $zip->addFromString('EnrolmentLogGood.csv', $this->createCSV($elg));
            $zip->addFromString('EnrolmentLogConcern.csv', $this->createCSV($elc));
            $zip->addFromString('Key.csv', $this->createCSV($key));
            $zip->close();
            $this->cleanUp();

            Log::debug('data-export', ['message' => 'Data export has been zipped.']);

            $file = explode('.', $filename)[0];
            $user = User::find($data['user-id']);
            $name = $user->first_name;
            $link = route('data.export', [$this->appId, $this->collectionId, $file]);

            $userData = [
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ];

            Mail::send('emails.data-export.complete', compact('name', 'link'), function ($message) use ($userData) {
                $name = $userData['first_name'].' '.$userData['last_name'];
                $subject = 'Your data export is complete!';
                $message->to('sam@netsells.co.uk', $name)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::debug('exception', compact('e'));
            $user = User::find($data['user-id']);
            $name = $user->first_name;

            $userData = [
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ];

            Mail::send('emails.data-export.unsuccessful', compact('name'), function ($message) use ($userData) {
                $name = $userData['first_name'].' '.$userData['last_name'];
                $message->to('sam@netsells.co.uk', $name)->subject('Your data export was unsuccessful!');
            });

            $admin = User::where('first_name', 'Core')->where('last_name', 'Admin')->first();

            Mail::send('emails.data-export.error', ['user' => serialize($user)], function ($message) use ($admin) {
                $email = $admin ? $admin->email : 'core.admin+prase@thedistance.co.uk';
                $message->to('sam@netsells.co.uk', 'Core Admin')->subject('NHS Prase Data Export Failed');
            });
        }

        return true;
    }

    /**
     * Clean up the current archives.
     */
    protected function cleanUp()
    {
        $archives = glob($this->directory.'/*.zip');
        rsort($archives);
        unset($archives[0], $archives[1]);

        if (count($archives)) {
            foreach ($archives as $archive) {
                unlink($archive);
            }
        }
    }

    protected function questionnaireView()
    {
        /*
         * Add field names
         */
        Log::debug('data-export', ['message' => 'Add field names to records array.']);
        $records = [
            [
                'OfflineId',
                'Researcher',
                'Trust',
                'Hospital',
                'Ward',
                'DateEnrol',
                'StayLength',
                'Age',
                'Gender',
                'Ethnicity',
                'CompletedBy',
                'FirstLanguage',
                'TimeRecorded',
                'TimeSpentQuestionnaire',
                'TimeSpentPatient',
                'IncompleteReason',
            ],
        ];

        $reversed = [];
        Log::debug('data-export', ['message' => 'Get question field names.']);
        $questions = PRRecord::first()->questions;

        foreach ($questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            $records[0][] = 'Q'.explode(' ', $question->node->title)[1];

            if ($question->node->fetchRevision()->reversescore) {
                $reversed[] = $question;
            }
        }

        foreach ($reversed as $i => $question) {
            $records[0][] = 'RQ'.explode(' ', $question->node->title)[1];
        }

        Log::debug('data-export', ['message' => 'Get domain field names.']);

        $nodeType = NodeType::where('name', 'question-domain')->first();
        $domains = [];
        foreach (Node::where('node_type', $nodeType->id)->get() as $node) {
            if ($domain = $node->fetchRevision()) {
                $domains[$domain->domainvalue] = $node;
            }
        }
        ksort($domains);
        foreach ($domains as $j => $domain) {
            $records[0][] = 'nd_'.$j;
        }
        foreach ($domains as $j => $domain) {
            $records[0][] = 'pd_'.$j;
        }

        /*
         * Add row content
         */
        Log::debug('data-export', ['message' => 'Add row content from records']);
        foreach ($this->records as $i => $record) {
            Log::debug('data-export', ['recordId' => $record->id]);
            $date = date('dmy\-His', strtotime($record->start_date));
            $user = strtoupper($record->user);

            $offlineId = "T{$date}{$user}";
            $researcher = $record->user;

            $hospitalNode = Node::find($record->hospital_node_id);
            $trustNode = $hospitalNode ? $hospitalNode->trust : null;

            $hospital = $hospitalNode ? $hospitalNode->fetchRevision() : null;
            $trust = $trustNode ? $trustNode->fetchRevision() : null;
            $ward = $record->ward ? $record->ward->fetchRevision() : null;

            $hospital = $hospital ? $hospital->name : null;
            $trust = $trust ? $trust->name : null;
            $ward = $ward ? $ward->name : null;

            $dateEnrol = date('d/m/Y H:i:s', strtotime($record->start_date));
            $basicData = json_decode($record->basic_data, true);
            $stayLength = $basicData['StayLength'];
            $age = $basicData['Age'];
            $gender = $basicData['Gender'];
            $ethnicity = $basicData['Ethnicity'];

            $completedBy = $basicData['Completer'];
            if ($completedBy == 'Other') {
                $completedBy = $basicData['OtherCompleter'];
            }

            $firstLanguage = $basicData['Language'];
            if ($firstLanguage == 'Other') {
                $firstLanguage = $basicData['OtherLanguage'];
            }

            $timeRecorded = $record->time_tracked;
            $timeSpentQuestionnaire = $record->time_spent_questionnaire;
            $timeSpentPatient = $record->time_spent_patient;

            $incompleteReason = $record->incomplete_reason;

            $records[] = [
                $offlineId, $researcher, $trust, $hospital, $ward, $dateEnrol, $stayLength, $age, $gender, $ethnicity, $completedBy, $firstLanguage, $timeRecorded, $timeSpentQuestionnaire, $timeSpentPatient, $incompleteReason,
            ];

            $reversed = [];
            $negative = [];
            $positive = [];

            foreach ($record->questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $j => $question) {
                $answerNode = $question->answer;
                $answerRevision = $answerNode ? $answerNode->fetchRevision() : null;
                $answerValue = $answerRevision ? $answerRevision->answervalue : null;
                $records[$i + 1][] = $answerValue;

                $questionNode = $question->node;
                $questionRevision = $questionNode ? $questionNode->fetchRevision() : null;
                $reverseScore = $questionRevision ? $questionRevision->reversescore : null;

                if ($reverseScore) {
                    $reversed[] = $question;
                }

                $domainId = $questionRevision ? $questionRevision->domain : null;

                if (empty($negative[$domainId])) {
                    $negative[$domainId] = [];
                }
                if (empty($positive[$domainId])) {
                    $positive[$domainId] = [];
                }

                if ($question->concern) {
                    $negative[$domainId][] = $question->concern;
                }
                if ($question->note) {
                    $positive[$domainId][] = $question->note;
                }
            }

            foreach ($reversed as $j => $question) {
                if ($question->answer) {
                    $count = 0;

                    $answerRevision = $question->answer ? $question->answer->fetchRevision() : null;
                    $answerValue = $answerRevision ? $answerRevision->answervalue : null;

                    $questionRevision = $question->node ? $question->node->fetchRevision() : null;
                    $answerTypes = $questionRevision ? $questionRevision->answertypes : null;
                    $answerTypes = $answerTypes ? explode(',', $answerTypes) : [];

                    foreach ($answerTypes as $answerTypeId) {
                        $answerTypeNode = Node::find($answerTypeId);
                        $answerTypeRevision = $answerTypeNode ? $answerTypeNode->fetchRevision() : null;

                        $options = $answerTypeRevision ? $answerTypeRevision->options : null;
                        $options = $options ? explode(',', $options) : [];

                        foreach ($options as $optionId) {
                            $optionNode = Node::find($optionId);
                            $optionRevision = $optionNode ? $optionNode->fetchRevision() : null;
                            $_answerValue = $optionRevision ? $optionRevision->answervalue : null;

                            if ($_answerValue > 0) {
                                $count++;
                            }
                        }
                    }

                    if ($answerValue > 0) {
                        $answerValue = $count - ($answerValue - 1);
                    }

                    $records[$i + 1][] = "$answerValue";
                } else {
                    $records[$i + 1][] = '';
                }
            }

            $nodeType = NodeType::where('name', 'question-domain')->first();

            $domains = [];

            foreach (Node::where('node_type', $nodeType->id)->get() as $domain) {
                $domainRevision = $domain->fetchRevision();
                $domainValue = $domainRevision ? $domainRevision->domainvalue : null;
                $domains[$domainValue] = $domain;
            }

            ksort($domains);

            Log::debug('data-export', ['message' => 'count negative domains']);
            foreach ($domains as $j => $domain) {
                $domainId = $domain->id;
                Log::debug('data-export', compact('domainId'));
                $records[$i + 1][] = isset($negative[$domain->id]) ? count($negative[$domain->id]) : '0';
            }

            Log::debug('data-export', ['message' => 'count positive domains']);
            foreach ($domains as $j => $domain) {
                $domainId = $domain->id;
                Log::debug('data-export', compact('domainId'));
                $records[$i + 1][] = isset($positive[$domain->id]) ? count($positive[$domain->id]) : '0';
            }
        }

        Log::debug('data-export', ['message' => 'Return all records']);

        return $records;
    }

    protected function enrolmentLogGood()
    {
        $notes = [
            [
                'OfflineId',
                'Trust',
                'HospitalName',
                'Ward',
                'QuestionNumber',
                'QuestionTextEnglish',
                'IncidentReportDescription',
            ],
        ];

        foreach (PRNote::has('concern', '<', 1)->get() as $i => $note) {
            Log::debug('data-export', ['message' => 'Concern: '.$i]);
            if ($note->record == null) {
                continue;
            }

            $date = date('dmy\-His', strtotime($note->record->start_date));
            $user = strtoupper($note->record->user);
            $offlineId = 'T'.$date.$user;

            // check if note is linked to hospital else grab hospital from record

            /*
             *  Get hospital revision from note
             */
            Log::debug('data-export', compact('note'));
            $hospital = $note->hospital ?: Node::find($note->record->hospital_node_id);
            $hospital = $hospital ? $hospital->fetchRevision() : null;
            Log::debug('data-export', ['message' => 'Concern (got hospital revision): '.$i]);

            /*
             *  Get trust revision from hospital revision
             */
            $trust = $hospital ? Node::find($hospital->trust) : null;
            $trust = $trust ? $trust->fetchRevision() : null;

            Log::debug('data-export', ['message' => 'Concern (got trust revision): '.$i]);

            $hospital = $hospital ? $hospital->name : null;
            $trust = $trust ? $trust->name : null;
            $ward = $note->ward;

            // check if the note is about a custom ward or an existing ward
            if ($ward) {
                $ward = $ward->fetchRevision()->name;
            } else {
                if ($note->ward_name) {
                    $ward = $note->ward_name;
                } else {
                    if ($ward = $note->record->ward) {
                        $ward = $ward->fetchRevision()->name;
                    } else {
                        $ward = $note->record->ward_name;
                    }
                }
            }
            Log::debug('data-export', ['message' => 'Concern (got ward): '.$i]);

            $questionNumber = '';
            $questionText = '';
            $incidentReport = $note->text;

            if ($question = $note->question) {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = @I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            }
            Log::debug('data-export', ['message' => 'Concern (got question): '.$i]);

            $notes[] = [
                $offlineId, $trust, $hospital, $ward, $questionNumber, $questionText, $incidentReport,
            ];

            Log::debug('data-export', ['message' => 'Finished Concern: '.$i]);
        }

        Log::debug('data-export', ['message' => 'Returning Concerns']);

        return $notes;
    }

    protected function enrolmentLogConcern()
    {
        $notes = [
            [
                'OfflineId',
                'Trust',
                'HospitalName',
                'Ward',
                'QuestionNumber',
                'QuestionTextEnglish',
                'IncidentReportDescription',
                'HowSerious',
                'PossibleToStop',
            ],
        ];

        foreach (PRConcern::all() as $i => $concern) {
            Log::debug('data-export', ['message' => 'Enroll Concern: '.$i]);
            if (!$concern or $concern->record == null) {
                continue;
            }

            $date = date('dmy\-His', strtotime($concern->record->start_date));
            $user = strtoupper($concern->record->user);
            $offlineId = 'T'.$date.$user;

            // check if note is linked to hospital else grab hospital from record
            $hospital = $concern->hospital ?: Node::find($concern->record->hospital_node_id);
            $hospital = $hospital->fetchRevision();

            $trust = Node::find($hospital->trust)->fetchRevision()->name;
            $hospital = $hospital->name;
            $ward = $concern->ward;

            // check if the note is about a custom ward or an existing ward
            if ($ward) {
                $ward = $ward->fetchRevision()->name;
            } else {
                if ($concern->ward_name) {
                    $ward = $concern->ward_name;
                } else {
                    if ($ward = $concern->record->ward) {
                        $ward = $ward->fetchRevision()->name;
                    } else {
                        $ward = $concern->record->ward_name;
                    }
                }
            }

            $questionNumber = '';
            $questionText = '';
            $incidentReport = ($concern->note) ? $concern->note->text : '';

            if ($question = $concern->question) {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            }

            $howSerious = $concern->serious_answer;
            $possibleToStop = $concern->prevent_answer;

            $notes[] = [
                $offlineId, $trust, $hospital, $ward, $questionNumber, $questionText, $incidentReport, $howSerious, $possibleToStop,
            ];
        }

        return $notes;
    }

    public function key()
    {
        $key = [
            [
                'QuestionId', 'QuestionText', 'OptionId', 'OptionText',
            ],
        ];

        foreach (PRRecord::first()->questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            foreach (explode(',', $question->node->fetchRevision()->answertypes) as $a => $answerTypeId) {
                foreach (explode(',', Node::find($answerTypeId)->fetchRevision()->options) as $b => $_optionId) {
                    Log::debug('data-export', ['message' => "Key: {$i} {$a} {$b}"]);
                    $questionId = explode(' ', $question->node->title)[1];
                    $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;

                    $optionId = Node::find($_optionId)->fetchRevision()->answervalue;
                    /*$text = Node::find($_optionId)->fetchRevision()->text;
                    if (!I18nString::whereKey(Node::find($_optionId)->fetchRevision()->text)->whereLang('en')->first())
                    {
                        dd(compact('_optionId', 'optionId', 'text'));
                    }*/
                    $node = Node::find($_optionId);
                    $option = $node ? $node->fetchRevision() : null;
                    $textKey = $option ? $option->text : null;
                    $i18nstring = $textKey ? I18nString::whereKey($textKey)->whereLang('en')->first() : null;
                    $optionText = $i18nstring ? $i18nstring->value : null;

                    $key[] = [
                        $questionId,//$a == 0 & $b == 0 ? $questionId : '',
                        $questionText,//$a == 0 & $b == 0 ? $questionText : '',
                        $optionId,
                        $optionText,
                    ];
                }
            }
        }

        return $key;
    }

    protected function createCSV($array)
    {
        // TODO: rewrite for larger file sizes
        $out = fopen('php://temp', 'wr');
        foreach ($array as $line) {
            fputcsv($out, $line);
        }
        fseek($out, 0);
        $string = stream_get_contents($out);

        fclose($out);

        return $string;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('app-id', InputArgument::REQUIRED, 'The app id.'),
            array('collection-id', InputArgument::REQUIRED, 'The collection id.'),
            array('user-id', InputArgument::REQUIRED, 'The user id.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}
