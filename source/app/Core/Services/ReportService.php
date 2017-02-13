<?php

namespace Core\Services;

use Carbon\Carbon;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ReportService
{
    public function generateReportForQuestionSet($pmosId, $wardIds, Carbon $startDate, Carbon $endDate)
    {
        $records = \PRRecord::whereIn('ward_node_id', $wardIds)
            ->with(['questions.answer', 'questions.node'])
            ->wherePmosId($pmosId)
            ->whereBetween('start_date', array($startDate, $endDate->endOfDay()))
            ->get();

        $reportData = $this->generateReportForRecords($records, $wardIds);

        $reportData['dates'] = [
            'start' => $startDate->toDateTimeString(),
            'end' => $endDate->toDateTimeString()
        ];

        return $reportData;
    }

    public function generateReportForRecords($records, $wardIds = array())
    {
        $wardIds  = $wardIds ? $wardIds : array($records[0]->ward_node_id);
        $answerOptions = (new Collection(DB::table('node_type_5')->get()))->keyBy('node_id');
        $questionData = (new Collection(DB::table('node_type_1')->join('i18n_strings', 'node_type_1.question', '=', 'i18n_strings.key')->get(['*', 'i18n_strings.value AS value'])))->keyBy('node_id');

        $wards = new Collection(DB::table('node_type_4')->where('status', 'published')->whereIn('node_id', $wardIds)->get());
        $hospital = DB::table('node_type_3')->whereNodeId($wards[0]->hospital)->first();
        $trust = DB::table('node_type_2')->whereNodeId($hospital->trust)->first();

        $domains = DB::table('node_type_10')->orderBy('domainValue','asc')->get();

        $reverseAnswerLookup = ['-1' => -1, '0' => 0, '1' => 5, '2' => 4, '3' => 3, '4' => 2, '5' => 1];

        $reportData = [
            'dates' => [
                'start' => $records->first()->start_date,
                'end' => $records->last()->start_date,
            ],
            'pmos_id' => $records[0]->pmos_id,
            'submissions' => [
                'total' => $records->count(),
                'male' => 0,
                'female' => 0,
            ],
            'notes' => [],
            'concerns' => [],
            'ward' => $wards->implode('name', ', '),
            'wardIds' => $wardIds,
            'hospital' => $hospital->name,
            'hospitalId' => $hospital->node_id,
            'trust' => $trust->name,
            'trustId' => $trust->node_id,
        ];

        foreach($domains as $domain) {
            $domainData = [
                'name' => $domain->title,
                'domainvalue' => $domain->domainvalue,
                'summary' => [],
                'questions' => [],
                'notes' => [],
                'concerns' => [],
            ];

            foreach($answerOptions as $option) {
                $domainData['summary'][$option->answervalue] = 0;
            }

            foreach($records as $record) {
                foreach($record->notes as $note) {
                    if ($note->prase_question_id == null && $note->concern == null) {
                        $reportData['notes'][$note->id] = [
                            'text' => $note->text,
                        ];
                    }
                }

                foreach($record->concerns as $concern) {
                    if ($concern->prase_question_id == null) {
                        $reportData['concerns'][$concern->id] = [
                            'text' => !is_null($concern->note) ? $concern->note->text : '',
                            'preventability' => (is_null($concern->prevent_answer)) ? 4 : $concern->prevent_answer,
                            'severity' => (is_null($concern->serious_answer)) ? 5 : $concern->serious_answer,
                        ];
                    }
                }

                foreach($record->questions as $question) {
                    if ($domain->node_id == $questionData[$question->node->id]->domain) {
                        // Check for concerns
                        if ($question->concern) {
                            $domainData['questions'][$question->node->id]['concerns'][] = [
                                'text' => !is_null($question->concern->note) ? $question->concern->note->text : '',
                                'preventability' => (is_null($question->concern->prevent_answer)) ? 4 : $question->concern->prevent_answer,
                                'severity' => (is_null($question->concern->serious_answer)) ? 5 : $question->concern->serious_answer,
                            ];
                        }

                        // Check for notes
                        if ($question->note) {
                            if ($question->concern) {
                                if ($question->concern->prase_note_id != $question->note->id) {
                                    $domainData['questions'][$question->node->id]['concerns'][] = [
                                        'text' => isset($question->note) ? $question->note->text : null,
                                    ];
                                }
                            } else {
                                $domainData['questions'][$question->node->id]['notes'][] = [
                                    'text' => isset($question->note) ? $question->note->text : null,
                                ];
                            }
                        }

                        // Log the answers per domain
                        if (!$question->answer) {
                            $answerValue = 0;
                        } else {
                          if($questionData[$question->node->id]->reversescore) {
                                 $answerValue = $reverseAnswerLookup[$answerOptions[$question->answer->id]->answervalue];
                             } else {
                                 $answerValue = $answerOptions[$question->answer->id]->answervalue;
                             }
                        }
                        $domainData['summary'][$answerValue]++;

                        // Now per question
                        $domainData['questions'][$question->node->id]['title'] = $question->node->title;
                        $domainData['questions'][$question->node->id]['text'] = $questionData[$question->node->id]->value;
                        $domainData['questions'][$question->node->id]['reversescore'] = $questionData[$question->node->id]->reversescore;
                        if (!isset($domainData['questions'][$question->node->id]['answers'][$answerValue])) {
                            $domainData['questions'][$question->node->id]['answers'][$answerValue] = 0;
                        }
                        $domainData['questions'][$question->node->id]['answers'][$answerValue]++;
                    }
                }

            }

            foreach($domainData['questions'] as $key => $question) {

                if (isset($question['notes'])) {
                    foreach($question['notes'] as $note) {
                        $note['question'] = $question['text'];
                        $domainData['notes'][] = $note;
                    }
                }

                if (isset($question['concerns'])) {

                    $questionConcerns = [];

                    foreach($question['concerns'] as $concern) {
                        $concern['question'] = $question['text'];
                        $questionConcerns[] = $concern;
                    }

                    $domainData['concerns'] = array_merge($domainData['concerns'], $questionConcerns);
                }
            }

            $reportData['domains'][$domain->node_id] = $domainData;

        }

        foreach($records as $record) {
            if(! empty($record->basicData()->Gender)) {
                $reportData['submissions'][strtolower($record->basicData()->Gender)]++;
            }
        }

        return $reportData;
    }

    /**
     * The reports are stored in json files
     *
     * @return array
     */
    public function getStandardReports()
    {
        $standardReportsPath = storage_path("reports/standard");

        $standardReportFiles = new Collection(scandir($standardReportsPath, SCANDIR_SORT_DESCENDING));

        $standardReports = $standardReportFiles->filter(function ($item) {

            // make sure we have only json files
            return str_contains($item, '.json');

        })->map(function ($reportFile) use ($standardReportsPath) {

            // decode the json file
            $report = json_decode(file_get_contents($standardReportsPath . '/' . $reportFile));
            list($timestamp, $extension) = explode('.', $reportFile);

            $report->generated_at = (new Carbon())->createFromTimestampUTC($timestamp)->format('d/m/Y H:i');
            $report->fileName = $timestamp;

            return $report;

        })->filter(function ($report) {

            // filter again to make sure the user has access to see the reports and also that filters apply
            $getFilterResult = true;

            if(Input::get('ward_id')) {
                $getFilterResult = in_array(Input::get('ward_id'), $report->wardIds);
            }
            elseif(Input::get('hospital_id')) {
                $getFilterResult = $report->hospitalId == Input::get('hospital_id');
            }
            elseif(Input::get('trust_id')) {
                $getFilterResult = $report->trustId == Input::get('trust_id');
            }

            $userHasAccess = true;
            if(! Sentry::getUser()->hasAccess('cms.export-data.manage.any')) {
                $userHasAccess = Sentry::getUser()->canAccessNodes($report->wardIds);
            }

            return $getFilterResult && $userHasAccess;
        })->values();

        return $standardReports;
    }

    /**
     * Generates standard reports json files
     *
     * @param int $chunkSize
     * @param bool $all
     */
    public function generateStandardReports($chunkSize = 20, $all = false)
    {
        $records = \PRRecord::with(['questions.answer', 'questions.node'])->orderBy('id', 'asc');

        if(false === $all) {
            $records->where('added_on_standard_report', 0);
        }

        $records = $records->get();

        // group by wards
        $perWardGroupedRecords = $records->groupBy('ward_node_id');

        foreach ($perWardGroupedRecords as $ward_id => $wardRecords) {

            // group by question sets
            $perPmosGroupedRecords = (new Collection($wardRecords))->groupBy('pmos_id');

            foreach ($perPmosGroupedRecords as $pmos_id => $pmosRecords) {

                // create chunks of 20
                $recordChunks = (new Collection($pmosRecords))->chunk($chunkSize);

                foreach($recordChunks as $recordChunk) {

                    if(count($recordChunk) < $chunkSize) {
                        continue;
                    }

                    // save json file with report
                    $reportData = $this->generateReportForRecords($recordChunk);

                    $reportJsonData = json_encode($reportData);

                    $fileKey = Carbon::createFromFormat('Y-m-d H:i:s', $recordChunk->last()->start_date)->timestamp;

                    file_put_contents(storage_path("reports/standard/{$fileKey}.json"), $reportJsonData);

                    // update so we know that the records were added to a report
                    $recordChunk->each(function ($record) {
                        if(! $record->added_on_standard_report) {
                            $record->added_on_standard_report = 1;
                            $record->save();
                        }
                    });

                    echo "{$fileKey}.json file was created" . PHP_EOL;
                }
            }
        }
    }
}
