<template lang="html">
<div class="page-candy-distribute" >
    <div class="distri-content" >
         <div class="distri-title">历史转出记录（{{total}}）</div>
        <div class="distri-con" v-show="!first_loading">
            <div class="distri-block">
                <a href="#/candy/distribute_detail"  @click.prevent="turnDetail(sugar)" class="item" v-for="order,index in orders" :key="index">
                    <div class="item-item flex-box">
                        <div class="item-time flex-1">{{order.create_time}}</div>
                        <div class="item-text" >接收人：{{order.to_user.username}}</div>
                    </div>
                    <div class="item-item flex-box">
                        <div class="item-text flex-1">转出数额 {{order.amount}}</div>
                        <div class="item-text">手机：**********{{order.to_user.mobile.substr(-1,1)}}</div>
                    </div>
                    <!--<div class="item-item flex-box">-->
                        <!--<div class="item-text flex-1">锁定：</div>-->
                        <!--<div class="item-text">xxx</div>-->
                    <!--</div>-->
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
import {Tab, TabItem ,Loading, TransferDomDirective as TransferDom,Radio  } from 'vux';
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
        Loading
    },
    data() {
        return {
            formData:{
                page:1,
                type:2,
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
                this.$http.post('/api/app.user/transfer/orders',this.formData).then(res => {
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
                    console.log(this.orders)
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
