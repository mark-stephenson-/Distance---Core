<?php

use Carbon\Carbon;
use Core\Services\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
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
        $trusts = Node::whereNodeTypeIs($this->trustNodeType, 'published')
            ->whereUserHasAccess('export-data')
            ->lists('name', 'node_id');

        $trusts = ['' => 'Please select a Trust'] + $trusts;

        $reportService = new ReportService();
        $standardReports = $reportService->getStandardReports();

        return View::make('reporting.index', compact('trusts', 'standardReports'));
    }

    public function hospitals($trustId)
    {
        $hospitals = Node::whereNodeTypeIs($this->hospitalNodeType, 'published')
            ->where('trust', $trustId)
            ->whereUserHasAccess('export-data')
            ->lists('node_type_3.name', 'node_id');

        $hospitals = ['' => 'Please select a Hospital'] + $hospitals;

        return json_encode($hospitals);
    }

    public function wards($hospitalId)
    {
        $search = Input::get('q');

        $wards = Node::whereNodeTypeIs($this->wardNodeType, 'published')
            ->where('hospital', $hospitalId)
            ->whereUserHasAccess('export-data');

        if ($search) {
            $wards = $wards->where('name', 'LIKE', "%{$search}%");
        }

        $wards = $wards->get();

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
                ->get([
                    'pmos_id',
                    'start_date',
                    'nodes.created_at',
                    'nodes.published_at',
                    'nodes.retired_at',
                    DB::raw('COUNT(prase_records.id) AS results'),
                ]);

            $selects = [];

            foreach ($questionSets as $questionSet) {
                $selects[$questionSet->pmos_id] = "Created: {$questionSet->created_at}, Published: {$questionSet->published_at}, Retired: {$questionSet->retired_at} RESULTS: {$questionSet->results}";
            }

            if (count($selects) > 1) {
                return Response::make(json_encode($selects), 416);
            }

            if (!count($questionSets)) {
                return Response::make('No data available within the report period selected.', 404);
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

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf->setOptions($options);

        //file_put_contents(storage_path("reports/{$fileKey}.html"), $pdfHtml);

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
        $type = '';
        if(Input::get('type')) {
            $type = 'standard/';
        }
        $filePath = storage_path("reports/{$type}{$fileKey}.json"); //no json is being generated
        return json_decode(file_get_contents($filePath));
    }

    public function updateStandardReportsTable()
    {
        $reportService = new ReportService();

        $standardReports = $reportService->getStandardReports();

        $markup = View::make('reporting.partials.standard-reports-table', compact('standardReports'))->__toString();

        return Response::json(['status' => 'success', 'markup' => $markup]);
    }
}
