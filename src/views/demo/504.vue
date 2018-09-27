<template >
<div class="error-504">
    <img src="@/assets/images/error_504.png" class="error-image" alt="">
    <p class="error">网络连接失败</p>
    <p>请检查您的网络</p>
    <div class="reload" v-on:click="reload()">刷新</div>
</div>

</template>
<script>

export default {
    components: { 
        
    },
    data () {
        return {
            
        }
    },  
    mounted () {
        // window.location.reload()
    },
    methods: {
        reload(){
            this.$router.go(-1)
        }
    }
}
</script>
<style lang="less" scoped>
@import '../../assets/css/variable.less';
    .error-504{
        height: 100%;
        background: #fff;
        text-align: center;
        font-size: @fs-middle;
        padding-top: 20%;
        color: #956d72;
        .error-image{
            width: 11.875rem;
            height: 11.875rem;
            display: block;
            margin: 0 auto 3.125rem;
        }
        .error{
            font-size: 2.1875rem;
        }
        .reload{
            color: #6b94f8;
            margin: 0 auto;
            padding: 0.625rem;
            width: 4rem;
        }
    }
</style>


