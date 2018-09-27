<template lang="html">
    <div class="deals-index">
        <div class="friend-block">
            <div class="friend-item flex-box" v-for="user in user_list">
                <div class="user-row">
                    <img src="@/assets/images/avatar.png" alt="" v-if="user.avatar==''">
                    <img v-bind:src="user.avatar" v-else>
                </div>
                <div class="friend-text flex-1 flex-box vux-1px-b">
                    <div class="fitem-name flex-1">{{user.username}}
                        <span class="iconfont friend_ico" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null">&#xe8f8;</span>
                        <span class="iconfont friend_ico" v-else-if="user.user_fans_id > 0 && user.user_fans_id != null">&#xe619;</span>
                    </div>
                    <router-link   :to="'/mine/steal/'+user.id" class="fitem-link" v-show="$store.state.init.friends">
                        <XButton :mini="true" type="primary" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null">去偷币</XButton>
                    </router-link>
                    <XButton :mini="true" @click.native="follow(user)" v-if="user.user_follow_id > 0 && user.user_follow_id != null"  v-show="$store.state.init.friends">已关注</XButton>
                    <XButton :mini="true" @click.native="follow(user)" v-else class="follow-btn"  v-show="$store.state.init.friends">+&nbsp;关注</XButton>
                </div>
            </div>
        </div>
        <Scroller v-if="user_list.length>0" v-on:load="loadLists" :loading="loading" :container="'.friend-block'" ></Scroller>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
    </div>
</div>
</template>
<script>
    import {  LoadMore , Divider, XTable,XButton } from 'vux';
    import Scroller from "@/components/scroller";
    import Nodata from "@/components/nodata";
    export default {
        components: {
            Scroller,
            LoadMore,
            Divider,
            XTable,
            XButton,
            Nodata,
        },
        data () {
            return {
                select:false,
                select1:false,
                formData:{page:1},
                lock:false,
                user_list : [],
                loading:false
            }
        },
        created(){
            this.loadLists();
        },
        mounted() {
        },
        methods:{
            follow(item){
                let formData = {
                    follow_user_id: item.id
                };
                let http_url = item.user_follow_id ? '/api/app.user/relations/unfollow' : '/api/app.user/relations/follow';
                this.$http.post(http_url,formData).then(res => {

                    item.user_follow_id = res.data.user_follow_id;

                    this.$vux.toast.text(res.message);
                    console.log(res);

                }).catch(err => {
                    this.$vux.toast.text(err.message);
                    console.log(err);
                    //  this.Toast(err || '网络异常，请求失败');
                });
            },
            loadLists(isRefresh=false){
                if (this.loading) {
                    //正在加载中
                }else if(this.formData.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.user/relations/myinvite',this.formData).then(res => {
                        if(res.data==null){
                            this.formData.page=null;
                            this.loading=false;
                        }else if (res.data.user_list.length<1) {
                            this.formData.page=null;
                            this.loading=false;
                        }
                        else{
                            if(isRefresh){
                                this.formData.page = 1;
                            }
                            else {
                                this.formData.page++;
                            }
                            this.user_list=this.user_list.concat(res.data.user_list);
                            this.loading=false;
                        }
                    }).catch(err => {
                        console.log(err);
                        this.formData.page=null;
                        this.loading = false;
                    });
                }else{
                    this.loading=false;
                }
            },
            onScrollBottom(){
                this.formData.page++;
                this.loadLists();
            },

        }
    }
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .deals-index{
        font-family: Arial, "Microsoft Yahei";
        color: #4c4c51;
        .ddjl{
            position: absolute;
            right: 10px;
            font-size: .8rem;
        }
        .deals-head{
            background: #fff;
            height: 3.5rem;
            padding:0 0.625rem;
            font-size: @fs-middle;
            color: #4c4c51;
            .iconfont{
                font-size: @fs-middle;
                color: #6b94f8;
                margin-top: -1px;
                margin-right: 4px;
            }
            .head-text {
                .assets-numb {
                    padding-left: 1.125rem;
                    font-size: 1.25rem;
                    font-family: arial;
                    font-weight: bold;
                    color: #6b94f8;
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
        .banner{
            width: 100%;
            img{
                width: 100%;
                display: block;
            }
        }
        .deals-title {
            padding-left: 10px;
            height: 2.4375rem;
            text-align: left;
            line-height: 2.4375rem;
            font-size: @fs-normal;
        }
        .item {
            padding: 0 0.625rem;
            background: #fff;
            height: 2.875rem;
            font-size: @fs-middle;
            margin-bottom: 0.25rem
        }
        .item-head {
            height: 1.75rem;
            background: #b5b7be;
            color: #fff;
            margin-bottom: 0;
            font-size: @fs-small;
            .item-item{
                span{
                    position: relative;
                    &:after{
                        display: block;
                        content: '';
                        position: absolute;
                        border-left: 4px solid transparent;
                        border-right: 4px solid transparent;
                        border-top: 4px solid #fff;
                        top: 50%;
                        transform: translateY(-50%);
                        right: -16px;
                        transition: all 0.5s;
                    }
                }
            }
            .numb{
                font-size: @fs-small;
            }
            .active{
                span{
                    &:after{
                        transform:rotate(180deg) translateY(50%);
                    }
                }
            }
        }
        .item-item{
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .name{
            width: 6rem;
        }
        .numb{
            width: 4.5rem;
            font-size: @fs-normal;
        }
        .price{
            text-align: center;
            width: 50%;
            .item-price {
                color: #9b94f8;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                em{
                    font-size: @fs-biger;
                    line-height: 1.25rem;
                    font-weight: bold;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
            }
            .decs {
                font-size: @fs-small;
                line-height: 0.9375rem
            }
        }
        .item-btn {
            width: 2.75rem;
        }
        a.item-btn{
            background: #6b94f8;
            color: #fff;
            text-align: center;
            font-size: @fs-middle;
            height: 1.5rem;
            line-height: 1.5rem;
        }
    }
    .loadmore {
        user-select: none;
        color: #628cf8;
        padding: 20px;
        text-align: center;
        .tc-loading {
            ~ span {
                vertical-align: middle;
            }
        }
    }
    .deals-index{
        min-height: 100%;
        background: #fff;
        .friend-item {
            background-color: #fff;
            height: 3.75rem;
            padding-left: 0.625rem;
            .user-row {
                width: 2.375rem;
                height: 2.375rem;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                border-radius: 1.5rem;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .friend-text {
                margin-left: 0.625rem;
                width: 50%;
                height: 100%;
                padding-right: 0.625rem;
            }
            .fitem-name {
                font-size: 0.9375rem;
                color: #363840;
                width: 50%;
                overflow: hidden;
                text-overflow:ellipsis;
                white-space: nowrap;
            }
            .fitem-link{
                margin-right: 0.625rem;
            }
            button.weui-btn{
                overflow: auto;
                padding: 0 0.625rem;
                background-color: #fff;
                color: #666;
                height: 1.375rem;
                border-radius: 0.6875rem;
                line-height: 1.375rem;
                font-size: 0.6875rem;
            }
            button.weui-btn_primary{
                padding: 0 1rem;
                color: #fff;
                background-color: #628cf8;
            }
            .weui-btn::after{
                border-radius: 1.375rem;
                border-color: #bbb;
                width: 199%;
            }
            .weui-btn ~ .weui-btn{
                margin-left: 0.625rem;
            }
            button.follow-btn{
                color: #e84300;
                &:after{
                    border-color: #e84300;
                }
            }
        }
        .friends-search{
            line-height: 1.75rem;;
        }
    }
</style>
