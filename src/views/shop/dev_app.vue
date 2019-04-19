<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="candy-numb" title="联系人">
                    <x-input  v-model="name" type="text"   placeholder="请输入姓名"  :required="true" ></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="联系方式：">
                    <x-input  v-model="mobile"  placeholder="请输入联系方式"  keyboard="number"  type="number"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="联系邮箱">
                    <x-input  v-model="email" type="text"   placeholder="请输入联系邮箱"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="推荐人">
                    <x-input  v-model="inviter" type="text"   placeholder="请输入推荐人"  :required="true" ></x-input>
                </group>
                <popup-radio title="请选择" :options="dev_types" v-model="dev_type">
                <p slot="popup-header" class="vux-1px-b grant-coin-slot">开发功能类别</p>
                </popup-radio>
                <popup-radio title="应用类型" :options="app_types_block_chain" v-model="app_type_block_chain" v-show="dev_type=='区块链应用'">
                <p slot="popup-header" class="vux-1px-b grant-coin-slot">应用类型</p>
                </popup-radio>
                <popup-radio title="应用类型" :options="app_types_net" v-model="app_type_net" v-show="dev_type=='互联网应用'">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">应用类型</p>
                </popup-radio>
                <group class="candy-numb" title="工程预算">
                    <x-input  v-model="budget" type="text"   placeholder="请输入工程预算"  :required="true" ></x-input>
                </group>
                <group lass="candy-numb" title="">
                    <datetime v-model="start_time" title="请选择上线时间"></datetime>
                </group>
                <popup-radio title="是否需要运维" :options="need_supports" v-model="need_support">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">应用类型</p>
                </popup-radio>
                <group title="功能要求" class="grant-bottom">
                    <x-input  v-model="app_need" type="text"   placeholder="请输入功能要求"></x-input>
                </group>
                <group>
                    <div style="padding: 10px 15px;color: #999999">
                        注：本技术开发服务由第三方技术公司提供，在申请提交3个工作日内将以邮件或电话的方式和您取得联系，GBL仅提供业务入口，提交开发申请需要燃烧{{$store.state.init.app_dev_fee}}GBL资产，且无论该申请是达成合作关系，燃烧资产将不退回，敬请知晓，如该申请成功达成合作关系，推荐人将获得第三方开发机构提供的5%-20%推荐奖励，奖励形式由第三方决定（合格社员方可享受）
                    </div>
                </group>
            </div>

            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="showDeploy=true">提交申请</x-button>
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
                <x-input  placeholder="请输入资产密码" v-model="security" type="password" ref="security" :required="true"></x-input>
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
import { Datetime, Loading,LoadMore,PopupRadio,XSwitch, TransferDomDirective as TransferDom,Popup, PopupHeader ,dateFormat } from 'vux'
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
          email:'',
          app_type_block_chain:'钱包',
          app_types_block_chain:['钱包','交易所','公链','商品溯源','健康数据','去中心化商城','区块链打车','其他系统'],
          lock:false,
          dev_types:['区块链应用','互联网应用'],
          dev_type:'区块链应用',
          app_types_net:['分销系统','即时通讯','VOIP','其他系统'],
          app_type_net:'分销系统',
          loading:false,
          app_need:'',
          need_supports:['是','否'],
          need_support:'是',
          start_time:dateFormat(new Date(), 'YYYY-MM-DD'),
          budget:'',
          inviter:''
            };
        },
        mounted() {

        },
        methods: {
            buy_fixed(){
                if(this.lock)
                {
                    return false;
                }
                this.lock = true;
               let form = {
                email:this.email,
                name:this.name,
                mobile:this.mobile,
                security:this.security,
                dev_type:this.dev_type,
                app_type_block_chain:this.app_type_block_chain,
                app_type_net:this.app_type_net,
                budget:this.budget,
                inviter:this.inviter,
                need_support:this.need_support,
                start_time:this.start_time,
                app_need:this.app_need
                }
                const _this = this;
                this.$http.post('api/app.apply/cardorder/appdev',form).then(res=>{
                    if(res.errcode==0)
                    {
                        this.$vux.toast.text("申请成功,请耐心等待");
                        this.$router.push({path:"/user/center"})
                    }else{
                        this.$vux.toast.text(res.message);
                    }
                    this.lock = false;
                }).catch(err=>{
                    this.lock = false;
                    this.$vux.toast.text(err.message);

                })
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
