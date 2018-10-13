<template lang="html">
    <div class="selfbcty">
        <div class="content">
            <div class="bctydetail">
                <div class="bcty-title">我的{{coin_type.coin_unit}}总额:{{vc_amount}}</div>
                <div class="flex-box">
                    <div class="detail-item flex-1">
                        <div class="as">可流通</div>
                        <span>{{vc_total}}</span>
                    </div>
                    <!-- <div class="flex-1 bk">
                        <div class="as">可用</div>
                        <span>{{vc_total}}</span>
                    </div> -->
                    <div class="detail-item flex-1 dj">
                        <div class="as">冻结</div>
                        <span>{{vc_freeze}}</span>
                    </div>
                </div>
            </div>
            <!--     <router-link to="/user/wallet" class="bcty-head flex-box">
                    <div class="head-text flex-1">剩余{{coin_type.coin_unit}}：<span>{{vc_total}}</span></div>
                </router-link> -->
            <div class="bcty-tab flex-box vux-1px-t">
                <popup-picker class="opnone" :columns="2" :data="list1" v-model="value1"  @on-change="changelist1"  ></popup-picker>
                <popup-picker :data="list1" :columns="2" v-model="value1" show-name></popup-picker>
            </div>
            <div class="bcty-block">
                <div class="bcty-item flex-box vux-1px-b" v-for="(item,index) in list" >
                    <div class="item-icon iconfont wakuang"></div>
                    <div class="item-text flex-1">
                        <div class="item-title flex-box">
                            <div class="title-text flex-1">{{item.detail}}</div>
                            <div class="item-numb">
                                <span> <em v-if="value1[0]=='income'">+</em><em v-if="value1[0]=='expend'">-</em>{{item.vc_amount}}</span>&nbsp;{{coin_type.coin_unit}}
                            </div>
                        </div>
                        <div class="item-time">{{item.create_time || item.freeze_time}}</div>
                    </div>
                </div>
            </div>
            <Scroller v-if="list.length>0" v-on:load="loadlist" :loading="loading" :container="'.bcty-block'" ></Scroller>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>
        <div class="selfbcty-bottom">
            <box gap="0" class="selfbcty-btn-box flex-box">
                <x-button type="primary" style="border-radius:99px;" link='/incharge' v-if="$store.state.coin_info.coin_type.id==0||$store.state.coin_info.coin_type.incharge_open">兑换</x-button>
                <x-button type="primary" style="border-radius:99px;background:#fc8c92;margin:0 0.625rem;" @click.native="toWithdraw()">上链</x-button>
            </box>
        </div>
    </div>
