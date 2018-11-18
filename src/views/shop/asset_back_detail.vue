<template lang="html">
    <div class="page-grant">
        <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">申请详情
            <router-link  :to="{path:'/connect'}" slot="right">联系我们</router-link>
        </x-header>
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="candy-numb" title="姓名">
                    <x-input  v-model="name" type="text"   placeholder="请输入姓名"  readonly ></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="手机号">
                    <x-input  v-model="mobile"  placeholder="请输入手机号" :max="11" keyboard="number"  type="number"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="所在市县">
                    <x-input  v-model="area" type="text"   placeholder="请输入所在市县"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="推荐人">
                    <x-input  v-model="inviter" type="text"   placeholder="请输入行业节点推荐人"  readonly ></x-input>
                </group>
                <popup-radio title="请选择有何资产要收回" :options="resource" v-model="source">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">请选择有何资产要收回</p>
                </popup-radio>
                <group class="candy-numb" title="资产名称">
                    <x-input  v-model="asset_name" type="text"   placeholder="请输入资产名称"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="资产数量">
                    <x-input  v-model="asset_amount" type="number"   placeholder="请输入资产数量"  readonly></x-input>
                </group>
                <group class="candy-numb" title="资产价值">
                    <x-input  v-model="asset_value" type="number"   placeholder="请输入资产价值" readonly ></x-input>
                </group>
                <popup-radio title="形成原因" :options="come_results" v-model="come_result" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">形成原因</p>
                </popup-radio>
                <group class="candy-numb">
                    <datetime v-model="asset_create_time" title="形成日期" readonly></datetime>
                </group>
                <group class="candy-numb">
                    <datetime v-model="asset_valid_time" title="资产有效期" readonly></datetime>
                </group>
                <popup-radio title="接受方式" :options="accepts_ways" v-model="accepts_way" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">接受方式</p>
                </popup-radio>
                <popup-radio title="相互关系" :options="relations" v-model="relation" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">形成原因</p>
                </popup-radio>
                <group class="candy-numb" title="户名" v-show="accepts_way=='现金'">
                    <x-input  v-model="account_name" type="text"   placeholder="请输入银行卡户名"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="帐号" v-show="accepts_way=='现金'">
                    <x-input  v-model="account" type="text"   placeholder="请输入银行卡号"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="开户行" v-show="accepts_way=='现金'">
                    <x-input  v-model="bank_name" type="text"   placeholder="请输入开户行，支行名"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="数字资产" v-show="accepts_way=='数字资产'">
                    <x-input  v-model="asset_address" type="text"   placeholder="请输入钱包地址"  readonly ></x-input>
                </group>
                <group class="candy-numb" title="以物易物" v-show="accepts_way=='以物易物'">
                    <x-input  v-model="change_asset_name" type="text"   placeholder="请输入希望所兑换资产名称"  readonly ></x-input>
                </group>
                <popup-radio title="接受最长解决时间" :options="accepts_times" v-model="accepts_time" readonly>
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">接受最长解决时间</p>
                </popup-radio>
                <group title="备注：" class="grant-bottom">
                    <x-input  v-model="memo" type="text"   placeholder="请输入备注"></x-input>
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
        </div>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
    import { Datetime, Loading,LoadMore,PopupRadio, TransferDomDirective as TransferDom,dateFormat } from 'vux'
    export default {
        directives: {
            TransferDom
        },
        components: {
            Datetime,
            Loading,
            LoadMore,
            PopupRadio,
        },
        data() {
            return {
                name:'',
                area:'',
                mobile:'',
                inviter:'',
                source:'钱',
                asset_name:'',
                asset_amount:'',
                asset_value:'',
                lock:false,
                resource:['钱','固定资产','无形资产','其他'],
                loading:false,
                accepts_way:'现金',
                accepts_ways:['现金','数字资产','以物易物'],
                accepts_time:'1个月',
                accepts_times:['1个月','2个月','3个月'],
                asset_valid_time:dateFormat(new Date(), 'YYYY-MM-DD'),
                asset_create_time:dateFormat(new Date(), 'YYYY-MM-DD'),
                account_name:'',
                account:'',
                bank_name:'',
                asset_address:'',
                change_asset_name:'',
                come_results:['拆借','往来','转移'],
                come_result:'拆借',
                relations:['亲朋','客商','僱用'],
                relation:'亲朋',
                  user_id:{},
                  user:{}
            };
        },
        mounted() {
            this.getDetail();
        },
        methods: {
            getDetail(){
                const _this = this;
                this.$http.post('api/app.apply/credit/asset_back_detail',{id:this.$route.params.id}).then(res=>{
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
              }
        },
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
