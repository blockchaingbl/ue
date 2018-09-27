<template lang="html">
<scroller @on-scroll-bottom="onScrollBottom" lock-x ref="scrollerEvent">
<div :id="page_name">
    <swiper auto v-if="adverst.length>0">
        <swiper-item class="swiper_item" v-for="(x,k) in adverst" :key="k" @click.native="open(x)">
            <img :src="x.image" alt="" class="swiper_image">
            <p class="swiper_desc">{{x.name}}</p>
        </swiper-item>
    </swiper>
    <group-title>文章</group-title>
    <div v-if="article_lists.length>0">
        <div style="margin:0 0.75rem 0.625rem;overflow: hidden;" v-for="item in article_lists" @click="turnDetail(item)">
            <div class="m-img">
                <img :src="item.image" alt="">
            </div>
            <div class="m-title">{{item.title}}</div>
            <div class="m-time">{{item.create_time}}</div>
        </div>
        <load-more tip="正在加载 . . ." v-if="loading"></load-more>
        <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
    </div>
    <nodata v-if="article_lists.length==0" :datatip="'暂无数据'"></nodata>
</div>
</scroller>
</template>
<script>
import {  LoadMore , Divider , Flexbox , FlexboxItem , Swiper , SwiperItem , GroupTitle , Masker } from 'vux';
import Nodata from "@/components/nodata";
import router from '@/router';
export default {
    components: {
        Flexbox,
        FlexboxItem,
        LoadMore,
        Divider,
        Swiper,
        SwiperItem,
        GroupTitle,
        Masker,
        Nodata
    },
    data () {
        return {
            page_title:router.currentRoute.meta.title,
            page_name:router.currentRoute.name,
            formData:{page:1},
            article_lists : [],
            lock : false,
            adverst : [],
            index:0
        }
    },
    mounted () {
       this.getAdverst();
       this.getArticles();
    },
    methods:{
      getArticles(){
        if(this.lock){
            return false;
        }
        this.lock = true;
        this.loading = 1;
        this.$http.post('api/app.cms/article',this.formData).then(res => {
          if(res.data.articles.length>0){
              this.article_lists =  this.article_lists.concat_unk(res.data.articles,"id");
              this.lock=false;
          }
          this.loading = 0;
          this.formData.page++;
        })
        .catch((err) => {

          this.loading = 0;
        })
      },
      onScrollBottom : function () {
          if(!this.lock){
              this.getArticles();
          }
      },
      turnDetail(x){
          var domain = document.domain;
          var url = `http://${domain}/article/${x.id}`
          try{
              App.open_type('{"url":"'+url+'"}');
          }catch(err){
              window.location.href = url;
          }
      },
      getAdverst(){
        this.$http.post('api/app.cms/adverst/article',{}).then(res => {
            this.adverst = res.data.adversts;
        })
      },
      open(item){
          try{
              App.open_type('{"url":"'+item.url+'"}');
          }catch(err){
              window.location.href = item.url;
          }
      }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    #articleIndex{
        .main_head{
            background-color:#2e3d6c;
            height:4rem;
            padding: 0.75rem;
        }
        .market_box{
            color: white;
        }

        .m-img {
            display: block;
            position: relative;
            width: 100%;
            height: 5.625rem;
            position: relative;
            overflow: hidden;
            img{
                width: 100%;
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                display: block;
            }
        }

        .m-title {
            font-weight: bold;
            font-size: 0.9375rem;
            line-height: 1.25rem;
            margin-top: 0.3125rem;
        }

        .m-time {
            font-size: 0.75rem;
            line-height: 1rem;
            color: #999;
        }

    }
    .swiper_image{
        width:100%;
        height:100%;
    }
    .swiper_item{
        position:relative;
    }
    .swiper_desc{
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3.375rem;
        font-size: 0.9375rem;
        padding: 1.25rem 4.25rem 0.75rem 0.75rem;
        margin: 0;
        background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, rgba(0, 0, 0, 0)), to(rgba(0, 0, 0, 0.7)));
        background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0, rgba(0, 0, 0, 0.7) 100%);
        color: #fff;
        text-shadow: 0 1px 0 rgba(0, 0, 0, 0.5);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        word-wrap: normal;
    }



</style>
