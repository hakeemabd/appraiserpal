<?php
namespace App\Repositories;

use App\Models\Setting;

class SettingRepository extends BaseRepository
{
    public function model()
    {
        return Setting::class;
    }

    public function getSettings()
    {	
        return $this->model->get();
    }

    public function getByKey($key)
    {   
        return $this->model
            ->where(function ($query) use ($key) {
                $query->where('key', $key);
            })
            ->first();
    }
}