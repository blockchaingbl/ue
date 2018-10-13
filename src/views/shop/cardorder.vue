<template lang="html">
<div class="page-candy-distribute" >
    <div class="distri-content" >
         <div class="distri-title">兑换次数（{{total}}）</div>
        <div class="distri-con" v-show="!first_loading">
            <div class="distri-block">
                <a href="#" class="item" v-for="order,index in orders" :key="index">
                    <div class="item-item flex-box">
                        <div class="item-time flex-1">{{order.create_time}}</div>
                        <div class="item-text" >兑换类型：{{order.card_type}}</div>
                    </div>
                    <div class="item-item flex-box">
                        <div class="item-text flex-1">兑换数额 {{order.shop_cards_amount}}</div>
                        <div class="item-text">
                            兑换卡种：<span v-if="order.card_type=='加油卡'">{{order.oil_card}}</span><span v-else>{{order.shop_card}}</span>
                        </div>
                    </div>
                    <div class="item-item flex-box">
                        <div class="item-text flex-1">消费金额{{order.amount}} {{$store.state.init.coin_unit}}</div>
                        <div class="item-text">
                            <router-link :to="{path: '/cardorder/detail/'+order.id}">查看详情</router-link>
                        </div>
                    </div>
                </a>
            </div>
            <Scroller v-if="orders.length>0" v-on:load="loadList" :loading="loading" :container="'.distri-block'" ></Scroller>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>
    </div>
    <div v-transfer-dom>
        <loading :show="first_loading"></loading>
    </div>
</div>
</template>
<script>
import {Loading, TransferDomDirective as TransferDom  } from 'vux';
import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
export default {
    directives: {
        TransferDom
    },
    components: {
        Scroller,
        Nodata,
        Loading
    },
    data() {
        return {
            formData:{
                page:1,
            },
            orders:[],
            total:0,
            loading:false,
            first_loading:true,
        };
    },
    mounted() {
        this.loadList();
    },
    methods: {
        loadList:function () {
            if (this.loading) {
                //正在加载中
            }else if(this.formData.page!=null){
                //加载完毕
                this.loading=true;
                this.$http.post('/api/app.apply/cardorder/myorder',this.formData).then(res => {
                    this.total = res.data.total;
                    this.first_loading = false;
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.orders.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.orders=this.orders.concat_unk(res.data.orders,"id");
                        this.formData.page++;
                        this.loading=false;
                    }
                }).catch(err => {
                    this.first_loading = false;
                    this.formData.page=null;
                    this.loading = false;
                });
            }else{
                this.loading=false;
            }
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";

.page-candy-distribute {
    min-height: 100%;
    padding-bottom: 1px;
    .distri-title {
        padding: 0.9375rem 0.9375rem 0;
        font-size: 0.8125rem;
        line-height: 1.25rem;
        
    }
    .distri-block {
        padding: 0.625rem 0.9375rem 0;
        .item {
            display: block;
            background: #fff;
            margin-bottom: 0.625rem;
            border-radius: 5px;
            padding: 0.625rem;
            font-size: 0.75rem;
            line-height: 1.25rem;
            color: #363840;
            .item-time {
                color: #888;
            }
        }
    }
}
</style>
