<template lang="html">
<div class="walletcreate">
    <div class="create-box">
        <div class="create-top">
            <div class="create-top-text">密码用于保护私钥和流通授权，强度非常重要</div>
            <div class="create-top-text">钱包不存储密码，也无法帮您找回，请务必牢记</div>
        </div>
        <div class="item-box">
<!--             <group class="item">
                <x-input  placeholder="用于标识您的钱包" :min="4" :max="16" v-model="name" ref="name" :required="true"></x-input>
            </group> -->
            <group class="item">
                <x-input  placeholder="请输入钱包密码" type="password" v-model="password" :min="4" :max="16" ref="password" :required="true"></x-input>
            </group>
            <group class="item">
                <x-input  placeholder="请再次输入钱包密码" type="password" v-model="confirm_password" :min="4" :max="16" :equal-with="password" ref="confirm_password" :required="true"></x-input>
            </group>
           <!--  <group class="item">
                <x-input :show-clear="false" placeholder="密码提示信息（可不填）"></x-input>
            </group> -->
            <group class="item flex-box check-box" >
                <check-icon :value.sync="demo1"></check-icon>
                <div>我已经仔细阅读并同意</div>
                <span  @click.native="readYs" class="ystk" >《服务及隐私条款》</span>
            </group>
        </div>
        <div class="create-btn-box">
            <box gap="15px 15px">
                <x-button type="primary" style="border-radius:99px;" class='found-btn' @click.native="createWallet" >立即创建</x-button>
                <router-link class='import-btn' to="/user/wallet/import"> 导入钱包</router-link>
            </box>
        </div>

        <div class="savezjbox" v-transfer-dom>
        <popup v-model="seedShow" height="100%">
            <div class="tip">
                <div class="save_seed_title">钱包创建成功,请备份助记词</div>
                <ul>
                    <li>请抄写下您的钱包助记词</li>
                    <li>助记词用于恢复钱包或者重置密码</li>
                    <li>钱包不保存助记词信息，一旦丢失，您将无法再找回</li>
                </ul>
            </div>
            <div   v-clipboard:copy="seed" v-clipboard:success="onCopy" v-clipboard:error="onError">
            <input type="hidden" v-model="seed">
            <div class="seedShow" >
                {{seed}}
            </div>
            <p>点击复制助记词</p>
            </div>
            <box class="seedSave">
                <x-button type="primary" @click.native="seedSave">已保存好</x-button>
            </box>
        </popup>
        </div>

        <div class="savezjbox" v-transfer-dom>
        <popup v-model="ysShow" height="100%">
             <x-header :left-options="{showBack: false}" class="tip_title">免责声明</x-header>
            <div>
                <ul>
                    <li>请抄写下您的钱包助记词</li>
                </ul>
            </div>
            <box class="seedSave">
                <x-button type="primary" @click.native="agreeYs">我已阅读并同意</x-button>
            </box>
        </popup>
        </div>
    </div>
