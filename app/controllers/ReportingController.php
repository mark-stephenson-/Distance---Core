<?php

use Carbon\Carbon;
use Core\Services\ReportService;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ReportingController extends \BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $trusts = Node::isPublished()
            ->whereNodeType(2)
            ->join('node_type_2', 'node_id', '=', 'nodes.id')
            ->lists('node_type_2.name', 'node_id');

        $trusts = ['' => 'Please select a Trust'] + $trusts;

        return View::make('reporting.index', compact('trusts'));
    }

    public function hospitals($trustId)
    {
        $hospitals = Node::isPublished()
            ->whereNodeType(3)
            ->join('node_type_3', 'node_id', '=', 'nodes.id')
            ->where('node_type_3.trust', $trustId)
            ->lists('node_type_3.name', 'node_id');

        $hospitals = ['' => 'Please select a Hospital'] + $hospitals;

        return json_encode($hospitals);
    }

    public function wards($hospitalId)
    {
        $search = Input::get('q');

        $wards = Node::isPublished()
            ->whereNodeType(4)
            ->join('node_type_4', 'node_id', '=', 'nodes.id')
            ->where('node_type_4.hospital', $hospitalId);

        if ($search) {
            $wards = $wards->where('node_type_4.name', 'LIKE', "%{$search}%");
        }

        $wards = $wards->get(['*', 'node_type_4.name AS node_type_name']);

        $wards = $wards->map(function ($node) {
            return [
                'id' => $node->node_id,
                'text' => $node->name,
            ];
        });

        return json_encode(['results' => $wards]);
    }

    public function generate($wardIds)
    {
        $wardIds = array_filter(explode(',', $wardIds));

        if (!Input::get('startDate') or !$startDate = Carbon::createFromFormat('d-m-Y', Input::get('startDate'))->startOfDay()) {
            return Response::make('Invalid start date specified.', 400);
        }

        if (!Input::get('endDate') or !$endDate = Carbon::createFromFormat('d-m-Y', Input::get('endDate'))->startOfDay()) {
            return Response::make('Invalid end date specified.', 400);
        }

        if (!Input::get('pmosId')) {
            $questionSets = PRRecord::whereIn('ward_node_id', $wardIds)
                ->where('start_date', '>=', $startDate)
                ->where('start_date', '<=', $endDate)
                ->join('nodes', 'pmos_id', '=', 'nodes.id')
                ->groupBy('pmos_id')
                ->get(
                    [
                        'pmos_id',
                        'start_date',
                        'nodes.created_at',
                        'nodes.published_at',
                        'nodes.retired_at',
                        DB::raw('COUNT(prase_records.id) AS results'),
                    ]
                );

            $selects = [];

            foreach ($questionSets as $questionSet) {
                $selects[$questionSet->pmos_id] = "Created: {$questionSet->created_at}, Published: {$questionSet->published_at}, Retired: {$questionSet->retired_at} RESULTS: {$questionSet->results}";
            }

            if (count($selects) > 1) {
                return Response::make(json_encode($selects), 416);
            }

            if (!count($questionSets)) {
                return Response::make('There is no data available for your search parameters.', 404);
            }

            $pmosId = $questionSets->first()->pmos_id;
        } else {
            $pmosId = Input::get('pmosId');
        }

        $reportService = new ReportService();
        $reportData = $reportService->generateReportForQuestionSet($pmosId, $wardIds, $startDate, $endDate);

        $reportJsonData = json_encode($reportData);

        $fileKey = time();

        file_put_contents(storage_path("reports/{$fileKey}.json"), $reportJsonData);

        return $fileKey;
    }

    public function view($fileKey)
    {
        $reportData = $this->getReportData($fileKey);

        $start = (new Carbon($reportData->dates->start))->format('d/m/Y');
        $end = (new Carbon($reportData->dates->end))->format('d/m/Y');

        return View::make('reporting.summary', compact('reportData', 'start', 'end', 'fileKey'));
    }

    public function viewPdf($fileKey)
    {
        ini_set('max_execution_time', 300);

        $reportData = $this->getReportData($fileKey);

        $start = (new Carbon($reportData->dates->start))->format('d/m/Y');
        $end = (new Carbon($reportData->dates->end))->format('d/m/Y');

        $pdfHtml = View::make('reporting.pdf', compact('reportData', 'start', 'end', 'fileKey'));

        $pdfHtml = $pdfHtml->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdfHtml);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

//        return $dompdf->stream('my.pdf',array('Attachment'=>0));
        return $dompdf->stream(
            (new Carbon($reportData->dates->start))->format('Y-m-d')
            . "-" .
            (new Carbon($reportData->dates->end))->format('Y-m-d')
        );
    }

    public function viewCsv($fileKey)
    {
        $reportData = $this->getReportData($fileKey);

        $csvReportService = new ReportService\CSV($reportData);

        return Response::download($csvReportService->generateCSVFromReportData());
    }

    public function getReportData($fileKey)
    {
        return json_decode(file_get_contents(storage_path("reports/{$fileKey}.json")));
    }
}
