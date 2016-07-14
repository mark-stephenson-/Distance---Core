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
        $questionData = (new Collection(DB::table('node_type_1')->get()))->keyBy('node_id');

        $wards = new Collection(DB::table('node_type_4')->whereIn('node_id', $wardIds)->get());
        $hospital = DB::table('node_type_3')->whereNodeId($wards[0]->hospital)->first();
        $trust = DB::table('node_type_2')->whereNodeId($hospital->trust)->first();

        $domains = DB::table('node_type_10')->get();

        $reportData = [
            'submissions' => [
                'total' => $records->count(),
                'male' => 0,
                'female' => 0,
            ],
            'notes' => [],
            'concerns' => [],
            'ward' => $wards->implode('name', ', '),
            'hospital' => $hospital->name,
            'trust' => $trust->name,
        ];

        foreach($domains as $domain) {
            $domainData = [
                'name' => $domain->title,
                'summary' => [
                ],
                'questions' => [],
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
                        $domainData['questions'][$question->node->id]['text'] = $question->node->title;
                        if (!isset($domainData['questions'][$question->node->id]['answers'][$answerValue])) {
                            $domainData['questions'][$question->node->id]['answers'][$answerValue] = 0;
                        }
                        $domainData['questions'][$question->node->id]['answers'][$answerValue]++;
                    }
                }
            }

            $reportData['domains'][$domain->node_id] = $domainData;

        }

        foreach($records as $record) {
            $reportData['submissions'][strtolower($record->basicData()->Gender)]++;
        }

        die(json_encode($reportData));

        return $reportData;
    }
}