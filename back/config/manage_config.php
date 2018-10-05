<?php
/**
 * 后台管理的节点与权限
 */

return [
    'nav_name'=>[
        'index'=>'首页',
        'user'=>'用户',
        'transaction'=>'交易',
        'withdraw'=>'提现',
        'platformlog'=>'报表',
        'cms'=>'内容管理',  //先不开启
        'miner'=>'矿机',
        'setting'=>'设置',
        'financialproduct'=>'聚宝盆',
    ],
    'nav' => [
        //首页
        'index' => [
            //nav点击的对应路由
            '/'	=>	[
                'name'	=>	'首页',
                'sub'	=>	'',
                'icon'	=>	'&#xe60b;',
                'uses'	=>	'IndexController@index',
            ]
        ],
        //用户管理
        'user' => [
            //nav点击的对应路由
            'user'	=>	[
                'name'	=>	'用户管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe64b;',
                'uses'	=>	'UserController@index',
            ],
            'sugar.auth' =>[
                'name'	=>	'糖果管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe65b;',
                'uses'	=>	'SugarController@auth',
            ],
            'lock_transfer.auth' =>[
                'name'	=>	'转账管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe65b;',
                'uses'	=>	'LockTransferController@auth',
            ],
            'apply' =>[
                'name'	=>	'社区建设申请',
                'sub'	=>	'',
                'icon'	=>	'&#xe65b;',
                'uses'	=>	'ApplyController@index',
            ],
            'apply.node' =>[
                'name'	=>	'行业节点申请',
                'sub'	=>	'',
                'icon'	=>	'&#xe65b;',
                'uses'	=>	'ApplyController@node',
            ]
        ],
        //交易管理
        'transaction' => [
            //nav点击的对应路由
            'transaction.putorder'	=>	[
                'name'	=>	'挂单管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe613;',
                'uses'	=>	'TransactionController@putorder',
            ],
            'transaction.saleorder'	=>	[
                'name'	=>	'成交订单',
                'sub'	=>	'',
                'icon'	=>	'&#xe613;',
                'uses'	=>	'TransactionController@saleorder',
            ],
            'exrate.index' =>[
                'name' =>'汇率列表',
                'sub'  =>'',
                'icon' =>'&#xe613;',
                'uses' =>'ExRateController@index'
            ],
            'trans'=>[
                'name' =>'转账管理',
                'sub'  =>'',
                'icon' =>'&#xe613;',
                'uses' =>'TransController@index'
            ]
        ],
        //提现管理
        'withdraw' => [
            //nav点击的对应路由
            'withdraw'	=>	[
                'name'	=>	'提现管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe679;',
                'uses'	=>	'WithdrawController@index',
            ]
        ],
        //平台日志
        'platformlog' => [
            //nav点击的对应路由
            'platformlog.index'	=>	[
                'name'	=>	'报表',
                'sub'	=>	'',
                'icon'	=>	'&#xe78f;',
                'uses'	=>	'PlatformlogController@index',
            ],
            'platformlog.allot'	=>	[
                'name'	=>	'划转',
                'sub'	=>	'',
                'icon'	=>	'&#xe78f;',
                'uses'	=>	'PlatformlogController@allot',
            ],
//            'platformlog.freeze' =>	 [
//                'name'	=>	'冻结',
//                'sub'	=>	'',
//                'icon'	=>	'&#xe78f;',
//                'uses'	=>	'PlatformlogController@freeze',
//            ],
            'platformlog.exchange' =>	 [
                'name'	=>	'兑换',
                'sub'	=>	'',
                'icon'	=>	'&#xe78f;',
                'uses'	=>	'PlatformlogController@exchange',
            ],
            'platformlog.cplog' =>	 [
                'name'	=>	'算力',
                'sub'	=>	'',
                'icon'	=>	'&#xe78f;',
                'uses'	=>	'PlatformlogController@cplog',
            ]
        ],
        'financialproduct' => [
            'btcbank'	=>	[
                'name'	=>	'聚宝盆',
                'sub'	=>	'',
                'icon'	=>	'&#xe620;',
                'uses'	=>	'BtcBankController@index',
            ],
            'btcbank.order'  =>  [
                'name'	=>	'购买明细',
                'sub'	=>	'',
                'icon'	=>	'&#xe78f;',
                'uses'	=>	'BtcBankController@order',
            ],
        ],
        //暂时先不开启
//        'cms'=>[
//            'cms.article' =>[
//                'name'	=>	'文章管理',
//                'sub'	=>	'',
//                'icon'	=>	'&#xe613;',
//                'uses'	=>	'SettingController@basecoin',
//            ],
//            'cms.adverst' =>[
//                'name'	=>	'广告管理',
//                'sub'	=>	'',
//                'icon'	=>	'&#xea1b;',
//                'uses'	=>	'SettingController@basecoin',
//            ],
//        ],
        'miner' => [
            //nav点击的对应路由
            'miner'	=>	[
                'name'	=>	'矿机商城',
                'sub'	=>	'',
                'icon'	=>	'&#xe604;',
                'uses'	=>	'MinerController@index',
            ],
            'miner.salelist'=>[
                'name'	=>	'销售列表',
                'sub'	=>	'',
                'icon'	=>	'&#xe604;',
                'uses'	=>	'MinerController@salelist',
            ]
        ],
        //系统设置
        'setting' => [
            //nav点击的对应路由
            'setting.basecoin'	=>	[
                'name'	=>	'平台币',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@basecoin',
            ],
            'setting.morecoin'	=>	[
                'name'	=>	'资产管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@mine',
            ],
            'setting.pool' 	=>	[
                'name'	=>	'矿池统计',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@pool',
            ],
            'setting.notice'	=>	[
                'name'	=>	'网站相关管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@notice',
            ],
            'setting.admin_role'	=>	[
                'name'	=>	'角色管理',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'AdminroleController@index',
            ],
            'setting.admin'	=>	[
                'name'	=>	'账号维护',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@admin',
            ],
            'setting.connect' => [
                'name'	=>	'用户反馈',
                'sub'	=>	'',
                'icon'	=>	'&#xe600;',
                'uses'	=>	'SettingController@connect',
            ]
        ]
    ],
    'nav_shortcut' => [
        '/'	=>	[
            'name'	=>	'首页',
            'sub'	=>	'首页',
            'icon'	=>	'&#xe60b;',
            'uses'	=>	'IndexController@index',
        ],
        'user'	=>	[
            'name'	=>	'用户',
            'sub'	=>	'用户',
            'icon'	=>	'&#xe64b;',
            'uses'	=>	'UserController@index',
        ],
        'setting.admin' =>	[
            'name'	=>	'系统设置',
            'sub'	=>	'设置',
            'icon'	=>	'&#xe600;',
            'uses'	=>	'SettingController@admin',
        ]
    ],
    //定义规则:
    //1. 分组中的路由名称与路由定义中的web分组的各路由as定义必需完全一致，而不是实际的route_name,即不包含路由分组的as定义
    //2. 每个分组中的routes数组中的每一个元素，在整个access_list中必需唯一
    'manage_access' => [
//        'access_list' => [
//            'index','user','get_userlist','transaction.putorder','transaction.saleorder','withdraw',
//            'platformlog.allot','platformlog.freeze','platformlog.exchange','platformlog.cplog',
//            'setting.basecoin','setting.morecoin','setting.notice','setting.admin','setting.pool','miner'
//        ],
        'access_list' => [
            'group_user_list' => [
                'name'=>'会员账户管理',
                'routes' => ['user.export','index','user','get_userlist','user.lock','to_update_mypwd','update_my_pwd','user.calc.detail']
            ],
            'group_user_money'=>[
                'name'=>'会员资金管理',
                'routes' => ['index','user','get_userlist','user.money.detail','user.money','user.asset','platformlog.free','platformlog.freeze','to_update_mypwd','update_my_pwd']
            ],
            'group_user_cp'=>[
                'name'=>'会员算力管理',
                'routes' => ['index','user','get_userlist','user.cp','to_update_mypwd','update_my_pwd']
            ],
            'group_sugar'=>[
                'name'=>'糖果授权管理',
                'routes' => ['index','sugar.auth','sugar.log','sugar.auth.save','sugar.auth.edit','user.asset','to_update_mypwd','update_my_pwd']
            ],
            'group_lock_transfer'=>[
                'name'=>'锁仓转账授权',
                'routes' => ['index','lock_transfer.auth','lock_transfer.log','lock_transfer.auth.save','lock_transfer.auth.edit','user.asset','to_update_mypwd','update_my_pwd']
            ],
            'group_octauth'=>[
                'name'=>'交易授权',
                'routes' => ['index','user','get_userlist','user.otcauth','to_update_mypwd','update_my_pwd']
            ],
            'group_transaction'=>[
                'name'=>'交易管理',
                'routes' => ['index','transaction.putorder','transaction.saleorder','transaction.down.saleorder','transaction.appeal','to_update_mypwd','update_my_pwd']
            ],
            'group_withdraw'=>[
                'name'=>'提现管理',
                'routes' => ['index','withdraw','withdraw.check','withdraw.export','tx.estimate','tx.send','to_update_mypwd','update_my_pwd']
            ],
            'group_log'=>[
                'name'=>'报表查询',
                'routes' => ['index','platformlog.allot','platformlog.freeze','platformlog.exchange','platformlog.cplog',
                    'platformlog.index','platformlog.detail','platformlog.chart','to_update_mypwd','update_my_pwd','lock_transfer.export','incharge.export'
                ]
            ],
            'group_basecoin'=>[
                'name'=>'平台币设置',
                'routes' => ['index','setting.basecoin','save_basecoin_conf','upload_icon','check_contract_address','to_update_mypwd','update_my_pwd']
            ],
            'group_morecoin'=>[
                'name'=>'其他资产设置',
                'routes' => ['index','setting.morecoin','save_morecoin_conf','upload_icon','check_contract_address','price.log.detail','to_update_mypwd','update_my_pwd']
            ],
            'group_pool'=>[
                'name'=>'矿池管理',
                'routes' => ['index','setting.pool','setting.pool.add','to_update_mypwd','update_my_pwd']
            ],
            'group_notice'=>[
                'name'=>'网站相关管理',
                'routes' => ['index','setting.notice','save_notice','setting.connect','to_update_mypwd','update_my_pwd','setting.huifu']
            ],
            'group_miner'=>[
                'name'=>'矿机管理',
                'routes' => ['index','miner.getlist','miner','miner.lock','miner.get','miner.pop','miner.salelist','miner.savedescribe','to_update_mypwd','update_my_pwd']
            ],
            'group_exrate'=>[
                'name'=>'汇率管理',
                'routes' => ['index','exrate.index','exrate.change','to_update_mypwd','update_my_pwd']
            ],
            'group_trans'=>[
                'name'=>'转账管理',
                'routes' => ['index','trans','to_update_mypwd','update_my_pwd']
            ],
            'group_apply'=>[
                'name'=>'社区建设申请',
                'routes' => ['index','apply','apply.apply','update_my_pwd','to_update_mypwd']
            ],
            'group_node'=>[
                'name'=>'行业节点申请',
                'routes' => ['index','apply.node','apply.node.apply','update_my_pwd','to_update_mypwd']
            ],
            'group_node'=>[
                'name'=>'聚宝盆管理',
                'routes' => ['index','btcbank','btcbank.setstatus','btcbank.saveproduct','update_my_pwd','to_update_mypwd','btcbank.order']
            ],
        ],
        'access_config'=>[
            'superadmin'=>[
                'name'	=>	'超级管理员'
            ],
//            'maintainer'=>[
//                'name'	=>	'演示',
//                'access'=>[
//                    'index','user','get_userlist','transaction.putorder','transaction.saleorder','withdraw',
//                    'platformlog.allot','platformlog.freeze','platformlog.exchange','platformlog.cplog',
//                    'setting.basecoin','setting.morecoin','setting.notice','setting.admin','setting.pool'
//                ]
//            ],
        ]
    ]
];