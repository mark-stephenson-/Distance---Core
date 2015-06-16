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
    protected $collectionId;

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
    public function fire()
    {
        $qv = $this->questionnaireView();
        $elg = $this->enrolmentLogGood();
        $elc = $this->enrolmentLogConcern();
        $key = $this->key();

        $this->collectionId = $this->argument('collection-id') ?: $collectionId;
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
        $zip->open($this->directory.'/'.time().'.zip', \ZipArchive::CREATE);
        $filename = $zip->filename;
        $zip->addFromString('QuestionnaireView.csv', $this->createCSV($qv));
        $zip->addFromString('EnrolmentLogGood.csv', $this->createCSV($elg));
        $zip->addFromString('EnrolmentLogConcern.csv', $this->createCSV($elc));
        $zip->addFromString('Key.csv', $this->createCSV($key));

        $zip->close();
        $this->cleanUp();

        return $filename;
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

        foreach (PRRecord::first()->questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            $records[0][] = 'Q'.explode(' ', $question->node->title)[1];

            if ($question->node->fetchRevision()->reversescore) {
                $reversed[] = $question;
            }
        }

        foreach ($reversed as $i => $question) {
            $records[0][] = 'RQ'.explode(' ', $question->node->title)[1];
        }

        $nodeType = NodeType::where('name', 'question-domain')->first();

        $domains = [];

        foreach (Node::where('node_type', $nodeType->id)->get() as $domain) {
            $domains[$domain->fetchRevision()->domainvalue] = $domain;
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

        foreach (PRRecord::all() as $i => $record) {
            $researcher = $record->user;
            $date = date('dmy\-His', strtotime($record->start_date));
            $user = strtoupper($researcher);
            $offlineId = 'T'.$date.$user;

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
                $records[$i + 1][] = $question->answer ? $question->answer->fetchRevision()->answervalue : '';

                if ($question->node->fetchRevision()->reversescore) {
                    $reversed[] = $question;
                }

                $domainId = $question->node->fetchRevision()->domain;

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
                    $answer = $question->answer->fetchRevision()->answervalue;
                    $answerTypes = explode(',', $question->node->fetchRevision()->answertypes);

                    foreach ($answerTypes as $answerTypeId) {
                        $options = explode(',', Node::find($answerTypeId)->fetchRevision()->options);

                        foreach ($options as $optionId) {
                            $answerValue = Node::find($optionId)->fetchRevision()->answervalue;

                            if ($answerValue > 0) {
                                $count++;
                            }
                        }
                    }

                    if ($answer > 0) {
                        $answer = $count - ($answer - 1);
                    }

                    $records[$i + 1][] = "$answer";
                } else {
                    $records[$i + 1][] = '';
                }
            }

            $nodeType = NodeType::where('name', 'question-domain')->first();

            $domains = [];

            foreach (Node::where('node_type', $nodeType->id)->get() as $domain) {
                $domains[$domain->fetchRevision()->domainvalue] = $domain;
            }

            ksort($domains);

            foreach ($domains as $j => $domain) {
                $records[$i + 1][] = isset($negative[$domain->id]) ? count($negative[$domain->id]) : '0';
            }

            foreach ($domains as $j => $domain) {
                $records[$i + 1][] = isset($positive[$domain->id]) ? count($positive[$domain->id]) : '0';
            }
        }

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
            $date = date('dmy\-His', strtotime($note->record->start_date));
            $user = strtoupper($note->record->user);
            $offlineId = 'T'.$date.$user;

            // check if note is linked to hospital else grab hospital from record
            $hospital = $note->hospital ?: Node::find($note->record->hospital_node_id);
            $hospital = $hospital ? $hospital->fetchRevision() : null;

            $trust = $hospital ? $hospital->trust : null;
            $trust = $trust ? $trust->fetchRevision() : null;
            $trust = $trust ? $trust->name : null;

            $hospital = $hospital ? $hospital->name : null;
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

            $questionNumber = '';
            $questionText = '';
            $incidentReport = $note->text;

            if ($question = $note->question) {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            }

            $notes[] = [
                $offlineId, $trust, $hospital, $ward, $questionNumber, $questionText, $incidentReport,
            ];
        }

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
            $incidentReport = $concern->note->text;

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
                    $questionId = explode(' ', $question->node->title)[1];
                    $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;

                    $optionId = Node::find($_optionId)->fetchRevision()->answervalue;
                    /*$text = Node::find($_optionId)->fetchRevision()->text;
                    if (!I18nString::whereKey(Node::find($_optionId)->fetchRevision()->text)->whereLang('en')->first())
                    {
                        dd(compact('_optionId', 'optionId', 'text'));
                    }*/
                    $optionText = I18nString::whereKey(Node::find($_optionId)->fetchRevision()->text)->whereLang('en')->first()->value;

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
            array('collection-id', InputArgument::REQUIRED, 'The collection id.'),
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
