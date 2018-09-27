<template lang="html">
<div class="deals-index">
    <div class="deals-head flex-box">
        
        <div class="head-text flex-1">
            <div class="assets-text">剩余{{$store.state.init.coin_uint}}：</div>
            <div class="assets-numb">{{vc_total}}</div>
        </div>
        <box gap="0" class="flex-box">
            <x-button type="primary" style="border-radius:99px;" class='found-btn' link="/deals/sell" v-if="$store.state.init.otc_sale_auth==0 || otc_auth">出让</x-button>
            <x-button type="primary" style="border-radius:99px;" class='import-btn' @click.native="toWithdraw()">上链</x-button>
        </box>
    </div>
    <!--<div class="banner">-->
        <!--<img src="@/assets/images/deals_banner.jpg" alt="">-->
    <!--</div>-->
    <div id="myChart" :style="{width: '100%', height: '200px', background: '#263238'}"></div>
    <div class="deals-content">
        <div class="deals-title">正在出让的{{$store.state.init.coin_uint}}
                <router-link to="/deals/record" class="ddjl">我的订单</router-link> </div>
        <div class="deals-block">
            <div class="item item-head flex-box">
                <div class="item-item name"></div>
                <div class="item-item numb" :class="{ 'active': select }" v-on:click='add_active()'><span>数量</span></div>
                <div class="item-item price flex-1"  :class="{ 'active': select1 }" v-on:click='add_active1()'><span>行情</span></div>
                <div class="item-item item-btn"></div>
            </div>
             <div class="block-box">
                <div class="item flex-box" v-for="otc in otc_list">
                    <div class="item-item name">{{otc.username}}</div>
                    <div class="item-item numb">{{otc.vc_less_amount}}</div>
                    <div class="item-item price flex-1">
                        <div class="item-price">&yen; <em>{{otc.total_price}}</em></div>
                        <div class="decs">单价 &yen; {{otc.vc_unit_price}}</div>
                    </div>
                    <router-link :to="{name:'dealsBuy',params:{id:otc.id}}" class="item-item item-btn">受让</router-link>
                </div>
            </div>
            <Scroller v-if="otc_list.length>0" v-on:load="loadOtcLists" :loading="loading" :container="'.block-box'" ></Scroller>
            <nodata  v-else :datatip="'暂无数据'"></nodata>
        </div>
    </div>
