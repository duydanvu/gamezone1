<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KPIController extends Controller
{
    public function listKPIAction(){
        $total = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date'))
            -> addSelect(DB::raw("'0' as acc_sub"))
            -> addSelect(DB::raw("'0' as acc_unsub"))
            -> addSelect(DB::raw("'0' as acc_psc"))
            -> addSelect(DB::raw("'0' as acc_active"))
            -> addSelect(DB::raw("'0' as acc_gh"))
            -> addSelect(DB::raw("'0' as acc_dk"))
            -> addSelect(DB::raw("'0' as acc_sum"))
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> first();
        $acc_sub = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_sub'))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_sub = 0;
        foreach ($acc_sub as $value){
            $total_sub += $value->count_sub;
        }
        $total->acc_sub = $total_sub;
        $acc_unsub_qr = DB::table('cdr_201908')
            -> select(DB::raw('COUNT(DISTINCT isdn) as sumUnsub'))
            -> where('request','=','UNSUB')
            -> get();
        foreach ($acc_unsub_qr as $value){
            $total->acc_unsub = $value ->sumUnsub;
        }

        $acc_psc = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_psc'))
            -> where('request','=','SUB')
            -> where('charge_price','>',0)
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_psc = 0;
        foreach ($acc_psc as $value){
            $total_psc += $value->count_psc;
        }
        $total->acc_psc = $total_psc;

        $acc_active = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_active'))
            -> where(function($query){
                $query -> where('request','=','SUB')
                    -> orwhere('request','=','GH');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_active = 0;
        foreach ($acc_active as $value){
            $total_active += $value->count_active;
        }
        $total->acc_active = $total_active;

        $acc_gh = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_gh'))
            -> where('request','=','GH')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_gh = 0;
        foreach ($acc_gh as $value){
            $total_gh += $value->count_gh;
        }
        $total->acc_gh = $total_gh;
        $acc_dk_sms = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_dk_sms'))
            -> where('channel','=','SMS')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_sms = 0;
        foreach ($acc_dk_sms as $value){
            $total_sms += $value->count_dk_sms;
        }
        $acc_dk_wap = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as wap'))
            -> where('channel','=','WAP')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_wap = 0;
        foreach ($acc_dk_wap as $value){
            $total_wap += $value->wap;
        }
        $acc_dk_vasgate = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_sub_vasgate'))
            -> where(function($query){
                $query->where('channel','<>','SMS')
                    ->orwhere('channel','<>','WAP');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $total_vagat = 0;
        foreach ($acc_dk_vasgate as $value){
            $total_vagat += $value->count_sub_vasgate;
        }
        $total->acc_dk = $total_sms + $total_wap + $total_vagat;

        $acc_sum = DB::table('cdr_201908')
            -> select(DB::raw('COUNT(DISTINCT(isdn)) as sumacc'))
            -> get();

        $total_sum = 0;
        foreach ($acc_sum as $value){
            $total_sum += $value->sumacc;
        }
        $total->acc_sum = $total_sum;
//        dd($total);
        $reg_tran = $this->regTransactions();
        $unreg_tran = $this->unregTransactions();
        $acc_psc_list = $this->accountPSC();
        $acc_gh_list = $this->accountGH();
        return view('kpi.kpi')->with([
            'total' => $total,
            'reg_tran' => $reg_tran,
            'unreg_tran' => $unreg_tran,
            'acc_psc_list' => $acc_psc_list,
            'acc_gh_list' => $acc_gh_list
        ]);
    }

    public function regTransactions(){
        $reg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> get();
        return $reg_tran;
    }

    public function unregTransactions(){
        $unreg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
            -> where('request','=','UNSUB')
            -> whereNotNull('reg_datetime')
            -> get();
        return $unreg_tran;
    }

    public  function  accountPSC(){
        $acc_psc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> where('request','=','SUB')
            -> where('charge_price','>',0)
            -> whereNotNull('reg_datetime')
            -> get();
        return $acc_psc;
    }

    public  function accountGH(){
        $acc_gh = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> where('request','=','GH')
            -> whereNotNull('reg_datetime')
            -> get();
        return $acc_gh;
    }

}
