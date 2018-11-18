<template lang="html">
    <div :id="page_name">
        <div class="content" v-html="about"></div>
        <group>
            <cell :title="'客服'+(key+1)"  v-for="(item,key) in normal_kefu" :key="key" :link="{path:'/chat/p2p-'+item.user.accid}">
                <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe638;</i>
            </cell>
            <cell  :title="'行业节点/社群专属客服'+(key+1)"  :key="key" v-for="(item,key) in society_kefu" @click.native="kefu(item)" is-link>
                <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe638;</i>
            </cell>
        </group>
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
                about:'',
                normal_kefu:[],
                society_kefu:[],
                level:0
            }
        },
        created () {

            this.$http.post('/api/app.cms/connect/about',{config:'CONNECT'}).then(res=>{
                if(res.errcode==0)
                {
                    this.about = res.data.about;
                    this.normal_kefu = res.data.normal_kefu;
                    this.society_kefu= res.data.society_kefu;
                    this.level = res.data.level
                    localStorage.setItem('local_connect_version',res.data.connect_version)
                }
            })


        },
        methods:{
          kefu(item)
          {
            if(this.level>0)
            {
              this.$router.push({path:'/chat/p2p-'+item.user.accid})
            }
            else{
              this.$vux.toast.text('行业节点、社群专用');
            }

          }
        },
        updated(){
           $('#connect_text>.content').find('a').click(function (e) {
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
    #connect_text{
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
