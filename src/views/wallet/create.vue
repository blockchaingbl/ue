<template lang="html">
    <div :id="page_name">
    <x-header :left-options="{showBack: true,backText:''}">{{page_title}}</x-header>

    <div class="tip">
        <ul class="tip_ul">
            <li>密码用于保护私钥和流通授权，强度非常重要</li>
            <li>钱包不存储密码，也无法帮您找回，请务必牢记</li>
        </ul>
    </div>

    <group>
        <x-input  placeholder="请输入钱包名称" :min="4" :max="16" v-model="name" ref="name" :required="true"></x-input>
        <x-input placeholder="请输入钱包密码" type="password" v-model="password" :min="4" :max="16" ref="password" :required="true"></x-input>
        <x-input  placeholder="再次输入钱包密码" type="password" v-model="confirm_password" :min="4" :max="16" :equal-with="password" ref="confirm_password" :required="true"></x-input>
    </group>
    <box gap="20px 10px">
        <x-button type="primary" style="border-radius:99px;height:2.375rem;font-size:0.875rem;" @click.native="createWallet">立即创建</x-button>
    </box>
    <box gap="20px 10px" style="text-align: center;">
        <router-link to="/wallet/import">导入钱包</router-link>
    </box>

        <popup v-model="seedShow" height="100%" v-transfer-dom class="create-pop">
            <x-header :left-options="{showBack: false}" class="tip_title">钱包创建成功 </x-header>
            <div class="tip">
                <div class="save_seed_title">请备份助记词</div>
                <ul>
                    <li>请抄写下您的钱包助记词</li>
                    <li>助记词用于恢复钱包或者重置密码</li>
                    <li>钱包不保存助记词信息，一旦丢失，您将无法再找回</li>
                </ul>
            </div>
            <div class="seed-box"  v-clipboard:copy="seed" v-clipboard:success="onCopy" v-clipboard:error="onError">
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
</template>
<script>
    import { TransferDom,XHeader,XInput,XButton,Box,AlertModule,Popup,XTextarea } from 'vux';
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
            "x-textarea":XTextarea
        },
        data () {
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                name:"",
                password:"",
                confirm_password:"",
                seedShow:false,
                seed:"",
                ifsuccess:false,
            }
        },
        mounted () {
        //console.log(XInput);
        //console.log(this.$refs.wallet_name);
        },
        beforeRouteLeave(to, from, next){//路由离开钩子
                if (to.name=="wallet"&& this.ifsuccess==true) {
                    this.refresh();
                }
                next();
                console.log(to);
                console.log(from);
                console.log(next);
        },
        methods: {  
            refresh(state,callback){
                // console.log(Vue);
                this.$store.state.balance_loading = true;
                this.$http.post('/api/app.wallet/propertydb/sum',{address:this.$store.state.wallet.address}).then(res => {
                    this.$store.state.properties = res.data.properties;
                    this.$store.state.balance_loading = false;
                    this.$store.state.properties_amount = 0;
                    for(var i in this.$store.state.properties)
                    {
                        this.$store.state.properties_set[this.$store.state.properties[i].type] = this.$store.state.properties[i];
                        if(this.$store.state.properties[i].amount>0)
                            this.$store.state.properties_amount += this.$store.state.properties[i].amount*this.$store.state.properties[i].price;
                    }
                    if(callback)
                        callback.call(null);
                }).catch(err => {
                    this.$store.state.balance_loading = false;
                    // if(err)
                    //     Vue.$vux.toast.text("网络超时，下拉重试");
                    if(callback)
                        callback.call(null);
                    console.log("err");
                    console.log(err);
                });
            },
            seedSave:function(){
                router.replace({path:"/wallet"});
            },
            createWallet:function(){
                this.ifsuccess=true;
                this.$refs.name.validate();
                this.$refs.password.validate();
                this.$refs.confirm_password.validate();
                if(this.$refs.name.valid&&this.$refs.password.valid&&this.$refs.confirm_password.valid)
                {

                    var loading = this.$loading({text:"创建中"});
                    var $vue = this;
                    setTimeout(function(){

                        $vue.seed = fanweEth.wallet.createSeed();
                        var _wallet = fanweEth.wallet.createBySeed($vue.seed,"",0);
                        var wallet = {};
                        wallet.name = $vue.name;
                        wallet.address = _wallet.address;
                        wallet.enc_privateKey = fanweCrypto.encrypt(_wallet.privatekey,$vue.password)
                        wallet.wallet_pwd = fanweCrypto.md5($vue.password);
                        $vue.$store.commit("addWallet",wallet);
                        $vue.$store.commit("loadProperties");

                        $vue.$http.post('/api/app.wallet/wallets/bind',{
                            name:wallet.name,
                            address:wallet.address,
                            enc_privatekey:wallet.enc_privateKey,
                            wallet_pwd:wallet.wallet_pwd
                        }).then(res => {
                            if(res.error>0){
                                $vue.$vux.toast.text(res.message);
                                console.log(res);
                            }else{
                                $vue.seedShow = true;
                                this.ifsuccess=true;
                            }
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
                    this.$refs.name.forceShowError = true;
                    this.$refs.password.forceShowError = true;
                    this.$refs.confirm_password.forceShowError = true;
                    if(!this.confirm_password)
                    {
                        this.$refs.confirm_password.errors.equal = '输入不一致';
                        this.$refs.confirm_password.getError();
                    }
                }
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
    #wallet_create{
        
            .tip{
                line-height: 25px;
                padding:.8rem 1.5rem;
                background: #2e68b3;
                color:#fff;
                .tip_ul{
                    li{
                        position: relative;
                        padding-left: 10px;
                        &::before{
                            position: absolute;
                            top: 10px;
                            content: '';
                            display: block;
                            width: 2px;
                            height: 2px;
                            border-radius: 2px;
                            border:1px solid #fff;
                            left: 0;
                        }
                    }
                }
            }
    }
    .tip{
        line-height: 25px;
        padding:.8rem 1.5rem;
        background: #2e68b3;
        color:#fff;
    }
    .vux-popup-dialog{ 
        background: #fff; 
    }
    .create-pop{
        .seed-box{
            padding:2rem 2rem 1rem; 
            background: #ccc; 
            margin:2rem 3rem; 
        }
        .seedShow{ 
            margin-bottom: 1rem;
            line-height: 2rem; 
            font-size:1rem;
        }
        p{
            line-height: 1rem;
            color: #628cf8;
            text-align: center;
        }
        .seedSave{ 
            margin:0px 3rem;
        }
        .save_seed_title{ 
            text-align: center; font-size: 16px; line-height: 35px;
        }
    }
</style>


