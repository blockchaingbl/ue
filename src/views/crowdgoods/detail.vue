<template lang="html">
<div class="crowd-detail" v-show="!loadings">
    <x-header :left-options="{showBack: true,backText:''}"  style="z-index:1;">{{title}}</x-header>
    <div class="content">
        <div class="banner">
            <img :src="image" alt="" style="height: 200px;">
        </div>
        <div class="crowd-info">
            <div class="info-box">
                <div class="info-top vux-1px-b">
                    <div class="crowd-title">{{title}}</div>
                    <div class="info-decs">{{brief}}</div>
                    <div class="info-time"> <img  class="info-img" src="@/assets/images/time_icon.png"><span class="item-time-word">{{start_time}}~{{end_time}}</span></div>
                </div>
            </div>
        </div>
        <card>
            <div slot="content" class="card-demo-flex card-demo-content01">
                <div class="vux-1px-r">
                    <span>{{amount-less_amount}}</span>
                    <br/>
                    已参与人次
                </div>
                <div class="vux-1px-r">
                    <span>{{amount}}</span>
                    <br/>
                    总需人次
                </div>
                <div class="vux-1px-r">
                    <span>{{less_amount}}</span>
                    <br/>
                    剩余人次
                </div>
                <div class="vux-1px-r">
                    <span>{{user_limit}}</span>
                    <br/>
                    每人最大份额
                </div>
            </div>
        </card>
        <div class="crowd-decs">
            <div class="decs-title">奖品介绍</div>
            <div class="decs-txt" v-html="content"></div>
        </div>

        <!--<group class="crowd_number">-->
            <!--<cell :title="rate">-->
                <!--<inline-x-number  width="50px" button-style="round" v-model="order_number"></inline-x-number>-->
            <!--</cell>-->
        <!--</group>-->
        <group class="crowd_number">
            <x-number :title="rate" v-model="order_number"  :min="0"   :max="less_amount" button-style="round"></x-number>
        </group>
    </div>
    <box gap="0" class="crowd-btn-box flex-box">
        <!-- 1 : 正在进行 2即将开始 3 已经结束 -->
        <x-button v-if="time_status==1&&less_amount>0&&status==0" type="primary" style="border-radius:0" class='crowd-btn crowd-start flex-box' @click.native="confirm_pay">
            <span class="">立即参与</span>
            <span class="crowd-btn-decs">{{(price*order_number).toFixed(5)}} {{$store.state.init.coin_uint}}</span>
        </x-button>
        <x-button v-if="time_status==2" type="warn" style="border-radius:0;" class='crowd-btn flex-box' @click.native="showawait">
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">{{countDown.day}}天{{countDown.hour}}时{{countDown.minute}}分{{countDown.second}}秒后开始</span>
        </x-button>
        <x-button v-if="status==1 && luck_name!=''" type="warn" style="border-radius:0;" class='crowd-btn flex-box'>
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">恭喜用户{{luck_name}}中奖</span>
        </x-button>
        <x-button v-else-if="status==1" type="warn" style="border-radius:0;" class='crowd-btn flex-box'>
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">已开奖</span>
        </x-button>
        <x-button v-if="status==-1" type="warn" style="border-radius:0;" class='crowd-btn flex-box'>
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">成团失败</span>
        </x-button>
        <x-button v-if="status==0&&time_status==1&&less_amount==0&&full==0" type="warn" style="border-radius:0;" class='crowd-btn flex-box'>
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">即将成团</span>
        </x-button>
        <x-button v-if="status==0&&full==1" type="warn" style="border-radius:0;" class='crowd-btn flex-box' @click.native="showEnd">
            <span class="crowd-btn-decs" style="color:#fff;font-size:0.9rem;">{{countDown.day}}天{{countDown.hour}}时{{countDown.minute}}分{{countDown.second}}秒后开奖</span>
        </x-button>
    </box>

    <div v-transfer-dom>
        <loading :show="loadings"></loading>
    </div>
