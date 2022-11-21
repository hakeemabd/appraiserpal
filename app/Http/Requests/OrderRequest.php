<?php

namespace App\Http\Requests;

use App\Models\Attachment;
use App\Models\Order;

class OrderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // start 1 step
            'software_id' => 'integer|exists:softwares,id',
            'collect_data_type' => 'string|in:"' . Attachment::TYPE_DATA_MANUAL . '", "' . Attachment::TYPE_DATA_MOBILE . '"',
            'has_subject' => 'boolean', // @todo required in 1 step
            'has_sketch' => 'boolean', // @todo required in 1 step
            'has_contract' => 'boolean', // @todo required in 1 step
            'has_comparable_info' => 'boolean',
            'has_mls' => 'boolean',
            'has_mc_1004' => 'boolean',
            'clone' => 'array', // required in 1 step ['id' => 1, 'key' => s3key, 'label' => 'label']
//            'data_file_mobile' => 'required_if:collect_data_type,"' . Attachment::TYPE_DATA_MOBILE . '"|array',
//            'data_file_manual' => 'required_if:collect_data_type,"' . Attachment::TYPE_DATA_MANUAL . '"|array',
            'contract' => 'required_if:has_contract,0|array',
            'subject' => 'required_if:has_subject,0|array',
            'sketch' => 'required_if:has_sketch,0|array',
            'comparable_info' => 'required_if:has_comparable_info,0|array',
            'mc_1004' => 'required_if:has_mc_1004,0|array',
            'mls' => 'required_if:has_mls,0|array',
            'miscellaneous' => 'array',
            // end 1 step
            // start 2 step
            'comparables' => 'array',
            // end 2 step
            // start 3 step
            'title' => 'string',
            'effective_date' => 'date',
            'report_type_id' => 'integer',
            'sample' => 'array',
            'assignment_type' => 'string|in:purchase,refinance,other',
            'financing' => 'string|in:conventional,fha,other',
            'occupancy_type' => 'string|in:owner,tenant,vacant',
            'property_rights' => 'string|in:fee simple,leasehold,other',
            'standard_instructions' => 'string',
            'specific_instructions' => 'string',
            // end 3 step
            // start 4 step
            'adjustment_type' => 'string|in:"' . Order::ADJ_TYPE_ADD_REGRESSION . '","' . Order::ADJ_TYPE_NONE . '","' . Order::ADJ_TYPE_UPLOAD . '"',
            'adj_sheets' => 'required_if:adjustment_type,' . Order::ADJ_TYPE_UPLOAD . '|array',
            // end 4 step
            // start 5 step
//            'photo' => 'array',
            // end 5 step
            'total' => 'numeric',
            'completed' => 'boolean',
            'user_id',
        ];
    }
}
