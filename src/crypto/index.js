import 'cryptojs/cryptojs';
import  CryptoJS from 'crypto-js/crypto-js';
var fanwe_crypto = {

    md5:function(str){
        return Crypto.MD5(str).toString();
    },
    /**
     * [encrypt 加密]
     * @return {[type]} [description]
     */
    enc: function (content,key) {
        if(!key)
            key = "wallet!^@$!@";
        var encryptResult = Crypto.AES.encrypt(content, key, {
            iv: key,
            mode: Crypto.mode.ECB(),
            padding: Crypto.pad.Pkcs7
        });
        return encryptResult;
    },
    /**
     * [decrypt 解密]
     * @return {[type]} [description]
     */
    dec:function (content,key) {
        if(!key)
            key = "xslwallet*&^(";
        var bytes = Crypto.AES.decrypt(content, key, {
            iv: key,
            mode: Crypto.mode.CBC(),
            padding: Crypto.pad.pkcs7
        });
        var decryptResult = bytes.toString();
        return decryptResult;
    },
    //新的加密
    encrypt:function (content,key) {
        if(!key)
            key = "wallet!^@$!@";
        var iv =  CryptoJS.enc.Utf8.parse("!$*@7fs8f9)(&^q1");
        var encryptContent = CryptoJS.AES.encrypt(content, key, {
            iv:iv,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        }).toString();
        return encryptContent;
    },
    decrypt:function(content,key){
        if(!key)
            key = "wallet!^@$!@";
        var iv =  CryptoJS.enc.Utf8.parse("!$*@7fs8f9)(&^q1");
        var decStr = "";
        try{
            decStr =  CryptoJS.AES.decrypt(content,key, {
                iv:iv,
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.ZeroPadding
            }).toString(CryptoJS.enc.Utf8);
        }catch(ex){
            return this.dec(content,key); //兼容旧的解密
            //decStr = null;
        }
        return decStr;
    }
};

export default fanwe_crypto;