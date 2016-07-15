<?php

namespace Core\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateReportForQuestionSet($pmosId, $wardIds, Carbon $startDate, Carbon $endDate)
    {
        $records = \PRRecord::whereIn('ward_node_id', $wardIds)
            ->with(['questions.answer', 'questions.node'])
            ->wherePmosId($pmosId)
            ->where('start_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get();

        $answerOptions = (new Collection(DB::table('node_type_5')->get()))->keyBy('node_id');
        $questionData = (new Collection(DB::table('node_type_1')->join('i18n_strings', 'node_type_1.question', '=', 'i18n_strings.key')->get(['*', 'i18n_strings.value AS value'])))->keyBy('node_id');

        $wards = new Collection(DB::table('node_type_4')->whereIn('node_id', $wardIds)->get());
        $hospital = DB::table('node_type_3')->whereNodeId($wards[0]->hospital)->first();
        $trust = DB::table('node_type_2')->whereNodeId($hospital->trust)->first();

        $domains = DB::table('node_type_10')->get();

        $reportData = [
            'dates' => [
                'start' => $startDate->toDateTimeString(),
                'end' => $endDate->toDateTimeString(),
            ],
            'pmos_id' => $pmosId,
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
            'trust' => $trust->name,
        ];

        foreach($domains as $domain) {
            $domainData = [
                'name' => $domain->title,
                'domainvalue' => $domain->domainvalue,
                'summary' => [
                ],
                'questions' => [],
                'notes' => [],
                'concerns' => [],
            ];

            foreach($answerOptions as $option) {
                $domainData['summary'][$option->answervalue] = 0;
            }

            foreach($records as $record) {

                foreach($record->notes as $note) {
                    if ($note->prase_question_id == null) {
                        $reportData['notes'][$note->id] = [
                            'text' => $note->text,
                        ];
                    }
                }

                foreach($record->concerns as $concern) {
                    if ($concern->prase_question_id == null) {
                        $reportData['concerns'][$concern->id] = [
                            'text' => $concern->note->text,
                            'preventability' => $concern->prevent_answer,
                            'severity' => $concern->serious_answer,
                        ];
                    }
                }

                foreach($record->questions as $question) {
                    if ($domain->node_id == $questionData[$question->node->id]->domain) {
                        // Check for notes
                        if ($question->note) {
                            $domainData['questions'][$question->node->id]['notes'][] = [
                                'text' => $question->note->text,
                            ];
                        }

                        // And notes
                        if ($question->concern) {
                            $domainData['questions'][$question->node->id]['concerns'][] = [
                                'text' => $question->concern->note->text,
                                'preventability' => $question->concern->prevent_answer,
                                'severity' => $question->concern->serious_answer,
                            ];
                        }

                        // Log the answers per domain
                        if (!$question->answer) {
                            $answerValue = 0;
                        } else {
                            $answerValue = $answerOptions[$question->answer->id]->answervalue;
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

                foreach($domainData['questions'] as $question) {

                    if (isset($question['notes'])) {
                        $domainData['notes'] = array_merge($domainData['notes'], $question['notes']);
                    }

                    if (isset($question['concerns'])) {
                        $domainData['concerns'] = array_merge($domainData['concerns'], $question['concerns']);
                    }
                }
            }

            $reportData['domains'][$domain->node_id] = $domainData;

        }

        foreach($records as $record) {
            $reportData['submissions'][strtolower($record->basicData()->Gender)]++;
        }

//        die(json_encode($reportData));

        return $reportData;
    }
}