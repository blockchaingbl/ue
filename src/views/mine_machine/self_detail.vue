<template lang="html">
<div class="page-self-detail" >
    <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">{{info.name}}</x-header>
    <div class="content">
        <div class="banner">
            <div class="banner-title">累计收益</div>
            <div class="banner-profit">{{info.income}} {{info.coin_unit}}</div>
            <div class="banner-footer flex-box">
                <div class="banner-item">
                    <div class="item-title">剩余期限（天）</div>
                    <div class="item-numb">{{info.expire_day}}</div>
                </div>
                <div class="banner-item flex-1">
                    <div class="item-title">算力</div>
                    <div class="item-numb">{{info.cp_total}}</div>
                </div>
                <div class="banner-item flex-2">
                    <div class="item-title">每日产量</div>
                    <div class="item-numb">{{info.low}}-{{info.high}}/{{info.coin_unit}}</div>
                </div>
                <div class="banner-item">
                    <div class="item-title">到期时间</div>
                    <div class="item-numb">{{info.expire_date}}</div>
                </div>
            </div>
        </div>
        <div class="detail-con">
            <div class="detail-title vux-1px-b">历史收益</div>
            <div class="profit-block">
                <div class="profit-item vux-1px-b" v-for="(item,index) in list"  :key="index">
                    <div class="profit-item-text flex-box">
                        <div class="text-title flex-1">收益</div>
                        <div class="profit-numb">+{{item.amount}} {{item.coin_type}}</div>
                    </div>
                    <div class="profit-item-time">{{item.create_time}}</div>
                </div>
            </div>
        </div>
    </div>
    <box gap="0 0" class="buy-btn">
        <!--<x-button type="primary" style="border-radius:0;font-size:0.875rem;height:100%" @click.native="fetch()">收取淘淘收益</x-button>-->
    </box>
</div>
</template>
<script>
import router from "@/router";
export default {
    components: {
        
    },
    data() {
        return {
            id:'',
            info:[],
            list:[],
            un_mined:0
        };
    },
    created(){
        this.id = router.currentRoute.query.id;
        this.getUserMiner();
        this.getMinerMined();
    },
    mounted() {
        
    },
    methods: {
        fetch(){
            this.$http.post('/api/app.miner/miner/fetch',{user_miner_id:this.id}).then(res => {
                this.$vux.toast.text("领取成功");
                this.getMinerMined();
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        getUserMiner(){
            this.$http.post('/api/app.miner/miner/user_miner_detail',{user_miner_id:this.id}).then(res => {
                this.info = res.data.info;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        getMinerMined(){
            this.$http.post('/api/app.miner/miner/mined',{user_miner_id:this.id}).then(res => {
                this.list = res.data.log;
                this.un_mined = res.data.un_mined;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";

.page-self-detail {
    min-height: 100%;
    padding-bottom: 1px;
    background: #fff;
    position: relative;
    .content{
        position: absolute;
        top: 2.875rem;
        left: 0;
        right: 0;
        bottom: 2.625rem;
        overflow-x: hidden;
        overflow-y: scroll;
    }
    .banner{
        background: url(../../assets/images/machine_detail.jpg) no-repeat top center;
        background-size: 100% 100%;
        padding: 0.9375rem 0.9375rem 0.5rem;
        color: #fff;
        
        .banner-title{
            text-align: center;
            font-size: 0.75rem;
            color: #ffffff;
            line-height: 1.125rem;
        }
        .banner-profit {
            text-align: center;
            line-height: 3.125rem;
            font-size: 1.875rem;
            color: #fff;
        }
        .banner-footer {
            margin-top: 1.125rem;
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
    .profit-block {
        padding-left: 0.9375rem;
        .profit-item {
            padding-right: 0.9375rem;
            padding-top: 0.625rem;
            padding-bottom: 0.75rem;
        }
        .profit-item-text {
            line-height: 1.375rem;
            font-size: 0.9375rem;
        }
        .profit-item-time {
            font-size: 0.75rem;
            line-height: 1.125rem;
            color: #999;
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
