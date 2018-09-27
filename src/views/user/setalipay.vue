<template lang="html">
<div class="setalipay">
    <div class="setalipay-box">
        <div class="setalipay-top">
            <div class="update-box flex-box">
                <div class="iconfont" v-if="qrcode==''">&#xe637;</div>
                <div class="setalipay-top-text" v-if="qrcode==''">
                    上传您的支付宝二维码
                </div>
                <img class="qrcode-img" v-bind:src="qrcode" v-else>
            </div>
            <div class="upload-btn" v-if="!$store.state.init.is_app">
                <vue-core-image-upload
                        class="btn btn-primary"
                        :crop="false"
                        @imageuploaded="imageuploaded"
                        :data="params"
                        :max-file-size="5242880"
                        inputOfFile="file"
                        :url="uploadUrl"
                        text="">
                </vue-core-image-upload>
            </div>
            <div class="upload-btn" v-else @click="AppUpload"></div>
        </div>
        <div class="block">
            <group class="alipay-item no-line">
                <x-input title='姓名' text-align="right" v-model="payment_receipt" :max="10"></x-input>
            </group>
        </div>
        <div class="block">
            <group class="alipay-item">
                <x-input title='支付宝账户' text-align="right" v-model="payment_account" placeholder="请输入您的支付宝账户" :max="20"></x-input>
            </group>
        </div>
        <div class="block">
            <group class="alipay-item no-line">
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
import { md5 } from 'vux'
import VueCoreImageUpload from 'vue-core-image-upload'
export default {
  components: {
      'vue-core-image-upload': VueCoreImageUpload
  },
  data () {
    return {
        hasWallet:0,
        payment_receipt:"",
        payment_account:"",
        security:"",
        qrcode:"",
        uploadUrl:"/api/app.util/upload",
        params:{
            type:"weixin",
            _user_token:this.$store.state.token
        },
        h5flag:true
    }
  },
  mounted () {
      this.getPaymentInfo();
      const _this = this
      window.CutCallBack = function (base64Str) {
         _this.CutCallBack(base64Str)
      }
  },
  methods:{
      getPaymentInfo(){
          this.$http.post('/api/app.payment/payment/bindinfo',{"payment_key":"alipay"}).then(res => {
              if(res.data.bind_info.alipay){
                  this.payment_receipt=res.data.bind_info.alipay.payment_receipt;
                  this.payment_account=res.data.bind_info.alipay.payment_account;
                  this.qrcode=res.data.bind_info.alipay.qrcode;
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
          if(this.qrcode==""){
              this.$vux.toast.text("请上传二维码图片");
              return;
          }
          if(this.payment_receipt==""){
              this.$vux.toast.text("请填写姓名");
              return;
          }
          if(this.payment_account==""){
              this.$vux.toast.text("请填写账号");
              return;
          }
          if(this.security==""){
              this.$vux.toast.text("请输入资产密码");
              return;
          }
          this.$http.post('/api/app.payment/payment/bind',{
              'payment_key':'alipay',
              'qrcode':this.qrcode,
              'payment_receipt':this.payment_receipt,
              'payment_account':this.payment_account,
              'security':md5(this.security)
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
      imageuploaded(res) {
          if (res.errcode == 0) {
              this.qrcode = res.data.file_url;
          }
      },
      AppUpload(){
        App.CutPhoto('{"w":"200","h":"200"}');
      },
      CutCallBack(base64Str){
          var formData = new FormData();
          formData.append('type','alipay');
          formData.append('file',convertBase64UrlToBlob(base64Str));
          let config = {
              header:{
                  'Content-Type': 'multipart/form-data'
              }
          }
          this.$http.post('/api/app.util/upload', formData,config)
              .then(res=>{
                  if (res.errcode == 0) {
                      this.qrcode = res.data.file_url;
                  }
              })
      }
  }
}

</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    @import '~vux/src/styles/1px.less';
    
    .setalipay{
        .setalipay-top{
            padding: 0.625rem;
            background: #fff;
            .update-box{
                border: 1px dashed #8d97a3;
                border-radius: 5px;
                color: #8d97a3;
                font-size: @fs-small;
                flex-direction: column;
                justify-content: center;
                height: 14.375rem;
                .iconfont{
                    font-size: 2rem;
                }
                .setalipay-top-text{
                    line-height: 1.75rem;
                }
                .qrcode-img{
                    max-width: 90%;
                    max-height: 90%;
                }
            }
        }
        .block{
            margin-top: 0.625rem;
            
        }
        .alipay-item{
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
    .upload-btn{
        position: absolute;
        top: 0.625rem;
        left: 0.625rem;
        right: 0.625rem;
        height: 14.375rem;
        .btn{
            height: 100%;
        }
    }
</style>
