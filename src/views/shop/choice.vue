<template lang="html">
    <div class="page-grant">
        <div>
            <div class="candy-senior-opera">
                <group class="candy-numb" title="">
                    <radio :options="urls" v-model="url"></radio>
                </group>
            </div>
            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="nextPage">下一步</x-button>
            </box>
        </div>
    </div>
</template>
<script>
import {Radio } from 'vux'
    export default {
        directives: {

        },
        components: {
            Radio
        },
        data() {
            return {
                urls:[
                    {key:'/shop/card',value:"兑换购物卡"},
                    {key:"/shop/cardorder",value:"我兑换的购物卡"},
                    {key:'#',value:"我有资产要变现"},
                    {key:"##",value:"我有资产要收回"},
                    ],
                url:"/shop/card",
                pid_mobile:'',
            };
        },
        mounted() {
        },
        methods: {
            nextPage(){
                if(this.url)
                {
                    this.$router.push({path:this.url})
                }
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
    }

</style>
