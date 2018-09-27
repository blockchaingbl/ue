/**
 * Created by Administrator on 2017/6/17.
 */
import Vue from 'vue'
import VueX from 'vuex'
import * as actions from './actions'
import * as getters from './getters'

import JSON from "JSON";
import fanweCrypto from '@/crypto';
import axios from '../axios/index.js';
import {AlertModule,AlertPlugin,Toast,ToastPlugin} from 'vux'
Vue.use(VueX)
Vue.use(AlertPlugin)
Vue.use(ToastPlugin)

const state = {
    token:"",
    scrollloding:false,
    wallet:{},
    wallets:false,
    properties:[],
    properties_set:{},
    properties_amount:0,
    balance_loading:false,
    page_loading:false,
    config:false,
    init:{},
    sign:true,
    coin_info:{}
}
const mutations={
    reset_state(state){
        state.wallets = false;
        state.wallet = {};
        state.properties = [];
        state.properties_set = {};
    },
    set_token(state, token){
        state.token = token
    },
    set_init(state, init){
        state.init = init
    },
    set_scrollloding(state, scrollloding){
        state.scrollloding = scrollloding
    },
    updateConfig(state,config){
        state.config = config;
    },
    set_coin_info(state,coin_info){
        state.coin_info = coin_info;
    },
    changeWallet(state,address){
        if(address)
            address = address.toLowerCase();
        if(!state.wallets)
        {
            state.wallets = {};
            var wallets_config = localStorage.wallets;
            if(wallets_config)
            {
                try{
                    wallets_config = fanweCrypto.decrypt(wallets_config);
                    state.wallets = JSON.parse(wallets_config);
                }catch(e)
                {
                    console.log(e);
                }
            }
        }
        var wallet = state.wallets[address];
        if(wallet)
        {
            state.wallet = wallet;
            localStorage.default_address = wallet.address;
        }
    },
    addWallet (state, wallet) {
        wallet.address = wallet.address.toLowerCase();
        if(!state.wallets)
        {
            state.wallets = {};
            var wallets_config = localStorage.wallets;
            if(wallets_config)
            {
                try{
                    wallets_config = fanweCrypto.decrypt(wallets_config);
                    state.wallets = JSON.parse(wallets_config);
                }catch(e)
                {
                    console.log(e);
                }
            }
        }
        state.wallets[wallet.address] = wallet;
        var encStr = JSON.stringify(state.wallets);
        encStr = fanweCrypto.encrypt(encStr);
        localStorage.wallets = encStr;

        localStorage.default_address = wallet.address;
        state.wallet = wallet;
    },
    updateWallet(state,wallet){
        wallet.address = wallet.address.toLowerCase();
        if(!state.wallets)
        {
            state.wallets = {};
            var wallets_config = localStorage.wallets;
            if(wallets_config)
            {
                try{
                    wallets_config = fanweCrypto.decrypt(wallets_config);
                    state.wallets = JSON.parse(wallets_config);
                }catch(e)
                {
                    console.log(e);
                }
            }
        }
        state.wallets[wallet.address] = wallet;
        var encStr = JSON.stringify(state.wallets);
        encStr = fanweCrypto.encrypt(encStr);
        localStorage.wallets = encStr;
    },
    deleteWallet(state,wallet){
        wallet.address = wallet.address.toLowerCase();
        if(!state.wallets)
        {
            state.wallets = {};
            var wallets_config = localStorage.wallets;
            if(wallets_config)
            {
                try{
                    wallets_config = fanweCrypto.decrypt(wallets_config);
                    state.wallets = JSON.parse(wallets_config);
                }catch(e)
                {
                    console.log(e);
                }
            }
        }
        delete state.wallets[wallet.address];
        var encStr = JSON.stringify(state.wallets);
        encStr = fanweCrypto.encrypt(encStr);
        localStorage.wallets = encStr;

        for(var address in state.wallets);
        if(!address){
            localStorage.removeItem("default_address");
            localStorage.removeItem("wallets");
            state.wallets = false;
            state.wallet = {};
        }
    },
    loadWallets(state,callback){
        var default_address = localStorage.default_address;
        var wallet;
        axios.http.post('/api/app.wallet/wallets',{}).then(res => {

            if(res.data.wallets!=""){
                state.wallets = res.data.wallets;
                wallet = state.wallets[default_address];
                if(!wallet)
                {
                    for(default_address in state.wallets)
                    {
                        wallet = state.wallets[default_address];
                        localStorage.default_address = default_address;
                        break;
                    }
                }
                state.wallet = {name: wallet.name, address: wallet.address, enc_privateKey: wallet.enc_privateKey};
                state.balance_loading = false;
            }
            if(callback)
                callback.call(null);
        }).catch(err => {
            state.balance_loading = false;
            console.log("err");
            console.log(err);
        });
    },
    loadProperties(state,callback){
        // console.log(Vue);
        state.balance_loading = true;
        axios.http.post('/api/app.wallet/propertydb/sum',{address:state.wallet.address}).then(res => {
            state.properties = res.data.properties;
            state.balance_loading = false;
            state.properties_amount = 0;
            for(var i in state.properties)
            {
                state.properties_set[state.properties[i].type] = state.properties[i];
                if(state.properties[i].amount>0)
                    state.properties_amount += state.properties[i].amount*state.properties[i].price;
            }
            if(callback)
                callback.call(null);
        }).catch(err => {
            state.balance_loading = false;
            // if(err)
            //     Vue.$vux.toast.text("网络超时，下拉重试");
            if(callback)
                callback.call(null);
            console.log("err");
            console.log(err);
        });
    },
    refresh_coin_type(state){
        var id = parseInt(state.coin_info.coin_type.id);
        axios.http.post('/api/app.user/account/asset',{coin_type:id,single:1}).then(res => {
            if(res.errcode==0){
                state.coin_info= res.data.asset;
            }
        })
    },
}
//创建store 实例
export default new VueX.Store({
    actions,
    getters,
    state,
    mutations
})