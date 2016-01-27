<?php

namespace Core\Services;

use Node;
use PRRecord;
use PRNote;
use PRConcern;
use League\Csv\Writer;

class ExportService
{
    protected $directory = 'export-csvs';

    public function generateExportForQuestionSet($questionSetId)
    {
        if (!PRRecord::wherePmosId($questionSetId)->count()) {
            throw new \Exception('No data to export');
        }

        $this->cleanUp();

        $zip = new \ZipArchive();
        $zip->open(storage_path($this->directory.'/export.zip'), \ZipArchive::CREATE);
        $zip->addFromString('QuestionnaireView.csv', $this->createQuestionnaireViewCsv($questionSetId));
        $zip->addFromString('EnrolmentLogGood.csv', $this->createEnrolmentLogGoodCsv($questionSetId));
        $zip->addFromString('EnrolmentLogConcern.csv', $this->createEnrolmentLogConcernCsv($questionSetId));
        $zip->addFromString('Key.csv', $this->createKeyCsv($questionSetId));
        $zip->close();

        return storage_path($this->directory.'/export.zip');
    }

    protected function cleanUp()
    {
        foreach (glob(storage_path($this->directory).'/*.zip') as $zip) {
            unlink($zip);
        }

        foreach (glob(storage_path($this->directory).'/*.csv') as $csv) {
            unlink($csv);
        }
    }

    public function createQuestionnaireViewCsv($questionSetId)
    {
        $filename = $this->directory.'/questionnaire-view.csv';

        $csv = Writer::createFromFileObject(new \SplFileObject(storage_path($filename), 'w+'));
        $csv->setDelimiter(',');

        $csv = $this->writeQuestionnaireViewHeaders($questionSetId, $csv);

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
                'questions.node',
                'questions.concern',
                'questions.note',
                'questions.answer',
            ))
            ->join('node_type_3', 'prase_records.hospital_node_id', '=', 'node_type_3.node_id') // Hospital name
            ->join('node_type_4', 'prase_records.ward_node_id', '=', 'node_type_4.node_id') // Ward name
            ->join('node_type_2', 'node_type_3.trust', '=', 'node_type_2.node_id') // Trust name
            ->get(array(
                'prase_records.*',
                'node_type_3.name AS hospital_name',
                'node_type_4.name AS ward_name',
                'node_type_2.name AS trust_name',
            ));

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
                $record->trust_name,
                $record->hospital_name,
                $record->ward_name,
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

    public function writeQuestionnaireViewHeaders($questionSetId, Writer $csv)
    {
        // Write the header
        $csvHeaders = array('OfflineId', 'Researcher', 'Trust', 'Hospital', 'Ward', 'DateEnrol', 'StayLength', 'Age', 'Gender', 'Ethnicity', 'CompletedBy', 'FirstLanguage', 'TimeRecorded', 'TimeSpentQuestionnaire', 'TimeSpentPatient', 'IncompleteReason');

        $reversed = [];
        $questions = PRRecord::wherePmosId($questionSetId)->orderBy('id', 'asc')->first()
            ->questions()
            ->with('node')
            ->join('node_type_1', 'prase_questions.question_node_id', '=', 'node_type_1.node_id')
            ->groupBy('node_type_1.node_id')
            ->get(['node_type_1.node_id', 'prase_questions.*', 'node_type_1.reversescore', 'node_type_1.id AS nodetypeid']);

        foreach ($questions->sortBy(function ($record) { return explode(' ', $record->node->title)[1]; }, SORT_NUMERIC) as $i => $question) {
            $csvHeaders[] = 'Q'.explode(' ', $question->node->title)[1];

            if ($question->reversescore) {
                $reversed[] = $question;
            }
        }

        foreach ($reversed as $i => $question) {
            $csvHeaders[] = 'RQ'.explode(' ', $question->node->title)[1];
        }

        $domains = [];
        // Fetch domains
        foreach (Node::where('node_type', '10')->join('node_type_10', 'nodes.id', '=', 'node_type_10.node_id')->get(['nodes.*', 'node_type_10.domainvalue']) as $node) {
            $domains[$node->domainvalue] = $node;
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

    public function createEnrolmentLogGoodCsv($questionSetId)
    {
        $filename = $this->directory.'/enrolment-log-good.csv';

        $csv = Writer::createFromFileObject(new \SplFileObject(storage_path($filename), 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['OfflineId', 'Trust', 'HospitalName', 'Ward', 'QuestionNumber', 'QuestionTextEnglish', 'IncidentReportDescription']);

        $prefetchedNotes = PRNote::has('concern', '<', 1)
            ->whereHas('record', function ($q) use ($questionSetId) {
                return $q->wherePmosId($questionSetId);
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

    public function createEnrolmentLogConcernCsv($questionSetId)
    {
        $filename = $this->directory.'/enrolment-log-concern.csv';

        $csv = Writer::createFromFileObject(new \SplFileObject(storage_path($filename), 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['OfflineId', 'Trust', 'HospitalName', 'Ward', 'QuestionNumber', 'QuestionTextEnglish', 'IncidentReportDescription', 'IncidentReportDescription', 'HowSerious', 'PossibleToStop']);

        $prefetchedConcerns = PRConcern::whereHas('record', function ($q) use ($questionSetId) {
                return $q->wherePmosId($questionSetId);
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
                $concern->text, // Incident report
                $concern->serious_answer,
                $concern->prevent_answer,
            ];
        }

        $csv->insertAll($concerns);

        return $filename;
    }

    public function createKeyCsv($questionSetId)
    {
        $filename = $this->directory.'/key.csv';

        $csv = Writer::createFromFileObject(new \SplFileObject(storage_path($filename), 'w+'));
        $csv->setDelimiter(',');

        $csv->insertOne(['QuestionId', 'QuestionText', 'OptionId', 'OptionText']);

        $prefetchedAnswerTypes = Node::whereNodeType(6)->join('node_type_6', 'nodes.id', '=', 'node_type_6.node_id')->get(['nodes.*', 'node_type_6.options AS options'])->keyBy('id');
        $prefetchedAnswerTypeOptions = Node::whereNodeType(5)->join('node_type_5', 'nodes.id', '=', 'node_type_5.node_id')->get(['nodes.*', 'node_type_5.answerValue AS answerValue'])->keyBy('id');

        $questions = PRRecord::wherePmosId($questionSetId)
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