</div> 
</template>
<script>
import {XHeader,TransferDom, CheckIcon,XInput,XButton,Box,AlertModule,Popup,XTextarea } from 'vux'
  import router from '@/router';
    import JSON from "JSON";
    import fanweEth from "@/ethereum";
    import fanweCrypto from '@/crypto';
    export default {
    directives: {
        TransferDom
    },
      components: {
          "x-header":XHeader,
          "x-input":XInput,
          "x-button":XButton,
          "box":Box,
          "popup":Popup,
          "x-textarea":XTextarea,
        CheckIcon
    },
    data () {
        return {
            demo1: true,
            page_title:router.currentRoute.meta.title,
            page_name:router.currentRoute.name,
            name:"default",
            password:"",
            confirm_password:"",
            seedShow:false,//true,
            ysShow:false,
            seed:""
        }
    },
    mounted () {
        
    },
    methods: {
    onCopy: function (e) {
         this.$vux.toast.text("复制成功");
      //console.log('You just copied: ' + e.text)
    },
    onError: function (e) {
         this.$vux.toast.text("复制失败，您可以尝试手动记录");
      //console.log('Failed to copy texts')
    },
      seedSave:function(){
        router.replace({path:"/user/wallet"});
      },
      readYs:function(){
        console.log(123);
       this.ysShow=true;
      },
      agreeYs:function(){
       this.ysShow=false;
      },
      createWallet:function(){
         // this.$refs.name.validate();
          this.$refs.password.validate();
          this.$refs.confirm_password.validate();
          console.log(this.$store);
          if(this.$refs.password.valid&&this.$refs.confirm_password.valid)
          {

              var loading = this.$loading({text:"创建中"});
              var $vue = this;
              setTimeout(function(){

                  $vue.seed = fanweEth.wallet.createSeed();
                  var _wallet = fanweEth.wallet.createBySeed($vue.seed,"",0);
                  var wallet = {};
                  wallet.name = $vue.name;
                  wallet.address = _wallet.address;
                  wallet.enc_privateKey = fanweCrypto.encrypt(_wallet.privatekey,$vue.password);
                  $vue.$store.commit("addWallet",wallet);
                  $vue.seedShow = true;
                  // console.log("钱包新建成功");
                  // console.log(wallet);
                  // console.log("密码传到服务端");
                  // console.log($vue.password);
                $vue.$http.post('/api/app.wallet/wallet/bind',{
                    address:wallet.address,
                    privatekey:wallet.enc_privateKey,
                    security:$vue.password,
                   }).then(res => {
                    $vue.$vux.toast.text(res.message);
                console.log(res);
                }).catch(err => {
                console.log(err);
                if (err.errcode) {
                  $vue.$vux.toast.text(err.message);
                }
                //  this.Toast(err || '网络异常，请求失败');
                 });
                  loading.close();
              },500);
          }
          else
          {
             // this.$refs.name.forceShowError = true;
              this.$refs.password.forceShowError = true;
              this.$refs.confirm_password.forceShowError = true;
              if(!this.confirm_password)
              {
                  this.$refs.confirm_password.errors.equal = '输入不一致';
                  this.$refs.confirm_password.getError();
              }
          }
      }
    },
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    
    .walletcreate{
        height: 100%;
        background: #fff;
        .ystk{
          color: #628cf8;
        }
        .create-top{
            background:-webkit-linear-gradient(left,#26cdcd,#5f5ece);
            background:linear-gradient(to right,#26cdcd,#5f5ece);
            color: #fff;
            padding: 0.625rem;
            .create-top-text{
                padding: 0 0.75rem;
                line-height: 1.125rem;
                position: relative;
                &:before{
                    content: '';
                    display: block;
                    width: 4px;
                    height: 4px;
                    background: #fff;
                    border-radius: 2px;
                    top: 0.5rem;
                    left: 0;
                    position: absolute;
                }
            }
        }
        .weui-cells{
            margin: 0;
            &:before{
                display: none;
            }
        }
        .check-box{
            line-height: 3.75rem;
            height: 3.75rem;
            padding: 0 0.625rem;
            .weui-cells{
                display: -webkit-box;
                display: -webkit-flex;
                display: flex;
                -webkit-box-align: center;
                -webkit-align-items: center;
                align-items: center;
                font-size: @fs-small;
                &:after{
                    display: none;
                }
            }
            .vux-check-icon{
                line-height: 3.75rem;
                height: 3.75rem;
                margin-right: 6px;
                i{
                    margin-top: -2px;
                }
                .weui-icon-success{
                    font-size: @fs-middle;
                    &:before{
                        color: #628df9;
                    }
                }
                .weui-icon-circle{
                    font-size: @fs-middle;
                    color: #bbb;
                }
            }
        }
        .create-btn-box{
            .found-btn{
                font-size: 0.9375rem;
                height: 40px;
            }
            .import-btn{
                display: block;
                width: 7.5rem;
                text-align: center;
                font-size: @fs-middle;
                margin: 1.125rem auto 0;
                color: #5d5d61;
                &:after{
                    display: none;
                }
                &:befter{
                    display: none;
                }
            }
        }
    }
    .savezjbox{
        .tip{
            line-height: 25px;
            padding:.8rem 1.5rem;
            padding-bottom: 1.8rem;
            background: -webkit-gradient(linear, left top, right top, from(#26cdcd), to(#5f5ece));
            background: linear-gradient(to right, #26cdcd, #5f5ece);
            color:#fff;
        }
        .vux-popup-dialog{ background: #fff; }
        .seedShow{ padding:2rem; background: #ccc; margin:2rem 3rem; line-height: 2rem; font-size:1rem;}
        .seedSave{ 
            height: 3rem;
            margin:0px 3rem; 
            position: absolute;
            bottom: .8rem;
            left: 0;
            right: 0;
        }
        .save_seed_title{ text-align: center; font-size: 16px; line-height: 35px;}
        p{
            height: 3rem;
            color: #628cf8;
            text-align: center;
        }
    }
</style>
