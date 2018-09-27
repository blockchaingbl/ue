<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:''}" :right-options="{showMore: false}" class="inter_wallet_header">{{page_title}}</x-header>
        <!-- <div class="main_card">
            <div class="wallet_iocn"> <img src="@/assets/images/walleticon.png" alt=""></div>
            <div class="wallet_name">{{wallet.name}}</div>
            <div class="wallet_address"  v-clipboard:copy="wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{wallet.address}}</div>
        </div> -->
        <div class="wallet-card-box">
                <div class="wallet-card">
                    <div class="wallet-info flex-box">
                        <div class="wallet-img">
                            <img src="@/assets/images/act_wallet_icon.png" alt="">
                        </div>
                        <div class="wallet-info-text flex-1">
                            <div class="wall-name">{{wallet.name}}</div>
                            <div class="wallet-address" v-clipboard:copy="wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(wallet.address,12,20,'...')}} <i class="iconfont">&#xe71f;</i></div>
                        </div>
                    </div>
                </div>
            </div>
        <group>
            <div  v-on:click="changeName" class="op_row">
                <cell is-link>
                    <span slot="title">钱包名</span>
                    <span slot>{{wallet.name}}</span>
                </cell>
            </div>
            <div  v-on:click="changePassword">
                <cell is-link>
                    <span slot="title">修改密码</span>
                </cell>
            </div>
        </group>

        <group>
            <div  v-on:click="exportPrivate" class="op_row">
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

        <box class="delete_box">
            <x-button @click.native="deleteWallet" class="delete_wallet">删除钱包</x-button>
        </box>


        <popup v-model="showChangeName"  v-transfer-dom>
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
                <group class="nobg">
                    <x-button type="primary" @click.native="doChangeName">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group>
            </box>
        </popup>

        <popup v-model="showChangePassword" v-transfer-dom>
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
                <group class="nobg">
                    <x-button type="primary" @click.native="doChangePassword">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group>
            </box>
        </popup>

        <popup v-model="showNeedPassword" v-transfer-dom>
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
                <group class="nobg">
                    <x-button type="primary" @click.native="doNeedPassword">确认</x-button>
                    <x-button type="primary" @click.native="cancelPop">取消</x-button>
                </group>
            </box>
        </popup>


        <popup v-model="showExportPrivate" height="100%" v-transfer-dom>
            <x-header :left-options="{showBack: false}" class="tip_title">私钥导出成功 </x-header>
            <div class="tip">
                <div class="export_title">私钥不可更改，请妥善保存你的私钥</div>
            </div>
            <div class="export_show" v-clipboard:copy="export_private_key" v-clipboard:success="onCopy" v-clipboard:error="onError">
                {{export_private_key}}
                <span class="copyd">点击复制</span>
            </div>

            <qrcode :value="export_private_key" class="qrcode"></qrcode>

            <box class="export_save">
                <x-button type="primary" @click.native="cancelPop">已保存好</x-button>
            </box>
        </popup>

        <popup v-model="showExportKeystore" height="100%" v-transfer-dom>
            <x-header :left-options="{showBack: false}" class="tip_title">Keystore导出成功 </x-header>
            <div class="tip">
                <div class="export_title">请妥善保存你的Keystore</div>
            </div>
            <div class="export_show_keystore" v-clipboard:copy="export_keystore" v-clipboard:success="onCopy" v-clipboard:error="onError">
                {{export_keystore}}
                <span class="copyd">点击复制</span>
            </div>

            <qrcode :value="export_keystore" class="qrcode"></qrcode>

            <box class="export_save">
                <x-button type="primary" @click.native="cancelPop">已保存好</x-button>
            </box>
        </popup>

    </div>
