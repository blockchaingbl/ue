<template lang="html">
<div class="page-machine-detail" >
    <div class="content">
        <div class="banner">
            <div class="banner-img">
                <img src="@/assets/images/machine_img.png" alt="">
            </div>
            <div class="name">{{info.name}}</div>
            <div class="banner-footer flex-box">
                <div class="banner-item flex-4">
                    <div class="item-title">运行时间</div>
                    <div class="item-numb">{{info.expire_time}} 天</div>
                </div>
                <div class="banner-item flex-3">
                    <div class="item-title">每日产量</div>
                    <div class="item-numb">{{info.low}}-{{info.high}}/{{info.coin_unit}}</div>
                </div>
                <div class="banner-item flex-3">
                    <div class="item-title">算力</div>
                    <div class="item-numb">{{info.compute_power}}</div>
                </div>
            </div>
        </div>
        <div class="detail-con">
            <div class="detail-title vux-1px-b">产品简介</div>
            <div class="detail-text" v-html="info.describe">
                {{info.describe}}
            </div>
        </div>
    </div>
    <box gap="0 0" class="buy-btn">
        <x-button type="primary" style="border-radius:0;font-size:0.875rem;height:100%" @click.native="buy()">立即受让:{{info.price}}&nbsp;{{$store.state.init.coin_uint}}</x-button>
    </box>
</div>
</template>
<script>
    import { PopupPicker } from 'vux'
    import router from "@/router";
export default {
    components: {
        
    },
    data() {
        return {
            info:[],
            id:null,
        };
    },
    created(){

        this.$http.post('api/app.miner/miner/miner_info',{miner_id:router.currentRoute.query.id}).then(res=>{
            if(!res.errcode){
                this.info = res.data.miner_info;
            }
        });
        this.id = router.currentRoute.query.id;
    },
    mounted() {
        
    },
    methods: {
        buy(){
            let _this = this;
            this.$vux.confirm.show({
                title: '您确定要使用'+_this.info.price+' '+_this.$store.state.init.coin_uint+'来受让'+_this.info.name+'？',
                onCancel(){},
                onConfirm(){
                    _this.$http.post('api/app.miner/miner/miner_buy',{miner_id:_this.id}).then(res=>{
                        _this.$vux.toast.text(res.message);
                        if(!res.errcode){
                            setTimeout(function () {
                                router.push("/mine_machine/self");
                            },1000)
                        }
                    }).catch(res=>{
                        _this.$vux.toast.text(res.message);
                    })
                }
            })

        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";

.page-machine-detail {
    min-height: 100%;
    padding-bottom: 1px;
    background: #fff;
    position: relative;
    .content{
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 2.625rem;
        overflow-x: hidden;
        overflow-y: scroll;
    }
    .banner{
        background: url(../../assets/images/machine_detail.jpg) no-repeat top center;
        background-size: 100% 100%;
        padding: 0.9375rem 0 0.5rem;
        color: #fff;
        .banner-img {
            width: 2.375rem;
            height: 2.375rem;
            margin: 0 auto;
            img {
                width: 100%;
                height: 100%;
                display: block;
            }
        }
        .name {
            text-align: center;
            line-height: 2.375rem;
            font-size: 1.25rem;
            color: #fff;
        }
        .banner-footer {
            margin-top: 0.625rem;
            .banner-item {
                line-height: 1.25rem;
                text-align: center;
            }
            .item-title{
                font-size: 0.75rem;
                color: #fff;
            }
            .item-numb{
                font-size: 1rem;
            }
        }
    }
    .detail-title {
        line-height: 2.5rem;
        padding: 0 0.9375rem;
        color: #666;
    }
    .detail-text {
        padding: 0.5rem 0.9375rem;
        p {
            line-height: 1.375rem;
            font-size: 0.875rem;
            color: #22262d;
            margin-bottom: 1rem;
        }
    }
    .buy-btn{
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2.625rem;
    }
}
</style>
