<?php

namespace Core\Services\ReportService;


use Illuminate\Support\Collection;
use League\Csv\Writer;
use Node;
use PRConcern;
use PRNote;
use PRRecord;
use SplFileObject;

class CSV
{
    protected $directory = 'reports/csvs';
    private $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function generateCSVFromReportData()
    {
        $zip = new \ZipArchive();
        $zip->open(storage_path($this->directory . '/export.zip'), \ZipArchive::CREATE);
        $zip->addFile($this->createQuestionnaireViewCsv(), 'QuestionnaireView.csv');
        $zip->addFile($this->createEnrolmentLogGoodCsv(), 'EnrolmentLogGood.csv');
        $zip->addFile($this->createEnrolmentLogConcernCsv(), 'EnrolmentLogConcern.csv');
        $zip->addFile($this->createKeyCsv(), 'Key.csv');
        $zip->close();

        return storage_path($this->directory.'/export.zip');
    }

    public function createQuestionnaireViewCsv()
    {
        $filename = storage_path($this->directory.'/questionnaire-view.csv');

        $csv = Writer::createFromFileObject(new SplFileObject($filename, 'w+'));
        $csv->setDelimiter(',');

        $csv = $this->writeQuestionnaireViewHeaders($csv);

        $records = [];

        $fetchedRecords = PRRecord::orderBy('created_at', 'desc')
            ->with(array(
                'questions' => function ($q) {
                    return $q
                        ->join('node_type_5', 'prase_questions.answer_node_id', '=', 'node_type_5.node_id')
                        ->join('node_type_1', 'prase_questions.question_node_id', '=', 'node_type_1.node_id')
                        ->groupBy('prase_questions.id')
                        ->select([
                            'prase_questions.*',
                            'node_type_5.answervalue AS answerValue',
                            'node_type_1.answertypes AS answerTypes',
                            'node_type_1.reversescore AS reverseScore',
                            'node_type_1.domain AS domainId',
                        ]);
                },
                'ward',
                'questions.node',
                'questions.concern',
                'questions.note',
                'questions.answer',
            ))
            ->where('prase_records.pmos_id', $this->reportData->pmos_id)
            ->whereIn('prase_records.ward_node_id', $this->reportData->wardIds)
            ->where('prase_records.start_date', '>=', $this->reportData->dates->start)
            ->where('prase_records.start_date', '<=', $this->reportData->dates->end)
            ->get([
                'prase_records.*',
            ]);

        $fetchedWards = \DB::table('node_type_4')
            ->whereIn('node_id', $fetchedRecords->lists('ward_node_id'))
            ->where('id', \DB::raw('(SELECT id FROM node_type_4 as wards WHERE wards.node_id = node_type_4.node_id ORDER BY updated_at DESC LIMIT 1)'))
            ->groupBy('node_id')
            ->orderBy('updated_at')
            ->get();

        $fetchedWards = (new Collection($fetchedWards))->keyBy('node_id');

        $fetchedHospitals = \DB::table('node_type_3')
            ->whereIn('node_id', $fetchedRecords->lists('hospital_node_id'))
            ->where('id', \DB::raw('(SELECT id FROM node_type_3 as wards WHERE wards.node_id = node_type_3.node_id ORDER BY updated_at DESC LIMIT 1)'))
            ->groupBy('node_id')
            ->orderBy('updated_at')
            ->get();

        $fetchedHospitals = (new Collection($fetchedHospitals))->keyBy('node_id');

        $fetchedTrusts = \DB::table('node_type_2')
            ->where('id', \DB::raw('(SELECT id FROM node_type_2 as wards WHERE wards.node_id = node_type_2.node_id ORDER BY updated_at DESC LIMIT 1)'))
            ->groupBy('node_id')
            ->orderBy('updated_at')
            ->get();

        $fetchedTrusts = (new Collection($fetchedTrusts))->keyBy('node_id');

        $prefetchedAnswerTypes = array();

        // TODO: replace with keyby
        foreach (Node::whereNodeType(6)->join('node_type_6', 'nodes.id', '=', 'node_type_6.node_id')->get(['nodes.*', 'node_type_6.options AS options']) as $type) {
            $prefetchedAnswerTypes[$type->id] = $type;
        }

        $prefetchedAnswerTypeOptions = array();

        // TODO: replace with keyBy
        foreach (Node::whereNodeType(5)->join('node_type_5', 'nodes.id', '=', 'node_type_5.node_id')->get(['nodes.*', 'node_type_5.answerValue AS answerValue']) as $option) {
            $prefetchedAnswerTypeOptions[$option->id] = $option;
        }

        foreach ($fetchedRecords as $i => $record) {
            $basicData = json_decode($record->basic_data, true);

            $recordRow = [
                'T'.date('dmy\-His', strtotime($record->start_date)).strtoupper($record->user), // Offline ID
                $record->user, // Researcher
                $fetchedTrusts[$fetchedHospitals[$record->hospital_node_id]->trust]->name,
                $fetchedHospitals[$record->hospital_node_id]->name,
                $record->ward_node_id,
                $fetchedWards[$record->ward_node_id]->name,
                $record->ward->published_at,
                $record->ward->retired_at,
                $fetchedWards[$record->ward_node_id]->{'ward-change-comment'},
                date('d/m/Y H:i:s', strtotime($record->start_date)), // Date enrolled
                $basicData['StayLength'],
                $basicData['Age'],
                $basicData['Gender'],
                $basicData['Ethnicity'],
                ($basicData['Completer'] == 'Other') ? $basicData['OtherCompleter'] : $basicData['Completer'],
                ($basicData['Language'] == 'Other') ? $basicData['OtherLanguage'] : $basicData['Language'],
                $record->time_tracked,
                $record->time_spent_questionnaire,
                $record->time_spent_patient,
                $record->incomplete_reason,
            ];

            $reversed = [];
            $negative = [];
            $positive = [];

            foreach ($record->questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $j => $question) {
                $recordRow[] = $question->answerValue;

                if ($question->reverseScore) {
                    $reversed[] = $question;
                }

                if (empty($negative[$question->domainId])) {
                    $negative[$question->domainId] = [];
                }
                if (empty($positive[$question->domainId])) {
                    $positive[$question->domainId] = [];
                }

                if ($question->concern) {
                    $negative[$question->domainId][] = $question->concern;
                }
                if ($question->note) {
                    $positive[$question->domainId][] = $question->note;
                }
            }

            foreach ($reversed as $j => $question) {
                if ($question->answer) {
                    $count = 0;
                    $answerTypes = $question->answerTypes ? explode(',', $question->answerTypes) : array();
                    $answerValue = $question->answerValue;

                    foreach ($answerTypes as $answerTypeId) {
                        $answerType = $prefetchedAnswerTypes[$answerTypeId];
                        $options = $answerType->options ? explode(',', $answerType->options) : [];

                        foreach ($options as $optionId) {
                            $option = $prefetchedAnswerTypeOptions[$optionId];

                            if ($option->answerValue > 0) {
                                ++$count;
                            }
                        }
                    }

                    if ($answerValue > 0) {
                        $answerValue = $count - ($answerValue - 1);
                    }

                    $recordRow[] = "$answerValue";
                } else {
                    $recordRow[] = '';
                }
            }

            $domains = [];

            foreach (Node::where('node_type', '10')->join('node_type_10', 'nodes.id', '=', 'node_type_10.node_id')->get(['nodes.*', 'node_type_10.domainvalue']) as $node) {
                $domains[$node->domainvalue] = $node;
            }

            ksort($domains);

            foreach ($domains as $j => $domain) {
                $recordRow[] = isset($negative[$domain->id]) ? count($negative[$domain->id]) : '0';
            }

            foreach ($domains as $j => $domain) {
                $recordRow[] = isset($positive[$domain->id]) ? count($positive[$domain->id]) : '0';
            }

            $records[] = $recordRow;
        }

        $csv->insertAll($records);

        return $filename;
    }

