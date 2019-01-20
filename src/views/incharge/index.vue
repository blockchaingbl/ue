<template lang="html">
    <div class="withdraw-box">
        <div class="head flex-box">
            <div class="head-text flex-1">{{coin_type.coin_unit}}兑换</div>
        </div>
        <div class="withdraw">
            <div class="title">兑换数额</div>
            <div class="input-box flex-box vux-1px-b">
                <x-input type="number" v-model="amount" :show-clear="false" class="flex-1" @on-change="sumFee()" ref="amount" :required="true"></x-input>
                <span>{{coin_type.coin_unit}}</span>
            </div>
            <div class="select-box" v-if="coin_type.id==0 || coin_type.token_exchange_open==1">
                <group>
                    <popup-picker title="选择令牌" :data="token_list" v-model="token_name" show-name :columns="1" @on-change="sumFee()"></popup-picker>
                </group>
            </div>
            <div class="item-block" v-show="coin_type.id==0 || coin_type.token_exchange_open==1">
                <div class="item flex-box">
                    <div class="item-title flex-1">需要支付：{{incharge_fee}} {{token_symbol}}</div>
                </div>
            </div>
        </div>
        <div class="pupBox">
            <div class="pupBox-content">
                <div class="title">提示</div>
                <div class="pup-block">
                    <div class="item">您还未创建钱包，请先去创建</div>
                </div>
                <box gap="2.1875rem 1.75rem 1.375rem">
                    <x-button type="primary" style="border-radius:99px;" class='pup-btn' link="/wallet/create">立即创建</x-button>
                </box>
            </div>
        </div>
        <box gap="40px 35px">
            <x-button type="primary" style="border-radius:99px;" class='pup-btn' @click.native="toPay()">立即支付</x-button>
        </box>
    </div>
