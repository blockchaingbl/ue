<template lang="html">
    <div class="withdraw-box">
        <div class="head flex-box">
            <div class="head-text flex-1">可转出{{show_coin.coin_type.coin_unit}}：<span>{{asset}}</span></div>
        </div>
        <div class="withdraw">
            <div class="select-box">
                <group  label-width="5em">
                    <popup-radio title="资产类型" :options="coin_info" v-model="chose_coin" @on-hide="hide_change">
                        <p slot="popup-header" class="vux-1px-b grant-coin-slot">资产类型</p>
                    </popup-radio>
                </group>
            </div>
            <div class="title">申请令牌转出</div>
            <div class="input-box flex-box vux-1px-b">
                <input type="text" v-model="withdraw_money"  :min="min_withdraw" class="flex-1">
                <span>{{coin_uint}}</span>
            </div>
            <div class="item-block">
                <div class="item flex-box">
                    <div class="item-title flex-1">预计所需燃烧：{{ServiceCharge}} {{coin_uint}}</div>
                    <div class="item-decs">最低转出{{min_withdraw}}{{coin_uint}}</div>
                </div>
                <div class="item flex-box">
                    <div class="item-title flex-1">预计到账数额：{{real_withdraw}} {{coin_uint}}</div>
                </div>
            </div>
            <div class="select-box">
                <group  label-width="5em">
                    <popup-picker :title="title1" :data="address" v-model="value1" show-name :columns="1"></popup-picker>
                </group>
            </div>
        </div>
        <div class="hasLogin" v-if="hasLogin==0">
            <box gap="40px 35px">
                <x-button type="primary" style="border-radius:99px;" class='withdraw-btn' v-on:click.native="open_pup()">确认转至钱包</x-button>
            </box>
        </div>
        <div class="pupBox">
            <div class="pupBox-content">
                <div class="title">申请转出成功</div>
                <span class="vux-close" v-on:click="colse_pup()"></span>
                <div class="pup-block">
                    <div class="item">转出数额：<span>{{withdraw_money}}</span></div>
                    <div class="item">燃烧：{{ServiceCharge}}</div>
                    <div class="item">到账数额：<span>{{real_withdraw}}</span></div>
                </div>
                <box gap="2.1875rem 1.75rem 1.375rem">
                    <x-button type="primary" style="border-radius:99px;" class='pup-btn' link="/user/withdrawlist">去查看进度</x-button>
                </box>
            </div>
        </div>
    </div>
</template>
<script>
  import { PopupPicker,PopupRadio } from 'vux'

  export default {
    components: {
      PopupPicker,
      PopupRadio
    },
    data () {
      return {
        hasLogin:0,
        coin_uint:'',
        min_withdraw:0,
        withdraw_rate:0,
        withdraw_money:'0.00',
        withdraw_result:'',
        limit_rate:0,
        vc_total:0,
        vc_normal:0,
        title1:'转出地址',
        address:[{name:'',value:''}],
        value1:[],
        coin_type:null,
        click_lock : false,
        chose_coin:0,
        asset:'',
        coin_info:[],
        show_coin:{coin_type:{coin_uint:'GBL'}}
      }
    },
    computed:{
      ServiceCharge :function () {
        if(this.withdraw_money.length<=0){
          return  0;
        }
        return   ((parseFloat(this.withdraw_money)* 100000000000 - parseFloat(this.real_withdraw) * 100000000000) / 100000000000).toFixed(8) || 0;
      },
      real_withdraw:function () {
        if(this.withdraw_money.length<=0){
          return  0;
        }
        return  ((parseFloat(this.withdraw_money) * ( 1.00 - parseFloat(this.withdraw_rate))) * 1000000000 / 1000000000).toFixed(8) || 0;
      }
    },
    mounted () {
      this.getWallets();
      this.getasset();
    },
    methods:{
      getasset(){
        this.$http.post('/api/app.tokenotc/deals/get_open_coin',{}).then(res => {
          let data = res.data.lists;
          for (let i=0; i<data.length;i++){
            this.coin_info.push({key:i,value:data[i].coin_type.coin_unit})
          }
          this.chose_coin = 0;
          this.coin_info_detail = data;
          this.vc_token = parseFloat(data[0].vc_token);
          this.hide_change()
          this.loading =false;
        }).catch(err=>{
        })
      },
      open_pup(){
        if(this.click_lock)
        {
          return false;
        }
        this.click_lock = true;
        var _this = this
        let formData = {
          amount:this.withdraw_money,
          coin_type:this.coin_type,
          address:this.value1[0]
        }
        this.$http.post('/api/app.wallet/wallet/tokenwithdraw',formData).then(res=>{
          _this.$vux.toast.text(res.message);
          $('.pupBox').addClass('isopen');
          this.$router.push({path:'/user/withdrawlist'})
        }).catch(err=>{
          this.click_lock = false;
          if (err.errcode) {
            _this.$vux.toast.text(err.message);
          }
        })
      },
      hide_change(){
        let index = this.chose_coin;
        this.show_coin = this.coin_info_detail[index];
        this.asset = this.show_coin.vc_token;
        this.coin_uint =this.show_coin.coin_type.coin_uint;
        this.withdraw_rate = this.show_coin.coin_type.withdraw_rate;
        this.min_withdraw = this.show_coin.coin_type.min_withdraw;
        this.withdraw_money = this.min_withdraw;
        this.coin_type = this.show_coin.coin_type.id;
      },
      colse_pup(){
        $('.pupBox').removeClass('isopen');
        this.$router.push({path:'/user/withdrawlist'})
      },
      getWallets(){
        this.$http.post('/api/app.wallet/wallets',{}).then(res => {
          let address = res.data.wallets;
          let arr = [];
          $('.input-box').find('input').focus();
          for (var x in address){
            arr.push({
              'name':address[x].name,
              'value':address[x].address,
              'parent': 0})
          }
          this.address= [arr];
          this.value1 = [arr[0].value];
        }).catch(err => {
          if (err.errcode) {
            this.$vux.toast.text(err.message);
          }
        });
      }
    }
  }
