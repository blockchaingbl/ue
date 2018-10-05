<template lang="html">
<div class="page-bank-index" >
    <div class="content">
        <div class="detail-con" v-if="!loadings">
            <tab :line-width="2" :animate="false">
                <tab-item selected><span>资产宝</span></tab-item>
                <tab-item @click.native="tab()"><span>我的聚宝</span></tab-item>
            </tab>
            <div class="con-box" v-if="current_list.length>0||fixed_list.length>0">
                <div class="profit-block" v-if="current_list.length>0">
                    <div class="profit-title vux-1px-b">活期</div>
                    <router-link :to="{path: '/finance/current_detail/'+item.id}" class="profit-item flex-box" v-for="(item,index) in current_list" :key="index">
                        <div class="profit-item-item">
                            <div class="item-item-title income-rate">{{item.year_rate}}%</div>
                            <div class="item-item-decs">年化创收率</div>
                        </div>
                        <div class="profit-item-item flex-1">
                            <div class="item-item-title">{{item.name}}</div>
                            <div class="item-item-decs" v-if="item.is_per_limit==0">随存随取 | 不限额</div>
                            <div class="item-item-decs" v-if="item.is_per_limit==1">随存随取 | 限购{{item.per_limit}}{{item.coin_unit}}</div>
                        </div>
                        <div class="profit-item-item t_r">
                            <div class="item-item-decs">
                                <div  class="item-item-btn">抢购</div>
                            </div>
                        </div>
                    </router-link>
                </div>
                <div class="profit-block" v-if="fixed_list.length>0">
                    <div class="profit-title vux-1px-b">定期</div>
                    <div class="profit-item flex-box"  v-for="(item,index) in fixed_list" :key="index">
                        <div class="profit-item-item">
                            <div class="item-item-title income-rate">{{item.year_rate}}%</div>
                            <div class="item-item-decs">年化创收率</div>
                        </div>
                        <div class="profit-item-item flex-1">
                            <div class="item-item-title">{{item.name}}</div>
                            <div class="item-item-decs" v-if="item.is_per_limit==0">{{item.time_length}} 天 | 不限额</div>
                            <div class="item-item-decs" v-if="item.is_per_limit==1">{{item.time_length}} 天 | 限购{{item.per_limit}}{{item.coin_unit}}</div>
                        </div>
                        <div class="profit-item-item t_r item-btn flex-box">
                            <div class="item-item-decs">
                                <div  class="item-item-btn" v-on:click="open_deploy($event,item.id,item.buy_limit,item.is_limit,item.buy_limit_less,item.vc_total,item.is_per_limit,item.can_buy,item.coin_unit)">抢购</div>
                            </div>
                        </div>
                        <router-link :to="{path: '/finance/detail/'+item.id}" class="item-link"></router-link>
                    </div>
                </div>
            </div>
            
        </div>
        <div v-transfer-dom>
            <loading :show="loadings"></loading>
        </div>
    </div>
    
    <!--定期产品买入-->
    <popup class="pop-deposit-deploy" v-model="showDeploy" position="bottom" style="width:100%;background:#fff;" @on-hide="close_deploy" v-transfer-dom>
        <popup-header
                left-text=""
                right-text=""
                title="存放"
                :show-bottom-border="false"
                @on-click-left="show1 = false"
                @on-click-right="showDeploy = false">
        </popup-header>
        <group>
            <cell class="vux-1px-b" title="可用数额" :value="vc_totals+' '+coin_unit"></cell>
            <!--<div class="deposit-deploy-tis" v-if="this.is_limits==1">还可购买 {{can_buy}} {{coin_unit}}</div>-->
            <x-input class="deposit-buy-numb" placeholder="请输入存放数额" v-model="amount" type="number" ref="amount" :required="true"></x-input>
            <x-input  placeholder="请输入交易密码" v-model="security" type="password" ref="security" :required="true"></x-input>
        </group>
        <group class="nobg flex-box">
            <x-button type="primary" style="border-radius:99px;height:2.25rem;line-height:2.25rem;font-size:0.875rem;background:#3f73ed" @click.native="buy_fixed">确认</x-button>
        </group>
    </popup>
