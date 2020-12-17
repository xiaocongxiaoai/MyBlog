<?php

namespace App\Jobs;

use App\Messagelog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ToSql2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($info)
    {
        //
        $this->data = $info;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $info = new Messagelog();
        $info->MessageOnlyId = Uuid::uuid1();
        $info->RoomId = $this->data['roomId'];
        $info->Data = $this->data['Data'];
        if($info->save()){
            $content = "写入成功！" . time() . "\n";
            Log::info($content);
        }else{
            Log::info("写入失败！");
        }
    }
}
