import Bip39 from 'bip39';
import Tx from "ethereumjs-tx";
import util from "ethereumjs-util";
import Wallet from "ethereumjs-wallet";
var HDKey = require('ethereumjs-wallet/hdkey');
var Buffer = require('safe-buffer').Buffer;

var FanweETH = {
    //钱包的管理
    wallet:{
        //通过密码创建一个钱包，返回地址
        createWallet:function(){
            //关于账户生成
            var _wallet = Wallet.generate();
            return {"address":_wallet.getAddressString(),"privatekey":_wallet.getPrivateKeyString().replace("0x","")};
        },
        //生成助记词
        createSeed:function(){
            return Bip39.generateMnemonic();
        },
        //根据助记词创建或找回钱包
        createBySeed:function(_seed,_pass,_index){
            var seedKey = Bip39.mnemonicToSeed(_seed,_pass);
            var hdWallet = HDKey.fromMasterSeed(seedKey);

            //BIP44：基于 BIP32 的系统，赋予树状结构中的各层特殊的意义。让同一个 seed 可以支援多令牌、多帐户等。各层定义如下：
            //m / purpose(固定44)' / coin_type(0:Bitcoin, 60:Ethereum)' / account' / change / address_index
            //以下是0与1两个钱包

            var key = hdWallet.derivePath("m/44'/60'/0'/0/"+_index);
            var address = util.pubToAddress(key._hdkey._publicKey, true);
            address = util.toChecksumAddress(address.toString('hex'));

            return {"address":address,
                "privatekey":key._hdkey._privateKey.toString('hex')};
        },
        createByPrivate:function(_privatekey){
            var address = this.privatekeyToAddress(_privatekey);
            return {"address":address,
                "privatekey":_privatekey};
        },
        createByKeystore:function(_keystore,_pass){
            var _wallet = Wallet.fromV3(_keystore,_pass);
            return {"address":_wallet.getAddressString(),"privatekey":_wallet.getPrivateKeyString().replace("0x","")};
        },
        //导出相密码的私钥
        privatekeyToKeystore:function(_privatekey,_pass){
            _privatekey = _privatekey.substr(0,2)=="0x"?_privatekey.replace("0x",""):_privatekey;
            var _wallet = Wallet.fromPrivateKey(new Buffer(_privatekey,"hex"));
            return _wallet.toV3String(_pass);
        },
        //通过私钥找回地址
        privatekeyToAddress:function(_privatekey){
            _privatekey = _privatekey.substr(0,2)=="0x"?_privatekey.replace("0x",""):_privatekey;
            var _wallet = Wallet.fromPrivateKey(new Buffer(_privatekey,"hex"));
            return _wallet.getAddressString();
        },
        //keystore找回私钥
        keystoreToPrivatekey:function(_keystore,_pass){
            var _wallet = Wallet.fromV3(_keystore,_pass);
            return _wallet.getPrivateKeyString().replace("0x","");
        },
        //keystore找回地址
        keystoreToAddress:function(_keystore,_pass){
            var _wallet = Wallet.fromV3(_keystore,_pass);
            return _wallet.getAddressString();
        }
    },
    transaction:{

        //为流通签名，提供给eth_sendRawTransaction
        sign:function(_privatekey,_nonce,_gasprice,_gaslimit,_to,_amount,_data){

            _nonce = parseInt(_nonce);
            _gasprice = parseInt(_gasprice);
            _gaslimit = parseInt(_gaslimit);


            _privatekey = _privatekey.substr(0,2)=="0x"?_privatekey.replace("0x",""):_privatekey;
            var privateKey = new Buffer(_privatekey, 'hex');
            var nonce  = "";
            if(_nonce)
                nonce = "0x"+_nonce.toString(16);

            _gasprice = 1000000000 * _gasprice;//转换成wei  Gwei(1Gwei = 0.000000001Ether = 1000000000Wei)
            var gasPrice = "0x"+_gasprice.toString(16);
            var gasLimit = "0x"+_gaslimit.toString(16);

            //_amount = parseFloat(_amount);
            // var amount = _amount*1000000000000000000;
            // amount = "0x"+amount.toString(16);
            var amount = _amount;

            var data = "0x";
            if(_data) data = _data;

            var rawTx = {};
            if(nonce)
            {
                rawTx = {
                    nonce: nonce,  //
                    gasPrice: gasPrice,  //20Gwei => Gwei(1Gwei = 0.000000001Ether = 1000000000Wei)
                    gasLimit: gasLimit, //21000=>5208  8888888=>87A238  4700000=>47B760
                    to: _to,
                    value: amount,   // 0.5Eth
                    data: data
                }
            }
            else
            {
                rawTx = {
                    gasPrice: gasPrice,  //20Gwei => Gwei(1Gwei = 0.000000001Ether = 1000000000Wei)
                    gasLimit: gasLimit, //21000=>5208  8888888=>87A238  4700000=>47B760
                    to: _to,
                    value: amount,   // 0.5Eth
                    data: data
                }
            }
            var tx = new Tx(rawTx)
            tx.sign(privateKey);


            var serializedTx = tx.serialize()
            var signData = serializedTx.toString('hex');
            return signData;
            //用于JSON_RPC中的eth_sendRawTransaction()发送流通使用
        }
    }
};

export default FanweETH;