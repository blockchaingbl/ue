<template lang="html">
<div class="deals-push-box">
    <div class="push-content">
        <div class="push-item vux-1px-b">
            <popup-radio title="资产类型" :options="coin_info" v-model="chose_coin" @on-hide="hide_change">
                <p slot="popup-header" class="vux-1px-b grant-coin-slot">资产类型</p>
            </popup-radio>
        </div>
        <div class="push-item title">需求数量</div>
        <div class="push-item vux-1px-b">
            <x-input type="number" placeholder="请输入需求数量" v-model="sale_amount" :required="true"></x-input>
        </div>
        <div class="push-item title">最低单次需求数量</div>
        <div class="push-item vux-1px-b">
            <x-input type="number" class="price" placeholder="请输入最低需求数量" v-model="min_buy_amount" :required="true"></x-input>
        </div>
        <div class="push-item title" v-show="$store.state.init.otc_sale_price">单价</div>
        <div class="push-item input-box" id="sale_price" v-show="$store.state.init.otc_sale_price">
            <x-number v-model="coin_price" :fillable="true" button-style="round" align="left" width="100px"></x-number>
        </div>
        <div class="push-item title">单价</div>
        <div class="push-item input-box">
            <x-input :min="0" :max="999999"  class="price" v-model="coin_price" :readonly="!price_choice"  width="100px"></x-input>
        </div>
        <cell title=""  primary="content" style="background:#fff;left: -40px;height: 70px;" v-if="price_choice">
            <range v-model="coin_price" :min="min" :max="max" :decimal="true" :step="0.01"></range>
        </cell>
        <div class="push-item title">需求保证金 (违约将被燃烧)</div>
        <div class="push-item input-box">
            <input type="text" class="price" v-model="otc_freeze_seller" readonly="true">
        </div>
    </div>
    <box gap="32px 35px 0">
        <x-button type="primary" style="border-radius:99px;" class='push-btn' v-on:click.native="sure_push()">发布需求</x-button>
    </box>
</div>
</template>
<script>
import {XNumber,XSwitch,PopupRadio,Range} from "vux";
export default {
    components: {
        XNumber,
        XSwitch,
      PopupRadio,Range
    },
    data () {
      return {
        coin_info:[],
        show_coin : {coin_type:{coin_unit:this.$store.state.init.coin_uint}},
        otc_freeze_seller: this.$store.state.init.otc_freeze_buyer + this.$store.state.init.coin_uint ,
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
    computed:{

    },
    mounted () {
        this.otc_freeze_buyer = this.$store.state.init.otc_freeze_buyer
        this.getAccount()
    },
    methods:{
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
      sure_push(){
        if(this.click_lock)
        {
          return;
        }else{
          this.click_lock = true;
        }
        if(this.coin_price<this.sale_price_min){
          this.$vux.toast.text('行情不得小于最低价' + this.sale_price_min);
          return;
        }
        if(this.coin_price>this.sale_price_max){
          this.$vux.toast.text('行情不得大于最高价' + this.sale_price_max);
          return;
        }
        const formData = {
          amount:this.sale_amount,
          price:this.coin_price,
          min_buy_amount:this.min_buy_amount,
          type:1,
          coin_type:this.show_coin.coin_type.id
        }
        this.$http.post('/api/app.tokenotc/dealsbuy/push',formData).then(res=>{
          if(res.errcode=="0"){
            this.$vux.toast.show({text: '发布成功'});
            this.$router.push({path:'/token_otc'});
          }else{
            this.click_lock = false;
          }
        }).catch(err=>{
          this.click_lock = false;
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }

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
        if(parseInt(this.show_coin.coin_type.id)==4)
        {
          this.min = this.coin_price ;
        }else{
          this.min = this.coin_price * (1-this.show_coin.coin_type.otc_price_rate);
        }
        this.min =parseFloat(this.min.toFixed(2));
        this.max = this.coin_price * (1+this.show_coin.coin_type.otc_price_rate);
        this.max =parseFloat(this.max.toFixed(2))
      }
    }
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
