<template lang="html">
    <div class="page-grant">
        <div>
            <div class="candy-basic-opera">
                <group class="candy-numb" title="姓名">
                    <x-input  v-model="name" type="text"   placeholder="请输入姓名"  :required="true" ></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="手机号">
                    <x-input  v-model="mobile"  placeholder="请输入手机号" :max="11" keyboard="number"  type="number"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="所在市县">
                    <x-input  v-model="area" type="text"   placeholder="请输入所在市县"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="用途">
                    <x-input  v-model="use_for" type="text"   placeholder="请填写用途"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="申请数额">
                    <x-input  v-model="amount" type="number"   placeholder="请填写请填写数额"  :required="true" ></x-input>
                </group>
                <popup-radio title="预计归还时间" :options="back_day_arr" v-model="back_day">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">预计归还时间</p>
                </popup-radio>
            </div>
            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="apply_money">申请</x-button>
            </box>
        </div>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
import { Datetime, Loading,LoadMore,PopupRadio,XSwitch, TransferDomDirective as TransferDom } from 'vux'
    export default {
        directives: {
            TransferDom
        },
        components: {
            Datetime,
            Loading,
            LoadMore,
            PopupRadio,
            XSwitch
        },
        data() {
            return {
                name:'',
                area:'',
                mobile:'',
                use_for:'',
                amount:'',
                lock:false,
                loading:false,
                back_day:'15',
                back_day_arr:[{key:'15',value:"15天"},{key:'30',value:"30天"}],

            };
        },
        mounted() {

        },
        methods: {
            apply_money(){
                if(this.lock)
                {
                    return false;
                }
                this.lock = true;
                let form = {
                    name:this.name,
                    mobile:this.mobile ,
                    area:this.area,
                    use_for:this.use_for,
                    back_day:parseInt(this.back_day),
                    amount:this.amount
                }
                this.$http.post('api/app.apply/credit/apply',form).then(res=>{
                    if(res.errcode==0)
                    {
                        this.$vux.toast.text("申请成功");
                    }else{
                        this.$vux.toast.text(res.message);
                    }
                    this.lock = false;
                }).catch(err=>{
                    this.lock = false;
                    this.$vux.toast.text(err.message);

                })
            },
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
