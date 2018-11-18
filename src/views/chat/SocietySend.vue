<template>
  <div class='g-inherit m-article p-setting g-window'>
    <x-header class="m-tab" :left-options="{backText: ' '}">
      <h1 class="m-tab-top">社群通知</h1>
      <a slot="left"></a>
      <a slot="right" @click='() => update()'>发送</a>
    </x-header>
    <group style="margin-top: -2.5rem;">
      <x-textarea autofocus  :placeholder="placeHolder" v-model="inputModel" ref='input'  :max='200'></x-textarea>
    </group>
  </div>
</template>

<script>
  import Utils from '../../utils'

  export default {
    data(){
      return {
        inputModel:'',
        placeHolder: ''
      }
    },
    mounted() {
      setTimeout(() => {
        this.$refs.input && this.$refs.input.focus()
      }, 500);
    },
    methods:{
      update(value){
        if (value===undefined && this.inputModel.length < 1) {
          this.$vux.toast.text('请输入内容后提交')
          return
        }
        this.$vux.loading.show({
          text: '信息发送中'
        })
        this.$http.post('/api/app.user/nim/send_to_society',{text:this.inputModel})
          .then(res => {
            this.$vux.loading.hide()
            this.$vux.toast.text(res.message)
            if(res.errcode==0)
            {
              this.inputModel = '';
              this.$router.push({path:'/session'})
            }
          })
          .catch(err=>{
            this.$vux.loading.hide()
            this.$vux.toast.text(err.message)
          })
      }
    }
  }
</script>

<style lang="less" scoped>
  @import "../../assets/css/nim/theme.css";

  .p-setting{
    background-color: #e6ebf0;
    padding-top: 4.6rem;
  }
  .weui-cell{
    background-color: white;
  }
  .select {
    img{
      position: absolute;
      right: 0;
    }
  }
  .icon-selected{
    display: inline-block;
    width: 1.4rem;
    height: 1.4rem;
    background-size: 20rem;
    background-image: url(http://yx-web.nos.netease.com/webdoc/h5/im/icons.png);
    background-position: -3.7rem -2.95rem;
  }
</style>


