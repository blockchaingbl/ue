import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);
/*
  最初稿 :views
  国际版: views_international
*/
var style='views';

export default new Router({routes:[
        {
            path: '/',
            name: 'index',
            component: resolve => require(['@/'+style+'/index/index'], resolve),
            meta: {
                title: '首页',
                requireAuth: false,
                pageType: 'index'
            }
        },
        {
            path: '/login',
            name: 'loginIndex',
            component: resolve => require(['@/'+style+'/login/index'], resolve),
            meta: {
                title: '登录',
                requireAuth: false,
                pageType: 'loginIndex'
            }
        },
        {
            path: '/login/pwdreset',
            name: 'pwdReset',
            component: resolve => require(['@/'+style+'/login/pwdreset'], resolve),
            meta: {
                title: '重置密码',
                requireAuth: false,
                pageType: 'pwdReset'
            }
        },
        {
            path: '/login/auth',
            name: 'login_auth',
            component: resolve => require(['@/'+style+'/login/auth'], resolve),
            meta: {
                title: '授权登录',
                requireAuth: false,
                pageType: 'login_auth'
            }
        },
        {
            path: '/user/center',
            name: 'userCenter',
            component: resolve => require(['@/'+style+'/user/index'], resolve),
            meta: {
                title: '个人中心',
                requireAuth: false,
                pageType: 'userCenter'
            }
        },
        {
            path: '/user/wallet',
            name: 'userWallet',
            component: resolve => require(['@/'+style+'/user/wallet'], resolve),
            meta: {
                title: '我的钱包',
                requireAuth: false,
                pageType: 'userWallet'
            }
        },
        {
            path: '/user/wallet/send',
            name: 'userWalletsend',
            component: resolve => require(['@/'+style+'/user/walletsend'], resolve),
            meta: {
                title: '我的钱包-发送',
                requireAuth: false,
                pageType: 'userWalletsend'
            }
        },
        {
            path: '/user/wallet/recieve',
            name: 'userWalletsend',
            component: resolve => require(['@/'+style+'/user/walletrecieve'], resolve),
            meta: {
                title: '我的钱包-接收',
                requireAuth: false,
                pageType: 'userWalletrecieve'
            }
        },
        {
            path: '/user/wallet/create',
            name: 'user_wallet_create',
            component: resolve => require(['@/'+style+'/user/walletcreate'], resolve),
            meta: {
                title: '创建钱包',
                requireAuth: false,
                pageType: 'user_wallet_create'
            }
        },
        {
            path: '/user/wallet/import',
            name: 'user_wallet_import',
            component: resolve => require(['@/'+style+'/user/walletimport'], resolve),
            meta: {
                title: '导入钱包',
                requireAuth: false,
                pageType: 'user_wallet_import'
            }
        },
        {
            path: '/user/wallet/manage',
            name: 'user_wallet_manage',
            component: resolve => require(['@/'+style+'/user/walletmanage'], resolve),
            meta: {
                title: '钱包管理',
                requireAuth: false,
                pageType: 'user_wallet_manage'
            }
        },
        {
            path: '/user/setpay',
            name: 'userSetpay',
            component: resolve => require(['@/'+style+'/user/setpay'], resolve),
            meta: {
                title: '支付设置',
                requireAuth: false,
                pageType: 'userSetpay'
            }
        },
        {
            path: '/user/setpay/setalipay',
            name: 'userSetalipay',
            component: resolve => require(['@/'+style+'/user/setalipay'], resolve),
            meta: {
                title: '支付设置-支付宝',
                requireAuth: false,
                pageType: 'userSetalipay'
            }
        },
        {
            path: '/user/setpay/setwechat',
            name: 'userSetwechat',
            component: resolve => require(['@/'+style+'/user/setwechat'], resolve),
            meta: {
                title: '支付设置-微信',
                requireAuth: false,
                pageType: 'userSetwechat'
            }
        },
        {
            path: '/user/setpay/setbank',
            name: 'userSetbank',
            component: resolve => require(['@/'+style+'/user/setbank'], resolve),
            meta: {
                title: '支付设置-银行卡',
                requireAuth: false,
                pageType: 'userSetbank'
            }
        },
        {
            path: '/user/edit/security',
            name: 'editSecurity',
            component: resolve => require(['@/'+style+'/user/security'], resolve),
            meta: {
                title: '密码设置',
                requireAuth: false,
                pageType: 'editSecurity'
            }
        },
        {
            path: '/user/setting',
            name: 'userSetting',
            component: resolve => require(['@/'+style+'/user/setting'], resolve),
            meta: {
                title: '设置',
                requireAuth: false,
                pageType: 'userSetting'
            }
        },
        {
            path: '/mine/center',
            name: 'mineCenter',
            component: resolve => require(['@/'+style+'/mine/index'], resolve),
            meta: {
                title: '挖矿首页',
                requireAuth: false,
                pageType: 'mineCenter'
            }
        },
        {
            path: '/task/center',
            name: 'taskCenter',
            component: resolve => require(['@/'+style+'/task/index'], resolve),
            meta: {
                title: '任务首页',
                requireAuth: false,
                pageType: 'taskCenter'
            }
        },
        {
            path: '/task/invitation',
            name: 'taskInvitation',
            component: resolve => require(['@/'+style+'/task/invitation'], resolve),
            meta: {
                title: '任务邀请',
                requireAuth: false,
                pageType: 'taskInvitation'
            }
        },
        {
            path: '/deals/center',
            name: 'dealsCenter',
            component: resolve => require(['@/'+style+'/deals/index'], resolve),
            meta: {
                title: '流通首页',
                requireAuth: false,
                pageType: 'dealsCenter'
            }
        },
        {
            path: '/deals/push',
            name: 'dealsPush',
            component: resolve => require(['@/'+style+'/deals/push'], resolve),
            meta: {
                title: '流通出让',
                requireAuth: false,
                pageType: 'dealsPush'
            }
        },
        {
            path: '/deals/buy',
            name: 'dealsBuy',
            component: resolve => require(['@/'+style+'/deals/buy'], resolve),
            meta: {
                title: '流通受让',
                requireAuth: false,
                pageType: 'dealsBuy'
            }
        },
        {
            path: '/deals/record',
            name: 'dealsRecord',
            component: resolve => require(['@/'+style+'/deals/record'], resolve),
            meta: {
                title: '流通记录',
                requireAuth: false,
                pageType: 'dealsRecord'
            }
        },
        {
            path: '/deals/sell',
            name: 'dealsSell',
            component: resolve => require(['@/'+style+'/deals/sell'], resolve),
            meta: {
                title: '流通出让',
                requireAuth: false,
                pageType: 'dealsSell'
            }
        },
        {
            path: '/user/selfmoney',
            name: 'userSelfmoney',
            component: resolve => require(['@/'+style+'/user/selfmoney'], resolve),
            meta: {
                title: '我的资产',
                requireAuth: false,
                pageType: 'userSelfbcty'
            }
        },
        {
            path: '/user/selfbcty',
            name: 'userSelfbcty',
            component: resolve => require(['@/'+style+'/user/selfbcty'], resolve),
            meta: {
                title: '我的资产',
                requireAuth: false,
                pageType: 'userSelfbcty'
            }
        },
        {
            path: '/user/withdraw',
            name: 'userWithdraw',
            component: resolve => require(['@/'+style+'/user/withdraw'], resolve),
            meta: {
                title: '我要上链',
                requireAuth: false,
                pageType: 'userWithdraw'
            }
        },
        {
            path: '/user/withdrawlist',
            name: 'userWithdrawlist',
            component: resolve => require(['@/'+style+'/user/withdrawlist'], resolve),
            meta: {
                title: '上链记录',
                requireAuth: false,
                pageType: 'userWithdrawlist'
            }
        },
        {
            path: '/incharge',
            name: 'inchargeIndex',
            component: resolve => require(['@/'+style+'/incharge/index'], resolve),
            meta: {
                title: '兑换',
                requireAuth: false,
                pageType: 'inchargeIndex'
            }
        },
        {
            path: '/error/404',
            name: '404',
            component: resolve => require(['@/'+style+'/error/404'], resolve),
            meta: {
                title: '404',
                requireAuth: false,
                pageType: '404'
            }
        },
        {
            path: '/error/504',
            name: '504',
            component: resolve => require(['@/'+style+'/error/504'], resolve),
            meta: {
                title: '暂无网络-504',
                requireAuth: false,
                pageType: '504'
            }
        },
        //上链钱包相关路由
        {
            path: '/wallet',
            name: 'wallet',
            component: resolve => require(['@/'+style+'/wallet/index'], resolve),
            meta: {
                title: '我的钱包',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/create',
            name: 'wallet_create',
            component: resolve => require(['@/'+style+'/wallet/create'], resolve),
            meta: {
                title: '创建钱包',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/import',
            name: 'wallet_import',
            component: resolve => require(['@/'+style+'/wallet/import'], resolve),
            meta: {
                title: '导入钱包',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/detail/:coin',
            name: 'wallet_detail',
            component: resolve => require(['@/'+style+'/wallet/detail'], resolve),
            meta: {
                title: '钱包流通',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/detail',
            name: 'wallet_detail',
            component: resolve => require(['@/'+style+'/wallet/detail'], resolve),
            meta: {
                title: '钱包流通',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/send/:coin',
            name: 'wallet_send',
            component: resolve => require(['@/'+style+'/wallet/send'], resolve),
            meta: {
                title: '转出',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/recieve/:coin',
            name: 'wallet_recieve',
            component: resolve => require(['@/'+style+'/wallet/recieve'], resolve),
            meta: {
                title: '转入',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/manage',
            name: 'wallet_manage',
            component: resolve => require(['@/'+style+'/wallet/manage'], resolve),
            meta: {
                title: '钱包管理',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
        {
            path: '/wallet/manage/:address',
            name: 'wallet_manage_item',
            component: resolve => require(['@/'+style+'/wallet/manage_item'], resolve),
            meta: {
                title: '钱包管理',
                requireAuth: false,
                pageType: 'wallet_chain'
            }
        },
//国际版上链钱包
        //上链钱包相关路由
        {
            path: '/wallet_international',
            name: 'wallet_international',
            component: resolve => require(['@/'+style+'/wallet_international/index'], resolve),
            meta: {
                title: '我的钱包',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/create',
            name: 'wallet_international_create',
            component: resolve => require(['@/'+style+'/wallet_international/create'], resolve),
            meta: {
                title: '创建钱包',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/import',
            name: 'wallet_international_import',
            component: resolve => require(['@/'+style+'/wallet_international/import'], resolve),
            meta: {
                title: '导入钱包',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/detail/:coin',
            name: 'wallet_international_detail',
            component: resolve => require(['@/'+style+'/wallet_international/detail'], resolve),
            meta: {
                title: '钱包流通',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/detail',
            name: 'wallet_international_detail',
            component: resolve => require(['@/'+style+'/wallet_international/detail'], resolve),
            meta: {
                title: '钱包流通',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/send/:coin',
            name: 'wallet_international_send',
            component: resolve => require(['@/'+style+'/wallet_international/send'], resolve),
            meta: {
                title: '转出',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/recieve/:coin',
            name: 'wallet_international_recieve',
            component: resolve => require(['@/'+style+'/wallet_international/recieve'], resolve),
            meta: {
                title: '转入',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/manage',
            name: 'wallet_international_manage',
            component: resolve => require(['@/'+style+'/wallet_international/manage'], resolve),
            meta: {
                title: '钱包管理',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },
        {
            path: '/wallet_international/manage/:address',
            name: 'wallet_international_manage_item',
            component: resolve => require(['@/'+style+'/wallet_international/manage_item'], resolve),
            meta: {
                title: '钱包管理',
                requireAuth: false,
                pageType: 'wallet_international_chain'
            }
        },

        //用户关系
        {
            path: '/relations/friends',
            name: 'relationsFriends',
            component: resolve => require(['@/'+style+'/relations/friends'], resolve),
            meta: {
                title: '好友',
                requireAuth: false,
                pageType: 'relationsFriends'
            }
        },
        {
            path: '/mine/steal/:user_id',
            name: 'mineSteal',
            component: resolve => require(['@/'+style+'/mine/steal'], resolve),
            meta: {
                title: '偷币',
                requireAuth: false,
                pageType: 'mineSteal'
            }
        },
        {
            path: '/relations/myinvite',
            name: 'relationsMyinvite',
            component: resolve => require(['@/'+style+'/relations/myinvite'], resolve),
            meta: {
                title: '我邀请的好友',
                requireAuth: false,
                pageType: 'relationsMyinvite'
            }
        },
        {
            path: '/market/index',
            name: 'marketIndex',
            component: resolve => require(['@/'+style+'/market/index'], resolve),
            meta: {
                title: '行情',
                requireAuth: false,
                pageType: 'marketIndex'
            }
        },
        {
            path: '/market/detail',
            name: 'marketDetail',
            component: resolve => require(['@/'+style+'/market/detail'], resolve),
            meta: {
                title: '行情详情',
                requireAuth: false,
                pageType: 'marketDetail'
            }
        },
        {
            path: '/article/index',
            name: 'articleIndex',
            component: resolve => require(['@/'+style+'/article/index'], resolve),
            meta: {
                title: '资讯',
                requireAuth: false,
                pageType: 'articleIndex'
            }
        },
        {
            path: '/article/:id',
            name: 'articleDetail',
            component: resolve => require(['@/'+style+'/article/detail'], resolve),
            meta: {
                title: '资讯详情',
                requireAuth: false,
                pageType: 'articleDetail'
            }
        },
        {
            path: '/lock_transfer/distribute',
            name: 'lockTransferDistribute',
            component: resolve => require(['@/views/lock_transfer/distribute'], resolve),
            meta: {
                title: '锁仓转出',
                requireAuth: false,
                pageType: 'lockTransferDistribute'
            }
        },
        {
            path: '/lock_transfer/grant/:coin_type',
            name: 'lock_transfer_grant',
            component: resolve => require(['@/views/lock_transfer/grant'], resolve),
            meta: {
                title: '转出',
                requireAuth: false,
                pageType: 'lock_transfer_grant'
            }
        },
        {
            path: '/lock_transfer/lock_transfer_success',
            name: 'lock_transfer_success',
            component: resolve => require(['@/views/lock_transfer/success'], resolve),
            meta: {
                title: '',
                requireAuth: false,
                pageType: 'lock_transfer_success'
            }
        },
        {
            path: '/lock_transfer/trans',
            name: 'trans',
            component: resolve => require(['@/views/lock_transfer/trans'], resolve),
            meta: {
                title: '锁仓资产',
                requireAuth: false,
                pageType: 'trans'
            }
        },
        {
            path: '/candy',
            name: 'candyIndex',
            component: resolve => require(['@/views/candy/index'], resolve),
            meta: {
                title: '糖果',
                requireAuth: false,
                pageType: 'candyIndex'
            }
        },
        {
            path: '/candy/distribute',
            name: 'candyDistribute',
            component: resolve => require(['@/views/candy/distribute'], resolve),
            meta: {
                title: '发糖果',
                requireAuth: false,
                pageType: 'candyDistribute'
            }
        },
        {
            path: '/candy/distribute_detail',
            name: 'distribute_detail',
            component: resolve => require(['@/views/candy/distribute_detail'], resolve),
            meta: {
                title: '领取详情',
                requireAuth: false,
                pageType: 'distribute_detail'
            }
        },
        {
            path: '/candy/grant',
            name: 'candy_grant',
            component: resolve => require(['@/views/candy/grant'], resolve),
            meta: {
                title: '发糖果',
                requireAuth: false,
                pageType: 'candy_grant'
            }
        },
        {
            path: '/candy/candy_success',
            name: 'candy_success',
            component: resolve => require(['@/views/candy/candy_success'], resolve),
            meta: {
                title: '',
                requireAuth: false,
                pageType: 'candy_success'
            }
        },
        {
            path: '/candy/trans',
            name: 'trans',
            component: resolve => require(['@/views/candy/trans'], resolve),
            meta: {
                title: '我的糖果',
                requireAuth: false,
                pageType: 'trans'
            }
        },
        {
            path: '/red_packet/index',
            name: 'redPacketIndex',
            component: resolve => require(['@/views/red_packet/index'], resolve),
            meta: {
                title: '我的红包',
                requireAuth: false,
                pageType: 'redPacketIndex'
            }
        },
        {
            path: '/red_packet/luckPacket',
            name: 'luckPacket',
            component: resolve => require(['@/views/red_packet/luckPacket'], resolve),
            meta: {
                title: '拼手气红包',
                requireAuth: false,
                pageType: 'luckPacket'
            }
        },
        {
            path: '/red_packet/normalPacket',
            name: 'normalPacket',
            component: resolve => require(['@/views/red_packet/normalPacket'], resolve),
            meta: {
                title: '普通红包',
                requireAuth: false,
                pageType: 'normalPacket'
            }
        },
        {
            path: '/red_packet/packetList',
            name: 'packetList',
            component: resolve => require(['@/views/red_packet/packetList'], resolve),
            meta: {
                title: '红包列表',
                requireAuth: false,
                pageType: 'packetList'
            }
        },
        {
            path: '/red_packet/receiveDetail',
            name: 'receiveDetail',
            component: resolve => require(['@/views/red_packet/receiveDetail'], resolve),
            meta: {
                title: '收到的红包详情',
                requireAuth: false,
                pageType: 'receiveDetail'
            }
        },
        {
            path: '/red_packet/sendDetail',
            name: 'sendDetail',
            component: resolve => require(['@/views/red_packet/sendDetail'], resolve),
            meta: {
                title: '发出的红包详情',
                requireAuth: false,
                pageType: 'sendDetail'
            }
        },
        {
            path: '/about',
            name: 'about',
            component: resolve => require(['@/views/about/about'], resolve),
            meta: {
                title: '关于我们',
                requireAuth: false,
                pageType: 'about'
            }
        },
        {
            path: '/connect',
            name: 'connect',
            component: resolve => require(['@/views/about/connect'], resolve),
            meta: {
                title: '意见反馈',
                requireAuth: false,
                pageType: 'connect'
            }
        },
        {
            path: '/connect_text',
            name: 'connect_text',
            component: resolve => require(['@/views/about/connect_text'], resolve),
            meta: {
                title: '联系我们',
                requireAuth: false,
                pageType: 'connect'
            }
        },
        {
            path: '/notice',
            name: 'notice',
            component: resolve => require(['@/views/about/notice'], resolve),
            meta: {
                title: '系统公告',
                requireAuth: false,
                pageType: 'connect'
            }
        },
        //demo示例页面

        {
            path: '/demo',
            name: 'demo',
            component: resolve => require(['@/'+style+'/demo/index'], resolve),
            meta: {
                title: 'demo',
                requireAuth: false,
                pageType: 'demo'
            }
        },
        {
            path: '/mine_machine',
            name: 'mineMachine',
            component: resolve => require(['@/views/mine_machine/index'], resolve),
            meta: {
                title: '矿机商城',
                requireAuth: false,
                pageType: 'mineMachine'
            }
        },
        {
            path: '/mine_machine/self',
            name: 'selfMachine',
            component: resolve => require(['@/views/mine_machine/self_machine'], resolve),
            meta: {
                title: '我的矿机',
                requireAuth: false,
                pageType: 'selfMachine'
            }
        },
        {
            path: '/mine_machine/history',
            name: 'history',
            component: resolve => require(['@/views/mine_machine/history'], resolve),
            meta: {
                title: '历史矿机',
                requireAuth: false,
                pageType: 'history'
            }
        },
        {
            path: '/mine_machine/detail',
            name: 'machine_detail',
            component: resolve => require(['@/views/mine_machine/machine_detail'], resolve),
            meta: {
                title: '矿机详情',
                requireAuth: false,
                pageType: 'machine_detail'
            }
        },
        {
            path: '/mine_machine/self_detail',
            name: 'machine_self_detail',
            component: resolve => require(['@/views/mine_machine/self_detail'], resolve),
            meta: {
                title: '已购矿机详情',
                requireAuth: false,
                pageType: 'machine_self_detail'
            }
        },
        {
            path: '/transfer/send/:id',
            name: 'transferSend',
            component: resolve => require(['@/views/transfer/send'], resolve),
            meta: {
                title: '转出',
                requireAuth: false,
                pageType: 'machine_self_detail'
            }
        },
        {
            path: '/transfer/success',
            name: 'transferSuccess',
            component: resolve => require(['@/views/transfer/success'], resolve),
            meta: {
                title: '转出成功',
                requireAuth: false,
                pageType: 'transferSuccess'
            }
        },
        {
            path: '/transfer/to',
            name: 'transferTo',
            component: resolve => require(['@/views/transfer/to'], resolve),
            meta: {
                title: '转出',
                requireAuth: false,
                pageType: 'transferTo'
            }
        },
        {
            path: '/transfer/receive',
            name: 'transferReceive',
            component: resolve => require(['@/views/transfer/receive'], resolve),
            meta: {
                title: '转入',
                requireAuth: false,
                pageType: 'transferReceive'
            }
        },
        {
            path: '/transfer/order',
            name: 'transferOrder',
            component: resolve => require(['@/views/transfer/order'], resolve),
            meta: {
                title: '转入记录',
                requireAuth: false,
                pageType: 'transferOrder'
            }
        },
        {
            path: '/transfer/order_out',
            name: 'transferOrderOut',
            component: resolve => require(['@/views/transfer/order_out'], resolve),
            meta: {
                title: '转出记录',
                requireAuth: false,
                pageType: 'transferOrderOut'
            }
        },
        {
            path: '/connect/huifu',
            name: 'connectHuifu',
            component: resolve => require(['@/views/about/connet_lists'], resolve),
            meta: {
                title: '我的反馈',
                requireAuth: false,
                pageType: 'connectHuifu'
            }
        },
        {
            path: '/society/apply',
            name: 'societyApply',
            component: resolve => require(['@/views/society/apply'], resolve),
            meta: {
                title: '申请成立社群',
                requireAuth: false,
                pageType: 'societyApply'
            }
        },
        {
            path: '/society/apply_node',
            name: 'societyApplyNode',
            component: resolve => require(['@/views/society/apply_node'], resolve),
            meta: {
                title: '行业节点',
                requireAuth: false,
                pageType: 'societyApplyNode'
            }
        },
        {
            path: '/society/choice',
            name: 'societyChoice',
            component: resolve => require(['@/views/society/choice'], resolve),
            meta: {
                title: '社群建设',
                requireAuth: false,
                pageType: 'societyApplyNode'
            }
        },


    ]});
/*
  router.beforeEach((to, from, next) => {
    const list = ['home', 'group', 'user']    // 将需要切换效果的路由名称组成一个数组
    const toName = to.name    // 即将进入的路由名字
    const fromName = from.name    // 即将离开的路由名字
    const toIndex = list.indexOf(toName)    // 进入下标
    const fromIndex = list.indexOf(fromName)   // 离开下标
    let direction = ''

    if (toIndex > -1 && fromIndex > -1) {   // 如果下标都存在
      if (toIndex < fromIndex) {          // 如果进入的下标小于离开的下标，那么是左滑
        direction = 'left'
      } else {
        direction = 'right'         // 如果进入的下标大于离开的下标，那么是右滑
      }
    }

    store.state.viewDirection = direction  //这里使用vuex进行赋值

    return next()
  })*/