</script>

<style lang="less">
    @import '~vux/src/styles/1px.less';
    @import '~vux/src/styles/close.less';
    @import '../../assets/css/variable.less';
    .withdraw-box{
        .withdraw {
            background: #fff;
            padding: 0.625rem;
            .title {
                font-size: 0.875rem;
                color: #4c4c51;
                line-height: 2rem;
            }
            .input-box {
                align-items: flex-end;
                padding: 0.5rem 0;
                input {
                    border: none;
                    height: 2rem;
                    font-size: 1.875rem;
                    font-family: arial;
                    color: #4c4c51;
                    font-weight: bold;
                    width: 50%;
                }
                span {
                    font-size: 0.75rem;
                    margin-top: 0.75rem;
                }
            }
            .select-box {
                align-items: flex-end;
                .weui-cells{
                    font-size: 0.875rem;
                    color: #4c4c51;
                    line-height: 2rem;
                    .weui-cell{
                        padding: 0.375rem 0;
                    }
                }
                .vux-no-group-title {
                    margin-top: 0;
                }
            }
            .item-block{
                padding-top: 0.375rem;
                .item{
                    line-height: 1.875rem;
                    font-size: 0.75rem;
                    font-family: Arial, "Microsoft Yahei";
                    color: #888;
                    .item-decs{
                        color: #c9564c;
                    }
                    .item-title{
                        span{
                            font-size: 1.0625rem;
                            color: #4c4c51;
                        }
                    }
                }
            }
        }
        .withdraw-btn{
            background-color: #3f72ed;
            font-size: 0.9375rem;
            height: 2.4375rem;
            line-height: 2.4375rem;
        }
        .noLogin{
            .withdraw-btn{
                background-color: #c4c4cf;
            }
        }
        .tis{
            text-align: center;
            color: #888;
            a{
                color:#6b94f8;
            }
        }
        .pupBox {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            transition: all 0.3s;
            background: rgba(0,0,0,0);
            z-index: 99;

            .pupBox-content {
                transition: all 0.3s;
                opacity: 0;
                position: absolute;
                top: 16%;
                background: #fff;
                z-index: 100;
                left: 2.5rem;
                right: 2.5rem;
                border-radius: 8px;
                .title {
                    font-size: 0.875rem;
                    text-align: center;
                    line-height: 2rem;
                    color: #4c4c51;
                    margin-bottom: 1rem;
                }
                .vux-close {
                    position: absolute;
                    top: 0.375rem;
                    right: 0.375rem;
                    width: 20px;
                    height: 20px;
                    color: #4c4c51;
                }
                .vux-close:before, .vux-close:after {
                    left: 4px;
                    top: 50%;
                    width: 12px;
                }
                .pup-block{
                    padding: 0 0.9375rem;
                    .item{
                        line-height: 1.875rem;
                        font-size: 0.75rem;
                        color: #888;
                        font-family: Arial, "Microsoft Yahei";
                        span{
                            color: #38485f;
                        }
                    }
                }
                .pup-btn{
                    background-color: #fc8c92;
                    height: 2.4375rem;
                    line-height: 2.4375rem;
                    font-size: 0.9375rem;
                }
            }
        }
        .isopen{
            background: rgba(0,0,0,0.5);
            pointer-events: auto;
            .pupBox-content {
                opacity: 1;
            }
        }
        .head{
            height: 3rem;
            background: #fff;
            padding: 0 0.625rem;
            color: #4c4c51;
            .iconfont{
                font-size: @fs-small;
                color: #6b94f8;
                margin-right: 0.25rem;
                margin-top: 2px;
            }
            .head-text{
                font-size: @fs-small;
                span{
                    font-size: 1.0625rem;
                    font-weight: bold;
                    color: #6b94f8;
                }
            }
        }
    }
</style>
