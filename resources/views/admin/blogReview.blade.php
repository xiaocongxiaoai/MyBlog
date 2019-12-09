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
    search: ''
@endsection
{{--事件绑定操作--}}
@section('function')
    tableRowClassName({row, rowIndex}) {
    if (row.isSuspicious === '异常') {
    return 'warning-row';
    }}
@endsection
{{--页面加载函数--}}
@section('mounted')
    var that = this
    $.ajax({
    type: 'GET',
    url: '/blogList',
    data: { index : 1,count : 12},
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
