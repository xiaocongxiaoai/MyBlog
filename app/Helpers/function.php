<?php
    //By xcc
    //全局类GetUserActions 运用算法用于推荐blog

    class GetUserActions
    {
       public $test = [1,2,3];
        //GUA(GetUserAction)运用算法用于推荐blog，改方法直接返回5篇推荐blog的Id
        function GUA(){
            dd($this->test);
        }

        //SUA(SetUserAction)用作后台定时运作任务，用于更新GUA需要运用的全局变量
        function SUA(){


        }
    }


