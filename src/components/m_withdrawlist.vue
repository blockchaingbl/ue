<template>
  <div class="withdraw-bottom ">
        <box gap="0 35px" class="withdraw-btn-box  flex-box">
            <x-button type="primary" style="border-radius:99px;" class='withdraw-btn'  @click.native="goWithdraw">我要上链</x-button>
        </box>
  </div>
</template>

<script>
export default {
    components: {

    },
     data () {
        return {
            has_wallet:0
        }
    },
    mounted () {
        if(!this.$store.state.wallets){
            this.getUserinfo()
        }
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.has_wallet=res.data.account_info.has_wallet;
            }).catch(err => {
                this.$vux.toast.text(err.message);
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        goWithdraw(){
            if(this.$store.state.wallets || this.has_wallet){
                this.$router.push({path:'/user/withdraw'})
            }else{
                console.log(this.has_wallet)
                this.$vux.toast.text('请先创建钱包')
            }
        }
    }
}
</script>
<style lang="less">
    @import '../assets/css/variable.less';
    .withdraw-bottom{
        height: 100%;
        .withdraw-btn-box {
            height: 100%;
            justify-content: space-between;
			.withdraw-btn{
	            background-color: #fc8c92;
	            font-size: 0.9375rem;
	            height: 2.4375rem;
	            line-height: 2.4375rem;
	        }
        }
    }
</style>
