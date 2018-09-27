<template lang="html">
    <div :id="page_name">
                <div class="wallet-head flex-box">
                <div class="head-top flex-box">
                    <div class="fangkuai flex-1">
                        <div class="lingxing"></div>
                    </div>
                    <!--<div class="scan">-->
                        <!--<i class="iconfont">&#xe6e1;</i>-->
                    <!--</div>-->
                </div>
                <div class="wallet-private_key" v-clipboard:copy="address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{address}}</div>
            </div>
        <group>
            <div  v-on:click="changePassword">
                <cell is-link>
                    <span slot="title">修改密码</span>
                </cell>
            </div>
        </group>

        <group>
            <div  v-on:click="exportPrivate" class="op_row vux-1px-b">
                <cell is-link>
                    <span slot="title">导出私钥</span>
                </cell>
            </div>
            <div  v-on:click="exportKeystore">
                <cell is-link>
                    <span slot="title">导出Keystore</span>
                </cell>
            </div>
        </group>




<div  v-transfer-dom>
        <popup v-model="showChangeName">
            <popup-header
                    left-text=""
                    right-text=""
                    title="请输入钱包名称"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="show1 = false">
            </popup-header>
            <box gap="0 10px 20px 10px">
                <group>
                    <x-input :max="16" :min="4" :required="true" placeholder="请输入钱包的新名称" v-model="inputWalletName" ref="wallet_name"></x-input>
                </group>
                 <flexbox>
                    <flexbox-item>
                      <x-button @click.native="cancelPop">取消</x-button>
                    </flexbox-item>
                    <flexbox-item>
                      <x-button type="primary" @click.native="doChangeName">确认</x-button>
                    </flexbox-item>
                  </flexbox>
               <!--  <group class="nobg flex-box">
                    <x-button type="primary" @click.native="doChangeName">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group> -->
            </box>
        </popup>
    
</div>

<div  v-transfer-dom>
        <popup v-model="showChangePassword">
            <popup-header
                    left-text=""
                    right-text=""
                    title="修改钱包密码"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="show1 = false">
            </popup-header>
            <box gap="0 10px 20px 10px">
                <group>
                    <x-input :max="16" :min="4" :required="true" type="password" placeholder="请输入钱包的原密码" v-model="old_password" ref="wallet_password"></x-input>
                    <x-input  placeholder="钱包新密码" type="password" v-model="password" :min="4" :max="16" ref="password" :required="true"></x-input>
                    <x-input  placeholder="再次输入钱包新密码" type="password" v-model="confirm_password" :min="4" :max="16" :equal-with="password" ref="confirm_password" :required="true"></x-input>
                </group>

                <group></group>
                 <flexbox>
                    <flexbox-item>
                      <x-button @click.native="cancelPop">取消</x-button>
                    </flexbox-item>
                    <flexbox-item>
                      <x-button type="primary" @click.native="doChangePassword">确认</x-button>
                    </flexbox-item>
                  </flexbox>
              <!--   <group class="nobg">
                    <x-button type="primary" @click.native="doChangePassword">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group> -->
            </box>
        </popup>
    
</div>
<div  v-transfer-dom>
        <popup v-model="showNeedPassword">
            <popup-header
                    left-text=""
                    right-text=""
                    title="请确认钱包密码"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="show1 = false">
            </popup-header>
            <box gap="0 10px 20px 10px">
                <group class="password_input">
                    <x-input :max="16" :min="4" :required="true" type="password" placeholder="请输入钱包的密码" v-model="need_password" ref="need_password"></x-input>
                </group>

                <group></group>
                 <flexbox>
                    <flexbox-item>
                      <x-button @click.native="cancelPop">取消</x-button>
                    </flexbox-item>
                    <flexbox-item>
                      <x-button type="primary" @click.native="doNeedPassword">确认</x-button>
                    </flexbox-item>
                  </flexbox>
               <!--  <group class="nobg">
                    <x-button type="primary" @click.native="doNeedPassword">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group> -->
            </box>
        </popup>
</div>
<div class="dcsy" v-transfer-dom>
        <popup v-model="showExportPrivate" height="100%">
            <div class="tip">
                <div class="export_title">私钥导出成功,私钥不可更改，请妥善保存你的私钥</div>
            </div>
            <input type="hidden" v-model="export_private_key">
            <div class="export_show"  v-clipboard:copy="export_private_key" v-clipboard:success="onCopy" v-clipboard:error="onError">
                {{export_private_key}}
                <span class="copyd">点击复制</span>
            </div>

            <qrcode :value="export_private_key" class="qrcode"></qrcode>

            <box class="export_save">
                <x-button type="primary" @click.native="cancelPop">已保存好</x-button>
            </box>
        </popup>
</div>

