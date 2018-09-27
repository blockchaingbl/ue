<template>
<div>
    <!-- 资产密码弹窗 -->
    <div class="dialog-black" @click="closeDialog" v-show="pwdShow"></div>   
    <div class="dialog-box" v-show="pwdShow">
        <h3 class="dialog-title"><i class="iconfont icon-chahao" @click="closeDialog"></i>资产密码</h3>
        <form class="dialog-content">
            <h4>红包总额</h4>
            <p><span>{{ number1 }}</span>&nbsp;{{inputValue[0]}}</p> 
            <input class="dialog-input" v-model="pwd" type="password" placeholder="请输入托管账户资产密码"/>
            <button class="dialog-btn" type="button" @click="sendDialog">确定</button>
        </form>
    </div>

    <!-- 红包已备好弹窗 -->
    <div class="dialog-black" @click="closeDialog"  v-show="sendShow"></div>
    <div class="dialog-box dialog-red" v-show="sendShow">
        <h3 class="dialog-title title-red"><i class="iconfont icon-chahao red-icon" @click="closeDialog"></i></h3>
        <form class="dialog-content">
            <h4 class="red-ready">红包已备好</h4>
            <p class="red-num">{{ number2 }}个红包&nbsp;&nbsp;<span>{{ number1 }}</span>&nbsp;{{inputValue[0]}}</p> 
            <button class="yellow-btn" type="button" @click="closeDialog">发红包</button>
        </form>
    </div>

</div>  
</template>

<script>
export default {
  props: {
    number1: {
      default: null
    },
    number2: {
      default: null
    },
    inputValue:{
      type:Array,
      default: ['AATC'],
    }
    },
  data() {
    return {
      sendShow: false,
      pwd: "",
      pwdShow: true
    }
  },
  methods: {
    closeDialog() {
      this.$emit("close", false);
      this.sendShow = false;
      this.pwdShow = true;
    },
    sendDialog(pwd) {
      this.sendShow = true;
      this.pwdShow = false;
    }
  }
};
</script>

<style lang="less" scoped>
.dialog-black {
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 999;
}
.dialog-box {
  width: 16.468rem;
  border-radius: 5px;
  background: #fff;
  overflow: hidden;
  position: fixed;
  top: 6.635rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1002;
  &.dialog-red {
    width: 19rem;
    padding-bottom: 0.5rem;
    background: url(../../../assets/images/red_dialog_bg.png) no-repeat;
    background-size: 100% 100%;
    box-shadow: 0 0 0.375rem 0.218rem rgba(0, 0, 0, 0.15);
  }
  .dialog-title {
    text-align: center;
    font-size: 0.875rem;
    color: #22262d;
    height: 2.375rem;
    line-height: 2.375rem;
    border-bottom: 1px solid #e6e6e6;
    font-weight: normal;
    position: relative;
    &.title-red {
      border-bottom: none;
    }
    i.icon-chahao {
      position: absolute;
      right: 0.875rem;
      top: 50%;
      transform: translateY(-50%);
      &.red-icon {
        right: 0.625rem;
        color: #f3bc78;
      }
    }
  }
  .dialog-content {
    padding: 0.625rem 0.75rem 0.72rem;
    h4 {
      color: #999;
      font-size: 0.81rem;
      font-weight: normal;
      text-align: center;
      &.red-ready {
        color: #f3bc78;
        font-size: 1.25rem;
      }
    }
    p {
      text-align: center;
      font-size: 0.875rem;
      color: #22262d;
      line-height: 0.875rem;
      margin: 0.635rem 0 1.1875rem;
      &.red-num {
        font-size: 0.94rem;
        color: #f3bc78;
        span {
          font-size: 0.94rem;
          color: #f3bc78;
          font-weight: normal;
        }
      }
      span {
        font-size: 1.25rem;
        font-weight: bold;
      }
    }
    .dialog-input {
      width: 100%;
      height: 2.125rem;
      border-radius: 5px;
      border: 1px solid #dadada;
      padding-left: 0.5rem;
      display: block;
      &::-webkit-input-placeholder {
        color: #c1c1c1;
        font-size: 0.875rem;
      }
    }
    .dialog-btn {
      width: 100%;
      height: 2.125rem;
      border-radius: 5px;
      border: none;
      font-size: 0.875rem;
      text-align: center;
      line-height: 2.125rem;
      color: #fff;
      background-color: #de4150;
      margin-top: 0.5rem;
    }
    .yellow-btn {
      width: 100%;
      height: 2.59rem;
      border-radius: 5px;
      border: none;
      font-size: 0.94rem;
      text-align: center;
      line-height: 2.59rem;
      color: #af303c;
      background-color: #f3bc78;
      margin-top: 7.375rem;
    }
  }
}
</style>

