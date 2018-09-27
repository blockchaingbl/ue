<template lang="html">
    <scroller @on-scroll-bottom="onScrollBottom"  lock-x ref="scrollerEvent">
<div class="withdrawlist-box">
    <div class="head">
        您上链的{{coin_type.coin_unit}}
    </div>
    <tab :line-width="2" custom-bar-width="60px">
        <tab-item selected @click.native="switchStatus('')">全部</tab-item>
        <tab-item @click.native="switchStatus(0)">待审核</tab-item>
        <tab-item @click.native="switchStatus(1)">已完成</tab-item>
        <tab-item @click.native="switchStatus(2)">失败</tab-item>
    </tab>
        <div class="withdeawlist-block"  v-if="withdraw_lists.length>0">
            <div class="item"  v-for="(withdraw,index) in withdraw_lists">
            <div class=" flex-box">
                <div class="item-text flex-1">
                    <div class="title">上链数量：<span>{{withdraw.vc_amount}}</span></div>
                    <div class="account-number"> 上链时间：{{withdraw.create_time}}</div>
                </div>
                <div class="item-type unaudited" v-if="withdraw.status==0">待审核</div>
                <div class="item-type finish" v-if="withdraw.status==1">已完成</div>
                <div class="item-type fail" v-if="withdraw.status==2">失败</div>
            </div>
                <div v-if="withdraw.status==2" class="failtip">失败原因：{{withdraw.memo}}</div>
            </div>
            <load-more tip="正在加载 . . ." v-if="loading"></load-more>
            <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
            <div class="loadmore">
            </div>
        </div>
        <nodata  v-else :datatip="'暂无数据'"></nodata>

</div>
    </scroller>
</template>
<script>
import { Tab, TabItem , Scroller , LoadMore , Divider} from 'vux';
import Nodata from "@/components/nodata";
export default {
    components: {
        Tab,
        TabItem,
        Scroller,
        LoadMore,
        Divider,
        Nodata
    },
    data () {
        return {
            hasLogin:0,
            index01: 0,
            withdraw_lists:[],
            formData:{page:1,status:''},
            lock:false,
            loading :0,
            coin_type:{}
        }
    },
    created (){
      if(this.$store.state.coin_info.hasOwnProperty('coin_type')){
        this.coin_type = this.$store.state.coin_info.coin_type
      }else{
        this.coin_type = {id:0,coin_unit:this.$store.state.init.coin_uint}
      }
      this.formData.coin_type = this.coin_type.id;
    },
    mounted () {
        this.loadWithdraw();
    },
    methods:{
        loadWithdraw : function () {
            if(this.lock){
                return false;
            }
            this.lock = true;
            this.loading = 1;
            this.$http.post('/api/app.wallet/wallet/withdrawlog',this.formData)
                .then(res => {
                    if(res.data.withdraw_log.length>0){
                        this.withdraw_lists =  this.withdraw_lists.concat_unk(res.data.withdraw_log,"id");
                        this.lock=false;
                    }
                    this.loading = 0;
                    this.formData.page++;
                })
                .catch(error=>{
                    this.loading = 0;
                })
        },
        onScrollBottom : function () {
            if(!this.lock){
                this.loadWithdraw();
            }
        },
        switchStatus : function (status) {
            if(status!==this.formData.status){
                this.$refs.scrollerEvent.reset({top: 0})
                this.withdraw_lists = [];
                this.lock = false;
                this.formData = {
                    page : 1,
                    status : status,
                    coin_type : this.coin_type.id
                }
                this.loadWithdraw();
            }

        }

    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '~vux/src/styles/close.less';
    @import '../../assets/css/variable.less';
    .withdrawlist-box{
        font-family: Arial, "Microsoft Yahei";
        .head{
            background: url(../../assets/images/withdeawlist.jpg) no-repeat top center;
            height: 3rem;
            line-height: 3rem;
            color: #fff;
            font-size: 0.9375rem;
            background-size: 100% 100%;
            padding: 0 0.625rem;
        }
        .withdeawlist-block{
            .item {
                background: #fff;
                padding: 0.625rem;
                line-height: 1.625rem;
                color: #888;
                margin-bottom: 0.25rem;
                font-family: Arial, "Microsoft Yahei";
                .title {
                    span{
                        color: #4c4c51;
                        font-size: 0.9375rem;
                    }
                }
                .get{
                    span{
                        font-size: 0.9375rem;
                        color: #6b94f8;
                    }
                }
                .item-type {
                    height: 1.25rem;
                    line-height: 1.25rem;
                    width: 3.25rem;
                    color: #fff;
                    font-size: 0.625rem;
                    text-align: center;
                    padding: 0 0.5rem;
                }
                .unaudited{
                    background: #14c3c0;
                }
                .finish{
                    background: #628df9;
                }
                .fail{
                    background: #f78383;
                }
                .failtip{
                    padding-top:.3rem;
                    color:#fc8c92;
                    line-height:1rem;font-size:.48rem;
                }
            }
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
