<template>
    <div class="packet-list">
        <div class="header-tabs">
             <i class="iconfont icon-fanhui1" @click="onClickBack"></i>
             <ul class="ul-tabs">
                 <li :class="dataType==0?'active':''" @click="choose(0)">我收到的</li>
                 <li :class="dataType==1?'active':''" @click="choose(1)">我发出的</li>
             </ul>
        </div>

        <div class="tab-content" v-show="dataType==0">
            <div class="top-portrait">
                <div class="top-info">
                    <div class="portrait-img"><img src="../../assets/images/packet_list.png" alt=""></div>
                    <p>{{ name }}共收到</p>   
                    <p><span>{{ num }}</span>个红包</p>
                </div>
            </div>
            <div class="packet-content">
                <span class="min-title">收到的红包已存入您的托管账户，可上链到本地钱包</span>
                <ul class="packet-ul">
                    <li v-for="(n,index) in 14" :key="index" class="packet-item">
                        <router-link to="/red_packet/receiveDetail">
                            <div class="packet-detail">
                                <p>{{name}}的外婆</p>
                                <span>07-07</span>
                            </div>
                            <div class="packet-detail">
                                <p>36.07496 &nbsp;AATC</p>
                            </div>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" v-show="dataType==1">
            <div class="top-portrait">
                <div class="top-info">
                    <div class="portrait-img"><img src="../../assets/images/packet_list.png" alt=""></div>
                    <p>{{ name }}共发出</p>   
                    <p><span>{{ num }}</span>个红包</p>
                </div>
            </div>
            <div class="packet-content packet2">         
                <ul class="packet-ul">
                    <li v-for="(n,index) in 6" :key="index" class="packet-item">
                        <router-link to="/red_packet/sendDetail">
                            <div class="packet-detail">
                                <p>拼手气红包</p>
                                <span>06-30</span>
                            </div>
                            <div class="packet-detail">
                                <p>36.07496 &nbsp;DLM</p>
                                <span>进行中&nbsp;0/3</span>
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
      name: "周杰伦",
      num: "4",
      dataType: 0
    };
  },
  methods: {
    choose(type) {
      this.dataType = type;
    },
    onClickBack(){
      this.$router ? this.$router.back() : window.history.back()
    }
  }
};
</script>

<style lang="less" scoped>
.packet-list {
  .header-tabs {
    height: 2.625rem;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    background: #fff;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    i {
      position: absolute;
      left: 0.94rem;
      top: 50%;
      transform: translateY(-50%);
    }
    .ul-tabs {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 8.53rem;
      height: 1.56rem;
      border: 1px solid #de4150;
      border-radius: 3px;
      li {
        width: 50%;
        height: 100%;
        text-align: center;
        font-size: 0.875rem;
        color: #de4150;
        &.active {
          background-color: #de4150;
          color: #fff;
        }
      }
    }
  }
  .tab-content {
    .top-portrait {
      background-color: #f5f5f5;
      width: 100%;
      height: 9.66rem;
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
          width: 4.06rem;
          height: 4.06rem;
          border-radius: 50%;
          overflow: hidden;
          margin: 0 auto;
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
          span {
            font-size: 1.875rem;
            color: #c63845;
          }
        }
      }
    }
    .packet-content {
      background: #fff;
      width: 100%;
      height: 100%;
      // margin-top: 14.16rem;
      // &.packet2{
      //    margin-top:12.285rem;
      // }
      span {
        color: #999;
        font-size: 0.75rem;
        background: #fff;
        &.min-title {
          height: 2.06rem;
          padding-left: 0.94rem;
          display: flex;
          justify-content: flex-start;
          align-items: center;
          position: relative;
        }
        &.min-title:after{
          content: '';
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

