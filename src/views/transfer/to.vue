<template lang="html">
    <div class="page-to">
        <group class="vux-1px-b">
            <x-input title="对方账户" v-model="to"  placeholder="请输入接收人手机/用户名"  type="text"  :required="true"></x-input>
        </group>
       <group title="资产将实时转入对方账户,无法退回">
       </group>
        <div class="grant-btn-box">
            <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="send_to" v-show="to!==''">下一步</x-button>
            <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;background-color: #9B9B9B"  v-show="to==''">下一步</x-button>
        </div>
        <div class="out_order">
            <router-link  :to="{path:'/transfer/order_out'}">
                <div class="bottom-txt">转出记录</div>
            </router-link>
        </div>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
    import { Loading, TransferDomDirective as TransferDom  } from 'vux';

    export default {
    directives: {
        TransferDom
    },
    components: {
        Loading
    },
    data () {
        return {
            to:'',
            loading:false
        }
    },
    mounted () {

    },
    methods: {
        send_to(){
            if(this.to=='')
            {
                this.$vux.toast.text("请输入接收人用户名或手机号");
                return false;
            }
            this.loading = true;
            this.$http.post('/api/app.user/transfer/userinfo',{to:this.to})
                .then(res => {
                    this.loading = false;
                    let path = '/transfer/send/'+res.data.user.id;
                    this.$router.push({path:path})
                })
                .catch(err=>{
                    this.loading = false;
                    this.$vux.toast.text(err.message);
                })
        }
    },
}
</script>
<style lang="less" scoped>
@import "../../assets/css/variable.less";
.page-to{
    .grant-btn-box{
        margin-top: 2rem;
    }
    .out_order{
        position: absolute;
        bottom: 11rem;
        width: 100%;
        text-align: center;
    }
}
</style>