</div>
</template>
<script>
import { Loading, TransferDomDirective as TransferDom, Card ,XNumber } from 'vux';
import router from '@/router';
export default {
  directives: {
      TransferDom
  },
  components: {
      TransferDom,
      Loading,
      Card,
      XNumber
  },
    data() {
        return {
            buy_show:false,
            formData:{crowd_id:router.currentRoute.params['crowd_id']},
            image:'',
            title:'',
            brief:'',
            start_time:'',
            crowd_id:'',
            end_time:'',
            amount:'',
            content:'',
            price:'',
            loadings:true,
            input_num:"",
            detail_id:'',
            lottery_time:"",
            confirm_password:'',
            status:0,
            start_timestamp:'', // 开始时间
            timestamp:'',	// 服务器当前时间
            countDown:{ // 倒计时
							day:"-",
							hour:"-",
							minute:"-",
							second:"-"
						},
            startCountDown:'',
            lock:false,
            show_coin_type:'',
            time_status:0,
            user_limit:0,
            order_number:1,
            less_amount:0,
            full:0,
            luck_name:''
        };
    },
    mounted() {
        this.getDetail();
		},
		destroyed(){
			clearTimeout(this.startCountDown)
		},
    methods: {
        close_buy(){
            this.buy_show=false;
        },
        showawait(){
            return false
        },
        showEnd(){
            return false
        },
        getDetail(){
            const _this = this;
            // 清除倒计时
            clearTimeout(this.startCountDown)
            this.$http.post('/api/app.crowdfund/crowdgoods/detail',this.formData).then(res=>{
                this.image=res.data.crowdFund.image;
                this.title=res.data.crowdFund.title;
                this.brief=res.data.crowdFund.brief;
                this.full = res.data.crowdFund.full;
                this.less_amount = res.data.crowdFund.less_amount
                this.start_time=res.data.crowdFund.start_time;
                this.content=res.data.crowdFund.content;
                this.time_status = res.data.crowdFund.time_status
                this.price=res.data.crowdFund.price;
                this.loadings=false;
                this.end_time = res.data.crowdFund.end_time;
                this.status = res.data.crowdFund.status;
                this.lottery_time = res.data.crowdFund.lottery_time
                this.amount = res.data.crowdFund.amount;
                this.user_limit = res.data.crowdFund.user_limit
                this.luck_name =res.data.luck_name
                // 当前时间
                this.timestamp = res.data.crowdFund.timestamp
                // 开始时间
                this.start_timestamp = res.data.crowdFund.start_timestamp
                this.lottery_timestamp =  res.data.crowdFund.lottery_timestamp
                let diff_value = 0;
                if(this.time_status==2)
                {
                     diff_value = this.start_timestamp - this.timestamp
                }else if(this.full==1 && this.status==0)
                {
                     diff_value = this.lottery_timestamp - this.timestamp
                }
                console.log(diff_value)
                // 开始时间大于当前时间
								if(diff_value>0){
									this.timestampChangeDate(diff_value)
								}
            })
            .catch(error=>{
                 this.loadings=false;
                _this.$vux.toast.text(error.message);
                setTimeout(function () {
                    _this.$router.back(-1);
                },2000)
            })

				},
				/**
				 * ns :秒
				 */
        timestampChangeDate(ns){
					let time = ns
					let day = Math.floor(time/(60*60*24))
					let hour = Math.floor(time/(60*60)%24)
					let minute = Math.floor(time/(60)%60)
					let second = Math.floor(time%60)

					this.countDown = {
						day:day>=10?day:'0'+day,
						hour:hour>=10?hour:'0'+hour,
						minute:minute>=10?minute:'0'+minute,
						second:second>=10?second:'0'+second
					}
					time = time - 1
					// 进入倒计时
					if(time>=0){
						this.startCountDown = setTimeout(()=>{
							this.timestampChangeDate(time)
						},1000)
					}else{
						// 清除倒计时 改变状态
						clearTimeout(this.startCountDown)
						this.status = 1
					}
        },
        confirm_pay(){
          let _this = this;
          this.$vux.confirm.show({
            title: '购买后将锁定名额,请在10分钟内发起支付,10分钟内未付款将会取消锁定资格,每日取消超过3次,将无法参与!',
            onCancel(){},
            onConfirm(){
              _this.loadings=true;
              _this.detail_id = _this.formData.crowd_id;
              _this.$http.post('/api/app.crowdfund/crowdgoods/join',{crowd_id:_this.formData.crowd_id,order_number: _this.order_number}).then(res=>{
                _this.loadings=false;
                if(res.errcode==0)
                {
                  let total_amount = res.data.crowd_order.amount;
                  let order_code = res.data.crowd_order.order_code;
                  let address = res.data.crowdgoods_address
                  let url =encodeURI('/wallet/send/GBL Asset Chain?api=1&order=1&data='+order_code+'&amount='+total_amount+'&address='+address)
                  _this.$router.push({path:url})
                }
              })
                .catch(error=>{
                  _this.lock = false;
                  _this.loadings=false;
                  _this.$vux.toast.text(error.message);
                })

            }
          })

        }
    },
    computed:{
        rate() {
          return "购买人次(中奖率约"+parseFloat(this.order_number*100/this.amount).toFixed(2)+"%)"
        }
    }
};
</script>
<style lang="less" scoped>
@import "~vux/src/styles/1px.less";
.crowd-detail{
    .card-demo-flex {
        display: flex;
    }
    .card-demo-content01 {
        padding: 10px 0;
    }
    .card-padding {
        padding: 15px;
    }
    .card-demo-flex > div {
        flex: 1;
        text-align: center;
        font-size: 12px;
    }
    .card-demo-flex span {
        color: #f74c31;
    }
    .crowd_number{
        /deep/.vux-number-selector-sub{
            vertical-align: middle;
            display: inline-block;
            padding: 0px;
        }
        /deep/.vux-number-selector-plus{
            vertical-align: middle;
            display: inline-block;
            padding: 0px;
        }
        /deep/.vux-number-input{
            display: inline-block;
            vertical-align: middle;
            height: 100%;
            line-height: 100%;
            position: relative;
            bottom: 5px;
        }
    }
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;

    .content{
        position: absolute;
        top: 2.875rem;
        left: 0;
        bottom: 3.5rem;
        right: 0;
        overflow-x: hidden;
        overflow-y: scroll;
        background: #fff;
        .banner {
            width: 100%;
            position: relative;
            overflow: hidden;
            &::before{
                display: block;
                content: '';
                padding-top: 55%;
            }
            img {
                width: 100%;
                height: 100%;
                display: block;
                position: absolute;
                top: 0;
                left: 0;
            }
        }
        .crowd-info {
            padding-bottom: 0.3125rem;
            background: #f5f5f5;
            .info-box {
                background: #fff;
                padding-left: 0.9375rem;
            }
            .info-top {
                padding: 0.875rem 0.9375rem 0.5rem 0;
            }
            .crowd-title {
                font-size: 1.0625rem;
                line-height: 1.5625rem;
                height: 1.5625rem;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 1;
                overflow: hidden;
            }

            .info-decs {
                font-size: 0.8125rem;
                color: #999;
                line-height: 1.125rem;
                min-height: 1.125rem;
            }
            .info-img{
                position: relative;
                top:0.75rem;
            }
            .info-time {
                font-size: 0.8125rem;
                color: #999;
                line-height: 1.125rem;
                min-height: 1.125rem;
            }
            .info-con {
                padding: 0.9375rem 0.9375rem 0.5rem;
                padding-left: 0;
                .info-item {
                    font-size: 0.75rem;
                    color: #999;
                    .info-item-title {
                        line-height: 1rem;
                        span{
                            display: inline-block;
                            width: 0.875rem;
                            height: 0.875rem;
                            line-height: 0.875rem;
                            text-align: center;
                            color: #fff;
                            margin-right: 0.625rem;
                        }
                        .qi{
                            background: #719aee
                        }
                        .zhi{
                            background: #ef7f89;
                        }
                        .open{
                            background: #cf222d;
                        }
                    }
                    .info-item-numb {
                        line-height: 2rem;
                        height: 2rem;
                    }
                    .back_color{
                        color: #363840;
                    }
                }
                .info-time{
                    height: 1.5rem;
                    .info-item-numb{
                        line-height: 1rem;
                    }
                }
                .info-assets {
                    .info-item-numb {
                        font-size: 0.9375rem;
                    }
                }
            }
        }
        .crowd-decs {
            padding: 0.625rem 0.9375rem;
            .decs-title {
                font-size: 0.9375rem;
                line-height: 1.375rem;
                margin-bottom: 0.5rem;
            }
            .decs-txt {
                font-size: 0.75rem;
                line-height: 1.25rem;
                p{
                    img {
                        max-width: 100%!important;
                        display: block;
                    }
                }
                img {
                    max-width: 100%!important;
                    display: block;
                }
            }
        }
    }
    .crowd-btn-box {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5rem;
        line-height: 3.5rem;

        button.crowd-btn {
            display: flex !important;
            font-size: 0.9375rem;
            height: 3.5rem;
            flex-direction: column;
            justify-content: center;
            line-height: 3.5rem;
            span{
                line-height: 1.375rem;
            }
            .crowd-btn-decs {
                font-size: 0.75rem;
                line-height: 1.125rem;
                color: #b8cdff;
                display: block;
                height: 100%;
                line-height: 3.5rem;
            }
        }
        button.crowd-start{
            .crowd-btn-decs {
                font-size: 0.75rem;
                line-height: 1.125rem;
                color: #b8cdff;
                display: block;
                height: 100%;
            }
        }
        button.disabled{
            background: #bbb;
            .crowd-btn-decs{
                color: #fff;
            }
        }
    }
}
.crowd-detail-popup {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0,0,0,0);
    pointer-events: none;
    transition: all 0.3s;
    z-index: 99;
    .pop-con {
        position: absolute;
        top: 50%;
        left: 1.75rem;
        right: 1.75rem;
        background: #fff;
        transform: translateY(-50%);
        opacity: 0;
        transition: all 0.3s;
        .close-icon {
            position: absolute;
            right: 0;
            height: 2rem;
            line-height: 2rem;
            width: 2.125rem;
            text-align: center;
            font-size: 1rem;
            top: 0;
        }
        .title {
            line-height: 2rem;
            text-align: center;
            font-size: 0.9375rem;
        }
        .pop-tis {
            line-height: 1.375rem;
            padding: 0 0.9375rem;
            background: #e3e5ed;
            font-size: 0.6875rem;
            color: #f22e58;
        }
        .form-tis {
            line-height: 1.625rem;
            font-size: 0.9375rem;
            padding: 0 0.9375rem;
            em{
                font-size: 0.8125rem;
            }
        }
    }
}
.crowd-detail-popup.active{
    pointer-events: auto;
    background: rgba(0,0,0,0.5);
    .pop-con {
        opacity: 1;
    }
}
</style>
