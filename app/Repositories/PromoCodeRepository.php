<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 25.01.16
 * Time: 7:54
 */

namespace App\Repositories;

use App\Components\AwsS3Policy;
use App\Exceptions\SaveException;
use App\Models\Attachment;
use App\Models\Label;
use App\Models\PromoCode;

class PromoCodeRepository extends BaseRepository
{
    public function model()
    {
        return PromoCode::class;
    }
}