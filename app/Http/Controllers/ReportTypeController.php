<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\ReportTypeRequest;
use App\Models\ReportType;
use App\Repositories\ReportTypeRepository;
use yajra\Datatables\Datatables;

class ReportTypeController extends Controller
{
    private $reportTypeRepository;

    public function __construct(ReportTypeRepository $reportTypeRepository)
    {
        $this->reportTypeRepository = $reportTypeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['report_types' => $this->reportTypeRepository->all()]);
    }

    public function view()
    {
        return view('order.report-type.index');
    }

    public function getReportTypes()
    {
        return Datatables::of($this->reportTypeRepository->all())
            ->addRowData('actions', function ($model) {
                $actions['edit'] = [
                    'link' => route('admin:reportType.update', ['report_type' => $model->id]),
                    'ajax' => false,
                ];

                $actions['delete'] = [
                    'link' => route('admin:reportType.delete', ['id' => $model->id]),
                    'confirm' => true,
                    'confirm-type' => 'remove',
                ];

                return $actions;
            })
            ->make(true);
    }

    public function showForm(ReportType $reportType)
    {
        return view('order.report-type.reportTypeForm', compact('reportType'));
    }

    public function destroy($id)
    {
        return response()->json(
            $this->reportTypeRepository->delete($id)
        );
    }

    /**
     * @param ReportTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @todo refactor
     */
    public function store(ReportTypeRequest $request, $id = false)
    {
        $requestParams = $request->only([
            'name',
            'current_price',
            'old_price',
        ]);
        if ($id) {
            return response()->json(
                $this->reportTypeRepository->update($requestParams, $id)
            );
        }

        return response()->json(
            $this->reportTypeRepository->create($requestParams)
        );
    }
}
