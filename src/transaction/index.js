import axios from '../axios';
import router from '../router';
import JSON from "JSON";
var FanweTransaction = {
    //普通转出
    sendTransaction:function(cointype,to,amount,callback){
        //定义回调
        tx_callback = callback;
        let url ="/wallet/send/"+cointype+"?address="+to+"&amount="+amount+"&api=1";
        url = encodeURI(url)
        router.push(url);
    },
    runContract:function(address,amount,contract,func,params,callback){
        //定义回调
        tx_callback = callback;
        var params_json = JSON.stringify(params);
        var _this = this;
        axios.http.post('/api/app.wallet/transaction/getdata',{contract:contract,func:func,params:params_json}).then(res => {
            var data = res['data']['txdata'];
            router.push("wallet/send/ethereum?address="+address+"&amount="+amount+"&data="+data+"&api=1");
        }).catch(err => {
            tx_callback = null;
        });
    },
    dechex:function(intvalue){
        var hex = "0x"+intvalue.toString(16);
        return hex;
    },
    //回调
    callbackDelegate:function(txhash){
        if(tx_callback)
            tx_callback.call(null,txhash);
    }
};


export default FanweTransaction;