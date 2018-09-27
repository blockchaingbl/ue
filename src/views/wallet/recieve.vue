<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:''}" :right-options="{showMore: false}">{{page_title}}</x-header>
        <div class="main_card">
            <div class="wallet_amount">{{coin_amount}}</div>
            <div class="wallet_name">{{this.$store.state.wallet.name}}</div>
            <div class="wallet_address" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(this.$store.state.wallet.address,12,20,'...')}} <i class="iconfont">&#xe64b;</i></div>
        </div>
        <div style="text-align: center;">
            <group class="recieve_amount">
                <x-input title="转入数额" @on-change="changeAmount" v-model="amount" type="number" :required="true" :is-type="checkAmount"></x-input>
            </group>
            <qrcode :value="qr_src" class="qrcode"></qrcode>
        </div>
    </div>
</template>
<script>
    import { XHeader,XInput,Qrcode} from 'vux';
    import router from '@/router';
    export default {
        components: {
            "x-header":XHeader,
            "x-input":XInput,
            "qrcode":Qrcode
        },
        data () {
            var coin_amount = 0;
            var coin_type = "ethereum";
            if(router.currentRoute.params['coin'])
            {
                coin_type = router.currentRoute.params['coin'];
            }
            if(this.$store.state.properties.length>0)
            {
                coin_amount = this.$store.state.properties_set[coin_type].name +" "+this.$store.state.properties_set[coin_type].amount;
            }
            else
            {
                var $vue = this;
                this.$store.commit("loadProperties",function(){
                    $vue.coin_amount = $vue.$store.state.properties_set[coin_type].name +" "+$vue.$store.state.properties_set[coin_type].amount;
                });
            }
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                coin_amount:coin_amount,
                qr_src:"",
                amount:0,
                coin_type:coin_type
            }
        },
        mounted () {
            if(!this.$store.state.page_loading&&!this.$store.state.config)
            {
                this.$store.state.page_loading = true;
                this.$http.post('/api/app.wallet/init',{}).then(res => {
                    this.$store.state.page_loading = false;
                    this.$store.commit("updateConfig",res.data.config);
                    this.qr_src = this.$store.state.config.RecieveUrlBase+this.coin_type+"?address="+this.$store.state.wallet.address+"&amount="+this.amount+"&api=1";
                    console.log(this.$store.state.config.RecieveUrlBase);
                }).catch(err => {
                    this.$store.state.page_loading = false;
                    console.log(err);
                });
            }
        },
        methods: {
            changeAmount:function(){
                var coin_type = "ethereum";
                if(router.currentRoute.params['coin'])
                {
                    coin_type = router.currentRoute.params['coin'];
                }
                var amount = 0;
                if(this.amount>0)
                    amount = parseFloat(this.amount);
                this.qr_src = this.$store.state.config.RecieveUrlBase+coin_type+"?address="+this.$store.state.wallet.address+"&amount="+amount+"&api=1";
                console.log(this.qr_src);
            },
            checkAmount:function(value){
                return {
                    valid: value > 0,
                    msg: '数额必须大于零'
                }
            },
            onCopy: function (e) {
            this.$vux.toast.text("复制成功");
            //console.log('You just copied: ' + e.text)
        },
        onError: function (e) {
            this.$vux.toast.text("复制失败，您可以尝试手动记录");
            //console.log('Failed to copy texts')
        }
        }
    }
</script>
<style lang="less" scoped>
    #wallet_recieve{
        .main_card{
            padding:1rem 0;
            width:100%;
            text-align:center;
            background-image:url("../../assets/images/walletbg.png");
            background-size: 100%;
            background-color: #2e3d6c;
            background-position: center top;
            background-repeat: no-repeat;
            line-height: 2rem;
            color:#fff;
        }
        .wallet_amount{ font-size:1.2rem;}
        .wallet_name{ font-size:1.5rem; }
        .wallet_address{  overflow: hidden; margin: 0 auto;
            word-break: break-all;
            font-size:.8rem;
        }
        .qrcode{ display: block; margin-top:3rem;}
        .recieve_amount{ font-size:.8rem; color:#333333; margin-top: 3rem; margin: 3rem 3rem;}
    }
</style>


