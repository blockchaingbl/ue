<template lang="html">
    <div class="page-bank-detail">
        <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">套餐详情</x-header>
        <div class="content">
            <div class="banner">
                <div class="banner-title">年化收益</div>
                <div class="banner-profit">{{year_rate}}%</div>
                <div class="banner-footer flex-box">
                    <div class="banner-item flex-1">
                        <div class="item-title">锁定期限&nbsp;&nbsp;(天)</div>
                        <div class="item-numb">{{time_length}}</div>
                    </div>
                    <div class="banner-item flex-1">
                        <div class="item-title"><span>聚宝数量&nbsp;&nbsp;({{asset_coin_type.coin_unit}})</span></div>
                        <div class="item-numb">{{buy_amount}}</div>
                    </div>
                </div>
            </div>
            <div class="detail-con">
                <div class="title vux-1px-b">套餐周期</div>
                <div class="flow-box">
                    <div class="flow-con"></div>
                    <div class="flow-block flex-box">
                        <div class="item item-buy flex-1" :class="{'active':receive_status==0}">
                            <div class="item-title">聚宝</div>
                            <div class="item-icon">
                                <div class="item-icon-inner"></div>
                            </div>
                            <div class="item-time">{{buy_day}}</div>
                        </div>
                        <div class="item item-start flex-1" :class="{'active':receive_status==1}">
                            <div class="item-title">计算收入</div>
                            <div class="item-icon">
                                <div class="item-icon-inner"></div>
                            </div>
                            <div class="item-time">{{start_day}}</div>
                        </div>
                        <div class="item flex-1"></div>
                        <div class="item item-end flex-1" :class="{'active':receive_status==2}">
                            <div class="item-title">到期时间</div>
                            <div class="item-icon">
                                <div class="item-icon-inner"></div>
                            </div>
                            <div class="item-time">{{end_day}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="detail-con detail-notice">
                <div class="title vux-1px-b">聚宝须知</div>
                <div class="flow-box con-decs">{{about}}</div>
            </div>
            <div class="detail-con detail-info">
                <div class="title vux-1px-b">产品信息</div>
                <div class="flow-box con-decs" id="btcbank_detail" v-html="detail"></div>
            </div>
        </div>
        <box class="bottom-btn-box" v-if="has_buy==0">
            <x-button class="bottom-btn" type="primary" style="border-radius:0;font-size:0.9375rem;height:100%;background:#3f72ed" @click.native="open_deploy($event)">立即抢购</x-button>
        </box>
        <box class="bottom-btn-box" v-if="has_buy==1">
            <x-button class="bottom-btn" type="primary" style="border-radius:0;font-size:0.9375rem;height:100%;background:#3f72ed" @click.native="take_fixed" v-if="status==0&&receive_status==2">立即赎回</x-button>
            <x-button class="bottom-btn" type="primary" style="border-radius:0;font-size:0.9375rem;height:100%;background:#bbb"  v-if="status==1">已经赎回</x-button>
            <x-button class="bottom-btn" type="primary" style="border-radius:0;font-size:0.9375rem;height:100%;background:#bbb"  v-if="receive_status==0||receive_status==1">未到期，不可赎回</x-button>
        </box>
        <!--定期产品买入-->
        <popup class="pop-deposit-deploy" v-model="showDeploy" position="bottom" style="width:100%;background:#fff;"  v-transfer-dom>
            <popup-header
                    left-text=""
                    right-text=""
                    title="存放"
                    :show-bottom-border="false"
                    @on-click-left="show1 = false"
                    @on-click-right="showDeploy = false">
            </popup-header>
            <group>
                <cell class="vux-1px-b" title="可用数额" :value="vc_totals+ ' '+asset_coin_type.coin_unit"></cell>
                <div class="deposit-deploy-tis" v-if="this.is_limit==1 || this.is_per_limit==1">还可购买 {{can_buy}} {{asset_coin_type.coin_unit}}</div>
                <x-input class="deposit-buy-numb" placeholder="请输入存放数额" v-model="amount" type="number" ref="amount" :required="true"></x-input>
                <x-input  placeholder="请输入交易密码" v-model="security" type="password" ref="security" :required="true"></x-input>
            </group>
            <group class="nobg flex-box">
                <x-button type="primary" style="border-radius:99px;height:2.25rem;line-height:2.25rem;font-size:0.875rem;background:#3f73ed" @click.native="buy_fixed">确认</x-button>
            </group>
        </popup>
        <div v-transfer-dom style="position:relative;z-index:999">
            <loading :show="loadings"></loading>
        </div>
    </div>
</template>
<script>
import { TransferDom, Popup, PopupHeader, Loading} from 'vux';
import router from '@/router';
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
            time_length:'',
            buy_day:'',
            start_day:'',
            end_day:'',
            status:null,
            receive_status:null,
            name:'',
            year_rate:'',
            time_length:'',
            buy_amount:'',
            has_buy:null,
            about:'',
            detail:'',
            showDeploy:false,
            buy_limits:'',
            is_limits:'',
            buy_limit_lessd:'',
            loadings:false,
            vc_totals:'',
            security:'',
            order_id:'',
            asset_coin_type:{},
            is_limit:null,
            is_per_limit:null,
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
        open_deploy(e){
            this.showDeploy=true;
            this._self = e.currentTarget;
        },
        getDetail(){
            this.$http.post('/api/app.financial/btcbank/detail',{production_id:this.production_id}).then(res=>{
                this.has_buy = res.data.detail.has_buy;
                this.name = res.data.detail.name;
                this.year_rate = res.data.detail.year_rate;
                this.time_length = res.data.detail.time_length;
                this.about = res.data.detail.about;
                this.detail = res.data.detail.detail;
                this.buy_amount =res.data.order.buy_amount;
                this.buy_day = res.data.order.buy_day;
                this.start_day = res.data.order.start_day;
                this.end_day = res.data.order.end_day;
                this.receive_status = res.data.order.receive_status;
                this.status = res.data.order.status;
                this.buy_limits = res.data.detail.buy_limit;
                this.can_buy = res.data.detail.can_buy;
                this.is_limit = res.data.detail.is_limit;
                this.is_per_limit = res.data.detail.is_per_limit;
                this.buy_limit_lessd = res.data.detail.buy_limit_less;
                this.vc_totals = res.data.detail.vc_total
                this.order_id = res.data.order.id;
                this.asset_coin_type = res.data.detail.asset_coin_type;
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
                this.showDeploy = false;
                this.loadings = false;
                this.$vux.toast.text("存入成功");
                setTimeout(function () {
                    _this.$router.push({path: '/finance/my_bank'});
                },2000)
            })
            .catch(error=>{
                console.log(error);
                this.loadings = false;
                this.$vux.toast.text(error.message);
            })
        },
        take_fixed(){
            this.loadings = true;
            let form = {
                order_id:this.order_id,
            }
            const _this = this;
            this.$http.post('/api/app.financial/btcbank/fixedreceive',form).then(res=>{
                this.loadings = false;
                this.$vux.toast.text("赎回成功");
                setTimeout(function () {
                    _this.$router.push({path: '/finance/my_bank'});
                },2000)
            })
            .catch(error=>{
                console.log(error);
                this.$vux.toast.text(error.message);
            })
        }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .page-bank-detail {
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
    }
</style>