<div class="dcsy" v-transfer-dom>
    <popup v-model="showExportKeystore" height="100%">
        <div class="tip">
            <div class="export_title">Keystore导出成功,请妥善保存你的Keystore</div>
        </div>
        <input type="hidden" v-model="export_keystore">
        <div class="export_show_keystore"  v-clipboard:copy="export_keystore" v-clipboard:success="onCopy" v-clipboard:error="onError">
            {{export_keystore}}
            <span class="copyd">点击复制</span>
        </div>

        <qrcode :value="export_keystore" class="qrcode"></qrcode>

        <box class="export_save">
            <x-button type="primary" @click.native="cancelPop">已保存好</x-button>
        </box>
    </popup>
</div>

    </div>
</template>
<script>
    import {TransferDom,  XHeader,Actionsheet,Cell,Badge,XButton,Box,PopupHeader,Popup,AlertModule,Qrcode , Flexbox, FlexboxItem } from 'vux';

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
            "actionsheet":Actionsheet,
            "cell":Cell,
            "badge":Badge,
            "x-button":XButton,
            "box":Box,
            "popup-header":PopupHeader,
            "popup":Popup,
            "qrcode":Qrcode,
            Flexbox, 
            FlexboxItem
        },
        data () {
            this.$store.commit("loadWallets");

//            var current_address = router.currentRoute.params['address'];
            var current_address = localStorage.default_address;  //本地只会保存一个钱包
            console.log(current_address);
            console.log(this.$store.state.wallets);
            var wallet = this.$store.state.wallets[current_address];

//            console.log("实始化该页面钱包时， 钱包从服务端获取");
//            console.log(wallet);

            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                wallet:{},
                address:"",
                showChangeName:false,
                inputWalletName:"",
                showChangePassword:false,
                old_password:"",
                password:"",
                confirm_password:"",
                need_password:"",
                showNeedPassword:false,
                needPasswordFor:"",
                export_private_key:"",
                export_keystore:"",
                showExportPrivate:false,
                showExportKeystore:false,
            }
        },
        mounted () {
            this.getinfo();
        },
        methods: {
            getinfo:function(){
                 this.$http.post('/api/app.user/account/info',{
                       }).then(res => {
                       // this.$vux.toast.text(res.message);
                       this.inputWalletName=res.data.account_info.username;
                       this.address=res.data.account_info.address;
                       this.wallet.address=res.data.account_info.address;
                       this.wallet.enc_privateKey=res.data.account_info.privatekey;
                        console.log(res);
                    }).catch(err => {
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                            //  this.Toast(err || '网络异常，请求失败');
                    });
            },
            changePassword:function(){
                this.showChangePassword = true;
            },
            doChangePassword:function(){
                 var privateKey = this.checkPassword(this.old_password);
                 if(privateKey=="")return;
                this.$refs.password.validate();
                this.$refs.confirm_password.validate();
                if(this.$refs.password.valid&&this.$refs.confirm_password.valid)
                {
                    if(this.password==this.old_password)
                    {
                        AlertModule.show({
                            title: '错误',
                            content: "新密码与旧密码相同",
                            onShow () {
                            },
                            onHide () {

                            }
                        });
                    }
                    else{
                        var enc_privateKey = fanweCrypto.encrypt(privateKey,this.password);
                        this.$store.commit("updateWallet",this.wallet);
                        this.$http.post('/api/app.wallet/wallet/setsecurity',{
                            security:this.old_password,
                            new_security:this.password,
                            privatekey:enc_privateKey
                           }).then(res => {
                           // this.$vux.toast.text(res.message);
                            console.log(res);
                            this.old_password = "";
                            this.password = "";
                            this.confirm_password = "";
                            AlertModule.show({
                                title: '提示',
                                content: res.message,
                                onShow(){
                                },
                                onHide(){
                                    router.replace({path:"/user/wallet"});
                                }
                            });
                            this.cancelPop();
                        }).catch(err => {
                            console.log(err);
                            if (err.errcode) {
                                this.$vux.toast.text(err.message);
                            }
                        //  this.Toast(err || '网络异常，请求失败');
                         });
                    /*    console.log("钱包密码修改成功");
                        console.log(this.wallet);
                        console.log("密码传到服务端");
                        console.log(this.password);*/

                        
                    }
                }
                else
                {
                    this.$refs.password.forceShowError = true;
                    this.$refs.confirm_password.forceShowError = true;
                    if(!this.confirm_password)
                    {
                        this.$refs.confirm_password.errors.equal = '输入不一致';
                        this.$refs.confirm_password.getError();
                    }
                }

            },
            exportPrivate:function(){
                this.needPasswordFor = "export_private";
                this.showNeedPassword = true;
            },
            exportKeystore:function(){
                this.needPasswordFor = "export_keystore";
                this.showNeedPassword = true;
            },

            doNeedPassword:function(){

                var privateKey = this.checkPassword(this.need_password);
                if(privateKey!="")
                {
                    var loading = this.$loading({text:"导出中"});
                    var $vue = this;
                    setTimeout(function(){
                        if($vue.needPasswordFor=="export_private")
                        {
                            $vue.export_private_key = privateKey;
                            $vue.showExportPrivate = true;
                        }
                        else
                        {
                            $vue.export_keystore = fanweEth.wallet.privatekeyToKeystore(privateKey,$vue.need_password);
                            $vue.showExportKeystore = true;
                        }
                        $vue.showNeedPassword = false;
                        $vue.need_password = "";
                        loading.close();
                    },500);

                }
            },
            cancelPop:function(){
                this.showChangeName = false;
                this.showChangePassword = false;
                this.showNeedPassword = false;
                this.showExportPrivate = false;
                this.showExportKeystore = false;
            },
            checkPassword:function(password){
                var privateKey ="";
                var dec_address =  "";

//                console.log("加密私钥和钱包地址从服务端获取，即钱包使用服务端的");
//                console.log(this.wallet);

                try{
                    var privateKey = fanweCrypto.decrypt(this.wallet.enc_privateKey,password);
                    dec_address = fanweEth.wallet.privatekeyToAddress(privateKey);
                }catch(ex)
                {
                    console.log(ex);
                }

                if(dec_address.toLowerCase()!=this.wallet.address.toLowerCase())
                {
                    AlertModule.show({
                        title: '错误',
                        content: "钱包密码错误",
                        onShow () {
                        },
                        onHide () {

                        }
                    });
                    return "";
                }

                return privateKey;
            },

            onCopy: function (e) {
                 this.$vux.toast.text("复制成功");
              //console.log('You just copied: ' + e.text)
            },
            onError: function (e) {
                 this.$vux.toast.text("复制失败，您可以尝试手动记录");
              //console.log('Failed to copy texts')
            },
        }
    }
