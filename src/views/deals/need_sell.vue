<template lang="html">
    <div class="buy-box">
        <div class="head">
            您将<span v-if="identity=='buyer'">兑换</span><span v-else-if="identity=='seller'">兑换</span><span v-else>兑换</span>的{{$store.state.init.coin_uint}}
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
                    <span v-if="identity=='buyer'">兑换方</span>
                    <span v-else-if="identity=='seller'">兑换方</span>
                    <span v-else>兑换方</span>
                </div>
                <div class="decs">{{sellerName}}</div>
            </div>
            <div class="item vux-1px-b flex-box">
                <div class="title flex-1">兑换数额</div>
                <div class="decs">
                    <div class="price-box flex-box" v-show="chose_payment_info.payment_key!='wallet' && chose_payment_info.payment_key!='asset'">
                        <div class="price">&yen; <span>{{(amount*vc_unit_price).toFixed(2) || 0}}</span></div>
                        <div class="univalent">单价 &yen; {{vc_unit_price}}|${{(vc_unit_price/$store.state.init.usd_rate).toFixed(2)}}</div>
                    </div>
                    <div class="price-box flex-box" v-show="chose_payment_info.payment_key=='wallet' || chose_payment_info.payment_key=='asset'">
                        <div class="price"> <span>{{(amount*vc_unit_price/$store.state.usdc_info.last/$store.state.init.usd_rate).toFixed(3) || 0}}  USDG</span></div>
                    </div>
                </div>
            </div>
            <div class="item vux-1px-b flex-box">
                <div class="red_weight flex-1">数量 <span v-if="!readOnlyFlag"></span></div>
                <x-input  v-model="amount" readonly="readonly" required placeholder="请输入兑换数量" placeholder-align="right" text-align="right" :required="true" :is-type="checkBuyNum"></x-input>
            </div>
            <div class="item flex-box vux-1px-b" v-show="payment_info.length>0&&!readOnlyFlag">
                <selector @on-change="showPayInfo"  style="width: 100%;"  title="兑换方式" :options="payment_info" v-model="chose_payment" direction="rtl"></selector>
            </div>
            <div class="item flex-box vux-1px-b" v-show="payment_info.length>0&&!readOnlyFlag&&chose_payment_info.is_connect">
                <div class="title flex-1">联系方式</div>
                <div class="decs"><a style="color: #4c4c51" :href="call_number">{{chose_payment_info.connect}}</a></div>
            </div>
            <div class="item flex-box vux-1px-b" v-show="payment_info.length==0 || readOnlyFlag">
                <div class="red_weight flex-1">兑换方式(可改)</div>
                <div class="decs">{{chose_payment_info.payment_org}}</div>
            </div>
            <div class="item flex-box vux-1px-b" v-show="(payment_info.length==0 || readOnlyFlag)&&chose_payment_info.is_connect">
                <div class="title flex-1">联系方式</div>
                <div class="decs"><a  style="color: #4c4c51" :href="call_number">{{chose_payment_info.connect}}</a></div>
            </div>
            <div class="item flex-box vux-1px-b">
                <div class="title flex-1">兑换保证金（违约将被燃烧）</div>
                <div class="decs">{{$store.state.init.otc_freeze_seller}} {{$store.state.init.coin_uint}}</div>
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
            <!--<div class="item item-tis flex-box vux-1px-b">-->
            <!--<div class="title">注：</div>-->
            <!--<div class="decs flex-1">注:此兑换订单将冻结一定数额资产，交易成功将全额退回，如未兑换成功点已确定兑换成功，或已兑换成功{{this.$store.state.init.otc_order_overtime}}分钟内未点击我确定已兑换成功，致使交易订单无法生成，冻结保证金将被燃烧掉，并且信用还将受到影响。如已确认兑换成功，商家在5小时内未发放资产，请在意见反馈里留言并上传兑换成功凭证，客服会核实发放资产。</div>-->
            <!--</div>-->
            <div class="candy-senior-opera">
                <group class="lock-time">
                    <div class="lock-select flex-box">
                        <div class="lock-se-title flex-1">是否锁仓</div>
                        <x-switch title="锁仓" v-model="lock" :disabled="true"></x-switch>
                    </div>
                    <x-input class="candy-numb" v-show="lock" v-model="lock_day"  placeholder="请输入锁定时间" keyboard="number" type="number" :max="5" :readonly="otc_auth_type==2">
                        <x-button slot="right" type="primary" mini>天</x-button>
                    </x-input>
                </group>
            </div>
            <group v-show="otc_auth_type==0">
                <cell>
                    <div v-show="otc_auth_type==0">
                        <span style="color: red">您没有锁仓权限</span>
                    </div>
                </cell>
            </group>

        </div>
        <div style="text-align: center;padding: 20px;" v-show="pay_status==1">
            <div v-show="chose_payment_info.payment_key=='weixin' || chose_payment_info.payment_key=='alipay' || chose_payment_info.payment_key=='wallet'">
                <img :src="chose_payment_info.qrcode"  height='200' width="200">
            </div>
        </div>
        <!--买家进入-->
        <div v-show="buy_flag==1 && !time_end">
            <box gap="8px 35px 25px" v-show="pay_status==0">
                <x-button type="primary" style="border-radius:99px;" class='buy-btn' :class="{ 'noTime': add }" v-on:click.native="buy()">确认兑换</x-button>
            </box>
            <div class="buy-bottom" v-show="pay_status==1">
                <div class="tis" v-show="confirm_pay==0">请在倒计时时间内完成兑换并点击下方我确认已兑换成功</div>
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
                    <p>您兑换的{{$store.state.init.coin_uint}}将在{{this.$store.state.init.otc_order_overtime}}分钟内到账，请注意查收。</p>
                    <p>未付款的请重新付款，否则将影响信用</p>
                </div>
                <div class="pup-btn-box flex-box vux-1px-t">
                    <router-link to="/deals/record" class="pup-btn flex-1 vux-1px-r">没收到,我要申诉</router-link>
                    <div class="pup-btn flex-1 confirm-btn" v-on:click="colse_pup()">知道了</div>
                </div>
            </div>
        </div>
        <popup class="pop-deposit-deploy" v-model="showDeploy" position="bottom" style="width:100%;background:#fff;"  v-transfer-dom>
            <popup-header
                    left-text=""
                    right-text=""
                    title="资金密码"
                    :show-bottom-border="false"
                    @on-click-right="showDeploy = false">
            </popup-header>
            <group>
                <div class="deposit-deploy-tis"></div>
                <x-input  placeholder="请输入交易密码" v-model="security" type="password" ref="security"></x-input>
            </group>
            <group class="nobg flex-box">
                <x-button type="primary" style="border-radius:99px;height:2.25rem;line-height:2.25rem;font-size:0.875rem;background:#3f73ed" @click.native="buy_fixed">确认</x-button>
            </group>
        </popup>
    </div>
