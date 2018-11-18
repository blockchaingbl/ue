<template lang="html">
<div class="buy-box">
    <div class="head">
        您将<span v-if="identity=='buyer'">受让</span><span v-else-if="identity=='seller'">出让</span><span v-else>受让</span>的{{$store.state.init.coin_uint}}
    </div>
    <div class="buy-block">
        <div class="item flex-box vux-1px-b" v-show="pay_status">
            <div class="title flex-1">
                <span>订单号</span>
            </div>
            <div class="decs" v-clipboard:copy="order_sn" v-clipboard:success="onCopy" v-clipboard:error="onError">{{order_sn}} <i class="iconfont">&#xe64b;</i></div>
        </div>
        <div class="item flex-box vux-1px-b">
            <div class="title flex-1">
                <span v-if="identity=='buyer'">出让方</span>
                <span v-else-if="identity=='seller'">受让方</span>
                <span v-else>出让方</span>
            </div>
            <div class="decs">{{sellerName}}</div>
        </div>
        <div class="item vux-1px-b flex-box">
            <div class="title flex-1">应支付</div>
            <div class="decs">
                <div class="price-box flex-box">
                    <div class="price">&yen; <span>{{(amount*vc_unit_price).toFixed(2) || 0}}</span></div>
                    <div class="univalent">单价 &yen; {{vc_unit_price}}</div>
                </div>
            </div>
        </div>
        <div class="item vux-1px-b flex-box">
            <div class="red_weight flex-1">数量 <span v-if="!readOnlyFlag">（可改）</span></div>
            <x-input  v-model="amount" :readonly="readOnlyFlag" required placeholder="请输入受让数量" placeholder-align="right" text-align="right" :required="true" :is-type="checkBuyNum"></x-input>
        </div>
        <div class="item flex-box vux-1px-b" v-show="payment_info.length>0&&!readOnlyFlag">
            <selector @on-change="showPayInfo"  style="width: 100%;"  title="支付方式(可改)" :options="payment_info" v-model="chose_payment" direction="rtl"></selector>
        </div>
        <div class="item flex-box vux-1px-b" v-show="payment_info.length>0&&!readOnlyFlag&&chose_payment_info.is_connect">
            <div class="title flex-1">联系方式</div>
            <div class="decs"><a style="color: #4c4c51" :href="call_number">{{chose_payment_info.connect}}</a></div>
        </div>
        <div class="item flex-box vux-1px-b" v-show="payment_info.length==0 || readOnlyFlag">
            <div class="red_weight flex-1">支付方式(可改)</div>
            <div class="decs">{{chose_payment_info.payment_org}}</div>
        </div>
        <div class="item flex-box vux-1px-b" v-show="(payment_info.length==0 || readOnlyFlag)&&chose_payment_info.is_connect">
            <div class="title flex-1">联系方式</div>
            <div class="decs"><a  style="color: #4c4c51" :href="call_number">{{chose_payment_info.connect}}</a></div>
        </div>
        <div class="item flex-box vux-1px-b">
            <div class="title flex-1">受让保证金（违约将被燃烧）</div>
            <div class="decs">{{$store.state.init.otc_freeze_buyer}} {{$store.state.init.coin_uint}}</div>
        </div>
        <div class="item flex-box vux-1px-b" v-if="lock_day>0">
            <div class="title flex-1">锁仓时间</div>
            <div class="decs">{{lock_day}}天</div>
        </div>
        <div class="item flex-box vux-1px-b" v-if="lock_day>0">
            <div class="title flex-1">每日释放</div>
            <div class="decs">{{amount/lock_day.toFixed(5)}}</div>
        </div>

        <div class="item flex-box vux-1px-b" v-show="pay_status==1&&chose_payment_info.payment_key == 'bankcard'">
            <div class="title flex-1">卡号</div>
            <div class="decs">{{chose_payment_info.payment_account}}</div>
        </div>
        <div class="item flex-box vux-1px-b" v-show="pay_status==1&&chose_payment_info.payment_key == 'bankcard'">
            <div class="title flex-1">支行</div>
            <div class="decs">{{chose_payment_info.branch_bank}}</div>
        </div>
        <div class="item flex-box vux-1px-b" v-show="pay_status==1&&chose_payment_info.payment_key == 'bankcard'">
            <div class="title flex-1">转入人</div>
            <div class="decs">{{chose_payment_info.payment_receipt}}</div>
        </div>
        <div class="item flex-box vux-1px-b" v-if="accid" @click="connect()">
            <div class="title flex-1">在线联系</div>
            <div class="decs">{{sellerName}}</div>
        </div>
        <div class="item item-tis flex-box vux-1px-b">
            <div class="title">注：</div>
            <div class="decs flex-1">注:此受让订单将冻结一定数额资产，交易成功将全额退回，如未支付点已确定支付，或已支付{{this.$store.state.init.otc_order_overtime}}分钟内未点击我确定已支付，致使交易订单无法生成，冻结保证金将被燃烧掉，并且信用还将受到影响。如已确认支付，商家在5小时内未发放资产，请在意见反馈里留言并上传支付凭证，客服会核实发放资产。</div>
        </div>

    </div>
    <div style="text-align: center;padding: 20px;" v-show="pay_status==1">
        <div v-show="chose_payment_info.payment_key=='weixin' || chose_payment_info.payment_key=='alipay'">
            <img :src="chose_payment_info.qrcode"  height='200' width="200">
        </div>
    </div>
    <!--买家进入-->
    <div v-show="buy_flag==1 && !time_end">
        <box gap="8px 35px 25px" v-show="pay_status==0">
            <x-button type="primary" style="border-radius:99px;" class='buy-btn' :class="{ 'noTime': add }" v-on:click.native="buy()">确认下单</x-button>
        </box>
        <div class="buy-bottom" v-show="pay_status==1">
            <div class="tis" v-show="confirm_pay==0">请在倒计时时间内完成支付并点击下方我确认已支付</div>
            <div class="count-down" v-show="confirm_pay==0">
                倒计时 <span>{{minute}}:{{second}}</span>
            </div>
            <box gap="8px 35px 25px">
                <x-button v-show="confirm_pay==0"  type="primary" style="border-radius:99px;" class='buy-btn' :class="{ 'noTime': add }" v-on:click.native="isopen()">{{buttonName}}</x-button>
            </box>
        </div>
    </div>
    <!--卖家进入-->
    <div v-show="buy_flag==0 && !time_end">
        <div class="buy-bottom">
            <div class="tis">转入不发{{$store.state.init.coin_uint}}将视为严重影响信用值,请在300分钟内完成发{{$store.state.init.coin_uint}}</div>
            <div class="count-down">
                倒计时 <span>{{minute}}:{{second}}</span>
            </div>
            <box gap="8px 35px 25px">
                <x-button type="primary" style="border-radius:99px;" class='buy-btn' :class="{ 'noTime': add }" @click.native="deliver()">我已收到款，确认发GBL</x-button>
            </box>
        </div>
    </div>
    <div class="pupBox" :class="{ 'isopen': is_open }">
        <div class="pupBox-content">
            <div class="pup-block">
                <p>您受让的{{$store.state.init.coin_uint}}将在{{this.$store.state.init.otc_order_overtime}}分钟内到账，请注意查收。</p>
                <p>未付款的请重新付款，否则将影响信用</p>
            </div>
            <div class="pup-btn-box flex-box vux-1px-t">
                <router-link to="/deals/record" class="pup-btn flex-1 vux-1px-r">没收到,我要申诉</router-link>
                <div class="pup-btn flex-1 confirm-btn" v-on:click="colse_pup()">知道了</div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
    import { XInput , PopupPicker , Group , Selector  } from 'vux'
    export default {
    components: {
        XInput,
        PopupPicker,
        Group,
        Selector
    },
    data () {
        return {
            timer:null,
            countimer:"",
            ifshow:false,
            wait:null,
            minute:null,
            second:null,
            add:false,
            is_open:false,
            sellerName :'',
            vc_unit_price:0,
            vc_less_amount:0,
            total_price:0,
            amount:0,
            payment_info:[],
            chose_payment:'',
            payment:{},
            chose_payment_info : {payment_key:''},
            pay_status:0,
            order_id : 0,
            confirm_pay:0,
            buttonName:'未支付点击视为违规行为，我确认已支付',
            readOnlyFlag : false,
            buy_flag : 1,
            time_end : false,
            identity:'',
            order_sn:'',
            lock_day:0,
            click_lock :false,
            accid:''
        }
    },
    mounted () {
       //订单列表过来
        if(!this.$route.params.id){
            if(!this.$route.params.otc_order_id){
                this.$router.push({path:'/deals/center'});
            }else{
                this.order_id = this.$route.params.otc_order_id;
                const type = this.$route.params.type;
                if(type==2){
                    this.buy_flag=0;
                }
                this.pay_status = 1;
                this.readOnlyFlag = true;
                this.$http.post('/api/app.otc/order',{order_id:this.order_id,type:type})
                    .then(res=>{
                        let otc_order = res.data.order_list[0];
                        this.sellerName = otc_order.buyersellername;
                        this.amount =otc_order.vc_amount;
                        this.vc_unit_price = otc_order.vc_uint_price;
                        this.identity = otc_order.identity;
                        this.order_sn = otc_order.order_sn;
                        this.lock_day = otc_order.lock_day
                        this.accid =otc_order.accid
                        if(type==1){
                            if(otc_order.status>0){
                                this.confirm_pay = 1;
                                this.buttonName = '已付款';
                            }else{
                                let end_time =  otc_order.create_time_stamp+this.$store.state.init.otc_order_overtime*60;
                                let  timestamp = Date.parse(new Date())/1000;
                                let diff_time = end_time - timestamp;
                                if(diff_time<0){
                                    this.$vux.toast.text('已超出付款时间');
                                    this.time_end = true;
//                                    this.$router.push({path:'/deals/center'});
                                }else{
                                    this.counttime(diff_time);
                                }
                            }
                        }else if(type==2){
                            let end_time = otc_order.pay_time_stamp+18000;
                            let  timestamp = Date.parse(new Date())/1000;
                            let diff_time = end_time - timestamp;
                            if(diff_time<0){
                                this.$vux.toast.text('已超出发GBL时间，如已收款，请在意见反馈里申请客服协助');
                                this.time_end = true;
//                                this.$router.push({path:'/deals/center'});
                            }else{
                                this.counttime(diff_time);
                            }
                        }
                        this.chose_payment_info = JSON.parse(otc_order.payment_info);

                        // if(otc_order.payment){
                        //     for (let x in otc_order.payment){
                        //         this.payment_info.push({key:x,value:otc_order.payment[x].payment_org});
                        //     }
                        //     this.payment = otc_order.payment;
                        //     this.chose_payment = this.payment_info[0].key
                        // }
                    })
                    .catch(error=>{
                    })
                return false;
            }
        }
        let formData = {
            otc_id: this.$route.params.id
        }
        //首页过来
        this.$http.post('/api/app.otc/deals',formData).then(res => {
            if(res.errcode==0){
                let otc = res.data.otc_list[0];
                this.sellerName = otc.username;
                this.vc_unit_price = otc.vc_unit_price;
                this.vc_less_amount = otc.vc_less_amount
                this.amount = otc.vc_less_amount;
                if(otc.payment){
                    for (let x in otc.payment){
                        this.payment_info.push({key:x,value:otc.payment[x].payment_org});
                    }
                  this.payment_info.sort(function (a,b) {
                       if(a.key=='bankcard') {
                          return -1;
                       }else{
                          return 1;
                       }
                  })
                  console.log( this.payment_info)
                    this.payment = otc.payment;
                    this.chose_payment = this.payment_info[0].key
                }
                this.lock_day = otc.lock_day;
            }else{
                this.$vux.toast.text(res.message);
            }
        }).catch(error => {
            if (error.errcode) {
                this.$vux.toast.text(error.message);
            }
            setTimeout(()=>{
                this.$router.push({path:'/deals/center'});
            },2000)
        });
    },
    methods:{
        counttime(TIME_COUNT){
            // const TIME_COUNT = 600;
            this.countimer = TIME_COUNT;
            if (!this.wait) {
                this.countimer = TIME_COUNT;
                this.ifshow = true;
                this.wait =setInterval(() =>{
                    if (this.countimer > 0 && this.countimer <= TIME_COUNT) {
                        this.countimer--;
                        this.minute=Math.floor(this.countimer/60);
                        this.second=Math.floor(this.countimer%60);
                        if (this.minute <= 9) this.minute = '0' + this.minute;
                        if (this.second <= 9) this.second = '0' + this.second;
                    } else{
                        this.ifshow = false;
                        this.add=true;
                        clearInterval(this.wait);
                        this.wait = null;
                        this.minute="00";
                        this.second="00";
                    }
                }, 1000)
            }
        },
        isopen(){
            if(this.confirm_pay==0){
                let formData = {
                    order_id:this.order_id,
                }
                this.$http.post('/api/app.otc/deals/confirmpay',formData)
                    .then(res=>{
                        this.is_open=true;
                        this.confirm_pay = 1;
                        this.buttonName = '已付款';
                    })
                    .catch(error=>{
                        if (error.errcode) {
                            this.$vux.toast.text(error.message);
                        }
                    })
            }else{
                this.is_open=true;
            }
        },
        colse_pup(){
            this.is_open=false
        },
        showPayInfo(){
            if(typeof (this.chose_payment)=="string")
            this.chose_payment_info = this.payment[this.chose_payment];
        },
        buy(){
            if(this.click_lock)
            {
                return false;
            }
            this.click_lock = true;
            if(this.vc_less_amount<=this.$store.state.init.min_otc_sale && this.amount!=this.vc_less_amount){
                this.amount = this.vc_less_amount;
                this.$vux.toast.text('剩余数量只有'+this.vc_less_amount+'个,必须全部受让');
                return;
            }
            let formData = {
                otc_id : this.$route.params.id,
                amount : this.amount,
                payment_info:this.chose_payment_info
            }
            this.$http.post('/api/app.otc/deals/buy',formData).then( res=>{
                if(res.errcode==0){
                    this.pay_status = 1;
                    this.order_id = res.data.order_id;
                    this.readOnlyFlag = true;
                    this.order_sn = res.data.order_sn;
                    this.accid = res.data.accid;
                }
                this.click_lock = false;
               this.pay_status=1;
               this.counttime(this.$store.state.init.otc_order_overtime*60);
            }).catch(error=>{
                this.click_lock = false;
                if (error.errcode) {
                    this.$vux.toast.text(error.message);
                }
            })
        },
        deliver(){
            let formData = {
                order_id:this.order_id
            }
            const _this = this
            this.$vux.confirm.show({
                content: '是否确认发GBL',
                onConfirm () {
                    _this.$http.post('/api/app.otc/deals/confirmreceive',formData)
                        .then(res=>{
                            if(res.errcode==0){
                                _this.$vux.toast.text('已确认发GBL');
                                setTimeout(function () {
                                    _this.$router.push({name:'dealsRecord',params:{type:2}});
                                },2000)
                            }else{
                                _this.$vux.toast.text(res.message);
                            }
                        })
                        .catch(error=>{
                            if (error.errcode) {
                                _this.$vux.toast.text(error.message);
                            }
                        })
                }
            })
        },
        checkBuyNum:function(value){
            return {
                valid: value > 0,
                msg: '受让数量必须大于0'
            }
        },
        onCopy: function (e) {
            this.$vux.toast.text("复制成功");
            //console.log('You just copied: ' + e.text)
        },
        onError: function (e) {
            this.$vux.toast.text("复制失败，您可以尝试手动记录");
            //console.log('Failed to copy texts')
        },
        connect(){
            this.$router.push({path: '/chat/p2p-'+this.accid})
        }
    },
    computed:{
        call_number (){
            return 'tel:'+this.chose_payment_info.connect
        }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .buy-box {
        font-family: Arial, "Microsoft Yahei";
        .head{
            background: url(../../assets/images/withdeawlist.jpg) no-repeat top center;
            height: 3rem;
            line-height: 3rem;
            color: #fff;
            font-size: @fs-normal;
            background-size: 100% 100%;
            padding: 0 0.625rem;
        }
        .red_weight{
            color: red;
            font-weight: bold;
        }
        .buy-block {
            background: #fff;
            padding-left: 0.625rem;
            .item {
                height: 2.8125rem;
                padding-right: 0.625rem;
                font-size: @fs-middle;
                &:after{
                    border-color: #ebebeb;
                }
                .title {
                    color: #888;
                }
                .decs {
                    color: #4c4c51;
                    .price {
                        margin-right: 0.5rem;
                        color: #6b94f8;
                        span{
                            font-size: @fs-big;
                            font-weight: bold;
                        }
                    }
                    .univalent {
                        text-align: center;
                        background: #78889a;
                        color: #fff;
                        height: 1.25rem;
                        line-height: 1.25rem;
                        padding: 0 0.25rem;
                        font-size: @fs-small;
                    }
                }
                .vux-x-input.weui-cell{
                    &:before{
                        display: none;
                    }
                    color: #4c4c51;
                }
                .vux-selector.weui-cell_select-after{
                    padding-left: 0;
                    label{
                        color: #888;
                    }
                    .weui-select{
                        color: #4c4c51;
                    }
                    .weui-cell__hd{
                        .weui-label{
                            color: red;
                            font-weight: bold;
                        }
                    }
                }
            }
            .item-tis{
                height: auto;
                padding: 0.625rem 0.625rem 0.625rem 0;
                -webkit-box-align: start;
                -ms-flex-align: flex-start;
                align-items: flex-start;
                .decs{
                    font-size: 0.8125rem;
                }
            }
        }
        .buy-bottom {
            text-align: center;
            font-size: @fs-small;
            line-height: 1.5rem;
            .tis {
                color: #888;
            }
            .count-down {
                color: #4c4c51;
            }
            .buy-btn{
                font-size: 0.9375rem;
                &:after{
                    display: none;
                }
            }
            .noTime{
                background: #c4c4cf;
            }
        }
        .pupBox {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            transition: all 0.3s;
            background: rgba(0,0,0,0);
            z-index: 99;

            .pupBox-content {
                transition: all 0.3s;
                opacity: 0;
                position: absolute;
                top: 25%;
                width: 18.5rem;
                background: #fff;
                z-index: 100;
                left: 50%;
                transform: translateX(-50%;);
                border-radius: 8px;
                .title {
                    font-size: @fs-middle;
                    text-align: center;
                    line-height: 2rem;
                    color: #4c4c51;
                    margin-bottom: 1rem;
                }
                .vux-close {
                    position: absolute;
                    top: 0.375rem;
                    right: 0.375rem;
                    width: 20px;
                    height: 20px;
                    color: #4c4c51;
                }
                .vux-close:before, .vux-close:after {
                    left: 4px;
                    top: 50%;
                    width: 12px;
                }
                .pup-block{
                    padding: 2rem 1.75rem;
                    color: #4c4c51;
                    font-size: @fs-middle;
                    p{
                        line-height: 1.125rem;
                        margin-bottom: 0.625rem;
                        &:last-child{
                            margin: 0;
                        }
                    }
                }
                .pup-btn{
                    text-align: center;
                    height: 2.8125rem;
                    line-height: 2.8125rem;
                    font-size: @fs-middle;
                    color: #6b94f8;
                }
                a.pup-btn{
                    color: #888;
                }
            }
        }
        .isopen{
            background: rgba(0,0,0,0.5);
            pointer-events: auto;
            .pupBox-content {
                opacity: 1;
            }
        }
    }
</style>
