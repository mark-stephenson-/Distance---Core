<?php

namespace Core\Services;

use Carbon\Carbon;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ReportService
{
    public function generateReportForQuestionSet($pmosId, $wardId, Carbon $startDate, Carbon $endDate)
    {
        $records = \PRRecord::where('ward_node_id', $wardId)
            ->with(['questions.answer', 'questions.node'])
            ->wherePmosId($pmosId)
            ->where('start_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get();

        $answerOptions = (new Collection(DB::table('node_type_5')->get()))->keyBy('node_id');
        $questionData = (new Collection(DB::table('node_type_1')->join('i18n_strings', 'node_type_1.question', '=', 'i18n_strings.key')->get(['*', 'i18n_strings.value AS value'])))->keyBy('node_id');

        $ward = DB::table('node_type_4')->where('status', 'published')->where('node_id', $wardId)->first();
        $hospital = DB::table('node_type_3')->whereNodeId($ward->hospital)->first();
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
            'ward' => $ward->name,
            'wardId' => $wardId,
            'hospital' => $hospital->name,
            'hospitalId' => $hospital->node_id,
            'trust' => $trust->name,
            'trustId' => $trust->node_id,
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
                                'text' => isset($question->concern->note) ? $question->concern->note->text : null,
                                'preventability' => (is_null($question->concern->prevent_answer)) ? 4 : $question->concern->prevent_answer,
                                'severity' => (is_null($question->concern->serious_answer)) ? 5 : $question->concern->serious_answer,
                            ];
                        }

                        // Check for notes
                        if ($question->note) {
                            if ($question->concern) {
                                if ($question->concern->prase_note_id != $question->note->id) {
                                    $domainData['questions'][$question->node->id]['notes'][] = [
                                        'text' => $question->note->text,
                                    ];
                                }
                            } else {
                                $domainData['questions'][$question->node->id]['notes'][] = [
                                    'text' => $question->note->text,
                                ];
                            }
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

    public function getStandardReports()
    {
        $standardReportsPath = storage_path("reports");

        $standardReportFiles = new Collection(scandir($standardReportsPath, SCANDIR_SORT_DESCENDING));

        $standardReports = $standardReportFiles->filter(function ($item) {
            return str_contains($item, '.json');
        })->map(function ($reportFile) use ($standardReportsPath) {
            $report = json_decode(file_get_contents($standardReportsPath . '/' . $reportFile));
            list($timestamp, $extension) = explode('.', $reportFile);

            $report->generated_at = (new Carbon())->createFromTimestampUTC($timestamp)->format('d/m/Y H:i');
            $report->fileName = $timestamp;
            return $report;
        })->filter(function ($report) {
            $getFilterResult = true;

            if(Input::get('ward_id')) {
                $getFilterResult = Input::get('ward_id') == $report->wardId;
            }
            elseif(Input::get('hospital_id')) {
                $getFilterResult = $report->hospitalId == Input::get('hospital_id');
            }
            elseif(Input::get('trust_id')) {
                $getFilterResult = $report->trustId == Input::get('trust_id');
            }

            $userHasAccess = Sentry::getUser()->canAccessNodes($report->wardId);

            return $getFilterResult && $userHasAccess;
        });

        return $standardReports;
    }

    public function generateStandardReports($chunks = 20)
    {
        
    }
}