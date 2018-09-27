<template lang="html">
<!--  v-show="this.$route.name!='mineCenter'" -->
<div id="app">
<x-header :on-click-back="back" :left-options="{showBack: this.$route.name!='loginIndex' && this.$route.name!='pwdReset' && this.$route.name!='login_auth' 
&& this.$route.name!='login_auth',backText:''}" 
v-if="this.$route.meta.pageType!='wallet_chain' && this.$route.name!='mineCenter'&& this.$route.name !='taskCenter' 
&& this.$route.name !='relationsFriends' && this.$route.name != 'dealsCenter' &&this.$route.name !='userCenter' 
&& this.$route.name !='index' &&this.$route.name !='wallet_international' && this.$route.name !='wallet_international_detail' 
&& this.$route.name !='wallet_international_recieve' && this.$route.name !='wallet_international_send' && this.$route.name !='wallet_international_create' 
&& this.$route.name !='wallet_international_import' && this.$route.name !='wallet_international_manage' 
&& this.$route.name !='wallet_international_manage_item' 
&& this.$route.name !='candy_success' 
&& this.$route.name !='lock_transfer_success'
&& this.$route.name !='redPacketIndex'
&& this.$route.name !='packetList'
&& this.$route.name !='packetDetail'
&& this.$route.name !='receiveDetail'
&& this.$route.name !='sendDetail'
&& this.$route.name !='selectTokens'
&& this.$route.name !='mineMachine'
&& this.$route.name !='selfMachine'
&& this.$route.name !='machine_self_detail'
&& this.$route.name !='transferSuccess'
"  @click="onClickBack" :class="{'market-header':(this.$route.name =='marketIndex'|| this.$route.name =='marketDetail')}">{{this.$route.meta.title}}

    <router-link  slot="right" to="/user/center" class="exalt" v-if="this.$route.name!='loginIndex' && this.$route.name!='pwdReset' && this.$route.name!='login_auth' && this.$route.name!='userSetting'"> 
    <i class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;">&#xe6f8;</i>
    </router-link>
</x-header>
<tabbar class="app_nav" v-if="this.$route.name=='mineCenter'||this.$route.name=='taskCenter'||this.$route.name=='dealsCenter'||this.$route.name=='relationsFriends'||this.$route.name=='userCenter' ||this.$route.name=='candyIndex'" v-model="index">
    <tabbar-item link="/mine/center">
        <i slot="icon" class="iconfont">&#xe6f7;</i>
        <span slot="label">挖矿</span>
    </tabbar-item>
    <tabbar-item link="/task/center">
        <i slot="icon" class="iconfont">&#xe6fc;</i>
        <span slot="label">任务</span>
    </tabbar-item>
    <tabbar-item link="/deals/center">
        <i slot="icon" class="iconfont">&#xe6fe;</i>
        <span slot="label">流通</span>
    </tabbar-item>
    <tabbar-item link="/candy">
        <i slot="icon" class="iconfont icon-hy"></i>
        <span slot="label">糖果</span>
    </tabbar-item>
    <tabbar-item  link="/user/center">
        <i slot="icon" class="iconfont">&#xe6f8;</i>
        <span slot="label">我</span>
    </tabbar-item>
</tabbar>
<div class="app-footer" v-if="this.$route.name =='dealsSell' || this.$route.name =='userWithdrawlist' || this.$route.name =='userSelfmoney'">
    <module-record v-if="this.$route.name =='dealsSell'"></module-record>
    <module-withdraw v-if="this.$route.name =='userWithdrawlist'"></module-withdraw>
    <box gap="10px 0" v-if="$store.state.init.market && this.$route.name =='userSelfmoney'">
        <x-button type="warn" link="/market/index">查看行情</x-button>
    </box>
</div>
    <walletbar  v-if="this.$route.name =='wallet_detail'"></walletbar>
<div class="app-footer-inter" v-if="this.$route.name =='wallet_international_detail'">
    <walletbarinternational v-if="this.$route.name =='wallet_international_detail'"></walletbarinternational>
</div>
<div class="app-footer-candy" v-if="this.$route.name =='lockTransferDistribute'">
    <a href="#/lock_transfer/grant/0" class="candy_dist-btn">立即转出</a>
</div>
<div class="app-footer-candy" v-if="this.$route.name =='candyDistribute'">
    <a href="#/candy/grant" class="candy_dist-btn">发糖果</a>
</div>
  <div class="app-body">
<!--     <keep-alive :include="['mineCenter', 'taskCenter', 'dealsCenter', 'userCenter']">
    </keep-alive> -->
      <router-view ></router-view>
  </div>

</div>
</template>


