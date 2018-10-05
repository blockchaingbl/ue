<template lang="html">
<div class="setbank">
    <div class="setbank-box">
        <div class="setbank-top">注：请设置您的转入支付方式，请务必使用本人实名账号</div>
        <group class="bank-item no-line">
            <x-input title='姓名' text-align="right" v-model="payment_receipt" :max="10"></x-input>
        </group>
        <div class="block">
            <group class="bank-item">
                <selector ref="defaultValueRef" title="开户银行" direction="rtl" :options="list" v-model="payment_org"></selector>
            </group>
        </div>
        <div class="block">
            <group class="bank-item">
                <x-input title='开户支行' text-align="right" v-model="branch_bank" placeholder="请输入您的开户支行" :max="25"></x-input>
            </group>
        </div>
        <div class="block">
            <group class="bank-item">
                <x-input title='银行卡号' text-align="right" v-model="payment_account" placeholder="请输入您的银行卡号" :max="25"></x-input>
            </group>
        </div>
        <div class="block">
            <group class="bank-item no-line">
                <x-switch title="是否显示联系方式" v-model="is_connect"></x-switch>
            </group>
        </div>
        <div class="block">
            <group class="bank-item no-line">
                <x-input type="text" title='联系方式' text-align="right" v-model="connect" placeholder="请输入您的联系方式"></x-input>
            </group>
        </div>
        <div class="block">
            <group class="bank-item no-line">
                <x-input type="password" title='资产密码' text-align="right" v-model="security" placeholder="请输入您的资产密码"></x-input>
            </group>
        </div>
        <div class="reset-btn-box">
            <box gap="30px 35px">
                <x-button type="primary" style="border-radius:99px;" class='found-btn' v-on:click.native="bindPayment()">保存</x-button>
            </box>
        </div>
    </div>
</div> 
</template>
<script>
import { Qrcode ,XSwitch} from 'vux'
import { Selector } from 'vux'
import { md5 } from 'vux'
export default {
  components: {
    //stepone:() => import('@/views/login/inc/stepone'),
    Qrcode,
    Selector,
  XSwitch
  },
  data () {
    return {
        hasWallet:0,
        list: [
            {key: '建设银行', value: '建设银行'},
            {key: '工商银行', value: '工商银行'},
            {key: '海峡银行', value: '海峡银行'},
            {key: '农业银行', value: '农业银行'},
            {key: '交通银行', value: '交通银行'},
            {key: '中国银行', value: '中国银行'},
            {key: '中信银行', value: '中信银行'},
            {key: '光大银行', value: '光大银行'},
            {key: '华夏银行', value: '华夏银行'},
            {key: '民生银行', value: '民生银行'},
            {key: '广发银行', value: '广发银行'},
            {key: '平安银行', value: '平安银行'},
            {key: '招商银行', value: '招商银行'},
            {key: '兴业银行', value: '兴业银行'},
            {key: '浦发银行', value: '浦发银行'},
            {key: '恒丰银行', value: '恒丰银行'},
            {key: '邮政储蓄银行', value: '邮政储蓄银行'},
            {key: '其它', value: '其它'},
        ],
        payment_receipt:"",
        payment_org:"",
        payment_account:"",
        security:"",
        branch_bank:"",
        connect:'',
        is_connect:false
    }
  },
  mounted () {
    this.getPaymentInfo();
  },
  methods:{
    getPaymentInfo(){
        this.$http.post('/api/app.payment/payment/bindinfo',{"payment_key":"bankcard"}).then(res => {
            if(res.data.bind_info.bankcard){
                this.payment_receipt=res.data.bind_info.bankcard.payment_receipt;
                this.payment_account=res.data.bind_info.bankcard.payment_account;
                this.payment_org=res.data.bind_info.bankcard.payment_org;
                this.branch_bank = res.data.bind_info.bankcard.branch_bank;
                this.is_connect = Boolean(res.data.bind_info.bankcard.is_connect);
                this.connect = res.data.bind_info.bankcard.connect;
            }
        }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
            console.log(err);
            //  this.Toast(err || '网络异常，请求失败');
        });
    },
    bindPayment(){
        if(this.payment_receipt==""){
          this.$vux.toast.text("请填写姓名");
          return;
        }
        if(this.payment_org==""){
            this.$vux.toast.text("请选择开户银行");
            return;
        }
        if(this.payment_account==""){
            this.$vux.toast.text("请填写银行卡号");
            return;
        }
        if(this.security==""){
            this.$vux.toast.text("请输入资产密码");
            return;
        }
        this.$http.post('/api/app.payment/payment/bind',{
            'payment_key':'bankcard',
            'payment_receipt':this.payment_receipt,
            'payment_org':this.payment_org,
            'payment_account':this.payment_account,
            'security':md5(this.security),
            'branch_bank':this.branch_bank,
            'is_connect':this.is_connect?1:0,
            'connect':this.connect
        }).then(res => {
            if(res.errcode=="0"){
                this.$vux.toast.show({text: '绑定成功'})
                this.$router.push({path:'/user/setpay'});
            }
        }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
            console.log(err);
            //  this.Toast(err || '网络异常，请求失败');
        });
    },
  }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    @import '~vux/src/styles/1px.less';
    
    .setbank{
        .setbank-top{
            line-height: 3.25rem;
            font-size: @fs-small;
            color: #ff6c6c;
            padding: 0 0.625rem;
        }
        .block{
            margin-top: 0.625rem;
            
        }
        .bank-item{
            background: #fff;
            padding-left: 0.625rem;
            .weui-select{
                color: #4c4c51;
            }
        }
        .weui-cells{
            font-size: @fs-middle;
            margin: 0;
            
            color: #4c4c51;
            &:before{
                display: none;
            }
            .weui-cell{
                height: 2.75rem;
                padding: 0 0.625rem 0 0;
            }
            input{
                font-size: @fs-small;
            }
            input::-webkit-input-placeholder {color:#999;
                color: #bbb;
            }
        }
        .no-line{
            .weui-cells{
                &:after{
                    display: none;
                }
            }
        }
    }
</style>
