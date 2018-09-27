/**
 * Created by Administrator on 2017/6/17.
 */

const getScrollloding = state => {
    return state.scrollloding
};
const getToken = state => {
    return state.token
};
const getInit = state => {
    return state.init
};

const getSign = state => {
    return state.sign
};

const coinInfo = state => {
    return state.coin_info
};
export {getScrollloding, getToken,getInit,getSign,coinInfo}
