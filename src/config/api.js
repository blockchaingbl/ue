const _baseUrl = process.env.API_ROOT
export default {
    wxLogin() {
        return _baseUrl + '?ctl=login&act=wx_login&itype=wawa_server'
    },
    mobileLogin() {
        return _baseUrl + '?ctl=login&act=do_login'
    },
    sendMobileVerify() {
        return _baseUrl + '?ctl=login&act=send_mobile_verify'
    },
    getIndex() {
        return _baseUrl + '?ctl=index&act=index'
    },
    getFocusVideo() {
        return _baseUrl + '?ctl=index&act=focus_video'
    },
    getSocietyList() {
        return _baseUrl + '?ctl=index&act=society&itype=weixin'
    },
    getUserInfo() {
        return _baseUrl + '?ctl=user&act=user_home&itype=weixin'
    },
    getAppInit() {
        return _baseUrl + '?ctl=app&act=init'
    },
    getIndexClassify() {
        return _baseUrl + '?ctl=index&act=classify'
    },
    getSvideo() {
        // 小视频列表
        return _baseUrl + '?ctl=svideo&act=index'
    },
    getUserHome() {
        // 会员中心
        return _baseUrl + '?ctl=user&act=user_home'
    },
    popProp() {
        // 送礼物
        return _baseUrl + '?ctl=deal&act=pop_prop'
    },
    weiboIndex() {
        // 送礼物
        return _baseUrl + '?ctl=weibo&act=index&itype=xr'
    },
    rankConsumption() {
        // 收入贡献榜
        return _baseUrl + '?ctl=rank&act=consumption'
    },
    rankContribution() {
        // 消费贡献榜
        return _baseUrl + '?ctl=rank&act=contribution'
    },
    userCenterProfit() {
        //个人中心收益
        return _baseUrl + '?ctl=user_center&act=profit'
    },
    userSearch() {
        // 搜索用户
        return _baseUrl + '?ctl=user&act=search&itype=weixin'
    },
    userFollow() {
        // 关注某个用户
        return _baseUrl + '?ctl=user&act=follow'
    },
    userExtractRecord() {
        // 个人中心收益领取记录
        return _baseUrl + '?ctl=user_center&act=extract_record'
    },
    userExchange() {
        //个人中心收益兑换
        return _baseUrl + '?ctl=user_center&act=exchange'
    },
    userDoExchange() {
        //个人中心收益兑换功能
        return _baseUrl + '?ctl=pay&act=do_exchange&itype=app'
    },
    userBindingAlipay() {
        //个人中心收益支付宝绑定
        return _baseUrl + '?ctl=user_center&act=binding_alipay'
    },
    userMoneyCarryAlipay() {
        //个人中心收益上链初始化
        return _baseUrl + '?ctl=user_center&act=money_carry_alipay'
    },
    userSubmitRefundAlipay() {
        //个人中心收益上链
        return _baseUrl + '?ctl=user_center&act=submit_refund_alipay'
    },
    userPublish_comment() {
        // 发表评论、点赞
        return _baseUrl + '?ctl=user&act=publish_comment&itype=xr'
    },
    delComment() {
        // 删除评论
        return _baseUrl + '?ctl=user&act=del_comment&itype=xr'
    },
    userContribution() {
        // 个人中心印票排行榜
        return _baseUrl + '?ctl=video&act=cont&itype=weixin'
    },
    user_centerGrade() {
        // 个人中心等级
        return _baseUrl + '?ctl=user_center&act=grade&itype=weixin'
    },
    loginLoginout() {
        // 退出登录
        return _baseUrl + '?ctl=login&act=login_out&itype=weixin'
    },
    settingsArticle_cate() {
        // 关于
        return _baseUrl + '?ctl=settings&act=article_cate'
    },
    articleIndex() {
        // 关于详情页
        return _baseUrl + '?ctl=article&act=index&itype=app'
    },
    settingsHelp() {
        // 帮助与反馈
        return _baseUrl + '?ctl=settings&act=help'
    },
    settingsFaq() {
        // 问题类型
        return _baseUrl + '?ctl=settings&act=faq'
    },
    settingsBlacklist() {
        // 黑名单
        return _baseUrl + '?ctl=settings&act=black_list'
    },
    userParCont() {
        // 个人中心直播间消费记录
        return _baseUrl + '?ctl=live&act=pay_cont'
    },
    payRecharge() {
        // 账户兑换
        return _baseUrl + '?ctl=pay&act=recharge'
    },
    userUser_focus() {
        // 粉丝列表
        return _baseUrl + '?ctl=user&act=user_focus&itype=weixin'
    },
    userUser_follow() {
        // 关注的用户
        return _baseUrl + '?ctl=user&act=user_follow&itype=weixin'
    },
    userUser_review() {
        // 直播回看列表
        return _baseUrl + '?ctl=user&act=user_review&itype=weixin'
    },
    shareSign() {
        // 微信分享签名
        return _baseUrl + '?ctl=share&act=sign&itype=weixin'
    },
    settingSecurity() {
        // 设置-账户与安全
        return _baseUrl + '?ctl=settings&act=security'
    },
    svideoVideo() {
        // 我的小视频
        return _baseUrl + '?ctl=svideo&act=video'
    },
    society_app_president() {
        // 工会详情-主播列表
        return _baseUrl + '?ctl=society_app&act=society_details'
    },
    society_out() {
        // 工会详情-退出公会
        return _baseUrl + '?ctl=society_app&act=society_out'
    },
    society_join() {
        // 工会详情-加入公会
        return _baseUrl + '?ctl=society_app&act=society_join'
    },
    join_check() {
        // 工会详情-会长审核加入公会用户
        return _baseUrl + '?ctl=society_app&act=join_check'
    },
    out_check() {
        // 工会详情-会长审核退会用户
        return _baseUrl + '?ctl=society_app&act=out_check'
    },
    society_member_del() {
        // 工会详情-会长踢出会员
        return _baseUrl + '?ctl=society_app&act=member_del'
    },
    create_society_app() {
        // 创建公会
        return _baseUrl + '?ctl=society_app&act=create'
    },
    society_app_reapply() {
        // 重新审核公会
        return _baseUrl + '?ctl=society_app&act=society_agree'
    },
    userSet_black() {
        // 拉黑
        return _baseUrl + '?ctl=user&act=set_black'
    },
    isLogin() {
        // 判断当前用户是否登录
        return _baseUrl + '?ctl=login&act=is_login&itype=weixin'
    },
    userEdit() {
        // 用户资料编辑
        return _baseUrl + '?ctl=user_center&act=user_edit'
    },
    doUpload() {
        // 上传图片
        return '../../m.php?m=PublicFile&a=do_upload&upload_type=1&dir=image'
    },
    doUpdate() {
        // 手机登录更新（昵称、性别、头像）
        return _baseUrl + '?ctl=login&act=do_update'
    },
    userSave() {
        // 保存账户信息
        return _baseUrl + '?ctl=user_center&act=user_save'
    },
    user_center_authent() {
        // 认证初始化
        return _baseUrl + '?ctl=user_center&act=authent'
    },
    user_center_attestation() {
        // 提交认证
        return _baseUrl + '?ctl=user_center&act=attestation'
    },
    tipoffType() {
        // 获得举报类型列表
        return _baseUrl + '?ctl=app&act=tipoff_type'
    },
    userTipoff() {
        // 举报用户
        return _baseUrl + '?ctl=user&act=tipoff'
    },
    loginDoUpdate() {
        // 手机注册
        return _baseUrl + '?ctl=login&act=do_update'
    },
    payWeixin() {
        // 微信支付
        return _baseUrl + '?ctl=pay&act=weixin_pay&itype=weixin'
    },
    addVideoCount() {
        // 增加播放次数
        return _baseUrl + '?ctl=weibo&act=add_video_count&itype=xr'
    },
    dollsInfo() {
        // 娃娃详情（供APP内使用）
        return _baseUrl + '?ctl=dolls&act=detail&itype=wawa_server'
    },
    gameSuccessRecord() {
        // 抓中记录
        return _baseUrl + '?ctl=dolls&act=game_succeed_record&itype=wawa_server'
    },
    gameLeaderboard() {
        // 娃娃详情
        return _baseUrl + '?ctl=dolls&act=game_leaderboard&itype=wawa_server'
    },
    //娃娃领取详情
    pickupwawa() {
        return _baseUrl + '?ctl=user&act=doll_detail&itype=wawa_server'
    },
    //领取娃娃请求提交
    pickUpSubmit() {
        return _baseUrl + '?ctl=pay&act=get_free&itype=wawa_server'
    },
    //收货地址
    addrList() {
        return _baseUrl + '?ctl=user&act=consignee&itype=wawa'
    },
    //添加新收货地址
    newAddress() {
        return _baseUrl + '?ctl=user&act=save_consignee&itype=wawa'
    },
    //设置默认地址
    setDefaultAddr() {
        return _baseUrl + '?ctl=user&act=set_default_consignee&itype=wawa'
    },
    //删除地址
    delAddrs() {
        return _baseUrl + '?ctl=user&act=del_consignee&itype=wawa'
    },

    // 首页娃娃机列表
    indexIndex() {
        return _baseUrl + '?ctl=index&act=index&itype=wawa_server'
    },

    // 娃娃机直播间信息
    getVideo() {
        return _baseUrl + '?ctl=video&act=get_video2&itype=wawa_server&sdk_type=h5'
    },
    // 我的娃娃列表
    dollsIndex() {
        return _baseUrl + '?ctl=user&act=my_doll_list&itype=wawa_server'
    },
    // 他的娃 
    othersDollsInfo() {
        return _baseUrl + '?ctl=user&act=his_doll_list&itype=wawa_server'
    },
    // 我的娃娃-订单详情
    dollsOrder() {
        return _baseUrl + '?ctl=user&act=doll_detail&itype=wawa'
    },
    // 预约或开始游戏
    gameStart() {
        return _baseUrl + '?ctl=game&act=start&itype=wawa_server'
    },
    // 取消预约
    gameCancelReserve() {
        return _baseUrl + '?ctl=game&act=cancel_reserve&itype=wawa_server'
    },
    // 游戏结束
    gameEnd() {
        return _baseUrl + '?ctl=game&act=end&itype=wawa_server'
    },
    // 游戏退出
    gameFinish() {
        return _baseUrl + '?ctl=game&act=finish&itype=wawa_server'
    },
    // 会员中心
    userInfo() {
        return _baseUrl + '?ctl=user&act=userinfo'
    },
    // 账单-兑换明细
    billRechargeDetail() {
        return _baseUrl + '?ctl=pay&act=payment_list&itype=wawa'
    },
    // 账单-消费明细
    billConsumeDetail() {
        return _baseUrl + '?ctl=pay&act=bill_list&itype=wawa'
    },
    // 设置-关于我们
    settingsArticle_cate() {
        return _baseUrl + '?ctl=settings&act=article_cate'
    },
    // 邀请好友
    inviteFriends() {
        return _baseUrl + '?ctl=invite&act=invite_history&itype=wawa_server'
    },
    //输入邀请码
    inviteCode() {
        return _baseUrl + '?ctl=invite&act=invite_validate&itype=wawa_server'
    },
    // 兑换
    recharge() {
        return _baseUrl + '?ctl=pay&act=recharge'
    },
    // 兑换-支付功能
    rechargePay() {
        return _baseUrl + '?ctl=pay&act=pay'
    },
    // 兑换-结果
    rechargeResult() {
        return _baseUrl + '?ctl=pay&act=pay_result&itype=wawa'
    },
    // 发现
    findIndex() {
        return _baseUrl + '?ctl=index&act=discovery&itype=wawa'
    },
    // 退出登录
    loginout() {
        return _baseUrl + '?ctl=login&act=loginout'
    },
    // 问题列表
    gameQuestion() {
        return _baseUrl + '?ctl=game&act=question&itype=wawa_server'
    },
    // 提交反馈问题
    gameSaveQuestion() {
        return _baseUrl + '?ctl=game&act=save_question&itype=wawa_server'
    },
    // 宝箱详情
    boxInfo() {
        return _baseUrl + '?ctl=user&act=score_box&itype=wawa_server'
    },
    //宝箱抽奖
    saveScoreBox() {
        return _baseUrl + '?ctl=user&act=save_score_box_log&itype=wawa_server'
    },
    //兑换记录
    integralRecordList() {
        return _baseUrl + '?ctl=user&act=exchange_score_log&itype=wawa_server'
    },
    //兑换列表
    getExchangeList() {
        return _baseUrl + '?ctl=user&act=exchange_score&itype=wawa_server'
    },
    //兑换钻石
    saveExchangeLog() {
        return _baseUrl + '?ctl=user&act=save_exchange_score_log&itype=wawa_server'
    },
    //兑换实物列表
    getThingList() {
        return _baseUrl + '?ctl=user&act=exchange_thing_list&itype=wawa_server'
    },
    //兑换实物
    getExchangeThing() {
        return _baseUrl + '?ctl=user&act=save_exchange_thing&itype=wawa_server'
    },
    //实物详情
    thingsInfo() {
        return _baseUrl + '?ctl=user&act=exchange_thing&itype=wawa_server'
    },
    //签到初始数据
    getSignData() {
        return _baseUrl + '?ctl=user&act=get_sign_in&itype=wawa_server'
    },
    //签到请求
    getSignIn() {
        return _baseUrl + '?ctl=user&act=sign_in&itype=wawa_server'
    },
    //娃娃兑换钻石
    dollRxchange() {
        return _baseUrl + '?ctl=pay&act=exchange_diamonds&itype=wawa_server'
    },
    //我的实物列表
    thingsIndex() {
        return _baseUrl + '?ctl=user&act=my_thing_list&itype=wawa_server'
    },
    //提取娃娃详情
    pickupthings() {
        return _baseUrl + '?ctl=user&act=thing_detail&itype=wawa_server'
    },
    //领取实物请求
    pickUpThingSubmit() {
        return _baseUrl + '?ctl=pay&act=get_thing_free&itype=wawa_server'
    },
    //实物订单信息
    thingsOrder() {
        return _baseUrl + '?ctl=user&act=thing_detail&itype=wawa_server'
    },
    //总运费
    totalFreight() {
        return _baseUrl + '?ctl=user&act=total_freight&itype=wawa_server'
    }
}