<template lang="html">
<div class="page-machine-self" >
    <x-header class="machine_header" :left-options="{showBack: true,backText:''}">
      <div class="overwrite-title-demo" slot="overwrite-title">
        <tabbar v-model="index">
            <tabbar-item @on-item-click="linktomall" >
                <span slot="label">淘淘商城</span>
            </tabbar-item>
            <tabbar-item @on-item-click="linktomine" >
                <span slot="label">我的淘淘</span>
            </tabbar-item>
        </tabbar>
      </div>
    </x-header>
    <div class="content">
        <div class="machine-block" v-if="list.length>0">
            <router-link v-for="(item,index) in list"  :key="index"  :to="'/mine_machine/self_detail?id='+item.id" class="item">
                <div class="item-head flex-box vux-1px-b">
                    <div class="item-name flex-1">
                        <img src="@/assets/images/machine_icon.png" alt="" class="item-icon">{{item.name}}
                    </div>

                    <div class="item-price">累计收益：{{item.income}}{{item.coin_unit}}</div>
                </div>
                <div class="item-con flex-box">
                    <div class="item-item">
                        <div class="power">{{item.compute_power}}</div>
                        <div class="item-decs">算力</div>
                    </div>
                    <div class="item-item flex-2">
                        <div class="mine-numb">{{item.low}}-{{item.high}}/{{item.coin_unit}}</div>
                        <div class="item-decs">每天挖矿量</div>
                    </div>
                    <div class="item-item flex-1" v-show="item.status == 1">
                        <div class="item-time"><em>{{item.expire_time}}</em>天</div>
                        <div class="item-decs">剩余运行时间</div>
                    </div>
                    <div class="item-item flex-1" v-show="item.status == 2">
                        <div class="item-time"></div>
                        <div class="item-decs">已过期</div>
                    </div>
                </div>
            </router-link>
        </div>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
    </div>
    <div class="history-btn-box">
        <router-link :to="{path: '/mine_machine/history'}" class="history-btn">点击查看历史淘淘</router-link>
    </div>
</div>
</template>
<script>
import {Tab, TabItem ,Loading, TransferDomDirective as TransferDom,Radio,ButtonTab, ButtonTabItem } from 'vux';
import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
export default {
    directives: {
        TransferDom
    },
    components: {
        Tab,
        TabItem,
        Scroller,
        Nodata,
        Loading,
        ButtonTab,
        ButtonTabItem,
    },
    data() {
        return {
            loading:false,
            first_loading:true,
            index: 1,
            getBarWidth: function (index) {
                return (index + 1) * 22 + 'px'
            },
            list:[],
        };
    },
    created(){
        this.$http.post('api/app.miner/miner/miner_user_list',{}).then(res=>{
            if(!res.errcode){
                this.list = res.data.list;
            }
        })
    },
    mounted() {
        
    },
    methods: {
        linktomall(){
            this.$router.replace("/mine_machine");
        },
        linktomine(){
            this.$router.replace("/mine_machine/self");
        }
        
    },
    watch: {
        $route() {
            if (this.$route.name=='mineMachine') {
                this.index=0;
            }else if (this.$route.name=='selfMachine') {
                this.index=1;
            }
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";

.page-machine-self {
    min-height: 100%;
    padding-bottom: 1px;
    padding-top: 2.875rem;
    position: relative;
    .machine_header{
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .content{
        position: absolute;
        top: 2.875rem;
        left: 0;
        right: 0;
        bottom: 0;
        overflow-x: hidden;
        overflow-y: scroll;
        bottom: 2.625rem;
        .item {
            color: #22262d;
            background: #fff;
            display: block;
            margin-bottom: 0.625rem;
           
            .item-head {
                padding: 0 0.9375rem;
                line-height: 2.375rem;
                font-size: 0.875rem;
            }
            .item-name {
                position: relative;
                padding-left: 1.25rem;
            }
            .item-icon {
                position: absolute;
                width: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                left: 0;
            }
            .item-price {
                color: #999;
            }
            .item-con {
                padding: 0.9375rem;
                padding-right: 1.75rem;
                position: relative;
                &::after{
                    position: absolute;
                    display: block;
                    content: '\e8fa';
                    right: 0.75rem;
                    color: #bababa;
                    font-size: 0.875rem;
                    top: 50%;
                    transform: translateY(-50%);
                    font-family: 'iconfont';
                }
                .item-item{
                    text-align: center;
                    font-size: 0.75rem;
                    &:first-child{
                        text-align: left;
                        width: 4rem;
                        flex-shrink: 0;
                    }
                    
                    .power {
                        font-size: 1.25rem;
                        color: #2f82ff;
                        line-height: 1.625rem;
                    }
                    .item-decs {
                        font-size: 0.75rem;
                        color: #999;
                        line-height: 1.25rem;
                    }
                    .mine-numb {
                        font-size: 1.25rem;
                        line-height: 1.625rem;
                    }
                    .item-decs {
                        color: #999;
                        line-height: 1.25rem;
                    }
                    .item-time {
                        line-height: 1.625rem;
                        em{
                            font-size: 0.9375rem;
                        }
                    }
                    
                }
            }
        }
    }
    .history-btn-box{
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 2.625rem;
        line-height: 2.625rem;
        text-align: center;
        .history-btn{
            color: #999;
            
        }
    }
}
</style>
