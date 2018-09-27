<template lang="html">
<div class="page-candy-success">
    <x-header :left-options="{showBack: false,backText:''}">
        <span>转出成功</span>
    </x-header>
    <div class="candy-succ-con">
         <div class="success-head">
            <div class="head-icon iconfont">&#xe6f6;</div>
             <div class="head-title">转出成功</div>
         </div>
        <group class="candy_success_block">
            <cell>
                <span slot="title" class="candy_success_title">转出数额</span>
                <span class="candy_success_value">{{transfer.amount}} {{$store.state.init.coin_uint}}</span>
            </cell>
            <cell>
                <span slot="title" class="candy_success_title">到账数额</span>
                <span class="candy_success_value">{{transfer.receive}} {{$store.state.init.coin_uint}}</span>
            </cell>
            <cell>
                <span slot="title" class="candy_success_title">转入人</span>
                <span class="candy_success_value">{{transfer.to_user}}</span>
            </cell>
        </group>
    </div>
    <box gap="0 0" style="text-align: center;" class="candy-succ-btn flex-box">
        <x-button type="primary" style="border-radius:99px;" class='withdraw-btn'  :link="one_more_link">再转一笔</x-button>
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
            transfer:{},
            coin_unit:'',
            one_more_link:''
        };
    },
    created(){
        console.log(this.$route.params)
        // if(Object.keys(this.$route.params).length==0){
        //     this.$router.push({name:'userCenter'});
        // }
        this.transfer = this.$route.params
        this.one_more_link = `/transfer/send/${this.transfer.to}`
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
