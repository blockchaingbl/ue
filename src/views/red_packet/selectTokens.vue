<template>
    <div class="select-tokens">
        <ul class="select-list">
            <li v-for="(item,index) in tokens" :key="index" class="select-items" @click="selectIcon(item)">
                 <span>{{item.token}}</span>  
                 <i class="iconfont" :class="item.isActive==true? 'icon-xuanzhong':''"></i>     
            </li>
        </ul>
    </div>
</template>

<script>
export default {
  data() {
    return {
      isActive: false,
      tokens: [
        { token: "AATC", isActive: true },
        { token: "BTC", isActive: false },
        { token: "ETH", isActive: false },
        { token: "ACT", isActive: false },
        { token: "LET", isActive: false },
        { token: "USC", isActive: false }
      ]
    };
  },
  methods: {
    selectIcon(item) {
      this.tokens.forEach(obj => {
        obj.isActive = false;
      });
      item.isActive = !item.isActive;
    }
  }
};
</script>

<style lang="less" scoped>
.select-tokens {
  .select-list {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #fff;
    bottom: 1px solid red;
    padding-left: 0.94rem;
    .select-items {
      height: 2.625rem;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-right:0.94rem;
      background: #fff;
      i {
        font-size: 1.4rem;
        color: #7f3fc1;
      }
    }
  }
}
</style>

