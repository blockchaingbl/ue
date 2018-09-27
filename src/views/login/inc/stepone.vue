<template lang="html">
 <div class="">
  <div class="l-tit">区块链身份创建/登录</div>
  <box gap="10px 10px">
    <div class="">手机归属地</div>
    <div class="">中国</div>
   </box>
  <group>
    <x-input title="+86" name="mobile" placeholder="请输入手机号码" type="number" keyboard="number" is-type="china-mobile"></x-input>
  </group>

   <box gap="10px 10px">
    <x-button type="primary">验证</x-button>
   </box>
 </div>
        
</template>
<script>

export default {
  components: {

  },
  data () {
    return {
      value:"",
      form: {
        mobile: '',
        password: ''
      },
      showMoveBox: false,
      moveDone: '',
      loadingMoveBox: false,
      canNotify: true,
      showWarn: false,
      warnText: '',
      closable: false
    }
  },
  mounted () {
  
  },
  methods: {
  
  }
}
</script>

<style lang="less">

</style>
