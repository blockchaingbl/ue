<template lang="html">
    <div class="page-grant">
        <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">兑换详情
            <router-link  :to="{path:'/connect'}" slot="right">联系我们</router-link>
        </x-header>
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="candy-numb" title="姓名">
                    <x-input  v-model="name" type="text"   :required="true" readonly ></x-input>
                </group>
                <a :href="'tel:'+mobile" style="color: #363840">
                    <group class="candy-numb vux-1px-b" title="电话">
                        <x-input  v-model="mobile"  :max="11" keyboard="number" readonly type="number"  :required="true" ></x-input>
                    </group>
                </a>
                <group class="candy-numb" title="所在市县">
                    <x-input  v-model="area" type="text"    :required="true" readonly></x-input>
                </group>
                <popup-radio title="请选择" :options="card_types" v-model="card_type" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">卡类型</p>
                </popup-radio>
                <popup-radio title="加油卡" :options="oil_cards" v-model="oil_card" v-show="card_type=='加油卡'" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">加油卡</p>
                </popup-radio>
                <popup-radio title="提货方式:" :options="get_ways" v-model="get_way" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">提货方式</p>
                </popup-radio>
                <group class="candy-numb" title="户名：" v-show="card_type=='加油卡'  && get_way=='电子卡'" readonly>
                    <x-input  v-model="oil_card_account" type="text"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="卡号：" v-show="card_type=='加油卡'  && get_way=='电子卡'" readonly>
                    <x-input  v-model="oil_card_number" type="text"   :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="油卡绑定的手机号：" v-show="card_type=='加油卡'  && get_way=='电子卡' " readonly>
                    <x-input  v-model="oil_card_mobile" type="text"   :required="true" ></x-input>
                </group>
                <popup-radio title="商超购物卡:" :options="shop_cards" v-model="shop_card" v-show="card_type=='商超购物卡'" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">商超购物卡</p>
                </popup-radio>
                <popup-radio title="兑换数额：" :options="shop_cards_amounts" v-model="shop_cards_amount" readonly>
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
                            共需消耗 {{total_amount}}
                        </div>
                    </cell>
                </group>
                <group class="candy-numb" title="电子卡号：" v-show="get_way=='电子卡' && card_type=='商超购物卡' " >
                    <x-input readonly v-model="card_number" type="text"   :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="收货地址："  v-show="get_way=='收货地址'">
                    <x-input readonly v-model="address" type="text"    :required="true" ></x-input>
                </group>
                <group>
                    <cell>
                        <div v-show="get_way=='收货地址'">
                            <span style="color: red">选择收货地址需额外{{$store.state.init.card_trans_fee}}个GBL资产</span>
                        </div>
                    </cell>
                </group>
                <group title="备注：" class="grant-bottom">
                    <x-input readonly v-model="memo" type="text"></x-input>
                </group>
                <group v-if="deal_user_id>0">
                    <cell>
                        <div style="font-size: 1rem;">
                            <a :href="'tel:'+deal_user.mobile">协助社群： {{deal_user.mobile}}</a>
                        </div>
                    </cell>
                    <cell @click.native="connect()" v-if="deal_user.accid">
                        <div style="font-size: 1rem;">
                            <span>在线联系</span>
                        </div>
                    </cell>
                </group>
                <group v-if="user_id>0">
                    <cell>
                        <div style="font-size: 1rem;">
                            <a :href="'tel:'+user.mobile">下单人： {{user.mobile}}</a>
                        </div>
                    </cell>
                    <cell @click.native="connect_user()" v-if="user.accid">
                        <div style="font-size: 1rem;">
                            <span>在线联系</span>
                        </div>
                    </cell>
                </group>
            </div>
            <box class="grant-btn-box" gap="0 0" v-if="user_id==0&&pay_type==1&&pay_status==0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="turn_to_pay()">立即支付</x-button>
            </box>
        </div>
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
        shop_cards_amount:'1000',
        shop_cards_amounts:[{key:'1000',value:'1000等值GBL'},{key:'3000',value:'3000等值GBL'},{key:'5000',value:'5000等值GBL'},{key:'10000',value:'10000等值GBL'},{key:'20000',value:'20000等值GBL'}],
        lock:false,
        get_ways:['自有加油卡','需要新卡'],
        get_way:'自有加油卡',
        card_types:['加油卡','商超购物卡'],
        card_type:'加油卡',
        oil_cards:['中石油','中石化'],
        oil_card:'中石油',
        loading:true,
        oil_card_account:'',
        oil_card_number:'',
        memo:'',
        address:'',
        total_amount:'',
        deal_user_id:0,
        deal_user:{},
        user_id:{},
        user:{},
        pay_status:0,
        pay_type:0
      };
    },
    mounted() {
      this.getDetail();
    },
    methods: {
      getDetail(){
        const _this = this;
        this.$http.post('api/app.apply/cardorder/detail',{id:this.$route.params.id}).then(res=>{
          if(res.errcode==0)
          {
            for (let x in res.data.order){
              this[x]=res.data.order[x]
            }
          }else{
            this.$vux.toast.text(res.message);
          }
          this.loading = false;

        }).catch(err=>{
          this.$vux.toast.text(err.message);
          this.loading = false;
        })
      },
        connect()
        {
            this.$router.push({path: '/chat/p2p-'+this.deal_user.accid})
        },
          connect_user(){
            this.$router.push({path: '/chat/p2p-'+this.user.accid})
          },
        turn_to_pay(){
            let total_amount = this.total_amount;
            let order_code = this.order_code;
            let url =encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+order_code+'&amount='+total_amount)
            this.$router.push({path:url})
        }
    },
    computed:{
      per_month(){
        return parseFloat(this.shop_cards_amount)/1000*140;
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
            min-height: 4rem;
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
