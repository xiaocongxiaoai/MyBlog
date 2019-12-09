<?php

namespace App\Http\Controllers;

use App\Console\Commands\GetUserAction;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use Hamcrest\Core\Set;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    }
}

