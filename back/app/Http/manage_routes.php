<?php

$app->loadConfig("manage_auth","auth");
$app->loadConfig("manage");

$app->routeMiddleware([
    'auth.manage' => App\Http\Middleware\Manage\AdminAuthMiddleware::class
]);

$app->middleware([
    Illuminate\Cookie\Middleware\EncryptCookies::class,
    Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    Illuminate\Session\Middleware\StartSession::class,
    Illuminate\View\Middleware\ShareErrorsFromSession::class
]);

$app->group([
    'namespace' => 'App\Http\Controllers\Manage',
    'middleware' => [
        App\Http\Middleware\Manage\FanweAdminCsrfToken::class
    ]
], function() use ($app)
{
    //首页
    $app->get('/', ['as'=>"index",'uses'=>'IndexController@index']);
    $app->get('repairblock', ['as'=>"index",'uses'=>'IndexController@repairblock']);
    $app->get('to_update_mypwd', ['as'=>"to_update_mypwd",'uses'=>'IndexController@to_update_mypwd']);
    $app->any('update_my_pwd', ['as'=>"update_my_pwd",'uses'=>'IndexController@update_my_pwd']);

    //登录
    $app->get('login', ['as'=>"login",'uses'=>'AuthController@login']);
    $app->get("logout",['as'=>"logout",'uses'=>'AuthController@logout']);
    //$app->any("dologin",['as'=>"dologin",'uses'=>'AuthController@dologin']);

    //用户管理
    $app->get('user', ['as'=>"user",'uses'=>'UserController@index']);
    $app->any('get_userlist', ['as'=>"get_userlist",'uses'=>'UserController@getUserList']);
//    $app->post('user.lock',['as'=>'user.lock','uses'=>'UserController@lock']);
//    $app->post('user.money',['as'=>'user.money','uses'=>'UserController@money']);
//    $app->post('user.cp',['as'=>'user.cp','uses'=>'UserController@cp']);
    $app->get('user.money.detail/{id}',['as'=>'user.money.detail','uses'=>'UserController@detail']);
    $app->get('user.flow',['as'=>'user.flow','uses'=>'UserController@flow']);//流量主申请列表
    $app->post('user.flow.appeal',['as'=>'user.flow','uses'=>'UserController@flow_appeal']);
    $app->get('user.achievement/{id}',['as'=>'user.achievement','uses'=>'UserController@achievement']);
    $app->get('user.export',['as'=>'user.export','uses'=>'UserController@export']);

    $app->get('apply',['as'=>'apply','uses'=>'ApplyController@index']);
    $app->post('apply.apply',['as'=>'apply.apply','uses'=>'ApplyController@apply']);
    $app->get('apply.node',['as'=>'apply.node','uses'=>'ApplyController@node']);
    $app->post('apply.node.apply',['as'=>'apply.node.apply','uses'=>'ApplyController@node_apply']);

    //交易管理
    $app->get('transaction.putorder', ['as'=>"transaction.putorder",'uses'=>'TransactionController@putorder']);
    $app->get('transaction.saleorder', ['as'=>"transaction.saleorder",'uses'=>'TransactionController@saleorder']);
    $app->post('transaction.down.putorder', ['as'=>"transaction.saleorder",'uses'=>'TransactionController@down']);
    $app->post('transaction.appeal', ['as'=>"transaction.appeal",'uses'=>'TransactionController@appeal']);
    $app->get('exrate.index', ['as'=>"exrate.index",'uses'=>'ExRateController@index']);
    $app->post('exrate.change', ['as'=>"exrate.change",'uses'=>'ExRateController@change']);
    $app->get('trans',['as'=>'trans','uses'=>'TransController@index']);

    //报表统计
    $app->get('platformlog.allot', ['as'=>"platformlog.allot",'uses'=>'PlatformlogController@allot']);
    $app->get('platformlog.freeze',['as'=>'platformlog.freeze','uses'=>'PlatformlogController@freeze']);
    $app->post('platformlog.free',['as'=>'platformlog.free','uses'=>'PlatformlogController@free']);
    $app->get('platformlog.exchange',['as'=>'platformlog.exchange','uses'=>'PlatformlogController@exchange']);
    $app->get('platformlog.cplog',['as'=>'platformlog.cplog','uses'=>'PlatformlogController@cplog']);
    $app->get('platformlog.achievement',['as'=>'platformlog.achievement','uses'=>'PlatformlogController@achievement']);
    $app->get('platformlog.index',['as'=>'platformlog.index','uses'=>'PlatformlogController@index']);
    $app->get('platformlog.mincharge',['as'=>'platformlog.mincharge','uses'=>'PlatformlogController@mincharge']);
    $app->get('platformlog.minelog',['as'=>'platformlog.minelog','uses'=>'PlatformlogController@minelog']);
    $app->get('platformlog.detail',['as'=>'platformlog.detail','uses'=>'PlatformlogController@detail']);
    $app->get('platformlog.chart',['as'=>'platformlog.chart','uses'=>'PlatformlogController@chart']);

    $app->get('user.calc.detail/{id}',['as'=>'user.calc.detail','uses'=>'CalcController@index']);


    //提现管理
    $app->get('withdraw', ['as'=>"withdraw",'uses'=>'WithdrawController@index']);
    $app->post('withdraw.examine',['as'=>'withdraw.check','uses'=>'WithdrawController@examine']);
    $app->any('withdraw.export',['as'=>'withdraw.export','uses'=>'WithdrawController@export']);

    //内容管理
    $app->get('cms.article', ['as'=>"cms.article",'uses'=>'ArticleController@index']);
    $app->get('cms.adverst',['as'=>'cms.adverst','uses'=>'AdverstController@index']);
    $app->get('cms.adverst/{id}',['as'=>'cms.adverst','uses'=>'AdverstController@adversts']);
    $app->post('upload_article_image',['as'=>'upload_article_image','uses'=>'ArticleController@upload_image']);
    $app->post('article.save',['as'=>'article.save','uses'=>'ArticleController@save']);
    $app->post('article.detail',['as'=>'article.detail','uses'=>'ArticleController@detail']);
    $app->post('article.delete',['as'=>'article.delete','uses'=>'ArticleController@delete']);
    $app->post('ad.cate.save',['as'=>'ad.cate.save','uses'=>'AdverstController@cate_save']);
    $app->post('adverst.save',['as'=>'adverst.save','uses'=>'AdverstController@save']);
    $app->post('adverst.delete',['as'=>'adverst.delete','uses'=>'AdverstController@delete']);
    $app->post('upload_adverst_image',['as'=>'upload_adverst_image','uses'=>'AdverstController@upload_image']);
    //系统设置
    $app->get('setting.basecoin', ['as'=>"setting.basecoin",'uses'=>'SettingController@basecoin']); //基础币
    $app->get('setting.morecoin', ['as'=>"setting.morecoin",'uses'=>'SettingController@morecoin']); //平台币
//    $app->post('save_basecoin_conf',['as'=>'save_basecoin_conf','uses'=>'SettingController@save_basecoin_conf']); //保存平台币配置
//    $app->post('save_morecoin_conf',['as'=>'save_morecoin_conf','uses'=>'SettingController@save_morecoin_conf']); //保存虚拟币配置
//    $app->post('check_contract_address',['as'=>'check_contract_address','uses'=>'SettingController@check_contract_address']); //ERC20代币合约验证
    $app->post('upload_icon', ['as'=>"upload_icon",'uses'=>'SettingController@upload_icon']); //上传图标
    $app->get('setting.notice', ['as'=>"setting.notice",'uses'=>'SettingController@notice']); //系统公告
    $app->get("setting.admin",['as'=>"setting.admin",'uses'=>'SettingController@admin']); //账号维护
    $app->any("set_admin_access",['as'=>"set_admin_access",'uses'=>'SettingController@set_admin_access']);
    $app->any("add_admin",['as'=>"add_admin",'uses'=>'SettingController@add_admin']);
    $app->any("del_admin",['as'=>"del_admin",'uses'=>'SettingController@del_admin']);
    $app->any("update_admin_pwd",['as'=>"update_admin_pwd",'uses'=>'SettingController@update_admin_pwd']);
    $app->get('setting.pool', ['as'=>"setting.pool",'uses'=>'SettingController@pool']); //矿池
//    $app->post('setting.pool.add', ['as'=>"setting.pool.add",'uses'=>'SettingController@pool_add']); //矿池修改
    $app->get('price.log.detail/{api_param}',['as'=>'price.log.detail','uses'=>'SettingController@price_log']);//价格采集
//    $app->get('setting.recharge', ['as'=>"setting.recharge",'uses'=>'SettingController@recharge']); //充值设置
//    $app->post('setting.recharge_save', ['as'=>"setting.recharge_save",'uses'=>'SettingController@recharge_save']); //充值设置修改
//    $app->post('save_notice',['as'=>'save_notice','uses'=>'SettingController@save_notice']); //保存公告设置
    $app->post('user.asset',['as'=>'user.asset','uses'=>'UserController@asset']);
    $app->get('sugar.auth',['as'=>'sugar.auth','uses'=>'SugarController@auth']);
    $app->get('sugar.log',['as'=>'sugar.log','uses'=>'SugarController@log']);
    $app->get('lock_transfer.auth',['as'=>'lock_transfer.auth','uses'=>'LockTransferController@auth']);
    $app->get('lock_transfer.log',['as'=>'lock_transfer.log','uses'=>'LockTransferController@log']);
    $app->get('lock_transfer.logs',['as'=>'lock_transfer.log','uses'=>'LockTransferController@logs']);
    $app->post('locktransfercancel',['as'=>'locktransfercancel','uses'=>'LockTransferController@cancel']);
    $app->any('lock_transfer.export',['as'=>'lock_transfer.export','uses'=>'LockTransferController@export']);
    $app->any('incharge.export',['as'=>'lock_transfer.export','uses'=>'LockTransferController@export_incharge']);
    //矿机
    $app->get('miner',['as'=>'miner','uses'=>'MinerController@index']);
    $app->any('miner.getlist',['as'=>'miner.getlist','uses'=>'MinerController@getList']);
    $app->any('miner.getcointype',['as'=>'miner.getcointype','uses'=>'MinerController@getCointype']);
    $app->post('miner.lock',['as'=>'miner.lock','uses'=>'MinerController@lock']);
    $app->post('miner.get',['as'=>'miner.get','uses'=>'MinerController@getById']);
    $app->post('miner.pop',['as'=>'miner.pop','uses'=>'MinerController@pop']);
    $app->get('miner.salelist',['as'=>'miner.salelist','uses'=>'MinerController@salelist']);
    $app->post('miner.savedescribe',['as'=>'miner.savedescribe','uses'=>'MinerController@savedescribe']);
    $app->get('setting.admin_role',['as'=>'setting.admin_role','uses'=>'AdminroleController@index']);
    $app->get('setting.connect',['as'=>'setting.connect','uses'=>'AdminroleController@connect']);
    $app->any('setting.huifu',['as'=>'setting.huifu','uses'=>'AdminroleController@huifu']);

    $app->get('btcbank',['as'=>'btcbank','uses'=>'BtcBankController@index']);
    $app->post('btcbank.setstatus',['as'=>'btcbank.setstatus','uses'=>'BtcBankController@setstatus']);
    $app->post('btcbank.saveproduct',['as'=>'btcbank.saveproduct','uses'=>'BtcBankController@saveproduct']);
    $app->get('btcbank.order',['as'=>'btcbank.order','uses'=>'BtcBankController@order']);

});


