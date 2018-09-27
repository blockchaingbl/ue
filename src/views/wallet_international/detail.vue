<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:'',preventGoBack:true}" :right-options="{showMore: false}" @on-click-back="goback"  class="inter_wallet_header">{{page_title}}
            <i  slot="right" v-on:click="open_scan()" v-show="$store.state.init.is_app"  class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;">&#xe6e1;</i>
        </x-header>
        <div class="main_card">
            <div class="wallet_amount">{{coin_amount}}</div>
            <div class="wallet_address" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(this.$store.state.wallet.address,12,20,'...')}} <i class="iconfont">&#xe71f;</i> </div>
        </div>
        <group class="tx_list_block">
            <div v-for="(tx, index) in this.tx_list" class="tx_list" v-on:click="showTx(tx.hash)">
                <div v-if="tx.confirmations>=ethereum_confirm_count" class="tx_list-con flex-box">
                    <div v-if="tx.txType=='shouru'" class="formatValue_add iconfont">&#xe71c;</div>  
                    <div class="iconfont formatValue_reduce"  v-if="tx.txType=='zhichu'">&#xe71e;</div>
                    <cell class="tx_list-txt flex-1" is-link>
                    <span slot="title">
                        <i :class="['iconfont','icon-'+tx.txType,'tx_icon']"></i> 
                        <span class="address_hash">
                            <span class="tx_list_key">{{shortStr(tx.hashShow,12,20,'...')}}</span>
                            <span class="tx_list_time">{{tx.datetime}}</span></span>
                        </span>
                        <span slot="value" class="tx_amount">{{tx.formatValue}}</span>
                    </cell>
                </div>
                <div class="tx_list-con flex-box" v-else>
                    <div v-if="tx.txType=='shouru'" class="formatValue_add iconfont">&#xe71c;</div>  
                    <div class="iconfont formatValue_reduce"  v-if="tx.txType=='zhichu'">&#xe71e;</div>
                    <cell class="tx_list-txt flex-1" is-link>
                        <span slot="title">
                            <i :class="['iconfont','icon-'+tx.txType,'tx_icon']"></i>
                            <span class="address_hash">
                                <span class="tx_list_key">{{shortStr(tx.hashShow,12,20,'...')}}</span>
                                <span class="tx_list_time" v-if="tx.confirmations>0">
                                    <x-progress :percent="tx.confirmations/ethereum_confirm_count*100" :show-cancel="false"></x-progress>
                                    确认中：{{tx.confirmations}}/{{ethereum_confirm_count}}
                                </span>
                                <span class="tx_list_time" v-else>
                                    <x-progress :percent="tx.confirmations/ethereum_confirm_count*100" :show-cancel="false"></x-progress>
                                    <span v-if="tx.is_error==0">等待打包</span><span v-else>流通失败</span>
                                </span>
                            </span>
                        </span>
                        <span slot="value" class="tx_amount">{{tx.formatValue}}</span>
                    </cell>
                </div>
            </div>
        </group>
        <nodata   v-if="tx_listlenght<1" :datatip="'暂无数据'"></nodata>
        <box class="page"  v-if="tx_listlenght>0">
            <load-more tip="正在加载" v-show="this.$store.state.page_loading"></load-more>
            <div v-on:click="loadMore">
                <load-more :show-loading="false" :tip="load_more_tip" background-color="#fbf9fe" v-show="!this.$store.state.page_loading"  ></load-more>
            </div>
        </box>

        <popup v-model="tx_detail" height="100%" v-transfer-dom class="wallet-inter-pop">
            <div class="tx_detail">
                <x-header :left-options="{showBack: false,backText:'',preventGoBack:true}" :right-options="{showMore: false}" @on-click-back="closeTxDetail">流通记录 <i  slot="right" v-on:click="closeTxDetail()"  class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;">&#xe617;</i></x-header>
                <div class="tx_detail-top">
                    <div class="detail-top-icon iconfont" v-if="tx_item.is_error==0">&#xe6f6;</div>
                    <div class="detail-top-icon iconfont" style="font-size: 3rem; text-align: center; line-height: 3.65625rem; background: #ff3300;" v-else>&#xe617;</div>
                    <div class="detail_value">{{tx_item.value}} {{tx_item.unit}}</div>
                    <div class="detail_txhash" v-clipboard:copy="tx_item.hash" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(tx_item.hash,12,40,'...')}}  <i class="iconfont">&#xe64b;</i></div>
                    <divider></divider>
                </div>
                <div class="tx_detail-people">
                    <group title="发款方" class="party vux-1px-b">
                        <cell>
                            <span slot="title" class="wallet_address" v-clipboard:copy="tx_item.from" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(tx_item.from,12,20,'...')}} <i class="iconfont">&#xe64b;</i></span>
                        </cell>
                    </group>
                    <group title="转入方"  v-if="tx_item.to" class="party vux-1px-b">
                        <cell>
                            <span slot="title" class="wallet_address" v-clipboard:copy="tx_item.to" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(tx_item.to,12,20,'...')}} <i class="iconfont">&#xe64b;</i></span>
                        </cell>
                    </group>
                    <group title="合约发布"  v-if="tx_item.contractAddress&&!tx_item.to" class="party">
                        <cell>
                            <span slot="title" class="wallet_address">{{tx_item.contractAddress}}</span>
                        </cell>
                    </group>
                </div>
                <group class="tx_detail_block">
                    <cell>
                        <span slot="title" class="tx_detail_title">矿工费用</span>
                        <span class="tx_detail_value">{{tx_item.gasFee}} {{base_coin}}</span>
                    </cell>
                    <cell>
                        <span slot="title" class="tx_detail_title">区块编号</span>
                        <span class="tx_detail_value">{{tx_item.blockNumber}} </span>
                    </cell>
                    <cell>
                        <span slot="title" class="tx_detail_title">流通时间</span>
                        <span class="tx_detail_value">{{tx_item.datetime}} </span>
                    </cell>
                    <cell v-if="tx_item.confirmations>0">
                        <span slot="title" class="tx_detail_title">流通确认</span>
                        <span class="tx_detail_value">{{tx_item.confirmations>=ethereum_confirm_count?'已确认':tx_item.confirmations+"/"+ethereum_confirm_count}} </span>
                    </cell>
                    <cell v-else>
                        <span slot="title" class="tx_detail_title">流通确认</span>
                        <span class="tx_detail_value" v-if="tx_item.is_error==0">等待打包</span>
                        <span class="tx_detail_value" v-else>流通失败</span>
                    </cell>
                </group>

            </div>
        </popup>

        
    </div>
