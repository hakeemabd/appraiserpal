<?php
namespace App\Helpers;
/**
 * Created by PhpStorm.
 * User: artem
 * Date: 26.01.16
 * Time: 16:08
 */

class StripeHelper
{
    public static function getStripeKey()
    {
        return env('STRIPE_API_KEY');
    }
}