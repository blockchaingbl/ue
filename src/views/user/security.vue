<template lang="html">
<div v-show="form.mobile!=null" class="security">
  <group>
    <x-input ref="verifycode"  class="weui-vcode"  type="number" keyboard="number"  placeholder="请输入验证码"  v-model="form.verifycode" :min="6" :required="true">
        <x-button v-if="!ifgetcode"   slot="right" plain  type="primary" mini v-on:click.native="getCode()">发送验证码</x-button>
        <x-button v-if="ifgetcode" slot="right" plain  type="primary" mini v-on:click.native="endtimer()">重新发送{{countimer}}s'</x-button>
    </x-input>
  </group>
  <group>
    <x-input type="password" title="新的密码" name="password" placeholder="请输入密码" v-model="form.password" :min="6" :max="16" ref="password" :required="true"></x-input>
    <x-input type="password" title="确认密码" name="check_password" placeholder="请输入密码" v-model="form.check_password" :min="6" :max="16" :equal-with="form.password" ref="check_password" :required="true"></x-input>
  </group>

    <box gap="20px 20px">
        <x-button type="primary" @click.native="resetPwd">确认设置</x-button>
    </box>
</div>
</template>
<script>
export default {
    components: {
    },
    data () {
        return {
            form : {
              verifycode : '',
              mobile: null,
              password: '',
              check_password: ''
            },
            ifgetcode :　false,
            countimer : '',
        }
    },
    mounted () {
       this.getUserinfo();
    },
    methods:{
      getUserinfo(){
          this.$http.post('/api/app.user/account/info',{}).then(res => {
              this.form.mobile = res.data.account_info.mobile;
              this.mobile_code = res.data.account_info.mobile_code;
          }).catch(err => {
              if (err.errcode) {
                  this.$vux.toast.text(err.message);
              }
              console.log(err);
              //  this.Toast(err || '网络异常，请求失败');
          });
      },
      getCode(){
          if (this.form.mobile!="") {
              this.$http.post('/api/app.util/sms/send',{
                  mobile:this.form.mobile,
                  type:0,
                  mobile_code:this.mobile_code,
              }).then(res => {
                  this.$vux.toast.text("发送成功");
                  this.$vux.toast.text(res.message);
                  console.log(res);
                  this.counttime(60);
              }).catch(err => {
                  this.$vux.toast.text(err.message);
                  console.log(err);
                  if (err.errcode==10000) {
                      this.ifgetcode=true;
                      this.countimer=err.data.second;
                      this.counttime(err.data.second);
                  }
                  //  this.Toast(err || '网络异常，请求失败');
              });
          }else{
              this.$vux.toast.text("请输入正确的手机号");
          }
      },
      resetPwd(){
          this.$refs.verifycode.validate();
          this.$refs.password.validate();
          this.$refs.check_password.validate();
          if(this.$refs.verifycode.valid&&this.$refs.password.valid&&this.$refs.check_password.valid)
          {
              this.$http.post('/api/app.user/account/security',this.form).then(res => {
                  console.log(res.errcode);
                  if (res.errcode=="0") {
                      this.$vux.toast.text("重置成功");
                      this.$router.go(-1);
                  }
              }).catch(err => {
                  this.$vux.toast.text(err.message);
                  console.log(err);
                  //  this.Toast(err || '网络异常，请求失败');
              });
          }else{

              this.$refs.verifycode.forceShowError = true;
              this.$refs.password.forceShowError = true;
              this.$refs.check_password.forceShowError = true;
              if(!this.form.check_password)
              {
                  this.$refs.check_password.errors.equal = '输入不一致';
                  this.$refs.check_password.getError();
              }
          }
      },
      backlogin(){
          this.$router.push({path:'/login'});
      },
      counttime(TIME_COUNT){
          if (!this.wait) {
              this.countimer = TIME_COUNT;
              this.ifgetcode = true;
              this.wait =setInterval(() =>{
              if (this.countimer > 0 && this.countimer <= TIME_COUNT) {
                  this.countimer--;
              } else {
                  this.ifgetcode = false;
                  clearInterval(this.wait);
                  this.wait = null;
              }
          }, 1000);
          }
      },
      endtimer(){
          this.$vux.toast.text("请于倒计时结束后再获取验证码");
      }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '../../assets/css/variable.less';
    .security{
        .weui-cell__ft button.weui-btn{
            height: 2rem;
        }
    }
</style>
