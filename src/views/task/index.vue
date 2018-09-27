<template lang="html">
<div class="task-index">
    <div class="head flex-box">
        <div class="head-item flex-1">我当前的算力：
            <span>{{cp_total}}</span>
        </div>
        <div class="head-item">我的算力排名：<span>{{cp_rank}}</span></div>
    </div>
<!--     <div class="assets flex-box">
        <div class="self-money flex-box flex-1">
            <router-link class="money-text" to="/user/selfmoney">
                <div class="assets-text">我的资产</div>
            </router-link>
        </div>
        <box gap="0" class="flex-box">
            <x-button type="primary" style="border-radius:99px;" class='found-btn' link="/deals/center">受让</x-button>
            <x-button type="primary" style="border-radius:99px;" class='import-btn' @click.native="toWithdraw()">上链</x-button>
        </box>
    </div> -->
    <div class="item-head">
        <div class="item-head-title">算力越高&nbsp;&nbsp;&nbsp;&nbsp;挖到{{$store.state.init.coin_uint}}越多</div>
        <div class="item-head-desc">完成以下任务可获得更多算力</div>
    </div>
    <div class="task-block">
  <!--       <a v-on:click="toJump()" class="item flex-box">
            <div class="item-img">
                <img src="@/assets/images/task_citem1.png" alt="">
            </div>
            <div class="item-info flex-1">
                <div class="item-title flex-box">
                    <div class="title-text flex-1">商城流通</div>
                    <div class="item-type">
                        <div class="nohas" v-if='banner.o2o.open==0'>未开通</div>
                        <div class="has" v-else>已获得 <span>{{banner.o2o.cp_total}}</span> 算力</div>
                    </div>
                </div>
                <div class="item-decs flex-box">
                    <div class="item-decs-list">前往商城流通返还相应算力</div>
                </div>
            </div>
        </a> -->
        <!--<a :href="banner.game.url" class="item flex-box">-->
            <!--<div class="item-img">-->
                <!--<img src="@/assets/images/task_citem2.png" alt="">-->
            <!--</div>-->
            <!--<div class="item-info flex-1">-->
                <!--<div class="item-title flex-box">-->
                    <!--<div class="title-text flex-1">{{banner.game.name}}</div>-->
                    <!--<div class="item-type">-->
                        <!--<div class="nohas" v-if='banner.game.open==0'>未开通</div>-->
                        <!--<div class="has" v-else>已获得 <span>{{banner.game.cp_total}}</span> 算力</div>-->
                    <!--</div>-->
                <!--</div>-->
                <!--<div class="item-decs flex-box">-->
                    <!--<div class="item-decs-list">{{banner.game.desc}}</div>-->
                <!--</div>-->
            <!--</div>-->
        <!--</a>-->
        <div class="item flex-box">
            <div class="item-img">
                <img src="@/assets/images/task_citem3.png" alt="">
            </div>
            <div class="item-info flex-1">
                <div class="item-title flex-box">
                    <div class="title-text flex-1">每日登录</div>
                    <div class="item-type">
                        <div class="nohas" v-if="banner.sign.open==0">未开通</div>
                        <div class="has" v-else>已获得 <span>{{banner.sign.cp_total}}</span> 算力</div>
                    </div>
                </div>
                <div class="item-decs flex-box">
                    <div class="item-decs-list">登录即可得{{banner.sign.income_cp}}算力</div>
                </div>
            </div>
        </div>
        <router-link to="/task/invitation" class="item flex-box">
            <div class="item-img">
                <img src="@/assets/images/task_citem4.png" alt="">
            </div>
            <div class="item-info flex-1">
                <div class="item-title flex-box">
                    <div class="title-text flex-1">邀请好友</div>
                    <div class="item-type">
                        <div class="nohas" v-if="banner.invite.open==0">未开通</div>
                        <div class="has" v-else>已获得 <span>{{banner.invite.cp_total}}</span> 算力</div>
                    </div>
                </div>
                <div class="item-decs flex-box">
                    <div class="item-decs-list">每邀请一名好友完成注册可得{{banner.invite.income_cp}}算力</div>
                </div>
            </div>
        </router-link>
        <div class="item flex-box" @click="toJump">
            <div class="item-img">
                <img src="@/assets/images/task_citem5.png" alt="">
            </div>
            <div class="item-info flex-1">
                <div class="item-title flex-box">
                    <div class="title-text flex-1">学习成长</div>
                    <div class="item-type">
                        <div class="has">已获得 <span>{{banner.study.cp_total}}</span> 算力</div>
                    </div>
                </div>
                <div class="item-decs flex-box">
                    <div class="item-decs-list">每天学习区块链知识,可得1算力</div>
                </div>
            </div>
        </div>
    </div>
    <div class="divider">更多任务即将开放，敬请期待...</div>
