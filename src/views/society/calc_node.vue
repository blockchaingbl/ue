<template lang="html">
    <div class="deals-index">
        <group gutter="0px">
            <div class="weui-cell"><div class="weui-cell__hd"></div> <div class="vux-cell-bd vux-cell-primary">
                <p><label class="vux-label">
                    <a class="vux-rater-box" style="color: rgb(204, 204, 204); margin-right: 4px; font-size: 25px; width: 25px; height: 25px; line-height: 25px;">
                    <span class="vux-rater-inner">
                        <span style="color: black" v-for="(index,x) in rate">★</span>
                    </span>
                </a>节点</label>
                </p>
            </div></div>
            <cell title="节点人数" :value="total" @click.native="toggle=true"></cell>
            <cell title="节点资产" :value="incharge_less_amount"></cell>
            <cell title="昨日流通" :value="last_day_trans"></cell>
            <cell title="昨日算力" :value="benefit_amount"></cell>
        </group>
        <div class="friend-block">
            <div class="friend-item flex-box" v-for="user in user_list">
                <div class="user-row">
                    <img src="@/assets/images/avatar.png" alt="" v-if="user.avatar==''">
                    <img v-bind:src="user.avatar" v-else>
                </div>
                <div class="friend-text flex-1 flex-box vux-1px-b">
                    <div class="fitem-name flex-1">{{user.username}}</div>
                </div>
            </div>
        </div>
        <Scroller v-if="user_list.length>0" v-on:load="loadLists" :loading="loading" :container="'.friend-block'" ></Scroller>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
    </div>
</div>
</template>
<script>
    import {  LoadMore , Divider, XTable,XButton,Rater } from 'vux';
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
            Rater
        },
        data () {
            return {
                select:false,
                select1:false,
                formData:{page:1},
                lock:false,
                user_list : [],
                loading:false,
                total:'',
                incharge_less_amount:'',
                rate:0,
                income:'',
                last_day_trans:'',
                benefit_amount:'',
                toggle:false
            }
        },
        created(){
            this.$vux.loading.show({
                text: ''
            })
            this.loadLists();
        },
        mounted() {
        },
        methods:{
            loadLists(isRefresh=false){
                if (this.loading) {
                    //正在加载中
                }else if(this.formData.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.apply/apply/calc',this.formData).then(res => {
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
                        }
                        this.total = res.data.total
                        this.incharge_less_amount = res.data.incharge_less_amount
                        this.rate = res.data.rate
                        this.benefit_amount = res.data.benefit_amount;
                        this.last_day_trans = res.data.last_day_trans;
                        this.loading=false;
                        this.$vux.loading.hide()
                    }).catch(err => {
                        this.$vux.loading.hide()
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
