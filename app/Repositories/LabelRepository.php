<?php
namespace App\Repositories;

use App\Models\Label;

class LabelRepository extends BaseRepository
{
    public function model()
    {
        return Label::class;
    }

    public function getAllUserLabels($userId)
    {
        return $this->model
            ->whereNull('deleted_at')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', null)
                    ->orwhere('user_id', $userId);
            })
            ->get();
    }


}