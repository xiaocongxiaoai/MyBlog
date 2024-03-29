<?php

namespace App\Console\Commands;

use App\UserAction;
use Carbon\Traits\Date;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

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
        date_default_timezone_set('PRC');//统一当前时间区间为北京
        //获取与更新逻辑写在此处
        //从数据库中获取数据存入txt中



        echo "获取初始数据中...    ".date( "Y:m:d H:i:s")."\n";

        if($this->createfile("app/Console/Data/StartData.txt")){
            echo "数据获取成功！------".date( "Y:m:d H:i:s")."\n";
            $mylog = fopen("app/Console/Log.txt", "a");
            fputs($mylog, getenv("REMOTE_ADDR").'/'.date( "Y:m:d H:i:s")."/EditLogfile\n");
            fclose($mylog);
            //转换数据格式
            if($this->createfileend("app/Console/Data/EndData.txt",file("app/Console/Data/StartData.txt")))
            {
                echo "数据转换成功！------".date( "Y:m:d H:i:s")."\n";
            }

        }else{
            echo "数据获取失败！------".date( "Y:m:d H:i:s")."\n";
        }
        //获取数据库数据
        //更新命令执行的记录文件
    }

    public function createfile($url){
        try {

        //先判断删除原有文件
         if(is_dir($url)){
             unlink($url);
         }
        $action = DB::select('
            select userId,blogInfoId from t_user_action group by userId,blogInfoId
        ');
        $myfile = fopen($url, "w");
        foreach ($action as $a){
            fwrite($myfile, 'userId:'.$a->userId.';blogInfoId:'.$a->blogInfoId."\n");
        }

        fclose($myfile);
        return true;
        }catch (Exception $e){
            //记录异常
            $mylog = fopen("app/Console/Log.txt", "w");
            fwrite($mylog,getenv("REMOTE_ADDR").'/'.date( "Y:m:d H:i:s").'/'.$e->getMessage()."\n");
            fclose($mylog);
        }
    }

    public function createfileend($url,$file){
        try {
            //先判断删除原有文件
            if(is_dir($url)){
                unlink($url);
            }
            echo "数据转换中请稍后...   ".date( "Y:m:d H:i:s")."\n";
            $myfile = fopen($url, "w");
            $infoend=[];
            for($i=0;$i<count($file);$i++)//逐行读取文件内容
            {
                //取id 和 博客id
                $info = str_replace(['userId:','blogInfoId:'],'',$file[$i]);

                $infos = explode(";",$info);
                $infoend[$infos[0]] = $infoend[$infos[0]] == null?$infoend[$infos[0]].";".$infos[1] :$infos[1];
            }

            foreach ($infoend as $k=>$v){
                fwrite($myfile, $k.":".$v);
            }

            fclose($myfile);
            return true;
        }catch (Exception $e){
            //记录异常 一般不会到这 兜底操作
            $mylog = fopen("app/Console/Log.txt", "w");
            fwrite($mylog,getenv("REMOTE_ADDR").'/'.date( "Y:m:d H:i:s").'/'.$e->getMessage()."\n");
            fclose($mylog);
        }
    }
}
