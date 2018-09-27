<template lang="html">
<div class="page-candy-distribute" >
    <div class="distri-content" >
        <div class="distri-title">历史发糖记录（{{total}}）</div>
        <div class="distri-con" v-show="!first_loading">
            <div class="distri-block">
                <a href="#/candy/distribute_detail"  @click.prevent="turnDetail(sugar)" class="item" v-for="sugar,index in sugars" :key="index">
                    <div class="item-item flex-box">
                        <div class="item-time flex-1">{{sugar.create_time}}</div>
                        <div class="item-text">领糖有效期：{{sugar.receive_limit}}天</div>
                    </div>
                    <div class="item-item flex-box">
                        <div class="item-text flex-1">资产({{sugar.coin_info.coin_unit}})：{{sugar.amount}}</div>
                        <div class="item-text" >份额：{{sugar.copys - sugar.copys_less}}/{{sugar.copys}}</div>
                    </div>
                    <div class="item-item flex-box">
                        <div class="item-text flex-1">锁定：{{sugar.lock_day}}天</div>
                        <div class="item-text">已释放：{{sugar.free_amount}}</div>
                    </div>
                </a>
            </div>
            <Scroller v-if="sugars.length>0" v-on:load="loadList" :loading="loading" :container="'.distri-block'" ></Scroller>
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
            sugars:[],
            total:0,
            loading:false,
            first_loading:true
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
                this.$http.post('/api/app.user/sugar',this.formData).then(res => {
                    this.total = res.data.total;
                    this.first_loading = false;
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.sugars.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.sugars=this.sugars.concat_unk(res.data.sugars,"id");
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
        },
        turnDetail(x){
            if(x.sugar_type==0){
                this.$router.push({name:'distribute_detail',params:x})
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
