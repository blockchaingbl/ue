<template lang="pug">
  div.app-download
    div.app-innner
      div.app-bg
      div.app-inf
        h2.app-title 多平台终端接入
        div.sub-app-title 覆盖iOS、Android、Wiindows、Mac多个平台，支持全业务功能
        div.btn-app-download-group
          a.btn-app-download(
            href=""
            target="_blank"
          )
            i.icon.iconfont &#xe72c;
            span iOS版下载
          a.btn-app-download.android(
            href=""
            target="_blank"
          )
            i.icon.iconfont &#xe603;
            span Android
</template>
<style lang="less" scoped>
  @import '../../../assets/css/variable.less';
  .app-download {
    height: 320px;
    width: 100%;
    margin-top: 20px;
    background: url(../../../assets/images/bg-appdownload.jpg) no-repeat;
    text-align: center;
    .app-innner {
      margin: 0 auto;
      // width: 1100px;
      height: 100%;
      display: inline-block;
    }
    .app-bg {
      width: 347px;
      height: 100%;
      background: url(../../../assets/images/bg-phone.png) 0 bottom no-repeat;
      margin-right: 180px;
      float: left;
    }
    .app-inf {
      float: left;
      margin-top: 70px;
      text-align: left;
      h2.app-title {
        font-size: 40px;
        color: @color-theme-sub;
        margin: 0;
      }
      .sub-app-title {
        font-size: 20px;
        color: #fff;
        margin-top: 15px;
      }
    }
    .btn-app-download-group {
      margin-top: 40px;
      .btn-app-download {
        width: 235px;
        height: 60px;
        line-height: 60px;
        background-color: #fff;
        color: #222;
        display: inline-block;
        text-align: center;
        border-radius: 3px;
        font-size: 20px;
        font-weight: lighter;
        margin-right: 40px;
        .iconfont {
          font-size: 26px;
          line-height: 1;
          margin-right: 20px;
        }
        &.android {
          background-color: @color-theme-sub;
          color: #fff;
        }
      }
    }
  }
</style>
