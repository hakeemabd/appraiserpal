<?php

namespace App\Jobs;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use SuperClosure\Analyzer\TokenAnalyzer;
use SuperClosure\Serializer;

class MailJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $view;
    protected $data;
    protected $callback;
    protected $viewPath;
    protected $wasNotified;

    /**
     * Create a new job instance.
     *
     * @param      $view      string  View name to use for message
     * @param      $data      array Data to pass to the view
     * @param      $callback  callable Array with configs (to, subject etc)
     * @param null $viewPath  string Prefix for the views (admin|customer|worker). Detected automatically when adding
     *                        job from Controller
     */
    public function __construct($view, $data, $callback, $viewPath = null)
    {
        $serializer = new Serializer(new TokenAnalyzer());
        //remember current view path
        $this->viewPath = $viewPath === null ? env('VIEW_PATH') : $viewPath;
        $this->view = $view;
        $this->data = $data;
        //serialize callback
        $this->callback = $serializer->serialize($callback);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $finder = new FileViewFinder(app()['files'], array(realpath(base_path('resources/views' . $this->viewPath))));
        View::setFinder($finder);
        $serializer = new Serializer(new TokenAnalyzer());
        //unserialize callback
//        dd($this->view, $this->data, $serializer->unserialize($this->callback));
        $this->wasNotified = Mail::send($this->view, $this->data, $serializer->unserialize($this->callback));
    }
}