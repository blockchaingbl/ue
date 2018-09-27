<template lang="html">
    <div :id="page_name">
        <pull-to :top-load-method="refresh" :is-bottom-bounce="false" :is-top-bounce="ontop"  style="z-index: 0">
            <x-header :left-options="{showBack: true,backText:'',preventGoBack:true}" :right-options="{showMore: false}" @on-click-back="goback"  style="z-index:1;" class="inter_wallet_header">{{page_title}}
                <i slot="right" v-on:click="showMenus()" class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:0.875rem;">&#xe725;</i>
            </x-header>
            <!-- <div class="main_card">
                <div class="wallet_iocn"><img src="@/assets/images/walleticon.png" alt=""></div>
                <div class="wallet_name"  v-on:click="showQrcode()">{{this.$store.state.wallet.name}}  <i class="iconfont">&#xe708;</i> </div>
                <div class="wallet_address" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(this.$store.state.wallet.address,12,20,'...')}} <i class="iconfont">&#xe64b;</i></div>
                <div class="bottom_bar" v-show="$store.state.init.token_add">
                    <div class="total_money">
                        <div class="total">
                            ≈
                            <span class="num">
                            <inline-loading v-show="$store.state.balance_loading"></inline-loading>
                        </span>
                            <span class="num" v-show="$store.state.balance_loading==false">{{($store.state.properties_amount).toFixed(2)}}</span>
                        </div>
                        <div class="text">总资产 ( ¥ ) </div>
                    </div>
                    <i class="iconfont token_add" v-on:click="showTokenList()">&#xe637;</i>
                </div>
            </div> -->
            <div class="wallet-card-box">
                <div class="wallet-card">
                    <div class="wallet-info flex-box">
                        <div class="wallet-img">
                            <img src="@/assets/images/act_wallet_icon.png" alt="">
                        </div>
                        <div class="wallet-info-text flex-1">
                            <div class="wall-name" v-on:click="showQrcode()">{{this.$store.state.wallet.name}}  <i class="iconfont">&#xe708;</i></div>
                            <div class="wallet-address" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(this.$store.state.wallet.address,12,20,'...')}} <i class="iconfont">&#xe71f;</i></div>
                        </div>
                    </div>
                    <div class="wallet-assets" v-show="$store.state.balance_loading==false"><em>&yen;</em> {{($store.state.properties_amount).toFixed(2)}}</div>
                </div>
            </div>
            <div class="total-assets flex-box">
                <div class="total-assets-txt flex-1">资产</div>
                <div class="iconfont token_add" v-on:click="showTokenList()">&#xe64d;</div>
            </div>
            <!--<div class="wallet-title">资产明细</div>-->
            <div class="wallet-box"  v-show="!this.$store.state.balance_loading">
                <router-link  class="item flex-box vux-1px-b"  v-for="(property, index) in this.$store.state.properties" :key="index" :to="'/wallet_international/detail/'+property.type">
                    <div class="item-img">
                        <!--  <img :src="" alt=""> -->
                        <img :src="property.icon" alt="">
                    </div>
                    <div class="item-info flex-1">
                        <div class="item-title flex-box">
                            <div class="title-text flex-1">{{property.name}}</div>
                            <div class="item-money flex-box">
                                <div class="item-type">
                                    {{property.amount}}
                                </div>
                                <div class="item-decs flex-box" v-if="property.amount>0&&property.price>0" v-show="$store.state.init.token_add">
                                    <div class="item-decs-list">≈&nbsp;&nbsp;¥&nbsp;{{(property.amount*property.price).toFixed(2)}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>

            <box class="page" v-show="this.$store.state.balance_loading">
                <load-more tip="正在加载" v-show="this.$store.state.balance_loading"></load-more>
            </box>
        </pull-to>

        <popup v-model="showRightBar" position="right" style="width:70%;background:#fff;"  v-transfer-dom>
            <div style="width:100%; padding-top:30px;">
                <group title="钱包列表">
                    <radio v-model="wallet_address" :options="wallets" class="right_item">
                        <template slot-scope="props" slot="each-item">
                            <p>
                                <img src="@/assets/images/act_icon_wallet.png" class="vux-radio-icon"> {{ props.label }}
                            </p>
                        </template>
                    </radio>
                </group>
                <box gap="25px 10px">
                    <x-button class="right_item  rbt" style="background-color:#5871ff;border-radius:99px;" type="primary" @click.native="create_wallet">创建钱包</x-button>
                    <x-button class="right_item rbt" style="background-color:#ff6678;border-radius:99px;" type="primary" @click.native="import_wallet">导入钱包</x-button>
                    <x-button class="right_item rbt" style="background-color:#fff;padding:0;" type="default" @click.native="manage_wallet">管理钱包</x-button>
                </box>
            </div>
        </popup>

        <popup v-model="showTokenListPage" position="right" style="width:100%;background:#fff;" v-transfer-dom>
            <x-header :left-options="{showBack: true,backText:'',preventGoBack:true}" :right-options="{showMore: false}" style="z-index:1;" @on-click-back="hideTokenList()" v-show="!isShowSearch">添加新资产
                <i slot="right" v-on:click="showSearch()" class="weui-icon-search" style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;"></i>
            </x-header>
            <search placeholder="搜索Token名称或合约地址" v-model="keyword" v-show="isShowSearch"  @on-submit="search" @on-cancel="cancel" ref="search_token" :auto-fixed="false" class="token-search"></search>
            <load-more tip="正在处理中 . . ." v-if="loading"></load-more>
            <div class="page-tokenlist page-tokenlist-inter">
                <scroller @on-scroll-bottom="onScrollBottom" height="-52" lock-x ref="scrollerEvent" v-if="token_lists.length>0">
                    <div class="task-block">
                        <div class="item flex-box vux-1px-b" v-for="token in token_lists">
                            <div class="item-img">
                                <img :src="token.icon" alt="">
                            </div>
                            <div class="item-info flex-1">
                                <div class="item-title flex-box">
                                    <div class="item-money flex-box">
                                        <div class="item-decs flex-box">
                                            <div class="item-type">{{token.token_symbol}}</div>
                                        </div>
                                        <div class="item-decs flex-box">
                                            <div class="item-decs-list">{{token.token_name}}</div>
                                        </div>
                                        <div class="item-decs flex-box">
                                            <div class="item-decs-list">{{(token.contract_address).substr(0,8)+"..."+(token.contract_address).substr(-8,8)}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <group class="item-decs flex-box" v-if="!isShowSearch">
                                <inline-x-switch v-model="token.status" :value-map="[0,1]" @on-change="subscribeSet(token.id)"></inline-x-switch>
                            </group>
                            <div class="item-decs flex-box" v-else>
                                <x-button mini type="primary" v-if="token.status!=1" @click.native="subscribeSet(token.id)">添加</x-button>
                                <span class="status-txt" v-else>已添加</span>
                            </div>
                        </div>
                        <load-more tip="正在加载 . . ." v-if="loading"></load-more>
                        <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
                    </div>
                </scroller>
                <nodata  v-else :datatip="'暂无数据'"></nodata>
                
            </div>
        </popup>


        <popup v-model="qrcodeshow" height="100%" position="top" is-transparent v-transfer-dom>
            <div style="width: 240px;background-color:#fff;height:220px;margin:150px auto 0;border-radius:5px;padding-top:20px; text-align: center; z-index: 999;">
                <qrcode :value="this.$store.state.wallet.address" class="qrcode"></qrcode>
                <div style="line-height:30px; height:30px;">{{shortStr(this.$store.state.wallet.address,12,20,'...')}}</div>
                <div style="padding:20px 15px; ">
                    <x-button type="primary" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError" style="font-size:14px;">复制</x-button>
                    <x-button @click.native="qrcodeshow = false" style="font-size:14px;">关闭</x-button>
                </div>
            </div>
        </popup>

    </div>
</template>
<script>
    import { TransferDom, XHeader,Actionsheet,Cell,Badge,LoadMore,Popup,Radio,XButton,Box,Scroller,Divider,InlineLoading,InlineXSwitch,Search,Qrcode,XSwitch} from 'vux';
    import router from '@/router';
    import PullTo from '@/components/fw_pull';
    import Nodata from "@/components/nodata";
    export default {
        directives: {
            TransferDom
        },
        components: {
            "x-header":XHeader,
            "actionsheet":Actionsheet,
            "cell":Cell,
            "badge":Badge,
            "load-more":LoadMore,
            "popup":Popup,
            "radio":Radio,
            "x-button":XButton,
            "box":Box,
            "pull-to":PullTo,
            "qrcode":Qrcode,
            Scroller,
            Divider,
            LoadMore,
            Nodata,
            InlineLoading,
            InlineXSwitch,
            Search,
            "x-switch": XSwitch,
        },
        data () {
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                iconfont:"iconfont",
                showRightBar:false,
                wallets:[],
                wallet_address:"",
                //ad:this.$store.state.wallet.address,
                showTokenListPage:false,
                token_lists:[],
                formData:{page:1},
                lock:false,
                loading:0,
                keyword:'',
                isShowSearch:false,
                qrcodeshow:false,
                ontop:true,
                displayType:0
            }
        },
        mounted () {
            var _this = this;
            this.$store.commit("loadWallets",function(){
                _this.refresh_wallet();
            });
        },
        methods: {
            goback:function(){
                this.$router.push("/user/center");
            },
            showQrcode:function(){
                this.qrcodeshow = true;
            },
            refresh_wallet:function(){
                if(this.$store.state.wallets)
                {
                    for(var address in this.$store.state.wallets){
                        var wallet_item = {key:address,value:this.$store.state.wallets[address].name};
                        this.wallets.push(wallet_item);
                    }
                    this.wallet_address = this.$store.state.wallet.address;
                }
                else
                {
                    router.replace("wallet/create");
                }
            },
            walletItemClass: function (wallet_item) {
                var cls = this.$store.state.wallet.address==wallet_item.address?'current':''
                return cls;
            },
            showMenus:function(){
                this.showRightBar = true;
            },
            create_wallet:function(){
                router.push("wallet_international/create");
            },
            import_wallet:function(){
                router.push("wallet_international/import");
            },
            manage_wallet:function(){
                router.push("wallet_international/manage");
            },
            refresh:function(loaded){
                this.$store.commit("loadProperties",function(){
                    loaded('done')
                });
            },
            onCopy: function (e) {
                this.$vux.toast.text("复制成功");
                //console.log('You just copied: ' + e.text)
            },
            onError: function (e) {
                this.$vux.toast.text("复制失败，您可以尝试手动记录");
                //console.log('Failed to copy texts')
            },
            showTokenList:function(){
                this.showTokenListPage = true;
                this.loadTokenList();
            },
            hideTokenList:function(){
                this.showTokenListPage = false;
                this.clearListData();
                this.$store.commit("loadProperties");
            },
            loadTokenList:function (keyword){
                if(this.lock){
                    return false;
                }
                this.lock = true;
                this.loading = 1;
                this.formData.keyword = keyword;
                this.formData.address = this.$store.state.wallet.address;
                this.$http.post('/api/app.wallet/token',this.formData).then(res => {
                    if(res.data.tokens.length>0){
                        this.token_lists =  this.token_lists.concat_unk(res.data.tokens,"id");
                        this.lock = false;
                    }
                    this.loading = 0;
                    this.formData.page++;
                })
                    .catch(error=>{
                        this.loading = 0;
                    })
            },
            onScrollBottom:function(){
                if(!this.lock){
                    if(this.isShowSearch){
                        this.searchTokenList();
                    }else{
                        this.loadTokenList();
                    }
                }
            },
            subscribeSet:function(id){
                var _this = this;
                this.$http.post('/api/app.wallet/token/subscribe',{token_id:id,address:this.$store.state.wallet.address}).then(res => {
                    if(res.errcode=="0"){
                        if(_this.isShowSearch){
                            _this.clearListData();
                            _this.searchTokenList();
                        }
                        // this.$vux.toast.text(err.message);
                    }
                })
                    .catch(error=>{
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                    })
            },
            showSearch:function(){
                this.$refs.search_token.setFocus();
                this.isShowSearch = true;
                this.clearListData();
            },
            search:function(){
                this.clearListData();
                this.searchTokenList();
            },
            cancel:function(){
                this.isShowSearch = false;
                this.clearListData();
                this.loadTokenList();
            },
            searchTokenList:function(){
                if(this.lock){
                    return false;
                }
                this.lock = true;
                this.loading = 1;
                this.$http.post('/api/app.wallet/token/search',{keyword:this.keyword,address:this.$store.state.wallet.address}).then(res => {
                    if(res.data.tokens.length>0){
                        this.token_lists =  this.token_lists.concat_unk(res.data.tokens,"id");
                        this.lock = false;
                    }
                    this.loading = 0;
                    this.formData.page++;
                })
                    .catch(error=>{
                        this.loading = 0;
                    })
            },
            clearListData:function(){
                this.token_lists = [];
                this.lock = false;
                this.formData.page = 1;
            }
        },
        watch:{
            wallet_address:{
                handler(currentAddress,lastAddress){
                    //无上次地址表示首次进入，当已加载过不加载
                    if(!lastAddress&&this.$store.state.properties.length>0)return;

                    this.$store.commit("changeWallet",currentAddress);
                    this.$store.commit("loadProperties");
                    this.showRightBar = false;
                },

            },
            $route() {
                this.$store.commit("loadWallets",function(){
                    _this.refresh_wallet();
                });
                this.$store.commit("loadProperties");

            }
        }
    }
</script>
<style lang="less" scoped>
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    #wallet_international{
        min-height: 100%;
        background-color: #fff;
        color: #363840;
        .main_card{
            padding:1rem 0;
            width:100%;
            text-align:center;
            background-image: url("../../assets/images/walletbg.png");
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: center bottom;
            background-color: #2e3d6c;
            line-height: 2rem;
            color:#fff;
            .bottom_bar{
                margin: 1rem 1.4rem 0 1.4rem;
                height: 50px;
                .total_money{
                    float: left;
                    .total{
                        height: 30px;
                        line-height: 30px;
                        text-align: left;
                        .num{
                            margin-left: 5px;
                            font-size: 20px;
                        }
                    }
                    .text{
                        height: 20px;
                        line-height: 20px;
                        text-align: left;
                    }
                }
                .token_add{
                    float: right;
                    font-size: 30px;
                    margin-top: 15px;
                }
            }
        }
        .wallet-card-box {
            background: #5871ff;
            padding: 0.625rem 0.9375rem;
            margin-bottom: 2.5rem;
            .wallet-card {
                height: 7.125rem;
                margin-bottom: -2.3125rem;
                position: relative;
                z-index: 1;
                background: #fff;
                box-shadow: 0 5px 10px 0 rgba(0,0,0,0.05);
                border-radius: 4px;
                padding: 1rem 0.9375rem 0.6875rem;
            }
            .wallet-info {
                margin-bottom: 0.875rem;
                .wallet-img {
                    width: 2.25rem;
                    height: 2.25rem;
                    margin-right: 0.625rem;
                    img {
                        display: block;
                        height: 100%;
                        width: 100%;
                    }
                }
                .wall-name {
                    font-size: 1.125rem;
                    line-height: 1.5rem;
                }
                .wallet-address {
                    font-size: 0.75rem;
                    line-height: 1rem;
                    color: #858e97;
                    .iconfont{
                        font-size: 0.75rem;
                    }
                }
            }
            .wallet-assets {
                font-size: 1.875rem;
                text-align: right;
                em {
                    font-size: 0.875rem;
                    line-height: 0.875rem;
                }
                
            }
        }
        .total-assets {
            padding: 0 0.9375rem;
            line-height: 2.8125rem;
            .total-assets-txt{
                font-size: 1rem;
            }
            .iconfont{
                font-size: 0.875rem;
            }
        }
        .wallet_iocn{
            width:2.25rem;
            margin:0 auto;
            img{
                width: 100%;
            }
        }
        .wallet_name{ font-size:1.2rem; }
        .wallet_address{
            overflow: hidden; margin: 0 auto;
            text-overflow:ellipsis;
            white-space: nowrap;
            font-size:.8rem;
            color: #a3b5d5;
        }
        .page{height:80px;}

        .property_row{ color:#333!important;}
        .wallet-title{
            background-color:#f6f6f6;
            padding:0 .625rem;
            line-height:2rem;
            font-size:.68rem;
            color: #888;
        }
        .wallet-box{
            background: #fff;
            .item {
                height: 4.0625rem;
                background: #fff;
                border-radius: 4px;
                padding: 0 0.9375rem;
                // .item-info{
                //     height: 3.75rem;
                // }
                &::after{
                    left: 0.9375rem;
                }
                .item-img {
                    width: 2.125rem;
                    height: 2.125rem;
                    margin-right: @fs-small;
                    img {
                        width: 100%;
                        height: 100%;
                        display: block;
                    }
                }
                .item-title {
                    line-height: 1.5rem;
                    .has {
                        color: #888;
                        span {
                            color: #4c4c51;
                        }
                    }
                }
                .item-money {
                    flex-direction: column;
                    align-items: flex-end;
                }
                .title-text {
                    font-size: 1rem;
                    color: #363840;
                }
                .item-type {
                    font-size: 1.25rem;
                    line-height: 1.1875rem;
                    color: #363840;
                }
                .nohas {
                    font-weight: bold;
                    color: #fc8c92;
                    line-height: 1.6rem;
                }
                .item-decs {
                    flex-wrap: wrap;
                }
                .item-decs-list {
                    padding-left: 0.5rem;
                    font-size: 0.875rem;
                    line-height: 1.0625rem;
                    color: #999;
                    position: relative;
                    list-style-type: none
                }
            }

        }
    }
    .page-tokenlist{
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
                height: 5.2rem;
                background: #fff;
                
                padding: 0 0.9375rem;
                // .item-info{
                //     height: 3.75rem;
                // }
                &::after{
                    left: 4rem;
                }
                &:nth-child(1){
                    &::after{
                        display: none;
                    }
                }
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
                    align-items: flex-start;
                }
                .title-text {
                    font-size: 1rem;
                    color: #363840;
                }
                .item-type {
                    padding-left: 0.5rem;
                    font-size: 1.25rem;
                    line-height: 1.8rem;
                    color: #363840;
                    position: relative;
                    list-style-type: none;
                }
                .nohas {
                    font-weight: bold;
                    color: #fc8c92;
                    line-height: 1.6rem;
                }
                .item-decs{
                    flex-wrap: wrap;
                    .status-txt{
                        color: #999999;
                    }
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
    .token-search{
        line-height: 1.65rem;
    }
</style>