</template>
<script>
    import { PopupPicker } from 'vux'
    import Scroller from "@/components/scroller";
    import Nodata from "@/components/nodata";
    import { setCookie, getCookie, deleteCookie } from "../../assets/js/cookieHandle";
    export default {
        components: {
            PopupPicker,
            Scroller,
            Nodata
        },
        data () {
            return {
                list1: [
                    {
                        name: '收入',
                        value: 'income',
                        parent: 0
                    }, {
                        name: '支出',
                        value: 'expend',
                        parent: 0
                    }, {
                        name: '冻结',
                        value: 'freeze',
                        parent: 0
                    }, {
                        name: '全部',
                        value: '',
                        parent: 'income'
                    }, {
                        name: '挖矿',
                        value: 'mine',
                        parent: 'income'
                    }, {
                        name: '转入',
                        value: 'trans_order',
                        parent: 'income'
                    },
                    {
                        name: '受让',
                        value: 'purchase',
                        parent: 'income'
                    }, {
                        name: '划拨',
                        value: 'allot',
                        parent: 'income'
                    },{
                        name: '偷取',
                        value: 'steal',
                        parent: 'income'
                    },{
                        name: '兑换',
                        value: 'incharge',
                        parent: 'income'
                    },{
                        name: '全部',
                        value: '',
                        parent: 'expend'
                    },{
                        name: '上链',
                        value: 'withdraw',
                        parent: 'expend'
                    },{
                        name: '出让',
                        value: 'sale',
                        parent: 'expend'
                    },{
                        name: '兑换',
                        value: 'exchange',
                        parent: 'expend'
                    }, {
                        name: '全部',
                        value: '',
                        parent: 'freeze'
                    },{
                        name: '挂卖',
                        value: 'sale',
                        parent: 'freeze'
                    }, {
                        name: '上链',
                        value: 'withdraw',
                        parent: 'freeze'
                    }, {
                        name: '平台',
                        value: 'manual',
                        parent: 'freeze'
                    },{
                        name: '糖果',
                        value: 'sugar',
                        parent: 'income'
                    },{
                        name: '发糖果',
                        value: 'sugar',
                        parent: 'expend'
                    },{
                        name: '糖果',
                        value: 'sugar',
                        parent: 'freeze'
                    },{
                        name: '释放',
                        value: 'free',
                        parent: 0
                    },{
                        name: '全部',
                        value: '',
                        parent: 'free'
                    },
                    {
                        name: '锁仓资产',
                        value: 'lock_transfer',
                        parent: 'income'
                    },{
                        name: '锁仓转出',
                        value: 'lock_transfer',
                        parent: 'expend'
                    },{
                        name: '锁仓资产',
                        value: 'lock_transfer',
                        parent: 'freeze'
                    },{
                        name: '锁仓资产',
                        value: 'lock_transfer',
                        parent: 'free'
                    },
                    {
                        name: '糖果',
                        value: 'sugar',
                        parent: 'free'
                    },
                    {
                        name: '转出',
                        value: 'trans_order',
                        parent: 'expend'
                    },
                ],
                list2: [['全部', '上链', '售出', '发送']],
                value1: ['income',"",],
                list:[],
                loading:false,
                page:2,
                vc_total:'',
                vc_amount:'',
                vc_freeze:'',
                coin_type:{}
            }
        },
        created(){
            var setCoinInfo =getCookie("setCoinInfo");
            this.$store.dispatch('setCoinInfo',JSON.parse(setCoinInfo))
            this.moneyInfo = this.$store.state.coin_info;
            //console.log( );
            if(this.moneyInfo){
                this.coin_type = this.moneyInfo.coin_type;
            }else{
                this.coin_type = {id:0,coin_unit:this.$store.state.init.coin_uint}
            }
        },
        mounted () {
            this.getIncome();
            if(this.coin_type.id==0){
                this.getInfo();
            }else{
                this.vc_total = this.moneyInfo.vc_total;
                this.vc_freeze = this.moneyInfo.vc_freeze;
                this.vc_amount = (parseFloat(this.moneyInfo.vc_total) + parseFloat(this.moneyInfo.vc_freeze)).toFixed(5);
            }
        },
        methods:{
            onShow () {
            },
            onHide (type) {
            },
            onChange (val) {
            },
            changelist1(val){
                this.loading=false;
                this.page=2;
                this.getIncome();
            },
            getIncome(){
                this.$http.post('/api/app.user/account/'+this.value1[0],{
                    type:this.value1[1],
                    page:1,
                    coin_type:this.coin_type.id
                }).then(res => {

                    if (this.value1[0]=="income") {
                        this.list=res.data.income_log;
                        if (res.data.income_log.length<10) {
                            this.loading=false;
                            this.page=null;
                        }
                    }else if (this.value1[0]=="expend") {
                        this.list=res.data.expend_log;
                        if (res.data.expend_log.length<10) {
                            this.loading=false;
                            this.page=null;
                        }

                    }else if (this.value1[0]=="freeze") {
                        this.list=res.data.freeze_log;
                        if (res.data.freeze_log.length<10) {
                            this.loading=false;
                            this.page=null;
                        }
                    }else if (this.value1[0]=="free") {
                        this.list=res.data.free_log;
                        console.log(res.data.free_log)
                        if (res.data.free_log.length<10) {
                            console.log(1)
                            this.loading=false;
                            this.page=null;
                        }
                    }
                }).catch(err => {
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            },
            loadlist(){
                if (this.loading) {
                    //正在加载中
                }else if(this.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.user/account/'+this.value1[0],{
                        type:this.value1[1],
                        page:this.page,
                        coin_type:this.coin_type.id
                    }).then(res => {
                        if (this.value1[0]=="income") {
                            if (res.data.income_log.length<1) {
                                this.page=null;
                                this.loading=false;
                            }else{
                                this.list=this.list.concat_unk(res.data.income_log,"id");
                                this.page++;
                                this.loading=false;
                            }
                        }else if (this.value1[0]=="expend") {
                            if (res.data.expend_log.length<1) {
                                this.page=null;
                                this.loading=false;
                            }else{
                                this.list=this.list.concat_unk(res.data.expend_log,"id");
                                this.page++;
                                this.loading=false;
                            }

                        }else if (this.value1[0]=="freeze") {
                            if (res.data.freeze_log.length<1) {
                                this.page=null;
                                this.loading=false;
                            }else{
                                this.list=this.list.concat_unk(res.data.freeze_log,"id");
                                this.page++;
                                this.loading=false;
                            }
                        }else if (this.value1[0]=="free") {
                            if (res.data.free_log.length<1) {
                                this.page=null;
                                this.loading=false;
                            }else{
                                this.list=this.list.concat_unk(res.data.free_log,"id");
                                this.page++;
                                this.loading=false;
                            }
                        }
                    }).catch(err => {
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                        console.log(err);
                        //  this.Toast(err || '网络异常，请求失败');
                    });

                }else{
                    this.loading=false;
                }
            },
            getInfo(){
                this.$http.post('/api/app.user/account/info',{}).then(res => {
                    this.vc_amount=res.data.account_info.vc_amount;
                    this.vc_total=res.data.account_info.vc_normal;
                    this.vc_freeze=res.data.account_info.vc_freeze;
                }).catch(err => {
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            },
            toWithdraw(){
                if(this.$store.state.coin_info && this.$store.state.coin_info.coin_type.id!=0){
                    if(this.$store.state.coin_info.coin_type.withdraw_open==1){
                        this.$router.push({"path":"/user/withdrawlist"});
                    }else{
                        this.$vux.toast.text("上链暂未开放");
                    }
                }else {
                    if(this.$store.state.init.withdraw_open==1){
                        this.$router.push({"path":"/user/withdrawlist"});
                    }else{
                        this.$vux.toast.text("上链暂未开放");
                    }
                }
            }

        }
    }
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .vux-cell-box.opnone{
        position: absolute;
        left: 0;
        top:0;
        width: 100%;
        opacity: 0;
    }
    .selfbcty{
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        .content{
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 2.875rem;
            overflow-x: hidden;
            overflow-y: scroll;
        }
        .selfbcty-bottom{
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2.875rem;
            padding: 0 0.9375rem;
            .selfbcty-btn-box {
                height: 100%;
                justify-content: center;
                .weui-btn.weui-btn_primary {
                    width: 100%;
                    height: 2rem;
                    line-height: 2rem;
                    margin: 0;
                    font-size: 1rem;
                }
            }
        }
        .bctydetail{
            background:-webkit-linear-gradient(left,#26cdcd,#5f5ece);
            background:linear-gradient(to right,#26cdcd,#5f5ece);
            width: 100%;
            padding: 0.625rem 0.3125rem;
            color: #fff;
            font-size: @fs-small;
            // .dj span,.bk span{
            //    color:#d01e27;
            // }
            .bcty-title{
                font-size: 1rem;
                line-height: 2.25rem;
                text-align: left;
                padding: 0 0.3125rem;
            }
            span{
                font-size: 1.125rem;
                font-weight: bold;
                height: 1.5rem;
                line-height: 1.5rem;
                min-width: 2rem;
                display: block;
            }
            .detail-item{
                padding: 0 0.3125rem;
                text-align: left;
                .as{
                    color: #e4e8f1;
                }
            }
        }
        .bcty-head{
            height: 2.625rem;
            background: #fff;
            position: relative;
            padding: 0 0.625rem;
            font-size: @fs-small;
            color:#4c4c51;
            &:after{
                content: " ";
                display: inline-block;
                height: 6px;
                width: 6px;
                border-width: 2px 2px 0 0;
                border-color: #888;
                border-style: solid;
                -webkit-transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
                transform: matrix(0.71, 0.71, -0.71, 0.71, 0, 0);
                position: relative;
                position: absolute;
                top: 50%;
                margin-top: -4px;
                right: 0.625rem;
            }
            .iconfont{
                color: #6b94f8;
                font-size: @fs-small;
                margin-top: 2px;
                margin-right: 0.25rem;
            }
            span{
                font-size: @fs-biger;
                color: #6b94f8;
                font-weight: bold;
                font-family: arial;
            }
        }
        .bcty-tab{
            height: 2rem;
            font-size: @fs-middle;
            line-height: 2rem;
            padding: 0 0.625rem;
            text-align: center;
            background: #b5b7be;
            color: #fff;
            .vux-cell-box{
                -webkit-box-flex: 1;
                -ms-flex: 1;
                flex: 1;
                &:before{
                    display: none;
                }
                .weui-cell{
                    padding: 0;
                }
                .weui-cell__ft{
                    display: none;
                }
                .vux-popup-picker-select{
                    text-align: center!important;
                    .vux-cell-value{
                        color: #fff;
                        position: relative;
                        padding-right: 2rem;
                        &:after{
                            display: block;
                            content: '';
                            position: absolute;
                            border-left: 4px solid transparent;
                            border-right: 4px solid transparent;
                            border-top: 4px solid #fff;
                            top: 50%;
                            transform: translateY(-50%);
                            right: 0;
                            transition: all 0.5s;
                        }
                    }
                }
            }
        }
        .bcty-block{
            background: #fff;
            padding-left: 0.625rem;
            .bcty-item{
                padding: 0.625rem;
                padding-left: 0;
                &:after{
                    border-color: #ebebeb;
                }
                .item-title {
                    line-height: 1.5rem;
                }
                .title-text {
                    font-size: @fs-middle;
                    color: #4c4c51;
                }
                .item-numb {
                    font-size: @fs-smaller;
                    font-family: arial;
                    color: #6b94f8;
                    span {
                        font-size: 1.0625rem;
                    }
                }
                .item-time {
                    font-size: 0.6875rem;
                    line-height: 0.875rem;
                    color: #888;
                }
            }
            .item-icon{
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 4px;
                margin-right: 0.625rem;
            }
            .wakuang{
                background: #facb93;
                position: relative;
                &:before{
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%,-50%);
                    display: block;
                    content: '\e60f';
                    font-size: 1.375rem;
                    color: #fff;
                }
            }
        }
        .more-data{
            line-height: 3.125rem;
            text-align: center;
            color: #6b94f8;
        }

    }
</style>
