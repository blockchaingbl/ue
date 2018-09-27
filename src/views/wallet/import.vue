<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:''}">{{page_title}}</x-header>
        <tab v-model="tab_index">
            <tab-item >助记词</tab-item>
            <tab-item >官方钱包</tab-item>
            <tab-item >私钥</tab-item>
        </tab>
        <box v-show="tab_index==0">
            <group>
                <x-textarea placeholder="助记词，按空格分隔" v-model="seed" :show-counter="false" ref="seed"></x-textarea>
                <x-input  title="助记词格式 m/44'/60'/0'/0/" label-width="10.6rem" placeholder="0" v-model="seed_index" type="number" ref="seed_index" ></x-input>
            </group>
        </box>
        <box v-show="tab_index==1">
            <group>
            <x-textarea placeholder="keystore 文本内容" v-model="keystore" :show-counter="false" ref="keystore"></x-textarea>
            <x-input  placeholder="keystore 密码" type="password" v-model="keystore_password" :min="4" :max="16" ref="keystore_password" :required="true"></x-input>
            </group>
        </box>
        <box v-show="tab_index==2">
            <group>
                <x-textarea placeholder="明文私钥" v-model="private_key" :show-counter="false" ref="private_key" :required="true"></x-textarea>
            </group>
        </box>

        <group title="钱包名称">
            <x-input  placeholder="用于标识您的钱包" :min="4" :max="16" v-model="name" ref="name" :required="true"></x-input>
        </group>
        <group title="请输入密码">
            <x-input  placeholder="钱包密码" type="password" v-model="password" :min="4" :max="16" ref="password" :required="true"></x-input>
            <x-input  placeholder="再次输入钱包密码" type="password" v-model="confirm_password" :min="4" :max="16" :equal-with="password" ref="confirm_password" :required="true"></x-input>
        </group>
        <box gap="20px 10px">
            <x-button type="primary" style="border-radius:99px;" @click.native="importWallet">立即导入</x-button>
        </box>
        <box gap="20px 10px" style="text-align: center;">
            <router-link to="/wallet/create">创建钱包</router-link>
        </box>
    </div>
</template>
<script>
    import { XHeader,XInput,XButton,Box,AlertModule,Tab,TabItem,XTextarea,Loading } from 'vux';
    import  { LoadingPlugin } from 'vux'
    import router from '@/router';
    import JSON from "JSON";
    import fanweEth from "@/ethereum";
    import fanweCrypto from '@/crypto';
    export default {
        components: {
            "x-header":XHeader,
            "x-input":XInput,
            "x-button":XButton,
            "box":Box,
            "tab":Tab,
            "tab-item":TabItem,
            "x-textarea":XTextarea,
            "loading":Loading
        },
        data () {
            console.log(router.currentRoute);
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                name:"",
                password:"",
                confirm_password:"",
                seed:"",
                seed_index:0,
                keystore:"",
                keystore_password:"",
                private_key:"",
                tab_index:0,
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
            importWallet:function(){
                var valid = true;
                this.$refs.name.validate();
                this.$refs.password.validate();
                this.$refs.confirm_password.validate();
                if(!this.$refs.name.valid||!this.$refs.password.valid||!this.$refs.confirm_password.valid)
                {
                    this.$refs.name.forceShowError = true;
                    this.$refs.password.forceShowError = true;
                    this.$refs.confirm_password.forceShowError = true;
                    valid = false;
                }
                switch(this.tab_index){
                    case 0:
                        this.$refs.seed_index.validate();
                        if(!this.$refs.seed_index.valid)
                        {
                            this.$refs.seed_index.forceShowError = true;
                            valid = false;
                        }
                        if($.trim(this.seed)=="")
                        {
                            valid = false;
                            AlertModule.show({
                                title: '提示',
                                content: "助记词不能为空",
                                onShow () {
                                },
                                onHide () {

                                }
                            })
                        }
                        break;
                    case 1:
                        this.$refs.keystore_password.validate();
                        if(!this.$refs.keystore_password.valid)
                        {
                            this.$refs.keystore_password.forceShowError = true;
                            valid = false;
                        }
                        if($.trim(this.keystore)=="")
                        {
                            valid = false;
                            AlertModule.show({
                                title: '提示',
                                content: "keystore 不能为空",
                                onShow () {
                                },
                                onHide () {

                                }
                            })
                        }
                        break;
                    case 2:
                        if($.trim(this.private_key)=="")
                        {
                            AlertModule.show({
                                title: '提示',
                                content: "私钥不能为空",
                                onShow () {
                                },
                                onHide () {

                                }
                            })
                            valid = false;
                        }
                        break;
                }


                if(valid)
                {
                    var loading = this.$loading({text:"正在导入"});
                    var $vue = this;
                    setTimeout(function(){
                        try{
                            var _wallet;
                            switch($vue.tab_index){
                                case 0:
                                    _wallet = fanweEth.wallet.createBySeed($.trim($vue.seed),"",$vue.seed_index);
                                    break;
                                case 1:
                                    _wallet = fanweEth.wallet.createByKeystore($.trim($vue.keystore),$vue.keystore_password);
                                    break;
                                case 2:
                                    _wallet = fanweEth.wallet.createByPrivate($.trim($vue.private_key));
                                    break;
                            }
                            var wallet = {};
                            wallet.name = $vue.name;
                            wallet.address = _wallet.address;
                            wallet.enc_privateKey = fanweCrypto.encrypt(_wallet.privatekey,$vue.password);
                            wallet.wallet_pwd = fanweCrypto.md5($vue.password);
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
                                    $vue.$store.commit("addWallet",wallet);
                                    $vue.$store.commit("loadProperties");
                                    AlertModule.show({
                                        title: '提示',
                                        content: "钱包导入成功",
                                        onShow () {
                                        },
                                        onHide () {

                                            router.replace("/wallet");
                                        }
                                    })
                                }
                            }).catch(err => {
                                console.log(err);
                                if (err.errcode) {
                                    $vue.$vux.toast.text(err.message);
                                }
                                //  this.Toast(err || '网络异常，请求失败');
                            });

                        }catch(ex)
                        {
                            console.log(ex);
                            AlertModule.show({
                                title: '提示',
                                content: "导入失败，请核查您导入的资料",
                                onShow () {
                                },
                                onHide () {

                                }
                            })
                        }

                        loading.close();
                    },500);

                }


            }
        }
    }
</script>
<style lang="less" scoped>
    #wallet_import{
        .vux-x-input,.vux-x-textarea{ font-size:.8rem;}
    }
</style>


