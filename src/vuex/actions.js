/**
 * Created by Administrator on 2017/6/17.
 */
const setScrollloding = ({ commit }, scrollloding) => {
    commit('set_scrollloding', scrollloding)
}
const setToken = ({ commit }, token) => {
    commit('set_token', token)
}
const setInit = ({ commit }, init) => {
    commit('set_init', init)
}
const setSign = ({ commit }, sign) => {
    commit('set_sign', sign)
}
const setCoinInfo = ({ commit }, coin_info) => {
    commit('set_coin_info', coin_info)
}
const refreshCoinType =  ({ commit }) => {
    commit('refresh_coin_type')
}



export {setScrollloding, setToken,setInit,setSign,setCoinInfo,refreshCoinType}
