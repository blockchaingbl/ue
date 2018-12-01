<template lang="html">
    <div :id="page_name">
        <x-header :left-options="{showBack: true,backText:''}" :right-options="{showMore: false}">{{page_title}}
            <i  slot="right" v-on:click="showMenus()"  class="iconfont"  style="fill:#fff;position:relative;top:-2px;font-size:1.4rem;">&#xe6f5;</i>
        </x-header>
        <div class="main_card">
            <div class="wallet_amount">{{coin_amount}}</div>
            <div class="wallet_name">{{this.$store.state.wallet.name}}</div>
            <div class="wallet_address" v-clipboard:copy="this.$store.state.wallet.address" v-clipboard:success="onCopy" v-clipboard:error="onError">{{shortStr(this.$store.state.wallet.address,12,20,'...')}} <i class="iconfont">&#xe64b;</i></div>
        </div>

        <group>
            <x-input title="转入地址" :readonly="order"  v-model="recieve_address" class="recieve_input" :required="true" ref="recieve_address">
                <i class="iconfont" slot="right" v-on:click="open_scan()" v-show="$store.state.init.is_app">&#xe6e1;</i>
            </x-input>
            <x-input title="转出数额" :readonly="order" v-model="recieve_amount" type="number" class="recieve_input recieve-price"  :required="true" ref="recieve_amount"></x-input>
        </group>

        <group>
            <x-input title="钱包密码"  type="password" class="recieve_input" v-model="password"></x-input>
        </group>
        <div class="recieve_input_block">
            <div class="cost-input"  v-show="displayType==0" :class='{active:displayType==0}' >
                <div class="gas-title">矿工费用</div>
                <cell title=""  primary="content" class=" recieve_input" style="background:#fff">
                    <range v-model="gas_price" :min="1" :max="100"></range>
                </cell>
                <div class="gas-s">慢</div>
                <div class="gas-price">gas price: {{gas_price}}</div>
                <div class="gas-f">快</div>
            </div>
            <div class="senior"   v-show="displayType==1" :class='{active:displayType==1}' style="background:#fff">
                <x-input title="Gas" type="number" v-model="gas" class="recieve_input"></x-input>
                <x-textarea placeholder="data(十六进制数据)" class="recieve_input" v-model="data" :readonly="order"></x-textarea>
            </div>

        </div>

        <group v-if="!api" class="senior_btn">
            <x-switch title="高级选项" :value-map="['0','1']" v-model="displayType" class="recieve_input"></x-switch>
        </group>

        <box gap="20px 10px">
            <x-button type="primary" style="border-radius:99px;" @click.native="estimateTx">流通估算</x-button>
        </box>

        <popup v-model="tx_detail" v-transfer-dom>
            <div class="tx_detail">
            <div class="txtit">流通核对</div>
                <group >
                    <cell>
                        <span slot="title" class="tx_detail_title">转入地址</span>
                        <span class="tx_detail_value bk">{{recieve_address}}</span>
                    </cell>
                    <cell>
                        <span slot="title" class="tx_detail_title">付款钱包</span>
                        <span class="tx_detail_value bk">{{this.$store.state.wallet.address}}</span>
                    </cell>
                    <cell>
                        <span slot="title" class="tx_detail_title">矿工费用</span>
                        <span class="tx_detail_value">{{miner_fee}} </span>
                    </cell>
                    <cell>
                        <span slot="title" class="tx_detail_title">数额</span>
                        <span class="tx_detail_value">{{recieve_amount}} {{coin_unit}}</span>
                    </cell>
                </group>
                <box gap="20px 10px">
                    <x-button type="primary" style="border-radius:99px;" @click.native="sendTx">流通发送</x-button>
                </box>

            </div>
        </popup>
        <popup v-model="showRightBar" position="right" style="width:70%;background:#fff;"  v-transfer-dom>
            <div style="width:100%; padding-top:30px;">
                <group title="钱包列表">
                    <radio v-model="wallet_address" :options="wallets" class="right_item">
                        <template slot-scope="props" slot="each-item">
                            <p>
                                <img src="@/assets/images/icon_wallet.png" class="vux-radio-icon"> {{ props.label }}
                            </p>
                        </template>
                    </radio>
                        <!--  -->
                </group>
                <box gap="25px 10px">
                <x-button class="right_item  rbt" style="background-color:#1d62c1;border-radius:99px;" type="primary" @click.native="create_wallet">创建钱包</x-button>
                <x-button class="right_item rbt" style="background-color:#1da0c1;border-radius:99px;" type="primary" @click.native="import_wallet">导入钱包</x-button>
                    <x-button class="right_item rbt" style="background-color:#fff;padding:0;" type="default" @click.native="manage_wallet">管理钱包</x-button>
                </box>
            </div>
        </popup>

        <popup v-model="tx_done" height="100%" position="top" is-transparent v-transfer-dom>
            <div style="width: 300px;background-color:#fff;height:230px;margin:150px auto 0;border-radius:5px;padding-top:20px; text-align: center;">
                <div style="font-size:16px; text-align: center; line-height: 70px;">流通提交成功，请等待流通生效</div>
                <span style="font-size:10px; background:#ca3f3f; color:white; line-height:20px; height:20px; display: inline-block; border-radius:5px;">&nbsp;&nbsp;{{shortStr(tx_hash,18,30,'...')}}&nbsp;&nbsp; </span>
                <div style="padding:20px 15px; ">
                    <x-button type="primary" v-clipboard:copy="tx_hash" v-clipboard:success="onCopy" v-clipboard:error="onError" style="font-size:14px;">复制流通单号</x-button>
                    <x-button @click.native="send_done" style="font-size:14px;">关闭</x-button>
                </div>
            </div>
        </popup>
    </div>