<script>
// import "@/assets/libs/vue/vue2-animate.min.css";
import { mapGetters ,mapActions} from 'vuex';
import {setCookie ,getCookie} from "@/assets/js/cookieHandle"
import Record from "@/components/m_record";
import Withdrawlist from "@/components/m_withdrawlist";
import WalletBar from "@/components/walletbar";
import WalletBarInternational from "@/components/walletbar_international";
export default {
  name: "app",
   data() {
    return {
      scroll: null, // 外部容器dom
      container:global,
      index:0,
      init:{},
      showMore:true,
      showMenus: false,
      isSign:getCookie("isSign"),//0:可以签到，1：已经签到过，2签到失败,
    }
  },
    created () {
  },
  mounted(){
      this.setToken(JSON.parse(localStorage.getItem('token')));
      this.getInit()
  },
  methods:{
     ...mapActions([
      'setToken',
      'setInit',
      'setSign'
    ]),
     back(){
        console.log("back");
     },
      getInit(){
          this.$http.post('/api/app.util/init',{}).then(res => {
              this.setInit(res.data);
             // console.log(res.data);
          }).catch(err => {
              // this.$vux.toast.text(err.message);
              console.log(err);
              //  this.Toast(err || '网络异常，请求失败');
          });
      },
      sign(){
          // setCookie("isSign",false);
          this.$http.post('/api/app.user/account/sign',{}).then(res => {
            setCookie("isSign",1,0.1);
            this.$vux.toast.text("今日登录，算力+"+res.data.cp_amount);
          }).catch(err => {
            if (err.errcode==50011){
              //如果已经登录
              setCookie("isSign",1,0.1);
            }
          });

      },
      onClickBack(){
          this.$router ? this.$router.back() : window.history.back()
      }
   },
  components: {
    "module-record":Record,
    "module-withdraw":Withdrawlist,
    walletbar: WalletBar,
    walletbarinternational: WalletBarInternational,
  },
  created(){
    //console.log(this.$route);
  },
  watch: {
    $route() {
     const route_name = ['mineCenter','taskCenter','dealsCenter','candyIndex','userCenter'];
        this.index = route_name.indexOf(this.$route.name)

     if (this.$route.name!="index" && this.$route.name != "loginIndex" && this.$route.name != "pwdReset" && this.$route.name != "login_auth" && this.isSign!=1) {
        this.sign();
     }
     if (this.$route.name==null){
        this.$router.replace({
            path: "/error/404"
          });
     }
     // console.log(this.$route);
    }
  }
};

</script>

<style lang="less">
@import './assets/css/reset.css';
@import './assets/css/elReset.less';
@import './assets/css/vuxReset.less';
.flex-box {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
}

.flex-1 {
    -webkit-flex: 1;
    -webkit-box-flex: 1;
    flex: 1;
}
.flex-2 {
    -webkit-box-flex: 2;
    -ms-flex: 2;
    flex: 2;
}
.flex-3 {
    -webkit-box-flex: 3;
    -ms-flex: 3;
    flex: 3;
}
.flex-4 {
    -webkit-box-flex: 4;
    -ms-flex: 4;
    flex: 4;
}
*{
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
body{
    background: white;
}
#app {
  background-color: #f4f4f4;
  font-family:Arial,"Microsoft YaHei",Helvetica,sans-serif,"SimSun";
  position: absolute;
  box-sizing: border-box;
  overflow: hidden;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
}

::-webkit-scrollbar {
	width: none;
	background-color: none;
}
::-webkit-scrollbar-track {
	// -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.15);
	background-color: none; 
}
::-webkit-scrollbar-thumb {
	width: none;
	border-radius: none;
	// -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.05);
	background-color: none;
}
#app > .weui-tabbar{
    position: absolute;
    right: 0;
    left: 0;
    z-index: 10;
    height: 3.5rem;
    background: #f7f7f8;
    
}
#app > .weui-tabbar:before{
    border-color: #eaeaea;
}
#app > .app-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0 0.625rem;
  height: 3.75rem;
}
#app > .app-footer-inter{
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0 0.625rem;
    height: 3rem;
}
#app > .app-body{
    position: absolute !important;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    overflow-x:hidden;
    overflow-y: auto;
    background: #f4f4f4;
    -webkit-overflow-scrolling: touch;
    &::-webkit-scrollbar {display:none}
}
#app > .vux-header ~ .app-body{
  top: 46px;
}
#app > .weui-tabbar ~ .app-body{
  bottom:3.5rem;
}
#app > .app-footer ~ .app-body{
  bottom: 3.75rem;
}
#app > .app-footer-inter ~ .app-body{
  bottom: 3.75rem;
}


.user-row{
    width: 3rem;
    height: 3rem;
    box-sizing: border-box;
    padding: 4px;
    margin:.4rem 0rem .4rem 1.8rem;
    background: #fff;
    border-radius: 1.5rem;
    box-shadow: 0 2px 15px 0 rgba(0, 0, 0, 0.2);
    img{
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: block;
    }
}
.user-row-username{font-size:1rem; color: #808080; }
.friend_ico{ font-size:14px; color:#f30;}
#app > .vux-header .vux-header-right a{
    color: #fff;
}
#app > .app-footer-candy{
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0 0.625rem;
    height: 2.875rem;
}
#app > .app-footer-candy ~ .app-body{
    bottom: 2.875rem;
}
.candy_dist-btn{
    margin: 0 -0.625rem;
    display: block;
    height: 100%;
    line-height: 2.875rem;
    text-align: center;
    background: #2f82ff;
    color: #fff;
    font-size: 0.9375rem;
}
.icon-hy:after{
    content:"\e612";
}

</style>
