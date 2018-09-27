<template lang="html">
<div class="userwallet" style="height:100%;">
        <div class="hasWallet-box"  v-if="hasWallet==1">
            <div class="wallet-head flex-box">
                <div class="head-top flex-box">
                 <!--    <div class="user-btn">
                        <i class="iconfont">&#xe6e0;</i>
                        <input type="checkbox" class="wallet-pup-int"  v-model="show1">
                    </div> -->
                    <div class="scan" v-on:click="open_scan()" v-show="$store.state.init.is_app">
                        <i class="iconfont">&#xe6e1;</i>
                    </div>
                </div>
                <div class="money-numb">{{vc_amount}}&nbsp;{{$store.state.init.coin_uint}}</div>
                <div class="wallet-private_key" v-clipboard:copy="address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{address}}</div>
                <router-link to="/user/wallet/manage">
                <div class="backups-btn flex-box">
                    &nbsp;&nbsp;&nbsp;<span>管理</span>&nbsp;&nbsp;<i class="iconfont">&#xe78b;</i>
                </div>
                </router-link>
            </div>
            <div class="wallet-record">
                <div class="wallet-record-box">
                    <div class="record-item vux-1px-b" v-for="(item,index) in list" :key="index">
                        <div class="record-title flex-box">
                            <div class="record-numb flex-1">{{item.vc_amount}}</div>
                            <div class="record-time">{{item.create_time}}</div>
                        </div>
                        <div class="record-decs">{{item.log}}</div>
                        <div class="record-decs" v-if="item.memo.length>0">备注：{{item.memo}}</div>
                    </div>
                </div>
            </div>
            <nodata  v-if="list.length<1" :datatip="'暂无数据'"></nodata>
        </div>
        <div v-transfer-dom>
            <actionsheet v-model="show1" :menus="menus1" @on-click-menu="demodoClose">
            </actionsheet>
        </div>

    <div class="noWallet-box" v-if="hasWallet==0">
        <div class="noWallet">
            <div class="noWallet-img">
                <img src="@/assets/images/icon_noWallet.png" alt="">
            </div>
            <p>暂未绑定{{$store.state.init.coin_uint}}钱包</p>
        </div>
        <div class="noWallet-btn-box">
            <box gap="10px 2.5rem">
                <x-button type="primary" style="border-radius:99px;height:2.375rem;" class='found-btn' link="/user/wallet/create">创建钱包</x-button>
                <x-button type="primary" style="border-radius:99px;height:2.375rem;" class='import-btn'  link="/user/wallet/import">导入钱包</x-button>
            </box>
        </div>
    </div>
    <walletbar v-if="hasWallet==1"></walletbar>
