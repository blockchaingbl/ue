<template lang="html">
    <div class="page-about-connect">
            <div class="upload_pic_title">请上传图片(选填)</div>
            <div class="setalipay-top">
                <div class="update-box flex-box">
                    <div class="iconfont" v-if="image==''">&#xe637;</div>
                    <div class="setalipay-top-text" v-if="image==''">

                    </div>
                    <img class="qrcode-img" v-bind:src="image" v-else>
                </div>
                <div class="upload-btn" v-if="!$store.state.init.is_app">
                    <vue-core-image-upload
                            class="btn btn-primary"
                            :crop="false"
                            @imageuploaded="imageuploaded"
                            :data="params"
                            :max-file-size="5242880"
                            inputOfFile="file"
                            :url="uploadUrl"
                            text="">
                    </vue-core-image-upload>
                </div>
                <div class="upload-btn" v-else @click="AppUpload"></div>
            </div>
            <div class="block">
                <group title="请输入反馈内容">
                    <x-textarea    :max="500" name="detail" v-model="content" :show-counter="true"></x-textarea>
                </group>
            </div>
            <div class="reset-btn-box">
                <box gap="30px 35px">
                    <x-button type="primary" style="border-radius:99px;" class='found-btn' v-on:click.native="submit()">提交</x-button>
                </box>
            </div>
        <div v-if="!loadings">
            <div class="connect-box">
                <div class="connect-block">
                    <div class="item-box" v-for="(connect,index) in connect_list" :key="index">
                        <div class="item flex-box">
                            <div class="item-img">
                                <img :src="connect.image" alt="反馈图片" v-if="connect.image">
                                <img src="@/assets/images/block_chain.jpg" alt="默认图片" v-else>
                            </div>
                            <div class="item-con flex-1">
                                <!--<div class="item-title"></div>-->
                                <div class="item-decs">反馈时间:{{connect.create_time}}</div>
                                <div class="item-content">反馈内容：<em>{{connect.content}} </em></div>
                                <div class="item-decs" v-if="connect.huifu">反馈回复：<em>{{connect.huifu}} </em></div>
                                <div class="item-decs" v-else>反馈回复：<em>请耐心等待回复</em> </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Scroller v-if="connect_list.length>0" v-on:load="loadlist" :loading="loading" :container="'.connect-block'" ></Scroller>
                <nodata  v-else :datatip="'暂无数据'"></nodata>
            </div>
        </div>
        <div v-transfer-dom>
            <loading :show="loadings"></loading>
        </div>
        </div>
