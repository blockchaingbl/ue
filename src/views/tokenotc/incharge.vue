<template lang="html">
<div class="withdraw-box">
    <div class="head flex-box">
        <div class="head-text flex-1">{{token_name[0]}}令牌转入</div>
    </div>
    <div class="withdraw">
        <div class="select-box">
            <group>
                <popup-picker title="选择令牌" :data="token_list" v-model="token_name" show-name :columns="1"></popup-picker>
            </group>
        </div>
        <div class="title">转入数额</div>
        <div class="input-box flex-box vux-1px-b">
            <x-input type="number" v-model="amount" :show-clear="false" class="flex-1"  ref="amount" :required="true"></x-input>
            <span>{{token_name[0]}}</span>
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
        <x-button type="primary" style="border-radius:99px;" class='pup-btn' @click.native="toPay()">确认转入</x-button>
    </box>
</div>
</template>
<script>
import { PopupPicker,PopupRadio  } from 'vux'
import tx from "@/transaction";
import { setCookie, getCookie, deleteCookie } from "../../assets/js/cookieHandle";
export default {
    components: {
      PopupPicker,
      PopupRadio
    },
    data () {
        return {
            coin_type:[],
            amount:'',
            token_list:[{name:'',value:'',address:'',rate:'',order_address:''}],
            token_name:0,
            token_symbol:'',
            address:'',
            rate:'',
            incharge_fee:0,
            platform_coin_price:0,
            chose:0,
            name:'',
            token:{}
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
    },
    mounted () {
        this.getWallets();
        this.getInchargeToken();
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
            this.$http.post('/api/app.util/init/getinchargetoken',{}).then(res => {
                let token = res.data.list;
                let arr = [];
                for (var x in token){
                    if(token[x].token_symbol){
                        arr.push({
                            'name':token[x].token_symbol,
                            'value':token[x].token_symbol,
                            'address':token[x].incharge_address,
                            'token_name':token[x].token_name
                        });
                    }
                }
                this.token =  token;
                this.token_list = [arr];
                this.token_name = [arr[0].value];
                this.address = arr[0].address;
                this.platform_coin_price = this.$store.state.init.coin_price;
                $('.input-box').find('input').focus();
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
            });
        },
        toPay(){
          var _this = this;
             var chose = this.token[this.token_name];
             console.log(chose)
             var token_name = chose.token_name;
             var amount = this.amount;
             _this.address = chose.incharge_address;
            _this.$refs.amount.validate();
            if(_this.$refs.amount.valid&&_this.amount>0){
                if(chose.id==0){
                  _this.tokenRecharge(amount);
                }else{
                  _this.walletPay(token_name,amount);
                }
            }else{
                _this.$vux.toast.text("数额必须大于0");
                _this.$refs.amount.forceShowError = true;
            }
        },
        walletPay(token_name,amount){
            var _this = this;
            var to_address = _this.address;
            tx.sendTransaction(token_name,to_address,amount,function(){
              _this.$router.push("/token_incharge/0");
            });
        },
        tokenRecharge(amount){
            let formData = {amount:amount}
            const _this = this;
            _this.$http.post('/api/app.tokenpay/pay/recharge',formData).then(res => {
                if(res.errcode>0){
                    _this.$vux.toast.text(res.message);
                }else{
                    let amount = res.data.token_recharge.amount;
                    let order_code = res.data.token_recharge.order_code;
                    let url =encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+order_code+'&amount='+amount);
                    this.$router.push({path:url})
                }
            }).catch(err => {
                if (err.errcode) {
                    _this.$vux.toast.text(err.message);
                }
            });
        },
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
