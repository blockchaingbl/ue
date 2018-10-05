<template lang="html">
    <div class="page-current-detail">
        <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">活期详情</x-header>
        <div class="content">
            <div class="banner">
                <div class="banner-title">昨日收益</div>
                <div class="banner-profit">{{last_day_income}}</div>
                <div class="banner-footer flex-box">
                    <div class="banner-item flex-1">
                        <div class="item-title">持有资产&nbsp;&nbsp;({{currency}})</div>
                        <div class="item-numb">{{buy_amount}}</div>
                    </div>
                    <div class="banner-item flex-1">
                        <div class="item-title"><span>累计收益&nbsp;&nbsp;({{currencys}})</span></div>
                        <div class="item-numb">{{income_amount}}</div>
                    </div>
                </div>
            </div>
            <div class="detail-con detail-notice">
                <div class="title vux-1px-b">购买须知</div>
                <div class="flow-box con-decs">{{about}}</div>
            </div>
            <div class="detail-con detail-info">
                <div class="title vux-1px-b">产品信息</div>
                <div class="flow-box con-decs" v-html="detail" id="btcbank_detail"></div>
            </div>
        </div>
        <tabbar class="wallbar">
            <tabbar-item  class="tbleft" style="line-height: 22px; font-size:22px;" @on-item-click="open_deposit">
                <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe615;</i><span>转入</span></span>
            </tabbar-item>
            <tabbar-item class="tbright" @on-item-click="open_extraction">
                <span slot="label" class="flex-box"><i class="iconfont wallbar-icon">&#xe8fb;</i><span>转出</span></span>
            </tabbar-item>
        </tabbar>
        <popup class="pop-deposit-deploy" v-model="showCurrentDeposit" position="bottom" style="width:100%;background:#fff;"  v-transfer-dom>
            <popup-header
                    left-text=""
                    right-text=""
                    title="转入"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="showCurrentDeposit = false">
            </popup-header>
            <group>
                <cell title="可用数额" :value="vc_totals"></cell>
                <div class="deposit-deploy-tis" v-if="this.is_limit==1">还可购买 {{can_buy}} {{currency}}</div>
                <x-input placeholder="请输入存放数额" v-model="amount"></x-input>
                <x-input  placeholder="请输入交易密码" v-model="security" type="password" ref="password" :required="true"></x-input>
            </group>
            <group class="nobg flex-box">
                <x-button type="primary" @click.native="buy_fixed" style="height:2.25rem;line-height:2.25rem;font-size:0.875rem;" >确认</x-button>
            </group>
        </popup>
        <popup class="pop-deposit-deploy" v-model="showExtraction" position="bottom" style="width:100%;background:#fff;"  v-transfer-dom>
            <popup-header
                    left-text=""
                    right-text=""
                    title="转出"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="showExtraction = false">
            </popup-header>
            <group>
                <cell title="账户数额" :value="vc_totals"></cell>
                <x-input :placeholder="take_out"  v-model="amounts"></x-input>
                <x-input  placeholder="请输入交易密码" v-model="securitys" type="password" ref="password" :required="true"></x-input>
            </group>
            <group class="nobg flex-box">
                <x-button type="primary" @click.native="take_fixed" style="height:2.25rem;line-height:2.25rem;font-size:0.875rem;">确认</x-button>
            </group>
        </popup>
        <div v-transfer-dom style="position:relative;z-index:999">
            <loading :show="loadings"></loading>
        </div>
    </div>
