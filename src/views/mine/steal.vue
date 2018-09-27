<template lang="html">
    <div class="mine-steal">
        <div class="mine-banner">
            <img src="@/assets/images/mine_bg_new.jpg" alt="" class="banner-img">
            <div class="banner-centent">
                <div v-for="(item,index) in coinlist"  class="profit" :class="{'animate1': animate1[index].show }" v-on:click='animate(index,item)' v-if='index<10'>
                    <div class="img-text">{{item.coin_unit}}</div>
                    <div class="profit-text">{{item.amount}}</div>
                </div>
                <div class="self-assets flex-box" style="top:1rem;">
                    <div class="assets-text">
                        {{friend.username}}
                         的矿池
                    </div>
                    <!--<div class="assets-numb flex-1">{{vc_amount}}</div>-->
                </div>
                <div class="dug-type" v-bind:class="{ active: isdugcoin }">
                    <img src="@/assets/images/icon_gem_new1.png" class="scene-img" alt="">
                    <module-svg></module-svg>
                </div>
                <div class="dug-text" v-bind:class="{ active: isdugcoin }">
                    正在挖矿中...
                </div>
                <audio controls id="audio">
                  <source src="@/assets/images/9897.mp3" >
                </audio>
            </div>
        </div>
        <div class="rank-box">
            <div class="rank-top flex-box">
                <div class="rank-top-title flex-1">好友列表</div>
            </div>

            <div class="rank-block">
                <x-table :cell-bordered="false" style="background-color:#fff;">
                    <tbody class="block-box">
                    <tr v-for="user in user_list">
                        <td>
                            <div class="user-row">
                                <img src="@/assets/images/avatar.png" alt="" v-if="user.avatar==''">
                                <img v-bind:src="user.avatar" v-else>
                            </div>
                        </td>
                        <td style="text-align: left;"><span class="user-row-username"> {{user.username}}
                                <span class="iconfont friend_ico" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null" >&#xe8f8;</span>
                                <span class="iconfont friend_ico" v-else-if="user.user_fans_id > 0 && user.user_fans_id != null">&#xe619;</span>
                            </span></td>
                        <td style="text-align: right; padding-right:1rem;">
                           <XButton :mini="true" type="primary" @click.native="steal_user(user.id)">去偷币</XButton>
                        </td>
                    </tr>
                    </tbody>
                </x-table>
            </div>

            <Scroller v-on:load="getFriend" :loading="loading" :container="'.block-box'" ></Scroller>
        </div>
    </div>   
