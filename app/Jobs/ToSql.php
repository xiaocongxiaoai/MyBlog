<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ToSql implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $UserId;
    public function __construct($Id)
    {
        //
        $this->UserId = $Id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //数据库操作
        $User = User::where('userOnlyId','=','b090b072-2e4e-11eb-98f0-52540043e348')->first();
        sleep(5);
        $content = $User->name. "写入！" . time() . "\n";
        Log::info($content);
    }
}
