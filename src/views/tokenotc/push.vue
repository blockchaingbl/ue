<template lang="html">
    <div class="deals-push-box">

        <div class="push-content">
            <div class="head-text flex-1">可出让{{show_coin.coin_type.coin_unit}}：<span>{{show_coin.vc_token}}</span>
            <a @click="turn_recharge()">去令牌转入</a>
            </div>
            <div class="push-item vux-1px-b">
                <popup-radio title="资产类型" :options="coin_info" v-model="chose_coin" @on-hide="hide_change">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">资产类型</p>
                </popup-radio>
            </div>
            <div class="push-item title">出让数量</div>
            <div class="push-item vux-1px-b">
                <x-input type="number" class="price" placeholder="请输入出让数量" v-model="sale_amount" :required="true"></x-input>
            </div>
            <div class="push-item title">最低受让数量</div>
            <div class="push-item vux-1px-b">
                <x-input type="number" class="price" placeholder="请输入最低受让数量" v-model="min_buy_amount" :required="true"></x-input>
            </div>
            <div class="push-item title">单价</div>
            <div class="push-item input-box">
                <x-input :min="0" :max="999999"  class="price" v-model="coin_price" :readonly="!price_choice"  width="100px"></x-input>
            </div>
            <cell title=""  primary="content" style="background:#fff;left: -40px;height: 70px;" v-if="price_choice">
                <range v-model="coin_price" :min="min" :max="max" :decimal="true" :step="0.01"></range>
            </cell>
            <div class="push-item title">出让行情</div>
            <div class="push-item input-box vux-1px-b">
                <input type="text" class="price" v-model="sale_total" readonly="true">
            </div>
            <div class="push-item title">燃烧</div>
            <div class="push-item vux-1px-b">
                <input type="text" class="price" v-model="otc_fee" readonly="true">
            </div>
            <div class="push-item title">出让保证金 (违约将被燃烧)</div>
            <div class="push-item input-box">
                <input type="text" class="price" v-model="otc_freeze_seller" readonly="true">
            </div>
        </div>
        <box gap="32px 35px 0">
            <x-button type="primary" style="border-radius:99px;" class='push-btn' v-on:click.native="push_sell()">出让</x-button>
        </box>
    </div>
