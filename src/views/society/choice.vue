<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-senior-opera">
                <group class="candy-numb" title="挂靠社群" v-if="!attached">
                    <x-input  v-model="pid_mobile" type="number"   placeholder="请填写社群手机号">
                        <x-button slot="right" type="primary" mini @click.native="attached_send">申请挂靠社群</x-button>
                    </x-input>
                </group>
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
                urls:[{key:"/society/apply",value:"申请成立社群"}],
                url:"/society/apply",
                pid_mobile:'',
                pid:0,
                attached:0,
                loading:true
            };
        },
        mounted() {
            this.getDetail();
        },
        methods: {
            getDetail(){
                    this.$vux.loading.show({
                        text: ''
                    })
                    this.$http.post('/api/app.user/account/info',{}).then(res=>{
                        this.attached = res.data.account_info.attached;
                        this.$vux.loading.hide()
                        this.loading = false;
                    })
                        .catch(error=>{
                            this.loading = false;
                            this.$vux.loading.hide()
                        })
                },
            nextPage(){
                if(this.url)
                {
                    this.$router.push({path:this.url})
                }
            },
            attached_send(){
                this.$vux.loading.show({
                    text: ''
                })
                this.$http.post('api/app.apply/apply/attached',{pid_mobile:this.pid_mobile}).then(res=>{
                    this.$vux.loading.hide()
                    this.$vux.toast.text(res.message);
                })
                    .catch(error=>{
                        this.$vux.loading.hide()
                        this.$vux.toast.text(error.message);
                    })
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
    }

</style>
