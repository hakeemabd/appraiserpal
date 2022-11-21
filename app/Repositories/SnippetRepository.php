<?php
namespace App\Repositories;

use App\Models\Snippet;

class SnippetRepository extends BaseRepository
{
    public function model()
    {
        return Snippet::class;
    }

    /**
     *
     * @param string $name emailSubject(slug), get, pageTitle, pageDescription, pageKeywords
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $data = [
            'order.invitation' => [
                'emailFromAddress' => env('WORKER_MAIL_FROM_ADDRESS'),
                'emailFromName' => env('WORKER_MAIL_FROM_NAME'),
                'emailSubject' => 'You have been invited to work an appraisal report',
            ],
        ];
        if (isset($data[$arguments[0]]) && isset($data[$arguments[0]][$name])) {
            return $data[$arguments[0]][$name];
        }
        return $arguments[0];
    }
}
