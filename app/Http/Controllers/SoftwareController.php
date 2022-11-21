<?php
namespace App\Http\Controllers;

use App\Models\Softwares;

class SoftwareController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(['softwares' => Softwares::all()]);
    }
}