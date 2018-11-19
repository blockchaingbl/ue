<template>
  <div class="g-inherit m-main p-contacts g-window " >
    <tab style="height: 46px;font-size: 14px">
      <tab-item  @on-item-click="$router.push({path:'session'})">会话</tab-item>
      <tab-item selected>通讯录</tab-item>
      <tab-item @on-item-click="$router.push({path:'sysmsgs'})">节点通知<badge v-show="customSysMsgUnread>0"></badge></tab-item>
    </tab>
    <div class="m-cards u-search-box-wrap" >
      <span class="u-search-box" v-if="ban==0">
        <a href="#/searchUser/0">
          添加好友\群
        </a>
      </span>
      <span class="u-search-box" v-else>
        <a href="#" @click.stop="limit()">
          添加好友\群
        </a>
      </span>
      <span class="u-search-box" v-if="level>0 && ban==0">
        <a href='#/teaminvite/0'>
        创建组\群
        </a>
      </span>
    </div>
    <div id="userList" class="m-list" style="margin-top: 2rem;">
      <group class="u-card" title="群发功能"  v-if="society_send>0 || sys_send>0 ">
        <cell title="社群通知" :link="{path:'/society_send'}" v-if="society_send>0">
          <img slot="icon"  class="iconfont icon_wallet" src="@/assets/images/tongzhi.png" alt=""  width="20"  style="display:block;margin-right: 0.6rem">
        </cell>
        <cell title="节点通知" :link="{path:'/sys_send'}" v-if="sys_send>0">
          <img slot="icon"  class="iconfont icon_wallet" src="@/assets/images/syssend.png" alt=""  width="20"  style="display:block;margin-right: 0.6rem">
        </cell>
      </group>
      <group class="u-card" title="群">
        <cell title="高级群" is-link link='/teamlist/advanced'>
          <span class="icon icon-team-advanced" slot="icon"></span>
        </cell>
        <cell title="讨论组" is-link link='/teamlist/normal'>
          <span class="icon icon-team" slot="icon"></span>
        </cell>

      </group>
      <group class="u-card" title="好友列表">
        <cell v-for="friend in friendslist" :title="friend.alias" :key="friend.account" is-link :link="friend.link">
          <img class="icon" slot="icon" width="20" :src="userInfos[friend.account].avatar">
        </cell>
      </group>
      <group class="u-card" title="黑名单">
        <cell v-for="friend in blacklist" :title="friend.alias" :key="friend.account" is-link :link="friend.link">
          <img class="icon u-circle" slot="icon" width="20" :src="userInfos[friend.account].avatar">
        </cell>
      </group>
      <loading v-show="loading"></loading>
    </div>
  </div>
</template>

<script>
import { Badge } from "vux";
import Loading from '../components/LoadingNew'
export default {
    data(){
        return {
            society_send:0,
            sys_send:0,
            level:0,
            loading:true,
            ban:0,
            loading:true,
            loadings:true,
        }
    },
    mounted(){
        this.getUserinfo()
    },
    components:{
      Badge:Badge,
      Loading:Loading
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/auth',{}).then(res => {
               this.society_send = res.data.society_send
               this.sys_send = res.data.sys_send
               this.level = res.data.level
               this.ban = res.data.ban
               this.loading = false;
                this.loadings = false;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
              this.$vux.loading.hide()
              this.loading = false;
              this.loadings = false;
            });
        },
        limit(){
            this.$vux.toast.text('功能受限');
        }
    },
  computed: {
    customSysMsgUnread(){
      let count = 0;

      return  count+this.$store.state.customSysMsgUnread;
    },
    friendslist () {
      return this.$store.state.friendslist.filter(item => {
        let account = item.account
        let thisAttrs = this.userInfos[account]
        let alias = thisAttrs.alias ? thisAttrs.alias.trim() : ''
        item.alias = alias || thisAttrs.nick || account
        item.link = `/namecard/${item.account}`
        if ((!thisAttrs.isFriend) || (thisAttrs.isBlack)) {
          return false
        }
        return true
      })
    },
    blacklist () {
      return this.$store.state.blacklist.filter(item => {
        let account = item.account
        let thisAttrs = this.userInfos[account]
        let alias = thisAttrs.alias ? thisAttrs.alias.trim() : ''
        item.alias = alias || thisAttrs.nick || account
        item.link = `/namecard/${item.account}`
        if (!thisAttrs.isFriend) {
          return false
        }
        return true
      })
    },
    robotslist () {
      return this.$store.state.robotslist.map(item => {
        item.link = `/namecard/${item.account}`
        return item
      })
    },
    userInfos () {
      return this.$store.state.userInfos
    }
  }
}
</script>

<style  lang="less" scoped>
  @import "../../assets/css/nim/theme.css";
  .p-contacts {
    .vux-tab{
      /deep/.vux-badge-dot{
        padding: 5px!important;
        height: auto;
      }
    }
    .add-friend {
      background-color: #fff;
    }
    .m-list {
      padding-top: 5rem;
    }
    .u-search-box-wrap {
      text-align: center;
    }
    .u-search-box {
      position: relative;
      display: inline-block;
      box-sizing: border-box;
      min-width: 45%;
      padding: 1em;
      height: 3rem;
      text-align: center;
      border: 1px solid #ccc;
      background-color: #fff;
      font-size: 0.8rem;
      box-shadow: 2px 2px 6px #ccc;
      a {
        display: inline-block;
        box-sizing: border-box;
        height: 100%;
        width: 100%;
      }
    }
    .u-card {
      .icon {
        display: inline-block;
        margin-right: 0.4rem;
        width: 1.4rem;
        height: 1.4rem;
        background-size: 20rem;
      }
      .icon-team-advanced {
        background-position: 0 -3rem;
        background-image: url(http://yx-web.nos.netease.com/webdoc/h5/im/icons.png);
      }
      .icon-team {
        background-position: -2.1rem -3rem;
        background-image: url(http://yx-web.nos.netease.com/webdoc/h5/im/icons.png);
      }

      /deep/.vux-tab-wrap{
        height: 44px;
      }
    }
  }
</style>

