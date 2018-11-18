<template>
  <div class='g-inherit m-article g-window'>
    <x-header class="m-tab" :left-options="{backText: ' '}">
      <h1 class="m-tab-top">加入群</h1>
      <a slot="left"></a>
    </x-header>
    <div class='g-body'>
      <img class="icon u-circle" slot="icon" width="50" height="50" :src="teamInfo && teamInfo.avatar">
      <div>{{teamInfo && teamInfo.name}}</div>
      <div>{{teamDesc}}</div>
      <div class='u-bottom'>
         <x-button type="primary" action-type="button" @click.native="applyClick">申请加入</x-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    teamId () {
      return this.$route.params.teamId
    },
    teamInfo () {
      return this.$store.state.searchedTeams.find(team => {
        return team.teamId === this.teamId
      })
    },
    teamDesc () {
      if (!this.teamInfo) {
        return ''
      }
      let teamType = this.teamInfo.type === "advanced" ? "高级群" : "普通群"
      return `${teamType}:${this.teamInfo.memberNum}人`
    }
  },
  methods: {
    applyClick () {
      var team = this.$store.state.teamlist.find(team => {
        return team.teamId === this.teamId
      })
      if (team && team.validToCurrentUser) {
        // 查询到该群且该群对自己有效，说明已在群中
        this.$vux.toast.text('已在群中')
        return
      }
      switch (this.teamInfo.joinMode) {
        case 'rejectAll':
          this.$vux.toast.text('该群禁止任何人加入')
          break
        case 'noVerify':
          this.applyTeam()
          break
        case 'needVerify':
          this.showConfirm()
          break
      }
    },
    showConfirm () {
      this.$vux.confirm.prompt('限十字以内', {
        title: '请输入验证信息',
        closeOnConfirm: false,
        inputAttrs: {
          maxlength:'10'
        },
        onConfirm: (msg) => {
          if(msg) {
            this.applyTeam(msg)
            this.$vux.confirm.hide()
          }else {
            this.$vux.toast.text('请输入验证信息')
          }
        }
      })
    },
    applyTeam (msg) {
      this.$store.dispatch('delegateTeamFunction', {
        functionName: 'applyTeam',
        options: {
          teamId: this.teamId,
          ps: msg || '',
          done: (error, obj) => {
            if(error){
              this.$vux.toast.text(error)
              return
            }
            this.$vux.toast.text(msg ? '申请成功 等待验证' : '已加入群')
            history.go(-2)
          }
        }
      })
    }

  }
}
</script>

<style scoped lang="less">
  @import "../../assets/css/nim/theme.css";

  .g-body {
    margin-top: 5rem;
    text-align: center;

    div {
      margin: 1rem auto;
    }
  }
</style>