</template>
<script>
    import {XNumber,XSwitch,PopupRadio,Range} from "vux";
    export default {
        components: {
            XNumber,
            XSwitch,
            PopupRadio,
            Range
        },
        data () {
            return {
                coin_info:[],
                show_coin : {coin_type:{coin_unit:this.$store.state.init.coin_uint}},
                otc_freeze_seller: this.$store.state.init.otc_freeze_seller + this.$store.state.init.coin_uint ,
                chose_coin:0,
                vc_token:0,
                coin_info_detail:{},
                price_choice:0,
                asset:'',
                sale_amount:0,
                coin_price:0,
                loading:true,
                otc_rate:0,
                  min:0,
                  max:1,
              min_buy_amount:0,
            }
        },
        created () {
            this.getAccount()
        },
        methods: {
            getAccount(){
                this.$http.post('/api/app.tokenotc/deals/get_open_coin',{sell:1}).then(res => {
                    let data = res.data.lists;
                    for (let i=0; i<data.length;i++){
                        this.coin_info.push({key:i,value:data[i].coin_type.coin_unit})
                    }
                    this.chose_coin = 0;
                    this.coin_info_detail = data;
                    this.vc_token = parseFloat(data[0].vc_token);
                   this.hide_change()
                    this.loading =false;
                }).catch(err=>{
                })
            },
            hide_change(){
                let index = this.chose_coin;
                this.show_coin = this.coin_info_detail[index];
                this.asset = "可出让："+this.show_coin.vc_token;
                this.price_choice = this.show_coin.coin_type.otc_price_choice;
                this.otc_rate = this.show_coin.otc_rate
                this.sale_amount = this.show_coin.coin_type.min_otc_sale;
                this.coin_price = this.show_coin.coin_type.price
                this.min = this.coin_price * (1-this.show_coin.coin_type.otc_price_rate);
                this.min =parseFloat(this.min.toFixed(2));
                this.max = this.coin_price * (1+this.show_coin.coin_type.otc_price_rate);
                this.max =parseFloat(this.max.toFixed(2))
            },
            push_sell()
            {
                if(this.lock)
                {
                    return;
                }else{
                    this.lock = true;
                    var that = this;
                    let formData = {coin_type:this.show_coin.coin_type.id,amount:this.sale_amount,price:this.coin_price,min_buy_amount:this.min_buy_amount}
                    this.$http.post('/api/app.tokenotc/deals/push',formData).then(res => {
                        this.$vux.toast.text(res.message);
                        if(res.errcode==0)
                        {
                          this.$router.push({path:'/token_otc'})
                        }
                    }).catch(err=>{
                          if(err.errcode=='90001')
                          {
                            this.$vux.confirm.show({
                              title: err.message,
                              onConfirm () {
                                formData.confirm = 1;
                                that.$http.post('/api/app.tokenotc/deals/push',formData).then(res => {
                                  that.$vux.toast.text(res.message);
                                  if(res.errcode==0)
                                  {
                                    that.$router.push({path:'/token_otc'})
                                  }
                                }).catch(err=>{
                                  that.lock = false;
                                  that.$vux.toast.text(err.message);
                                })
                              },
                              onCancel(){
                                that.lock =false;
                              }
                            })
                          }else{
                            this.lock = false;
                            this.$vux.toast.text(err.message);
                          }
                    })
                }

            },
            turn_recharge()
            {
                this.$router.push({path:'/token_incharge/'+this.chose_coin});
            }
        },
        computed:{
            otc_fee()
            {
                let fee = parseFloat(this.show_coin.coin_type.otc_rate) * parseFloat(this.sale_amount);
                if(fee>0)
                {
                  return fee.toFixed(5)+this.show_coin.coin_type.coin_unit;
                }else{
                  return `0${this.show_coin.coin_type.coin_unit}`
                }

            },
            sale_total()
            {
                let total =  parseFloat(this.coin_price) * parseFloat(this.sale_amount)
                if(total>0)
                {
                  return total.toFixed(5)
                }
                return 0;
            }
        },
    }
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .deals-push-box{
        font-family: Arial, "Microsoft Yahei";
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
                font-size: @fs-small;
                span{
                    font-size: 1.0625rem;
                    font-weight: bold;
                    color: #6b94f8;
                }
            }
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
        .push-btn{
            font-size: @fs-normal;
            height: 2.4375rem;
        }
        .weui-cell .vux-number-selector{
            height: 25px;
        }
        .weui-cell .vux-number-round .vux-number-selector{
            width: 25px;
            background-color: #2f82ff;
            svg{
                fill: #ffffff;
            }
        }
        .weui-cell .vux-number-round .vux-number-selector-sub{
            border: 1px solid #2f82ff;
        }
        .weui-cell .vux-number-round .vux-number-selector-plus{
            border: 1px solid #2f82ff;
        }
        min-height: 100%;
        padding-bottom: 2.375rem;
        .candy-basic-opera{
            background: #fff;
            padding-bottom: 0.625rem;
        }
        .candy-senior-opera{
            margin-top: 0.625rem;
            background: #fff;
            padding-bottom: 0.25rem;
        }
        .coin-slot {
            text-align: center;
            padding: 8px 0;
            color: #888;
        }
        .text_red{
            color: red;
        }
        .cell-assets{
            padding-bottom: 0;
        }
        .grant-bottom{
            margin-top: 0.625rem;
            min-height: 6rem;
            background: #fff;
        }
        .grant-btn-box{
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .lock-time {
            padding-bottom: 0.25rem;
        }
        .lock-select{
            padding-left: 15px;
            padding-top: 10px;
        }
    }
</style>
