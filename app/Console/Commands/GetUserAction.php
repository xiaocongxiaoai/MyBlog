<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetUserAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetUserAction'; //调用命令名称、写好后调用命令php artisan GetUserAction

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';//命令描述：每隔半个小时自动更新用户动作记录数组

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();//自构函数
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取与更新逻辑写在此处

    }
}
