<template lang="html">
    <div :id="page_name">
        <div class="content" v-html="about"></div>
    </div>
</template>
<script>
    import router from '@/router';
    export default {
        components: {
        },
        data () {
            return {
                page_name:router.currentRoute.name,
                about:''
            }
        },
        created () {

            this.$http.post('/api/app.cms/connect/about',{config:'NOTICE_TEXT'}).then(res=>{
                if(res.errcode==0)
                {
                    this.about = res.data.about
                }
            })


        },
        methods:{
        },
        updated(){
           $('#notice>.content').find('a').click(function (e) {
               var src = $(this).attr('href');
               e.preventDefault();
                if(src!=='')
                {
                    App.open_type('{"url":"'+src+'"}');
                }
                return false;
           })
        }
    }
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    #notice{
        min-height: 100%;
        background: #fff;
        padding: 0.625rem;
        .title{
            font-size:1.5rem;
            text-align:center;
        }
        .content{
            width:100%;
            p{
                font-size: 0.875rem;
                line-height: 1.125rem;
                margin-bottom: 0.625rem;
                &:last-child{
                    margin-bottom: 0;
                }
            }
            img{
                max-width: 100%;
            }
        }
    }

</style>
