<template lang="html">
<div class="page-machine-history" >
    <div class="content">
        <div class="machine-block" v-if="list.length>0">
            <div class="item" v-for="(item,index) in list"  :key="index">
                <div class="item-head flex-box vux-1px-b">
                    <div class="item-name flex-1">
                        <img src="@/assets/images/machine_icon.png" alt="" class="item-icon">3星淘淘
                    </div>
                    <div class="item-price">累计收益：{{item.income}}{{item.coin_unit}}</div>
                </div>
                <div class="item-con flex-box">
                    <div class="item-item">
                        <div class="power">{{item.cp_total}}</div>
                        <div class="item-decs">算力</div>
                    </div>
                    <div class="item-item flex-1">
                        <div class="mine-numb" style="font-size: 1rem">{{item.low}}-{{item.high}}/{{item.coin_unit}}</div>
                        <div class="item-decs">每天挖矿量</div>
                    </div>
                    <div class="item-item flex-1" >
                        <div class="item-time"><em>{{item.expire_time}}</em></div>
                        <div class="item-decs">过期时间</div>
                    </div>
                    <div class="item-item flex-1">
                        <router-link  :to="'/mine_machine/self_detail?id='+item.id" class="item-btn">收益详情</router-link>
                    </div>
                </div>
            </div>
        </div>
        <!-- <nodata  v-else :datatip="'暂无数据'"></nodata> -->
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
            list:[],
        };
    },
    created(){
        this.$http.post('api/app.miner/miner/expired_miner_list',{}).then(res=>{
            if(!res.errcode){
                this.list = res.data.list;
            }
        })
    },
    mounted() {
        
    },
    methods: {
        
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

.page-machine-history {
    min-height: 100%;
    padding-bottom: 1px;
    position: relative;
    .machine_header{
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
    }
    .content{
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        overflow-x: hidden;
        overflow-y: scroll;
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
                font-size: 0.75rem;
            }
            .item-con {
                padding: 0.9375rem;
                position: relative;
                
                .item-item{
                    text-align: center;
                    font-size: 0.75rem;
                    &:first-child{
                        text-align: left;
                        min-width: 4rem;
                        flex-shrink: 0;
                    }
                    &:last-child{
                        text-align: right;
                    }
                    .item-btn {
                        background: #2f82ff;
                        color: #fff;
                        text-align: center;
                        line-height: 2rem;
                        height: 2rem;
                        display: inline-block;
                        border-radius: 4px;
                        width: 4.25rem;
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
}
</style>
