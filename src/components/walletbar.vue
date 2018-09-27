<template lang="html">
    <tabbar class="wallbar wallbargn">
        <tabbar-item class="tbleft" show-dot :link="{path:'/wallet/recieve/'+coin_type}">
            <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe615;</i><span>转入</span></span>
        </tabbar-item>
        <tabbar-item  class="tbright" :link="{path:'/wallet/send/'+coin_type}" style="line-height: 22px; font-size:22px;">
            <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe616;</i><span>转出</span></span>
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
#app > .weui-tabbar .weui-tabbar__item.vux-tabbar-simple{

    height: 3.5rem;
    line-height: 3.5rem;
}
    .wallbar{
        position: absolute;
        bottom:0;
        .weui-tabbar__label{
            color:#fff;
            span{
                justify-content: center;
            }
        }
        .wallbar-icon{
            font-size: 1.125rem;
            margin-right: 0.625rem;
        }
    }
</style>
