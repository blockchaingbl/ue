<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="assets-type vux-1px-b">
                    <popup-radio title="资产类型" :options="coin_info" v-model="chose_coin" @on-hide="hide_change">
                        <p slot="popup-header" class="vux-1px-b grant-coin-slot">资产类型</p>
                    </popup-radio>
                </group>
                <group class="numb-assets">
                    <cell class="cell-assets" title="转出数额" :value="asset"></cell>
                    <!-- <group  class="candy-radio" v-show="chose_coin==0">
                        <radio :options="vc_type" v-model="vc_chose_type" ></radio>
                    </group> -->
                    <div class="candy-radio flex-box" v-show="chose_coin==0">
                        <div class="radio-item flex-1 active" data-type="vc_normal" @click="radio_btn">
                            <div class="radio-title">可流通</div>
                            <div class="radio-numb">{{vc_normal}}</div>
                        </div>
                        <div class="radio-item flex-1" data-type="vc_untrade" @click="radio_btn">
                            <div class="radio-title">不可流通</div>
                            <div class="radio-numb">{{vc_untrade}}</div>
                        </div>
                    </div>
                    <x-input class="grant-money" v-model="amount"  placeholder="请输入转出数额"  type="number"  :required="true"></x-input>
                </group>
            </div>
            <div class="candy-basic-opera">
                <group class="candy-numb vux-1px-b" title="转入人手机号">
                    <x-input  v-model="mobile" is-type="china-mobile"   placeholder="请输入转入人手机号"  keyboard="number"  type="number"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="资产密码">
                    <x-input  v-model="security" type="password"   placeholder="请输入资产密码"  :required="true" ></x-input>
                </group>
            </div>
            <div class="candy-senior-opera">
                <group class="lock-time">
                    <div class="lock-select flex-box">
                        <div class="lock-se-title flex-1">是否锁仓</div>
                        <x-switch title="锁仓" v-model="lock" :disabled="true"></x-switch>
                    </div>
                    <x-input class="candy-numb" v-show="lock" v-model="lock_time" :disabled="auth_type==1" placeholder="请输入锁定时间" keyboard="number" type="number" :max="5">
                        <x-button slot="right" type="primary" mini>{{show_time_type}}</x-button>
                    </x-input>
                    <x-input class="candy-numb" v-show="lock" v-model="start_day"  placeholder="请输入开始释放天数" keyboard="number" type="number" :max="5">
                        <x-button slot="right" type="primary" mini>天后开始释放</x-button>
                    </x-input>
                    <group :title="day_release" v-show="lock"></group>
                </group>
            </div>
                <group class="grant-bottom">
                    <cell>
                        <div v-show="lock">
                            <span style="color: red">{{sugar_fee}}</span>
                        </div>
                        <div v-show="disable">
                            <span style="color: red">{{disable_desc}}</span>
                        </div>
                    </cell>
                </group>
            
            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="grant" v-if="!disable">转出</x-button>
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" v-else @click.native="grant">转出</x-button>
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
      lock:false,
      chose_coin : '',
      coin_info_detail : {},
      vc_total :{},
      show_coin : {},
      asset : 0,
      lock_time:'',
      disable:false,
      amount:'',
      disable_desc:'',
      coin_unit : this.$store.state.init.coin_uint,
      loading :true,
      sugar_type:'day',
      mobile:'',
      sugar_type_chose:[{key:'day',value:'按天'},{key:'week',value:'按周'},{key:'month',value:'按月'}],
      show_time_type:'天',
      security:'',
      auth_type:2,
      vc_type:[],
      vc_chose_type : 'vc_normal',
      vc_normal:'',
      vc_untrade:'',
      start_day:1
    }
},
        created () {
            this.get_auth_info();
        },
        methods: {
            get_auth_info(){
                this.$http.post('/api/app.user/locktransfer/auth',{}).then(res => {
                    let data = res.data.auth_info;
                    for (let x in data){
                        this.coin_info.push({key:x,value:data[x].coin_unit})
                        if(x==0)
                        {
                            this.vc_normal=data[x].assets.vc_normal;
                            this.vc_untrade=data[x].assets.vc_untrade;
                        }
                    }
                    this.chose_coin = this.$route.params.coin_type.toString()
                    this.coin_info_detail = data;
                    this.vc_total = parseFloat(data[0].assets.amount);
                    this.hide_change()
                    if(!res.data.has_sec){
                        this.$vux.toast.text('您还未设置资产密码,请前去设置');
                        setTimeout(()=>{
                            this.$router.push({name:'editSecurity'});
                        },2000)
                    }
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
                        this.disable_desc = `您的${this.coin_unit}实时资产低于${limit},已被限制锁仓转出`
                        this.lock = false;
                        return false;
                    }
                    this.lock = true;
                    if(this.show_coin.auth_info.auth_type==1)
                    {
                        this.lock_time = this.show_coin.auth_info.limit_day;
                    }
                    this.auth_type = this.show_coin.auth_info.auth_type;
                }else{
                    this.disable = true;
                    this.lock = false;
                    this.disable_desc = '您没有锁仓权限'
                }
            },
            grant(){
                let lock_time = this.lock_time
                const _this = this
                if(!this.lock){
                    lock_time = 0
                }
                let formData = {
                    coin_type:this.chose_coin,
                    amount:this.amount,
                    lock_time:lock_time,
                    mobile:this.mobile,
                    sugar_type:this.sugar_type,
                    security:this.security,
                    vc_chose_type:this.vc_chose_type,
                    start_day:this.start_day
                }
                if(this.lock_time != Math.round(this.lock_time)){
                    this.$vux.toast.text("请输入整的锁仓时间");
                    return false;
                }
                if(this.lock_time.length>5){
                    this.$vux.toast.text("您输入的锁仓天数太久，请重新输入");
                    return false;
                }
                if(this.lock &&　!this.lock_time){
                    this.$vux.toast.text("请输入锁仓时间");
                    return false;
                }
                if(!this.security){
                    this.$vux.toast.text("请输入密码");
                    return false;
                }
                this.loading = true;
                this.$http.post('/api/app.user/locktransfer/grant',formData).then(res => {
                    this.loading = false;
                    if(res.errcode==0){
                        const data ={
                            coin_unit:_this.show_coin.coin_unit,
                            sugar_info:res.data.sugar,
                            time_type:this.show_time_type
                        };
                        _this.$router.push({name:'lock_transfer_success',params:data});
                    }else{
                        this.$vux.toast.text(res.message);
                    }
                }).catch(err => {
                    this.loading = false;
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                });
            },
            change_show_text(value, label){
                this.show_time_type = label[1];
            },
            radio_btn(event){
                var el = event.currentTarget;
                this.vc_chose_type=$(el).attr("data-type");
                $(el).addClass('active').siblings().removeClass('active');
                console.log(this.vc_chose_type);
            }
        },
    computed:{
        sugar_fee:function () {
            if(this.lock &&　this.show_coin.hasOwnProperty('auth_info')){
                let rate =   this.show_coin.price /parseFloat(this.coin_info_detail[0].price);
                let fee = (this.amount * this.show_coin.auth_info.receive_fee*rate).toFixed(5);
                return `共需冲消${fee} ${this.coin_unit}`;
            }
            return ''
        },
        day_release:function () {
            let amount = parseFloat(this.amount)
            let day = parseInt(this.lock_time)
            if(amount>0&&day)
            {
                return `每日释放${(amount/day).toFixed(5)}`;
            }else {
                return ''
            }
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
        margin-bottom: 0.625rem;
    }
    .candy-senior-opera{
        
        background: #fff;
        margin-bottom: 0.625rem;
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
        min-height: 6rem;
        background: #fff;
        margin-bottom: 0.625rem;
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
    .grant-money.vux-x-input.weui-cell{
        padding: 0.75rem 0.9375rem 1.25rem;
    }
    .candy-radio{
        padding: 0 0.9375rem;
        margin: 0.625rem 0;
        .radio-item {
            padding: 0.625rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            .radio-title {
                font-size: 0.8125rem;
                line-height: 1.1875rem;
            }
            .radio-numb {
                font-size: 1.0625rem;
                line-height: 1.1875rem;
            }
        }
        .radio-item + .radio-item{
            margin-left: 0.9375rem;
        }
        .radio-item.active {
            border-color: #2f82ff;
            background: #2f82ff;
            color: #fff;
        }
    }
}
</style>


