<template lang="pug">
  div.banner
    el-carousel(
      height="436px"
      :autoplay="autoplay"
    )
      el-carousel-item(
        v-for="item in banner"
        :key="item.id"
      )
        img.banner-image(
          :src="item.image"
        )
    div.news
      ul.news-list
        li.news-item(
          v-for="item in 4"
        ) 
          router-link(
            to="/"
            v-text="`开启第六期免费上币投票活动(02-05)`"
          )
</template>
<script>
export default {
  props: ['banner'],
  data () {
    return {
      autoplay: true
    }
  }
}
</script>
<style lang="less">
  @import '../../../assets/css/banner.less';
  .banner {
    position: relative;
    height: 436px;
    .banner-image {
      width: 100%;
      height: 100%;
      min-width: 100%;
    }
    .news {
      width: 100%;
      height: 57px;
      line-height: 57px;
      position: absolute;
      bottom: 0;
      left: 0;
      z-index: 999;
      background-color: rgba(0, 0, 0, .3);
      color: #fff;
      .news-list {
        padding: 0 40px;
        font-size: 16px;
        .news-item {
          width: 25%;
          display: inline-block;
          text-align: center;
          &:first-child {
            text-align: left;
          }
          &:last-child {
            text-align: right;
          }
        }
        a {
          color: #fff;
          padding: 0 20px;
          overflow: hidden;
          text-overflow:ellipsis;
          white-space: nowrap;
          display: block;
        }
      }
    }
  }
</style>
