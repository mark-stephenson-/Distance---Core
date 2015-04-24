<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataExportCommand extends Command {

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
    protected $description = 'Export .csv file of Prase submissions.';
    
    /**
     * The directory where archives are stored
     */
    protected $directory;

    /**
     * The directory where PID files are stored
     */
    protected $pidDirectory;

    /**
     * The collection ID of the archive currently being created
     */
    protected $collectionId;

    /**
     * The data export file
     */
    protected $export;
    
    /**
     * The slugs of the question domains and their internal IDs
     */
    protected $keys = [
        'answer' => [
            29 => -1,
            28 => 0,
            31 => 1,
            32 => 2,
            33 => 3,
            34 => 4,
            35 => 5
        ],
        'domain' => [
            'dignity-and-respect' => 0,
            'communication-and-teamworking' => 1,
            'organisation-and-care-planning' => 2,
            'access-to-resources' => 3,
            'ward-type-and-layout' => 4,
            'information-flow' => 5,
            'staff-roles-and-responsibilities' => 6,
            'staff-training' => 7,
            'equipment-(design-and-functioning)' => 8,
            'delays' => 9
        ]
    ];
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $qv = $this->questionnaireView();
        $elg = $this->enrolmentLogGood();
        $elc = $this->enrolmentLogConcern();
        $key = $this->key();
        
        $this->collectionId = $this->argument('collection-id') ?: $collectionId;
        $this->directory = storage_path() . '/exports/' . $this->collectionId;
        $this->pidDirectory = $this->directory . '/pid';

        if ( ! is_dir($this->directory) ) {
            mkdir( $this->directory, 0777, true );
        }

        // Ensure the PID directory exists
        if ( ! is_dir($this->pidDirectory) ) {
            mkdir( $this->pidDirectory, 0777);
        }

        $pidFileName = $this->pidDirectory . '/' . getmypid();

        $pidFile = fopen( $pidFileName, 'w');
        fclose($pidFile);

        // Clean up PID files when we exit/are complete
        register_shutdown_function(function() use ($pidFileName) {
            unlink($pidFileName);
        });

        $this->stopOtherExports();

        // Make the zip archive
        $zip = new \ZipArchive;
        $zip->open( $this->directory . '/' . time() . '.zip', \ZipArchive::CREATE );
        $filename = $zip->filename;
        $zip->addFromString('QuestionnaireView.csv', $this->createCSV($qv));
        $zip->addFromString('EnrolmentLogGood.csv', $this->createCSV($elg));
        $zip->addFromString('EnrolmentLogConcern.csv', $this->createCSV($elg));
        $zip->addFromString('Key.csv', $this->createCSV($key));
        
        $zip->close();
        $this->cleanUp();
        
        return $filename;
    }

    /**
     * Stop any other archive actions that might be occuring for this collection
     */
    protected function stopOtherExports()
    {
        $pidFiles = glob($this->pidDirectory . '/*');

        foreach ( $pidFiles as $_pidFile ) {
            $explode = explode('/', $_pidFile);
            $_pid = $explode[ count($explode) - 1];

            if ( $_pid  != getmypid() ) {
                posix_kill($_pid, 2);
                unlink($this->pidDirectory . '/' . $_pid);
            }
        }
    }

    /**
     * Clean up the current archives
     */
    protected function cleanUp()
    {
        $archives = glob($this->directory . '/*.zip');
        rsort($archives);
        unset($archives[0], $archives[1]);

        if ( count($archives) ) {
            foreach ( $archives as $archive ) {
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
                'IncompleteReason'
            ]
        ];
        
        $reversed = [];
        
        foreach(PRRecord::first()->questions as $i => $question)
        {
            $records[0][] = 'Q'.($i + 1);
            
            if ($question->node->fetchRevision()->reversescore) $reversed[] = $question;
        }
        
        foreach($reversed as $i => $question)
        {
            $records[0][] = 'RQ'.($i + 1);
        }
        
        $nodeType = NodeType::where('name', 'question-domain')->first();
        $domains = [];
        foreach(Node::where('node_type', $nodeType->id)->get() as $domain) {
            $domains[$this->keys['domain'][$domain->title]] = $domain;
        }
        ksort($domains);
        
        foreach($domains as $i => $domain)
        {
            $records[0][] = 'nd_'.$i;
        }

        foreach($domains as $j => $domain)
        {
            $records[0][] = 'pd_'.$i;
        }
        
        /*
         * Add row content
         */
        
        foreach(PRRecord::all() as $i => $record)
        {            
            $date = date('dmy\-His', strtotime($record->start_date));
            $user = strtoupper($record->user);
            $offlineId = 'T'.$date.substr($user,0 ,6);

            $researcher = $record->user;
            $hospital = Node::find($record->hospital_node_id)->fetchRevision();
            $trust = Node::find($hospital->trust)->fetchRevision()->name;
            $hospital = $hospital->name;
            $ward = $record->ward->fetchRevision()->name;
            $dateEnrol = date('d/m/Y H:i:s', strtotime($record->start_date));
            
            $basicData = json_decode($record->basic_data, true);
            
            $stayLength = $basicData['StayLength'];
            $age = $basicData['Age'];
            $gender = $basicData['Gender'];
            $ethnicity = $basicData['Ethnicity'];
            
            $completedBy = $basicData['Completer'];
            if($completedBy == 'Other')
                $completedBy = $basicData['OtherCompleter'];
            
            $firstLanguage = $basicData['Language'];
            if($firstLanguage == 'Other')
                $firstLanguage = $basicData['OtherLanguage'];
            
            $timeRecorded = $record->time_tracked;
            $timeSpentQuestionnaire = $record->time_additional_questionnaire;
            $timeSpentPatient = $record->time_additional_patient;
            
            $incompleteReason = $record->incomplete_reason;
            
            $records[] = [
                $offlineId, $researcher, $trust, $hospital, $ward, $dateEnrol, $stayLength, $age, $gender, $ethnicity, $completedBy, $firstLanguage, $timeRecorded, $timeSpentQuestionnaire, $timeSpentPatient, $incompleteReason
            ];
            
            $reversed = [];
            $negative = [];
            $positive = [];
            
            foreach($record->questions as $j => $question)
            {
                $records[$i + 1][] = $this->keys['answer'][$question->answer_node_id];
                
                $questionNode = Node::find($question->question_node_id);
                
                if ($questionNode->fetchRevision()->reversescore) $reversed[] = $question;
                
                $domainId = $questionNode->fetchRevision()->domain;
                
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
            
            foreach($reversed as $j => $question)
            {                
                $answer = $this->keys['answer'][$question->answer_node_id];
                
                if ($answer > 0) $answer = 5 - ($answer - 1);
                
                $records[$i + 1][] = $answer;
            }
            
            $nodeType = NodeType::where('name', 'question-domain')->first();
            
            $domains = [];
            foreach(Node::where('node_type', $nodeType->id)->get() as $domain) {
                $domains[$this->keys['domain'][$domain->title]] = $domain;
            }
            ksort($domains);
            
            foreach($domains as $j => $domain)
            {
                $records[$i + 1][] = count($negative[$domain->id]);
            }
            
            foreach($domains as $j => $domain)
            {
                $records[$i + 1][] = count($positive[$domain->id]);
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
                'IncidentReportDescription'
            ]
        ];
        
        foreach(PRNote::all() as $i => $note)
        {
            $date = date('dmy\-His', strtotime($note->record->start_date));
            $user = strtoupper($note->record->user);
            $offlineId = 'T'.$date.substr($user,0 ,6);

            // check if note is linked to hospital else grab hospital from record
            $hospital = $note->hospital ?: Node::find($note->record->hospital_node_id);
            $hospital = $hospital->fetchRevision();
            
            $trust = Node::find($hospital->trust)->fetchRevision()->name;
            $hospital = $hospital->name;
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
            
            if ($question = $note->question)
            {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            }
                        
            $notes[] = [
                $offlineId, $trust, $hospital, $ward, $questionNumber, $questionText, $incidentReport
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
                'PossibleToStop'
            ]
        ];
        
        foreach(PRConcern::all() as $i => $concern)
        {
            $date = date('dmy\-His', strtotime($concern->record->start_date));
            $user = strtoupper($concern->record->user);
            $offlineId = 'T'.$date.substr($user,0 ,6);

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
                    $ward = $note->ward_name;
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
            
            if ($question = $concern->question)
            {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            }
            
            $howSerious = $concern->serious_answer;
            $possibleToStop = $concern->prevent_answer;
            
            $notes[] = [
                $offlineId, $trust, $hospital, $ward, $questionNumber, $questionText, $incidentReport, $howSerious, $possibleToStop
            ];
        }
        
        return $notes;
    }
    
    public function key()
    {
        $key = [
            [
                'QuestionId', 'QuestionText', 'OptionId', 'OptionText'
            ]
        ];
        
        foreach(PRRecord::first()->questions as $i => $question)
        {
            $questionId = explode(' ', $question->node->title)[1];
            $questionText = I18nString::whereKey($question->node->fetchRevision()->question)->whereLang('en')->first()->value;
            $optionId = $this->keys['answer'][$question->answer_node_id];
            $optionText = I18nString::whereKey($question->answer->fetchRevision()->text)->whereLang('en')->first()->value;
            
            $key[] = [$questionId, $questionText, $optionId, $optionText];
        }
        
        return $key;
    }
    
    protected function createCSV($array)
    {
        // TODO: rewrite for larger file sizes
        $out = fopen('php://temp', 'wr');
        foreach($array as $line) {
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
            array('collection-id', InputArgument::REQUIRED, 'The collection id.')
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