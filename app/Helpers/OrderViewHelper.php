<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/15/16
 * Time: 4:06 PM
 */

namespace App\Helpers;


use App\Models\Order;

class OrderViewHelper
{
    /**
     * @link http://themeforest.net/item/angulr-bootstrap-admin-web-app-with-angularjs/full_screen_preview/8437259
     */
    public static function getStatusWrapCls($status)
    {
        $labels = [
            Order::STATUS_NEW => 'label label-info',
            Order::STATUS_CREATING => 'label label-primary',
            Order::STATUS_REWORKING => 'label label-primary',
            Order::STATUS_DONE => 'label label-primary',
            Order::STATUS_DELIVERED => 'label label-success',
            Order::STATUS_ARCHIVED => 'label bg-light dk',
            Order::STATUS_CANCELED => 'label label-danger',
            Order::STATUS_SENT_BACK => 'label label-primary',
        ];
        if (!isset($labels[$status])) {
            //throwing exception because this should never happen
            throw new \Exception("No lables is defined for status $status");
        }

        return $labels[$status];
    }

    //@todo need right method placement (smt like global helper)
    public static function getStateList()
    {
        return
            [
                ['value' => 'AL', 'text' => 'Alabama'],
                ['value' => 'AK', 'text' => 'Alaska'],
                ['value' => 'AZ', 'text' => 'Arizona'],
                ['value' => 'AZ', 'text' => 'Arizona'],
                ['value' => 'AR', 'text' => 'Arkansas'],
                ['value' => 'CA', 'text' => 'California'],
                ['value' => 'CO', 'text' => 'Colorado'],
                ['value' => 'CT', 'text' => 'Connecticut'],
                ['value' => 'DE', 'text' => 'Delaware'],
                ['value' => 'DC', 'text' => 'District of Columbia'],
                ['value' => 'FL', 'text' => 'Florida'],
                ['value' => 'GA', 'text' => 'Georgia'],
                ['value' => 'HI', 'text' => 'Hawaii'],
                ['value' => 'ID', 'text' => 'Idaho'],
                ['value' => 'IL', 'text' => 'Illinois'],
                ['value' => 'IN', 'text' => 'Indiana'],
                ['value' => 'IA', 'text' => 'Iowa'],
                ['value' => 'KS', 'text' => 'Kansas'],
                ['value' => 'KY', 'text' => 'Kentucky'],
                ['value' => 'LA', 'text' => 'Louisiana'],
                ['value' => 'ME', 'text' => 'Maine'],
                ['value' => 'MD', 'text' => 'Maryland'],
                ['value' => 'MA', 'text' => 'Massachusetts'],
                ['value' => 'MI', 'text' => 'Michigan'],
                ['value' => 'MN', 'text' => 'Minnesota'],
                ['value' => 'MS', 'text' => 'Mississippi'],
                ['value' => 'MO', 'text' => 'Missouri'],
                ['value' => 'MT', 'text' => 'Montana'],
                ['value' => 'NE', 'text' => 'Nebraska'],
                ['value' => 'NV', 'text' => 'Nevada'],
                ['value' => 'NH', 'text' => 'New Hampshire'],
                ['value' => 'NJ', 'text' => 'New Jersey'],
                ['value' => 'NM', 'text' => 'New Mexico'],
                ['value' => 'NY', 'text' => 'New York'],
                ['value' => 'NC', 'text' => 'North Carolina'],
                ['value' => 'ND', 'text' => 'North Dakota'],
                ['value' => 'OH', 'text' => 'Ohio'],
                ['value' => 'OK', 'text' => 'Oklahoma'],
                ['value' => 'OR', 'text' => 'Oregon'],
                ['value' => 'PA', 'text' => 'Pennsylvania'],
                ['value' => 'RI', 'text' => 'Rhode Island'],
                ['value' => 'SC', 'text' => 'South Carolina'],
                ['value' => 'SD', 'text' => 'South Dakota'],
                ['value' => 'TN', 'text' => 'Tennessee'],
                ['value' => 'TX', 'text' => 'Texas'],
                ['value' => 'UT', 'text' => 'Utah'],
                ['value' => 'VT', 'text' => 'Vermont'],
                ['value' => 'VA', 'text' => 'Virginia'],
                ['value' => 'WA', 'text' => 'Washington'],
                ['value' => 'WV', 'text' => 'West Virginia'],
                ['value' => 'WI', 'text' => 'Wisconsin'],
                ['value' => 'WY', 'text' => 'Wyoming']
            ];
    }
}