</template>
<script>
    import { TransferDom,XHeader,Actionsheet,Cell,Badge,XButton,Box,PopupHeader,Popup,AlertModule,Qrcode  } from 'vux';

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
            "qrcode":Qrcode
        },
        data () {
            var current_address = router.currentRoute.params['address'];
            var wallet = this.$store.state.wallets[current_address];
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                wallet:wallet,
                showChangeName:false,
                inputWalletName:wallet.name,
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
        },
        methods: {
            changeName:function(){
                this.showChangeName = true;
            },
            doChangeName:function(){
                this.$refs.wallet_name.validate();
                if(!this.$refs.wallet_name.valid)
                {
                    this.$refs.wallet_name.forceShowError = true;
                }
                else
                {
                    this.wallet.name = this.inputWalletName;
                    this.$store.commit("updateWallet",this.wallet);
                    this.$http.post('/api/app.wallet/wallets/setname',{
                        address:this.wallet.address,
                        name:this.inputWalletName
                    }).then(res => {
                        if(res.error>0)
                        {
                            this.$vux.toast.text(res.message);
                            console.log(res);
                        }else{
                            this.cancelPop();
                            AlertModule.show({
                                title: '提示',
                                content: "钱包名称修改成功",
                                onShow () {

                                },
                                onHide () {

                                }
                            });
                        }
                    }).catch(err => {
                        console.log(err);
                        if (err.errcode) {
                            $vue.$vux.toast.text(err.message);
                        }
                        //  this.Toast(err || '网络异常，请求失败');
                    });
                }
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
                    else
                    {
                        this.wallet.enc_privateKey = fanweCrypto.encrypt(privateKey,this.password);
                        this.$store.commit("updateWallet",this.wallet);

                        this.$http.post('/api/app.wallet/wallets/setpassword',{
                            address:this.wallet.address,
                            enc_privatekey:this.wallet.enc_privateKey
                        }).then(res => {
                            if(res.error>0)
                            {
                                this.$vux.toast.text(res.message);
                                console.log(res);
                            }else{
                                this.cancelPop();
                                this.old_password = "";
                                this.password = "";
                                this.confirm_password = "";
                                AlertModule.show({
                                    title: '提示',
                                    content: "新密码设置成功",
                                    onShow () {
                                    },
                                    onHide () {

                                    }
                                });
                            }
                        }).catch(err => {
                            console.log(err);
                            if (err.errcode) {
                                this.$vux.toast.text(err.message);
                            }
                            //  this.Toast(err || '网络异常，请求失败');
                        });
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
                    var $vue = this;
                    if($vue.needPasswordFor!="delete_wallet"){
                        var loading = this.$loading({text:"导出中"});
                    }
                    setTimeout(function(){
                        if($vue.needPasswordFor=="export_private")
                        {
                            $vue.export_private_key = privateKey;
                            $vue.showExportPrivate = true;
                        }
                        else if($vue.needPasswordFor=="export_keystore")
                        {
                            $vue.export_keystore = fanweEth.wallet.privatekeyToKeystore(privateKey,$vue.need_password);
                            $vue.showExportKeystore = true;
                        }
                        else
                        {
                            $vue.$store.commit("deleteWallet",$vue.wallet);
                            for(var address in $vue.$store.state.wallets);
                            if(address)
                                $vue.$store.commit("changeWallet",address);

                            $vue.$http.post('/api/app.wallet/wallets/delete',{
                                address:$vue.wallet.address,
                            }).then(res => {
                                if(res.error>0)
                                {
                                    $vue.$vux.toast.text(res.message);
                                    console.log(res);
                                }else{
                                    router.replace({path:"/wallet_international"});
                                }
                            }).catch(err => {
                                console.log(err);
                                if (err.errcode) {
                                    $vue.$vux.toast.text(err.message);
                                }
                                //  this.Toast(err || '网络异常，请求失败');
                            });
                        }
                        $vue.showNeedPassword = false;
                        $vue.need_password = "";
                        if($vue.needPasswordFor!="delete_wallet"){
                            loading.close();
                        }
                    },500);
                }
            },
            deleteWallet:function(){
                var $vue = this;
                $vue.needPasswordFor = "delete_wallet";
                $vue.showNeedPassword = true;
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
    #wallet_international_manage_item{
    .main_card{
        padding:1rem 0;
        width:100%;
        text-align:center;
        background-image: url("../../assets/images/walletbg.png");
        background-size: 100%;
        background-repeat: no-repeat;
        background-position: center bottom;
        background-color: #2e3d6c;
        line-height: 2rem;
        color:#fff;
    }
    .wallet-card-box {
        background: #5871ff;
        padding: 0.625rem 0.9375rem;
        margin-bottom: 2.5rem;
        .wallet-card {
            height: 4.5rem;
            margin-bottom: -2.3125rem;
            position: relative;
            z-index: 1;
            background: #fff;
            box-shadow: 0 5px 10px 0 rgba(0,0,0,0.05);
            border-radius: 4px;
            padding: 1rem 0.9375rem 0.6875rem;
        }
        .wallet-info {
            margin-bottom: 0.875rem;
            .wallet-img {
                width: 2.25rem;
                height: 2.25rem;
                margin-right: 0.625rem;
                img {
                    display: block;
                    height: 100%;
                    width: 100%;
                }
            }
            .wall-name {
                font-size: 1.125rem;
                line-height: 1.5rem;
            }
            .wallet-address {
                font-size: 0.75rem;
                line-height: 1rem;
                color: #858e97;
                .iconfont{
                    font-size: 0.75rem;
                }
            }
        }
        .wallet-assets {
            font-size: 1.875rem;
            text-align: right;
            em {
                font-size: 0.875rem;
                line-height: 0.875rem;
            }
            
        }
    }
    .wallet_iocn{ 
          width:2.25rem;
          margin:0 auto;
          img{
              width: 100%;
          }
        }
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
}
    .vux-popup-dialog{ background: #fff;}

    .tip{
        line-height: 25px;
        padding:.8rem 1.5rem;
        background: #4f95ff;
        color:#fff;
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
    .export_show{position: relative; padding:2rem; background: #ccc; margin:2rem 3rem; line-height: 1.6rem; font-size:1rem; word-break: break-all; }
    .export_show_keystore{ position: relative; padding:2rem 1rem 1rem; background: #ccc; margin:1.5rem 1.5rem; line-height: 1rem; font-size:.6rem; word-break: break-all; }
    .export_save{ margin:0px 3rem;}
    .export_title{ text-align: center; font-size: 16px; line-height: 35px;}
    .qrcode{ display: block; margin:3rem auto; text-align: center; }


</style>


