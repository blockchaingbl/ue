<template lang="html">
<div :id="page_name">
  <div class="title">
    {{article.title}}
  </div>
  <group-title class="create_time">{{article.create_time}}</group-title>
   <load-more :show-loading="false" background-color="#fbf9fe"></load-more>
   <div class="content" v-html="article.content">
   </div>
</div>
</template>
<script>
import { GroupTitle , LoadMore } from 'vux';
import router from '@/router';
export default {
    components: {
      GroupTitle,
      LoadMore
    },
    data () {
        return {
            page_title:router.currentRoute.meta.title,
            page_name:router.currentRoute.name,
            article:{}
        }
    },
    mounted () {
      const id =  this.$route.params.id;
      this.getArticle(id);
    },
    methods:{
      getArticle(id){
        this.$http.post('api/app.cms/article/detail',{id:id}).then(res => {
            this.article = res.data.article;
            if(res.data.return_cp>0){
                this.$vux.toast.text('算力增加'+res.data.return_cp);
            }
        })
      }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    #articleDetail{
        .title{
            font-size:1.5rem;
            text-align:center;
        }
        .create_time{
            text-align:center;
        }
        .content{
            width:100%;
            overflow:hidden;
            padding:0 20px 0 20px;
        }
    }

</style>
