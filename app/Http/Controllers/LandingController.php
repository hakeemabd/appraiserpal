<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 12/22/15
 * Time: 6:18 PM
 */

namespace App\Http\Controllers;


class LandingController extends Controller
{
    /**
     * Show the main page
     *
     * @return \Illuminate\Http\Response
     */
    public function showLanding()
    {
        return view('customer.landing.index');
    }
}