<?php

namespace App\Http\Controllers\Manage;

use App\FanweErrcode;
use App\Helper;
use App\Http\Controllers\Manage\AuthBaseController;
use App\Http\Models\CoinType;
use App\Http\Models\CpLog;
use App\Http\Models\ExpendLog;
use App\Http\Models\FreeLog;
use App\Http\Models\FreezeLog;
use App\Http\Models\IncomeLog;
use App\Http\Models\MineIncharge;
use App\Http\Models\MineLog;
use App\Http\Models\MinePool;
use App\Http\Models\PromoterReward;
use App\Http\Models\UserAsset;
use App\Http\Models\Web\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlatformlogController extends AuthBaseController
{

    //平台日志-划转
    public function allot(Request $request)
    {
        $coin_type = $request->input("coin_type");
        $keyword = trim($request->input("keyword"));
        $allot_date = $request->input('allot_date');
        $time_fields = 'create_time';

        if ($allot_date) {
            $userDateStr = explode('~', $allot_date);
            $begin_date = $userDateStr[0];
            $end_date = $userDateStr[1];
            $param['allot_date'] = $begin_date . '~' . $end_date;
        }
        if ($keyword) {
            $searchUid = DB::table('user')->whereRaw("username like'%" . $keyword . "%' or fanwe_user.mobile like '" . $keyword . "%'")->lists('id');
            $lists = DB::table('allot_log')
                ->whereIn('to', $searchUid)
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {
            $lists = DB::table('allot_log')
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        }

        $uidArr = $lists->pluck('to')->unique();
        $userInfo = DB::table('user')->select('username', 'mobile', 'id')->whereIn('id', $uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');

        $lists->map(function ($list) use ($userInfo) {
            $list->username = collect($userInfo->get($list->to))->get('username');
            $list->mobile = collect($userInfo->get($list->to))->get('mobile');
        });

        $coin_list = Helper::getCoinType();

        return view("manage.platformlog.allot", ['lists' => $lists, 'keyword' => $keyword, 'param' => $param, 'coin_list' => $coin_list, 'coin_type' => $coin_type]);
    }

    //平台日志-冻结
    public function freeze(Request $request)
    {
        $coin_type = $request->input("coin_type");
        $keyword = trim($request->input("keyword"));
        $freeze_date = $request->input('freeze_date');
        $time_type = $request->input('time_type');
        $type = $request->input('type', 'manual');
        if ($time_type == 1) {
            $time_fields = 'freeze_time';
        } else {
            $time_fields = 'free_time';
        }
        if ($freeze_date) {
            $userDateStr = explode('~', $freeze_date);
            $begin_date = $userDateStr[0];
            $end_date = $userDateStr[1];
            $param['freeze_date'] = $begin_date . '~' . $end_date;
            $param['time_type'] = $time_type;
        }
        if ($keyword) {
            $searchUid = DB::table('user')->whereRaw("username like'%" . $keyword . "%' or fanwe_user.mobile like '" . $keyword . "%'")->lists('id');
            $lists = DB::table('freeze_log')
                ->whereIn('user_id', $searchUid)
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $type, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($type) {
                        $query->where('type', '=', $type);
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {
            $lists = DB::table('freeze_log')
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $type, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($type) {
                        $query->where('type', '=', $type);
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        }

        $uidArr = $lists->pluck('user_id')->unique();
        $userInfo = DB::table('user')->select('username', 'mobile', 'id')->whereIn('id', $uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');

        $lists->map(function ($list) use ($userInfo) {
            $list->username = collect($userInfo->get($list->user_id))->get('username');
            $list->mobile = collect($userInfo->get($list->user_id))->get('mobile');
        });

        $coin_list = Helper::getCoinType();

        return view("manage.platformlog.freeze", ['lists' => $lists, 'keyword' => $keyword, 'param' => $param, 'coin_list' => $coin_list, 'coin_type' => $coin_type]);
    }

    //解冻
    public function free(Request $request)
    {
        $id = $request->input('id');
        try {
            DB::beginTransaction();
            Helper::freeCoin($id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $message = explode(':', $e->getMessage());
            switch ($message[0]) {
                case 'total_vc_free_not_enough':
                    return $this->error('冻结总金额不足', FanweErrcode::SYSTEM_ERROR);
                    break;
                case 'total_vc_free_normal_not_enough':
                    return $this->error('冻结的可交易部分不足', FanweErrcode::SYSTEM_ERROR);
                    break;
                case 'total_vc_free_untrade_not_enough':
                    return $this->error('冻结的不可交易部份不足', FanweErrcode::SYSTEM_ERROR);
                    break;
                default:
                    return $this->error();
            }
        }
        return $this->success();
    }

    //平台日志-兑换
    public function exchange(Request $request)
    {
        $keyword = trim($request->input("keyword"));
        $exchange_date = $request->input('exchange_date');
        $time_fields = 'create_time';

        if ($exchange_date) {
            $dateStr = explode('~', $exchange_date);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
            $param['exchange_date'] = $begin_date . '~' . $end_date;
        }
        if ($keyword) {
            $searchUid = DB::table('user')->whereRaw("username like'%" . $keyword . "%' or fanwe_user.mobile like '" . $keyword . "%'")->lists('id');
            $lists = DB::table('exchange')
                ->whereIn('user_id', $searchUid)
                ->where(function ($query) use ($begin_date, $end_date, $time_fields) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {
            $lists = DB::table('exchange')
                ->where(function ($query) use ($begin_date, $end_date, $time_fields) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        }

        $uidArr = $lists->pluck('user_id')->unique();
        $userInfo = DB::table('user')->select('username', 'mobile', 'id')->whereIn('id', $uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');

        $lists->map(function ($list) use ($userInfo) {
            $list->username = collect($userInfo->get($list->user_id))->get('username');
            $list->mobile = collect($userInfo->get($list->user_id))->get('mobile');
        });
        return view("manage.platformlog.exchange", ['lists' => $lists, 'keyword' => $keyword, 'param' => $param]);
    }

    //平台日志-算力记录
    public function cplog(Request $request)
    {
        $keyword = trim($request->input('keyword'));
        $param['cp_log_date'] = $request->input('cp_log_date');
        if ($param['cp_log_date']) {
            $dateStr = explode('~', $param['cp_log_date']);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
        }
        $param['api']= $request->input('api',0);

        $lists = CpLog::leftJoin("user", "user.id", "=", "cp_log.user_id")->select("cp_log.*", "user.username", "user.mobile", "user.cp_total")
            ->where(function ($query) use ($keyword, $begin_date, $end_date,$param) {
                //$query->where('cp_log.api', 'platform');
                if ($keyword) {
                    $query->whereRaw("(fanwe_user.username like'%" . $keyword . "%' or fanwe_user.mobile like '" . $keyword . "%')");
                }
                if ($begin_date) {
                    $query->where('cp_log.create_time', '>=', "$begin_date 00:00:00");
                }
                if ($end_date) {
                    $query->where('cp_log.create_time', '<=', "$end_date 23:59:59");
                }
                if($param['api']){
                    $query->where('cp_log.api','=',$param['api']);
                }
            })->orderBy('cp_log.id', 'desc')->paginate(20);
        $api =['sign'=>'签到','invite'=>'邀请','incharge'=>'充值','lock_transfer'=>'锁仓转账',
            'lock_transfer_invite'=>'锁仓转账推荐人','platform'=>'划转','study'=>'学习区块链知识',
            'transfer'=>'转出','transfer_invite'=>'转出推荐人'
        ];

        return view("manage.platformlog.cplog", ['lists' => $lists, 'param' => $param, 'keyword' => $keyword ,'api'=>$api]);
    }

    //业绩
    public function achievement(Request $request)
    {
        $date = $request->input('date');
        $keyword = trim($request->input('keyword'));
        if ($keyword) {
            $user_id = User::whereRaw("username like '%{$keyword}%' OR mobile like '%{$keyword}%'")->lists('id')->toArray();
        }
        if ($date) {
            $dateStr = explode('~', $date);
            $begin_date = $dateStr[0];
            $end_date = $dateStr[1];
            $param['date'] = $begin_date . '~' . $end_date;
        }
        $lists = PromoterReward::with('invite_user', 'user')->
        where(function ($query) use ($begin_date, $end_date, $user_id) {
            if ($begin_date) {
                $query->where('create_time', ">=", $begin_date . " 00:00:00");
            }
            if ($end_date) {
                $query->where('create_time', "<=", $end_date . " 23:59:59");
            }
            if (!empty($user_id)) {
                $query->whereRaw("(user_id in (?) OR from_user_id in (?) )", [$user_id, $user_id]);
            } else if (isset($user_id)) {
                $query->whereRaw('1!=1');
            }
        })->orderBy('id', 'desc')
            ->paginate(20);
        $sum = PromoterReward::sum('total_amount');
        if ($date) {
            $curr_sum = PromoterReward::where(function ($query) use ($begin_date, $end_date) {
                if ($begin_date) {
                    $query->where('create_time', ">=", $begin_date . " 00:00:00");
                }
                if ($end_date) {
                    $query->where('create_time', "<=", $end_date . " 23:59:59");
                }
            })->sum('total_amount');
        }
        return view("manage.platformlog.achievement", ['keyword' => $keyword, 'date' => $date, 'lists' => $lists, 'sum' => $sum, 'curr_sum' => $curr_sum]);
    }


    //平台收入
    public function income(Request $request)
    {
        $coin_type = $request->input("coin_type");
        $keyword = trim($request->input("keyword"));
        $freeze_date = $request->input('freeze_date');
        $time_type = $request->input('time_type');
        $type = $request->input('type', 'manual');
        if ($time_type == 1) {
            $time_fields = 'freeze_time';
        } else {
            $time_fields = 'free_time';
        }
        if ($freeze_date) {
            $userDateStr = explode('~', $freeze_date);
            $begin_date = $userDateStr[0];
            $end_date = $userDateStr[1];
            $param['freeze_date'] = $begin_date . '~' . $end_date;
            $param['time_type'] = $time_type;
        }
        if ($keyword) {
            $searchUid = DB::table('user')->whereRaw("username like'%" . $keyword . "%' or fanwe_user.mobile like '" . $keyword . "%'")->lists('id');
            $lists = DB::table('freeze_log')
                ->whereIn('user_id', $searchUid)
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $type, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($type) {
                        $query->where('type', '=', $type);
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {
            $lists = DB::table('freeze_log')
                ->where(function ($query) use ($begin_date, $end_date, $time_fields, $type, $coin_type) {
                    if ($begin_date) {
                        $query->where($time_fields, ">=", $begin_date . " 00:00:00");
                    }
                    if ($end_date) {
                        $query->where($time_fields, "<=", $end_date . " 23:59:59");
                    }
                    if ($type) {
                        $query->where('type', '=', $type);
                    }
                    if ($coin_type > 0) {
                        $query->where("coin_type", $coin_type);
                    } else {
                        $query->where("coin_type", 0);
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20);
        }

        $uidArr = $lists->pluck('user_id')->unique();
        $userInfo = DB::table('user')->select('username', 'mobile', 'id')->whereIn('id', $uidArr)->get();
        $userInfo = collect($userInfo)->keyBy('id');

        $lists->map(function ($list) use ($userInfo) {
            $list->username = collect($userInfo->get($list->user_id))->get('username');
            $list->mobile = collect($userInfo->get($list->user_id))->get('mobile');
        });

        $coin_list = Helper::getCoinType();

        return view("manage.platformlog.freeze", ['lists' => $lists, 'keyword' => $keyword, 'param' => $param, 'coin_list' => $coin_list, 'coin_type' => $coin_type]);
    }

    //报表首页
    public function index()
    {
        $coin_list = CoinType::getAll();
        $mine_pool = MinePool::all()->keyBy('coin_type')->toArray();

        $coin_list = collect($coin_list)->map(function ($val) use ($mine_pool) {
            $val['mine_pool'] = $mine_pool[$val['coin_type']]['total_amount'];
            if ($val['coin_type'] == 0) {
                $val['vc_total'] = Helper::formatCoin(User::sum('vc_total'), 1);
                $val['vc_normal'] = Helper::formatCoin(User::sum('vc_normal'), 1);
                $val['vc_untrade'] = Helper::formatCoin(User::sum('vc_untrade'), 1);
                $val['vc_freeze'] = Helper::formatCoin(User::sum('vc_freeze'), 1);
                $val['vc_freeze_normal'] = Helper::formatCoin(User::sum('vc_freeze_normal'), 1);
                $val['vc_freeze_untrade'] = Helper::formatCoin(User::sum('vc_freeze_untrade'), 1);
            } else {
                $val['vc_total'] = Helper::formatCoin(UserAsset::where('coin_type',$val['coin_type'])->sum('vc_total'), 1);
                $val['vc_freeze'] = Helper::formatCoin(UserAsset::where('coin_type',$val['coin_type'])->sum('vc_freeze'), 1);
            }
            $val['image'] = img($val['image'], 64, 64);
            $val['lock_trans_freeze_amount'] = Helper::formatCoin(FreezeLog::where(['type' => 'lock_transfer', 'coin_type' => $val['coin_type']])->sum('vc_amount'), 1);
            $val['lock_trans_freeze_done_amount'] = Helper::formatCoin(FreezeLog::where(['type' => 'lock_transfer', 'coin_type' => $val['coin_type']])->sum('vc_done_amount'), 1);
            $val['lock_trans_freeze'] = Helper::formatCoin($val['lock_trans_freeze_amount'] - $val['lock_trans_freeze_done_amount'], 1);
            $val['mined'] = Helper::formatCoin(MineLog::where(['mined' => '1', 'coin_type' => $val['coin_type']])->sum('amount'), 1);
            $val['un_mined'] = Helper::formatCoin(MineLog::where(['mined' => '0', 'coin_type' => $val['coin_type']])->sum('amount'), 1);
            $val['mine_total'] = Helper::formatCoin($val['mined'] + $val['un_mined'], 1);
            return $val;
        });

        return view('manage.platformlog.index', ['lists' => $coin_list]);
    }


    //矿池记录
    public function mincharge(Request $request)
    {
        $coin_type = $request->input('coin_type');
        $type = $request->input('type',0);
        $lists = MineIncharge::where('coin_type', $coin_type)->where('type',$type)->orderBy('id', 'desc')->paginate(20);
        return view("manage.platformlog.mincharge", ['lists' => $lists, 'coin_type' => $coin_type,'type'=>$type]);
    }

    //矿池产出记录
    public function minelog(Request $request)
    {
        $coin_type = $request->input('coin_type');
        $lists = MineLog::with('user')->where('coin_type', $coin_type)->orderBy('id', 'desc')->paginate(20);
        return view("manage.platformlog.minelog", ['lists' => $lists, 'coin_type' => $coin_type]);
    }

    //资金明细
    public function detail(Request $request)
    {
        $coin_type = $request->input("coin_type");
        $table = $request->input('table', 'income_log');
        $type = $request->input('type', 'all');
        $user = User::where('mobile', $request->input('mobile'))->first();
        $date = $request->input('date');
        $vc_amount = $request->input('vc_amount');
        $than = $request->input('than', '>');
        if ($table == 'income_log') {
            $typeNameArr = [
                'mine' => '挖矿',
//                'transfer' => '转账',
                'purchase' => '购买',
                'allot' => '划拨',
                'steal' => '偷币',
                'incharge' => '充值'
            ];
            $lists = IncomeLog::with('user')->where(function ($query) use ($user, $type, $coin_type, $date, $vc_amount, $than) {
                if ($type !== 'all') {
                    $query->where('type', $type);
                }
                if ($coin_type > 0) {
                    $query->where("coin_type", $coin_type);
                } else {
                    $query->where("coin_type", 0);
                }
                if ($date) {
                    $dateStr = explode('~', $date);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    $query->where('create_time', '>', $begin_date);
                    $query->where('create_time', '<', $end_date . ' 23:59:59');
                }
                if (is_numeric($vc_amount)) {
                    $query->where('vc_amount', $than, $vc_amount);
                }
            })->orderBy('id', 'desc');
            $total = $lists->sum('vc_amount');
            $lists = $lists->paginate(20);
            $lists->map(function ($val) use ($typeNameArr) {
                $val->typeName = $typeNameArr[$val->type];
            });
        } elseif ($table == 'expend_log') {
            $typeNameArr = [
                'withdraw' => '提现',
//                'transfer' => '转账',
                'sale' => '销售',
                'exchange' => '兑换'
            ];
            $lists = ExpendLog::where(function ($query) use ($type, $coin_type, $date, $vc_amount, $than) {
                if ($type !== 'all') {
                    $query->where('type', $type);
                }
                if ($coin_type > 0) {
                    $query->where("coin_type", $coin_type);
                } else {
                    $query->where("coin_type", 0);
                }
                if ($date) {
                    $dateStr = explode('~', $date);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    $query->where('create_time', '>', $begin_date);
                    $query->where('create_time', '<', $end_date . ' 23:59:59');
                }
                if (is_numeric($vc_amount)) {
                    $query->where('vc_amount', $than, $vc_amount);
                }
            })->orderBy('id', 'desc');
            $total = $lists->sum('vc_amount');
            $lists = $lists->paginate(20);
            $lists->map(function ($val) use ($typeNameArr) {
                $val->typeName = $typeNameArr[$val->type];
            });
        } elseif ($table == 'freeze_log') {
            $typeNameArr = [
                'withdraw' => '提现',
                'manual' => '平台',
                'sale' => '挂卖',
                'sugar' => '糖果',
                'lock_transfer' => '转账(包含锁仓式)',
            ];
            $lists = FreezeLog::where(function ($query) use ($user, $type, $coin_type, $date, $vc_amount, $than) {
                if ($type !== 'all') {
                    $query->where('type', $type);
                }
                if ($coin_type > 0) {
                    $query->where("coin_type", $coin_type);
                } else {
                    $query->where("coin_type", 0);
                }
                if ($date) {
                    $dateStr = explode('~', $date);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    $query->where('freeze_time', '>', $begin_date);
                    $query->where('freeze_time', '<', $end_date . ' 23:59:59');
                }
                if (is_numeric($vc_amount)) {
                    $query->where('vc_amount', $than, $vc_amount);
                }
            })->orderBy('id', 'desc');
            $total = $lists->sum('vc_amount');
            $lists = $lists->paginate(20);
            $lists->map(function ($val) use ($typeNameArr) {
                $val->typeName = $typeNameArr[$val->type];
            });
        } elseif ($table == 'free_log') {
            $typeNameArr = [
                'sugar' => '糖果',
                'lock_transfer' => '转账(包含锁仓式)',
            ];
            $lists = FreeLog::where(function ($query) use ($type, $coin_type, $date, $vc_amount, $than) {
                if ($type !== 'all') {
                    $query->where('type', $type);
                }
                if ($coin_type > 0) {
                    $query->where("coin_type", $coin_type);
                } else {
                    $query->where("coin_type", 0);
                }
                if ($date) {
                    $dateStr = explode('~', $date);
                    $begin_date = $dateStr[0];
                    $end_date = $dateStr[1];
                    $query->where('create_time', '>', $begin_date);
                    $query->where('create_time', '<', $end_date . ' 23:59:59');
                }
                if (is_numeric($vc_amount)) {
                    $query->where('vc_amount', $than, $vc_amount);
                }
            })->orderBy('id', 'desc');
            $total = $lists->sum('vc_amount');
            $lists = $lists->paginate(20);
            $lists->map(function ($val) use ($typeNameArr) {
                $val->typeName = $typeNameArr[$val->type];
            });
        }
        $coin_list = Helper::getCoinType();
        return view("manage.platformlog.detail", ['vc_amount' => $vc_amount, 'than' => $than,
            'total' => $total, 'lists' => $lists, 'table' => $table, 'type' => $type, 'user' => $user, 'coin_list' => $coin_list,
            'coin_type' => $coin_type, 'date' => $date]);
    }

    public function chart(Request $request)
    {
        $coin_type = $request->input('coin_type',0);
        $date = $request->input('chart_date');
        if ($date) {
            $dateStr = explode('~', $date);
            $begin_date = $dateStr[0].' 00:00:00';
            $end_date = $dateStr[1].' 23:59:59';
        }else{
            $begin_date = date('Y-m-d',strtotime('-7 days')).' 00:00:00';
            $end_date = date('Y-m-d').' 23:59:59';
        }
        $type = [
            'mine' => '挖矿',
//            'transfer' => '转账',
            'purchase' => '购买',
            'allot' => '划拨',
            'incharge' => '充值'
        ];
        if(config('app.friends'))
        {
            $type['steal'] = '偷币';
        }
        if(config('app.sugar'))
        {
            $type['sugar'] = '糖果';
        }
        if(config('app.lock_transfer'))
        {
            $type['lock_transfer'] = '转账(包含锁仓式)';
        }
        $data = [];
        collect($type)->map(function ($val, $key) use ($coin_type,$begin_date,$end_date,$date,&$data,$type) {
            IncomeLog::selectRaw("type,sum(vc_amount) as total,DATE_FORMAT(create_time,'%Y-%m-%d') as date")
            ->where('type',$key)
            ->where('create_time','>',$begin_date)
            ->where('create_time','<',$end_date)
            ->where('coin_type',$coin_type)
            ->groupBy('date')->get()->keyBy('date')->map(function ($v,$k)use(&$data,$val,$type){
                $data[$k][$val] = intval($v->total);
                foreach ($type as $eName=>$cName)
                {
                    if(!isset($data[$k][$cName]))
                    {
                        $data[$k][$cName] = 0;
                    }
                }
            });
            return $val;
        });
        return $this->success('', $data);
    }

}
