@extends('base')
@section('content')
    <div>
        <span>这是我的地盘！</span>
    </div>
    <el-table
        :data="tableData.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
        style="width: 100%;margin-top: 20px"
        :row-class-name="tableRowClassName">
        <el-table-column
            prop="created_at"
            label="日期"
            width="180"
            align="center">
        </el-table-column>
        <el-table-column
            prop="blogTitle"
            label="博客标题"
            width="450">
        </el-table-column>
        <el-table-column
            prop="type"
            label="博客类别"
            width="100">
        </el-table-column>
        <el-table-column
            prop="name"
            label="作者"
            width="200">
        </el-table-column>
        <el-table-column
            prop="readNum"
            label="阅读人数"
            width="100">
        </el-table-column>
        <el-table-column
            prop="isSuspicious"
            label="状态"
            width="100">
        </el-table-column>
        <el-table-column
            align="right"
            fixed="right"
            width="250"
            >
            <template slot="header" slot-scope="scope">
                <el-input
                    v-model="search"
                    size="mini"
                    placeholder="输入关键字搜索(作者)"/>
            </template>
            <template slot-scope="scope">
                <el-button
                    size="mini"
                    @click="handleEdit(scope.$index, scope.row)">审核</el-button>
                <el-button
                    size="mini"
                    type="danger"
                    @click="handleDelete(scope.$index, scope.row)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
    <el-dialog title="审核页面" :visible.sync="dialogFormVisible">
        <el-form ref="form" :model="form" :label-position = "left">
            <el-form-item label="博客标题">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item label="作者">
                <el-input v-model="form.name"></el-input>
            </el-form-item>
            <el-form-item label="">
                <el-divider><span>正文</span></el-divider>
            </el-form-item>
            <el-form-item>
                <el-input v-html="form.content"></el-input>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer" style="text-align: center">
            <el-button @click="dialogFormVisible = false" type="danger" round>实 锤</el-button>
            <el-button type="success" @click="dialogFormVisible = false" round>通 过</el-button>
        </div>
    </el-dialog>
    <div style="text-align: center;margin-top: 20px">
        <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page="currentPage4"
            :page-sizes="[10, 20, 30, 40]"
            :page-size="10"
            layout="total, sizes, prev, pager, next, jumper"
            :total="total">
        </el-pagination>
    </div>
@endsection
{{--数据绑定--}}
@section('data')
    tableData: [],
    tableDatas:{
    blogId:'',
    date: '',
    name: '',
    title: '',
    type:'',
    readNum:'',
    isSuspicious:''
    },
    search: '' ,
    index : 1  ,
    count : 10 ,
    currentPage4: 1,
    total : '0',
    dialogFormVisible : false,
    form : {
        title : '',
        name : '',
        content: ''
    }
@endsection
{{--事件绑定操作--}}
@section('function')
    tableRowClassName({row, rowIndex}) {
    if (row.isSuspicious === '异常') {
    return 'warning-row';
    }},
    handleSizeChange(val) {
        this.count = val;
        var that = this
        $.ajax({
        type: 'GET',
        url: '/blogList',
        data: { index : that.index,count : that.count},
        dataType: 'json',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
        that.tableData = []
        data.data.forEach(item=>{

        if(item.isSuspicious == 1){
        item.isSuspicious = '异常'
        }
        if(item.isSuspicious == 0){
        item.isSuspicious = '正常'
        }
        if(item.isSuspicious == 2){
        item.isSuspicious = '被举报'
        }
        that.tableData.push(item)
        })
        that.total = data.counts
        },
        error: function(xhr, type){
        alert('Ajax error!')
        }
        })
    },
    handleCurrentChange(val) {
        this.index = val;
        var that = this
        $.ajax({
        type: 'GET',
        url: '/blogList',
        data: { index : that.index,count : that.count},
        dataType: 'json',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){
        that.tableData = []
        data.data.forEach(item=>{

        if(item.isSuspicious == 1){
        item.isSuspicious = '异常'
        }
        if(item.isSuspicious == 0){
        item.isSuspicious = '正常'
        }
        if(item.isSuspicious == 2){
        item.isSuspicious = '被举报'
        }
        that.tableData.push(item)
        })
        that.total = data.counts
        },
        error: function(xhr, type){
        alert('Ajax error!')
        }
        })
    },
    handleDelete(index, row){
{{--        console.log(row.blogOnlyId);--}}
    },
    handleEdit(index, row){
    {{--审核--}}
{{--        console.log(row.blogOnlyId);--}}
        this.dialogFormVisible = true;
        var that = this
        that.form = {}
        $.ajax({
        type: 'GET',
        url: '/GetBlogInfo',
        data: { blogOnlyId : row.blogOnlyId},
        dataType: 'json',
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data){

        }

@endsection
{{--页面加载函数--}}
@section('mounted')
    var that = this
    $.ajax({
    type: 'GET',
    url: '/blogList',
    data: { index : that.index,count : that.count},
    dataType: 'json',
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(data){
    data.data.forEach(item=>{
    if(item.isSuspicious == 1){
    item.isSuspicious = '异常'
    }
    if(item.isSuspicious == 0){
    item.isSuspicious = '正常'
    }
    if(item.isSuspicious == 2){
    item.isSuspicious = '被举报'
    }
    that.tableData.push(item)
    that.total = data.counts
    })},
    error: function(xhr, type){
    alert('Ajax error!')
    }
    })
@endsection

<style>
    .el-table .warning-row {
        background: #ffbcb1;
    }

    .el-table .success-row {
        background: #f0f9eb;
    }
</style>
<script>

</script>
