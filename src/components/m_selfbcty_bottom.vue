<template>
  <div class="selfbcty-bottom">
    <box gap="0 35px" class="selfbcty-btn-box flex-box">
        <x-button type="primary" style="border-radius:99px;" link='/incharge' v-if="$store.state.coin_info.coin_type.id==0||$store.state.coin_info.coin_type.incharge_open">兑换</x-button>
        <x-button type="primary" style="border-radius:99px;background:#fc8c92;margin:0 0.625rem;" @click.native="toWithdraw()">上链</x-button>
        <x-button type="primary" style="border-radius:99px;background:#1ea0c2;">转出</x-button>
    </box>
  </div>
</template>

<script>
export default {
    components: {

    },
     data () {
        return {

        }
    },
    mounted () {

    },
    methods:{
        toWithdraw(){
            if(this.$store.state.coin_info && this.$store.state.coin_info.coin_type.id!=0){
              if(this.$store.state.coin_info.coin_type.withdraw_open==1){
                  this.$router.push({"path":"/user/withdrawlist"});
              }else{
                  this.$vux.toast.text("上链暂未开放");
              }
            }else {
              if(this.$store.state.init.withdraw_open==1){
                  this.$router.push({"path":"/user/withdrawlist"});
              }else{
                  this.$vux.toast.text("上链暂未开放");
              }
            }
        }
    }
}
</script>
<style lang="less">
    @import '../assets/css/variable.less';
    .selfbcty-bottom{
        height: 100%;
        .selfbcty-btn-box {
            height: 100%;
            justify-content: center;
            .weui-btn.weui-btn_primary {
                width: 100%;
                height: 2rem;
                line-height: 2rem;
                margin: 0;
                font-size: 1rem;
            }
        }
    }
</style>
