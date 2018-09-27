<template>
  <div class="marquee-box">
    <div class="marquee-content" ref="out">
      <p :class="speed">
        <span class="text1" ref="in" >{{content}}</span>
        <span class="text2" v-if="showtwo||run">{{content}}</span>
      </p>
    </div>
  </div>
</template>
<script>
  export default {
    name: 'VueMarquee',
    data (){
      return{
        run: true,
        pWidth: '',
      }
    },
    props: {
      content: {
        default: "暂无内容",
        type: String
      },
      speed: {
        default: 'middle',
        type: String
      },
      showtwo: {
        default: true
      }
    },
    mounted (){
      // let out = document.getElementById(this.pid.out).clientWidth;
      // let _in = document.getElementById(this.pid.in).clientWidth;
      var _this = this;
      this.$nextTick(()=>{
        let out = _this.$refs.out.clientWidth;
        let _in = _this.$refs.in.clientWidth;
        _this.run=_in>out?true:false;
      });
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="less" scoped>
  .marquee-box {
    background-size: 24px 24px;
  }
  .marquee-content{
    overflow: hidden;
  }
  .marquee-content p{
    display: inline-block;
    white-space: nowrap;
    margin: 0;
    color:#fff;
  }
  .marquee-content span{
    display: inline-block;
    white-space: nowrap;
    padding-right: 40px;
  }
  .quick{
    -webkit-animation: marquee 5s linear infinite;
    animation: marquee 5s linear infinite;
  }
  .middle{
    -webkit-animation: marquee 15s linear infinite;
    animation: marquee 15s linear infinite;
  }
  .slow{
    -webkit-animation: marquee 25s linear infinite;
    animation: marquee 25s linear infinite;
  }
  @-webkit-keyframes marquee {
    0%  { -webkit-transform: translate3d(126%,0,0); }
    100% { -webkit-transform: translate3d(-126%,0,0); }
  }
  @keyframes marquee {
    0%  { transform: translateX(126%); }
    100% { transform: translateX(-126%);}
  }
</style>