$app->group([
    'namespace' => 'App\Http\Controllers\Manage',
    'middleware' => [
        Laravel\Lumen\Http\Middleware\VerifyCsrfToken::class,
        \App\Http\Middleware\PostLog::class
    ]
], function() use ($app)
{
    $app->any("dologin",['as'=>"dologin",'uses'=>'AuthController@dologin']);
    $app->post('user.lock',['as'=>'user.lock','uses'=>'UserController@lock']);
    $app->post('user.money',['as'=>'user.money','uses'=>'UserController@money']);
    $app->post('user.cp',['as'=>'user.cp','uses'=>'UserController@cp']);
    $app->post('setting.pool.add', ['as'=>"setting.pool.add",'uses'=>'SettingController@pool_add']); //矿池修改
    $app->post('save_basecoin_conf',['as'=>'save_basecoin_conf','uses'=>'SettingController@save_basecoin_conf']); //保存平台币配置
    $app->post('save_morecoin_conf',['as'=>'save_morecoin_conf','uses'=>'SettingController@save_morecoin_conf']); //保存虚拟币配置
    $app->post('check_contract_address',['as'=>'check_contract_address','uses'=>'SettingController@check_contract_address']); //ERC20代币合约验证
    $app->get('setting.recharge', ['as'=>"setting.recharge",'uses'=>'SettingController@recharge']); //充值设置
    $app->post('setting.recharge_save', ['as'=>"setting.recharge_save",'uses'=>'SettingController@recharge_save']); //充值设置修改
    $app->post('save_notice',['as'=>'save_notice','uses'=>'SettingController@save_notice']); //保存公告设置
    $app->post('sugar.auth.save',['as'=>'sugar.auth.save','uses'=>'SugarController@save']);
    $app->post('sugar.auth.edit',['as'=>'sugar.auth.edit','uses'=>'SugarController@edit']);
    $app->post('lock_transfer.auth.save',['as'=>'lock_transfer.auth.save','uses'=>'LockTransferController@save']);
    $app->post('lock_transfer.auth.edit',['as'=>'lock_transfer.auth.edit','uses'=>'LockTransferController@edit']);
    $app->post('user.otcauth',['as'=>'user.otcauth','uses'=>'UserController@otcauth']);

    $app->post('tx.estimate', ['as'=>"tx.estimate",'uses'=>'TransactionController@estimate']);
    $app->post('tx.send', ['as'=>"tx.send",'uses'=>'TransactionController@send']);

    $app->any('miner.getlist',['as'=>'miner.getlist','uses'=>'MinerController@getList']);

    $app->post('setting.admin_role.insert',['as'=>'setting.admin_role.insert','uses'=>'AdminroleController@insertrole']);
    $app->post('setting.admin_role.update',['as'=>'setting.admin_role.update','uses'=>'AdminroleController@updaterole']);
    $app->post('setting.admin_role.del',['as'=>'setting.admin_role.del','uses'=>'AdminroleController@delrole']);
});