</template>
<script>
    
    import {
        TransferDom,
        XHeader,
        Actionsheet,
        Cell,
        Badge,
        Box,
        LoadMore,
        Popup,
        Divider,
        Msg,
        XProgress
    } from "vux";
    import router from "@/router";
    export default {
        directives: {
            TransferDom
        },
        components: {
            
            "x-header": XHeader,
            actionsheet: Actionsheet,
            cell: Cell,
            badge: Badge,
            box: Box,
            "load-more": LoadMore,
            popup: Popup,
            divider: Divider,
            msg: Msg,
            "x-progress":XProgress,
            nodata:() => import('@/components/nodata'),
        },
        data() {
            var coin_amount = 0;
            var coin_type = "ethereum";
            if (router.currentRoute.params["coin"]) {
                coin_type = router.currentRoute.params["coin"];
            }
            if (this.$store.state.properties.length > 0) {
                coin_amount =
                    this.$store.state.properties_set[coin_type].amount +
                    " " +
                    this.$store.state.properties_set[coin_type].name;
            }
            return {
                page_title: router.currentRoute.meta.title,
                page_name: router.currentRoute.name,
                coin_amount: coin_amount,
                tx_list:{},
                tx_listlenght:1,
                load_more_tip: "点击加载更多",
                page: 0,
                list_end: false,
                tx_detail: false,
                tx_item: {},
                icon: "success",
                unconfirm_tx_list:[], //未确认流通
                unconfirm_tx_items:[],
                ethereum_confirm_count:12,
                base_coin:""
            };
        },
        mounted() {
            var _this = this;
            if (!this.$store.state.wallet.address) {
                this.$store.commit("loadWallets", function() {
                    _this.loadMore();
                    _this.$store.commit("loadProperties", function() {
                        var coin_type = "ethereum";
                        if (router.currentRoute.params["coin"]) {
                            coin_type = router.currentRoute.params["coin"];
                        }
                        _this.coin_amount =
                            _this.$store.state.properties_set[coin_type].amount +
                            " " +
                            _this.$store.state.properties_set[coin_type].name;
                    });
                });
            } else this.loadMore();

            window.js_qr_code_scan = function(qr_code){
                _this.js_qr_code_scan(qr_code);
            }

        },
        methods: {
            goback:function(){
                this.$router.push("/wallet");
            },
            confirmTx:function(){
               var _this = this;
              if(this.unconfirm_tx_list.length>0)
              {
                  setTimeout(function(){
                      _this.$http
                          .post("/api/app.wallet/transaction/confirm", {tx_hashes:_this.unconfirm_tx_list})
                          .then(res => {
                              //更新tx_list同时重新生成unconfirm_tx_list
                              for(var i=0;i<res.data.tx_list.length;i++){
                                  var confirmed_hash = res.data.tx_list[i].hash;
                                  for(var j=0;j<_this.unconfirm_tx_items.length;j++){
                                      if(_this.unconfirm_tx_items[j].hash==confirmed_hash)
                                      {
                                          _this.unconfirm_tx_items[j].confirmations = res.data.tx_list[i].confirmations;
                                          _this.unconfirm_tx_items[j].pending = res.data.tx_list[i].pending;
                                          _this.unconfirm_tx_items[j].is_error = res.data.tx_list[i].is_error;
                                      }
                                  }
                                  //_this.tx_list[confirmed_hash].confirmations = res.data.tx_list[i].confirmations;
                              }
                              if(res.data.confirmed_list.length==_this.unconfirm_tx_list.length){
                                  _this.unconfirm_tx_list = [];
                                  _this.unconfirm_tx_items = [];
                                  console.log(_this.tx_list);
                              }
                              _this.confirmTx();
                              console.log(res);
                          })
                          .catch(err => {
                              _this.$store.state.page_loading = false;
                              console.log(err);
                          });
                  },5000);

              }
            },
            showTx: function(hash) {
                console.log(this.tx_list[hash]);
                this.tx_detail = true;
                this.tx_item = this.tx_list[hash];
            },
            closeTxDetail: function() {
                this.tx_detail = false;
            },
            loadMore: function() {
                //下一页
                if (!this.list_end) {
                    this.page++;
                    var coin_type = "ethereum";
                    if (router.currentRoute.params["coin"]) {
                        coin_type = router.currentRoute.params["coin"];
                    }
                    if (!this.$store.state.page_loading) {
                        this.$store.state.page_loading = true;
                        this.$http
                            .post("/api/app.wallet/transaction/txlistdb", {
                                address: this.$store.state.wallet.address,
                                page: this.page,
                                coin: coin_type
                            })
                            .then(res => {
                                this.$store.state.page_loading = false;
                                this.base_coin = res.data.base_coin;
                                this.ethereum_confirm_count = res.data.ethereum_confirm_count;
                                if (res.data.tx_list.length == 0) {

                                    this.list_end = true;
                                    this.load_more_tip = "到底了";
                                } else {
                                    if (res.data.tx_list.length < 30) {
                                        this.list_end = true;
                                        this.load_more_tip = "到底了";
                                    }
                                    for (var i = 0; i<res.data.tx_list.length;i++){
                                        this.tx_list[res.data.tx_list[i].hash] = res.data.tx_list[i];
                                        if(res.data.tx_list[i].confirmations<this.ethereum_confirm_count&&res.data.tx_list[i].is_error==0){
                                            this.unconfirm_tx_list.push(res.data.tx_list[i].hash);
                                            this.unconfirm_tx_items.push(res.data.tx_list[i]);
                                        }
                                    }
                                }
                                if(isNaN(this.tx_list)){
                                    var _length = 0;
                                    for(var k in this.tx_list)
                                    {
                                        _length++;
                                    }
                                    this.tx_listlenght = _length;
                                }else{
                                    this.tx_listlenght = 0;
                                }
                                console.log(res);
                                console.log(this.unconfirm_tx_list);
                                this.confirmTx();
                            })
                            .catch(err => {
                                this.$store.state.page_loading = false;
                                console.log(err);
                            });
                    }
                }
            },
            open_scan(){
                App.qr_code_scan();
                var _this = this;
                window.js_qr_code_scan = function(qr_code){
                    if(qr_code.substr(0,2)=="0x")
                    {
                        var coin_type = "ethereum";
                        if (router.currentRoute.params["coin"]) {
                            coin_type = router.currentRoute.params["coin"];
                        }
                        var path = "/wallet_international/send/"+coin_type+"?address="+qr_code;
                        _this.$router.push({path:path});
                    }
                    else{
                        qr_code = qr_code.replace(_this.$store.state.init.route_domain+"/#","");
                        _this.$router.push({path:qr_code});
                    }
                };
            },
            onCopy: function (e) {
                this.$vux.toast.text("复制成功");
                //console.log('You just copied: ' + e.text)
            },
            onError: function (e) {
                this.$vux.toast.text("复制失败，您可以尝试手动记录");
                //console.log('Failed to copy texts')
            }
        }
    };
