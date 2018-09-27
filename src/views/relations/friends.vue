<template lang="html">
    <div :id="page_name" class="page-friends">
        <tab v-model="tab_index">
            <tab-item >关注</tab-item>
            <tab-item >粉丝</tab-item>
            <tab-item >推荐</tab-item>
        </tab>
        <box v-show="tab_index==0">
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
                        <router-link   :to="'/mine/steal/'+user.id" class="fitem-link">
                            <XButton :mini="true" type="primary" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null">去偷币</XButton>
                        </router-link>
                        <XButton :mini="true" @click.native="follow(user)" class="followed flex-box" v-if="user.user_follow_id > 0 && user.user_follow_id != null">已关注</XButton>
                        <XButton :mini="true" @click.native="follow(user)" class="follow-btn flex-box" v-else><i class="iconfont">&#xe64d;</i>&nbsp;<span>关注</span></XButton> 
                    </div>
                </div>
            </div>

        </box>
        <box v-show="tab_index==1">
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
                        <router-link   :to="'/mine/steal/'+user.id" class="fitem-link">
                            <XButton :mini="true" type="primary" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null">去偷币</XButton>
                        </router-link>
                        <XButton :mini="true" @click.native="follow(user)" class="followed flex-box" v-if="user.user_follow_id > 0 && user.user_follow_id != null">已关注</XButton>
                        <XButton :mini="true" @click.native="follow(user)" class="follow-btn flex-box" v-else><i class="iconfont">&#xe64d;</i>&nbsp;<span>关注</span></XButton>
                    </div>
                </div>
            </div>
        </box>
        <box v-show="tab_index==2">
            <search placeholder="请输入对方手机号码" v-model="keyword" position="static" @on-submit="search" @on-cancel="cancel" ref="search" class="friends-search"></search>
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
                        <router-link   :to="'/mine/steal/'+user.id" class="fitem-link">
                            <XButton :mini="true" type="primary" v-if="user.user_follow_id > 0 && user.user_follow_id != null && user.user_fans_id > 0 && user.user_fans_id != null">去偷币</XButton>
                        </router-link>
                        <XButton :mini="true" @click.native="follow(user)" class="followed flex-box" v-if="user.user_follow_id > 0 && user.user_follow_id != null">已关注</XButton>
                        <XButton :mini="true" @click.native="follow(user)" class="follow-btn flex-box" v-else><i class="iconfont">&#xe64d;</i>&nbsp;<span>关注</span></XButton>
                    </div>
                </div>
            </div>
        </box>
        <Scroller v-if="user_list.length>0" v-on:load="loadLists" :loading="loading" :container="'.block-box'" ></Scroller>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
    </div>
</template>
<script>
    import { XHeader,XInput,XButton,Box,AlertModule,Tab,TabItem,XTextarea,Loading,XTable,Search } from 'vux';
    import Scroller from "@/components/scroller";
    import  { LoadingPlugin } from 'vux'
    import router from '@/router';
    import axios from '@/axios';
    import Nodata from "@/components/nodata";
    export default {
        components: {
            "x-header":XHeader,
            "x-input":XInput,
            "x-button":XButton,
            "box":Box,
            "tab":Tab,
            "tab-item":TabItem,
            "x-textarea":XTextarea,
            "loading":Loading,
            "x-table":XTable,
            Scroller,XButton,Search,
            Nodata,
        },
        data () {
            console.log(router.currentRoute);
            return {
                page_title:router.currentRoute.meta.title,
                page_name:router.currentRoute.name,
                tab_index:0,
                formData:{page:1},
                lock:false,
                user_list : [],
                loading:false,
                keyword:""
            }
        },
        mounted () {
            //console.log(XInput);
            //console.log(this.$refs.wallet_name);
            this.loadLists();
        },
        methods: {
            cancel(){
                this.$refs.search.setBlur();
                this.loading = false;
                this.user_list = [];
                this.formData.page = 1;
                this.loadLists();
            },
            search () {
                this.$refs.search.setBlur();
                this.loading = false;
                this.user_list = [];
                this.formData.page = 1;
                this.loadLists(true);
            },
            follow(item){
                    console.log(item)
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
                });
            },
            loadLists(search=false){
                if (this.loading) {
                    //正在加载中
                }else if(this.formData.page!=null){
                    //加载完毕
                    this.loading=true;
                    let url = '';
                    let query = this.formData;
                    switch (this.tab_index){
                        case 0:
                            url = '/api/app.user/relations/myfollow';
                            break;
                        case 1:
                            url = '/api/app.user/relations/myfans';
                            break;
                        case 2:
                            url = '/api/app.user/relations/recommend';
                            break;
                    }
                    if(search)
                    {
                        query = {'search_key':this.keyword,'page':this.formData.page};
                        url = '/api/app.user/relations/find';
                    }
                    this.$http.post(url,query).then(res => {
                        if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.user_list.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.formData.page++;
                        this.user_list=this.user_list.concat_unk(res.data.user_list,"id");
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
        },
        watch:{
            tab_index:{
                handler(current_index,last_index){
                    while (axios.cancelTokens.length > 0) {
                        var cancel = axios.cancelTokens.pop();
                        cancel();
                    }
                    this.loading = false;
                    this.user_list = [];
                    this.formData.page = 1;
                    this.keyword = "";
                    this.$refs.search.setBlur();
                    this.loadLists();
                }
            }
        }
    }
</script>
<style lang="less" scoped>
    @import '~vux/src/styles/1px.less';
    #relationsFriends{
    .vux-x-input,.vux-x-textarea{ font-size:.8rem;}
    .vux-search-fixed{ position: inherit;}
    }
    .page-friends{
        min-height: 100%;
        .friend-item {
            background-color: #fff;
            height: 3.75rem;
            padding-left: 0.625rem;
            &:last-child{
                .friend-text{
                    &::after{
                        display: none;
                    }
                }
            }
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
                overflow: hidden;
                padding: 0 0.5rem;
                background-color: #fff;
                color: #666;
                height: 1.4rem;
                border-radius: 1.25rem;
                font-size: 0.8125rem;
                line-height: 1;
                display: flex;
                span{
                    display: block;
                    height: 1.375rem;
                    line-height: 1.25rem;
                }
                
            }
            button.weui-btn_primary{
                padding: 0 1rem;
                color: #fff;
                background-color: #628cf8;
            }
            .weui-btn::after{
                display: none;
            }
            .weui-btn ~ .weui-btn{
                margin-left: 0.625rem;
                
            }
            button.follow-btn{
                color: #e84300;
                 border:1px solid #e84300;
                i{
                    font-size: 0.625rem;
                    height: 0.625rem;
                } 
            }
            button.followed{
                border:1px solid #bbb;
                line-height: 1.25rem;
            }
        }
        .friends-search{
            line-height: 1.75rem;
        }
    }
</style>


