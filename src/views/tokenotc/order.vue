<template lang="html">
    <scroller @on-scroll-bottom="onScrollBottom" height="100%" lock-x ref="scrollerEvent">
        <div class="deals-record-box">
            <div class="head">
                我的订单
            </div>
            <tab :line-width="2" custom-bar-width="60px">
                <tab-item  :selected="this.formData.type==1" @click.native="switchType(1)">受让</tab-item>
                <tab-item  :selected="this.formData.type==2"  @click.native="switchType(2)">出让</tab-item>
            </tab>
            <div v-if="!loadings">
                <div class="record-block" v-if="order_list.length>0" >
                    <div class="item flex-box" v-for="order in order_list" @click="turnOrder(order)">
                        <div class="item-text flex-1">
                            <div class="title flex-box">
                                <div class="flex-1">数量：<span>{{order.vc_amount}} {{order.coin_info.coin_unit}}</span></div>
                                <div class="flex-1">
                                    <span v-if="formData.type==2 &&order.status!=2 && order.status!=3"><span>电话：</span><a :href="'tel:'+order.buyer.mobile" style="color: #4c4c51;font-size: 0.9375rem;">{{order.buyer.mobile}}</a></span></div>
                            </div>
                            <div class="money flex-box">
                                <div class="get flex-1">行情：<span>&yen;{{order.vc_total_price}}</span></div>
                                <div class="price flex-1" v-if="formData.type==2">受让方：<span>{{order.buyer.username}}</span></div>
                                <div class="price flex-1" v-if="formData.type==1">出让方：<span>{{order.seller.username}}</span></div>
                            </div>
                            <div class="account-number flex-box">
                                <div class="flex-1 price">
                                    单价：&yen;{{order.vc_uint_price}}
                                </div>
                                <div class="flex-1 price">
                                    <span>{{order.create_time}} </span>
                                </div>
                            </div>
                        </div>
                        <div class="order-sn">单号：{{order.order_sn}}</div>
                        <div class="item-type-box" v-if="formData.type==1">
                            <div class="item-type unaudited" v-if="order.status==0">待付款</div>
                            <div class="item-type paid" v-if="order.status==1">已付款</div>
                            <div class="item-type fail" v-if="order.status==2">已完成</div>
                            <div class="item-type fail" v-if="order.status==3">已取消</div>
                            <!--<div class="item-btn appeal" v-if="order.status==0" @click.stop="cancel(order)">取消</div>-->
                        </div>
                        <div class="item-btn-box" v-if="formData.type==1">
                            <div class="item-btn appeal" v-if="(order.appeal_status==0 || order.appeal_status==2) && (order.status==1) && order.seller_time_over" @click.stop="appeal(order.id)">申诉</div>
                            <div class="item-btn appeal" v-else-if="order.appeal_status==3" style="width:5rem;">申诉已处理</div>
                            <div class="item-btn appeal" v-if="order.appeal_status==1 || order.appeal_status==4">申诉中</div>
                        </div>
                        <div class="item-type-box" v-if="formData.type==2">
                            <div class="item-type unaudited" v-if="order.status==0">待付款</div>
                            <div class="item-type paid" v-if="order.status==1">待发</div>
                            <div class="item-type fail" v-if="order.status==2">已完成</div>
                            <div class="item-type fail" v-if="order.status==3">已取消</div>

                        </div>
                        <div class="item-bnt-box" v-if="formData.type==2">
                            <div class="item-btn appeal" v-if="(order.appeal_status==0 || order.appeal_status==1) && order.status==1" @click.stop="appeal(order.id)">申诉</div>
                            <div class="item-btn appeal" v-if="order.appeal_status==2 || order.appeal_status==4">申诉中</div>
                            <div class="item-btn appeal" v-if="order.appeal_status==3" style="width:5rem;">申诉已处理</div>
                        </div>
                    </div>
                    <load-more tip="正在加载 . . ." v-if="loading"></load-more>
                    <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
                    <div class="loadmore">
                    </div>
                </div>
                    <nodata  v-else :datatip="'暂无数据'"></nodata>
            </div>
            <div v-transfer-dom>
                <loading :show="loadings"></loading>
            </div>
        </div>
    </scroller>