</script>
<style lang="less" scoped>
    @import "~vux/src/styles/1px.less";
    #wallet_international_detail {
        color: #363840;
        .d-tit {
            background-color: #f6f6f6;
            padding: 0 0.625rem;
            line-height: 2rem;
            font-size: 0.68rem;
            color: #888;
        }
        .main_card {
            padding: 2.381rem 0;
            width: 100%;
            text-align: center;
            background-color: #fff;
            line-height: 2rem;
            margin-bottom: 0.625rem;
        }
        .wallet_amount {
            font-size: 1.875rem;
        }
        .wallet_name {
            font-size: 1.5rem;
        }
        .wallet_address {
            overflow: hidden;
            margin: 0 auto;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.75rem;
            color: #999;
            .iconfont{
                font-size: 0.75rem;
            }
        }
        .address_hash {
            font-family: arial;
            line-height: 1.5rem;
            font-size: 0.625rem;
            span{
                display: block;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 0.8rem;
                line-height: 1.5rem;
            }
            .tx_list_key{
                color: #363840;
            }
            .tx_list_time{
                color: #999;
            }
        }
        .tx_icon {
            font-size: 2rem;
            line-height: 3rem;
            display: inline-block;
            width: 3rem;
            float: left;
        }
        .icon-shouru {
            color: #5193ff;
        }
        .icon-zhichu {
            color: #ff744a;
        }
        .page {
            height: 80px;
        }
        .tx_list {
            // border-bottom: solid 1px #ebeef5;
            &:first-child{
                .tx_list-txt{
                    &::before{
                        display: none;
                    }
                }
            }
            .tx_list-con{
                padding-left: 0.9375rem;
                .iconfont{
                    font-size: 1.625rem;
                }
                .formatValue_add{
                    color: #ff586b;
                }
                .formatValue_reduce{
                    color: #7ed321;
                }
            }
        }
        .tx_detail_value {
            width: 70%;
            overflow: hidden;
            margin: 0 auto;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.8rem;
        }
        .tx_detail_title {
            font-size: 0.8rem;
        }
        .tx_amount {
            font-size: 1rem;
            color: #363840;
            font-weight: bold
        }
    }
    .vux-popup-dialog {
        color: #363840;
        background: #f4f4f4;
        .tx_detail {
            width: 100%;
            .tx_detail-top {
                background-color: #fff;
                padding-top: 1.875rem;
                .detail-top-icon {
                    margin: 0 auto 1rem;
                    font-size: 3.65625rem;
                    width: 3.65625rem;
                    height: 3.65625rem;
                    border-radius: 50%;
                    background-color: #4887f6;
                    color: #fff;
                }
            }
            .party {
                padding: 1rem 0;
                &:last-child {
                    &::after {
                        display: none;
                    }
                }
            }
            .detail_value {
                font-size: 2.25rem;
                text-align: center;
                line-height: 2.8125rem;
                font-family: arial;
            }
            .detail_txhash {
                font-size: 0.8rem;
                text-align: center;
                width: 70%;
                overflow: hidden;
                margin: 0 auto;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #999;
            }
        }
    }

</style>
