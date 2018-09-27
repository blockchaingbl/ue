<template lang="html">
<div class="reset">
    <div class="reset-box">
        <group class="reset-item">
            <x-input ref="mobile"  name="mobile" :value="15015075294" keyboard="number">
                <x-button slot="right" plain  type="primary" mini style="border-radius:99px;">验证码</x-button>
            </x-input>
        </group>
        <group class="reset-item">
            <x-input placeholder="请输入短信验证码"></x-input>
        </group>
        <group class="reset-item">
            <x-input placeholder="新密码"></x-input>
        </group>
        <group class="reset-item">
            <x-input placeholder="再输一遍密码"></x-input>
        </group>
    </div>
    <div class="reset-btn-box">
        <box gap="30px 35px 0">
            <x-button type="primary" style="border-radius:99px;" class='found-btn' link="/user/wallet/create">修改</x-button>
        </box>
    </div>
</div> 
</template>
<script>

export default {
    components: {
        //stepone:() => import('@/views/login/inc/stepone'),

    },
    data () {
        return {
            hasWallet:0,

        }
    },
    mounted () {
        
    },
    methods:{
        getCode(){
      
        },
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .reset{
        .reset-item{
            padding-left: 0.625rem;
            background: #fff;
            .weui-cells{
                font-size: @fs-middle;
                margin: 0;
                padding-top: 0;
                &:before{
                    display: none;
                }
                .weui-btn_plain-primary{
                    background: #628df9;
                    border-color: #628df9;
                    color: #fff;
                }
                input::-webkit-input-placeholder {color:#999;
                    color: #bcbcbc;
                }
            }
            &:last-child{
                .weui-cells{
                    &:after{
                        display: none;
                    }
                }
            }
        }
        .reset-btn-box{
            .weui-btn_primary{
                background: #bbb;
                height: 2.5rem;
            }
        }
    }
</style>
