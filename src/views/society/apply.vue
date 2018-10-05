<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="candy-basic-opera">
                <group class="candy-numb"  @click.native="turn()">
                    <cell  title="简介：" class="introduction">
                        <div>
                            <span style="color: #2b85e4;font-size: 18px;">组建社群的好处</span>
                        </div>
                    </cell>
                </group>
                <group class="candy-numb" title="姓名">
                    <x-input  v-model="name" type="text"   placeholder="请输入姓名"  :required="true" ></x-input>
                </group>
                <group class="candy-numb vux-1px-b" title="手机号">
                    <x-input  v-model="mobile"  placeholder="请输入手机号" :max="11" keyboard="number"  type="number"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="所在市县">
                    <x-input  v-model="area" type="text"   placeholder="请输入所在市县"  :required="true" ></x-input>
                </group>
                <popup-radio title="资源情况" :options="resource" v-model="source">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">资源情况</p>
                </popup-radio>
                <group class="candy-numb" title="所属行业">
                    <x-input  v-model="industry" type="text"   placeholder="请输入所属行业"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="现团队人数">
                    <x-input  v-model="team_number" type="number"   placeholder="请填写人数"  :required="true" ></x-input>
                </group>
                <group class="candy-numb" title="预计发展规模">
                    <x-input  v-model="team_number_expect" type="number"   placeholder="请填写人数"  :required="true" ></x-input>
                </group>
                <popup-radio title="预计完成时间" :options="finish_month_arr" v-model="finish_month">
                    <p slot="popup-header" class="vux-1px-b grant-coin-slot">预计完成时间</p>
                </popup-radio>
                <group class="candy-numb" title="已通过审核社群推荐人">
                    <x-input  v-model="inviter" type="text"   placeholder="请填写推荐人"  :required="true" ></x-input>
                </group>
            </div>
            <div class="candy-senior-opera">
                <group class="lock-time">
                    <div class="lock-select flex-box">
                        <div class="lock-se-title flex-1">是否全职发展</div>
                        <x-switch :title="full_time_text" v-model="full_time"></x-switch>
                    </div>
                </group>
            </div>
            <group class="grant-bottom">
                <cell><span v-if="status_show==3">未通过原因：</span>{{memo}}</cell>
            </group>
            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="publish" v-if="status_show==0">申请成立社群</x-button>
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;"  v-if="status_show==1">申请中,请耐心等待</x-button>
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;"  v-if="status_show==2">申请已通过</x-button>
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="publish"  v-if="status_show==3">申请未通过,可再次申请</x-button>
            </box>
        </div>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
