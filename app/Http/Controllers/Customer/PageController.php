<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use \App\Models\Page;

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

    public function getAboutUsPage()
    {
        $pageDetails = \App\Models\Page::where('page_slug', 'about-us')->first();
        if (isset($pageDetails->id) && !empty($pageDetails->id)) {
            return view('pages.page')->with(['page' => $pageDetails->page_content]);
        }
    }

    public function getFaqPage()
    {
        $pageDetails = \App\Models\Page::where('page_slug', 'faq')->first();
        if (isset($pageDetails->id) && !empty($pageDetails->id)) {
            return view('pages.page')->with(['page' => $pageDetails->page_content]);
        }
    }
}
