<template lang="html">
<div class="page-candy-index">
    <div class="candy-block" v-if="!loadings">
        <div class="candy-block-box">
            <div class="cand-item flex-box" v-for="(item,index) in sugars_list">
                <div class="candy-icon">
                    <img :src="item.coin_info.icon" alt="">
                </div>
                <div class="item-text flex-1 flex-box vux-1px-b">
                    <div class="item-info flex-1">
                        <div class="candy-title">{{item.coin_info.coin_unit}}: {{item.copys_amount}}</div>
                        <div class="candy-decs"><div>锁仓{{item.lock_day}}天</div>
                            <div v-if="formData.type==0">份数： {{item.copys_less}}/{{item.copys}}</div>
                            <div v-else>来自： {{item.from_user.username}}</div>
                        </div>
                    </div>
                    <div class="item-btn-box" @click="receive(item.id,item)">
                        <div class="item-btn item_btnde" v-if="item.have==1&&formData.type==0">已领取</div>
                        <div class="item-btn-decs" v-if="item.have==1">{{item.desc}}</div>
                        <div class="item-btn" v-else="item.have==0">领取</div>
                    </div>
                </div>
            </div>
        </div>
        <Scroller v-if="sugars_list.length>0" v-on:load="loadOtcLists" :loading="loading" :container="'.candy-block-box'" ></Scroller>
        <nodata :datatip="'暂无数据'" v-else></nodata>
    </div>
    <div v-transfer-dom>
        <loading :show="loadings"></loading>
    </div>
</div>
</template>
<script>
import { Tab, TabItem, Loading, TransferDomDirective as TransferDom } from "vux";
import Nodata from "@/components/nodata";
import Scroller from "@/components/scroller";
export default {
    directives: {
        TransferDom
    },
    components: {
        Tab,
        TabItem,
        "nodata":Nodata,
        "Scroller":Scroller,
        Loading
    },
    data() {
        return {
            formData: { page: 1, type: 0 },
            sugars_list:[],
            sugar_id:'',
            loading:false,
            loadings:true
        };
    },
    mounted() {
        this.getSugars();
    },
    methods: {
        getSugars(){
            
            this.$http.post('/api/app.user/sugar',this.formData)
                .then(res=>{
                    if(res.data.sugars.length>0){
                        this.sugars_list=res.data.sugars;
                    }
                    this.formData.page++;
                    this.loadings = false;
                })
                .catch(error=>{
                    this.$vux.toast.text(error.message);
                })
        },
        loadOtcLists(){
            if (this.loading) {
                //正在加载中
            }else if(this.formData.page!=null){
                //加载完毕
                this.loading=true;
                this.$http.post('/api/app.user/sugar',this.formData).then(res => {
                    if(res.data==null){
                        this.formData.page=null;
                        this.loading=false;
                    }else if (res.data.sugars.length<1) {
                        this.formData.page=null;
                        this.loading=false;
                    }
                    else{
                        this.sugars_list=this.sugars_list.concat_unk(res.data.sugars,"id");
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
        switchType(type){
            if(type!=this.formData.type){
               this.reload(type);
            }
        },
        reload(type){
            this.formData = {type:type,page:1} ;
            this.loadings = true;
            this.sugars_list=[];
            this.getSugars();
        },
        receive(sugar_id,item){
            this.$http.post('/api/app.user/sugar/receive',{
                sugar_id:sugar_id
            })
            .then(res=>{
                item.have=1;
                if(res.data.desc.length>0){
                    item.desc=res.data.desc;
                    item.copys_less--;
                }else{
                    item.desc='已可用';
                }
                this.$vux.toast.text(res.message);
            })
            .catch(error=>{
                console.log(error);
                this.$vux.toast.text(error.message);
            })
        }
    }
};
</script>

<style lang="less">
@import "../../assets/css/variable.less";
.page-candy-index {
    position: relative;
    height: 100%;
    background: #fff;
    .candy-tab {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
    }
    .candy-block {
        overflow-x: hidden;
        overflow-y: scroll;
        height: 100%;
        width: 100%;
        .cand-item {
            padding-left: 0.75rem;
            .candy-icon {
                width: 2.375rem;
                height: 2.375rem;
                margin-right: 0.625rem;
                img{
                    width: 100%;
                    height: 100%;
                    display: block;
                }
            }
            .item-text {
                padding: 0.625rem 0.9375rem 0.625rem 0;
            }
            .item-info {
                color: #888;
                font-size: 0.6875rem;
                line-height: 0.9375rem;
                .candy-title {
                    font-size: 1rem;
                    font-weight: bold;
                    color: #363840;
                    line-height: 1.625rem;
                }
            }
            .item_box_btnde{
                display: none;
            }
            .item-btn {
                font-size: 0.6875rem;
                width: 4.125rem;
                height: 1.375rem;
                line-height: 1.375rem;
                text-align: center;
                border-radius: 0.6875rem;
                background: #3f72ed;
                color: #fff;
            }
            .item-btn-decs {
                margin-top: 0.25rem;
                font-size: 0.6875rem;
                text-align: center;
                line-height: 0.9375rem;
                color: #888;
            }
            .item-btn.item_btnde{
                background: #c5ccd7;
            }
        }
  }
}
</style>