    /**
     * @param $csv
     * @return mixed
     */
    private function writeQuestionnaireViewHeaders($csv)
    {
        // Write the header
        $csvHeaders = array('OfflineId', 'Researcher', 'Trust', 'Hospital', 'Ward Reference', 'Ward', 'Ward Start Date', 'Ward End Date', 'Ward Change Comment', 'DateEnrol', 'StayLength', 'Age', 'Gender', 'Ethnicity', 'CompletedBy', 'FirstLanguage', 'TimeRecorded', 'TimeSpentQuestionnaire', 'TimeSpentPatient', 'IncompleteReason');

        $questions = new Collection();

        foreach($this->reportData->domains as $domain) {
            foreach($domain->questions as $questionId => $question) {
                $questions->put($questionId, $question);
            }
        }

        $reversed = [];

        foreach ($questions->sortBy(function ($record) { return explode(' ', $record->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            $csvHeaders[] = 'Q'.explode(' ', $question->title)[1];

            if ($question->reversescore) {
                $reversed[] = $question;
            }
        }

        foreach ($reversed as $i => $question) {
            $csvHeaders[] = 'RQ'.explode(' ', $question->title)[1];
        }

        $domains = [];
        // Fetch domains
        foreach($this->reportData->domains as $domainId => $domain) {
            $domains[$domain->domainvalue] = $domain;
        }

        ksort($domains);
        foreach ($domains as $j => $domain) {
            $csvHeaders[] = 'nd_'.$j;
        }
        foreach ($domains as $j => $domain) {
            $csvHeaders[] = 'pd_'.$j;
        }

        $csv->insertOne($csvHeaders);

        return $csv;
    }

    public function createEnrolmentLogGoodCsv()
    {
        $filename = storage_path($this->directory.'/enrolment-log-good.csv');

        $csv = Writer::createFromFileObject(new \SplFileObject($filename, 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['OfflineId', 'Trust', 'HospitalName', 'Ward', 'QuestionNumber', 'QuestionTextEnglish', 'IncidentReportDescription']);
        $reportData = $this->reportData;

        $prefetchedNotes = PRNote::has('concern', '<', 1)
            ->whereHas('record', function ($q) use ($reportData) {
                return $q->wherePmosId($reportData->pmos_id)
                    ->whereIn('prase_records.ward_node_id', $reportData->wardIds)
                    ->where('prase_records.start_date', '>=', $reportData->dates->start)
                    ->where('prase_records.start_date', '<=', $reportData->dates->end);
            })
            ->with([
                'record' => function ($q) {
                    return $q
                        ->join('node_type_3', 'prase_records.hospital_node_id', '=', 'node_type_3.node_id') // Hospital name
                        ->join('node_type_4', 'prase_records.ward_node_id', '=', 'node_type_4.node_id') // Ward name
                        ->join('node_type_2', 'node_type_3.trust', '=', 'node_type_2.node_id') // Trust name
                        ->get(array(
                            'prase_records.*',
                            'node_type_3.name AS hospital_name',
                            'node_type_4.name AS ward_name',
                            'node_type_2.name AS trust_name',
                        ));
                },
                'question' => function ($q) {
                    return $q
                        ->join('node_type_1', 'prase_questions.question_node_id', '=', 'node_type_1.node_id')
                        ->join('i18n_strings', 'i18n_strings.key', '=', 'node_type_1.question')
                        ->where('i18n_strings.lang', 'en')
                        ->get([
                            'prase_questions.*',
                            'i18n_strings.value AS questionText',
                        ]);
                },
                'question.node',
            ])
            ->get();

        foreach ($prefetchedNotes as $i => $note) {
            if ($note->record == null) {
                continue;
            }

            $questionNumber = '';
            $questionText = '';

            if ($question = $note->question) {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = $note->question->questionText;
            }

            $notes[] = [
                'T'.date('dmy\-His', strtotime($note->record->start_date)).strtoupper($note->record->user), // OfflineID
                $note->record->trust_name,
                $note->record->hospital_name,
                $note->record->ward_name,
                $questionNumber,
                $questionText,
                $note->text, // Incident report
            ];
        }

        $csv->insertAll($notes);

        return $filename;
    }

    public function createEnrolmentLogConcernCsv()
    {
        $filename = storage_path($this->directory.'/enrolment-log-concern.csv');

        $csv = Writer::createFromFileObject(new \SplFileObject($filename, 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['OfflineId', 'Trust', 'HospitalName', 'Ward', 'QuestionNumber', 'QuestionTextEnglish', 'IncidentReportDescription', 'HowSerious', 'PossibleToStop']);

        $reportData = $this->reportData;

        $prefetchedConcerns = PRConcern::whereHas('record', function ($q) use ($reportData) {
            return $q->wherePmosId($reportData->pmos_id)
                ->whereIn('prase_records.ward_node_id', $reportData->wardIds)
                ->where('prase_records.start_date', '>=', $reportData->dates->start)
                ->where('prase_records.start_date', '<=', $reportData->dates->end);
        })
            ->with([
                'note',
                'record' => function ($q) {
                    return $q
                        ->join('node_type_3', 'prase_records.hospital_node_id', '=', 'node_type_3.node_id') // Hospital name
                        ->join('node_type_4', 'prase_records.ward_node_id', '=', 'node_type_4.node_id') // Ward name
                        ->join('node_type_2', 'node_type_3.trust', '=', 'node_type_2.node_id') // Trust name
                        ->get(array(
                            'prase_records.*',
                            'node_type_3.name AS hospital_name',
                            'node_type_4.name AS ward_name',
                            'node_type_2.name AS trust_name',
                        ));
                },
                'question' => function ($q) {
                    return $q
                        ->join('node_type_1', 'prase_questions.question_node_id', '=', 'node_type_1.node_id')
                        ->join('i18n_strings', 'i18n_strings.key', '=', 'node_type_1.question')
                        ->where('i18n_strings.lang', 'en')
                        ->get([
                            'prase_questions.*',
                            'i18n_strings.value AS questionText',
                        ]);
                },
                'question.node',
            ])
            ->get();

        foreach ($prefetchedConcerns as $i => $concern) {
            $questionNumber = '';
            $questionText = '';

            if ($question = $concern->question) {
                $questionNumber = explode(' ', $question->node->title)[1];
                $questionText = $concern->question->questionText;
            }

            $concerns[] = [
                'T'.date('dmy\-His', strtotime($concern->record->start_date)).strtoupper($concern->record->user), // OfflineID
                $concern->record->trust_name,
                $concern->record->hospital_name,
                $concern->record->ward_name,
                $questionNumber,
                $questionText,
                ($concern->note) ? $concern->note->text : '', // Incident report
                $concern->serious_answer,
                $concern->prevent_answer,
            ];
        }

        $csv->insertAll($concerns);

        return $filename;
    }

    public function createKeyCsv()
    {
        $filename = storage_path($this->directory.'/key.csv');

        $csv = Writer::createFromFileObject(new \SplFileObject($filename, 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['QuestionId', 'QuestionText', 'OptionId', 'OptionText']);

        $prefetchedAnswerTypes = Node::whereNodeType(6)->join('node_type_6', 'nodes.id', '=', 'node_type_6.node_id')->get(['nodes.*', 'node_type_6.options AS options'])->keyBy('id');
        $prefetchedAnswerTypeOptions = Node::whereNodeType(5)->join('node_type_5', 'nodes.id', '=', 'node_type_5.node_id')->get(['nodes.*', 'node_type_5.answerValue AS answerValue'])->keyBy('id');

        $questions = PRRecord::wherePmosId($this->reportData->pmos_id)
            ->whereIn('prase_records.ward_node_id', $this->reportData->wardIds)
            ->where('prase_records.start_date', '>=', $this->reportData->dates->start)
            ->where('prase_records.start_date', '<=', $this->reportData->dates->end)
            ->first()
            ->questions()
            ->join('node_type_1', 'prase_questions.question_node_id', '=', 'node_type_1.node_id')
            ->join('i18n_strings', 'i18n_strings.key', '=', 'node_type_1.question')
            ->where('i18n_strings.lang', 'en')
            ->get([
                'node_type_1.answertypes AS answerTypes',
                'prase_questions.*',
                'i18n_strings.value AS questionText',
            ]);

        foreach ($questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            foreach (explode(',', $question->answerTypes) as $a => $answerTypeId) {
                foreach (explode(',', Node::find($answerTypeId)->fetchRevision()->options) as $b => $_optionId) {
                    $optionId = Node::find($_optionId)->fetchRevision()->answervalue;
                    $node = Node::find($_optionId);
                    $option = $node ? $node->fetchRevision() : null;
                    $textKey = $option ? $option->text : null;
                    $i18nstring = $textKey ? \I18nString::whereKey($textKey)->whereLang('en')->first() : null;
                    $optionText = $i18nstring ? $i18nstring->value : null;

                    $key[] = [
                        explode(' ', $question->node->title)[1], // QuestionId
                        $question->questionText, // QuestionText
                        $optionId,
                        $optionText,
                    ];
                }
            }
        }

        $csv->insertAll($key);

        return $filename;
    }
}