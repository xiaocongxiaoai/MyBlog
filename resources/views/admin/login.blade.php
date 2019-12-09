<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <!-- 引入组件库 -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="{{asset('jquery/jquery.js')}}"></script>
    <style>
        body{
            width: 100%;
            height: 105%;
            background: url("{{asset('img/admin/2.jpg')}}");
            background-repeat: no-repeat;
            background-size:100% 100%;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
<form action="">
    <div id="app">
        <el-button @click="show = !show">点击登录</el-button>
        <div style="display: flex; margin-top: 12%; height: 300px; margin-left: 40vw">
            <transition name="el-fade-in-linear">
                <div v-show="show" class="transition-box">
                    <div style="margin-top: -30px;" >
                    <h1>登录</h1>
                    </div>
                    <div style="margin-top: 20px;" class = "test">

                    <el-input placeholder="请输入账号" v-model="name" clearable>
                        <template slot="prepend">账号：</template>
                    </el-input>
                    </div>
                    <div style="margin-top: 25px">
                    <el-input placeholder="密码" v-model="password" class = "test" show-password>
                        <template slot="prepend">密码：</template>
                    </el-input>
                    </div>
                    <div>
                        <el-button plain style="margin-top: 20px;width: 100%" icon="el-icon-unlock" @click="submit">登录</el-button>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</form>
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
    new Vue({
        el: '#app',
        data: function() {
            return {
                visible: false ,
                show:false,
                name:'',
                password:''
            }
        },
        //页面加载函数
        mounted: function () {
           this.show = true;
        },
        methods:{
            submit : function(){
                var that = this
                $.ajax({
                    type: 'POST',
                    url: '/login',
                    data: { name : that.name,password : that.password},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data){
                        if(data.msg_code == 0){
                            that.$message({
                                    message: '登陆成功！',
                                    type: 'success',
                                    duration:1500,
                                }
                            ).then(
                                //延时跳转，等待当前提示框消失在执行某操作（伪实现）
                                setTimeout(function () {
                                    window.location.href="{{route('home')}}"
                                    //window.location.href="http://localhost:8000/home"
                                },2000)
                            );
                        }else{
                            that.$message({
                                    message: data.msg,
                                    type: 'error',
                                    duration:1500,
                                }
                            )
                        }
                    },
                    error: function(xhr, type){
                        alert('Ajax error!')
                    }
                })

            }
        }


    })
</script>

<style>
    .transition-box {
        margin-bottom: 10px;
        width: 400px;
        height: 300px;
        border-radius: 4px;
        background-color: #FFF0E67C;
        text-align: left;
        color: #fff;
        padding: 40px 20px;
        box-sizing: border-box;
        margin-right: 20px;
    }
    .test{
        background-color: transparent;

    }
    .el-input__inner{
        background-color: transparent;
        color: #f8fafc;
    }
</style>
</body>
<!-- import Vue before Element -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>



</html>
