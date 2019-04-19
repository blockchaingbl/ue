<template lang="html">
    <div class="mine-center">

        <div class="mine-banner">
            <img src="@/assets/images/mine_bg_new.jpg" alt="" class="banner-img">
            <div class="banner-centent">
                <div v-for="(item,index) in coinlist"  class="profit" :class="{'animate1': animate1[index].show }" v-on:click='animate(index,item)' v-if='index<10'>
                    <div class="img-text">
                        <img src="@/assets/images/miner_icon.png" alt="" style="width:40px;height:45px;" v-if="item.mine_type==1">
                        <img src="@/assets/images/icon_gem_new.png" alt="" v-else>
                        <span v-if="item.mine_type!=1">{{item.coin_unit}}</span>
                    </div>
                    <div class="profit-text" v-if="item.mine_type==1">淘淘</div>
                    <div class="profit-text" v-else>{{item.amount}}</div>

                    <!-- <div class="img-text">ETH</div>
                    <div class="profit-text">0.00018</div> -->
                </div>
                <div class="banner-top">
                  <vueSeamless :data="system_noticearr" :class-option="optionLeft" class="seamless-warp2">
                        <ul class="item">
                            <li v-text="system_notice"></li>
                        </ul>
                    </vueSeamless>
                    <!-- <vue-marquee :content="system_notice" class="two" :speed="'middle'" :showtwo="false"></vue-marquee> -->
                </div>
                <router-link to="/user/selfmoney" class="self-assets-box">
                    <div class="self-assets flex-box">
                        <div class="assets-text">我的资产</div>
                        <!--<div class="assets-numb flex-1">{{vc_amount}}</div>-->
                    </div>
                </router-link>
                <div class="dug-type" v-bind:class="{ active: isdugcoin }">
                    <img src="@/assets/images/icon_gem_new1.png" class="scene-img" alt="">
                    <p class="dug-type-txt">GBL</p>
                    <module-svg></module-svg>
                </div>
                <div class="dug-text" v-bind:class="{ active: isdugcoin }">
                    资产开发中...
                </div>
                <audio  id="audio">
                  <source src="@/assets/images/9897.mp3" >
                </audio>

                <div class="banner-bottom flex-box">
                    <div class="count-power">
                        <div class="power flex-box">
                            <div class="power-text">算力</div>
                            <div class="power-numb flex-1">{{cp_total}}</div>
                        </div>
                        <div class="cont-text">算力越高，开发越快</div>
                    </div>
                    <router-link to="/session" class="msg-session">
                        <div class="exalt-img2" style="position: relative;">
                            <img src="@/assets/images/msg-session.png" alt="">
                            <badge v-if="unread>0" style="position: absolute;right: -0.25rem;top: 2rem;"></badge>
                        </div>
                        <div class="exalt-text">消息</div>
                    </router-link>
                    <div class="candy_router" @click="go_dev">
                        <div class="exalt-img3" style="position: relative;">
                            <img src="@/assets/images/app_dev.png" alt="">
                        </div>
                        <div class="exalt-text">技术支持</div>
                    </div>
                    <router-link to="/task/center" class="exalt">
                        <div class="exalt-img">
                            <img src="@/assets/images/icon_exalt_new.png" alt="">
                        </div>
                        <div class="exalt-text">提升算力</div>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="rank-box">
            <div class="rank-top flex-box">
                <div class="rank-top-title flex-1">排行榜</div>
            </div>
            <div class="rank-head flex-box">
                <div class="rank-head-item ranking">名次</div>
                <div class="rank-head-item flex-1">账户</div>
                <div class="rank-head-item">算力值</div>
            </div>
            <div class="rank-block">
                <div class="rank-item flex-box" v-for="(item,index) in list" v-if='index<10'>
                    <div class="ranking rank-numb">
                        <img src="@/assets/images/icon_rank1.png" alt="" v-if='index==0'>
                        <img src="@/assets/images/icon_rank2.png" alt="" v-if='index==1'>
                        <img src="@/assets/images/icon_rank3.png" alt="" v-if='index==2'>
                        <span v-if='index>2'>{{index+1}}</span>
                    </div>
                    <div class="rank-power flex-1">{{item.username}}</div>
                    <div class="item-item BCTY">{{item.cp_total}}</div>
                </div>
                <load-more :show-loading="false" tip="我是底线" background-color="#fbf9fe"></load-more>
            </div>
            <nodata  v-if="list.length<1" :datatip="'暂无数据'"></nodata>
        </div>
    </div>
