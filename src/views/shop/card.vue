<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="candy-numb" title="姓名">
                    <x-input  v-model="name" type="text"   placeholder="请输入姓名"  :required="true" ></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="电话">
                    <x-input  v-model="mobile"  placeholder="请输入电话" :max="11" keyboard="number"  type="number"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="所在市县">
                    <x-input  v-model="area" type="text"   placeholder="请输入所在市县"  :required="true" ></x-input>
                </group>
                <popup-radio title="请选择" :options="card_types" v-model="card_type">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">卡类型</p>
                </popup-radio>
                <popup-radio title="加油卡" :options="oil_cards" v-model="oil_card" v-show="card_type=='加油卡'">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">加油卡</p>
                </popup-radio>
                <popup-radio title="提货方式:" :options="get_ways" v-model="get_way">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">提货方式</p>
                </popup-radio>
                <group class="candy-numb" title="请输入户名：" v-show="card_type=='加油卡' && get_way=='自有加油卡'">
                    <x-input  v-model="oil_card_account" type="text"   placeholder="请输入户名"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="请输入卡号：" v-show="card_type=='加油卡' && get_way=='自有加油卡'">
                    <x-input  v-model="oil_card_number" type="text"   placeholder="请输入卡号"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="请输入油卡绑定的手机号：" v-show="card_type=='加油卡' && get_way=='自有加油卡' ">
                    <x-input  v-model="oil_card_mobile" type="text"   placeholder="请输入绑定的手机号"  :required="true" ></x-input>
                </group>
                <popup-radio title="商超购物卡:" :options="shop_cards" v-model="shop_card" v-show="card_type=='商超购物卡'">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">商超购物卡</p>
                </popup-radio>
                <popup-radio title="兑换数额：" :options="shop_cards_amounts" v-model="shop_cards_amount">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">兑换数额</p>
                </popup-radio>
                <group>
                    <cell>
                        <div>
                            分5-10个月领取（奖励随各社群规则）
                        </div>
                    </cell>
                </group>
                <group>
                    <cell>
                        <div>
                            共需消耗 {{pay_amount}} GBL
                        </div>
                    </cell>
                </group>
                <group class="candy-numb" title="请输入电子卡号：" v-show="get_way=='电子卡' && card_type=='商超购物卡'">
                    <x-input  v-model="card_number" type="text"   placeholder="请输入电子卡号"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="请输入收货地址："  v-show="get_way=='收货地址' || get_way=='需要新卡'">
                    <x-input  v-model="address" type="text"   placeholder="请输入收货地址"  :required="true" ></x-input>
                </group>
                <group>
                    <cell>
                        <div v-show="get_way=='收货地址' || get_way=='需要新卡' ">
                            <span style="color: red">{{get_way}}需额外消耗{{$store.state.init.card_trans_fee}}GBL</span>
                        </div>
                    </cell>
                </group>
                <group>
                    <div style="padding: 10px 15px;color: #999999">
                        兑换业务由行业节点提供，行业节点属于第三方商业合作机构，规则及售后均由行业节点提供，GBL只保证数据安全，功能正常使用，保证合作行业节点不会出现道德风险，如遇行业节点对兑换方案进行合理调整或合理解约，GBL将无权干涉，敬请理解！                    </div>
                </group>
                <group title="备注：" class="grant-bottom">
                    <x-input  v-model="memo" type="text"   placeholder="请输入备注"></x-input>
                </group>
                <popup-radio title="支付方式：" :options="pay_ways" v-model="pay_way">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">支付方式</p>
                </popup-radio>
            </div>

            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="create_order">提交申请</x-button>
            </box>
        </div>
        <popup class="pop-deposit-deploy" v-model="showDeploy" position="bottom" style="width:100%;background:#fff;"  v-transfer-dom>
            <popup-header
                    left-text=""
                    right-text=""
                    title="资金密码"
                    :show-bottom-border="false"
                    @on-click-right="showDeploy = false">
            </popup-header>
            <group>
                <div class="deposit-deploy-tis"></div>
                <x-input  placeholder="请输入交易密码" v-model="security" type="password" ref="security" :required="true"></x-input>
            </group>
            <group class="nobg flex-box">
                <x-button type="primary" style="border-radius:99px;height:2.25rem;line-height:2.25rem;font-size:0.875rem;background:#3f73ed" @click.native="buy_fixed">确认</x-button>
            </group>
        </popup>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
