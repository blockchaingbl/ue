<template lang="html">
<div class="token-deals-index">
    <div class="deals-content">
        <tab>
            <tab-item v-for="(val,key) in $store.state.token_coin_lists" :key="key" :selected="key==0">{{val.coin_type.coin_unit}}</tab-item>
        </tab>
        <div class="token_otc_lists">
            <div class="otc-item" v-for="(val,key) in otc_list" :key="key">
                <flexbox>
                    <flexbox-item :span="12">
                        <div class="flex-otc-head">
                            <img v-if="val.user.avatar!=''" src="@/assets/images/avatar.png">
                            <img v-else src="@/assets/images/avatar.png">
                            <span>{{val.user.username}}</span>
                        </div>
                    </flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="6"><div class="flex-otc-number">数量 {{val.vc_less_amount}}{{val.coin_info.coin_unit}}</div></flexbox-item>
                    <flexbox-item :span="6"><div class="flex-otc-number-right">单价</div></flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="6"><div class="flex-otc-number">总价 ￥{{val.total_price}}</div></flexbox-item>
                    <flexbox-item :span="6"><div class="flex-otc-number-price">￥{{val.vc_unit_price}}</div></flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="6">
                        <div class="pay_way">
                            <img src="@/assets/images/wechat.png" v-if="val.payment_key.indexOf('weixin')!==-1">
                            <img src="@/assets/images/alipay.png" v-if="val.payment_key.indexOf('alipay')!==-1">
                            <img src="@/assets/images/bankcard.png" v-if="val.payment_key.indexOf('bankcard')!==-1">
                        </div>
                    </flexbox-item>
                    <flexbox-item :span="6">
                        <div class="flex-otc-button">
                            <x-button type="primary" mini>购买</x-button>
                        </div>
                    </flexbox-item>
                </flexbox>
            </div>
        </div>
        <div class="deals-block">
            <Scroller v-if="otc_list.length>0" v-on:load="loadOtcLists" :loading="loading" :container="'.block-box'" ></Scroller>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>

    </div>
</div>   
</template>
<script>
import {  LoadMore , Divider,Tab, TabItem ,Flexbox ,FlexboxItem,Popup } from 'vux';


import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
export default {
    components: {
      Scroller,
      LoadMore,
      Divider,
      Tab,
      TabItem,
      Nodata,
      Flexbox ,
      FlexboxItem,
      Popup
    },
    data () {
        return {

            formData:{page:1,type:0,orderkey:'',orderby:'desc'},
            lock:false,
            otc_list : [],
            loading:false,
            formData:{
              coin_type:0,
              page:1
            }
        }
    },
    mounted () {
        this.getTab();
        // this.loadOtcLists();
    },
    methods:{
        getTab(){
            this.$http.post('/api/app.tokenotc/deals/get_open_coin',{}).then(res => {
                this.$store.state.token_coin_lists=res.data.lists;
                this.loadOtcListsFirst();
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
      loadOtcListsFirst(){
            this.$http.post('/api/app.tokenotc/deals',this.formData).then(res => {
                this.otc_list = this.otc_list.concat_unk(res.data.otc_list,"id");
                if (res.data.otc_list.length<10) {
                    this.loading=false;
                    this.formData.page=null;
                }else{
                    this.formData.page=2;
                }
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        loadOtcLists(){
            if (this.loading) {
                //正在加载中
            }else if(this.formData.page!=null){
                //加载完毕
                this.loading=true;
                this.$http.post('/api/app.tokenotc/deals',this.formData).then(res => {
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.otc_list.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.otc_list=this.otc_list.concat_unk(res.data.otc_list,"id");
                        this.formData.page++;
                        this.loading=false;
                       // console.log(this.otc_list);
                    }
                }).catch(err => {
                    console.log(err);
                    this.formData.page=null;
                    this.loading = false;
                });
            }else{
                this.loading=false;
            }
        },
        onScrollBottom(){
            this.formData.page++;
            this.loadOtcLists();
        },
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .token-deals-index{
        .deals-content{
            .token_otc_lists{
                .otc-item{
                    margin-top: .675rem;
                    background-color: #fff;
                    padding: 0.3rem 10px 0.5rem 10px;
                    img{
                        width: 1.5rem;
                        height: 1.5rem;
                        border-radius: 50%;
                    }
                    .pay_way{
                        width: 1.5rem;
                        height: 1.5rem;
                        display: inline-block;
                    }
                    .flex-otc-head{
                        display: flex;
                        align-items: center;
                        span{
                            padding-left: 0.25rem;
                        }
                    }
                    .flex-otc-number{
                        height: 1.5rem;
                        line-height: 1.5rem;
                        font-size: 12px;
                        color: #999;
                    }
                    .flex-otc-number-right{
                        height: 1.5rem;
                        line-height: 1.5rem;
                        text-align: right;
                        font-size: 12px;
                        color: #999;
                        padding-right: 1rem;
                    }
                    .flex-otc-number-price{
                        height: 1.5rem;
                        line-height: 1.5rem;
                        text-align: right;
                        font-size: 1.2rem;
                        color: rgb(47,130,255);
                        padding-right: 1rem;
                    }
                    .flex-otc-button{
                        height: 2rem;
                        text-align: right;
                        padding-right: 1rem;
                    }

                }
            }
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