</div>   
</template>
<script>
import {  LoadMore , Divider } from 'vux';
import Echarts from 'echarts';
import Scroller from "@/components/scroller";
import Nodata from "@/components/nodata";
export default {
    components: {
        Scroller,
        LoadMore,
        Divider,
        Echarts,
        Nodata
    },
    data () {
        return {
            select:false,
            select1:false,
            vc_total:0,
            formData:{page:1,type:0,orderkey:'',orderby:'desc'},
            lock:false,
            otc_list : [],
            loading:false,
            chart_date:[],
            chart_price:[],
            chart_price_max:0,
            chart_price_min:0,
            otc_auth:0
        }
    },
    mounted () {
        this.getUserinfo();
        //this.loadOtcLists();
        this.getChartData();
        this.getIncome();
    },
    methods:{
        add_active(index){
            if(this.select==false){
                this.formData.orderkey = 'vc_less_amount';
                this.formData.orderby = 'desc';
                this.otc_list = [];
                this.formData.page=1;
                this.select=true;
            }else{
                 this.formData.orderkey = 'vc_less_amount';
                this.formData.orderby = 'asc';
                this.formData.page=1;
                this.otc_list = [];
                this.select=false;
            }
            this.select1=false;
            this.getIncome();
        },
        add_active1(){
            if(this.select1==false){
                this.formData.orderkey = 'vc_unit_price';
                this.formData.orderby = 'desc';
                this.formData.page=1;
                this.otc_list = [];
                this.select1=true;
            }else{
                this.formData.orderkey = 'vc_unit_price';
                this.formData.orderby = 'asc';
                this.formData.page=1;
                this.otc_list = [];
                this.select1=false;
            }
            this.select=false;
            this.getIncome();
        },
        getUserinfo(){
            this.$http.post('/api/app.user/account/info',{}).then(res => {
                this.cp_total=res.data.account_info.cp_total;
                this.cp_rank=res.data.account_info.cp_rank;
                this.vc_total=res.data.account_info.vc_total;
                this.otc_auth=res.data.account_info.otc_auth;
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        getIncome(){
            this.$http.post('/api/app.otc/deals',this.formData).then(res => {
                this.otc_list = this.otc_list.concat_unk(res.data.otc_list,"id");
                if (res.data.otc_list.length<10) {
                    this.loading=false;
                    this.formData.page=null;
                }else{
                    this.formData.page=2;
                }
            }).catch(err => {
                if (err.errcode) {
                    this.$vux.toast.text(err.message);
                }
                console.log(err);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        loadOtcLists(){
            if (this.loading) {
                //正在加载中
            }else if(this.formData.page!=null){
                //加载完毕
                this.loading=true;
                this.$http.post('/api/app.otc/deals',this.formData).then(res => {
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.otc_list.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.otc_list=this.otc_list.concat_unk(res.data.otc_list,"id");
                        this.formData.page++;
                        this.loading=false;
                       // console.log(this.otc_list);
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
            this.loadOtcLists();
        },
        /*sortOtc(type){
            switch (type){
                case 'num_desc':
                    this.formData.orderkey = 'vc_less_amount';
                    this.formData.orderby = 'desc';

                break;
                case 'num_asc':
                    this.formData.orderkey = 'vc_less_amount';
                    this.formData.orderby = 'asc'
                    break;
                case 'price_desc':
                    this.formData.orderkey = 'vc_unit_price';
                    this.formData.orderby = 'desc'
                    break;
                case 'price_asc':
                    this.formData.orderkey = 'vc_unit_price';
                    this.formData.orderby = 'asc'
                    break;
                default:
                    break;
            }
            this.$refs.scrollerEvent.reset({top: 0})
            this.lock = false;
            this.otc_list = [];
            this.formData.page = 1;
            this.loadOtcLists();
        },*/
        getChartData(){
            this.$http.post('/api/app.util/chart/pricetrend',{}).then(res => {
                this.chart_date = res.data.date;
                this.chart_price = res.data.price;
                this.chart_price_max = res.data.max;
                this.chart_price_min = res.data.min;
                this.drawLine();
            }).catch(err => {
                 //   this.$vux.toast.text(err.message);
                //  this.Toast(err || '网络异常，请求失败');
            });
        },
        drawLine(){
            // 基于准备好的dom，初始化echarts实例
            let myChart = Echarts.init(document.getElementById('myChart'));
            // 绘制图表
            myChart.setOption({
                title: {
                    text: '行情走势',
                    subtext: '最高 '+this.chart_price_max+"  最低 "+this.chart_price_min+' 当前行情 '+this.$store.state.init.coin_price,
                    textStyle: {
                        color: '#6d7887',
                        fontSize: 14
                    },
                    subtextStyle: {
                        color: '#a7acae',
                        fontSize: 12
                    },
                    itemGap: 5,
                    padding: [12,20],
                    left: 0
                },
                grid: {
                    left: 45,
                    right: 20,
                    bottom: 30
                },
                xAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#6d7887'
                        }
                    },
                    data: this.chart_date
                },
                yAxis: {
                    axisLine: {
                        lineStyle: {
                            color: '#6d7887'
                        }
                    },
                    splitLine: false
                },
                series: [{
                    type: 'line',
                    smooth: true,
                    showSymbol: false,
                    itemStyle: {
                        color: '#67a6cd'
                    },
                    lineStyle: {
                        color: '#67a6cd'
                    },
                    data: this.chart_price
                }]
            });
        },
        toWithdraw(){
            if(this.$store.state.init.withdraw_open==1){
                this.$router.push({"path":"/user/selfmoney"});
            }else{
                this.$vux.toast.text("上链暂未开放");
            }
        }
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
</style>
