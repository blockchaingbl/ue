<template>
  <div class="g-inherit m-article p-search-user g-window">
    <!--<x-header class="m-tab" :left-options="{backText: ' '}">-->
    <!---->
      <!--<a slot="left"></a>-->
    <!--</x-header>-->
    <button-tab class="m-tab-top" v-model="searchType" style="margin-top: 2rem;line-height: 31px;">
      <button-tab-item class="u-tab-top">搜索用户</button-tab-item>
      <button-tab-item class="u-tab-top">搜索群</button-tab-item>
    </button-tab>
    <group class="u-search">
      <search
        class="u-ipt-default"
        type="text"
        :required="false"
        :auto-fixed="false"
        v-model="searchText"
        :placeholder="searchType===0?'请输入搜索内容':'请输入群号'"
        ref='searchInput'
        >
        <span slot="label">搜索：</span>
      </search>
    </group>
    <div style="text-align: center;margin-top: 3rem;">
      <x-button type="primary" action-type="button" @click.native="searchUser">搜索</x-button>
    </div>
    <group class="u-card u-list">
      <cell v-if='searchType === 0'
        v-for="(user, index) in searchList"
        class="u-list-item"
        :title="user.nick"
        :key="user.account"
        :userId="index"
        is-link
        :link="user.link">
        <img class="icon" slot="icon" width="20" :src="user.avatar">
      </cell>
      <cell v-else
        v-for="team in searchList"
        class="u-list-item"
        :title="team.name"
        :key="team.teamId"
        is-link
        :link="team.link">
        <img class="icon" slot="icon" width="20" :src="team.avatar">
      </cell>
    </group>
    <div class="u-card">
      <h3>{{errMsg}}</h3>
    </div>
  </div>
</template>

<script>
import config from '../../configs'

export default {
  mounted () {
    this.$nextTick(() => {
      this.searchType = parseInt(this.$route.params.searchType)
      this.$store.dispatch('resetSearchResult')
    })

    setTimeout(() => {
      // 立即focus会引起切页时白屏，故增加timeout
      this.$refs.searchInput.$refs.input.focus()
    }, 500);
  },
  data () {
    return {
      searchType: 0, // 用户 0 群 1
      searchText: '',
      searchList: [],
      errMsg: '',
      // 首次加载标志，因为mount和watch会有时序问题，首次加载不显示errMsg
      firstEntry: true,
    }
  },
  watch: {
    searchResult (val, oldVal) {
      if ((val.length === 0) && (!this.firstEntry)) {
        this.errMsg = '无记录'
      } else {
        this.errMsg = ''
      }
      this.searchList = val
    },
    searchType() {
      this.$refs.searchInput.$refs.input.focus()
    }
  },
  computed: {
    searchResult () {
      let result = []
      if (this.searchType === 1) {
        result = this.$store.state.searchedTeams.map(item => {
         item.avatar = item.avatar || config.defaultUserIcon
          item.link = `/teamcard/${item.teamId}`
          return item
        })
      } else if (this.searchType === 0) {
        result = this.$store.state.searchedUsers.map(item => {
          item.nick = item.nick || item.account
          item.link = `/namecard/${item.account}`
          item.avatar = item.avatar || config.defaultUserIcon
          return item
        })
      }
      return result
    }
  },
  methods: {
    searchUser () {
      if (!this.searchText) {
        this.$vux.toast.text('未输入内容')
        return
      }
      this.firstEntry = false
      if (this.searchType === 1) {
        if(!/^(\d){4,11}$/.test(this.searchText)){
          this.$vux.toast.text('输入的群号非法')
          return
        }
        this.$store.dispatch('searchTeam', {
          teamId: this.searchText
        })
      } else if (this.searchType === 0) {
        if (this.searchText === this.$store.state.userUID) {
          this.searchList = []
          this.errMsg = '别看了，就是你自己！'
        } else {
          this.$http.post('api/app.user/nim',{username:this.searchText}).then(res => {
                if(res.errcode==0)
                {
                  this.$store.dispatch('searchUsers', {
                    accounts: res.data.accounts
                  })
                }
          })
            .catch((err) => {

            })

        }
      }
    }
  }
}
</script>
<style lang="less" scoped>
  @import "../../assets/css/nim/theme.css";
  .g-window{
    /deep/.weui-search-bar{
      height: 3.25rem;
      .weui-search-bar__cancel-btn{
        line-height: 2.5rem;
      }
      .weui-search-bar__label{
        /deep/span{
          margin-top: 0.825rem;
        }
      }
    }
  }
</style>
