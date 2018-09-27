<template lang="html">
    <div class="connect-index">
        <div v-if="!loadings">
            <div class="connect-box">
                <div class="connect-block">
                    <div class="item-box" v-for="(connect,index) in connect_list" :key="index">
                        <div class="item flex-box">
                            <div class="item-img">
                                <img :src="connect.image" alt="反馈图片" v-if="connect.image">
                                <img src="@/assets/images/block_chain.jpg" alt="默认图片" v-else>
                            </div>
                            <div class="item-con flex-1">
                                <!--<div class="item-title"></div>-->
                                <div class="item-decs">反馈时间:{{connect.create_time}}</div>
                                <div class="item-content">反馈内容：<em>{{connect.content}} </em></div>
                                <div class="item-decs" v-if="connect.huifu">反馈回复：<em>{{connect.huifu}} </em></div>
                                <div class="item-decs" v-else>反馈回复：<em>请耐心等待回复</em> </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Scroller v-if="connect_list.length>0" v-on:load="loadlist" :loading="loading" :container="'.connect-block'" ></Scroller>
                <nodata  v-else :datatip="'暂无数据'"></nodata>
            </div>
        </div>
        <div v-transfer-dom>
            <loading :show="loadings"></loading>
        </div>
    </div>
</template>
<script>
import { Loading, TransferDomDirective as TransferDom} from 'vux';
import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
    export default {
        directives: {
            TransferDom
        },
        components: {
            Nodata,
            Loading,
            Scroller
        },
        data() {
            
            return {
                connect_list:[],
                type:1,
                page:2,
                loadings:true,
                loading:false,
            };
        },
        mounted() {
            this.connect();
        },
        methods: {
            connect(){
                this.$http.post('/api/app.user/account/huifu',{type:this.type,page:1}).then(res=>{
                    this.connect_list=res.data.huifus;
                    this.loadings=false;
                    if(res.data.huifus.length<10){
                        this.loading=false;
                        this.page=null;
                    }
                })
                .catch(error=>{
                    this.$vux.toast.text(error.message);
                })
            },
            loadlist(){
                if (this.loading) {
                    //正在加载中
                }else if(this.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.user/account/huifu',{type:this.type,page:this.page}).then(res=>{
                        if (res.data.huifus.length<1) {

                            this.page=null;
                            this.loading=false;
                            this.loadings = false;
                        }else{
                            this.connect_list=this.connect_list.concat_unk(res.data.huifus,"id");
                            this.loadings = false;
                            this.page++;
                            this.loading=false;
                        }
                    }).catch(err => {
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                    });

                }else{
                    this.loading=false;
                }
            },
        }
    };
</script>
<style lang="less" scoped>
    @import "~vux/src/styles/1px.less";
    .connect-index{
        min-height: 100%;
        padding-bottom: 0.625rem;
        background: #fff;
        .item-box{
            background: #f5f5f5;
            padding-bottom: 0.3125rem;
        }
        .item {
            padding: 0.9375rem;
            background: #fff;
            color: #363840;
            .item-img {
                width: 6rem;
                overflow: hidden;
                margin-right: 0.625rem;
                position: relative;
                flex-shrink: 0;
                &::before {
                    display: block;
                    content: '';
                    padding-top: 75%;
                }
                img {
                    width: 80%;
                    height: 100%;
                    display: block;
                    position: absolute;
                    top: 0;
                    left: 0;
                }
                img[src='']{
                    border: 1px solid #eee;
                }
            }
            .item-con{
                width: 50%;
            }
            .item-title {
                line-height: 1.5rem;
                font-size: 0.9375rem;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;

            }
            .item-content{
                font-size: 0.6875rem;
                color: #999;
                line-height: 1.25rem;
                margin: 0.25rem 0;
                overflow: hidden;
                text-overflow:ellipsis;
                white-space: nowrap;
            }
            .item-decs {
                font-size: 0.6875rem;
                color: #999;
                line-height: 1.25rem;
                margin: 0.25rem 0;
            }
            .item-price {
                font-size: 0.6875rem;
                color: #999;
                text-align:justify;
                text-justify:inter-ideograph;
                em {
                    font-size: 0.9375rem;
                    color: #363840;
                }
            }
        }
    }
</style>
