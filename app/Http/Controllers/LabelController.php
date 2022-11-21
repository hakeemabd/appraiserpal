<?php
namespace App\Http\Controllers;

use App\Repositories\LabelRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LabelController extends Controller
{
    private $labelRepository;

    public function __construct(LabelRepository $labelRepository)
    {
        $this->labelRepository = $labelRepository;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $label = $this->labelRepository->create($request->only('user_id', 'name'));
        return response()->json($label);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $label = $this->labelRepository->find($id);
        if ($label->user_id != $request->user_id) {
            throw new AccessDeniedHttpException();
        }
        $label->name = $request->name;
        $label->save();
        return response()->json($label);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function destroy($id, Request $request)
    {
        $label = $this->labelRepository->find($id);
        if ($label->user_id != $request->user_id) {
            throw new AccessDeniedHttpException();
        }
        $this->labelRepository->delete($id);
        return response()->json();
    }

    public function index(Request $request)
    {
        return response()->json($this->labelRepository->getAllUserLabels($request->user_id));
    }
}