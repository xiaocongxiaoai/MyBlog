<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <!-- 引入组件库 -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <style>
        body{
            width: 100%;
            height: 105%;
            background: url("{{asset('img/3.jpg')}}");
            background-repeat: no-repeat;
            background-size:100% 100%;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
    <div id="app">
        <el-button @click="show = !show">Click Me</el-button>
        <div style="display: flex; margin-top: 20px; height: 100px;">
            <transition name="el-fade-in-linear">
                <div v-show="show" class="transition-box"><h1>你好</h1></div>
            </transition>
        </div>
    </div>
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
    new Vue({
        el: '#app',
        data: function() {
            return {
                visible: false ,
                show:false
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
        text-align: center;
        color: #fff;
        padding: 40px 20px;
        box-sizing: border-box;
        margin-right: 20px;
    }
</style>
</body>
<!-- import Vue before Element -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<!-- import JavaScript -->
<script src="https://unpkg.com/element-ui/lib/index.js"></script>



</html>