</template>
<script>
    import { XTextarea, Group } from 'vux'
    import VueCoreImageUpload from 'vue-core-image-upload'
    import { Loading, TransferDomDirective as TransferDom} from 'vux';
    import Scroller from "@/components/scroller";
    import Nodata from "@/components/nodata";
    export default {
        directives: {
            TransferDom
        },
        components: {
            XTextarea,
            Group,
            'vue-core-image-upload': VueCoreImageUpload,
            Nodata,
            Loading,
            Scroller
        },
        data () {
            return {
                connect:this.$store.state.init.connect,
                title:'意见反馈',
                image:'',
                uploadUrl:"/api/app.util/upload",
                params:{
                    type:"connect",
                    _user_token:this.$store.state.token
                },
                content:'',
                connect_list:[],
                type:1,
                page:2,
                loadings:true,
                loading:false,
            }
        },
        created () {
            const _this = this
            this.$http.post('/api/app.cms/connect/connect',{}).then(res=>{
                if(res.errcode==0)
                {
                    this.connect = res.data.connect
                }
            });
            this.loadconnect();
        },
        mounted(){
            const _this = this
            window.CutCallBack = function (base64Str) {
                _this.CutCallBack(base64Str)
            }
        },
        methods:{
            imageuploaded(res) {
                if (res.errcode == 0) {
                    this.image = res.data.file_url;
                }
            },
            AppUpload(){
                App.CutPhoto('{"w":"200","h":"200"}');
            },
            CutCallBack(base64Str){
                var formData = new FormData();
                formData.append('type','connect');
                formData.append('file',convertBase64UrlToBlob(base64Str));
                let config = {
                    header:{
                        'Content-Type': 'multipart/form-data'
                    }
                }
                this.$http.post('/api/app.util/upload', formData,config)
                    .then(res=>{
                        if (res.errcode == 0) {
                            this.image = res.data.file_url;
                        }
                    })
            },
            submit(){
                let data = {
                    content:this.content,
                    image:this.image
                }
                this.$http.post('/api/app.cms/connect/store',data).then(res=>{
                    if(res.errcode==0)
                    {
                        this.$vux.toast.text('感谢您的积极反馈');
                        setTimeout(()=>{
                            this.$router.push({path:'/user/center'});
                        },2000)
                    }
                }).catch(err=>{
                    this.$vux.toast.text(err.message);
                })
            },
            loadconnect(){
                this.$http.post('/api/app.user/account/huifu',{type:this.type,page:1}).then(res=>{
                    this.connect_list=res.data.huifus;
                    this.loadings=false;
                    if(res.data.huifus.length<10){
                        this.loading=false;
                        this.page=null;
                    }
                })
                    .catch(error=>{
                        this.$vux.toast.text(error.message);
                    })
            },
            loadlist(){
                if (this.loading) {
                    //正在加载中
                }else if(this.page!=null){
                    //加载完毕
                    this.loading=true;
                    this.$http.post('/api/app.user/account/huifu',{type:this.type,page:this.page}).then(res=>{
                        if (res.data.huifus.length<1) {

                            this.page=null;
                            this.loading=false;
                            this.loadings = false;
                        }else{
                            this.connect_list=this.connect_list.concat_unk(res.data.huifus,"id");
                            this.loadings = false;
                            this.page++;
                            this.loading=false;
                        }
                    }).catch(err => {
                        if (err.errcode) {
                            this.$vux.toast.text(err.message);
                        }
                    });

                }else{
                    this.loading=false;
                }
            },
        }
    }
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .page-about-connect{
        .upload_pic_title{
            margin-top: 0.77em;
            margin-bottom: 0.3em;
            padding-left: 15px;
            padding-right: 15px;
            color: #999999;
            font-size: 14px;
        }
        .content{
            width:100%;
            overflow:hidden;
            padding: 0.625rem;
            background: #fff;
            margin-bottom: 0.625rem;
            p{
                font-size: 0.875rem;
                line-height: 1.125rem;
                margin-bottom: 0.625rem;
                &:last-child{
                    margin-bottom: 0;
                }
            }
            img{
                max-width: 100%;
            }
        }
        .setalipay-top{
            padding: 0.625rem;
            background: #fff;
            .update-box{
                margin-right: 1rem;
                overflow: hidden;
                display: inline-block;
                border: 1px dashed #8d97a3;
                border-radius: 5px;
                color: #8d97a3;
                font-size: @fs-small;
                flex-direction: column;
                justify-content: center;
                height: 6.375rem;
                .iconfont{
                    font-size: 2rem;
                    margin-left: 1.4rem;
                    margin-top: 2rem;
                }
                .setalipay-top-text{
                    line-height: 1.75rem;
                }
                .qrcode-img{
                    max-width: 100%;
                    max-height: 90%;
                    display: inline-block;
                    margin-top: 0.125rem;
                }
                width: 5rem;
            }
        }
        .block{
            margin-top: 0.625rem;
            background: #fff;
        }



        .upload-btn{
            position: absolute;
            top: 0.625rem;
            left: 0.625rem;
            right: 0.625rem;
            height: 7.375rem;
            .btn{
                height: 100%;
            }
            width: 5rem;
        }
        min-height: 100%;
        padding-bottom: 0.625rem;
        background: #fff;
        .item-box{
            background: #f5f5f5;
            padding-bottom: 0.3125rem;
        }
        .item {
            padding: 0.9375rem;
            background: #fff;
            color: #363840;
            .item-img {
                width: 6rem;
                overflow: hidden;
                margin-right: 0.625rem;
                position: relative;
                flex-shrink: 0;
                &::before {
                    display: block;
                    content: '';
                    padding-top: 75%;
                }
                img {
                    width: 80%;
                    height: 100%;
                    display: block;
                    position: absolute;
                    top: 0;
                    left: 0;
                }
                img[src='']{
                    border: 1px solid #eee;
                }
            }
            .item-con{
                width: 50%;
            }
            .item-title {
                line-height: 1.5rem;
                font-size: 0.9375rem;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;

            }
            .item-content{
                font-size: 0.6875rem;
                color: #999;
                line-height: 1.25rem;
                margin: 0.25rem 0;
                overflow: hidden;
                text-overflow:ellipsis;
                white-space: nowrap;
            }
            .item-decs {
                font-size: 0.6875rem;
                color: #999;
                line-height: 1.25rem;
                margin: 0.25rem 0;
            }
            .item-price {
                font-size: 0.6875rem;
                color: #999;
                text-align:justify;
                text-justify:inter-ideograph;
                em {
                    font-size: 0.9375rem;
                    color: #363840;
                }
            }
        }
    }
</style>
