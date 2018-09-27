<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:''}" :right-options="{showMore: false}" class="inter_wallet_header">{{page_title}}</x-header>

        <group>
            <cell is-link  :link="{path:'/wallet_international/manage/'+address}" v-for="(wallet, address) in this.$store.state.wallets" :key="address">
                <span slot="title">
                    <div class="wallet_name">{{wallet.name}}</div>
                    <div class="wallet_address">{{address}}</div>
                </span>
            </cell>
        </group>

    </div>
</template>
<script>
    import { XHeader,Actionsheet,Cell,Badge} from 'vux';
    import router from '@/router';
    export default {
        components: {
            "x-header":XHeader,
            "actionsheet":Actionsheet,
            "cell":Cell,
            "badge":Badge
        },
        data () {
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name
            }
        },
        mounted () {
        },
        methods: {
        }
    }
</script>
<style lang="less" scoped>
    #wallet_international_manage{
        .wallet_name{
            text-align: left;
            color:#4179cf;
            font-size:1rem;
        }
        .wallet_address{
            text-align: left;
            width:80%; overflow: hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
            font-size:.8rem;
            color:#606266; line-height: 25px;
        }
    }


</style>


