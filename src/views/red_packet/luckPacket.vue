<template>
  <div class="luck-packet">
    <group class="packet-group">
          <popup-picker :title="title1" :data="list1" v-model="inputValue"  
          @on-show="onShow" 
          @on-hide="onHide" 
          @on-change="onChange" >
          </popup-picker>
    </group>
    <p class="luck-remark">每人数额随机</p>
    <div class="luck-group">
      <router-link to="" class="luck-link">
        <p>总数额</p>
        <span><input v-model="number1" type="number" placeholder="0"/>&nbsp;{{inputValue[0]}}</span>
      </router-link>
    </div>
    <p class="luck-remark">托管数额：<span class="luck-number">{{ amount }}{{inputValue[0]}}</span><span class="red-pay">兑换</span></p>
    <div class="luck-group">
      <router-link to="" class="luck-link">
        <p>红包个数</p>
        <span><input v-model="number2" type="number" placeholder="输入个数"/>&nbsp;个</span>
      </router-link>
    </div>
    <textarea class="luck-message" placeholder="恭喜发财，吉祥如意"></textarea>
    <div class="luck-total">
      <div class="total-con">
         <p>塞币总额({{inputValue[0]}})</p>
         <span>{{ number1 }}</span>
      </div>    
    </div>
  
   <button type="button" class="into-purse" @click="showDialog">塞钱进钱包</button>

  <!-- 弹窗组件 -->
  <div v-transfer-dom>
    <div v-show="isShow">
        <luck-dialog v-on:close="closeDialog" :number1="number1" :number2="number2" :inputValue="inputValue"></luck-dialog>
    </div>
 </div> 

  </div>
</template>

<script>
import { Group, PopupPicker, TransferDomDirective as TransferDom } from "vux";
import luckDialog from "@/views/red_packet/inc/luckDialog";
export default {
  directives: {
    TransferDom
  },
  components: {
    luckDialog,
    Group,
    PopupPicker
  },
  data() {
    return {
      amount: 36.07568,
      title: "资产密码",
      title1: "红包代币",
      placeholder: "请输入托管账户资产密码",
      isShow: false,
      hasShow: false,
      list1: [["AATC", "BTC", "ETH", "ACT", "LET", "USC"]],
      inputValue: ["AATC"],
      number1: null,
      number2: null
    };
  },
  methods: {
    showDialog() {
      this.isShow = true;
    },
    closeDialog(val) {
      this.isShow = val;
    },
    showTokens() {
      this.hasShow = true;
    },
    onShow() {},
    onHide() {},
    onChange() {}
  },
  watch: {
    number1() {
      if (this.number1 >= 9) {
        this.number1 = this.number1.slice(0, 9);
      }
    }
  }
};
</script>

<style lang="less" scoped>
.luck-packet {
  padding: 0.9375rem 0.9375rem 0;
  .luck-group {
    height: 2.625rem;
    background: #fff;
    border-radius: 2px;
    padding: 0 0.75rem;
    .luck-link {
      width: 100%;
      height: 100%;
      display: block;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #22262d;
      font-size: 0.875rem;
      i {
        vertical-align: middle;
      }
      input {
        text-align: right;
        font-size: 0.875rem;
        border: none;
        &::-webkit-input-placeholder {
          color: #c1c1c1;
        }
      }
      .select-token {
        color: #22262d;
      }
    }
  }
  .luck-remark {
    font-size: 0.9375rem;
    color: #999;
    margin-bottom: 0.625rem;
    line-height: 0.95rem;
    margin-top: 0.375rem;
    .luck-number {
      color: #22262d;
    }
    .red-pay {
      color: #c63845;
      margin-left: 0.9375rem;
    }
  }
  .luck-message {
    width: 100%;
    height: 4.375rem;
    border: none;
    border-radius: 3px;
    padding-top: 0.75rem;
    padding-left: 0.75rem;
    margin-top: 0.75rem;
    resize: none;
    &::-webkit-input-placeholder {
      color: #c1c1c1;
      font-size: 0.875rem;
      font-family: Arial, Helvetica, sans-serif;
    }
    &:focus {
      outline-style: none;
    }
  }
  .luck-total {
    height: 5.968rem;
    color: #22262d;
    display: flex;
    justify-content: center;
    align-items: center;
    .total-con {
      text-align: center;
      span {
        display: block;
        font-size: 2.5rem;
        height: 2.5rem;
        line-height: 2.5rem;
      }
    }
  }
  .into-purse {
    width: 18.22rem;
    height: 2.59rem;
    background: #de4150;
    border: none;
    color: #fff;
    font-size: 0.9375rem;
    margin: 0 auto;
    border-radius: 5px;
    display: block;
  }
}
</style>