</template>
<script>
  import { PopupPicker } from 'vux'
  import tx from "@/transaction";
  import { setCookie, getCookie, deleteCookie } from "../../assets/js/cookieHandle";
  export default {
    components: {
      PopupPicker
    },
    data () {
      return {
        coin_type:[],
        amount:'',
        token_list:[{name:'',value:'',address:'',rate:'',order_address:''}],
        token_name:[],
        token_symbol:'',
        address:'',
        rate:'',
        incharge_fee:0,
        platform_coin_price:0
      }
    },
    computed:{

    },
    created(){
      var setCoinInfo = getCookie("setCoinInfo");
      if(setCoinInfo){
        this.$store.dispatch('setCoinInfo',JSON.parse(setCoinInfo));
      }
      this.moneyInfo = this.$store.state.coin_info;
      if(this.moneyInfo.coin_type){
        this.coin_type = this.moneyInfo.coin_type;
      }else{
        this.coin_type = {id:0,coin_unit:this.$store.state.init.coin_uint}
      }
      console.log(this.coin_type);
    },
    mounted () {
      this.getWallets();
      this.getInchargeToken(this.coin_type.id);
      this.$refs.amount.focus();
    },
    methods:{
      open_pup(){
        $('.pupBox').addClass('isopen');
      },
      getWallets(){
        this.$http.post('/api/app.wallet/wallets',{}).then(res => {
          if(res.data.wallets==""){
            this.open_pup();
          }
        }).catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
        });
      },
      getInchargeToken(id){
        this.$http.post('/api/app.util/init/incharge_token',{id:id}).then(res => {
          let token = res.data.list;
          let arr = [];
          for (var x in token){
            if(token[x].token_symbol){
              arr.push({
                'name':token[x].token_symbol,
                'value':token[x].token_name,
                'address':token[x].incharge_address,
                'rate':token[x].incharge_rate
              });
            }
          }
          this.token_list = [arr];
          this.token_name = [arr[0].value];
          this.address = arr[0].address;
          this.rate = arr[0].rate;
          this.platform_coin_price = this.$store.state.init.coin_price;
          $('.input-box').find('input').focus();
        }).catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
        });
      },
      sumFee(){
        var _this = this;
        if(_this.coin_type.id==0 || _this.coin_type.token_exchange_open==1){
          //只有平台币要根据不用币去计算
          if(_this.amount>0){
            var list = _this.token_list[0];
            for(var i in list){
              if(list[i].value==_this.token_name[0]){
                _this.token_symbol = list[i].name;
                _this.address = list[i].address;
                _this.order_address = list[i].order_address
                _this.rate = list[i].rate;
              }
            }

            if(_this.coin_type.token_exchange_open==1)
            {
              if(_this.coin_type.coin_unit == _this.token_symbol)
              {
                _this.incharge_fee = parseFloat(_this.amount);
              }else{
                let int = 100000000000000;
                _this.incharge_fee = int * parseFloat(_this.amount)*parseFloat(this.coin_type.real_price)/parseFloat(_this.rate)/int || 0;
              }
            }else{
              _this.incharge_fee = parseFloat(_this.amount)/(_this.rate/_this.platform_coin_price) || 0;
            }
            _this.incharge_fee = _this.incharge_fee.toFixed(5);

            if(_this.token_symbol==this.$store.state.init.coin_uint && this.coin_type.coin_unit==this.$store.state.init.coin_uint){
              _this.incharge_fee = (_this.incharge_fee/2).toFixed(5)
            }
          }
        }
      },
      toPay(){
        var _this = this;
        _this.$refs.amount.validate();
        if(_this.$refs.amount.valid&&_this.amount>0){
          if(_this.coin_type.id==0){
            //兑换平台币
            if(_this.incharge_fee>0){
              if(_this.token_symbol==_this.$store.state.init.coin_uint){
                //钱包兑换
                var token_name = _this.token_name;
                var amount = _this.incharge_fee;
                _this.walletPay(token_name,amount);
              }else{
                //资产兑换
                _this.assetPay();
              }
            }else{
              _this.$vux.toast.text("您输入的数额太少，无法兑换");
              _this.$refs.amount.forceShowError = true;
            }
          }else{
            //兑换其他币
            var token_name = _this.token_name;
            var amount = _this.amount;
            if(_this.coin_type.coin_unit == _this.token_name)
            {
              _this.walletPay(token_name,amount);
            }else{
             _this.tokenPay(token_name,amount);
            }

          }
        }else{
          _this.$vux.toast.text("数额必须大于0");
          _this.$refs.amount.forceShowError = true;
        }
      },
      tokenPay(token_name,amount){
        let formData = {id:this.coin_type.id,amount:amount}
        const _this = this;
        _this.$http.post('/api/app.tokenpay/pay/exchange',formData).then(res => {
          if(res.errcode>0){
            _this.$vux.toast.text(res.message);
          }else{
            let amount = res.data.token_exchange.amount;
            let order_code = res.data.token_exchange.order_code;
            let url =encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+order_code+'&amount='+amount)
            this.$router.push({path:url})
          }
        }).catch(err => {
          if (err.errcode) {
            _this.$vux.toast.text(err.message);
          }
        });
      },
      walletPay(token_name,amount){
        var _this = this;
        var to_address = _this.address;
        tx.sendTransaction(token_name,to_address,amount,function(){
          _this.$router.push("/incharge");
        });
        // tx.runContract("0x6821b2c96bff0514b609835b71ae3d916eabb360","0","token20","transfer",["0x7CA4541F9c9fe5ca3835E4A2683Ef0a2EFF1437B",amount],function(tx_hash){
        //     _this.$router.push("/incharge");
        //     console.log(tx_hash);
        // });
      },
      assetPay(){
        var _this = this;
        var token_symbol = _this.token_symbol;
        var token_amount = _this.incharge_fee;
        var incharge_amount = _this.amount;
        this.$vux.confirm.show({
          title: '确认要兑换吗？',
          onCancel () {},
          onConfirm () {
            _this.$http.post('/api/app.user/incharge',{incharge_amount:incharge_amount,token_symbol:token_symbol,token_amount:token_amount}).then(res => {
              if(res.errcode>0){
                _this.$vux.toast.text(res.message);
              }else{
                _this.$vux.toast.text("兑换成功");
                _this.$router.push("/user/selfmoney");
              }
            }).catch(err => {
              if (err.errcode) {
                _this.$vux.toast.text(err.message);
              }
            });
          }
        });
      }
    }
  }
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '~vux/src/styles/close.less';
    @import '../../assets/css/variable.less';
    .withdraw-box{
        .withdraw {
            background: #fff;
            padding: 0.625rem;
            .title {
                font-size: 0.875rem;
                color: #4c4c51;
                line-height: 2rem;
            }
            .input-box {
                align-items: flex-end;
                padding: 0.5rem 0;
                &::after{
                    display: none;
                }
                .weui-cell{
                    padding: 0;
                }
                input {
                    border: none;
                    height: 2rem;
                    font-size: 1.875rem;
                    font-family: arial;
                    color: #4c4c51;
                    font-weight: bold;
                    width: 100%;
                }
                span {
                    font-size: 0.875rem;
                    margin-left: 0.625rem;
                }
            }
            .select-box {
                align-items: flex-end;
                .weui-cells{
                    font-size: 0.875rem;
                    color: #4c4c51;
                    line-height: 2rem;
                    .weui-cell{
                        padding: 0.375rem 0;
                    }
                }
                .vux-no-group-title {
                    margin-top: 0;
                }
            }
            .item-block{
                padding-top: 0.375rem;
                .item{
                    line-height: 1.875rem;
                    font-size: 0.75rem;
                    font-family: Arial, "Microsoft Yahei";
                    color: #888;
                    .item-decs{
                        color: #c9564c;
                    }
                    .item-title{
                        span{
                            font-size: 1.0625rem;
                            color: #4c4c51;
                        }
                    }
                }
            }
        }
        .withdraw-btn{
            background-color: #fc8c92;
            font-size: 0.9375rem;
            height: 2.4375rem;
            line-height: 2.4375rem;
        }
        .noLogin{
            .withdraw-btn{
                background-color: #c4c4cf;
            }
        }
        .tis{
            text-align: center;
            color: #888;
            a{
                color:#6b94f8;
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
                top: 16%;
                background: #fff;
                z-index: 100;
                left: 2.5rem;
                right: 2.5rem;
                border-radius: 8px;
                .title {
                    font-size: 0.875rem;
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
                    padding: 0 0.9375rem;
                    .item{
                        line-height: 1.875rem;
                        font-size: 0.75rem;
                        color: #888;
                        font-family: Arial, "Microsoft Yahei";
                        span{
                            color: #38485f;
                        }
                    }
                }
                .pup-btn{
                    background-color: #628cf8;
                    height: 2.4375rem;
                    line-height: 2.4375rem;
                    font-size: 0.9375rem;
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
        .head{
            height: 3rem;
            background: #fff;
            padding: 0 0.625rem;
            color: #4c4c51;
            .iconfont{
                font-size: @fs-small;
                color: #6b94f8;
                margin-right: 0.25rem;
                margin-top: 2px;
            }
            .head-text{
                font-size: 1rem;
                span{
                    font-size: 1.0625rem;
                    font-weight: bold;
                    color: #6b94f8;
                }
            }
        }
    }
</style>
