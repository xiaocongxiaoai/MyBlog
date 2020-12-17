{{--///所有页面的公共模板页--}}
{{--侧面菜单和顶层菜单--}}
<head>
    <meta charset="UTF-8">
    <!-- import CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <!-- 引入组件库 -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="{{asset('jquery/jquery.js')}}"></script>
</head>
<body>
<div id="app">
<el-container style="height: 100%; border: 1px solid #eee">
    <el-drawer
        title="我是标题"
        :visible.sync="drawer"
        :with-header="false">
        <el-card :body-style="{ padding: '10px' }" shadow="hover">
            <div style="padding: 14px">
                <el-collapse v-model="activeNames">
                    <el-collapse-item title="个人信息" name="1">
                    <div style="margin-left: 8%">
                        <el-avatar src="{{route('/img/admin/2.jpg')}}"  :size = "200" :fit="none"  class="image"></el-avatar>
                    </div>
                    <div style="padding: 14px;text-align: center" >
                        <span>你好管理员</span>
                        <h4 >用户名：<span v-text="userinfo.name"></span></h4>
                        <h4>电话号码：<span v-text="userinfo.phoneNum"></span></h4>
                        <h4>邮箱：<span v-text="userinfo.email"></span></h4>
                    </div>
                    </el-collapse-item>
                </el-collapse>
            </div>
            <div style="padding: 14px;">
                <el-collapse v-model="activeNames">
                    <el-collapse-item title="个人简介" name="2">
                        <div v-text="userinfo.summary"></div>
                    </el-collapse-item>
                </el-collapse>
            </div>
        </el-card>
        <div class="block" style="padding: 14px">
            <el-scrollbar style="height:81%">
                <div style="width:500px;height:100%" >
                <el-timeline>
                    <el-timeline-item v-for="i in bloginfo" :timestamp="i.created_at" placement="top">
                        <el-card>
                            <h3 v-text="i.blogTitle" style="font-family:'Script MT Bold'"></h3>
                            <h5 v-text="i.blogContent"></h5>
                            <p v-text="i.name" style="float: left">
                            <p style="float: left;margin-left: 10px;margin-top: 12px">提交</p>
                        </el-card>
                    </el-timeline-item>
                </el-timeline>
                </div>
            </el-scrollbar>
        </div>
    </el-drawer>
{{--    <el-button @click="drawer = true" type="primary" style="margin-left: 16px;">--}}
{{--        点我打开--}}
{{--    </el-button>--}}
    <el-header style="text-align: right; font-size: 12px">

        <el-badge :value="remake" class="item" style="margin-right: 20px;margin-top: -24px">
            <el-button size="mini" @click="change">留言板</el-button>
        </el-badge>
        <el-dropdown @command="handleCommand">
{{--            <i class="el-icon-setting" style="margin-right: 15px"></i>--}}
            <el-avatar src="http://10.20.38.251/1.jpg" :size = "35" class="image" style="margin-top: 10px" :fit = 'none'></el-avatar>
{{--            <el-avatar :size="35" :src="circleUrl" style="margin-top: 10px;background-color: #f8fafc"><span style="font-size:initial;color: #3490dc;">王</span></el-avatar>--}}
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="MyInfo">我的信息</el-dropdown-item>
                <el-dropdown-item command="LoginOut">登出</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </el-header>

    <el-container style="height: 50%">

        <el-aside  width="250px" style="background-color: #545c64" >
            <el-row class="tac">
                <el-col :span="24">
                    <el-menu class="el-menu-vertical-demo"
                             background-color="#545c64"
                             style="border: 0px"
                             text-color="#fff"
                             active-text-color="aqua">

                        <el-submenu index="1">
                            <template slot="title"><i class="el-icon-tickets" ></i><span slot="title">博客管理</span></template>
                                <el-menu-item index="1-1" style="font-size: small"><i class ="el-icon-document"></i>用户博客</el-menu-item>
                                <el-menu-item index="1-2"><i class="el-icon-view"></i>博客审核</el-menu-item>
                            <el-submenu index="1-3">
                                <template slot="title"><i class="el-icon-s-tools"></i><span slot="title">博客基础信息设置</span></template>
                                <el-menu-item index="1-3-1"><i class="el-icon-setting"></i>类别管理</el-menu-item>
                                <el-menu-item index="1-3-2"><i class="el-icon-set-up"></i>标签管理</el-menu-item>
                            </el-submenu>
                        </el-submenu>
                        <el-submenu index="2">
                            <template slot="title"><i class="el-icon-user"></i><span slot="title">用户管理</span></template>
                            <el-menu-item-group>
                                <el-menu-item index="2-1"><i class="el-icon-user-solid"></i>网站用户管理</el-menu-item>
                                <el-menu-item index="2-2"><i class="el-icon-message"></i>举报信息</el-menu-item>
                            </el-menu-item-group>
{{--                            <el-submenu index="2-4">--}}
{{--                                <template slot="title">选项4</template>--}}
{{--                                <el-menu-item index="2-4-1">选项4-1</el-menu-item>--}}
{{--                            </el-submenu>--}}
                        </el-submenu>
                        <el-submenu index="3">
                            <template slot="title"><i class="el-icon-data-analysis"></i><span slot="title">新闻公告管理</span></template>
                            <el-menu-item-group>
                                <el-menu-item index="3-1" ><i class="el-icon-bell"></i>新闻公告推送管理</el-menu-item>
                                <el-menu-item index="3-2"><i class="el-icon-chat-line-square"></i>我的留言</el-menu-item>
                            </el-menu-item-group>
{{--                            <el-menu-item-group title="分组2">--}}
{{--                                <el-menu-item index="3-3">选项3</el-menu-item>--}}
{{--                            </el-menu-item-group>--}}
{{--                            <el-submenu index="3-4">--}}
{{--                                <template slot="title">选项4</template>--}}
{{--                                <el-menu-item index="3-4-1">选项4-1</el-menu-item>--}}
{{--                            </el-submenu>--}}
                        </el-submenu>
                    </el-menu>
                </el-col>
            </el-row>
        </el-aside>

        <el-main>
            <div>
                @yield('content')
            </div>
{{--            <el-table :data="tableData">--}}
{{--                <el-table-column prop="date" label="日期" width="140">--}}
{{--                </el-table-column>--}}
{{--                <el-table-column prop="name" label="姓名" width="120">--}}
{{--                </el-table-column>--}}
{{--                <el-table-column prop="address" label="地址">--}}
{{--                </el-table-column>--}}
{{--            </el-table>--}}
        </el-main>
    </el-container>
</el-container>

</div>
</body>
<style>
    .el-header {
        background-color: #000000;
        color: #f8fafc;
        line-height: 60px;
    }
    .el-aside {
        color: #333;
    }
    .image {
        width: 50%;
        display: block;
    }
    .el-timeline-item__wrapper{
        width: 90%;
    }
</style>
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script type="text/javascript">
    new Vue({
        el: '#app',
        data() {
            return {
                remake: 0,
                drawer:false,
                activeNames: ['1'],
                none:"fill",
                userinfo:[],
                bloginfo:[],
                test:"1992/2/2",
                @yield('data')
            }
        },
        mounted: function () {
            @yield('mounted')
        },
        methods:{
            handleCommand:function(command) {
                var that = this
                if(command =="MyInfo"){
                    this.drawer = true
                    var that = this
                    $.ajax({
                        type: 'GET',
                        url: '/userinfo',
                        data: { userId : '{{\Illuminate\Support\Facades\Auth::user()->userOnlyId}}'},
                        dataType: 'json',
                        headers: {
                            //'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(data){
                            that.userinfo = data.userinfos
                            that.bloginfo = data.bloginfos
                        },
                        error: function(xhr, type){
                            alert('Ajax error!')
                        }
                    });
                }else{
                    //登出
                    $.ajax({
                        type: 'GET',
                        url: '/logout',
                        data: '',
                        dataType: 'json',
                        headers: {},
                        success: function(data){
                            that.$message({
                                message: "管理员再见",
                                type: 'error',
                                duration:1000,
                            });
                            window.location.href=""
                        },
                        error: function(xhr, type){
                            alert('Ajax error!')
                        }
                    });
                }
            },
            errorHandler(){
                return true
            },
            change:function () {
                this.remake+=1
            },
            @yield('function')
        }
    })
</script>