import { Datetime, Loading,LoadMore,PopupRadio,XSwitch, TransferDomDirective as TransferDom,Popup, PopupHeader } from 'vux'
    export default {
        directives: {
            TransferDom
        },
        components: {
            Datetime,
            Loading,
            LoadMore,
            PopupRadio,
            XSwitch,
            Popup,
            PopupHeader
        },
        data() {
            return {
                security:'',
                showDeploy:false,
                name:'',
                mobile:'',
                area:'',
                card_number:'',
                shop_card:'天虹',
                shop_cards:['天虹','永辉','北国','大润发','步步高','沃尔玛'],
                oil_card_mobile:'',
                shop_cards_amount:'500',
                shop_cards_amounts:[{key:'500',value:'500等值GBL'},{key:'1000',value:'1000等值GBL'},{key:'3000',value:'3000等值GBL'},{key:'5000',value:'5000等值GBL'},{key:'10000',value:'10000等值GBL'},
                    {key:'20000',value:'20000等值GBL'}],
                lock:false,
                get_ways:['自有加油卡','需要新卡'],
                get_way:'自有加油卡',
                card_types:['加油卡','商超购物卡'],
                card_type:'加油卡',
                oil_cards:['中石油','中石化'],
                oil_card:'中石油',
                loading:false,
                oil_card_account:'',
                oil_card_number:'',
                memo:'',
                address:'',
                pay_ways:['令牌兑换'],
                pay_way:'令牌兑换'
            };
        },
        mounted() {
            this.get_auth();
        },
        methods: {
            buy_fixed(){
                if(this.lock)
                {
                    return false;
                }
                this.lock = true;
                let form = {
                    card_number:this.card_number,
                    address:this.address,
                    name:this.name,
                    mobile:this.mobile,
                    security:this.security,
                    area:this.area,
                    shop_card:this.shop_card,
                    oil_card_mobile:this.oil_card_mobile,
                    shop_cards_amount:this.shop_cards_amount,
                    get_way:this.get_way,
                    card_type:this.card_type,
                    oil_card:this.oil_card,
                    oil_card_account:this.oil_card_account,
                    oil_card_number:this.oil_card_number,
                    memo:this.memo
                }
                const _this = this;
                this.$http.post('api/app.apply/cardorder/buy',form).then(res=>{
                    if(res.errcode==0)
                    {
                        this.$vux.toast.text("申请成功");
                        this.$router.push({path:'/shop/cardorder'})
                    }else{
                        this.$vux.toast.text(res.message);
                    }
                    this.lock = false;
                }).catch(err=>{
                    this.lock = false;
                    this.$vux.toast.text(err.message);

                })
            },
            create_order(){
                if(this.pay_way=='资产兑换')
                {
                    this.showDeploy=true;
                    return
                }else{
                    if(this.lock)
                    {
                        return false;
                    }
                    this.lock = true;
                    let form = {
                        card_number:this.card_number,
                        address:this.address,
                        name:this.name,
                        mobile:this.mobile,
                        security:this.security,
                        area:this.area,
                        shop_card:this.shop_card,
                        oil_card_mobile:this.oil_card_mobile,
                        shop_cards_amount:this.shop_cards_amount,
                        get_way:this.get_way,
                        card_type:this.card_type,
                        oil_card:this.oil_card,
                        oil_card_account:this.oil_card_account,
                        oil_card_number:this.oil_card_number,
                        memo:this.memo
                    }
                    const _this = this;
                    this.$http.post('api/app.apply/cardorder/token_buy',form).then(res=>{
                        if(res.errcode==0)
                        {
                            let amount = res.data.amount;
                            let order_code = res.data.order_code;
                            let address= res.data.card_address;
                            let url =encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+order_code+'&amount='+amount+"&address="+address)
                            this.$router.push({path:url})
                        }else{
                            this.$vux.toast.text(res.message);
                        }
                        this.lock = false;
                    }).catch(err=>{
                        this.lock = false;
                        this.$vux.toast.text(err.message);
                    })
                }
            },
            get_auth(){
                this.$http.post('api/app.apply/cardorder/auth',{}).then(res=>{
                   if(res.data.auth==1)
                   {
                       this.pay_ways.push('资产兑换')
                   }
                }).catch(err=>{
                    this.lock = false;
                    this.$vux.toast.text(err.message);

                })
            }
        },
        computed:{
            per_month(){
                return parseFloat(this.shop_cards_amount)/1000*140;
            },
            pay_amount(){

                let amount =  parseFloat(this.shop_cards_amount/this.$store.state.init.coin_price)
                if(this.pay_way=='资产兑换')
                {
                  amount *= (1+this.$store.state.init.card_fee);
                }
                if(this.get_way=='需要新卡' || this.get_way=='收货地址')
                {
                    amount+=this.$store.state.init.card_trans_fee;
                }
                return amount.toFixed(5);
            }
        },
        watch:{
            card_type(){
                if(this.card_type=='加油卡')
                {
                    this.get_ways = ['自有加油卡','需要新卡']
                    this.get_way = '自有加油卡'
                }else{
                    this.get_ways = ['电子卡','收货地址']
                    this.get_way = '电子卡'
                }
            }
        }
    };
</script>
<style lang="less" scoped>
    @import "../../assets/css/variable.less";
    .page-grant{
        min-height: 100%;
        padding-bottom: 2.375rem;
        .candy-basic-opera{
            background: #fff;
            margin-bottom: 0.625rem;
        }
        .candy-senior-opera{

            background: #fff;
            margin-bottom: 0.625rem;
        }
        .coin-slot {
            text-align: center;
            padding: 8px 0;
            color: #888;
        }
        .text_red{
            color: red;
        }
        .cell-assets{
            padding-bottom: 0;
        }
        .grant-bottom{
            min-height: 6rem;
            background: #fff;
            margin-bottom: 0.625rem;
        }
        .grant-btn-box{
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .lock-time {
            padding-bottom: 0.25rem;
        }
        .lock-select{
            padding-left: 15px;
            padding-top: 10px;
        }
        .grant-money.vux-x-input.weui-cell{
            padding: 0.75rem 0.9375rem 1.25rem;
        }
        .candy-radio{
            padding: 0 0.9375rem;
            margin: 0.625rem 0;
            .radio-item {
                padding: 0.625rem;
                border: 1px solid #ddd;
                border-radius: 4px;
                .radio-title {
                    font-size: 0.8125rem;
                    line-height: 1.1875rem;
                }
                .radio-numb {
                    font-size: 1.0625rem;
                    line-height: 1.1875rem;
                }
            }
            .radio-item + .radio-item{
                margin-left: 0.9375rem;
            }
            .radio-item.active {
                border-color: #2f82ff;
                background: #2f82ff;
                color: #fff;
            }
        }
        .introduction /deep/ .vux-label{
            font-size: 18px;
        }
    }

</style>