</template>
<script>
import {
  TransferDom,
  XHeader,
  XInput,
  XTextarea,
  XButton,
  Box,
  XSwitch,
  Range,
  Cell,
  Popup,
  Radio,
  AlertModule
} from "vux";
import Web3 from "web3";
import router from "@/router";
import JSON from "JSON";
import fanweEth from "@/ethereum";
import fanweCrypto from "@/crypto";
import tx from "@/transaction";
export default {
  directives: {
    TransferDom
  },
  components: {
    "x-header": XHeader,
    "x-input": XInput,
    "x-textarea": XTextarea,
    "x-button": XButton,
    box: Box,
    "x-switch": XSwitch,
    range: Range,
    cell: Cell,
    popup: Popup,
    radio: Radio
  },
  data() {
    var coin_amount = 0;
    var coin_type = "ethereum";
    var gas_price = 5;
    if (router.currentRoute.params["coin"]) {
      coin_type = router.currentRoute.params["coin"];
    }
    if (this.$store.state.properties.length > 0) {
      coin_amount =
        this.$store.state.properties_set[coin_type].name +
        " " +
        this.$store.state.properties_set[coin_type].amount;
        gas_price = this.$store.state.properties_set[coin_type].default_gas_price;
    }
    return {
      page_title: router.currentRoute.meta.title,
      page_name: router.currentRoute.name,
      coin_amount: coin_amount,
      coin_type: coin_type,
      router: router,
      recieve_address: "",
      recieve_amount: "",
      gas_price: gas_price,
      gas: "25200",
      gas_estimate: 0,
      data: "",
      displayType: 0,
      password: "",
      tx_detail: false,
      tx_raw: "",
      miner_fee: 0,
      coin_unit: "",
      api: false,
      showRightBar: false,
      wallets: [],
      wallet_address: "",
      showfrist: true,
      tx_done:false,
      tx_hash:"",
      base_coin:"",
      block_chain:"",
      order:false
    };
  },
  mounted() {
	if(router.currentRoute.query.address)
    this.recieve_address = router.currentRoute.query.address;
    if (router.currentRoute.query.amount)
      this.recieve_amount = router.currentRoute.query.amount;
    if (router.currentRoute.query.gas_price)
      this.gas_price = router.currentRoute.query.gas_price;
    if (router.currentRoute.query.gas) this.gas = router.currentRoute.query.gas;
    this.data = router.currentRoute.query.data;
    this.api = router.currentRoute.query.api == 1;
    this.order = Boolean(router.currentRoute.query.order);
    if(this.order)
    {
      this.recieve_address = this.$store.state.init.order_address;
    }
    var _this = this;
    if (!this.$store.state.wallet.address) {
      this.$store.commit("loadWallets", function() {
        _this.refresh_wallet();
        _this.$store.commit("loadProperties", function() {
          var coin_type = "ethereum";
          if (router.currentRoute.params["coin"]) {
            coin_type = router.currentRoute.params["coin"];
          }
          _this.coin_amount =
            _this.$store.state.properties_set[coin_type].amount +
            " " +
            _this.$store.state.properties_set[coin_type].name;
            _this.gas_price= _this.$store.state.properties_set[coin_type].default_gas_price;
        });
      });
    }
    if (this.$store.state.wallets) {
      for (var address in this.$store.state.wallets) {
        var wallet_item = {
          key: address,
          value: this.$store.state.wallets[address].name
        };
        this.wallets.push(wallet_item);
      }
      this.wallet_address = this.$store.state.wallet.address;
    }
  },
  methods: {
    changerange(val) {
      /*  this.gas_price=parseInt(val);
                console.log();*/
    },
      send_done(){
        this.tx_done = false;
        this.password = "";
        var path = "/wallet/detail/"+this.coin_type;
        this.$router.push(path);
      },
      open_scan(){
          App.qr_code_scan();
          var _this = this;
          window.js_qr_code_scan = function(qr_code){
              if(qr_code.substr(0,2)=="0x"){
                  _this.recieve_address = qr_code;
              }
              else
              {
                  qr_code = qr_code.replace(_this.$store.state.init.route_domain+"/#","");
                  var address = qr_code.match(/address\=([^\&]+)/);
                  if(address){
                      address = address[1];
                  }
                  _this.recieve_address = address;
                  var amount = qr_code.match(/amount\=([\d|.]+)/);
                  if(amount){
                      amount = amount[1];
                  }
                  _this.recieve_amount = amount;
              }
          };
      },
    sendTx: function() {
      if (!this.$store.state.page_loading) {
        this.$store.state.page_loading = true;
        var loading = this.$loading({ text: "流通发送" });
        this.$http
          .post("/api/app.wallet/transaction/sendraw", { raw: this.tx_raw ,block_chain:this.block_chain})
          .then(res => {
            this.$store.state.page_loading = false;
            loading.close();
            this.tx_detail = false;
            this.password = "";
            this.tx_hash = res.data.tx_hash;
            this.tx_done = true;

            // console.log(res);
          })
          .catch(err => {
            this.$store.state.page_loading = false;
            loading.close();
            console.log(err);
            if (err.errcode) {
              AlertModule.show({
                title: "错误",
                content: err.message,
                onShow() {},
                onHide() {}
              });
            }
          });
      }
    },
    estimateTx: function() {
        if(this.recieve_address.substr(0,2)!='0x')
        {
            AlertModule.show({
                title: '错误',
                content: "请输入正确的ETH地址",
                onShow () {
                },
                onHide () {

                }
            });
            return;
        }
        //md5校验
        var password = this.password;
        if(this.$store.state.wallet.wallet_pwd){
            if(this.$store.state.wallet.wallet_pwd!=fanweCrypto.md5(password)){
                AlertModule.show({
                    title: '错误',
                    content: "钱包密码错误",
                    onShow () {
                    },
                    onHide () {

                    }
                });
                return;
            }
        }

      var privateKey = "";
      var dec_address = "";
      try {
        var privateKey = fanweCrypto.decrypt(
          this.$store.state.wallet.enc_privateKey,
          this.password
        );
        var dec_address = fanweEth.wallet.privatekeyToAddress(privateKey);
      } catch (ex) {
        console.log(ex);
      }

      if (
        dec_address.toLowerCase() !=
        this.$store.state.wallet.address.toLowerCase()
      ) {
        AlertModule.show({
          title: "错误",
          content: "钱包密码错误",
          onShow() {},
          onHide() {}
        });
        return;
      }

      if (this.recieve_amount < 0) {
        AlertModule.show({
          title: "错误",
          content: "数额错误",
          onShow() {},
          onHide() {}
        });
        return;
      }
      if (
        this.recieve_amount >
        this.$store.state.properties_set[this.coin_type].amount
      ) {
        AlertModule.show({
          title: "错误",
          content: "账户余额不足",
          onShow() {},
          onHide() {}
        });
        return;
      }

      this.$refs.recieve_address.validate();
      this.$refs.recieve_amount.validate();
      if (
        !this.$refs.recieve_address.valid ||
        !this.$refs.recieve_amount.valid
      ) {
        this.$refs.recieve_address.forceShowError = true;
        this.$refs.recieve_amount.forceShowError = true;
        return;
      }

      if (!this.$store.state.page_loading) {
        var decimals = parseInt(
          this.$store.state.properties_set[this.coin_type].decimals
        );
        //var value = parseFloat(this.recieve_amount) * Math.pow(10, decimals);
        var value =  this.recieve_amount
        this.coin_unit = this.$store.state.properties_set[this.coin_type].name;

        this.$store.state.page_loading = true;
        var loading = this.$loading({ text: "流通估算" });
        this.$http
          .post("/api/app.wallet/transaction/estimate", {
            from: this.$store.state.wallet.address,
            to: this.recieve_address,
            value: value,
            data: this.data,
            coin: this.coin_type
          })
          .then(res => {
            this.$store.state.page_loading = false;
            this.base_coin = res.data.base_coin;
            this.block_chain = res.data.block_chain;
            if (res.data.gas == 0) {
              AlertModule.show({
                title: "错误",
                content: "流通出错",
                onShow() {},
                onHide() {}
              });
              loading.close();
              return;
            }

            this.gas_estimate = res.data.gas;
            //gas太低使用预估的
            if (res.data.gas > this.gas) this.gas = res.data.gas;
            if (res.data.data) {
              this.data = res.data.data;
            }
            var transfer_value = res.data.transfer_value;
            var transfer_address = res.data.transfer_address;
            this.tx_raw =
              "0x" +
              fanweEth.transaction.sign(
                privateKey,
                res.data.nonce,
                this.gas_price,
                this.gas,
                transfer_address,
                transfer_value,
                this.data
              );
            loading.close();
            // console.log(this.tx_raw);
            this.tx_detail = true;
            this.miner_fee =
              "约 " + this.gas_estimate * this.gas_price / 1000000000 + " " + this.base_coin;
          })
          .catch(err => {
            this.$store.state.page_loading = false;
            loading.close();
            if(err.errcode!=0)
            {
              AlertModule.show({
                title: '错误',
                content: err.message,
                onShow () {
                },
                onHide () {

                }
              });
            }
          });
      }
    },
    refresh_wallet: function() {
      if (this.$store.state.wallets) {
        for (var address in this.$store.state.wallets) {
          var wallet_item = {
            key: address,
            value: this.$store.state.wallets[address].name
          };
          this.wallets.push(wallet_item);
        }
        this.wallet_address = this.$store.state.wallet.address;
      } else {
        router.replace("/wallet/create");
      }
    },
    walletItemClass: function(wallet_item) {
      var cls =
        this.$store.state.wallet.address == wallet_item.address
          ? "current"
          : "";
      return cls;
    },
    showMenus: function() {
      this.showRightBar = true;
      if (this.showfrist) {
        this.showfrist = false;
      }
    },
    create_wallet: function() {
      router.push("/wallet/create");
    },
    import_wallet: function() {
      router.push("/wallet/import");
    },
    manage_wallet: function() {
      router.push("/wallet/manage");
    },
    refresh: function(loaded) {
      this.$store.commit("loadProperties", function() {
        loaded("done");
      });
    },
    onCopy: function(e) {
      this.$vux.toast.text("复制成功");
      //console.log('You just copied: ' + e.text)
    },
    onError: function(e) {
      this.$vux.toast.text("复制失败，您可以尝试手动记录");
      //console.log('Failed to copy texts')
    }
  },
  watch: {
    wallet_address: {
      handler(currentAddress, lastAddress) {
        //无上次地址表示首次进入，当已加载过不加载
        if (
          (!lastAddress && this.$store.state.properties.length > 0) ||
          lastAddress == ""
        )
          return;
        var $vue = this;
        this.$store.commit("changeWallet", currentAddress);
        this.$store.commit("loadProperties", function() {
          $vue.coin_amount =
            $vue.$store.state.properties_set[$vue.coin_type].name +
            " " +
            $vue.$store.state.properties_set[$vue.coin_type].amount;
        });
        this.showRightBar = false;
      }
    },
    gas_price: {
      handler(currentGas, lastGas) {
        this.gas_price = parseFloat(this.gas_price);

        // console.log("test");
      }
    },
    $route() {
      var _this = this;
      this.$store.commit("loadWallets", function() {
        _this.refresh_wallet();
      });
      this.$store.commit("loadProperties");
    }
  }
};
</script>
<style lang="less" scoped>
@import "~vux/src/styles/1px.less";
.txtit {
  width: 100%;
  line-height: 2.6rem;
  font-size: 1.1rem;
  text-align: left;
  background: rgb(255, 255, 255);
  padding-top: 0.4rem;
  padding-left: 15px;
}
#wallet_send {
  overflow-x: hidden;
  .main_card {
    padding: 2rem 0;
    width: 100%;
    text-align: center;
    background: url("../../assets/images/walletbg.png");
    background-size: cover;
    line-height: 2rem;
    color: #fff;
  }
  .wallet_amount {
    font-size: 1.2rem;
  }
  .wallet_name {
    font-size: 1.5rem;
  }
  .wallet_address {
    overflow: hidden;
    margin: 0 auto;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.8rem;
  }
  .recieve_input {
    font-size: 0.9rem;
    color: #333333;
  }
  .vux-popup-dialog {
    background: #fff;
    color: #525252;
  }
  .tx_detail_value {
    font-size: 0.8rem;
    display: inline-block;
    float: left;
  }
  .vux-cell-primary {
    width: 80px;
  }
  .tx_detail_title {
    font-size: 0.8rem;
    display: inline-block;
    width: 80px;
  }

  .bk {
    word-wrap: break-word;
    word-break: break-all;
  }

  .senior {
    transform: translateX(100%);
    transition: all 0.3s ease;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
  }
  .cost-input {
    transition: all 0.3s ease;
    transform: translateX(-100%);
    position: relative;
    top: 0;
    left: 0;
    right: 0;
  }
  .senior.active {
    transform: translateX(0);
  }
  .cost-input.active {
    transform: translateX(0);
  }
  .recieve_input_block {
    position: relative;
    height: 9.3125rem;
    background-color: #fff;
    .gas-title {
      padding: 10px 15px;
      color: #888;
      margin-bottom: 0.625rem;
    }
    .gas-price {
      position: absolute;
      transform: translateX(-50%);
      left: 50%;
      bottom: -2rem;
    }
    .gas-f,.gas-s{
      position: absolute;
      bottom: -2rem;
      color:#888;
    }
    .gas-s {
      left:1.5rem
    }
    .gas-f{
      right:1.5rem;
    }
  }
}
</style>


