<template >
<div class="error-404">
    <img src="@/assets/images/error_404.png" class="error-image" alt="">
    <p class="error">404</p>
    <p>您找到页面走丢了</p>
    <router-link to="/" class="error-btn">返回首页</router-link>
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
   
    },
    methods: {
        
    }
}
</script>
<style lang="less" scoped>
    @import '../../assets/css/variable.less';
    
    .error-404{
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
            font-family: arial;
        }
    }
</style>


