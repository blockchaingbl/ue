<template lang="html">
<div class="page-setting">
    <div class="user-list-box">
            <group>
                <cell title="支付设置" :link="{path:'/user/setpay'}">
                    <i slot="icon" class="iconfont icon_payset" width="20" style="display:block;margin-right: 1rem;">&#xe704;</i>
                </cell>
                <cell title="资产密码" :link="{path:'/user/edit/security'}">
                    <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe707;</i>
                </cell>
                <cell title="发糖果" :link="{path:'/candy/distribute'}" v-if="$store.state.sugar_auth>0">
                    <i slot="icon" class="iconfont  icon_wallet" width="20" style="display:block;margin-right: 1rem;">&#xe614;</i>
                </cell>
                <cell title="锁仓转出" :link="{path:'/lock_transfer/grant/0'}" v-show="lock_transfer_auth >0 || $store.state.lock_transfer_auth>0">
                    <i slot="icon" class="iconfont  icon_assets" width="20" style="display:block;margin-right: 1rem;">&#xe610;</i>
                </cell>
                <cell title="我的糖果" :link="{path:'/candy/trans'}">
                    <i slot="icon" class="iconfont icon_payset" width="20" style="display:block;margin-right: 1rem;">&#xe61a;</i>
                </cell>
                <cell title="矿机商城" :link="{path:'/mine_machine'}">
                    <i slot="icon" class="iconfont icon_assets" width="20" style="display:block;margin-right: 1rem;">&#xe622;</i>
                </cell>
                <cell title="关于我们" :link="{path:'/about'}" v-if="$store.state.init.about_open">
                    <i slot="icon" class="iconfont icon_wallet" width="20" style="display:block;margin-right: 1rem;">&#xe62d;</i>
                </cell>
                <cell title="意见反馈" :link="{path:'/connect'}" v-if="$store.state.init.connect_open">
                    <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe62c;</i>
                </cell>
                <cell title="联系我们" :link="{path:'/connect_text'}" v-if="$store.state.init.connect_open">
                    <i slot="icon" class="iconfont icon_payset" width="20" style="display:block;margin-right: 1rem;">&#xe63b;</i>
                </cell>
                <cell title="系统公告" :link="{path:'/notice'}" v-if="$store.state.init.notice_open">
                    <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe638;</i>
                </cell>
                <cell title="帮助中心" is-link @click.native="open">
                    <i slot="icon" class="iconfont icon_accset" width="20" style="display:block;margin-right: 1rem;">&#xe6f5;</i>
                </cell>
            </group>
        </div>
        <div class="user-list-box">
            <group>
                <cell title="退出登录"  v-on:click.native="logout()">
                    <i slot="icon" class="iconfont icon_back" width="20" style="display:block;margin-right: 1rem;">&#xe703;</i>
                </cell>
            </group>
        </div>
</div> 
</template>
<script>
import {Confirm,Group, XSwitch, XButton, TransferDomDirective as TransferDom } from 'vux'
import { Flexbox, FlexboxItem, Divider } from 'vux'
import { setCookie, getCookie, deleteCookie,clearCookie } from "../../assets/js/cookieHandle";
import VueCoreImageUpload from 'vue-core-image-upload'
export default {
    directives: {
        TransferDom
    },
    components: {
        Confirm,
        Group,
        XSwitch,
        XButton,
        Flexbox,
        FlexboxItem,
        Divider,
        'vue-core-image-upload': VueCoreImageUpload
    },
  data () {
    return {
        show5: true,
        useravatar:"",
        usermobile:"",
        username:"",
        editname:"",
        uploadUrl:"/api/app.util/upload",
        address:'',
        params:{
            type:"avatar",
            _user_token:this.$store.state.token
        },
        lock_transfer_auth:0,
        url:"https://shop.bmweixin.com/app/index.php?r=qa&i=20&do=mobile&wxref=mp.weixin.qq.com&c=entry&m=ewei_shopv2#wechat_redirect"
    }
  },
  mounted () {
      this.getUserinfo();
  },
  methods:{
    onConfirm5(){},
        onShow5(){},
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.usermobile=res.data.account_info.mobile;
                this.username=res.data.account_info.username;
                this.useravatar=res.data.account_info.avatar;
                this.address = res.data.account_info.address
                this.$store.state.sugar_auth = res.data.account_info.sugar_auth;
                this.$store.state.lock_transfer_auth =  parseInt(res.data.account_info.lock_transfer_auth);
                this.lock_transfer_auth = parseInt(res.data.account_info.lock_transfer_auth);
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        test(){

                    localStorage.clear();
                    clearCookie();
        },
        logout(){
            let _this = this;
            this.$vux.confirm.show({
                title: '确定要退出吗？',
                onCancel(){},
                onConfirm(){
                    console.log("退出");
                   // setCookie("token","");
                    localStorage.removeItem('token');
                    localStorage.clear();
                    clearCookie();
                    setCookie("isSign","");
                    _this.$store.commit("reset_state");
                    _this.$vux.toast.text("退出成功");
                    _this.$router.push({path:'/login'});
                }
            })
        },
        tis_btn(){
            this.$vux.toast.text('敬请期待');
        },
        open(){
            if(this.$store.state.init.is_app){
                App.open_type('{"url":"'+this.url+'"}');
            }else{
                location.href = this.url;
            }
        }
  }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .page-setting{
        .user-list-box{
            margin-top: 10px;
            .weui-cells{
                margin: 0;
                &:before{
                    display: none;
                }
                &:after{
                    display: none;
                }
            }
            .weui-cell{
                p{
                    font-size: @fs-middle;
                    color: #5d5d61;
                }
                .iconfont{
                    font-size: 20px;
                }
                .icon_assets{
                    color: #69a5f5;
                }
                .icon_wallet{
                    color: #ff5853;
                }
                .icon_payset{
                    color: #f1a30b;
                }
                .icon_accset{
                    color: #68a4f2;
                }
                .icon_back{
                    color: #f35a5e;
                }
            }
        }
    }
</style>
