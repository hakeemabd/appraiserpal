<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Page;
use yajra\Datatables\Datatables;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.pagesForm');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = false)
    {
        $htmlContent = $request->page_content;
        $request->page_content = strip_tags(str_replace('&nbsp;', '', $request->page_content));
        $this->validate($request, [
            'page_name' => 'required',
            'page_slug' => 'required',
            'page_content' => 'required'
        ]);
        $request->page_content = $htmlContent;
        if ($id) {
            return response()->json(
                \App\Models\Page::managePage($request->only(['page_name', 'page_slug', 'page_content']), $id)
            );
        }

        return response()->json(
            \App\Models\Page::managePage($request->only(['page_name', 'page_slug', 'page_content']))
        );


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (empty($id) || !is_numeric($id))
            return redirect()->back();
        $pageDetails = \App\Models\Page::find($id);
        return view('pages.pagesForm')->with(['page' => $pageDetails]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!empty($id)) {
            return response()->json(\App\Models\Page::find($id)->delete());
        }
    }


    public function getPages()
    {
        return Datatables::of(\App\Models\Page::select('id', 'page_name', 'page_slug')->get())
            ->addRowData('actions', function ($model) {
                return [
                    'edit' => [
                        'link' => route('admin:pages.edit', ['id' => $model]),
                        'ajax' => false,
                    ],
                    'delete' => [
                        'link' => route('admin:pages.destroy', ['id' => $model]),
                        'confirm' => true,
                        'confirm-header' => 'Are you sure you want to delete this page ?',
                        'confirm-message' => 'This will delete your page datas and cannot be undo !!'
                    ],
                ];
            })
            ->make(true);
    }
}
