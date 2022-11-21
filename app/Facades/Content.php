<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/22/16
 * Time: 12:29 PM
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Content extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'snippetManager';
    }
}