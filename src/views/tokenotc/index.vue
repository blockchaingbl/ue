<template lang="html">
<div class="token-deals-index">
    <div class="deals-content">
        <x-header class="m-tab" :left-options="{backText: ' '}">
            <h1 class="m-tab-top">令牌流通</h1>
            <a slot="left"></a>
            <a slot="right">
                <i slot="right" v-on:click="showMenus()" class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;">&#xe6f5;</i>
                <!--<router-link to="/token_otc/order">我的订单</router-link>-->
            </a>
        </x-header>
        <tab>
            <tab-item :disabled="lock" v-for="(val,key) in $store.state.token_coin_lists" :key="key" :selected="key==0" @on-item-click="chose_type(val.id)">{{val.coin_type.coin_unit}}</tab-item>
        </tab>
        <div class="token_otc_lists">
            <div class="otc-item" v-for="(val,key) in otc_list" :key="key">
                <flexbox>
                    <flexbox-item :span="12">
                        <div class="flex-otc-head">
                            <img v-if="val.user.avatar!=''" src="@/assets/images/avatar.png">
                            <img v-else src="@/assets/images/avatar.png">
                            <span v-if="val.reward==0">{{val.user.username}}</span>
                            <span v-if="val.reward==1" style="color:#2f82ff;">{{val.user.username}}</span>
                        </div>
                    </flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="8"><div class="flex-otc-number">限额 {{val.min_buy_amount}} ~ {{val.vc_less_amount}}{{val.coin_info.coin_unit}}</div></flexbox-item>
                    <flexbox-item :span="4"><div class="flex-otc-number-right">单价</div></flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="9"><div class="flex-otc-number">总额{{val.vc_less_amount}}{{val.coin_info.coin_unit}}  总价 ￥{{val.total_price}}</div></flexbox-item>
                    <flexbox-item :span="3"><div class="flex-otc-number-price">￥{{val.vc_unit_price}}</div></flexbox-item>
                </flexbox>
                <flexbox>
                    <flexbox-item :span="6">
                            <img src="@/assets/images/wechat.png" v-if="val.payment_key.indexOf('weixin')!==-1">
                            <img src="@/assets/images/alipay.png" v-if="val.payment_key.indexOf('alipay')!==-1">
                            <img src="@/assets/images/bankcard.png" v-if="val.payment_key.indexOf('bankcard')!==-1">
                            <img src="@/assets/images/wallet.png" v-if="val.payment_key.indexOf('asset')!==-1 || val.payment_key.indexOf('wallet')!==-1">
                    </flexbox-item>
                    <flexbox-item :span="6">
                        <div class="flex-otc-button">
                            <x-button type="primary" mini @click.native="turn_buy(val.id)" v-if="val.type==0">受让</x-button>
                            <x-button type="warn" mini @click.native="turn_sell(val.id)" v-if="val.type==1">出让</x-button>
                        </div>
                    </flexbox-item>
                </flexbox>
            </div>
        </div>
        <div class="deals-block">
            <Scroller v-if="otc_list.length>0" v-on:load="loadOtcLists" :loading="loading" :container="'.block-box'" ></Scroller>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>
        <popup v-model="showRightBar" position="right" style="width:60%;background:#fff;"  v-transfer-dom>
            <div style="width:100%; padding-top:30px;">
                <box gap="25px 10px">
                    <x-button class="right_item  rbt" style="background-color:#1d62c1;border-radius:99px;" type="primary" link="/token_otc/order">我的订单</x-button>
                    <x-button class="right_item rbt" style="background-color:#1da0c1;border-radius:99px;color: #fff" type="primary" link="/token_otc/deal/sell">我的挂单</x-button>
                    <x-button class="right_item  rbt" style="background-color:#cf222d;border-radius:99px;" type="primary" link="/token_incharge/0">令牌转入</x-button>
                    <x-button class="right_item rbt" style="background-color:#1aad19;border-radius:99px;color: #fff" type="primary" link="/token_otc/withdraw">令牌转出</x-button>
                    <x-button v-if="auth>0" class="right_item rbt" style="background-color:#df5000;border-radius:99px;color: #fff" type="default" @click.native="turn_push">出让</x-button>
                    <x-button class="right_item rbt" style="background-color:#fff;padding:0;border-radius:99px; color: #1d62c1; border-color: #7fabe7;" type="default" @click.native="turn_need">需求</x-button>
                    <!--<x-button  class="right_item rbt" style="background-color:#fff;padding:0;border-radius:99px; color: #1d62c1; border-color: #7fabe7;" type="default" link="/token_incharge/0">令牌转入</x-button>-->
                    <!--<x-button  class="right_item rbt" style="background-color:#fff;padding:0;border-radius:99px;" type="default" link="/token_incharge/0">令牌转出</x-button>-->

                </box>
            </div>
        </popup>

    </div>
</div>
</template>
<script>
import {  LoadMore , Divider,Tab, TabItem ,Flexbox ,FlexboxItem,Popup,TransferDom } from 'vux';


import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
export default {
    directives: {
    TransferDom
    },
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
            lock:false,
            otc_list : [],
            loading:false,
            formData:{
              coin_type:0,
              page:1
            },
            showRightBar:false,
            auth:0,
            lock:false
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
                this.auth = res.data.auth;
                this.formData.coin_type = res.data.lists[0].id;
                this.loadOtcListsFirst();
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
      loadOtcListsFirst(){
          if(!this.lock)
          {
            this.lock = true;
            this.$http.post('/api/app.tokenotc/deals',this.formData).then(res => {
              this.otc_list = this.otc_list.concat_unk(res.data.otc_list,"id");
              if (res.data.otc_list.length<10) {
                this.loading=false;
                this.formData.page=null;
              }else{
                this.formData.page=2;
              }
              this.lock = false;
            }).catch(err => {
              if (err.errcode) {
                this.$vux.toast.text(err.message);
              }
              this.lock = false;
              console.log(err);
              //  this.Toast(err || '网络异常，请求失败');
            });
          }

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
                  this.lock = false;
                }).catch(err => {
                    this.lock = false;
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
      turn_buy(id){
            this.$router.push({path:'/token_buy/'+id})
      },
      turn_sell(id)
      {
        this.$router.push({path:'/token_sell/'+id})
      },
      turn_push(){
        this.$router.push({path:'/token_push'})
      },
      turn_need(){
        this.$router.push({path:'/token_need'})
      },
      chose_type(id){
          if(this.formData.coin_type==id)
          {
            return;
          }
          this.formData = {
            coin_type:id,
            page:1
          }
          this.otc_list = [];
          this.loadOtcListsFirst();
      },
      showMenus()
      {
        this.showRightBar = true
      }
    },
  watch:{
      lock(){
        console.log(this.lock)
      }
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
