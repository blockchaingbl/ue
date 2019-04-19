<template lang="html">
<div class="deals-push-box">
    <div class="push-content">
        <div class="push-item title">需求数量</div>
        <div class="push-item vux-1px-b">
            <x-input type="number" placeholder="请输入需求数量" v-model="sale_amount" :required="true" :is-type="checkSaleNum"></x-input>
        </div>
        <!--<div class="push-item title">最低单次需求数量</div>-->
        <!--<div class="push-item vux-1px-b">-->
            <!--<x-input type="number" class="price" placeholder="请输入最低需求数量" v-model="min_buy_amount" :required="true"></x-input>-->
        <!--</div>-->
        <div class="push-item title" v-show="$store.state.init.otc_sale_price">单价</div>
        <div class="push-item input-box" id="sale_price" v-show="$store.state.init.otc_sale_price">
            <x-number :min="0" :max="999999" :step="(sale_price_max-sale_price_min)/10" v-model="coin_price" :fillable="true" button-style="round" align="left" width="100px"></x-number>
        </div>
        <div class="push-item title">需求行情</div>
        <div class="push-item input-box vux-1px-b">
            <input type="text" class="price" v-model="sale_total" readonly="true">
        </div>
        <div class="push-item title">需求所需USDG资产</div>
        <div class="push-item input-box vux-1px-b">
            <input type="text" class="price" v-model="usdg_total" readonly="true">
        </div>
        <!--<div class="push-item title">燃烧({{$store.state.init.coin_uint}})</div>-->
        <!--<div class="push-item input-box">-->
            <!--<input type="text" class="price" v-model="otc_fee" readonly="true">-->
        <!--</div>-->
        <div class="push-item title">需求保证金 (违约将被燃烧)</div>
        <div class="push-item input-box">
            <input type="text" class="price" v-model="otc_freeze_buyer" readonly="true">
        </div>
    </div>
    <box gap="32px 35px 0">
        <x-button type="primary" style="border-radius:99px;" class='push-btn' v-on:click.native="sure_push()">发布需求</x-button>
    </box>
</div>
</template>
<script>
import {XNumber,XSwitch} from "vux";
export default {
    components: {
        XNumber,
        XSwitch
    },
    data () {
        return {
            vc_total:0,
            vc_normal:0,
            coin_price:0,
            sale_amount:'',
            sale_price_min:0,
            sale_price_max:0,
            lock:false,
            otc_auth_type:null,
            otc_freeze_buyer:'',
            click_lock:false,
            min_buy_amount:0
        }
    },
    computed:{
        sale_total:function(){
            var saleAmount = this.sale_amount;
            if(!saleAmount)saleAmount = 0;
            saleAmount = parseFloat(saleAmount);
            if(saleAmount < this.$store.state.init.min_otc_sale){
                return 0;
            }
            var price = (saleAmount*1000000000*this.coin_price/1000000000).toFixed(2);
            return price || 0;
        },
        usdg_total:function () {
          var ret = this.sale_amount*1000000000*this.coin_price/1000000000 * 10000000 / this.$store.state.usdc_info.last / this.$store.state.init.usd_rate /10000000
          if (!ret)
          {
            return 0;
          }
          return ret.toFixed(5);
        }
    },
    mounted () {
        this.otc_freeze_buyer = this.$store.state.init.otc_freeze_buyer
        this.getUserinfo();
        this.coin_price = parseFloat(this.$store.state.init.coin_price);
        this.otc_fee_rate = parseFloat(this.$store.state.init.otc_fee_rate);
        this.sale_price_min = this.coin_price-this.coin_price*parseFloat(this.$store.state.init.otc_saleprice_rate);
        this.sale_price_max = this.coin_price+this.coin_price*parseFloat(this.$store.state.init.otc_saleprice_rate);
        $("#sale_price").find("input").unbind("blur");
        var _this = this;
        $("#sale_price").find("input").bind("blur",function(){
            _this.checkSalePrice();
        });
    },
    methods:{
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
        checkSalePrice(){
            if(this.coin_price<this.sale_price_min){
                this.$vux.toast.text('行情不得小于最低价' + this.sale_price_min);
                return;
            }
            if(this.coin_price>this.sale_price_max){
                this.$vux.toast.text('行情不得大于最高价' + this.sale_price_max);
                return;
            }
        },
        sure_push(){
            if(this.click_lock)
            {
                return;
            }else{
                this.click_lock = true;
            }
            const formData = {
                amount:this.sale_amount,
                price:this.coin_price,
                is_lock:Number(this.lock),
                lock_day:this.lock_day,
                min_buy_amount:this.min_buy_amount,
                type:1
            }
            this.$http.post('/api/app.otc/dealsbuy/push',formData).then(res=>{
                if(res.errcode=="0"){
                    this.$vux.toast.show({text: '发布成功'});
                    this.$router.push({path:'/deals/center'});
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
        checkSaleNum:function(value){
            return {
                valid: parseFloat(value) >= parseFloat(this.$store.state.init.min_otc_sale),
                msg: '需求数量不得低于'+this.$store.state.init.min_otc_sale
            }
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
