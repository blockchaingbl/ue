<template>
  <div class="new_loading">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast" style="position: absolute;">
      <i class="weui-loading weui-icon_toast"></i>
      <p class="weui-toast__content">正在同步区块信息</p>
    </div>
  </div>
</template>

<script type="text/javascript">
  export default {
    computed: {

    }
  }
</script>
<style scoped type="text/css" lang="less">
  .new_loading{
    .weui-toast__content{
      font-size: 12px;
    }
  }
</style>