</template>
<script>
import { LoadMore, XTable, XButton } from "vux";
import router from "@/router";
import Scroller from "@/components/scroller";
import SVG from "@/views/mine/inc/svg";
export default {
  name: "mineCenter",
  components: {
    LoadMore,
    "x-table": XTable,
    XButton,
    Scroller,
    "module-svg": SVG
  },
  data() {
    return {
      loading: false,
      page: 1,
      user_list: [],
      coinlist: [],
      coinlength: null,
      _index: "",
      vc_amount: "",
      animate1: [
        {
          id: 0,
          show: false
        },
        {
          id: 1,
          show: false
        },
        {
          id: 2,
          show: false
        },
        {
          id: 3,
          show: false
        },
        {
          id: 4,
          show: false
        },
        {
          id: 5,
          show: false
        },
        {
          id: 6,
          show: false
        },
        {
          id: 7,
          show: false
        },
        {
          id: 8,
          show: false
        },
        {
          id: 9,
          show: false
        }
      ],
      isPlaying: false,
      isdugcoin: true,
      top_left: "",
      _width: "",
      setInterval: "",
      friend: { avatar: "", username: "" },
      user_id: 0
    };
  },
  mounted() {
    this.user_id = router.currentRoute.params["user_id"];
    this.page = 1;
    this.getFriend();
    this.getUserInfo(this.user_id);
    this.getCoin(this.user_id);
  },
  methods: {
    steal_user(user_id) {
      this.user_id = user_id;
      this.page = 1;
      this.getUserInfo(user_id);
      this.getCoin(user_id);
    },
    getCoin(user_id) {
      // console.log("res.data");
      this.$http
        .post("/api/app.mine/coin/compute_steal", { user_id: user_id })
        .then(res => {
          this.animate1 = [
            { id: 0, show: false },
            { id: 1, show: false },
            { id: 2, show: false },
            { id: 3, show: false },
            { id: 4, show: false },
            { id: 5, show: false },
            { id: 6, show: false },
            { id: 7, show: false },
            { id: 8, show: false },
            { id: 9, show: false }
          ];
          this.coinlist = res.data.mine_coin;
          if (res.data.mine_coin.length < 1) {
            this.coinlength = null;
            this.isdugcoin = true;
          } else {
            this.coinlength = res.data.mine_coin.length;
            this.isdugcoin = false;
          }
          console.log(res.data);
        })
        .catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
          console.log(err);
          //  this.Toast(err || '网络异常，请求失败');
        });
    },
    getUserInfo(user_id) {
      let _this = this;
      setTimeout(function() {
        _this.$http
          .post("/api/app.user/account/friend", { user_id: user_id })
          .then(res => {
            _this.friend = res.data.user;
          })
          .catch(err => {
            if (err.errcode) {
              _this.$vux.toast.text(err.message);
            }
            // console.log(err);
            //  this.Toast(err || '网络异常，请求失败');
          });
      }, 1000);
    },
    getFriend() {
      if (!this.loading && this.page >= 0) {
        this.loading = true;
        this.$http
          .post("/api/app.user/relations/friends", {
            page: this.page
          })
          .then(res => {
            //console.log(res.data);
            if (res.data.user_list.length > 0) {
              this.user_list = this.user_list.concat_unk(
                res.data.user_list,
                "id"
              );
              this.page++;
            } else {
              this.page = -1;
            }
            this.loading = false;
          })
          .catch(err => {
            if (err.errcode) {
              this.$vux.toast.text(err.message);
            }
            console.log(err);
            this.loading = false;
            //  this.Toast(err || '网络异常，请求失败');
          });
      }
    },
    animate(numb, item) {
      console.log(numb);
      var audio = document.querySelector("#audio");
      audio.currentTime = 0;
      audio.play();
      this.animate1[numb].show = true;
      this.coinlength--;

      console.log("animate:" + this.coinlength);
      this.$http
        .post("/api/app.mine/coin/fetch_steal", {
          id: item.id
        })
        .then(res => {
          console.log(res.data);
        })
        .catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
          console.log(err);
          //  this.Toast(err || '网络异常，请求失败');
        });
    }
  },
  watch: {
    coinlength() {
      if (this.coinlength < 1) {
        var _this = this;
        setTimeout(function() {
          _this.getCoin(_this.user_id);
        }, 1500);

        // console.log(this.coinlength);
      }
    }
  }
};
</script>

