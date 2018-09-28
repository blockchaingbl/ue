@extends('manage.layouts.dashboard')

@section('container')

    <div id="userList">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">用户列表（共 @{{ total }} 条记录）</h3>
            </div>
            <div class="panel-body">
                <form id="search_form" class="form-horizontal" role="form">
                    <div class="form-group">
                        <div class="col-md-1"  style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <select class="form-control" name="coin_type" v-on:change="toSearch()" style="width:125px;">
                                    <option value="0">平台币({{db_config("COIN_UNIT")}})</option>
                                    <option v-for="(coin, index) in coin_list" v-bind:value="coin.id">@{{ coin.name }}(@{{ coin.coin_unit }})</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2"  style="display:block; float: left; padding-left:5px;">
                            <div class="input-group">
                                <span class="input-group-addon">用户</span>
                                <input type="text" class="form-control" name="username" placeholder="输入用户名/手机号">
                            </div>
                        </div>
                        <div class="col-md-2" style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <span class="input-group-addon">推荐人</span>
                                <input type="text" class="form-control" name="ref_name" placeholder="输入推荐人用户名/手机号" />
                            </div>
                        </div>
                        <div class="col-md-2" style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <span class="input-group-addon">时间</span>
                                <input type="text" class="form-control" id="create_time" name="create_time" placeholder="选择注册时间" />
                            </div>
                        </div>
                        <div class="col-md-2" style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <span class="input-group-addon">会员等级</span>
                                <select class="form-control" name="level">
                                    <option value="">所有</option>
                                    <option value="0">普通会员</option>
                                    <option value="1">A级社群</option>
                                    <option value="2">A级行业节点</option>
                                    <option value="3">AB级(超过父社群50%)</option>
                                    <option value="4">B级</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2" style="display:block; float: left; padding-left:0px;">
                            <div class="input-group">
                                <span class="input-group-addon">账号状态</span>
                                <select class="form-control" name="status">
                                    <option value="">所有</option>
                                    <option value="normal">正常</option>
                                    <option value="disabled">禁用</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="display:block; float: left;">
                            <button type="button" class="btn btn-primary" v-on:click="clearSearch()">清 除</button>
                            <button type="button" class="btn btn-primary" v-on:click="toSearch()">查 询</button>
                        </div>
                    </div>
                </form>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="text-align:center;">头像</th>
                    <th>用户名</th>
                    <th>手机号</th>
                    <th rel="sort" sort_key="vc_total">账户余额</th>
                    <th rel="sort" sort_key="vc_normal" v-show="otc_open==1&&coin_type==0">可交易</th>
                    <th rel="sort" sort_key="vc_untrade" v-show="otc_open==1&&coin_type==0">不可交易</th>
                    <th rel="sort" sort_key="vc_freeze">冻结金额</th>
                    <th rel="sort" sort_key="cp_total">算力</th>
                    <th>推荐人</th>
                    <th rel="sort" sort_key="create_time">注册时间</th>
                    <th>账号状态</th>
                    <td>会员等级</td>
                    <th style="width:280px;">操作</th>
                </tr>
                </thead>
                <tbody v-for="(item, index) in list">
                <tr>
                    <td style="width:40px;text-align:center;color:#cccccc;">
                        <img v-bind:src="item.avatar" style="width:30px;border-radius:50%;" v-if="item.avatar">
                        <div v-else>无</div>
                    </td>
                    <td>@{{ item.username }}</td>
                    <td>@{{ item.mobile }}</td>
                    <td>@{{ item.vc_total }}</td>
                    <td v-show="otc_open==1&&coin_type==0">@{{ item.vc_normal }}</td>
                    <td v-show="otc_open==1&&coin_type==0">@{{ item.vc_untrade }}</td>
                    <td>@{{ item.vc_freeze }}</td>
                    <td>@{{ item.cp_total }}</td>
                    <td>
                        <span v-if="item.ref_name">@{{ item.ref_name }}</span>
                        <span style="color:#cccccc;" v-else>无</span>
                    </td>
                    <td>@{{ item.create_time }}</td>
                    <td>
                        <span style="color:#5cb85c;" v-if="item.status">正常</span>
                        <span style="color:#d9534f;" v-else>禁用</span>
                    </td>
                    <td>
                        <span style="color:#cccccc;" v-if="item.level==0">普通会员</span>
                        <span  v-else-if="item.level==1">A级会员(社群建设)</span>
                        <span  v-else-if="item.level==2">A级会员(行业节点)</span>
                        <span  v-else-if="item.level==3">AB级会员(超过父级50%)</span>
                        <span  v-else-if="item.level==4">B级会员</span>
                        <button v-bind:userid="item.id" v-if="item.level>0" v-on:click="calcDetail($event)" class="btn btn-primary btn-xs btn-success">查看统计</button>
                    </td>
                    <td style="text-align:center;">
                        <button v-bind:userid="item.id" v-on:click="openSetMoney($event)" class="btn btn-primary btn-xs money" data-toggle="modal" data-target="#coin_deal">金币操作</button>
                        <button v-bind:userid="item.id" v-on:click="openSetCp($event)" class="btn btn-primary btn-xs money" data-toggle="modal" data-target="#cp_deal">算力操作</button>
                        <button v-bind:userid="item.id" v-on:click="moneyDetail($event)" class="btn btn-primary btn-xs">收支明细</button>
                        <br/>
                        <button v-bind:userid="item.id" v-bind:status="0" v-on:click="setAccountLock($event)" class="btn btn-danger btn-xs" v-if="item.status">冻结账户</button>
                        <button v-bind:userid="item.id" v-bind:status="1" v-on:click="setAccountLock($event)" class="btn btn-success btn-xs" v-else>启用账户</button>
                        {{--<button v-bind:userid="item.id" v-bind:otc_auth="0" v-on:click="setOtcAuth($event)" class="btn btn-danger btn-xs" v-if="item.otc_auth">解除OTC授权</button>--}}
                        <button v-bind:userid="item.id"  v-on:click="setOtcAuth(item)" class="btn btn-primary btn-xs" v-if="item.otc_auth==0">OTC授权</button>
                        <button v-bind:userid="item.id"  v-on:click="setOtcAuth(item)" class="btn btn-success btn-xs" v-if="item.otc_auth>0">OTC授权</button>
                        <button v-bind:userid="item.id" v-bind:status="0" v-on:click="freeMoney(item)" class="btn btn-danger btn-xs" v-if="item.status">解冻资金</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div style="position:relative;top:10px;" v-html="pagehtml"></div>
        </div>
        <!-- 金币操作 -->
        <div class="modal fade" id="coin_deal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">金币操作</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="change_money_type" value="1" checked="checked" v-on:click="dealTypeSelect($event)"> 冻结
                                    </label>
                                    <label>
                                        <input type="radio" name="change_money_type" value="2" v-on:click="dealTypeSelect($event)"> 充值
                                    </label>
                                    <label>
                                        <input type="radio" name="change_money_type" value="3" v-on:click="dealTypeSelect($event)"> 扣减
                                    </label>
                                </div>
                                <br>
                                <div class="input-group">
                                    <span class="input-group-addon">类型</span>
                                    <select class="form-control" id="coin_type" style="width:150px;" v-on:change="coinTypeSelect($event)">
                                        <option value="0">平台币({{db_config("COIN_UNIT")}})</option>
                                        <option v-for="(coin, index) in coin_list" v-bind:value="coin.id">@{{ coin.name }}(@{{ coin.coin_unit }})</option>
                                    </select>
                                </div>
                                <br>
                                <div v-if="otc_open">
                                    <div id="otc_open">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">可交易</span>
                                            <input type="text" class="form-control" placeholder="请输入金额" id="change_normal_money" style="width:150px;">
                                        </div>
                                        <br>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="money_text">不可交易</span>
                                            <input type="text" class="form-control" placeholder="请输入金额" id="change_untrade_money" style="width:150px;">
                                        </div>
                                    </div>
                                    <div id="otc_close" class="input-group" style="display:none;">
                                        <span class="input-group-addon" id="money_text">金额</span>
                                        <input type="text" class="form-control" placeholder="请输入金额" id="change_money" style="width:150px;">
                                    </div>
                                </div>
                                <div id="otc_close" class="input-group" v-else>
                                    <span class="input-group-addon" id="money_text">金额</span>
                                    <input type="text" class="form-control" placeholder="请输入金额" id="change_money" style="width:150px;">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" v-on:click="changeMoney()">确认提交</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
        <!-- 算力操作 -->
        <div class="modal fade" id="cp_deal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">算力操作</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="change_cp_type" value="1" checked> 扣减
                                    </label>
                                    <label>
                                        <input type="radio" name="change_cp_type" value="2"> 增加
                                    </label>
                                </div>
                                <br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="money_text">算力值</span>
                                    <input type="text" class="form-control" placeholder="请输入算力值" id="change_cp" style="width:150px;">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" v-on:click="changeCp()">确认提交</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
        <!-- 授权操作 -->
        <div class="modal fade" id="otc_auth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">otc授权</h4>
                    </div>
                    <div class="modal-body">
                        <div style="padding: 10px 10px 10px;">
                            <form class="bs-example bs-example-form" role="form">
                                <div class="input-group">
                                    <span class="input-group-addon">otc授权</span>
                                    <select class="form-control"  style="width:150px;" v-model="otc_auth">
                                        <option value="0">未授权</option>
                                        <option value="1">已授权</option>
                                    </select>
                                </div>
                                <div class="input-group" style="margin-top: 20px;" v-show="otc_auth">
                                    <span class="input-group-addon" id="auth_type">锁仓授权</span>
                                    <select class="form-control"  style="width:150px;" v-model="otc_auth_type">
                                        <option value="0">未授权</option>
                                        <option value="1">限制最低锁仓天数</option>
                                        <option value="2">指定锁仓天数</option>
                                        <option value="3">可自由指定锁仓天数</option>
                                    </select>
                                </div>
                                <div id="limit_day" class="input-group"  style="margin-top: 20px;" v-show="otc_auth_type>0 && otc_auth_type<3">
                                    <span class="input-group-addon">天数</span>
                                    <input type="text" class="form-control" placeholder="请输入天数" v-model="limit_day" style="width:150px;">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" v-on:click="submitOtcAuth()">确认提交</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
        <input type="hidden" id="user_id">
    </div>

    <script>
        var userList = new Vue({
            el:'#userList',
            data:{
                url:'{{url("get_userlist")}}',
                list:[],
                coin_list:[],
                coin_type:0,
                total:0,
                pagehtml:'',
                sort_key:'id',
                sort_type:'asc',
                otc_open:'',
                otc_info :{},
                otc_auth_type:0,
                limit_day:'',
                otc_auth:0,
                user_id : 0,
                page:1
            },
            updated(){
                this.bindBageEvent();
                this.renderSort();
                this.initDatePicker();
            },
            mounted(){
                this.loadList(this.url);
            },
            methods:{
                clearSearch(){
                    $("input").val('');
                    $("select[name='status']").val('');
                },
                toSearch(){
                    this.loadList(this.url);
                },
                loadList(url){
                    var _this = this;
                    var data = $("#search_form").serialize();
                    var query = {sort_key:this.sort_key,sort_type:this.sort_type};
                    for(var k in query)
                    {
                        data+="&"+k+"="+query[k];
                    }
                    apiPost(url,data,function(res){
                        _this.page = res.page
                        _this.list = res.list;
                        _this.total = res.total;
                        _this.pagehtml = res.pagehtml;
                        _this.coin_list = res.coin_list;
                        _this.coin_type = res.coin_type;
                        _this.otc_open = res.otc_open;
                    });
                },
                bindBageEvent(){
                    var _this = this;
                    $(".pagination").find("a").unbind("click");
                    $(".pagination").find("a").bind("click",function(){
                        _this.loadList($(this).attr("href"));
                        return false;
                    });
                },
                renderSort(){
                    var _this = this;
                    $("*[rel='sort']").each(function(i,dom){
                        var sort_key = $(dom).attr("sort_key");
                        $(dom).css({
                            position:'relative',
                            cursor:'pointer'
                        });
                        $(dom).unbind("click");
                        $(dom).bind("click",function(){
                            userList.toSort(sort_key);
                        });
                        var html = '<a href="javascript:void(0);" style="text-decoration:none;position:absolute;right:5px;">';
                        if(sort_key==_this.sort_key)
                        {
                            if(_this.sort_type=='asc')
                            {
                                html+='<i class="iconfont" style="font-size:12px;color:#666666;">&#xe62e;</i>';
                            }
                            else
                            {
                                html+='<i class="iconfont" style="font-size:12px;color:#666666;">&#xe62f;</i>';
                            }
                        }
                        else
                        {
                            html+='<i class="iconfont" style="font-size:12px;color:#666666;">&#xe656;</i>';
                        }
                        html+='</a>';
                        $(dom).find("a").remove();
                        $(dom).append(html);
                    });
                },
                toSort(sort_key){
                    if(sort_key==this.sort_key)
                    {
                        this.sort_type = this.sort_type =="asc"?"desc":"asc";
                    }
                    else
                    {
                        this.sort_type = 'asc';
                    }
                    this.sort_key = sort_key;
                    this.loadList(this.url);
                },
                setAccountLock(e){
                    var _this = this;
                    var obj = e.target;
                    var id = $(obj).attr("userid");
                    var status = $(obj).attr("status");
                    layer.confirm("确认此操作？",{title:"提示"},function(index){
                        layer.close(index);
                        $.ajax({
                            url:'{{url('user.lock')}}',
                            type:'POST',
                            dataType:'JSON',
                            data:{id:id,status:status},
                            success:function (response) {
                                if(response.errcode==0) {
                                    layer.msg('设置成功');
                                    _this.loadList(_this.url);
                                }else{
                                    layer.msg(response.message);
                                }
                            },error:function(err){
                                layer.msg('设置失败');
                            }
                        })
                    });
                },
                dealTypeSelect(e){
                    if(this.otc_open){
                        var coin_type = $('#coin_type').val();
                        if(coin_type==0){
                            $("#otc_open").show();
                            $("#otc_close").hide();
                        }else{
                            $("#otc_open").hide();
                            $("#otc_close").show();
                        }
                    }
                },
                coinTypeSelect(e){
                    if(this.otc_open){
                        var coin_type = $('#coin_type').val();
                        if(coin_type==0){
                            $("#otc_open").show();
                            $("#otc_close").hide();
                        }else{
                            $("#otc_open").hide();
                            $("#otc_close").show();
                        }
                    }
                },
                openSetMoney(e){
                    var obj = e.target;
                    var id = $(obj).attr("userid");
                    $('#user_id').val(id);
                    $('#change_normal_money').val('');
                    $('#change_untrade_money').val('');
                    $('#change_money').val('');
                },
                changeMoney(){
                    var _this = this;
                    var deal_type = $('input[name="change_money_type"]:checked').val();
                    var coin_type = $('#coin_type').val();
                    var normal_money = $('#change_normal_money').val();
                    var untrade_money = $('#change_untrade_money').val();
                    var change_money = $('#change_money').val();
                    if(coin_type==""){
                        layer.msg("请选择币的类型");
                        return;
                    }
                    if(this.otc_open){
                        if(coin_type==0){
                            if(normal_money==""&&untrade_money==""){
                                layer.msg("请至少输入一个金额");
                                return;
                            }
                        }else{
                            if(change_money==""){
                                layer.msg("请输入金额");
                                return;
                            }
                        }
                    }else{
                        if(change_money==""){
                            layer.msg("请输入金额");
                            return;
                        }
                    }
                    var data = {
                        id:$('#user_id').val(),
                        type:deal_type,
                        normal_money:normal_money,
                        untrade_money:untrade_money,
                        change_money:change_money,
                        coin_type:coin_type
                    };
                    $.ajax({
                        url:'{{url('user.money')}}',
                        type:'POST',
                        dataType:'JSON',
                        data:data,
                        success:function (response) {
                            if(response.errcode==0) {
                                $("#coin_deal").modal('hide');
                                layer.msg('设置成功');
                                _this.loadList(_this.url);
                            }else{
                                layer.msg(response.message);
                            }
                        },error:function(err){
                            layer.msg('设置失败');
                        }
                    })
                },
                openSetCp(e){
                    var obj = e.target;
                    var id = $(obj).attr("userid");
                    $('#user_id').val(id);
                    $('#change_cp').val('');
                },
                changeCp(){
                    var _this = this;
                    if($('#change_cp').val()==""){
                        layer.msg("请输入算力值");
                        return;
                    }
                    var data = {
                        id:$('#user_id').val(),
                        type:$('input[name="change_cp_type"]:checked').val(),
                        cp_amount:$('#change_cp').val()
                    };
                    $.ajax({
                        url:'{{url('user.cp')}}',
                        type:'POST',
                        dataType:'JSON',
                        data:data,
                        success:function (response) {
                            if(response.errcode==0) {
                                $("#cp_deal").modal('hide');
                                layer.msg('设置成功');
                                _this.loadList(_this.url);
                            }else{
                                layer.msg(response.message);
                            }
                        },error:function(err){
                            layer.msg('设置失败');
                        }
                    })
                },
                moneyDetail(e){
                    var obj = e.target;
                    var id = $(obj).attr("userid");
                    layer.open({
                        type: 2,
                        content: '{{url('user.money.detail')}}'+'/'+id,
                        area: ['850px', '600px'],
                        title:'收支明细'
                    });
                },
                calcDetail(e){
                    var obj = e.target;
                    var id = $(obj).attr("userid");
                    layer.open({
                        type: 2,
                        content: '{{url('user.calc.detail')}}'+'/'+id,
                        area: ['850px', '600px'],
                        title:'统计详情'
                    });
                },
                freeMoney(x){
                    layer.open({
                        type: 2,
                        content: '{{url('platformlog.freeze')}}'+'?keyword='+x.mobile,
                        area: ['850px', '600px'],
                        title:'冻结记录'
                    });
                },
                initDatePicker(){
                    $('#create_time').daterangepicker({
                        "linkedCalendars": false,
                        "autoUpdateInput": false,
                        "locale": {
                            format: 'YYYY-MM-DD',
                            separator: '~',
                            applyLabel: "应用",
                            cancelLabel: "取消",
                            resetLabel: "重置",
                        }
                    }, function(start, end, label) {
                        var beginTimeStore = start;
                        var endTimeStore = end;
                        if(!this.startDate){
                            this.element.val('');
                        }else{
                            this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                        }
                    });
                },
                setOtcAuth(x){
                    this.otc_auth = x.otc_auth;
                    this.otc_auth_type = x.otc_auth_type;
                    this.limit_day = x.limit_day;
                    this.user_id = x.id;
                    $('#otc_auth').modal('show')
                },
                submitOtcAuth(){
                    let formData = {
                        otc_auth:this.otc_auth,
                        otc_auth_type:this.otc_auth_type,
                        limit_day:this.limit_day,
                        user_id:this.user_id
                    }
                    const _this = this

                    $.ajax({
                        url:'{{url('user.otcauth')}}',
                        type:'POST',
                        dataType:'JSON',
                        data:formData,
                        success:function (response) {
                            if(response.errcode==0) {
                                layer.msg('设置成功');
                                $('#otc_auth').modal('hide')
                                _this.loadList(_this.url+'?page='+_this.page);
                            }else{
                                layer.msg(response.message);
                            }
                        },error:function(err){
                            layer.msg('设置失败');
                        }
                    })
                }
            }
        });
    </script>
@stop
