<template lang="html">
<div class="page-candy-success">
    <x-header :left-options="{showBack: false,backText:''}">
        <span v-if="sugar.sugar_type==0">发糖成功</span>
        <span v-else>转出成功</span>
    </x-header>
    <div class="candy-succ-con">
         <div class="success-head">
            <div class="head-icon iconfont">&#xe6f6;</div>
            <div class="head-title">发糖成功</div>
         </div>
        <group class="candy_success_block">
            <cell>
                <span slot="title" class="candy_success_title">资产类型</span>
                <span class="candy_success_value">{{coin_unit}}</span>
            </cell>
            <cell>
                <span slot="title" class="candy_success_title">发糖数量</span>
                <span class="candy_success_value">{{sugar.amount}}</span>
            </cell>
            <cell>
                <span slot="title" class="candy_success_title">锁仓时间</span>
                <span class="candy_success_value">{{sugar.lock_day}}天</span>
            </cell>
            <cell>
                <span slot="title" class="candy_success_title">每份数量</span>
                <span class="candy_success_value">{{sugar.copys_amount}} {{coin_unit}}</span>
            </cell>

        </group>
    </div>
    <box gap="0 0" style="text-align: center;" class="candy-succ-btn flex-box">
        <x-button type="primary" style="border-radius:99px;" class='withdraw-btn'  link="/candy/grant">再发一笔</x-button>
        <x-button type="primary" style="border-radius:99px;" class='withdraw-btn'  link="/mine/center">返回首页</x-button>
    </box>
</div>
</template>
<script>
export default {
    directives: {},
    components: {
    },
    data() {
        return {
            sugar:{},
            coin_unit:''
        };
    },
    created(){
        if(Object.keys(this.$route.params).length==0){
            this.$router.push({name:'userCenter'});
        }
        this.sugar = this.$route.params.sugar_info
        this.coin_unit = this.$route.params.coin_unit
    },
    methods: {

    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";

.page-candy-success {
    height: 100%;
    .candy-succ-con{
        overflow-y: scroll;
        position: absolute;
        width: 100%;
        left: 0;
        top: 2.875rem;
        right: 0;
        bottom: 3.5rem;
    }
   .success-head{
        background: #fff;
        padding: 1.875rem 0 1rem;
        margin-bottom: 0.625rem;
       .head-icon{
            margin: 0 auto 1rem;
            font-size: 3.65625rem;
            width: 3.65625rem;
            height: 3.65625rem;
            border-radius: 50%;
            background-color: #4887f6;
            color: #fff;
       }
       .head-title{
            font-size: 1.25rem;
            text-align: center;
            line-height: 2.8125rem;
            font-family: arial;
       }
   }
}
</style>
