<template lang="html">
    <tabbar class="wallbar">
        <tabbar-item class="tbright" show-dot link="/user/wallet/recieve">
            <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe708;</i><span>接收</span></span>
        </tabbar-item>
        <tabbar-item  class="tbleft" link="/user/wallet/send" style="line-height: 22px; font-size:22px;">
            <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe709;</i><span>发送</span></span>
        </tabbar-item>
    </tabbar>
</template>

<script>
import  {Tabbar, TabbarItem} from 'vux';
import router from "@/router";
export default {
    components: {
        "tabbar":Tabbar,
        "tabbar-item":TabbarItem
    },data () {
        var coin_amount = 0;
        
        var coin_type = "ethereum";
        if(router.currentRoute.params['coin'])
        {
            coin_type = router.currentRoute.params['coin'];
        }
        return {
            coin_type:coin_type
        }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="less" scoped>
    .wallbar{
        position: fixed;
        bottom:0;
        .weui-tabbar__label{
            color:#fff;
            span{
                justify-content: center;
            }
        }
        .wallbar-icon{
            font-size: 1.0625rem;
            margin-right: 0.625rem;
        }
    }
</style>