</template>
<script>
import { Tab, TabItem ,Scroller , LoadMore , Divider, Loading, TransferDomDirective as TransferDom} from 'vux';
import Nodata from "@/components/nodata";
export default {
    directives: {
        TransferDom
    },
    components: {
        Tab,
        TabItem,
        Scroller,
        LoadMore,
        Divider,
        Nodata,
        Loading
    },
    data () {
        return {
            hasLogin:0,
            index01: 0,
            formData:{page:1,type:1},
            lock:false,
            loading:false,
            order_list:[],
            loadings:true
        }
    },
    mounted () {
        if(this.$route.params.type==2){
           this.formData.type=2;
        }

        this.loadOtcOrders();


    },
    methods:{
        loadOtcOrders(){
            if(this.lock){
               return false;
            }
            this.lock = true;
            this.loading = true;
            this.$http.post('/api/app.tokenotc/order',this.formData)
                .then(res=>{
                    if(res.data.order_list.length>0){
                        this.lock = false;
                        this.order_list=this.order_list.concat_unk(res.data.order_list,"id")
                    }
                    this.formData.page++;
                    this.loading = false;
                    this.loadings=false;
                })
                .catch(error=>{
                    this.loading = false;
                })
        },
        onScrollBottom(){
            this.loadOtcOrders();
        },
        switchType(type){
            if(type!=this.formData.type){
               this.reload(type);
            }
        },
        reload(type){
            this.$refs.scrollerEvent.reset({top: 0})
            this.formData = {type:type,page:1} ;
            this.loadings = true;
            this.order_list = [];
            this.lock = false;
            this.loadOtcOrders();
        },
        appeal(id){
            const _this = this
            this.$vux.confirm.show({
                content: '是否确认申诉',
                onConfirm () {
                    let formData = {
                        order_id:id,
                        type:_this.formData.type
                    };
                    _this.$http.post('/api/app.tokenotc/order/appeal',formData)
                        .then(res=>{
                            _this.$vux.toast.text('申诉成功');
                            setTimeout(function () {
                                _this.reload(_this.formData.type);
                            },2000)
                        })
                        .catch(error=>{
                            if (error.errcode) {
                                _this.$vux.toast.text(error.message);
                            }
                        })
                }
            })
        },
        turnOrder(order){
          if(this.formData.type==1 && (order.status==0 || order.status==1)) {
            this.$router.push({path: '/token_otc/order/' + order.id})
          }else if(this.formData.type==2 && order.status==1){
            this.$router.push({path: '/token_otc/order/' + order.id})
          }
        }
    }
}
</script>

<style lang="less" scoped>
    @import '~vux/src/styles/1px.less';
    @import '~vux/src/styles/close.less';
    @import '../../assets/css/variable.less';
    .deals-record-box{
        font-family: Arial, "Microsoft Yahei";
        font-size: @fs-middle;
        .head{
            background: url(../../assets/images/withdeawlist.jpg) no-repeat top center;
            height: 3rem;
            line-height: 3rem;
            color: #fff;
            font-size: 0.9375rem;
            background-size: 100% 100%;
            padding: 0 0.625rem;
        }
        .record-block{
            .item {
                background: #fff;
                padding:2rem 0.625rem 0.625rem;
                line-height: 1.625rem;
                color: #888;
                margin-top: 0.4rem;
                font-family: Arial, "Microsoft Yahei";
                position: relative;
                .title {
                    span{
                        color: #4c4c51;
                        font-size: 0.9375rem;
                    }
                }
                .get{
                    span{
                        font-size: 0.9375rem;
                        color: #6b94f8;
                    }
                }
                .order-sn {
                    height: 1.5rem;
                    line-height: 1.5rem;
                    color: #4c4c51;
                    font-size: 0.75rem;
                    padding: 0 0.5rem;
                    position: absolute;
                    top: 0;
                    left: 0;
                    background: #eee;
                }
                .item-type-box{
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 3.25rem;
                    height: 1.25rem;
                }
                .item-type {
                    height: 1.25rem;
                    line-height: 1.25rem;
                    width: 3.25rem;
                    color: #fff;
                    font-size: @fs-smaller;
                    text-align: center;
                    padding: 0 0.5rem;
                }
                .item-btn{
                    height: 1.5rem;
                    line-height: 1.5rem;
                    width: 3.5rem;
                    color: #6b94f8;
                    font-size: @fs-small;
                    border-radius: 0.75rem;
                    text-align: center;
                    padding: 0 0.5rem;
                    border: 1px solid #6b94f8;
                }
                .unaudited{
                    background: #14c3c0;
                }
                .finish{
                    background: #628df9;
                }
                .fail{
                    background: #f78383;
                }
                .send-coin{
                    background: #12e0de
                }
                .paid{
                    background: #eb5a5a;
                }
            }
        }
        .btn-box{
            position: absolute;
            left: 0;
            bottom: 0;
            right: 0;
        }
        .record-btn{
            font-size: 0.9375rem;
            height: 2.4375rem;
            line-height: 2.4375rem;
        }
    }
    .loadmore {
        user-select: none;
        color: #628cf8;
        padding: 20px;
        text-align: center;
        .tc-loading {
        ~ span {
              vertical-align: middle;
          }
        }
    }
</style>