</template>
<script>
import router from '@/router';
import { TransferDom, Popup, PopupHeader, Loading} from 'vux';
export default {
    directives: {
        TransferDom
    },
    components: {
        "popup":Popup,
        "popup-header":PopupHeader,
        Loading
    },
    data () {
        return {
            production_id:router.currentRoute.params['production_id'],
            year_income:'',
            amount:'',
            amounts:'',
            security:'',
            securitys:'',
            time_length:'',
            status:0,
            receive_status:0,
            showCurrentDeposit:false,
            showExtraction:false,
            name:'',
            about:'',
            detail:'',
            buy_amount:'',
            last_day_income:'',
            income_amount:'',
            take_out:'',
            currency:'',
            currencys:'',
            vc_total:'',
            vc_totals:'',
            loadings:false,
            is_limit:null,
            can_buy:'',
            is_per_limit:null
        }
    },
    mounted () {
        this.getDetail();
    },
    updated(){
        const route_domain = this.$store.state.init.route_domain;
        if(this.$store.state.init.is_app)
        {
            $('#btcbank_detail').find('a').attr('target','self');
            $('#btcbank_detail').find('a').click(function (e) {
                var src = $(this).attr('href');
                e.preventDefault();
                if(src!=='')
                {
                    if(route_domain.indexOf(src))
                    {
                        location.href = src;
                    }else{
                        App.open_type('{"url":"'+src+'"}');
                    }
                }
                return false;
            })
        }
    },
    methods:{
        open_deposit(){
            this.showCurrentDeposit=true;
        },
        open_extraction(){
            this.showExtraction = true;
        },
        getDetail(){

            this.$http.post('/api/app.financial/btcbank/detail',{production_id:this.production_id}).then(res=>{
                console.log(res);
                this.name = res.data.detail.name;
                this.about = res.data.detail.about;
                this.detail = res.data.detail.detail;
                this.vc_total = res.data.detail.vc_total;
                this.currency = res.data.detail.asset_coin_type.coin_unit;
                this.currencys = res.data.detail.profit_coin_type.coin_unit;
                this.vc_totals = this.vc_total + ' '+this.currency;
                this.buy_amount = res.data.order.buy_amount;
                this.last_day_income = res.data.order.last_day_income;
                this.income_amount = res.data.order.income_amount;
                this.take_out = '最多可转出'+this.buy_amount + this.currency;
                if(res.data.detail.is_limit || res.data.detail.is_per_limit)
                {
                    this.is_limit = 1;
                    this.can_buy= res.data.detail.can_buy
                }
            })
            .catch(error=>{
                this.$vux.toast.text(error.message);
            })
        },
        buy_fixed(){
            this.loadings = true;
            let form = {
                product_id:this.production_id,
                amount: this.amount,
                security:this.security
            }
            const _this = this;
            this.$http.post('/api/app.financial/btcbank/fixedbuy',form).then(res=>{
                this.showCurrentDeposit=false;
                this.loadings = false;
                this.$vux.toast.text("存入成功");
                setTimeout(function () {
                    location.reload();
                },2000)
            })
            .catch(error=>{
                this.loadings = false;
                console.log(error);
                this.$vux.toast.text(error.message);
            })
        },
        take_fixed(){
            this.loadings = true;
            let form = {
                fp_id:this.production_id,
                amount: this.amounts,
                security:this.securitys
            }
            const _this = this;
            this.$http.post('/api/app.financial/btcbank/currentreceive',form).then(res=>{
                this.showExtraction = false;
                this.loadings = false;
                this.$vux.toast.text("转出成功");
                setTimeout(function () {
                    location.reload();
                },2000)
            })
            .catch(error=>{
                this.loadings = false;
                console.log(error);
                this.$vux.toast.text(error.message);
            })
        }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .page-current-detail {
        height: 100%;
        padding-bottom: 1px;
        overflow: hidden;
        position: relative;
        .content{
            position: absolute;
            top: 2.875rem;
            left: 0;
            right: 0;
            bottom: 2.75rem;
            overflow-x: hidden;
            overflow-y: scroll;
            z-index: 1;
        }
        .bottom-btn-box{
            height: 2.75rem;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .banner{
            background: url(../../assets/images/finace_banner.jpg) no-repeat top center;
            background-size: 100% 100%;
            padding: 0.9375rem 0.9375rem 0.5rem;
            color: #fff;
            
            .banner-title{
                text-align: center;
                font-size: 0.75rem;
                color: #fff;
                line-height: 1.125rem;
            }
            .banner-profit {
                text-align: center;
                line-height: 3.125rem;
                min-height: 3.125rem;
                font-size: 1.875rem;
                color: #fff;
            }
            .profit-btn{
                .item-title{
                    span{
                        position: relative;
                        &::before{
                            position: absolute;
                            display: block;
                            content: '\e8fa';
                            font-family: 'iconfont';
                            top: 50%;
                            transform: translateY(-50%);
                            right: -0.875rem;
                            font-size: 0.4375rem;
                        }
                    }
                }
            }
            .banner-footer {
                margin: 0.625rem 0 0.375rem;
                .banner-item {
                    line-height: 1.25rem;
                    text-align: center;
                    color: #fff;
                }
                .item-title{
                    font-size: 0.75rem;
                    color:#fff;
                }
                .item-numb{
                    font-size: 1.25rem;
                    height: 1.5rem;
                    line-height: 1.5rem;
                }
            }
        }
        .detail-con {
            margin-top: 0.8125rem;
            background: #fff;
            .title {
                padding: 0 0.9375rem;
                line-height: 2.25rem;
                font-size: 0.875rem;
                color: #363840;
            }
            .flow-box {
                margin: 0 0.9375rem;
                position: relative;
                padding: 0.625rem 0;
                color: #9ca5bd;
                font-size: 0.75rem;
                .flow-con {
                    position: absolute;
                    width: 100%;
                    height: 3px;
                    background: #d7dde9;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    z-index: 1;
                }
                .flow-block{
                    position: relative;
                    z-index: 2;
                }
                .item {
                    line-height: 1.625rem;
                    .item-icon {
                        height: 0.5rem;
                        width: 0.5rem;
                        background: #d7dde9;
                        border-radius: 0.25rem;
                        padding: 2px;
                    }
                    .item-icon-inner {
                        width: 100%;
                        height: 100%;
                        background: #fff;
                        border-radius: 50%;
                    }
                    .item-time{
                        height: 1.625rem;
                    }
                }
                .item.active{
                    color: #3f72ed;
                    .item-icon {
                        background: #3f72ed;
                    }
                }
                .item-start{
                    text-align: center;
                    .item-icon {
                        margin: 0 auto;
                    }
                }
                .item-end{
                    display: flex;
                    flex-direction: column;
                    align-items: flex-end;
                }
            }
            .con-decs{
                font-size: 0.8125rem;
                color: #363840;
                line-height: 1.25rem;
            }
        }
        .detail-notice{
            margin-top: 0.625rem;
        }
        .detail-info{
            margin-top: 0.625rem;
            img{
                max-width: 100%;
            }
        }
        .wallbar{
            position: absolute;
            left: 0;
            right: 0;
            bottom:0;
            .weui-tabbar__label{
                color:#fff;
                span{
                    justify-content: center;
                }
            }
            .wallbar-icon{
                font-size: 1.0625rem;
                margin-right: 0.625rem;
            }
        }
    }
</style>
