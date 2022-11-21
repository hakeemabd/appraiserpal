<?php
namespace App\Repositories;

use App\Models\ReportType;

class ReportTypeRepository extends BaseRepository
{
    public function model()
    {
        return ReportType::class;
    }
}