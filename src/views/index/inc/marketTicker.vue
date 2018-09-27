<template lang="pug">
	div.market-ticker
		div.market-search
			input(
				placeholder="搜索"
			)
			span.btn-search 搜索
		el-tabs(
			v-model="activeName"
			type="card"
			@tab-click="handleClick"
		)
			el-tab-pane(
				label="自选"
				name="custom"
			)
				div.market-ticker-table
					el-table(
						:data="customData"
						style="width: 100%"
						v-loading="tableLoading"
					)
						el-table-column(
							prop="market"
							label="市场"
						)
						el-table-column(
							prop="new_price"
							label="最新价"
						)
							template(
								slot-scope="scope"
							)
								span(
									style="color:#82980e;"
								) {{scope.row.new_price.bt}}
								span(
									style="color:#8d97a3;"
								) &nbsp;/&nbsp;{{scope.row.new_price.rmb | formatMoney}}
						el-table-column(
							prop="range"
							label="24h涨跌"
						)
							template(
								slot-scope="scope"
							)
								span(
									style="color:#82980e;"
								) {{scope.row.range}}
						el-table-column(
							prop="high_price"
							label="24h最高价"
						)
						el-table-column(
							prop="high_price"
							label="24h最低价"
						)
						el-table-column(
							prop="high_price"
							label="24h成交价"
						)
			el-tab-pane(
				label="BNB市场"
				name="BNB"
			)
				div.market-ticker-table
					el-table(
						:data="BNBData"
						style="width: 100%"
						v-loading="tableLoading"
					)
						el-table-column(
							prop="market"
							label="市场"
						)
						el-table-column(
							prop="new_price"
							label="最新价"
						)
						el-table-column(
							prop="range"
							label="24h涨跌"
						)
						el-table-column(
							prop="high_price"
							label="24h最高价"
						)
						el-table-column(
							prop="high_price"
							label="24h最低价"
						)
						el-table-column(
							prop="high_price"
							label="24h成交价"
						)
			el-tab-pane(
				label="BTC市场"
				name="BTC"
			)
				div.market-ticker-table
					el-table(
						:data="BTCData"
						style="width: 100%"
						v-loading="tableLoading"
					)
						el-table-column(
							prop="market"
							label="市场"
						)
						el-table-column(
							prop="new_price"
							label="最新价"
						)
						el-table-column(
							prop="range"
							label="24h涨跌"
						)
						el-table-column(
							prop="high_price"
							label="24h最高价"
						)
						el-table-column(
							prop="high_price"
							label="24h最低价"
						)
						el-table-column(
							prop="high_price"
							label="24h成交价"
						)
			el-tab-pane(
				label="ETH市场"
				name="ETH"
			)
				div.market-ticker-table
					el-table(
						:data="ETHData"
						style="width: 100%"
						v-loading="tableLoading"
					)
						el-table-column(
							prop="market"
							label="市场"
						)
						el-table-column(
							prop="new_price"
							label="最新价"
						)
							template(
								slot-scope="scope"
							)
								span {{new_price}}
						el-table-column(
							prop="range"
							label="24h涨跌"
						)
						el-table-column(
							prop="high_price"
							label="24h最高价"
						)
						el-table-column(
							prop="high_price"
							label="24h最低价"
						)
						el-table-column(
							prop="high_price"
							label="24h成交价"
						)
</template>
<script>
export default {
	data () {
		return {
			activeName: 'custom',
			tableLoading: false,
			customData: [{
        market: 'ETH/BTC',
        new_price: {
          bt: 0.1548745,
          rmb: 4415456
        },
        range: '5.06%',
        high_price: 0.345465667
      }, {
        market: 'ETH/BTC',
        new_price: {
          bt: 0.1548745,
          rmb: 4415456
        },
        range: '5.06%',
        high_price: 0.989089889
      }, {
        market: 'ETH/BTC',
        new_price: {
          bt: 0.1548745,
          rmb: 4415456
        },
        range: '5.06%',
        high_price: 0.3453546
      }, {
        market: 'ETH/BTC',
        new_price: {
          bt: 0.1548745,
          rmb: 4415456
        },
        range: '5.06%',
        high_price: 0.15487323
      }],
      BNBData: [],
      BTCData: [],
      ETHData: [],
		}
	},
	methods: {
		handleClick(tab, event) {
      this.tableLoading = true;
      console.log(tab.name);
      switch (tab.name) {
        case 'custom':
          setTimeout(() => {
            this.customData = [{
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.3245345
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.5675676
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.768789
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.6756767
            }];
            this.tableLoading = false;
          }, 500);
          break;
        case 'BNB':
          setTimeout(() => {
            this.BNBData = [{
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.3245345
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.5675676
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.7897989
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.2343544
            }];
            this.tableLoading = false;
          }, 500);
          break;
        case 'BTC':
          console.log(123);
          setTimeout(() => {
            this.BTCData = [{
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.567567
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.243234
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.6787798
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.23435646
            }];
            this.tableLoading = false;
          }, 500);
          break;
        case 'ETHData':
          setTimeout(() => {
            this.BTCData = [{
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.567567
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.243234
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.6787798
            }, {
              market: 'ETH/BTC',
              new_price: {
                bt: 0.1548745,
                rmb: 4415456
              },
              range: '5.06%',
              high_price: 0.23435646
            }];
            this.tableLoading = false;
          }, 500);
          break;
      
        default:
          break;
      }
    }
	}
}
</script>
<style lang="less">
	@import '../../../assets/css/variable.less';
	.market-ticker {
		position: relative;
		.market-search {
			position: absolute;
			top: 0;
			right: 0;
			z-index: 999;
			input {
				width: 280px;
				height: 40px;
				border: 1px solid @bdcolor;
				padding: 0 10px;
			}
			.btn-search {
				position: absolute;
				top: 0;
				right: 10px;
				widows: 40px;
				height: 40px;
				line-height: 40px;
				display: block;
				cursor: pointer;
			}
		}
		.market-ticker-table {
			border-left: 1px solid @bdcolor;
			border-right: 1px solid @bdcolor;
			.el-table {
				font-size: 16px;
				color: #222;
			}
		}
		.el-tabs__header {
			margin: 0;
		}
		.el-tabs--card>.el-tabs__header .el-tabs__nav {
			border: 0;
			height: 50px;
		}
		.el-tabs--card>.el-tabs__header .el-tabs__item {
			background-color: #fdfdfd;
			margin-right: 5px;
			border: 1px solid @bdcolor;
			border-bottom: 0;
			border-top-left-radius: 3px;
			border-top-right-radius: 3px;
			display: block;
			float: left;
			margin-top: 10px;
			padding: 0 40px;
			font-size: 16px;
			color: #8d97a3;
		}
		.el-tabs--card>.el-tabs__header .el-tabs__item.is-active {
			height: 50px;
			line-height: 50px;
			margin-top: 0;
			color: #233853;
			font-size: 18px;
		}
		.el-table th>.cell {
			font-size: 16px;
			color: #8d97a3;
			font-weight: 400;
		}
		.el-table .cell {
			padding: 0 40px;
		}
	}
</style>

