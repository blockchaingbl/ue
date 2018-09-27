<template lang="html">
<div class="page-distribute-detail">
    <x-header :left-options="{showBack: true,backText:''}">{{this.$route.meta.title}}</x-header>
    <div class="detail-head">
        <div class="condy-time">发糖时间：{{sugar.create_time}}</div>
        <div class="condy-time">领糖截止：{{sugar.receive_end_time}}</div>
        <div class="candy-info">
            <div class="info-item flex-box">
                <div class="item-text flex-1">资产({{sugar.coin_info.coin_unit}})：{{sugar.amount}}</div>
                <div class="item-text">已释放：{{sugar.free_amount}}</div>
            </div>
            <div class="info-item flex-box">
                <div class="item-text flex-1">糖果(份)：{{sugar.copys}}</div>
                <div class="item-text">被领取(份)：{{sugar.copys - sugar.copys_less}}</div>
            </div>
            <div class="info-item flex-box">
                <div class="item-text flex-1">锁定：{{sugar.lock_day}}天</div>
                <div class="item-text">总释放：{{sugar.amount}}</div>
            </div>
        </div>
    </div>
    <div class="detail-history" v-show="!first_loading">
        <div class="history-title">领糖果记录（{{sugar.copys - sugar.copys_less}}）</div>
        <div class="his-table">
            <div class="his-table-head flex-box">
                <div class="table-head-item his-time">领取时间</div>
                <div class="table-head-item his-name">领取人</div>
                <div class="table-head-item his-numb">领取数量</div>
                <div class="table-head-item his-distr">是否释放</div>
            </div>
            <div class="his-tab-con">
                <div class="table-item flex-box vux-1px-b" v-for="x in sugars_detail">
                    <div class="item-item his-time">{{x.create_time}}</div>
                    <div class="item-item his-name">{{x.to_username}}</div>
                    <div class="item-item his-numb">{{x.sugar_amount}}</div>
                    <div class="item-item his-distr" v-if="x.free">是</div>
                    <div class="item-item his-distr" v-else >否</div>
                </div>
            </div>
        </div>
        <Scroller v-if="sugars_detail.length>0" v-on:load="loadDetail" :loading="loading" :container="'.his-tab-con'" ></Scroller>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
    </div>
    <div v-transfer-dom>
        <loading :show="first_loading"></loading>
    </div>
</div>
</template>
<script>
    import Scroller from "@/components/scroller";
    import Nodata from "@/components/nodata";
    import {Loading, TransferDomDirective as TransferDom} from 'vux';
export default {
    directives: {TransferDom},
    components: {
        Scroller,
        Nodata,
        Loading
    },
    data() {
        return {
            sugar:{},
            formData:{
                page:1
            },
            sugars_detail:[],
            loading:false,
            total:0,
            first_loading:true
        };
    },
    created(){
        this.sugar = this.$route.params
        this.formData.sugar_id = this.sugar.id
    },
    mounted() {
        this.loadDetail()
    },
    methods: {
        loadDetail:function () {
            if (this.loading) {
                //正在加载中
            }else if(this.formData.page!=null){
                //加载完毕
                this.loading=true;
                this.$http.post('api/app.user/sugar/grant_detail',this.formData).then(res => {
                    this.first_loading = false;
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.detail.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.total = res.data.total;
                        this.sugars_detail=this.sugars_detail.concat_unk(res.data.detail,"id");
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

.page-distribute-detail{
    color: #404f5c;
    min-height: 100%;
    padding-bottom: 1px;
    .detail-head {
        padding: 0.9375rem;
        background: #fff;
        font-size: 0.75rem;
        line-height: 1.5625rem;
        .condy-time {
            color: #888;
        }
    }
    .detail-history {
        .history-title {
            font-size: 0.8125rem;
            line-height: 2.625rem;
            padding: 0 0.9375rem;
        }
        .his-table-head {
            height: 1.5625rem;
            background: #b5b7be;
            padding: 0 0.9375rem;
            font-size: 0.6875rem;
            color: #fff;
        }
        .his-time {
            width: 27.25%;
            flex-shrink: 0;
            font-size: 0.6875rem;
        }
        .his-name {
            width: 29.856%;
            flex-shrink: 0;
        }
        .his-numb {
            width: 25.51%;
            flex-shrink: 0;
        }
        .his-distr {
            width: 17.685%;
            flex-shrink: 0;
        }
        .his-tab-con {
            padding-left: 0.9375rem;
            .table-item {
                padding-right: 0.9375rem;
                padding-top: 0.625rem;
                padding-bottom: 0.625rem;
                font-size: 0.75rem;
                .his-time {
                    color: #888;
                }
            }
        }
    }
}
</style>