</template>
<script>
  import { XInput , PopupPicker , Group , Selector,Popup , XSwitch,PopupHeader,TransferDomDirective as TransferDom  } from 'vux'
  export default {
    components: {
      XInput,
      PopupPicker,
      Group,
      Selector,Popup,PopupHeader,XSwitch
    },
    directives: {
      TransferDom
    },
    data () {
      return {
        security:'',
        showDeploy:false,
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
        buttonName:'未兑换成功点击视为违规行为，我确认已兑换成功',
        readOnlyFlag : false,
        buy_flag : 1,
        time_end : false,
        identity:'',
        order_sn:'',
        lock_day:0,
        click_lock :false,
        accid:'',
        otc_auth_type:0,
        lock:false,
        otc_auth_type:0,
        sale_amount:0
      }
    },
    mounted () {
      //订单列表过来
      this.getUserinfo();
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
      getUserinfo(){
        this.$http.post('/api/app.user/account/info',{}).then(res => {
          this.vc_total=res.data.account_info.vc_total;
          this.vc_normal=res.data.account_info.vc_normal;
          this.otc_auth_type = res.data.account_info.otc_auth_type;
          if(this.otc_auth_type==2)
          {
            this.lock_day = res.data.account_info.limit_day;

          }
          if(this.otc_auth_type!=0)
          {
            this.lock = true;
          }
        }).catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }

          console.log(err);
          //  this.Toast(err || '网络异常，请求失败');
        });
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
        if(this.chose_payment_info.payment_key=='asset')
        {
          this.showDeploy =true;
          return;
        }
        this.tobuy();
      },
      buy_fixed(){
        this.tobuy();
      },
      checkSaleNum:function(value){
        return {
          valid: parseFloat(value) >= parseFloat(this.$store.state.init.min_otc_sale),
          msg: '出让数量不得低于'+this.$store.state.init.min_otc_sale
        }
      },
      tobuy(){
        if(this.click_lock)
        {
          return false;
        }
        this.click_lock = true;
        if(this.vc_less_amount<=this.$store.state.init.min_otc_sale && this.amount!=this.vc_less_amount){
          this.amount = this.vc_less_amount;
          this.$vux.toast.text('剩余数量只有'+this.vc_less_amount+'个,必须全部兑换');
          return;
        }
        let formData = {
          otc_id : this.$route.params.id,
          amount : this.amount,
          payment_info:this.chose_payment_info,
          security: this.security,
          lock_day:this.lock_day,
          is_lock:this.lock
        }
        var _this= this;
        if(this.chose_payment_info.payment_key=='asset')
        {
          this.$vux.confirm.show({
            title: '使用USDG资产兑换,即时到账',
            onCancel () {},
            onConfirm () {
              _this.$vux.loading.show({
                text: ''
              })
              _this.$http.post('/api/app.otc/dealsbuy/assetsell',formData).then( res=>{
                if(res.errcode==0){
                  _this.$vux.loading.hide()
                  _this.$vux.toast.text('兑换成功');
                  _this.$router.push({path:'/deals/record'})
                }
              }).catch(error=>{
                _this.click_lock = false;
                if (error.errcode) {
                  _this.$vux.toast.text(error.message);
                }
                _this.$vux.loading.hide()
              })
            }
          });
        }else{
          this.$http.post('/api/app.otc/dealsbuy/sell',formData).then( res=>{
            if(res.errcode==0){
              this.$vux.toast.text('请等待付款');
              _this.$router.push({path:'/deals/record'})
            }
            this.click_lock = false;
          }).catch(error=>{
            this.click_lock = false;
            if (error.errcode) {
              this.$vux.toast.text(error.message);
            }
          })
        }
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
          msg: '兑换数量必须大于0'
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
      },
      day_release:function () {
        let amount = parseFloat(this.amount)
        let day = parseInt(this.lock_day)
        if(amount&&day)
        {
          return `每日释放${(amount/day).toFixed(5)}`;
        }else {
          return ''
        }
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
        .candy-senior-opera{
            margin-top: 0.625rem;
            background: #fff;
            padding-bottom: 0.25rem;
        }
        .lock-time {
            padding-bottom: 0.25rem;
        }
        .lock-select{
            padding-left: 15px;
            padding-top: 10px;
        }
        .push-content {
            background: #fff;
            padding-left: 0.9375rem;
            padding-bottom: 0.25rem;
            line-height: 2.5rem;
            color: #4c4c51;
            margin-top: 0.625rem;
            .push-item {
                padding-right: 0.625rem;
                .weui-cell{
                    font-family:Arial, "Microsoft Yahei";
                    padding-left: 0;
                    font-size: @fs-biger;
                    color: #5d5d61;
                }
            }
            .input-box {
                margin-top: 0.25rem;
                height: 2.5rem;
                &::after{
                    left: 0;
                }
            }
            input {
                border: none;
                color: #5d5d61;
                height: 100%;
                width: 100%;
            }
            input.price {
                font-size: 1.875rem;
                font-weight: bold;
            }
        }
    }
</style>
