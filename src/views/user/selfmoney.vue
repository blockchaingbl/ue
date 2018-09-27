<template lang="html">
<scroller @on-scroll-bottom="onScrollBottom" height="100%" lock-x ref="scrollerEvent">
<div class="page-slefmoney">
    <div class="task-block" v-if="money_lists.length>0">
        <div class="item flex-box" v-for="x in money_lists" @click="goPage(x)">
            <div class="item-img">
                <img :src="x.coin_type.icon" alt="">
            </div>
            <div class="item-info flex-1">
                <div class="item-title flex-box">
                    <div class="title-text flex-1">{{x.coin_type.coin_unit}}</div>
                    <div class="item-money flex-box">
                        <div class="item-type">
                            {{x.vc_amount}}
                        </div>
                        <div class="item-decs flex-box" v-if="x.coin_type.price>0">
                            <div class="item-decs-list">≈&nbsp;&nbsp;¥&nbsp;{{(x.vc_amount*x.coin_type.price).toFixed(2)}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <load-more tip="正在加载 . . ." v-if="loading"></load-more>
        <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
    </div>
    
    <nodata  v-else :datatip="'暂无数据'"></nodata>
    
</div>
</scroller>
</template>
<script>

import { setCookie, getCookie, deleteCookie } from "../../assets/js/cookieHandle";
import { Scroller , Divider , LoadMore} from 'vux';
import Nodata from "@/components/nodata";
export default {
    components: {
        Scroller,
        Divider,
        LoadMore,
        Nodata
    },
    data () {
        return {
            money_lists:[],
            formData:{page:1},
            lock:false,
            loading :0
        }
    },
    mounted () {
        this.loadMyMoney();
    },
    methods:{
        loadMyMoney : function () {
            if(this.lock){
                return false;
            }
            this.lock = true;
            this.loading = 1;
            this.$http.post('/api/app.user/account/asset',this.formData)
                .then(res => {
                    if(res.data.asset.length>0){
                        this.money_lists =  this.money_lists.concat_unk(res.data.asset,"id");
                        this.lock=false;
                    }
                    this.loading = 0;
                    this.formData.page++;
                })
                .catch(error=>{
                    this.loading = 0;
                })
        },
        onScrollBottom : function () {
            if(!this.lock){
                this.loadMyMoney();
            }
        },
        goPage : function(x){
            setCookie("setCoinInfo",JSON.stringify(x));
            this.$store.dispatch('setCoinInfo',x).then((r) => {
              this.$router.push({name:'userSelfbcty'})
            });
        }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .page-slefmoney{
        height: 100%;
        background-color: #f6f6f6;
        .head{
            height: 3rem;
            background:-webkit-linear-gradient(left,#26cdcd,#5c81ea);
            background:linear-gradient(to right,#26cdcd,#5c81ea);
            font-size: @fs-middle;
            color: #fff;
            padding: 0 0.625rem;
            span{
                font-family: arial;
                font-size: 1rem;
            }
        }
        .assets {
            padding: 0 0.625rem;
            background: #fff;
            height: 3.625rem;
            .self-money {
                line-height: 1.5rem;
                .iconfont{
                    font-size: @fs-middle;
                    color: #6b94f8;
                    margin-top: 3px;
                    margin-right: 6px;
                }
                .money-text {
                    font-size: @fs-middle;
                    .assets-numb{
                        padding-left: 1.25rem;
                        font-size: 1.25rem;
                        color: #6b94f8;
                        font-weight: bold;
                    }
                }
            }
            .weui-btn{
                font-size: @fs-middle;
                width: 4.9375rem;
            }
            .weui-btn + .weui-btn{
                margin-top: 0;
                margin-left: 0.25rem;
                background-color: #fc8c92;
            }
        }
        .item-head{
            background: url(../../assets/images/task_center_item.png) no-repeat top center;
            background-size: 100% 100%;
            height: 3.75rem;
            color: #fff;
            text-align: center;
            margin: 0.625rem;
            padding: 0.5625rem;
            .item-head-title{
                font-size: 1rem;
                line-height: 1.5rem;
            }
            .item-head-desc{
                font-size: 0.6875rem;
                line-height: 1.1875rem;
            }
        }
        .task-block{
             font-family: Arial, "Microsoft Yahei";
            .item {
                height: 4.25rem;
                background: #fff;
                margin: 0.625rem 0.625rem 0;
                border-radius: 4px;
                box-shadow: 0 2px 7px 0 rgba(0,0,0,0.05);
                padding: 0.3125rem 0.625rem;
                // .item-info{
                //     height: 3.75rem;
                // }
                .item-img {
                    width: 2.125rem;
                    height: 2.125rem;
                    margin-right: @fs-small;
                    img{
                        width: 100%;
                        height: 100%;
                        display: block;
                    }
                }
                .item-title{
                    line-height: 1.5rem;
                    .has {
                        color: #888;
                        span{
                            color: #4c4c51;
                        }
                    }
                }
                .item-money{
                    flex-direction: column;
                    align-items: flex-end;
                }
                .title-text {
                    font-size: 1rem;
                    color: #363840;
                }
                .item-type {
                    font-size: 1rem;
                    color: #6b94f8;
                    line-height: 1.1875rem;
                }
                .nohas {
                    font-weight: bold;
                    color: #fc8c92;
                    line-height: 1.6rem;
                }
                .item-decs{
                    flex-wrap: wrap;
                }
                .item-decs-list{
                    padding-left: 0.5rem;
                    font-size: 0.875rem;
                    line-height: 1.0625rem;
                    color: #999;
                    position: relative;
                    list-style-type:none
                }
            }
        }
        .divider{
            font-size: @fs-middle;
            line-height: 1.875rem;
            color: #628df8;
            text-align: center;
        }
    }
</style>
