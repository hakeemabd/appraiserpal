<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests;

class PageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  string $page
     *
     * @return \Illuminate\Http\Response
     */
    public function show($page)
    {
        return view('pages.' . $page);
    }
}