<style lang="less">
@import "../../assets/css/style.css";
@import "../../assets/css/variable.less";
.mine-steal {
  & .mine-banner {
    position: relative;
    height: 130.67vw;
    .banner-img {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      pointer-events: none;
      z-index: 1;
    }
    .banner-centent {
      position: relative;
      z-index: 2;
      height: 100%;
      & .banner-top {
        background: rgba(0, 0, 0, 0.3);
        height: 1.75rem;
        line-height: 1.75rem;
        padding: 0 0.625rem;
        color: #fff;
        font-size: @fs-middle;
        position: relative;
        overflow: hidden;
        p {
          line-height: 1.75rem;
          position: absolute;
          top: 0;
          white-space: nowrap;
        }
      }
      & .self-assets {
        position: absolute;
        top: 2.5rem;
        left: 0.625rem;
        background: #2b49ab;
        height: 2rem;
        // min-width:10.9375rem;
        border-radius: 1rem;
        padding: 0 1rem;
        color: #fff;
        font-size: @fs-middle;
        .assets-numb {
          margin-left: 0.5rem;
          text-align: center;
          font-size: 1.0625rem;
          font-family: arial;
          font-weight: bold;
        }
      }
      & .dug-type {
        position: absolute;
        transform: translateX(-50%);
        top: 30%;
        left: 50%;
        color: #fff;
        font-size: @fs-middle;
        width: 130px;
        height: 130px;
        overflow: hidden;
        border-radius: 50%;
        transition: all 0.3s ease;
        .scene-img {
          width: 3.25rem;
          height: 3.25rem;
          position: absolute;
          transform: translate(-50%, -50%);
          top: 50%;
          left: 50%;
          z-index: 9;
        }
      }
      .dug-type.active {
        transform: translateX(-50%) scale(1.5, 1.5);
      }
      .dug-text {
        position: absolute;
        transform: translateX(-50%);
        top: 78%;
        left: 50%;
        color: #fff;
        font-size: @fs-middle;
        transition: all 0.3s ease;
        font-size: 1rem;
        font-weight: bold;
        opacity: 0;
      }
      .dug-text.active {
        top: 64%;
        opacity: 1;
      }
      & .banner-bottom {
        position: absolute;
        left: 0;
        width: 100%;
        bottom: 0.75rem;
        justify-content: space-between;
        padding: 0 0.625rem;
        color: #fff;
        pointer-events: none;
        img {
          width: 100%;
          height: 100%;
          display: block;
        }
        .count-power {
          pointer-events: auto;
        }
        .power {
          height: 2.0625rem;
          min-width: 8rem;
          padding: 0 1rem;
          font-size: @fs-middle;
          border-radius: 1.5rem;
          margin: 0.5rem 0;
          background: linear-gradient(#a4c0fa, #8384f6);
          .power-numb {
            font-size: 1.125rem;
            text-align: center;
            margin-left: 0.5rem;
          }
        }
        .exalt {
          pointer-events: auto;
          display: block;
          color: #fff;
        }
        .cont-text,
        .exalt-text {
          font-size: @fs-middle;
          text-align: center;
        }
        .exalt-img {
          width: 2.8125rem;
          height: 2.8125rem;
          margin: 0 auto 0.25rem;
        }
      }
      .profit {
        position: absolute;
        text-align: center;
        color: #fff;
        font-size: @fs-small;
        min-width: 2.75rem;
        opacity: 1;
        z-index: 2;
        filter: alpha(opacity=100);
        background: url(../../assets/images/icon_gem_new.png) no-repeat top
          center;
        background-size: 2.75rem 2.75rem;
        .img-text {
          text-align: center;
          width: 200%;
          color: #2c3038;
          font-weight: bold;
          font-family: arial;
          font-size: 0.75rem;
          margin-left: -50%;
          height: 2.75rem;
          line-height: 2.65rem;
        }
        .profit-text {
          font-size: 0.9375rem;
        }

        &:nth-child(odd) {
          animation: anima 1s alternate infinite ease-in-out;
          -moz-animation: anima 1s alternate infinite ease-in-out; /* Firefox */
          -webkit-animation: anima 1s alternate infinite ease-in-out; /* Safari 和 Chrome */
          -o-animation: anima 1s alternate infinite ease-in-out;
        }
        &:nth-child(even) {
          animation: animat 0.8s alternate infinite ease-in-out;
          -moz-animation: animat 0.8s alternate infinite ease-in-out; /* Firefox */
          -webkit-animation: animat 0.8s alternate infinite ease-in-out; /* Safari 和 Chrome */
          -o-animation: animat 0.8s alternate infinite ease-in-out;
        }
        &:nth-child(1) {
          left: 10%;
          top: 41%;
          animation-duration: 0.5s;
        }
        &:nth-child(2) {
          left: 75%;
          top: 46%;
          animation-duration: 0.7s;
        }
        &:nth-child(3) {
          left: 78%;
          top: 8%;
          animation-duration: 0.9s;
        }
        &:nth-child(4) {
          left: 47%;
          top: 80%;
          animation-duration: 1s;
        }
        &:nth-child(5) {
          left: 74%;
          top: 26%;
          animation-duration: 0.6s;
        }
        &:nth-child(6) {
          left: 48%;
          top: 10%;
          animation-duration: 0.8s;
        }
        &:nth-child(7) {
          top: 60%;
          left: 16%;
        }
        &:nth-child(8) {
          top: 20%;
          left: 8%;
        }
        &:nth-child(9) {
          top: 64%;
          left: 72%;
        }
        &:nth-child(10) {
          left: 42%;
          top: 62%;
        }
        &.animate1 {
          animation: myfirst 0.6s;
          animation-fill-mode: forwards;
          opacity: 0;
          transition: 1.5s all ease;
          filter: alpha(opacity=0);
          pointer-events: none;
        }
      }
      #audio {
        display: none;
      }
    }
  }
  & .rank-box {
    .rank-top {
      height: 2.65625rem;
      padding: 0 0.625rem;
      background: #fff;
      .rank-top-title {
        font-size: @fs-big;
        color: #4c4c51;
      }
      // .rank-top-decs{
      //     font-size: @fs-middle;
      //     color: #999;
      // }
    }
    .rank-head {
      height: 1.75rem;
      background: #b5b7be;
      color: #fff;
      padding: 0 0.625rem;
      font-size: @fs-middle;
      text-align: center;
      .rank-head-item {
        &:last-child {
          min-width: 6rem;
        }
      }
      .ranking {
        width: 3rem;
      }
    }

    .rank-item {
      padding: 0 0.625rem;
      height: 2.625rem;
      background: #fff;
      margin-bottom: 0.3125rem;
      font-size: @fs-middle;
      text-align: center;
      .item-item {
        width: 6rem;
      }
      .ranking {
        width: 3rem;
      }
      .rank-numb {
        font-size: @fs-big;
        font-weight: bold;
        color: #888;
        font-family: arial;
        line-height: 2.625rem;
        text-align: center;
        img {
          width: 1.1875rem;
          display: block;
          margin: 0 auto;
        }
      }
      .rank-power {
        text-align: center;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
        width: 50%;
        font-family: arial;
      }
      .BCTY {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-family: arial;
        font-weight: bold;
        color: #6b94f8;
        font-size: @fs-big;
      }
    }
  }
    .dc-logo {
    position: fixed;
    right: 10px;
    bottom: 10px;
  }

  .dc-logo:hover svg {
    -webkit-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: arrow-spin 2.5s 0s cubic-bezier(0.165, 0.84, 0.44, 1)
      infinite;
    animation: arrow-spin 2.5s 0s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
  }
  .dc-logo:hover:hover:before {
    content: "\2764";
    padding: 6px;
    font: 10px/1 Monaco, sans-serif;
    font-size: 10px;
    color: #00fffe;
    text-transform: uppercase;
    position: absolute;
    left: -70px;
    top: -30px;
    white-space: nowrap;
    z-index: 20px;
    box-shadow: 0px 0px 4px #222;
    background: rgba(0, 0, 0, 0.4);
  }
  .dc-logo:hover:hover:after {
    content: "Digital Craft";
    padding: 6px;
    font: 10px/1 Monaco, sans-serif;
    font-size: 10px;
    color: #6e6f71;
    text-transform: uppercase;
    position: absolute;
    right: 0;
    top: -30px;
    white-space: nowrap;
    z-index: 20px;
    box-shadow: 0px 0px 4px #222;
    background: rgba(0, 0, 0, 0.4);
    background-image: none;
  }
  @-webkit-keyframes arrow-spin {
    50% {
      -webkit-transform: rotateY(360deg);
      transform: rotateY(360deg);
    }
  }

  @keyframes arrow-spin {
    50% {
      -webkit-transform: rotateY(360deg);
      transform: rotateY(360deg);
    }
  }
  @keyframes anima {
    0% {
      margin-top: -2px;
    }
    100% {
      margin-top: 2px;
    }
  }
  @keyframes animat {
    0% {
      margin-top: 2px;
    }
    100% {
      margin-top: -2px;
    }
  }
  @keyframes myfirst {
    0% {
      transform: scale(1);
      opacity: 1;
      filter: alpha(opacity=100);
    }

    50% {
      transform: scale(1.2);
      opacity: 1;
      filter: alpha(opacity=100);
    }
    100% {
      transform: scale(0.5);
      opacity: 0;
      filter: alpha(opacity=0);
    }
  }
  @-webkit-keyframes arrow-spin {
    0% {
      transform: scale(1);
      opacity: 1;
      filter: alpha(opacity=100);
    }

    50% {
      transform: scale(1.2);
      opacity: 1;
      filter: alpha(opacity=100);
    }
    100% {
      transform: scale(0.5);
      opacity: 0;
      filter: alpha(opacity=0);
    }
  }
}
</style>
