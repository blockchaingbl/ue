<template>
    <div class="packet-detail">
        <div class="red-packet-title">
            <i class="iconfont icon-fanhui1" @click="onClickBack"></i>
            <h1>{{name}}红包</h1>
        </div>
        <div class="detail-content">
            <div class="top-portrait">
                <div class="top-info">
                    <div class="portrait-img"><img src="../../assets/images/packet_list.png" alt=""></div>
                    <p>{{ name }}的AATC礼包</p>   
                    <p>恭喜发财，吉祥如意</p>
                </div>
            </div>
           <div class="packet-content">
            <span class="min-title">已领取 1/2 个，共 1/2 AATC</span>               
            <ul class="packet-ul">
                <li v-for="(n,index) in 14" :key="index" class="packet-item">
                    <router-link to="">
                        <div class="min-img"><img src="../../assets/images/packet_list.png" alt=""></div>
                        <div class="packet-detail">
                            <p>{{name}}</p>
                            <span>07-07 11:37</span>
                        </div>
                        <div class="packet-detail">
                            <p>36.07496 &nbsp;AATC</p>
                        </div>
                    </router-link>
                </li>
            </ul>
        </div>
        </div>
        

    </div>
</template>

<script>
export default {
  data() {
    return {
      name: "AVATAR"
    };
  },
  methods:{
    onClickBack(){
      this.$router ? this.$router.back() : window.history.back()
    }
     
  }
};
</script>

<style lang="less" scoped>
.packet-detail {
  .red-packet-title {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
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
      height: 2.656rem;
      position: absolute;
      left: 0;
      line-height: 2.656rem;
      padding-left: 0.9375rem;
    }
  }
  .detail-content {
    .top-portrait {
      background: #f5f5f5 url(../../assets/images/red_detail_bg.png) no-repeat;
      background-size: 100%;
      width: 100%;
      height: 10.75rem;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 2.625rem;
      // position: fixed;
      // top: 2.625rem;
      // left: 0;
      // right: 0;
      // z-index: 100;
      .top-info {
        .portrait-img {
          width: 3.125rem;
          height: 3.125rem;
          border-radius: 50%;
          overflow: hidden;
          margin: 0.5rem auto 0;
          img {
            width: 100%;
            height: 100%;
          }
        }
        p {
          text-align: center;
          font-size: 0.875rem;
          line-height: 0.875rem;
          margin: 0.75rem 0 0;
        }
      }
    }
    .packet-content {
      span {
        color: #999;
        font-size: 0.75rem;
        background: #fff;
        &.min-title {
          height: 2.625rem;
          padding-left: 0.94rem;
          display: flex;
          justify-content: flex-start;
          align-items: center;
          // position: fixed;
          // top: 13.4rem;
          // left: 0;
          // right: 0;
          // z-index: 999;
        }
        &.min-title:after {
          content: "";
          position: absolute;
          width: 100%;
          height: 1px;
          background: #f0f0f0;
          left: 0.94rem;
          bottom: 0;
        }
      }
      .packet-ul {
        width: 100%;
        height: 100%;
        padding-left: 0.94rem;
        background: #fff;
        // overflow: scroll;
        // margin-top: 12.8rem;
        .packet-item {
          width: 100%;
          padding-right: 0.94rem;
          border-bottom: 1px solid #f0f0f0;
          a {
            height: 3.68rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            color: #22262d;
            .min-img {
              width: 2.1875rem;
              height: 2.1875rem;
              border-radius: 50%;
              overflow: hidden;
              vertical-align: middle;
              margin-right: 0.625rem;
              img {
                width: 100%;
                height: 100%;
              }
            }
            .packet-detail {
              width: 50%;
              height: 100%;
              padding-top: 0.78rem;
              p {
                font-size: 0.94rem;
                line-height: 0.94rem;
                margin-bottom: 0.5rem;
              }
              &:last-child {
                text-align: right;
              }
            }
          }
        }
      }
    }
  }
}
</style>
