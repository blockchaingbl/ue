<template lang="html">
    <div class="crowd-index">
        <div v-if="!loadings">
            <div class="course-box">
                <div class="course-block">
                    <router-link class="item-box" v-for="(item,index) in crowdList" :key="index" :to="{path: '/crowdgoods/detail/'+item.crowd_id}">
                        <div class="item flex-box" >
                            <div class="item-img">
                                <img :src="item.image" alt="">
                            </div>
                            <div class="item-con flex-1">
                                <div class="item-title">{{item.title}}</div>
                                <div class="item-decs">{{item.crowd_goods.brief}}</div>
                                <div class="item-footer flex-box">
                                    <div class="item-time"> {{item.create_time}}</div>
                                </div>
                                <div class="item-button">
                                    <x-button v-if="item.status==0" type="primary"  mini @click.native.prevent.stop="turn_pay(item)">立即支付</x-button>
                                    <span v-else-if="item.status==-1" style="color:#999">已取消</span>
                                    <span v-else-if="item.status==1">支付中</span>
                                    <span v-else-if="item.status==-4">支付失败</span>
                                    <span v-else-if="item.status==2&&item.crowd_goods.less_amount>0" style="color:sandybrown">等待成团</span>
                                    <span v-else-if="item.status==2&&item.crowd_goods.less_amount==0&&item.is_reward==0" style="color:sandybrown">等待开奖</span>
                                    <span v-else-if="item.status==-2&&item.crowd_goods.status==-1" style="color: #1aad19"> 拼团失败,退款中</span>
                                    <span v-else-if="item.status==-3&&item.crowd_goods.status==-1" style="color: #1aad19"> 拼团失败,已退款</span>
                                    <span v-else-if="item.status==-2&&item.is_reward==-1" style="color: #1aad19"> 未中奖,退款中</span>
                                    <span v-else-if="item.status==-3&&item.is_reward==-1" style="color: #1aad19"> 未中奖,已退款</span>
                                    <span v-else-if="item.status==2&&item.crowd_goods.status==1&&item.is_reward==1"  style="color: red">恭喜中奖</span>
                                    <span v-else-if="item.status==2&&item.crowd_goods.status==1&&item.is_reward==-1" style="color: forestgreen">未中奖,继续努力</span>
                                </div>
                            </div>
                        </div>
                    </router-link>
                </div>
                <Scroller v-if="crowdList.length>0" v-on:load="loadlist" :loading="loading" :container="'.course-block'" ></Scroller>
                <nodata  v-else :datatip="'暂无数据'"></nodata>
            </div>
        </div>
        <div v-transfer-dom>
            <loading :show="loadings"></loading>
        </div>
    </div>
</template>
<script>
    import { Loading, TransferDomDirective as TransferDom ,Clocker} from 'vux';
    import Scroller from "@/components/scroller";
    import Nodata from "@/components/nodata";
    export default {
        directives: {
            TransferDom
        },
        components: {
            Loading,
            Nodata,
            Scroller,
            Clocker
        },
        data() {

            return {
                crowdList:[],
                loadings:true,
                page:2,
                loading:false,
            };
        },
        mounted() {
            this.getCrowd()
        },
        methods: {
            stop_click(){
                return false;
            },
            turn_pay(item){
              let address = this.$store.state.init.crowd_address
                let url = encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+item.order_code+'&amount='+item.amount+'&address='+address);
                this.$router.push({path:url})
            },
            getCrowd(){
                this.$http.post('/api/app.crowdfund/crowdgoods/myfund',{}).then(res=>{
                    this.crowdList=res.data.lists;
                    this.loadings=false;

                })
                .catch(error=>{
                    this.loadings=false;
                    this.$vux.toast.text(error.message);
                })
            },
            loadlist(){
                if (this.loading) {
                    //正在加载中
                }else if(this.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.crowdfund/crowdgoods/myfund',{page:this.page}).then(res=>{
                        if (res.data.lists.length<1) {
                            this.page=null;
                            this.loading=false;
                            this.loadings = false;
                        }else{
                            this.crowdList=this.crowdList.concat_unk(res.data.lists,"id");
                            this.loadings = false;
                            this.page++;
                            this.loading=false;
                        }
                    }).catch(err => {
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                        console.log(err);
                        //  this.Toast(err || '网络异常，请求失败');
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
    .crowd-index{
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
                &::before{
                    display: block;
                    content: '';
                    padding-top: 75%;
                }
                img {
                    width: 100%;
                    height: 100%;
                    display: block;
                    position: absolute;
                    top: 0;
                    left: 0;
                }
            }
            .item-title {
                line-height: 1.3125rem;
                font-size: 0.9375rem;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;
                overflow: hidden;
            }
            .item-decs {
                font-size: 0.6875rem;
                color: #999;
                line-height: 0.9375rem;
                height: 1.875rem;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                margin-bottom: 0.375rem;
            }
            .item-time {
                font-size: 0.75rem;
                color: #999;
                min-width: 6.5rem;
                position: relative;
                line-height: 1.375rem;
                em {
                    display: inline-block;
                    width: 0.875rem;
                    height: 0.875rem;
                    line-height: 0.875rem;
                    text-align: center;
                    color: #fff;
                    border-radius: 2px;
                    position: absolute;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                }
                .qi{
                    background: #719aee;
                }
                .zhi{
                    background: #ef7f89;
                }
            }
            .item-con{
                position: relative;
            }
            .item-button{
                position: absolute;
                right: 0.25rem;
                top: 2rem;
            }
        }
    }
</style>