</div> 
</template>
<script>
import { TransferDom, Actionsheet, LoadMore } from 'vux';
import { Scroller } from 'vux';
import WalletBar from "@/components/user_wallet";
import Nodata from "@/components/nodata";
export default {
    directives: {
        TransferDom
    },
    components: {
        //stepone:() => import('@/views/login/inc/stepone'),
        Actionsheet,
        Scroller,
        LoadMore,
        walletbar: WalletBar,
        Nodata
    },
    data () {
        return {
            hasWallet:null,
            show1: false,
            menus1: {
                menu1: '重置流通密码',
                menu2: '退出当前钱包账户'
            },
            bottomCount: 6,
            scrollTop: 0,
            onFetching: false,
            scro:true,
            loading:'加载中',
            pull:true,
            address:'',
            vc_amount:'',
            list:[],
            _length:'',
        }
    },
    mounted () {
        this.getInfo();
        this.getTransfer();
        const _this = this;
        window.js_qr_code_scan = function(qr_code){
            _this.js_qr_code_scan(qr_code);
        }
    },
    methods:{
        onClick () {
        },
        demodoClose (menuKey) {
            if(menuKey=='menu1'){
                window.location.href = "#/user/center";
            }else if(menuKey=='menu2'){
                this.$vux.toast.text('退出成功');
            }else{
                this.$vux.toast.text('失败请重试');
            }
        },
        onScrollBottom () {
          if (this.onFetching) {
            // do nothing
          } else {
            this.onFetching = true
            if(this.bottomCount<10){
                setTimeout(() => {
                  this.bottomCount += 10
                  this.$nextTick(() => {
                    this.$refs.scrollerBottom.reset()
                  })
                  this.onFetching = false
                }, 2000)
            }else{
                this.scro=false;
                this.loading='暂无数据'
            }
            
          }
        },
        onScrollTop(){
            setTimeout(() => {
            }, 2000)
        },
        getInfo(){
            var loading = this.$loading();
              this.$http.post('/api/app.user/account/info',{}).then(res => {
                    console.log(res.data);
                    this.address=res.data.account_info.address;
                    this.vc_amount=res.data.account_info.vc_amount;
                    if (res.data.account_info.address) {
                        this.hasWallet=1;
                    }else{
                        this.hasWallet=0;
                    }
                   loading.close();
                }).catch(err => {
                   loading.close();
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
        },
        getTransfer(){
              this.$http.post('/api/app.wallet/wallet/transfer',{
                page:1,
              }).then(res => {
                    console.log(res.data);
                    this.list=res.data.transfer_list;
                    this._length=res.data.transfer_list.length;
                    console.log(this._length);
                    if(this._length==0){
                        this.scro=false;
                        this.loading='暂无数据'
                    }
                }).catch(err => {
                    if (err.errcode) {
                        this.$vux.toast.text(err.message);
                    }
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
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
        open_scan(){
            App.qr_code_scan();
        },
        js_qr_code_scan(qr_code){
            this.$router.push({path:qr_code});
        }
    }
}


</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .userwallet{
        background-color: #fff;
    }
    .wallet-head{
        padding: 10px;
        background-color: #2e3d6c;
        background-image: url(../../assets/images/walletbg.png);
        background-repeat: no-repeat;
        background-position: bottom center;
        background-size: 100%;
        flex-direction: column;
        color: #fff;
        .head-top{
            flex-shrink:0;
            width: 100%;
            align-items: flex-start;
            position: relative;
            .scan{
                position: absolute;
                right: 0;
                top: 0;
            }
            .iconfont{
                font-size: 1.375rem;
                line-height: 1.5rem;
            }
            .user-btn{
                position: relative;
                input{
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }
            }
            .fangkuai{
                height: 5rem;
            }
            .lingxing{
                margin: 1.25rem auto 0;
                background: #b8ab3f;
                width: 2.75rem;
                height: 2.75rem;
                -moz-transform:rotate(45deg);
                -webkit-transform:rotate(45deg);
                -o-transform:rotate(45deg);
                transform:rotate(45deg);
            }
        }
        .wallet-private_key{
            margin:0.625rem 0;
            text-align: center;
            line-height: 1rem;
            min-height: 1rem;
            word-wrap: break-word;
            width: 100%;
            color: #a3b5d5;
            font-size: 0.8125rem;
        }
        .backups-btn{
            color: #fff;
            height: 1.375rem;
            line-height: 1.375rem;
            padding: 0 0.5rem 0 0.625rem;
            background: rgba(0,0,0,0.3);
            border-radius: 0.75rem;
            font-size: @fs-small;
            i{
                font-size: 0.625rem;
            }
        }
        .bcty{
            font-size: @fs-small;
            line-height: 1.25rem;
            margin-top: 1.125rem;
            color: #8cb6f3;
        }
        .money-numb{
            font-size: 1.875rem;
            font-family: arial;
            line-height: 2.5rem;
            height: 2.5rem;
        }
    }
    .wallet-btn-box{
        background: #fff;
        padding: 0.75rem 0.625rem;
        .wallet-btn{
            width: 10.75rem;
            height: 3.625rem;
            line-height: 3.625rem;
            background-size: 100% 100%;
            text-align: center;
            
            font-size: 1.125rem;
            color: #fff;
            font-weight: bold;
            img{
                height: 3.625rem;
            }
        }
        .send-btn{
            margin-right: 0.75rem;
            background: url(../../assets/images/send_btn.png) no-repeat top center;
            background-size: 100% 100%;
            padding-left: 1.375rem;
        }
        .receive-btn{
            background: url(../../assets/images/receive_btn.png) no-repeat top center;
            background-size: 100% 100%;
            padding-left: 1.5rem;
            img{
                margin-right: 0.5rem;
            }
        }
    }
    .wallet-record {
        background: #f4f4f4;
        padding-top: 0.625rem;
        color: #888;
        .wallet-record-box{
            padding-bottom: 1.25rem;
            background-color: #fff;
        }
        .record-item {
            margin-left: 0.625rem;
            padding-right: 0.625rem;
            padding: 0.75rem 0.625rem 0.75rem 0;
            font-family: arial;
            line-height: 1.25rem;
            .record-numb {
                font-weight: bold;
                color: #363840;
                font-size: 1rem;
            }
            .record-decs {
                line-height: 1.25rem;
                word-wrap: break-word;
                word-break:break-all;
                font-size: 0.625rem;
                color: #363840;
            }
            .record-time{
                font-size: 0.625rem;
            }
        }
        // .weui-loadmore {
        //     width: 100%;
        //     padding: 1rem 0 0;
        //     line-height: 1rem;
        //     font-size: 0.875rem;
        //     text-align: center;
        // }
        // .weui-loadmore_line{
        //     padding: 1.25rem 0 0;
        //     margin: 0 auto;
        //     width: 65%;
        // }
        // .vux-loadmore.weui-loadmore_line:before, .vux-loadmore.weui-loadmore_line:after{
        //     top: 0.5rem;
        // }
        // .weui-loadmore_line .weui-loadmore__tips{
        //     top: 0;
        // }
    }
    .noWallet-box{
        background-color: #2e3d6c;
        height: 100%;
        font-family: Arial, "Microsoft YaHei";
    }
    .noWallet{
        text-align: center;
        padding-top: 8.75rem;
        .noWallet-img{
            width: 6.625rem;
            margin: 0 auto 0.875rem;
            img{
                width: 100%;
            }
        }
        p{
            color: #fff;
            line-height: 1.5rem;
            font-size: 1.125rem;
        }
    }
    .noWallet-btn-box{
        position: absolute;
        bottom: 0.8125rem;
        left: 0;
        right: 0;
        .found-btn{
            background:-webkit-linear-gradient(left,#2883d3,#1e65c2);
            background:linear-gradient(to right,#2883d3,#1e65c2);
            font-size: 0.875rem;
        }
        .import-btn{
            background:none;
            font-size: 0.875rem;
        }
    }
</style>
