<template >
    <div class="page-index">
        
  <img src="@/assets/images/loading.png" alt="" width='150px' height="150px" class="loading">
    <!-- <img src="@/assets/images/index_bg.png" alt="" class="index-bg">
        <div class="index-content">
            <div class="logo">
                <img src="@/assets/images/index_logo.png" alt="">
            </div>
            <div class="index-title">{{$store.state.init.app_name}}</div>
            <div class="index-decs">{{$store.state.init.app_describe}}</div>
        </div> -->
    </div>
</template>
<script>
   import router from '@/router';
export default {
    components: { 
        
    },
    data () {
        return {

        }
    },  
    mounted () {
        this.getUserinfo();
    },
    methods: {
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                router.replace({path:"/mine/center"});
            }).catch(err =>{
                //this.$vux.toast.text(err.message);
               // console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        
    }
}
</script>
<style lang="less" scoped>
    .page-index{
        position: relative;
        height: 100%;
        .loading{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    z-index: 999;
  }
        .index-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            z-index: 1;
            pointer-events: none;
        }
        .index-content{
            position: relative;
            z-index: 9;
            color: #fff;
            padding-top: 3.5rem;
            .logo{
                width: 5.1875rem;
                height: 5.1875rem;
                margin: 0 auto 0.75rem;
                img{
                    width: 100%;
                    height: 100%;
                    display: block;
                }
            }
            .index-title{
                line-height: 2.125rem;
                font-size: 1.375rem;
                text-align: center;
            }
            .index-decs {
                color: #a7c6fd;
                font-size: 1rem;
                line-height: 1.5625rem;
                padding: 0 1.625rem;
                width: 13.5rem;
                margin: 0 auto;
                position: relative;
                text-align: center;
                &:before,&:after{
                    display: block;
                    content: '';
                    height: 1px;
                    width: 1rem;
                    transform: translateY(-50%);
                    top: 50%;
                    
                    background-color: #fff;
                    position: absolute;
                }
                &:before{
                    left: 0;
                }
                &:after{
                    right: 0;
                }
            }
        }
    }
</style>