</script>
<style lang="less" scoped>
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .vux-popup-dialog{
        background: #fff;
    }
    .export_show,.export_show_keystore{
        position: relative;
    }
    .copyd{
        position: absolute;
        right: 0;
        top: 0;
        line-height: 1rem;
        font-size: .7rem;
        color: #628cf8;
        padding:.5rem;
    }
    #wallet_manage{
        .weui-cells{
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .wallet-head{
            background-color: #2e3d6c;
            background-image: url(../../assets/images/walletbg.png);
            background-repeat: no-repeat;
            background-position: bottom center;
            background-size: 100%;
            flex-direction: column;
            color: #fff;
            padding:1rem 0.625rem;
            .head-top{
                width: 100%;
                align-items: flex-start;
                position: relative;
                flex-shrink:0;
                .scan{
                    position: absolute;
                    right: 0;
                    top: 0;
                }
                .iconfont{
                    font-size: 1.375rem;
                    line-height: 1.5rem;
                }
                .user-btn{
                    position: relative;
                    input{
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                    }
                }
                .fangkuai{
                    height: 5rem;
                }
                .lingxing{
                    margin: 1.25rem auto 0;
                    background: #b8ab3f;
                    width: 2.75rem;
                    height: 2.75rem;
                    -moz-transform:rotate(45deg);
                    -webkit-transform:rotate(45deg);
                    -o-transform:rotate(45deg);
                    transform:rotate(45deg);
                }
            }
            .wallet-private_key{
                margin:0.625rem 0;
                text-align: center;
                line-height: 1rem;
                min-height: 1rem;
                word-wrap: break-word;
                width: 100%;
            }
        }
        .main_card{
            padding:2rem 0;
            width:100%;
            text-align:center;
            background:url("../../assets/images/main_bg.png");
            background-size: cover;
            line-height: 2rem;
            color:#fff;
        }
        .wallet_ico{ font-size:2rem;}
        .wallet_name{ font-size:1.5rem; }
        .wallet_address{ width:70%; overflow: hidden; margin: 0 auto;
            text-overflow:ellipsis;
            white-space: nowrap;
            font-size:.8rem;
        }
        .op_row{
            border-bottom:solid 1px #ebeef5;
        }
        .delete_wallet{
            font-size:.8rem!important;
            line-height: 3rem!important;
            width:90%;
        }
        .delete_box{
            position: fixed;
            bottom:0;
            width:100%;
            padding:20px 0;
        }
        .weui-btn{
            font-size:.8rem;
            line-height: 2rem;
        }
        .vux-popup-dialog{ background: #fff;}

    }
    .dcsy{
        
        .tip{
            line-height: 25px;
            padding:.8rem 1.5rem;
            background: -webkit-gradient(linear, left top, right top, from(#26cdcd), to(#5f5ece));
            background: linear-gradient(to right, #26cdcd, #5f5ece);
            color:#fff;
        }
        .export_show{ padding:2rem; background: #ccc; margin:2rem 3rem; line-height: 1.6rem; font-size:1rem; word-break: break-all; }
        .export_show_keystore{  padding:2rem; background: #ccc; margin:1.5rem 1.5rem; line-height: 1rem; font-size:.6rem; word-break: break-all; }
        .export_save{ 
                height: 3rem;
                margin:0px 3rem; 
                position: absolute;
                bottom: .8rem;
                left: 0;
                right: 0;
            }
        .export_title{ text-align: center; font-size: 16px; line-height: 35px;}
        .qrcode{ display: block; margin:3rem auto; text-align: center; }
    }

</style>


