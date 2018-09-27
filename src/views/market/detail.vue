<template lang="html">
<div class="market-detail" style="">
    <div class="market-detail-con">
        <flexbox :gutter="0" class="main_head vux-1px-b">
            <flexbox-item :span="4">
                <div class="market_box">{{x.name}}</div>
                <div class="market_box color_white">{{x.coin_unit}}</div>
            </flexbox-item>
            <flexbox-item :span="4" class="t_r">
                <div class="market_box">￥{{x.price}}</div>
                <div class="market_box color_white">${{x.price_usd}}</div>
            </flexbox-item>
            <flexbox-item :span="4" class="t_r">
                <div class="market_box">
                    <x-button style="background:#d3483e;color:#fff;width:4.875rem;padding:0;" type="warn" :mini='true'	v-if="x.trend==0">-{{x.rate_abs}}%</x-button>
                    <x-button style="background:#79d576;color:#fff;width:4.875rem;padding:0;" :mini='true' v-else>+{{x.rate_abs}}%</x-button>
                </div>
            </flexbox-item>
        </flexbox>
        <flexbox :gutter="0" class="main_head">
            <flexbox-item :span="3">
                <div class="market_box_c">开盘</div>
                <div class="market_box_c color_white_c">$ {{x.open}}</div>
            </flexbox-item>
            <flexbox-item :span="3">
                <div class="market_box_c">最高</div>
                <div class="market_box_c color_white_c">$ {{x.high}}</div>
            </flexbox-item>
            <flexbox-item :span="3">
                <div class="market_box_c">最低</div>
                <div class="market_box_c color_white_c">$ {{x.low}}</div>
            </flexbox-item>
            <flexbox-item :span="3">
                <div class="market_box_c">成交量</div>
                <div class="market_box_c color_white_c">{{x.volume}}</div>
            </flexbox-item>
        </flexbox>
        <div class="tab">
            <button-tab v-model="tab_index">
            <button-tab-item @on-item-click="invoke">5分</button-tab-item>
            <button-tab-item selected @on-item-click="invoke">2时</button-tab-item>
            <button-tab-item @on-item-click="invoke">4时</button-tab-item>
            <button-tab-item @on-item-click="invoke">天</button-tab-item>
            </button-tab>
        </div>
    </div>
    <div id="myChart" :style="{width: '100%', height: '400px', background: '#2e3d6c'}"></div>



</div>
</template>
<script>
import {  ButtonTab, ButtonTabItem , Flexbox , FlexboxItem } from 'vux';
import Echarts from 'echarts';
export default {
    components: {
        Flexbox,
        FlexboxItem,
        ButtonTab,
        ButtonTabItem
    },
    data () {
        return {
          x:{},
          tab_index : 1,
          formChose : ['min','hour2','hour4','day'],
          chart_date:[],
          chart_price:[],
          chart_data:[],
          volumes:[]
        }
    },
    mounted () {
      this.x = this.$route.params.market
      this.invoke();
    },
    methods:{
      invoke(){
        let formData = {api_param:this.x.api_param,divide:this.formChose[this.tab_index]};
        console.log(formData)
        this.$http.post('api/app.market/market/detail',formData).then(res => {
          console.log(res);
          this.chart_date = res.data.date;
          this.chart_price = res.data.price;
          this.drawLine();
        })
      },
      drawLine(){
          // 基于准备好的dom，初始化echarts实例
          let myChart = Echarts.init(document.getElementById('myChart'));
          // 绘制图表
          myChart.setOption({
              title: {
                  text: '行情走势',
                  textStyle: {
                      color: '#7988b7',
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
                          color: '#7988b7'
                      }
                  },
                  data: this.chart_date
              },
              yAxis: {
                  axisLine: {
                      lineStyle: {
                          color: '#7988b7'
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
      }
    }
}
</script>

<style lang="less">
    @import '../../assets/css/variable.less';
    .market-detail{ 
        min-height:100%; 
        width:100%; 
        background:#2e3d6c;
        .market-detail-con{
            padding: 0 0.9375rem 0.625rem;
        }
        .main_head{
            background-color:#2e3d6c;
            padding:0.625rem 0;
            font-size: 0.75rem;
            -webkit-box-align: end;
            -ms-flex-align: flex-end;
            align-items: flex-end;
        }
        .market_box{
            color: #7988b7;
            line-height: 1.375rem;
            font-size: 0.75rem;
        }
        .color_white{
            font-size: 1.125rem;
            color: #fff;
            line-height: 1.625rem;
        }
        .market_box_c {
            font-size: 0.75rem;
            color: #7988b7;
            text-align: left;
            line-height: 1.5rem;
        }
        .color_white_c{
            color: #fff;
        }
        .t_r{
            text-align: right;
        }
    }
</style>
