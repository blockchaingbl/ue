webpackJsonp([58],{dGl8:function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=o("mAm1"),r=o("NyFS"),s=o("oa6C"),a=o("SPhl"),n=o("sNOJ"),c=o("Kw0a"),l=o("AL98"),d=(o("9rMa"),o("xSLg"),i.a,r.a,s.a,l.a,a.a,n.a,c.a,{components:{Flexbox:i.a,FlexboxItem:r.a,Divider:s.a,Drawer:l.a,Tab:a.a,TabItem:n.a,PopupPicker:c.a},data:function(){return{countimer:"",ifgetcode:!1,wait:null,form:{mobile:"",verifycode:"",password:"",check_password:""},mobile_code:["+86"],code_list:[]}},mounted:function(){var e=this;this.$http.post("api/app.util/init/get_mobile_code",{}).then(function(t){e.code_list=t.data.code_list})},methods:{getCode:function(){var e=this;this.$refs.mobile.validate(),this.$refs.mobile.valid&&""!=this.form.mobile?this.$http.post("/api/app.util/sms/send",{mobile:this.form.mobile,type:0,mobile_code:parseInt(this.mobile_code)}).then(function(t){e.$vux.toast.text("发送成功"),e.$vux.toast.text(t.message),e.counttime(60)}).catch(function(t){e.$vux.toast.text(t.message),1e4==t.errcode&&(e.ifgetcode=!0,e.countimer=t.data.second,e.counttime(t.data.second))}):(this.$refs.mobile.forceShowError=!0,this.$vux.toast.text("请输入正确的手机号"))},resetPwd:function(){var e=this;this.$refs.mobile.validate(),this.$refs.verifycode.validate(),this.$refs.password.validate(),this.$refs.check_password.validate(),this.$refs.mobile.valid&&this.$refs.verifycode.valid&&this.$refs.password.valid&&this.$refs.check_password.valid?this.$http.post("/api/app.user/user/resetpwd",this.form).then(function(t){"0"==t.errcode&&(e.$vux.toast.text("重置成功"),e.$router.go(-1))}).catch(function(t){e.$vux.toast.text(t.message)}):(this.$refs.mobile.forceShowError=!0,this.$refs.verifycode.forceShowError=!0,this.$refs.password.forceShowError=!0,this.$refs.check_password.forceShowError=!0,this.form.check_password||(this.$refs.check_password.errors.equal="输入不一致",this.$refs.check_password.getError()))},backlogin:function(){this.$router.push({path:"/login"})},counttime:function(e){var t=this;this.wait||(this.countimer=e,this.ifgetcode=!0,this.wait=setInterval(function(){t.countimer>0&&t.countimer<=e?t.countimer--:(t.ifgetcode=!1,clearInterval(t.wait),t.wait=null)},1e3))},endtimer:function(){this.$vux.toast.text("请于倒计时结束后再获取验证码")}}}),u={render:function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"page-login"},[o("group",[o("div",{staticClass:"login-mobile flex-box"},[o("popup-picker",{ref:"picker",attrs:{title:"",data:e.code_list,columns:3},model:{value:e.mobile_code,callback:function(t){e.mobile_code=t},expression:"mobile_code"}}),e._v(" "),o("x-input",{ref:"mobile",staticClass:"mobile-input flex-1",attrs:{title:"",name:"mobile",placeholder:"请输入手机号码",type:"number",keyboard:"number",required:!0},model:{value:e.form.mobile,callback:function(t){e.$set(e.form,"mobile",t)},expression:"form.mobile"}})],1),e._v(" "),o("x-input",{ref:"verifycode",staticClass:"weui-vcode",attrs:{placeholder:"请输入验证码",type:"number",keyboard:"number",min:6,required:!0},model:{value:e.form.verifycode,callback:function(t){e.$set(e.form,"verifycode",t)},expression:"form.verifycode"}},[e.ifgetcode?e._e():o("x-button",{attrs:{slot:"right",plain:"",type:"primary",mini:""},nativeOn:{click:function(t){e.getCode()}},slot:"right"},[e._v("发送验证码")]),e._v(" "),e.ifgetcode?o("x-button",{attrs:{slot:"right",plain:"",type:"primary",mini:""},nativeOn:{click:function(t){e.endtimer()}},slot:"right"},[e._v("重新发送"+e._s(e.countimer)+"s'")]):e._e()],1),e._v(" "),o("x-input",{ref:"password",attrs:{type:"password",title:"新的密码",name:"password",placeholder:"请输入密码",min:6,max:16,required:!0},model:{value:e.form.password,callback:function(t){e.$set(e.form,"password",t)},expression:"form.password"}}),e._v(" "),o("x-input",{ref:"check_password",attrs:{type:"password",title:"确认密码",name:"check_password",placeholder:"请输入密码",min:6,max:16,"equal-with":e.form.password,required:!0},model:{value:e.form.check_password,callback:function(t){e.$set(e.form,"check_password",t)},expression:"form.check_password"}})],1),e._v(" "),o("box",{attrs:{gap:"30px 10px 20px"}},[o("x-button",{attrs:{type:"primary"},nativeOn:{click:function(t){e.resetPwd()}}},[e._v("确认")])],1),e._v(" "),o("div",{staticClass:"backlogin",staticStyle:{"text-align":"center"},on:{click:function(t){e.backlogin()}}},[e._v("\n        返回登录页\n    ")])],1)},staticRenderFns:[]};var f=o("C7Lr")(d,u,!1,function(e){o("r3kB")},null,null);t.default=f.exports},r3kB:function(e,t){},x5MS:function(e,t,o){var i={"./views/login/pwdreset":"dGl8"};function r(e){return o(s(e))}function s(e){var t=i[e];if(!(t+1))throw new Error("Cannot find module '"+e+"'.");return t}r.keys=function(){return Object.keys(i)},r.resolve=s,e.exports=r,r.id="x5MS"}});