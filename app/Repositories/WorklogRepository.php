<?php
namespace App\Repositories;

use App\Models\Worklog;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Collection;

class WorklogRepository extends Repository {

    protected $worklogRepository;
    
    public function model()
    {
        return Worklog::class;
    }

    public function first()
    {
        $this->applyCriteria();
        return $this->model->first();
    }

    /**
     * @param Worklog $worklog
     * @param $hours
     */
    public function extendDeadline(Worklog $worklog, $hours)
    {
        $worklog->deadline = $worklog->deadline->addHours($hours);
        $worklog->save();
    }

}