<?php

use App\BlogInfo;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use phpDocumentor\Reflection\Types\This;
use function foo\func;



//用于训练集
function learns(){
    //特定训练集
        $learn[] = "你妈死了傻逼儿子哈皮";
        $learn[] = "你好美姑娘我爱你";
        $learn[] = "我日你妈";
        $learn[] = "我爱你祖国";
        $learn[] = "Surprise mather fuck";
        $learn[] = "你好我叫某某";
    $classVec = [1,0,1,0,1,0];
    //数据库中的数据集
    //从数据库中取出“isSuspicious”为1的值

    $Suspicious = BlogInfo::where('isSuspicious','=','1')->count();   //数据库中带有侮辱性质的博客数量
    $NoSuspicious = BlogInfo::where('isSuspicious','=','0')->count();  //数据库中不带有侮辱性质的博客数量
    $getNum = null;
    if(BlogInfo::all()->count()>0&&$Suspicious>0&&$NoSuspicious>0){
        if($Suspicious==$NoSuspicious){
                $getNum = 0;
            }
        }else{
            if($Suspicious>$NoSuspicious){
                $getNum = $NoSuspicious;
            }else{
                $getNum = $Suspicious;
            }
        }
    if($getNum == 0){
        $blogTitle= BlogInfo::select(['blogTitle','isSuspicious'])->get();
    }else{
        $blogTitle= BlogInfo::select(['blogTitle','isSuspicious'])->paginate()->get();
    }

    //去除标点字符
    $char = "。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";
    $pattern = array(
        "/[[:punct:]]/i", //英文标点符号
        '/['.$char.']/u', //中文标点符号
        '/[ ]{2,}/'
    );

    //
    foreach($blogTitle as $blogTitles){
        $learn[]=preg_replace($pattern, ' ', $blogTitles->blogTitle);
        //同步更新$classVec
        $classVec[]=$blogTitles->isSuspicious;
    }

    //分词必要设置↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    ini_set('memory_limit','1024M');
    Jieba::init();
    Finalseg::init();
    //分词必要设置↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        foreach ($learn as $learns){
            $postingList[] =Jieba::cut($learns);

        }
        return [$classVec,$postingList];
    }

    //获取当前情况下最全面的判断词库
    function createVocabList($dataSet){
        $vocabSet=[] ;
        foreach ($dataSet as $dataSets){
            //两个数组之间取并集（数组+数组计算，求出唯一性词库）
            $vocabSet =array_merge($vocabSet,array_unique($dataSets));
        }
        $vocabSet = array_unique($vocabSet);

        return $vocabSet;
    }
    function setOfWords2Vec($vocabList,$inputSet){
        //申明一个长度为$vocabList的0数组当做0向量
        $returnVec = [];
        foreach ($vocabList as $vocabLists){
            $returnVec[] = 0;
        }
        foreach ($inputSet as $word){
            if( in_array($word,$vocabList)){
                $returnVec[array_search($word,$vocabList)] = 1;
            }
        }
        return $returnVec;
    }

    function trainNB($trainMatrix,$trainCategory){
        $numTrainDocs = count($trainMatrix);            //计算数据总数，多少条词条变量
        $numWords = count($trainMatrix[0]);             //每一个词条的字数
        $pAbusive = array_sum($trainCategory)/(float)$numTrainDocs;            //计算是不好的标题的概率
        $p0Num =[]; $p0Denom = 2.0;
        foreach ($trainMatrix[0] as $s){
            $p0Num[] = 1;
        }
        $p1Num =[]; $p1Denom = 2.0;
        foreach ($trainMatrix[0] as $s){
            $p1Num[] = 1;
        }
        foreach (range(0,$numTrainDocs-1) as $s){
            if($trainCategory[$s]==1){
                for($i=0;$i<$numWords;$i++){
                    $p1Num[$i] += $trainMatrix[$s][$i];
                }
                $p1Denom += array_sum($trainMatrix[$s]);
            }else{
                for($i=0;$i<$numWords;$i++){
                    $p0Num[$i] += $trainMatrix[$s][$i];
                }
                $p0Denom += array_sum($trainMatrix[$s]);
            }
        }
        foreach (range(0,$numWords-1) as $s){
            $p1Vect[] = log((float)($p1Num[$s]/$p1Denom));            //P(Xn|A)          已知知道数据来自坏的词向量，那么出现某些词的概率
            $p0Vect[] = log((float)($p0Num[$s]/$p0Denom));            //P(Xn|B)          已知知道数据来自好的词向量，那么出现某些词的概率
        }

        return [$p1Vect,$p0Vect,$pAbusive];
    }





