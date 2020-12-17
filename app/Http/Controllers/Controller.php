<?php

namespace App\Http\Controllers;

use App\Console\Commands\GetUserAction;
use App\Jobs\ToSql;
use App\Messagelog;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(){
        //$info = DB::select('select *from t_user');
        return json_encode(['msg_code'=>0,'msg'=>'这是一条测试'],JSON_UNESCAPED_UNICODE);//第二个参数为将json数据格式编码更改

    }

    public function test_Bayesian(){
        $listOposts = learns();

        $listClasses = $listOposts[0];
        $listOposts = $listOposts[1];

        //得到词库
        $myVocabList = createVocabList($listOposts);
        $trainMat=[];
        foreach ($listOposts as $listOpostses){
            $trainMat[] = setOfWords2Vec($myVocabList,$listOpostses);
        }
        //训练算法
        $returns = trainNB($trainMat,$listClasses);
        //测试数据
        //分词必要设置↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        ini_set('memory_limit','1024M');
        Jieba::init();
        Finalseg::init();
        //分词必要设置↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        $testWords = "测试";
        $testWords = Jieba::cut($testWords);
        //开始
        $testWords_return = setOfWords2Vec($myVocabList,$testWords);
        $end = $this->classifyNB($testWords_return,$returns[1],$returns[0],$returns[2]);//预测结果1为侮辱性，0为正常
        return $end;
    }

    //贝叶斯分类器
    public function classifyNB($vec2Classify,$p0vec,$p1vec,$pClass1){
        foreach(range(0,count($vec2Classify)-1) as $i){
            $t1[] = $p1vec[$i]*$vec2Classify[$i];     //(P(Xn|A)/P(Xn))*P(A)
            $t0[] = $p0vec[$i]*$vec2Classify[$i];     //(P(Xn|B)/P(Xn))*P(B)
        }
        $p1 = array_sum($t1)+log($pClass1);
        $p0 = array_sum($t0)+log(1.0-$pClass1);
        if($p1>$p0){
            return "你再骂！？";
        }else{
            return "你是个懂礼貌的好孩子";
        }
    }
    ///测试
    public function test_jiami(){

//        $iv = random_bytes(16);
//        dd($iv);
//        $t="Fyi6kiI24shuDhfyNNHl3g==";
//        $t = encrypt("Fyi6kiI24shuDhfyNNHl3g==");
//
//        //加密
//        $t = decrypt($t);
//        dd($t);
        $count = Messagelog::all()->count();

        $info = Messagelog::where('RoomId','=','SZSP')
            ->offset($count>30?0:$count-30)->limit(30)
            ->orderBy('created_at','asc')->get(['Data','created_at']);
        return $info;
    }
    public function TestRedis(){
        //Redis::ltrim('Room1',0,0);   //清空队列
        Redis::rpush('Room1', rand(1,10));  // 返回列表长度 1
        $redis = Redis::lrange ('Room1', 0, -1); //返回第0个至倒数第一个, 相当于返回所有元素
        foreach ($redis as $k =>$v){
            print $v.'<br/>';
        }
    }

    public function StartBuy(Request $request){
        //定义开始抢购
        $store = 20;
        $res = Redis::llen('goods_store');
        $count = $store -$res;

        for($i= 0 ;$i<$count;$i++){
            //向队列中插入数据
            Redis::lpush('goods_store',1);
        }

        return json_encode(['msg'=>1,'msg_code'=>'OK'],JSON_UNESCAPED_UNICODE);
    }

    //Redis 队列POP 操作进行并发操作
    public function Miao(Request $request){
        //给我UserId 用于记录

        //pop 操作进行抢购
        $count = Redis::lpop('goods_store');
        if(!$count){
            return json_encode(['msg'=>0,'msg_code'=>'sorry!'],JSON_UNESCAPED_UNICODE);
        }else{

            ToSql::dispatch($request->UserId)->delay(now()->addSecond(3));  //对于用户来说 我只需要进行抢购成功 通知我就可以，后续的更改数据库或者其他操作我并不担心，所以这里用到队列，异步执行后续操作，立马返回是抢购成功
            return json_encode(['msg'=>1,'msg_code'=>'OK!'],JSON_UNESCAPED_UNICODE);
        }
    }

}

