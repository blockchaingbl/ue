<template lang="html">
    <div class="page-grant">
        <div v-if="!loading">
            <div class="center-banner">
                <div class="user-info">
                    <div class="user-portrait">
                        <img src="@/assets/images/avatar.png" alt="" v-if="useravatar==''">
                        <img v-bind:src="useravatar" v-else>
                    </div>
                    <div class="user-name">
                        <div class="name">{{username}}</div>
                    </div>
                    <div class="user-modile"></div>
                </div>

            </div>
            <div class="candy-basic-opera">
                <group class="assets-type vux-1px-b">
                    <popup-radio title="" :options="ex_rate" v-model="chose_rate"  @on-hide="hide_change">
                        <p slot="popup-header" class="vux-1px-b grant-coin-slot"></p>
                    </popup-radio>
                </group>
                <group class="assets-type vux-1px-b">
                    <popup-radio title="" :options="asset_lists" v-model="chose_asset_id"  @on-hide="hide_change_asset">
                        <p slot="popup-header" class="vux-1px-b grant-coin-slot"></p>
                    </popup-radio>
                </group>
                <group class="numb-assets vux-1px-b">
                    <x-input  v-model="amount"  placeholder="请输入转出数额"  type="number"  :required="true"></x-input>
                </group>
                <div class="candy-basic-opera">
                    <group class="candy-numb" title="资产密码">
                        <x-input  v-model="security" type="password"  placeholder="请输入资产密码"  :required="true" ></x-input>
                    </group>
                </div>
            </div>
            <group class="grant-bottom">
                <cell>
                    <div>
                        <span style="color: red">{{gbl_price}}</span>
                    </div>
                </cell>
                <cell>
                    <div>
                        <span style="color: red">可转{{vc_normal}}</span>
                    </div>
                </cell>
            </group>
            <div class="order_out">
                <router-link  :to="{path:'/transfer/order_out'}">
                    <div class="bottom-txt">转出记录</div>
                </router-link>
            </div>

            <box class="grant-btn-box" gap="0 0">
                <x-button type="primary" style="border-radius:0;height:2.875rem;font-size:0.875rem;" @click.native="send_to">确认转出</x-button>
            </box>
        </div>
        <div v-transfer-dom>
            <loading :show="loading"></loading>
        </div>
    </div>
