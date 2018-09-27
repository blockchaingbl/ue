<template>
    <div class="red-packet-bg">
        <div class="red-packet-title">
            <i class="iconfont icon-fanhui1"  @click="onClickBack"></i>
            <h1>红包</h1>
            <router-link to="/red_packet/packetList"><i class="iconfont icon-jilu"></i>记录</router-link>    
        </div>
        <div class="red-packet-content">
            <div class="aatc-logo-bg">
                <img class="aatc-logo" src="@/assets/images/red_logo.png" alt="">
            </div>
            <div class="btn-groud">
                <router-link to="/red_packet/luckPacket">拼手气红包</router-link>
                <router-link to="/red_packet/normalPacket">普通红包</router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  data(){
    return{

    }
  },
  methods:{
    onClickBack(){
      this.$router ? this.$router.back() : window.history.back()
    }
  }
};
</script>

<style lang="less" scoped>
.red-packet-bg {
  width: 100%;
  height: 100%;
  background: url(../../assets/images/red_bg.png) no-repeat;
  background-size: 100% 100%;
  .red-packet-title {
    height: 2.656rem;
    background: #c63845;
    color: #fff;
    h1 {
      color: #fff;
      font-size: 1.06rem;
      width: 60%;
      text-align: center;
      position: absolute;
      line-height: 2.656rem;
      left: 50%;
      transform: translateX(-50%);
    }
    & > i {
      width: 20%;
      height:2.656rem;
      position: absolute;
      left: 0;
      line-height: 2.656rem;
      padding-left: 0.9375rem;
    }
    a {
      width: 20%;
      position: absolute;
      right: 0;
      height: 2.656rem;
      line-height: 2.656rem;
      padding-right: 0.9375rem;
      display: flex;
      justify-content: flex-end;
      font-size: 0.9375rem;
      color: #fff;
       @media screen and (max-width:320px) {
         &{width: 22%;}
       } 
      i {
        margin-right: 0.375rem;
      }
    }
  }
  .red-packet-content {
    width: 100%;
    .aatc-logo-bg {
      width: 100%;
      height: 12.25rem;
      background: url(../../assets/images/red_logo_bg.png) no-repeat;
      background-size: 100% 100%;
      padding-top: 3.375rem;
      .aatc-logo {
        width: 10.81rem;
        margin: 0 auto;
        display: block;
      }
    }
    .btn-groud {
      margin-top: 6.94rem;
      a {
        width: 17.656rem;
        height: 2.59rem;
        background: #f3bc78;
        display: block;
        border-radius: 5px;
        text-align: center;
        color: #af303c;
        font-size: 0.9375rem;
        line-height:  2.59rem;
        margin: 0 auto 1.25rem;
        font-weight: bold;
      }
    }
  }
}
</style>

