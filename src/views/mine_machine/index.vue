<template lang="html">
<div class="page-machine" >
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
        <div v-if="!loadings">
            <div class="machine-block" v-if="list.length>0">
                <div class="item-box" v-for="(item,index) in list"  :key="index">
                    <router-link v-show="item.status == 1" :to="'/mine_machine/detail?id='+item.id" class="item" :class="{'disabled':is_type}">
                        <div class="item-head flex-box vux-1px-b">
                            <div class="item-name flex-1">
                                <img src="@/assets/images/machine_icon.png" alt="" class="item-icon">{{item.name}}
                            </div>
                            <div class="item-price">行情：{{item.price}} {{$store.state.init.coin_uint}}</div>
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
                            <div class="item-item flex-1">
                                <div class="item-time"><em>{{item.expire_time}}</em>天</div>
                                <div class="item-decs">运行时间</div>
                            </div>
                            
                        </div>
                    </router-link>
                    <router-link v-show="item.status == 0" :to="''" class="item" :class="{'disabled':item.status == 0}">
                        <div class="item-head flex-box vux-1px-b">
                            <div class="item-name flex-1">
                                <img src="@/assets/images/machine_icon.png" alt="" class="item-icon">{{item.name}}
                            </div>
                            <div class="item-price">行情：{{item.price}} {{$store.state.init.coin_uint}}</div>
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
                            <div class="item-item flex-1">
                                <div class="item-time"><em>{{item.expire_time}}</em>天</div>
                                <div class="item-decs">运行时间</div>
                            </div>

                        </div>
                        <div class="item-type" v-if="is_type">
                            <span>已售罄</span>
                        </div>
                    </router-link>
                </div>
            </div>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>
        <div v-transfer-dom>
            <loading :show="loadings"></loading>
        </div>
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
            index: 0,
            getBarWidth: function (index) {
                return (index + 1) * 22 + 'px'
            },
            list:[],
            loadings:true,
            is_type:true,
        };
    },
    created(){
        this.$http.post('api/app.miner/miner/get_list',{}).then(res=>{
            this.list = res.data.miner_list;
            this.loadings=false;
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

.page-machine {
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
        .item-box{
            position: relative;
            margin-bottom: 0.625rem;
            .item-type {
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
                span{
                    display: block;
                    position: absolute;
                    bottom: 0.625rem;
                    right: 0.625rem;
                    width: 3.5rem;
                    height: 3.5rem;
                    border: 2px solid #ff6363;
                    line-height: 3.5rem;
                    border-radius: 2rem;
                    color: #ff6363;
                    text-align: center;
                    transform: rotate(-45deg);
                }
            }
        }
        .item {
            color: #22262d;
            background: #fff;
            display: block;
           
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
        .item.disabled{
            .item-con{
                &::after{
                    display: none;
                }
            }
        }
    }
}
</style>