import { Datetime, Loading,LoadMore,PopupRadio,XSwitch, TransferDomDirective as TransferDom } from 'vux'
    export default {
        directives: {
            TransferDom
        },
        components: {
            Datetime,
            Loading,
            LoadMore,
            PopupRadio,
            XSwitch
        },
        data() {
            return {
                name:'',
                area:'',
                team_number_expect:'',
                mobile:'',
                full_time:0,
                source:'良好',
                team_number:'',
                industry:"",
                lock:false,
                status:null,
                showload:true,
                err_show:'',
                memo:'',
                resource:['良好','一般','无'],
                status_show:null,
                loading:false,
                finish_month:'1个月',
                finish_month_arr:['1个月','2个月','3个月'],
                inviter:''
            };
        },
        mounted() {
            const _this = this
            window.CutCallBack = function (base64Str) {
                _this.CutCallBack(base64Str)
            }
            this.getDetail();
        },
        methods: {
            getDetail(){
                this.$vux.loading.show({
                    text: ''
                })
                this.$http.post('/api/app.apply/apply/applyinfo',{}).then(res=>{
                    this.showload=false;
                    this.name = res.data.info.name;
                    this.industry = res.data.info.industry;
                    this.area = res.data.info.area;
                    this.mobile = res.data.info.mobile;
                    this.finish_month = res.data.info.finish_month;
                    this.source = res.data.info.source;
                    this.full_time = Boolean(res.data.info.full_time);
                    this.team_number = res.data.info.team_number;
                    this.status = res.data.info.status;
                    this.team_number_expect = res.data.info.team_number_expect;
                    this.memo = res.data.info.memo;
                    this.err_show = res.errcode;
                    this.status_show = res.data.info.status+1;
                    this.inviter = res.data.info.inviter
                    this.$vux.loading.hide()
                })
                .catch(error=>{
                    this.$vux.loading.hide()
                    this.showload=false;
                    this.pid = error.data.pid;
                    this.err_show = error.errcode;
                    if(this.err_show==70025){
                        this.status_show=0;
                    }
                    console.log(error)
                })
            },
            publish(){
                if(this.lock)
                {
                    return false;
                }
                this.lock = true;
                let form = {
                    name:this.name,
                    industry:this.industry,
                    full_time:this.full_time?1:0,
                    team_number_expect:parseInt(this.team_number_expect),
                    mobile:this.mobile ,
                    team_number:parseInt(this.team_number) ,
                    finish_month:this.finish_month,
                    source:this.source ,
                    area:this.area,
                    inviter:this.inviter
                }
                const _this = this;
                this.$http.post('api/app.apply/apply/apply',form).then(res=>{
                    if(res.errcode==0)
                    {
                        this.$vux.toast.text("申请成功");
                        this.status=0;
                       _this.getDetail()
                    }else{
                        this.$vux.toast.text(res.message);
                    }
                    this.lock = false;
                }).catch(err=>{
                    this.lock = false;
                    this.$vux.toast.text(err.message);

                })
            },
            turn(){
                const  url = "https://shop.bmweixin.com/app/index.php?i=20&c=entry&m=ewei_shopv2&do=mobile&r=qa.detail&id=19";
                if(this.$store.state.init.is_app){
                    App.open_type('{"url":"'+url+'"}');
                }else{
                    location.href = url;
                }
            }
        },
        computed:{
            full_time_text:function () {
                if(this.full_time)
                {
                    return '全职';
                }else{
                    return '非全职'
                }
            }
        }
    };
</script>
<style lang="less" scoped>
    @import "../../assets/css/variable.less";
    .page-grant{
        min-height: 100%;
        padding-bottom: 2.375rem;
        .candy-basic-opera{
            background: #fff;
            margin-bottom: 0.625rem;
        }
        .candy-senior-opera{

            background: #fff;
            margin-bottom: 0.625rem;
        }
        .coin-slot {
            text-align: center;
            padding: 8px 0;
            color: #888;
        }
        .text_red{
            color: red;
        }
        .cell-assets{
            padding-bottom: 0;
        }
        .grant-bottom{
            min-height: 6rem;
            background: #fff;
            margin-bottom: 0.625rem;
        }
        .grant-btn-box{
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .lock-time {
            padding-bottom: 0.25rem;
        }
        .lock-select{
            padding-left: 15px;
            padding-top: 10px;
        }
        .grant-money.vux-x-input.weui-cell{
            padding: 0.75rem 0.9375rem 1.25rem;
        }
        .candy-radio{
            padding: 0 0.9375rem;
            margin: 0.625rem 0;
            .radio-item {
                padding: 0.625rem;
                border: 1px solid #ddd;
                border-radius: 4px;
                .radio-title {
                    font-size: 0.8125rem;
                    line-height: 1.1875rem;
                }
                .radio-numb {
                    font-size: 1.0625rem;
                    line-height: 1.1875rem;
                }
            }
            .radio-item + .radio-item{
                margin-left: 0.9375rem;
            }
            .radio-item.active {
                border-color: #2f82ff;
                background: #2f82ff;
                color: #fff;
            }
        }
        .introduction /deep/ .vux-label{
            font-size: 18px;
        }
    }

</style>
