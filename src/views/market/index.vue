<template lang="html">
<scroller @on-scroll-bottom="onScrollBottom" height="100%" lock-x ref="scrollerEvent" style="background:#2e3d6c;">
  <div class="market-index">
    <flexbox :gutter="0" class="main_head vux-1px-b">
      <flexbox-item :span="4"><div class="market_box">资产名称</div></flexbox-item>
      <flexbox-item :span="4" class="t_r"><div class="market_box">最新价</div></flexbox-item>
        <flexbox-item :span="4" class="t_r"><div class="market_box">涨跌幅</div></flexbox-item>
    </flexbox>
    
    <div>
        <flexbox :gutter="0" class="main_item vux-1px-b" v-for="(x,index) in market_lists" :key="index" @click.native="turnDetail(x)" v-if="market_lists.length>0">
            <flexbox-item :span="4">
                <div class="market_box">{{x.name}}</div>
                <div class="market_box color_white">{{x.coin_unit}}</div>
            </flexbox-item>
            <flexbox-item :span="4" class="t_r">
                <div class="market_box">￥{{x.price}}</div>
                <div class="market_box color_white">
                    ${{x.price_usd}}
                </div>
            </flexbox-item>
            <flexbox-item :span="4" class="t_r">
                <div class="market_box">
                    <x-button style="background:#d3483e;color:#fff;width:4.875rem;padding:0;" type="warn" :mini='true'	v-if="x.trend==0">-{{x.rate_abs}}%</x-button>
                    <x-button :mini='true' style="background:#79d576;color:#fff;width:4.875rem;padding:0;" v-else>+{{x.rate_abs}}%</x-button>
                </div>
            </flexbox-item>
        </flexbox>
    </div>
    <div v-transfer-dom>
        <loading :show="loadings"></loading>
    </div>
  </div>
</scroller>
</template>
<script>
import {  LoadMore , Divider , Flexbox , FlexboxItem, Loading, Scroller, TransferDomDirective as TransferDom} from 'vux';
export default {
    directives: {
        TransferDom
    },
    components: {
        Flexbox,
        FlexboxItem,
        LoadMore,
        Divider,
        Scroller,
        Loading,
    },
    data () {
        return {
            formData:{page:1},
            market_lists : [],
            lock : false,
            loadings:true
        }
    },
    mounted () {
       this.getMarket();
    },
    methods:{
      getMarket(){
        if(this.lock){
            return false;
        }
        this.lock = true;
        this.loading = 1;
        this.$http.post('api/app.market/market',{}).then(res => {
          if(res.data.market_lists.length>0){
              this.market_lists =  this.market_lists.concat_unk(res.data.market_lists,"id");
              this.lock=false;
              console.log(res);
              this.loadings=false;
          }
          this.loading = 0;
          this.formData.page++;
        })
        .catch((err) => {
          this.loading = 0;
        })
      },
      onScrollBottom : function () {
          if(!this.lock){
              this.getMarket();
          }
      },
      turnDetail(x){
        this.$router.push({name:'marketDetail',params:{market:x}})
      }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .market-index{
        min-height: 100%;
        width: 100%;
        background:#2e3d6c;
        .main_head{
            background-color:#2e3d6c;
            height:2.25rem;
            padding:0 0.9375rem;
            font-size: 0.75rem;
        }
        .market_box{
            color: #7988b7;
            line-height: 1.375rem;
            font-size: 0.75rem;
        }
        .main_item {
            padding: 0.625rem 0.9375rem;
        }
        .color_white{
            font-size: 1.125rem;
            color: #fff;
            line-height: 1.625rem;
        }
        .t_r{
            text-align: right;
        }
    }
</style>
