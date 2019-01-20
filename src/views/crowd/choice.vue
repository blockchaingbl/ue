<template lang="html">
    <div class="page-choice">
        <div class="find-box">
            <div class="find-block flex-box">
                <router-link class="item flex-box" :to="{path:'/zc'}">
                    <div class="user_icon">
                        <img src="@/assets/images/zc_icon.png" alt="">
                    </div>
                    <div class="item-text">筹宝</div>
                </router-link>
                <router-link class="item flex-box" :to="{path:'/crowd_buy'}">
                    <div class="user_icon">
                        <img src="@/assets/images/lottery.png" alt="">
                    </div>
                    <div class="item-text">中奖购</div>
                </router-link>
                <!--<div class="item flex-box" @click="tis_btn">-->
                    <!--<div class="user_icon">-->
                        <!--<img src="@/assets/images/liutong.png" alt="">-->
                    <!--</div>-->
                    <!--<div class="item-text">令牌流通</div>-->
                <!--</div>-->
                <router-link class="item flex-box" :to="{path:'/token_otc'}">
                    <div class="user_icon">
                        <img src="@/assets/images/liutong.png" alt="">
                    </div>
                    <div class="item-text">令牌流通</div>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        directives: {

        },
        mounted() {
        },
        methods: {
          tis_btn(){
            this.$vux.toast.text('正在努力开发中');
          },
        },
    };
</script>
<style lang="less" scoped>
    @import "../../assets/css/variable.less";
    .page-choice{
        .find-box {
            background: #fff;
            margin-top: 0.625rem;
            &:first-child{
                margin: 0;
            }
            .title {
                padding: 0 0.9375rem;
                line-height: 2.5rem;
                font-size: 0.875rem;
                position: relative;
                &::after{
                    position: absolute;
                    content: '';
                    bottom: -1px;
                    display: block;
                    width: 100%;
                    height: 1px;
                    background: #eee;
                    left: 0;
                }
            }
            .find-block {
                flex-wrap: wrap;
                border-bottom: 1px solid #eee;
                .item {
                    width: 33.3333%;
                    height: 6.75rem;
                    color: #363840;
                    flex-direction: column;
                    justify-content: center;
                    border-right: 1px solid #eee;
                    border-bottom: 1px solid #eee;
                    &:nth-child(3n+3) {
                        border-right: none;
                    }
                    .user_icon {
                        width: 3.0625rem;
                        height: 3.0625rem;
                        margin: 0 auto 0.5rem;
                        img{
                            display: block;
                            width: 100%;
                            height: 100%;
                        }
                    }
                    .item-num{
                        color: #999999;
                        font-size: 12px;
                        padding-top: 2px;
                    }
                }
            }
        }
    }

</style>