</div>
</template>
<script>
import { Loading ,Tab,TabItem ,TransferDom, Popup, PopupHeader} from 'vux';
import Nodata from "@/components/nodata";
export default {
    directives: {
        TransferDom
    },
    components: {
        Tab,
        TabItem,
        "popup":Popup,
        "popup-header":PopupHeader,
        Nodata,
        Loading,
    },
    data() {
        return {
            showDeploy:false,
            _self:'',
            idx:1,
            limit:0,
            limit_less:0,
            amount:'',
            security:'',
            fixed_list:[],
            current_list:[],
            buy_limits:'',
            is_limits:'',
            buy_limit_lessd:'',
            loadings:true,
            vc_totals:'',
            product_id:'',
            can_buy:'',
            coin_unit:''
        };
    },
    created() {
        
    },
    mounted() {
        this.getBtcbank();
    },
    methods: {
        open_deploy(e,id,buy_limit,is_limit,buy_limit_less,vc_total,is_per_limit,can_buy,coin_unit){
            this.product_id = id;
            this.buy_limits = buy_limit;
            if(is_limit || is_per_limit)
            {
                this.is_limits = 1;
            }else{
                this.is_limits = 0;
            }
            this.coin_unit = coin_unit
            this.buy_limit_lessd = buy_limit_less;
            this.vc_totals = vc_total;
            this.showDeploy=true;
            this.can_buy = can_buy;
            this.coin_unit = coin_unit;
            this._self = e.currentTarget;
        },
        tab(){
            this.$router.replace({path: '/finance/my_bank'});
        },
        getBtcbank(){
            this.$http.post('/api/app.financial/btcbank',{}).then(res=>{
                console.log(res);
                this.fixed_list = res.data.list.fixed;//定期
                this.current_list = res.data.list.current;//活期
                this.loadings = false;
            })
            .catch(error=>{
                this.loadings = false;
                this.$vux.toast.text(error.message);
            })
        },
        buy_fixed(){
            let form = {
                product_id:this.product_id,
                amount: this.amount,
                security:this.security
            }
            const _this = this;
            this.$http.post('/api/app.financial/btcbank/fixedbuy',form).then(res=>{
                this.$vux.toast.text('抢购成功');
                setTimeout(function () {
                    _this.$router.push({path: '/finance/my_bank'});
                },2000)
            })
            .catch(error=>{
                console.log(error);
                this.$vux.toast.text(error.message);
            })
        },
        close_deploy(){
            this.amount=null;
            this.security=null;
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";
.page-bank-index {
    height: 100%;
    padding-bottom: 1px;
    
    overflow: hidden;
    position: relative;
    .content{
        position: absolute;
        top: 0rem;
        left: 0;
        right: 0;
        bottom: 0;
        overflow-x: hidden;
        overflow-y: scroll;
        z-index: 1;
    }
    .con-box{
        margin-top: 0.25rem;
    }
    .detail-title {
        line-height: 2.5rem;
        padding: 0 0.9375rem;
        color: #666;
    }
    .profit-block {
        margin-bottom: 0.3125rem;
        background: #fff;
        .profit-title{
            padding: 0 0.9375rem;
            line-height: 2.1875rem;
            font-size: 0.8125rem;
        }
        .profit-item {
            padding:0.625rem 0.9375rem 0.5rem;
            color: #363840;
            position: relative;
            &::after{
                content: " ";
                position: absolute;
                left: 0.9375rem;
                bottom: 0;
                right: 0;
                height: 1px;
                border-bottom: 1px solid #C7C7C7;
                color: #C7C7C7;
                -webkit-transform-origin: 0 100%;
                transform-origin: 0 100%;
                -webkit-transform: scaleY(0.5);
                transform: scaleY(0.5);
            }
            &:last-child{
                &::after{
                    display: none;
                }
            }
        }
        .item-btn{
            position: relative;
            z-index: 2;
            height: 3.5rem;
        }
        .item-link{
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            z-index: 1;
        }
        .profit-item-item{
            &:first-child{
                width: 33%;
            }
            &:last-child{
                width: 23%;
                .item-item-decs{
                    height: 1.375rem;
                    line-height: 1.375rem
                }
            }
        }
        .t_r{
            text-align: right;
            
        }
        .profit-item-item.self-icon{
            width: 0.75rem;
            height: 0.75rem;
            position: relative;
            &::after{
                display: block;
                content: '\e8fa';
                position: absolute;
                left: 0;
                top: 0;
                font-size: 0.75rem;
                line-height: 0.75rem;
                color: #b6a4a7;
                font-family: 'iconfont';
            }
        }
        .item-item-title{
            font-size: 0.9375rem;
            line-height: 1.375rem;
        }
        .income-rate{
            color: red;
            font-size: 1.125rem;
        }
        .item-item-decs{
            font-size: 0.625rem;
            color: #999;
            line-height: 1.125rem;
            height: 1.125rem;
            
        }
        .self-type{
            display: flex;
            span {
                margin-left: 2px;
                display: inline-block;
                background: #ff6d84;
                padding: 0 3px;
                border-radius: 2px;
                height: 1.75rem;
                line-height: 1.75rem;
                color: #fff;
                font-size: 1.25rem;
                transform: scale(0.5);
                margin: -0.25rem -0.875rem 0;
            }
        }
        .item-item-btn {
                display: inline-block;
                border-radius: 2rem;
                padding: 0 0.4375rem;
                line-height: 1.375rem;
                background: #3f72ed;
                color: #fff;
                font-size: 0.75rem;
                width: 4.125rem;
                text-align: center;
        }
        .item-item-btn.btn_disabled {
            background: #bbb;
            color: #fff;
        }
    }
    .accrual-nav{
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        top: 0;
        pointer-events: none;
        
        .close-mask{
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0, 0, 0, 0);
            transition: all 0.3s;
            z-index: 1;
        }
        .accrual-deposit-box,.accrual-current-box{
            bottom: 2.375rem;
            position: absolute;
            left: 0;
            right: 0;
            transform: translateY(100%);
            transition: all 0.3s;
            z-index: 3;
            .deposit-item {
                line-height: 2.5rem;
                text-align: center;
                font-size: 0.875rem;
                background: #fff;
                &::after{
                    left: 0.9375rem;
                }
            }
        }
        .accrual-explain{
            line-height: 1.875rem;
            color: #3f72ed;
            background: #f1f1f3;
            text-align: center;
            font-size: 0.75rem;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 2.625rem;
            pointer-events: auto;
        }
        .accrual-nav-btn{
            height: 2.625rem;
            position: absolute;
            pointer-events: auto;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 3;
            
            .accrual-btn + .accrual-btn{
                margin: 0;
                background: #1ea0c2;
            }
            .accrual-btn{
                span{
                    position: relative;
                    &::after {
                        display: block;
                        content: '';
                        position: absolute;
                        border-left: 4px solid transparent;
                        border-right: 4px solid transparent;
                        border-top: 4px solid #fff;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -16px;
                        transition: all 0.5s;
                    }
                }
            }
        }
    }
    .accrual-nav.active{
        pointer-events: auto;
        
        .close-mask{
            background: rgba(0, 0, 0, 0.5);
        }
        .accrual-deposit-box{
            transform: translateY(0);
        }
        .accrual-explain{
            display: none;
        }
        .accrual-nav-btn{
            .accrual-btn{
                &:first-child{
                    span{
                        &::after {
                            transform: translateY(-50%) rotate(-180deg);
                        }
                    }
                }
            }
        }
    }
    .accrual-nav.current{
        pointer-events: auto;
        .close-mask{
            background: rgba(0, 0, 0, 0.5);
        }
        .accrual-current-box{
            transform: translateY(0);
        }
        .accrual-explain{
            display: none;
        }
        .accrual-nav-btn{
            .accrual-btn{
                &:last-child{
                    span{
                        &::after {
                            transform: translateY(-50%) rotate(-180deg);
                        }
                    }
                }
            }
        }
    }
}
</style>
