<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public static function managePage($pageData, $id = false)
    {
        if (!$id)
            $page = new Page();
        else
            $page = Page::find($id);
        foreach ($pageData as $key => $data)
            $page->$key = $data;
        $page->save();
        return $page;
    }
}
