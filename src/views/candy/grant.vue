<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="assets-type vux-1px-b">
                    <popup-radio title="资产类型" :options="coin_info" v-model="chose_coin" @on-hide="hide_change">
                        <p slot="popup-header" class="vux-1px-b grant-coin-slot">资产类型</p>
                    </popup-radio>
                </group>
                <group class="numb-assets vux-1px-b">
                    <cell class="cell-assets" title="发糖总数额" :value="asset"></cell>
                    <x-input  v-model="amount"  placeholder="请输入发糖总数额"  type="number"  :required="true"></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="糖果份数">
                    <x-input  v-model="copys"  placeholder="请输入糖果份数"  keyboard="number"  type="number"  :required="true" :max="5"></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="资产密码">
                    <x-input  v-model="security" type="password"   placeholder="请输入资产密码"  :required="true" ></x-input>
                </group>
                <group :title="per_sugar"></group>
            </div>
            <div class="candy-senior-opera">
                <group class="lock-time vux-1px-b">
                    <!--<div class="lock-select flex-box">-->
                        <!--<div class="lock-se-title flex-1">是否锁仓</div>-->
                        <!--<x-switch title="锁仓" v-model="lock" :readonly="true"></x-switch>-->
                    <!--</div>-->
                    <x-input class="candy-numb" v-show="lock"  v-model="lock_day" placeholder="请输入锁仓天数(天)" keyboard="number" type="number" :max="5" :readonly="true"></x-input>
                </group>
                <group class="limit-time" title="领取有效期(天)" :readonly="true">
                    <x-input  placeholder="输入发糖后可领取时间" v-model="receive_limit" type="number" keyboard="number"  :required="true"></x-input>
                </group>
            </div>
                <group class="grant-bottom">
                    <cell>
                        <div v-show="!disable">
                            <span style="color: red">{{sugar_fee_one}}</span>
                        </div>
                        <div v-show="!disable">
                            <span style="color: red">{{sugar_fee}}</span>
                        </div>
                        <div v-show="disable">
                            <span style="color: red">{{disable_desc}}</span>
                        </div>
                    </cell>
                </group>

            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="grant" v-if="!disable">发糖果</x-button>
                <x-button type="primary" style="background-color: #6f7180;border-radius:0;height:2.875rem;font-size:0.875rem;" disabled v-else>发糖果</x-button>
            </box>
        </div>


        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
import { XInput,XButton,Box,PopupRadio,XSwitch,Loading, TransferDomDirective as TransferDom,Radio  } from 'vux';
export default {
    directives: {
        TransferDom
    },
    components: {
        XInput,
        XButton,
        Box,
        PopupRadio,
        XSwitch,
        Loading,
        Radio
    },
    data () {
        return {
            coin_info:[],
            lock:true,
            chose_coin : '',
            coin_info_detail : {},
            vc_total :{},
            show_coin : {},
            asset : 0,
            copys :'',
            lock_day:50,
            disable:false,
            amount:'',
            disable_desc:'',
            coin_unit : this.$store.state.init.coin_uint,
            copys_amount:0,
            receive_limit:1,
            loading :true,
            mobile:'',
            security:'',
        }
    },
    mounted () {
        this.get_auth_info();
    },
    methods: {
        get_auth_info(){
            this.$http.post('/api/app.user/sugar/sugar_auth',{}).then(res => {
                let data = res.data.auth_info;
                for (let x in data){
                    this.coin_info.push({key:x,value:data[x].coin_unit})
                }
                this.chose_coin = '0'
                this.coin_info_detail = data;
                this.vc_total = parseFloat(data[0].assets.amount);
                this.hide_change()
                this.loading = false;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
            });
        },
        hide_change(){
            let index = this.chose_coin;
            this.show_coin = this.coin_info_detail[index];
            this.asset = "剩余资产："+this.show_coin.assets.amount;
            if(this.show_coin.auth_info.status==1) {
                this.disable = false;
                if(parseFloat(this.vc_total)<parseFloat(this.show_coin.auth_info.min_limit)){
                    this.disable = true;
                    let limit = parseFloat(this.show_coin.auth_info.min_limit).toFixed(5);
                    this.disable_desc = `您的${this.coin_unit}实时资产低于${limit},已被限制`
                }
            }else{
                this.disable = true;
                this.disable_desc = '您没有权限'
            }
        },
        grant(){
            let lock_day = this.lock_day
            const _this = this
            if(!this.lock){
                lock_day = 0
            }

            let formData = {
                coin_type:this.chose_coin,
                amount:this.amount,
                copys:this.copys,
                copys_amount:this.copys_amount,
                lock_day:lock_day,
                receive_limit:this.receive_limit,
                mobile:this.mobile,
                security:_this.security
            }
            if((this.copys==0||this.copys=='')){
                this.$vux.toast.text("输入份数不能小于1");
                return false;
            }
            if((this.copys != Math.round(this.copys))){
                this.$vux.toast.text("请输入整的份数");
                return false;
            }
            if(this.lock_day != Math.round(this.lock_day)){
                this.$vux.toast.text("请输入整的锁仓天数");
                return false;
            }
            if(this.copys.length>5){
                this.$vux.toast.text("您输入的份数太多，请重新输入");
                return false;
            }
            if(this.lock_day.length>5){
                this.$vux.toast.text("您输入的锁仓天数太久，请重新输入");
                return false;
            }
            if(this.lock &&　!this.lock_day){
                this.$vux.toast.text("请输入锁仓天数");
                return false;
            }
            if(!this.security){
                this.$vux.toast.text("请输入密码");
                return false;
            }
            this.loading = true;
            this.$http.post('/api/app.user/sugar/grant',formData).then(res => {
                this.loading = false;
                if(res.errcode==0){
                    const data ={
                        coin_unit:_this.show_coin.coin_unit,
                        sugar_info:res.data.sugar
                    };
                    _this.$router.push({name:'candy_success',params:data});
                }else{
                    this.$vux.toast.text(res.message);
                }
            }).catch(err => {
                this.loading = false;
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
            });


        }
    },
    computed:{
        per_sugar:function () {
            let per_sugar = 0;
            if(this.copys>0){
                per_sugar = (this.amount/this.copys).toFixed(5)
            }
            this.copys_amount = per_sugar;
            let unit = this.show_coin.coin_unit
            return `每人领多少: ${per_sugar} ${unit}`
        },
        sugar_fee_one:function () {
            if(!this.disable && this.copys>0　&& this.show_coin.hasOwnProperty('auth_info')){
                let rate = this.show_coin.price / parseFloat(this.coin_info_detail[0].price);
                let fee = (this.amount * this.show_coin.auth_info.receive_fee/this.copys*rate).toFixed(5);
                return `一份糖果所需手续费${fee} ${this.coin_unit}`;
            }
            return ''
        },
        sugar_fee:function () {
            if(!this.disable &&　this.show_coin.hasOwnProperty('auth_info')){
                let rate =   this.show_coin.price /parseFloat(this.coin_info_detail[0].price);
                let fee = (this.amount * this.show_coin.auth_info.receive_fee*rate).toFixed(5);
                return `共需手续费${fee} ${this.coin_unit}`;
            }
            return ''
        }
    }
}
</script>
<style lang="less" scoped>
@import "../../assets/css/variable.less";
.page-grant{
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


