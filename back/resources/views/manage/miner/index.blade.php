@extends('manage.layouts.dashboard')

@section('container')
    <div id="app" v-cloak>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">矿机列表（共 @{{ total }} 条记录）</h3>
            </div>
            <div class="panel-body">
                <form id="search_form" class="form-horizontal" role="form">
                    <div class="form-group">
                        <div class="col-md-2" style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <span class="input-group-addon">账号状态</span>
                                <select class="form-control" name="status">
                                    <option value="">所有</option>
                                    <option value="1">正常</option>
                                    <option value="0">禁用</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="display:block; float: left;">
                            <button type="button" class="btn btn-primary" @click="clearSearch()">清 除</button>
                            <button type="button" class="btn btn-primary" @click="toSearch()">查 询</button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pop" @click="add()">新 增</button>
                        </div>

                    </div>
                </form>

            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>矿机名</th>
                    <th rel="sort" sort_key="compute_power">算力</th>
                    <th rel="sort" sort_key="account_area">产量范围</th>
                    <th rel="sort" sort_key="coin_name">产出币种</th>
                    <th rel="sort" sort_key="price">价格</th>
                    <th rel="sort" sort_key="expire_time">运行时间(天)</th>
                    <th rel="sort" sort_key="change_time">收益率变化时间(天)</th>
                    <th rel="sort" sort_key="float_per_before">变化前收益率(前区)</th>
                    <th rel="sort" sort_key="float_per_after">变化后收益率(前区)</th>
                    <th rel="sort" sort_key="agent_cp">购买后邀请人返算力</th>
                    <th rel="sort" sort_key="stock">库存</th>
                    <th>状态</th>
                    <th style="width:280px;">操作</th>
                </tr>
                </thead>
                <tbody v-for="(item, index) in list">
                <tr>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.compute_power }}</td>
                    <td>@{{ item.low }}~@{{ item.high }}</td>
                    <td>@{{ item.coin_unit }}</td>
                    <td>@{{ item.price }}</td>
                    <td>@{{ item.expire_time }}</td>
                    <td>@{{ item.change_time }}</td>
                    <td>@{{ item.float_per_before }}</td>
                    <td>@{{ item.float_per_after }}</td>
                    <td>@{{ item.agent_cp }}</td>
                    <td>@{{ item.stock }}</td>
                    <td>
                        <span style="color:#5cb85c;" v-if="item.status == 1">正常</span>
                        <span style="color:#d9534f;" v-else>禁用</span>

                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs money" data-toggle="modal" data-target="#pop" @click="edit($event)" :miner_id="item.id">编辑</button>
                        <button class="btn btn-primary btn-xs money btn-danger"  v-if="item.status == 1" :miner_id="item.id" @click="changeStatus($event,0)">禁用</button>
                        <button class="btn btn-primary btn-xs money btn-success" v-if="item.status == 0" :miner_id="item.id" @click="changeStatus($event,1)">启用</button>
                        <button class="btn btn-primary btn-xs"  data-toggle="modal" data-target="#describe_set" :miner_id="item.id" @click="openDescribe($event)">矿机说明</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div style="position:relative;top:10px;" v-html="pagehtml"></div>
            {{--模态框--}}
            <div class="modal fade" id="pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">矿机@{{pop.action_name}}</h4>
                        </div>
                        <div class="modal-body">
                            <div style="padding: 10px 10px 10px;">
                                <form id="form" class="bs-example bs-example-form" role="form">
                                    <br>
                                    <div>
                                        <div class="input-group">
                                            <span class="input-group-addon">矿机名</span>
                                            <input id="name" name="name" type="text" class="form-control" placeholder="请输入矿机名" style="width:150px;">
                                            <input id="miner_id" name="miner_id" type="hidden" value="">
                                        </div>
                                        <br>
                                        <div class="input-group" >
                                            <span class="input-group-addon" id="money_text">算力</span>
                                            <input id="cp" type="text" class="form-control" placeholder="请输入算力"  name="compute_power" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">日产量最低值</span>
                                            <input id="low" type="text" class="form-control" placeholder="请输入产量" name="low" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">日产量最高值</span>
                                            <input id="high" type="text" class="form-control" placeholder="请输入产量" name="high" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">产出币种</span>
                                            <select name="coin_type" class="form-control" id="coin_type"  style="width:150px;">
                                                <option  v-for="(item,index) in coin_list" class="coin_op" :value='item.id'>@{{ item.coin_unit }}</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">价格</span>
                                            <input id="price" type="text" class="form-control" placeholder="请输入价格" name="price" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">运行天数</span>
                                            <input id="expire" type="text" class="form-control" placeholder="请输入运行天数" name="expire_time" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">收益率变化天数</span>
                                            <input id="change_time" type="text" class="form-control" placeholder="请输入变化天数" name="change_time" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">变化时间前收益率</span>
                                            <input id="float_per_before" type="text" class="form-control" placeholder="请输入收益率(范围0~1)" name="float_per_before" style="width:160px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">变化时间后收益率</span>
                                            <input id="float_per_after" type="text" class="form-control" placeholder="请输入收益率(范围0~1)" name="float_per_after" style="width:160px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">购买后算力返点</span>
                                            <input id="agent_cp" type="text" class="form-control" placeholder="请输入算力返点" name="agent_cp" style="width:160px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">库存</span>
                                            <input id="stock" type="text" class="form-control" placeholder="请输入库存" name="stock" style="width:150px;">
                                        </div>
                                        <br>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="button" class="btn btn-primary" @click="submit()">确认提交</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal -->
            </div>
            {{--矿机说明--}}
            <div class="modal fade" id="describe_set" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">矿机说明</h4>
                        </div>
                        <div class="modal-body">
                            <div style="padding: 10px 10px 10px;">
                                <div id="describe"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="button" class="btn btn-primary" onclick="saveDescribe()">确认提交</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal -->
            </div>

        </div>

    </div>
    <link href="{{asset('summernote/dist/summernote.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{asset('summernote/dist/summernote.js')}}"></script>
    <script type="text/javascript" src="{{asset('summernote/lang/summernote-zh-CN.js')}}"></script>
    <script>
        function openDescribe(){
            $('#describe_set #describe').summernote({height: 400});
            $('#describe_set #describe').summernote('code','{!! db_config("MINER_DESCRIBE") !!}');
        }
        function saveDescribe(){
            var describe = $('#describe_set #describe').summernote('code');
            var miner_id = $('#describe_set #describe').attr('miner_id');
            $.ajax({
                url:'{{'miner.savedescribe'}}',
                data:{describe:describe,miner_id:miner_id},
                type:'post',
                success:function (res) {
                    layer.msg(res.message);
                    setTimeout(function () {
                        location.reload();
                    },1000)
                }
            })
        }
        var app = new Vue({
            el:'#app',
            data:{
                url:'{{url("miner.getlist")}}',
                list:[],
                coin_list:[],
                coin_type:0,
                total:0,
                pagehtml:'',
                sort_key:'id',
                sort_type:'asc',
                pop:{
                    action_name:'添加',
                },
                miner:{
                    id:0,
                    name:'',

                }
            },
            mounted: function () {
                this.getCointype();
                this.getList(this.url);
            },
            updated(){
                this.bindPageEvent();
            },
            methods:{
                getList(url){
                    var _this = this;
                    var data = $("#search_form").serialize();
//                    console.log(data);
                    apiPost(url,data,function(res){
                        console.log(res);
                        _this.total = res.total;
                        _this.list = res.list;
                        _this.pagehtml = res.pagehtml;
                        console.log(res.test,res.msg);
                    });
                },
                getCointype(){
                    var CointypeUrl = '{{url("miner.getcointype")}}';
                    var _this = this;
                    var data1 = [];
                    apiPost(CointypeUrl,data1,function(res){
                        console.log(res);
                        _this.coin_list = res;
                    });
                },
                bindPageEvent(){
                    var _this = this;
                    $(".pagination").find("a").unbind("click");
                    $(".pagination").find("a").bind("click",function(){
                        _this.getList($(this).attr("href"));
                        return false;
                    });
                },
                changeStatus(e,status){
                    var _this = this;
                    var url = '{{url('miner.lock')}}';
                    var data = {
                        miner_id:$(e.target).attr('miner_id'),
                        status:status
                    };
                    apiPost(url,data,function(res){
                        if(!res.errcode){
                            layer.msg('设置成功');
                            _this.getList(_this.url);
                        }else{
                            layer.msg(res.message);
                        }
                    });
                },
                add(){
                    this.pop.action_name = '添加';
                    $("#form")[0].reset();
                },
                openDescribe(e){
                    $('#describe_set #describe').summernote({height: 400});
                    var url = '{{url('miner.get')}}';
                    $('#describe_set #describe').attr('miner_id',$(e.target).attr('miner_id'));
                    var data = {miner_id:$(e.target).attr('miner_id')};
                    apiPost(url,data,function(res){
                        if(!res.errcode){
                            $('#describe_set #describe').summernote('code',res.data.describe);
                        }
                    });
                },
                edit(e){
                    this.pop.action_name = '编辑';
                    var url = '{{url('miner.get')}}';
                    var data = {miner_id:$(e.target).attr('miner_id')};
                    apiPost(url,data,function(res){
                        if(!res.errcode){
                            $("#name").val(res.data.name);
                            $("#coin_type").val(res.data.coin_type);
                            $("#cp").val(res.data.compute_power);
                            $("#price").val(res.data.price);
                            $("#expire").val(res.data.expire_time);
                            $("#miner_id").val(res.data.id);
                            $("#stock").val(res.data.stock);
                            $("#low").val(res.data.low);
                            $("#high").val(res.data.high);
                            $("#agent_cp").val(res.data.agent_cp);
                            $("#float_per_before").val(res.data.float_per_before);
                            $("#float_per_after").val(res.data.float_per_after);
                            $("#change_time").val(res.data.change_time);
                        }
                    });
                },
                submit(){
                    var _this = this;
                    var url = '{{url('miner.pop')}}';
                    var data=$("#form").serializeArray();
                    apiPost(url,data,function(res){
                        if(!res.errcode){
                            console.log(res);
                            layer.msg(res.message);
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }else{
                            console.log(res);
                            layer.msg(res.message);
                        }
                    });
                },
                clearSearch(){
                    $("#search_form").find("select[name='status']").val('');
                },
                toSearch(){
                    this.getList(this.url);
                }

            },


        })

    </script>
@stop