</template>
<script>
import { LoadMore , Badge } from "vux";
import SVG from "@/views/mine/inc/svg";
import Nodata from "@/components/nodata";
import VueMarquee from "@/components/marquee";
import vueSeamless from 'vue-seamless-scroll';
import cookie from '../../utils/cookie'
export default {
  name: "mineCenter",
  components: {
    LoadMore,
    "module-svg": SVG,
    "nodata" : Nodata,
    "vue-marquee": VueMarquee,
    vueSeamless,
    Badge
  },
  data() {
    return {
      list: [],
      coinlist: [],
      coinlength: null,
      _index: "",
      vc_amount: "",
      cp_total: "",
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
      system_notice: "",
      top_left: "",
      _width: "",
      setInterval: "",
      system_noticearr:[this.system_notice,this.system_notice],
      auth:0,
    };
  },
  computed: {
      optionLeft () {
          return {
                  direction: 2,
                  limitMoveNum: 2,
                  step:0.5,
                  switchOffset:0,
              }
      },
        unread()
        {
           let count = 0 ;
           this.$store.state.sessionlist.map(function (val) {
             count +=val.unread;
           })

          count+=this.$store.state.customSysMsgUnread;
          return count
        }
   },
  mounted() {
    this.getRank();
    this.getUserInfo();
    this.getCoin();
    this.system_notice = this.$store.state.init.system_notice;
    const len = this.system_notice.length;
    let width =  len*13;

    $('.seamless-warp2').find('.item').css('width',width+'px')
    this.invite_cp = this.$store.state.init.invite_cp;

  },
  methods: {
    go_dev(){
      this.$router.push({path:'/devapp'})
    },
    getCoin() {
      // console.log("res.data");
      this.$http
        .post("/api/app.mine/coin/compute", {})
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
          if(isNaN(res.data.mine_coin.length)){
            var _length = 0;
            for(var k in res.data.mine_coin)
            {
              _length++;
            }
            this.coinlength = _length;
            this.isdugcoin = false;
          }
          else if (res.data.mine_coin.length < 1) {
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
    getRank() {
      this.$http
        .post("/api/app.mine/coin/rank", {
          page: 1
        })
        .then(res => {
          //  console.log(res.data);
          this.list = res.data.rank;
        })
        .catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
          console.log(err);
          //  this.Toast(err || '网络异常，请求失败');
        });
    },
    getUserInfo() {
      let _this = this;
      setTimeout(function() {
        _this.$http
          .post("/api/app.user/account/info", {})
          .then(res => {
            // console.log(res.data);
            _this.vc_amount = res.data.account_info.vc_amount;
            _this.cp_total = res.data.account_info.cp_total;
            _this.auth = res.data.account_info.chat_auth;
            cookie.setCookie('uid', res.data.account_info.accid)
            cookie.setCookie('sdktoken',res.data.account_info.token)
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
    animate(numb, item) {
      console.log(numb);
      var audio = document.querySelector("#audio");
      audio.currentTime = 0;
      audio.play();
      this.animate1[numb].show = true;
      this.coinlength--;

      console.log("animate:" + this.coinlength);
      this.$http
        .post("/api/app.mine/coin/fetch", {
            id: item.id,type:item.mine_type
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
          _this.getCoin();
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
.mine-center {
    .seamless-warp2 {
        overflow: hidden;
        height: 25px;
        width: 100%;
        ul.item {
            width:1500px;
            li {
                float: left;
 				margin-right: 10px;
                height: 1.75rem;
                line-height: 1.75rem;
            }
        }
    }
    & .mine-banner {
        position: relative;
        height: 130.67vw;
        width: 100%;
        overflow: hidden;
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
                z-index: 99;
                p {
                    line-height: 1.75rem;
                    position: absolute;
                    top: 0;
                    white-space: nowrap;
                }
            }
            .self-assets-box{
                position: absolute;
                top: 2.5rem;
                left: 0.625rem;
            }
            & .self-assets {

                background: -webkit-linear-gradient(left, #2881d3, #1d62c1);
                background: linear-gradient(to right, #2881d3, #1d62c1);
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
                top: 18%;
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
                .dug-type-txt {
                    position: absolute;
                    -webkit-transform: translate(-50%, -50%);
                    transform: translate(-50%, -50%);
                    top: 50%;
                    left: 50%;
                    font-weight: bold;
                    z-index: 10;
                    font-size: 0.75rem;
                }
            }
            // .dug-type.active{
            //     transform: translateX(-50%) scale(1.2,1.2);
            // }
            .dug-text{
                position: absolute;
                transform: translateX(-50%);
                top: 0;
                left: 50%;
                color: #fff;
                font-size: @fs-middle;
                transition: all 0.3s ease;
                font-size: 1rem;
                line-height: 2rem;
                font-weight: bold;
                opacity: 0;
            }
            .dug-text.active{
                top: 2.5rem;
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
                background: -webkit-linear-gradient(left, #2881d3, #1d62c1);
                background: -o-linear-gradient(left, #2881d3, #1d62c1);
                background: linear-gradient(to right, #2881d3, #1d62c1);
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
                .cont-text{
                    font-size: 0.625rem;
                    text-align: center
                }
                .exalt-text {
                    font-size: @fs-middle;
                    text-align: center;
                }
                .exalt-img {
                    width: 2.8125rem;
                    height: 2.8125rem;
                    margin: 0 auto 0.25rem;
                }
                .exalt-img2 {
                    height: 3.0625rem;
                    width: 35px;
                    padding-top: 1rem;
                    font-size: 0.875rem;
                }
                .exalt-img3 {
                    height: 3.0625rem;
                    width: 42px;
                    padding-top: 0.9rem;
                    font-size: 0.875rem;
                    margin-left: 0.5rem;
                    bottom:1px;
                }
                .msg-session {
                    margin-left: 1rem;
                    pointer-events: auto;
                    display: block;
                    color: #fff;
                }
                .cont-text{
                    font-size: 0.625rem;
                    text-align: center
                }
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
                will-change: transform;
                position: absolute;
                text-align: center;
                color: #fff;
                font-size: @fs-small;
                min-width: 2.75rem;
                opacity: 1;
                z-index: 2;
                filter: alpha(opacity=100);
                .img-text {
                    text-align: center;
                    width: 100%;
                    color: #2c3038;
                    font-weight: bold;
                    font-family: arial;
                    font-size: 0.75rem;
                    height: 2.75rem;
                    line-height: 2.65rem;
                    position: relative;
                    span{
                        position: relative;
                        z-index: 2;
                    }
                    img{
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%,-50%);
                        width: 2.75rem;
                        height: 2.75rem;
                        z-index: 1;
                    }
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
                    left: 5%;
                    top: 40%;
                    animation-duration: 0.5s;
                }
                &:nth-child(2) {
                    left: 76%;
                    top: 46%;
                    animation-duration: 0.7s;
                }
                &:nth-child(3) {
                    left: 78%;
                    top: 8%;
                    animation-duration: 0.9s;
                }
                &:nth-child(4) {
                    left: 50%;
                    top: 50%;
                    animation-duration: 1s;
                }
                &:nth-child(5) {
                    left: 74%;
                    top: 26%;
                    animation-duration: 0.6s;
                }
                &:nth-child(6) {
                    left: 28%;
                    top: 48%;
                    animation-duration: 0.8s;
                }
                &:nth-child(7) {
                    top: 60%;
                    left: 8%;
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
                    left: 30%;
                    top: 65%;
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
            #audio_msg {
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
                    height: 1.5rem;
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
    .icon-hy:after{
        content:"\e612";
    }
    .candy_router{
        pointer-events: auto;
        display: block;
        color: #fff;
    }
}
</style>