</template>
<script>
import { XInput,XButton,Box,PopupRadio,XSwitch,Loading, TransferDomDirective as TransferDom,Radio  } from 'vux';
export default {
    directives: {
        TransferDom
    },
    components: {
        XInput,
        XButton,
        Box,
        PopupRadio,
        XSwitch,
        Loading,
        Radio
    },
    data () {
        return {
            useravatar:'',
            username:'',
            loading:true,
            usermobile:'',
            security:'',
            trans_fee:null,
            ex_rate:[],
            chose_rate:'',
            amount:'',
            coin_price:'',
            real_expend:'',
            rate_lists:[],
            chose_info:{},
            security:"",
            vc_normal:'',
            coin_type:0,
            asset_lists:[],
            chose_asset:{}
        }
    },
    mounted () {
        this.get_info();
    },
    methods: {
        get_info(){
            const id = this.$route.params.id;
            this.$http.post('/api/app.user/transfer/userexrate',{id:id}).then(res => {
            if(!res.data.has_sec){
                this.$vux.toast.text('您还未设置资产密码,请前去设置');
                setTimeout(()=>{
                    this.$router.push({name:'editSecurity'});
                },2000)
            }
            this.trans_fee =res.data.trans_fee;
            this.username = res.data.user.username;
            this.user_id = res.data.user.id;
            this.useravatar = res.data.user.avatar
            this.usermobile = res.data.user.mobile
            this.coin_price = res.data.coin_price;
            this.rate_lists = res.data.lists;
            this.asset_lists_all = res.data.assets_lists;
            res.data.assets_lists.map((val)=> {
            let value = val.coin_type.coin_unit;
            this.asset_lists.push({key:val.id,value:value})
            })
            this.chose_asset_id= res.data.last_transfer;
            this.hide_change_asset();
            res.data.lists.map((val)=> {
                let value = `${val.name}(${val.symbol})`;
                this.ex_rate.push({key:val.id,value:value})
            })
            this.chose_rate = res.data.lists[0].id;
            this.hide_change();
            this.loading = false;
            }).catch(err => {
                this.$router.push({path: '/error/404'})
                this.loading = false;
            });
        },
        send_to(){
            this.loading = true;
            let post = {
                amount:this.amount,
                ex_id:this.chose_rate,
                to_id:this.user_id,
                security:this.security,
                coin_type:this.chose_asset_id
            }
            this.$http.post('/api/app.user/transfer/send',post).then(res => {
                if(res.errcode==0)
                {
                    let transfer = res.data.transfer;
                    transfer.to_user = this.username;
                    this.$router.push({name:'transferSuccess',params:transfer});
                }
                this.loading = false;
            }).catch(err=>{
                this.$vux.toast.text(err.message);
                this.loading = false;
            })
        },
        hide_change(){
            let id = this.chose_rate;
            this.chose_info = this.rate_lists.find(function (val) {
                return val.id==id;
            })
        },
        hide_change_asset(){
            let id = this.chose_asset_id;
            this.chose_asset = this.asset_lists_all.find(function (val) {
              return val.id==id;
            })
            this.vc_normal =  this.chose_asset.vc_total;

        }
    },
    computed:{
        gbl_price:function () {
            if(parseFloat(this.amount)>0)
            {
                let amount = parseFloat(this.amount)
                let chose_info =this.chose_info;
                let coin_price = parseFloat(this.chose_asset.coin_type.real_price);
                let gbl_price = (amount*chose_info.rate/coin_price)*1000000000/1000000000*(parseFloat(this.chose_asset.coin_type.transfer_fee)+1);
                return '约'+gbl_price.toFixed(5) + '  '+this.chose_asset.coin_type.coin_unit;
            }
        }
    }
}
</script>
<style lang="less" scoped>
@import "../../assets/css/variable.less";
.page-grant{
    min-height: 100%;
    padding-bottom: 2.375rem;
    .candy-basic-opera{
        background: #fff;
    }
    .candy-senior-opera{
        margin-top: 0.625rem;
        background: #fff;
        padding-bottom: 0.25rem;
    }

    .text_red{
        color: red;
    }
    .cell-assets{
        padding-bottom: 0;
    }
    .grant-bottom{
        margin-top: 0.625rem;
        min-height: 5rem;
        background: #fff;
        position: relative;
    }
    .order_out{
        width: 100%;
        text-align: center;
        display: block;
        margin-top: 1rem;
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
    .user-info{
        padding: 2.5rem 0 0.25rem;
        text-align: center;
        width: 100%;
        left: 0;
        z-index: 1;
        .user-portrait{
            width: 5rem;
            height: 5rem;
            position: absolute;
            top: 1.5rem;
            transform: translateX(-50%);
            left: 50%;
            box-sizing: border-box;
            padding: 4px;
            background: #fff;
            border-radius: 2.5rem;
            box-shadow: 0 2px 15px 0 rgba(33, 93, 247, 0.2);
            img{
                width: 100%;
                height: 100%;
                border-radius: 50%;
                display: block;
            }
        }
        .user-name{
            margin-top: 4.5rem;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            display: -webkit-flex;
            box-align: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            font-size: 0.875rem;
            line-height: 1.5rem;
            height: 1.5rem;
            font-weight: bold;
            color: #5d5d61;
            .name{
                margin: 0 auto;
            }
        }

        .user-modile{
            font-size: @fs-middle;
            font-family: arial;
            line-height: 1.25rem;
            height: 1.25rem;
            margin-top: 0.25rem;
            color: #888;
        }
    }
    .center-banner{
        background: url(../../assets/images/user_index_banner.png);
        background-repeat: no-repeat;
        background-position: top center;
        background-color: #fff;
        background-size: 100%;
        height: 51.735vw;
        position: relative;
    }
}
</style>