</div>   
</template>
<script>

export default {
    name: "taskCenter",
    components: {
        
    },
    data () {
        return {
            cp_total:0,
            cp_rank:0,
            vc_total:0,
            banner:{o2o:{},game:{},sign:{},invite:{},study:{}}
        }
    },
    mounted () {
        this.getUserinfo();
        this.getBanner();
    },
    methods:{
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.cp_total=res.data.account_info.cp_total;
                this.cp_rank=res.data.account_info.cp_rank;
                this.vc_total=res.data.account_info.vc_total;
            }).catch(err => {
                if (err.errcode)
                this.$vux.toast.text(err.message);
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        getBanner(){
            this.$http.post('/api/app.task/banner',{}).then(res => {
                this.banner=res.data.banner_list;
            }).catch(err => {
                if (err.errcode)
            this.$vux.toast.text(err.message);
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        toJump(){
            this.$http.post('/api/app.task/banner/study',{}).then(res => {
                if(this.$store.state.init.is_app){
                    App.open_type('{"url":"'+this.banner.study.url+'"}');
                }else{
                    location.href = this.banner.study.url;
                }
            }).catch(err => {
                if(this.$store.state.init.is_app){
                    App.open_type('{"url":"'+this.banner.study.url+'"}');
                }else{
                    location.href = this.banner.study.url;
                }
            });
        },
        toWithdraw(){
            if(this.$store.state.init.withdraw_open==1){
                this.$router.push({"path":"/user/withdrawlist"});
            }else{
                this.$vux.toast.text("上链暂未开放");
            }
        }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    
    .task-index{
        .head{
            height: 3rem;
            background:-webkit-linear-gradient(left,#26cdcd,#5c81ea);
            background:linear-gradient(to right,#26cdcd,#5c81ea);
            font-size: @fs-middle;
            color: #fff;
            padding: 0 0.625rem;
            span{
                font-family: arial;
                font-size: 1rem;
            }
        }
        .assets {
            padding: 0 0.625rem;
            background: #fff;
            height: 3.625rem;
            .self-money {
                line-height: 1.5rem;
                .iconfont{
                    font-size: @fs-middle;
                    color: #6b94f8;
                    margin-top: 3px;
                    margin-right: 6px;
                }
                .money-text {
                    font-size: @fs-middle;
                    color: #000;
                    .assets-numb{
                        padding-left: 1.25rem;
                        font-size: 1.25rem;
                        color: #6b94f8;
                        font-weight: bold;
                    }
                }
            }
            .weui-btn{
                font-size: @fs-middle;
                width: 4.9375rem;
            }
            .weui-btn + .weui-btn{
                margin-top: 0;
                margin-left: 0.25rem;
                background-color: #fc8c92;
            }
        }
        .item-head{
            background: url(../../assets/images/task_center_item.png) no-repeat top center;
            background-size: 100% 100%;
            height: 3.75rem;
            color: #fff;
            text-align: center;
            margin: 0.625rem;
            padding: 0.5625rem;
            .item-head-title{
                font-size: 1rem;
                line-height: 1.5rem;
            }
            .item-head-desc{
                font-size: 0.6875rem;
                line-height: 1.1875rem;
            }
        }
        .task-block{
            .item {
                height: 5.9375rem;
                background: #fff;
                margin: 0 0.625rem 0.625rem;
                border-radius: 4px;
                box-shadow: 0 2px 7px 0 rgba(0,0,0,0.05);
                padding: 0.3125rem 0.625rem;
                // .item-info{
                //     height: 3.75rem;
                // }
                .item-img {
                    width: 2.25rem;
                    height: 2.25rem;
                    margin-right: @fs-small;
                    img{
                        width: 100%;
                        height: 100%;
                        display: block;
                    }
                }
                .item-title{
                    line-height: 1.5rem;
                    .has {
                        color: #888;
                        span{
                            color: #4c4c51;
                        }
                    }
                }
                .title-text {
                    font-size: 1rem;
                    color: #4c4c51;
                }
                .item-type {
                    font-size: @fs-middle;
                }
                .nohas {
                    font-weight: bold;
                    color: #fc8c92;
                    line-height: 1.6rem;
                }
                .item-decs{
                    flex-wrap: wrap;
                }
                .item-decs-list{
                    padding-left: 0.5rem;
                    font-size: 0.8125rem;
                    line-height: 1.25rem;
                    color: #888;
                    position: relative;
                    min-width: 8.25rem;
                    &:before{
                        display: block;
                        content: '';
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        left: 0;
                        width: 0.25rem;
                        height: 0.25rem;
                        border-radius: 50%;
                        background: #888;
                    }
                }
            }
        }
        .divider{
            font-size: @fs-middle;
            line-height: 1.875rem;
            color: #628df8;
            text-align: center;
        }
    }
</style>
