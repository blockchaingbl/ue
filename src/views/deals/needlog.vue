<template lang="html">
<div class="record-box">
    <div class="head">
        需求中的{{$store.state.init.coin_uint}}
    </div>
    <scroller @on-scroll-bottom="onScrollBottom"  lock-x ref="scrollerEvent">
        <div class="record-block" v-if="otc_list.length>0">
            <div class="item flex-box" v-for="otc in otc_list">
                <div class="item-text flex-1">
                    <div class="title">数量：<span>{{otc.vc_less_amount}}</span></div>
                    <div class="money flex-box">
                        <div class="get flex-1">行情：<span>&yen;{{(otc.vc_less_amount * otc.vc_unit_price).toFixed(2)}}</span></div>
                        <div class="price flex-1">单价：&yen; {{otc.vc_unit_price}}</div>
                    </div>
                    <div class="account-number">上架时间：{{otc.create_time}}</div>
                </div>
                <div class="item-btn appeal" @click.stop="down(otc.id)">下架</div>
            </div>
            <load-more tip="正在加载 . . ." v-if="loading"></load-more>
            <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
            <div class="loadmore">
            </div>
        </div>
        <nodata  v-else :datatip="'暂无数据'"></nodata>
        <div class="" style="clear:both;"></div>
    </scroller>
</div>
</template>
<script>
    import { Scroller , LoadMore , Divider} from 'vux';
    import Nodata from "@/components/nodata";
export default {
    components: {
        Scroller,
        LoadMore,
        Divider,
        Nodata
    },
    data () {
        return {
            formData:{page:1,myself:true,type:1},
            lock:false,
            loading:false,
            otc_list:[],
        }
    },
    mounted () {
        this.loadOtcLists();
    },
    methods:{
        loadOtcLists(){
            if(this.lock){
                return false;
            }
            this.lock = true;
            this.loading = true;
            this.$http.post('/api/app.otc/deals',this.formData).then(res => {
                if(res.data.otc_list.length>0){
                    res.data.otc_list.map(val=>{val.total_price = val.vc_unit_price * val.vc_less_amount});
                    this.otc_list = this.otc_list.concat_unk(res.data.otc_list,"id");
                    this.lock =false;
                }
                this.loading = false;
                this.formData.page++;
            }).catch(err => {
                this.loading = false;
            });
        },
        onScrollBottom(){
            this.loadOtcLists();
        },
        down(id){
            const _this = this
            this.$vux.confirm.show({
                content: '是否确认下架',
                onConfirm () {
                    let formData = {
                        otc_id:id,
                    };
                    _this.$http.post('/api/app.otc/deals/down',formData)
                        .then(res=>{
                            _this.$vux.toast.text('下架成功');
                            setTimeout(function () {
                                _this.reload();
                            },2000)
                        })
                        .catch(error=>{
                            if (error.errcode) {
                                _this.$vux.toast.text(error.message);
                            }
                        })
                }
            })
        },
        reload(){
            this.lock = false;
            this.formData = {page:1,myself:true,type:1};
            this.otc_list = [];
            this.loadOtcLists();
        }
    }
}
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '~vux/src/styles/close.less';
    @import '../../assets/css/variable.less';
    .record-box{
        font-family: Arial, "Microsoft Yahei";
        font-size: @fs-middle;
        .head{
            background: url(../../assets/images/withdeawlist.jpg) no-repeat top center;
            height: 3rem;
            line-height: 3rem;
            color: #fff;
            font-size: 0.9375rem;
            background-size: 100% 100%;
            padding: 0 0.625rem;
        }
        .record-block{
            .item {
                background: #fff;
                padding:1.25rem 0.625rem 0.625rem;
                line-height: 1.625rem;
                color: #888;
                margin-top: 0.25rem;
                font-family: Arial, "Microsoft Yahei";
                position: relative;
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
                    font-size: @fs-smaller;
                    text-align: center;
                    padding: 0 0.5rem;
                    position: absolute;
                    top: 0;
                    left: 0.625rem;
                }
                .item-btn{
                    height: 1.5rem;
                    line-height: 1.5rem;
                    width: 3.5rem;
                    color: #6b94f8;
                    font-size: @fs-small;
                    border-radius: 0.75rem;
                    text-align: center;
                    padding: 0 0.5rem;
                    border: 1px solid #6b94f8;
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
                .send-coin{
                    background: #12e0de
                }
                .paid{
                    background: #eb5a5a;
                }
            }
        }
        .btn-box{
            position: absolute;
            left: 0;
            bottom: 0;
            right: 0;
        }
        .record-btn{
            font-size: 0.9375rem;
            height: 2.4375rem;
            line-height: 2.4375rem;
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
