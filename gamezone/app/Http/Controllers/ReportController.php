<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    public function table_name($from ,$to ,$nameSub ){
        if(substr($from,0,4) == substr($to,0,4)){
            if(substr($from,5,2) < substr($to,5,2)){
                $countMonth = (int)substr($to,5,2) - (int)substr($from,5,2);
                $arr = [];
                for($i = 0;$i <=$countMonth;++$i){
                    if($i < 10) {
                        $arr[$i] = $nameSub . substr($from, 0, 4) . '0' . (substr($from, 5, 2) + $i);
                    }else{
                        $arr[$i] = $nameSub . substr($from, 0, 4) . (substr($from, 5, 2) + $i);
                    }
                }
                return  $arr;

            }
            if(substr($from,5,2) > substr($to,5,2)){
                return null;
            }
            if(substr($from,5,2) == substr($to,5,2)){
                if(substr($from,8,2) <= substr($to,8,2)){
                    $arr = $nameSub."".substr($from,0,4).substr($from,5,2);
                    return $arr;
                }
                else{
                    return null;
                }
            }
        }
        if(substr($from,0,4) > substr($to,0,4))return null;
        else{
            $year = substr($to,0,4) - substr($from,0,4);
            if($year <= 1){
                $month = 12 - substr($from,5,2);
                for($i = 0; $i <=$month;++$i){
                    if((substr($from,5,2)+$i) >=10) {
                        $arr[$i] = $nameSub . (substr($from, 0, 4) . (substr($from, 5, 2) + $i));
                    }else{
                        $arr[$i] = $nameSub . (substr($from, 0, 4) . '0'.(substr($from, 5, 2) + $i));
                    }
                }
                $month2 = (substr($to,5,2) - 0);
                for($i = 1;$i <= $month2 ; ++$i){
                    if($i >=10) {
                        array_push($arr, $nameSub . (substr($to, 0, 4) . $i));
                    }else{
                        array_push($arr, $nameSub . (substr($to, 0, 4) . '0'.$i));
                    }
                }
                return $arr;

            }if($year > 1){
                $arr2 = [];
                for($i = 0;$i <= $year;++$i){
                    if($i == 0){
                        $month3 = 12 - substr($from,5,2);
                        for($j = 0; $j <= $month3;++$j){
                            if((substr($from,5,2) + $j) >= 10) {
                                $arr2[$j] = $nameSub . (substr($from, 0, 4) . (substr($from, 5, 2) + $j));
                            }else{
                                $arr2[$j] = $nameSub . (substr($from, 0, 4) . '0'.(substr($from, 5, 2) + $j));
                            }
                        }
                    }
                    if($i == $year){
                        $month4 = (substr($to,5,2) - 0);
                        for($k = 1;$k <= $month4 ; ++$k){
                            if($k  >= 10) {
                                array_push($arr2, $nameSub . (substr($to, 0, 4) . $k));
                            }else{
                                array_push($arr2, $nameSub . (substr($to, 0, 4) . '0'.$k));
                            }
                        }
                    }
                    else{
                        for($j = 1; $j <= 12; $j++){
                            if($j >=10) {
                                array_push($arr2, $nameSub . ((substr($from, 0, 4)+1) . $j));
                            }else{
                                array_push($arr2, $nameSub . ((substr($from, 0, 4)+1) . '0'.$j));
                            }
                        }
                    }
                }
                return $arr2;
                dd($arr2);

            }
        }
    }

    public function getQueryDB($start,$end  ,$phone ,$username,$getFunctionData ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->table_name($start,$end,'cdr_');
        }else{
            $table = $this->table_name($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($end,0,4)) {
            if($start != null && $end != null){
                $result_acc = $this->$getFunctionData($table, $start, $end, null, 1);
            }else{
                $result_acc = $this->$getFunctionData($table, $dateend, $datenow, null, 1);
            }
        }
        else {

            $result_acc1 = $this->$getFunctionData($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $result_acc = $this->$getFunctionData($table[$i], $start, $end, $result_acc1,3);
            }
        }
        return $result_acc;
    }

    public function totalReport($table,$start,$end,$total_0,$index){
        if($index == 1){
            $total = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date'))
                -> addSelect(DB::raw("'0' as acc_sub"))
                -> addSelect(DB::raw("'0' as acc_unsub_pp"))
                -> addSelect(DB::raw("'0' as acc_unsub_stm"))
                -> addSelect(DB::raw("'0' as acc_psc"))
                -> addSelect(DB::raw("'0' as acc_active"))
                -> addSelect(DB::raw("'0' as acc_gh"))
                -> addSelect(DB::raw("'0' as acc_dk_sms"))
                -> addSelect(DB::raw("'0' as acc_dk_wap"))
                -> addSelect(DB::raw("'0' as acc_dk_vasgate"))
                -> addSelect(DB::raw("'0' as revenue_day"))
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> get();
            return $total;
        }elseif($index == 2){
            $total = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date'))
                -> addSelect(DB::raw("'0' as acc_sub"))
                -> addSelect(DB::raw("'0' as acc_unsub_pp"))
                -> addSelect(DB::raw("'0' as acc_unsub_stm"))
                -> addSelect(DB::raw("'0' as acc_psc"))
                -> addSelect(DB::raw("'0' as acc_active"))
                -> addSelect(DB::raw("'0' as acc_gh"))
                -> addSelect(DB::raw("'0' as acc_dk_sms"))
                -> addSelect(DB::raw("'0' as acc_dk_wap"))
                -> addSelect(DB::raw("'0' as acc_dk_vasgate"))
                -> addSelect(DB::raw("'0' as revenue_day"))
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime');
            return $total;
        }else{
            $total = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date'))
                -> addSelect(DB::raw("'0' as acc_sub"))
                -> addSelect(DB::raw("'0' as acc_unsub_pp"))
                -> addSelect(DB::raw("'0' as acc_unsub_stm"))
                -> addSelect(DB::raw("'0' as acc_psc"))
                -> addSelect(DB::raw("'0' as acc_active"))
                -> addSelect(DB::raw("'0' as acc_gh"))
                -> addSelect(DB::raw("'0' as acc_dk_sms"))
                -> addSelect(DB::raw("'0' as acc_dk_wap"))
                -> addSelect(DB::raw("'0' as acc_dk_vasgate"))
                -> addSelect(DB::raw("'0' as revenue_day"))
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> union($total_0)
                -> get();
            return $total;
        }
    }

    public function revenueDay($table, $start,$end,$revenue_0,$index){
        if($index == 1) {
            $revenue = DB:: table($table)
                ->select(DB::raw('DISTINCT DATE(reg_datetime) as date, SUM(charge_price) as revenue_day'))
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end])
                ->groupBy('date')
                ->get();
            return $revenue;
        }elseif ($index == 2){
            $revenue = DB:: table($table)
                ->select(DB::raw('DISTINCT DATE(reg_datetime) as date, SUM(charge_price) as  revenue_day'))
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end])
                ->groupBy('date');
            return $revenue;
        }else{
            $revenue = DB:: table($table)
                ->select(DB::raw('DISTINCT DATE(reg_datetime) as date, SUM(charge_price) as  revenue_day'))
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end])
                ->groupBy('date')
                ->union($revenue_0)
                ->get();
            return $revenue;
        }
    }

    public function countSub($table,$start,$end,$countSub_0,$index){
        if($index == 1){
            $acc_sub = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub'))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> get();
            return $acc_sub;
        }elseif ($index == 2){
            $acc_sub = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub'))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date');
            return $acc_sub;
        }else{
            $acc_sub = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub'))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> union($countSub_0)
                -> get();
            return $acc_sub;
        }
    }

    public function countUnSubPP($table,$start,$end,$countUnSubPP_0,$index){
        if($index == 1){
            $acc_unsub_people = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_pp'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','=','SMS')
                        ->orwhere('channel','=','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> get();
            return $acc_unsub_people;
        }elseif ($index == 2){
            $acc_unsub_people = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_pp'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','=','SMS')
                        ->orwhere('channel','=','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date');
            return $acc_unsub_people;
        }else{
            $acc_unsub_people = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_pp'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','=','SMS')
                        ->orwhere('channel','=','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union($countUnSubPP_0)
                -> groupBy('date')
                -> get();
            return $acc_unsub_people;
        }
    }

    public function countUnSubST($table,$start,$end,$countUnSubST_0,$index){
        if($index == 1){
            $acc_unsub_system = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_stm'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','<>','SMS')
                        ->orwhere('channel','<>','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> get();
            return $acc_unsub_system;
        }elseif ($index == 2){
            $acc_unsub_system = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_stm'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','<>','SMS')
                        ->orwhere('channel','<>','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date');
            return $acc_unsub_system;
        } else {
            $acc_unsub_system = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_stm'))
                -> where('request','=','UNSUB')
                -> where(function($query){
                    $query->where('channel','<>','SMS')
                        ->orwhere('channel','<>','WAP');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> union($countUnSubST_0)
                -> get();
            return $acc_unsub_system;
        }
    }

    public function countPsc($table,$start,$end,$countPsc_0,$index){
        if($index == 1){
            $acc_psc = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_psc'))
                -> where('request','=','SUB')
                -> where('charge_price','>',0)
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> get();
            return $acc_psc;
        }elseif ($index == 2){
            $acc_psc = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_psc'))
                -> where('request','=','SUB')
                -> where('charge_price','>',0)
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date');
            return $acc_psc;
        } else {
            $acc_psc = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_psc'))
                -> where('request','=','SUB')
                -> where('charge_price','>',0)
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> union($countPsc_0)
                -> get();
            return $acc_psc;
        }
    }

    public function countActive($table,$start,$end,$countActive_0,$index){
        if($index == 1){
            $acc_active = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_active'))
                -> where(function($query){
                    $query -> where('request','=','SUB')
                        -> orwhere('request','=','GH');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> get();
            return $acc_active;
        }elseif ($index == 2){
            $acc_active = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_active'))
                -> where(function($query){
                    $query -> where('request','=','SUB')
                        -> orwhere('request','=','GH');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date');
            return $acc_active;
        } else {
            $acc_active = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_active'))
                -> where(function($query){
                    $query -> where('request','=','SUB')
                        -> orwhere('request','=','GH');
                })
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('date')
                -> union($countActive_0)
                -> get();
            return $acc_active;
        }
    }

    public function countGH($table,$start,$end,$countGH_0,$index){
        if($index == 1){
            $acc_gh = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_gh'))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> get();
            return $acc_gh;
        }elseif ($index == 2){
            $acc_gh = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_gh'))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime');
            return $acc_gh;
        } else {
            $acc_gh = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_gh'))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> union($countGH_0)
                -> get();
            return $acc_gh;
        }
    }

    public function countDkSMS($table,$start,$end,$countDkSMS_0,$index){
        if($index == 1){
            $acc_dk_sms = DB::table($table)
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_dk_sms'))
            -> where('channel','=','SMS')
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
            -> groupBy('reg_datetime')
            -> get();
            return $acc_dk_sms;
        }elseif ($index == 2){
            $acc_dk_sms = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_dk_sms'))
                -> where('channel','=','SMS')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime');
            return $acc_dk_sms;
        } else {
            $acc_dk_sms = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_dk_sms'))
                -> where('channel','=','SMS')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> union($countDkSMS_0)
                -> get();
            return $acc_dk_sms;
        }
    }

    public function countDkWap($table,$start,$end,$countDkWap_0,$index){
        if($index == 1){
            $acc_dk_wap = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as wap'))
                -> where('channel','=','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> get();
            return $acc_dk_wap;
        }elseif ($index == 2){
            $acc_dk_wap = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as wap'))
                -> where('channel','=','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime');
            return $acc_dk_wap;
        } else {
            $acc_dk_wap = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as wap'))
                -> where('channel','=','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union($countDkWap_0)
                -> groupBy('reg_datetime')
                -> get();
            return $acc_dk_wap;
        }
    }

    public function countDkVasgate($table,$start,$end,$countDkVasgate_0,$index){
        if($index == 1){
            $acc_dk_vasgate = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_vasgate'))
                -> where('channel','<>','SMS')
                -> where('channel','<>','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime')
                -> get();
            return $acc_dk_vasgate;
        }elseif ($index == 2){
            $acc_dk_vasgate = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_vasgate'))
                -> where('channel','<>','SMS')
                -> where('channel','<>','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> groupBy('reg_datetime');
            return $acc_dk_vasgate;
        } else {
            $acc_dk_vasgate = DB::table($table)
                -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_vasgate'))
                -> where('channel','<>','SMS')
                -> where('channel','<>','WAP')
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> groupBy('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union ($countDkVasgate_0)
                -> get();
            return $acc_dk_vasgate;
        }
    }

    public function countPhone($table,$start,$end,$countPhone_0,$index){
        if($index == 1){
            $totalNumber = 0;
            $sum_acc_phone = DB::table($table)
                -> select(DB::raw('COUNT(DISTINCT isdn) as sum'))
                -> whereBetween('reg_datetime', [$start, $end])
                -> whereNotNull('isdn')
                -> get();
            foreach ($sum_acc_phone as $value){
                $totalNumber = $value-> sum;
            }
            return $totalNumber;
        }elseif ($index == 2){
            $sum_acc_phone = DB::table($table)
            -> select(DB::raw('COUNT(DISTINCT isdn) as sum'))
                -> whereBetween('reg_datetime', [$start, $end])
            -> whereNotNull('isdn')
            ->get();
            return $sum_acc_phone;
        } else {
            $totalNumber = 0;
            $sum_acc_phone = DB::table($table)
                -> select(DB::raw('COUNT(DISTINCT isdn) as sum'))
                -> whereNotNull('isdn')
                -> whereBetween('reg_datetime', [$start, $end])
                -> get();
            foreach ($countPhone_0 as $value){
                foreach ($sum_acc_phone as $value2){
                    $totalNumber = $value->sum + $value2->sum;
                }
            }
            return $totalNumber;
        }
    }

    public function listReportDayAction(){
        $total_sub = 0;
        try {
            $total = $this->getQueryDB('', '', '', '', 'totalReport');
            $acc_sub = $this ->getQueryDB('','','','','countSub');
            foreach ($total as $value){
                foreach ($acc_sub as $value1){
                    if($value->date === $value1->date){
                        $value->acc_sub = $value1->count_sub;
                        $total_sub = $total_sub + $value1->count_sub;
                    }
                }
            }
            $total_unsub_pp = 0;
            $acc_unsub_people = $this ->getQueryDB('','','','','countUnSubPP');
            foreach ($total as $value){
                foreach ($acc_unsub_people as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_pp = $value1->count_sub_pp;
                        $total_unsub_pp = $total_unsub_pp + $value1->count_sub_pp;
                    }
                }
            }
            $total_unsub_stm = 0;
            $acc_unsub_system = $this -> getQueryDB('','','','','countUnSubST');
            foreach ($total as $value){
                foreach ($acc_unsub_system as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_stm = $value1->count_sub_stm;
                        $total_unsub_stm = $total_unsub_stm + $value1->count_sub_stm;
                    }
                }
            }
            $total_psc = 0;
            $acc_psc = $this -> getQueryDB('','','','','countPsc');
            foreach ($total as $value){
                foreach ($acc_psc as $value1){
                    if($value->date === $value1->date){
                        $value->acc_psc = $value1->count_psc;
                        $total_psc = $total_psc + $value1->count_psc;
                    }
                }
            }
            $total_active = 0;
            $acc_active = $this -> getQueryDB('','','','','countActive');
            foreach ($total as $value){
                foreach ($acc_active as $value1){
                    if($value->date === $value1->date){
                        $value->acc_active = $value1->count_active;
                        $total_active = $total_active + $value1->count_active;
                    }
                }
            }
            $total_gh = 0;
            $acc_gh = $this -> getQueryDB('','','','','countGH');
            foreach ($total as $value){
                foreach ($acc_gh as $value1){
                    if($value->date === $value1->date){
                        $value->acc_gh = $value1->count_gh;
                        $total_gh = $total_gh + $value1->count_gh;
                    }
                }
            }
            $total_dk_sms = 0;
            $acc_dk_sms = $this -> getQueryDB('','','','','countDkSMS');
            foreach ($total as $value){
                foreach ($acc_dk_sms as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_sms = $value1->count_dk_sms;
                        $total_dk_sms = $total_dk_sms + $value1->count_dk_sms;
                    }
                }
            }
            $total_dk_wap = 0;
            $acc_dk_wap = $this -> getQueryDB('','','','','countDkWap');
            foreach ($total as $value){
                foreach ($acc_dk_wap as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_wap = $value1->wap;
                        $total_dk_wap = $total_dk_wap + $value1->wap;
                    }
                }
            }
            $total_dk_vasgate = 0;
            $acc_dk_vasgate = $this -> getQueryDB('','','','','countDkVasgate');
            foreach ($total as $value){
                foreach ($acc_dk_vasgate as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_vasgate = $value1->count_sub_vasgate;
                        $total_dk_vasgate = $total_dk_vasgate + $value1->count_sub_vasgate;
                    }
                }
            }
            $sum_acc_phone = $this -> getQueryDB('','','','','countPhone');
            $total_revenue_day = 0;
            $revenue_day = $this -> getQueryDB('','','','','revenueDay');
            foreach ($total as $value){
                foreach ($revenue_day as $value1){
                    if($value->date === $value1->date){
                        $value->revenue_day = $value1->revenue_day;
                        $total_revenue_day = $total_revenue_day + $value1->revenue_day;
                    }
                }
            }
            return view('report.report_day')->with([
                'total'=>$total,
                'sum' =>$sum_acc_phone,
                'total_sub'=>$total_sub,
                'total_unsub_pp'=>$total_unsub_pp,
                'total_unsub_stm'=>$total_unsub_stm,
                'total_psc'=>$total_psc,
                'total_active'=>$total_active,
                'total_gh'=>$total_gh,
                'total_dk_sms'=>$total_dk_sms,
                'total_dk_wap'=>$total_dk_wap,
                'total_dk_vasgate' => $total_dk_vasgate,
                'total_revenue_day' => $total_revenue_day]);

        }catch (QueryException $ex){
            $total = [];
            return view('report.report_day')->with([
                'total'=>$total,
                'sum' =>1,
                'total_sub'=>0,
                'total_unsub_pp'=>0,
                'total_unsub_stm'=>0,
                'total_psc'=>0,
                'total_active'=>0,
                'total_gh'=>0,
                'total_dk_sms'=>0,
                'total_dk_wap'=>0,
                'total_dk_vasgate' => 0,
                'total_revenue_day' => 0
            ]);
        }
    }


    public function SearchDateTime(Request $request)
    {
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;
        try {
            $total = $this->getQueryDB($start, $end, '', '', 'totalReport');
            $count_sub = 0;
            $acc_sub = $this ->getQueryDB($start,$end,'','','countSub');
            foreach ($total as $value){
                foreach ($acc_sub as $value1){
                    if($value->date === $value1->date){
                        $value->acc_sub = $value1->count_sub;
                        $count_sub = $count_sub + $value1->count_sub;
                    }
                }
            }
            $count_unsub_pp = 0;
            $acc_unsub_people = $this ->getQueryDB($start,$end,'','','countUnSubPP');
            foreach ($total as $value){
                foreach ($acc_unsub_people as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_pp = $value1->count_sub_pp;
                        $count_unsub_pp = $count_unsub_pp + $value1->count_sub_pp;
                    }
                }
            }
            $count_unsub_stm = 0;
            $acc_unsub_system = $this -> getQueryDB($start,$end,'','','countUnSubST');
            foreach ($total as $value){
                foreach ($acc_unsub_system as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_stm = $value1->count_sub_stm;
                        $count_unsub_stm = $count_unsub_stm + $value1->count_sub_stm;
                    }
                }
            }
            $count_psc = 0;
            $acc_psc = $this -> getQueryDB($start,$end,'','','countPsc');
            foreach ($total as $value){
                foreach ($acc_psc as $value1){
                    if($value->date === $value1->date){
                        $value->acc_psc = $value1->count_psc;
                        $count_psc = $count_psc + $value1->count_psc;
                    }
                }
            }
            $count_active = 0;
            $acc_active = $this -> getQueryDB($start,$end,'','','countActive');
            foreach ($total as $value){
                foreach ($acc_active as $value1){
                    if($value->date === $value1->date){
                        $value->acc_active = $value1->count_active;
                        $count_active = $count_active + $value1->count_active;
                    }
                }
            }
            $count_gh = 0;
            $acc_gh = $this -> getQueryDB($start,$end,'','','countGH');
            foreach ($total as $value){
                foreach ($acc_gh as $value1){
                    if($value->date === $value1->date){
                        $value->acc_gh = $value1->count_gh;
                        $count_gh = $count_gh + $value1->count_gh;
                    }
                }
            }
            $count_dk_sms = 0;
            $acc_dk_sms = $this -> getQueryDB($start,$end,'','','countDkSMS');
            foreach ($total as $value){
                foreach ($acc_dk_sms as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_sms = $value1->count_dk_sms;
                        $count_dk_sms = $count_dk_sms + $value1->count_dk_sms;
                    }
                }
            }
            $count_dk_wap = 0;
            $acc_dk_wap = $this -> getQueryDB($start,$end,'','','countDkWap');
            foreach ($total as $value){
                foreach ($acc_dk_wap as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_wap = $value1->wap;
                        $count_dk_wap = $count_dk_wap + $value1->wap;
                    }
                }
            }
            $count_dk_vasgate = 0;
            $acc_dk_vasgate = $this -> getQueryDB($start,$end,'','','countDkVasgate');
            foreach ($total as $value){
                foreach ($acc_dk_vasgate as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_vasgate = $value1->count_sub_vasgate;
                        $count_dk_vasgate = $count_dk_vasgate + $value1->count_sub_vasgate;
                    }
                }
            }
            $total_revenue_day = 0;
            $revenue_day = $this -> getQueryDB($start,$end,'','','revenueDay');
            foreach ($total as $value){
                foreach ($revenue_day as $value1){
                    if($value->date === $value1->date){
                        $value->revenue_day = $value1->revenue_day;
                        $total_revenue_day = $total_revenue_day + $value1->revenue_day;
                    }
                }
            }
            $sum_acc_phone = $this -> getQueryDB($start,$end,'','','countPhone');

                foreach ($total as $key => $value) {
                    $result .= '<tr>';
                    $result .= '<td>' . ($key + 1) . '</td>';
                    $result .= '<td>' . $value->date . '</td>';
                    $result .= '<td>' . $value->acc_sub . '</td>';
                    $result .= '<td>' . $value->acc_unsub_pp . '</td>';
                    $result .= '<td>' . $value->acc_unsub_stm . '</td>';
                    $result .= '<td>' . $value->acc_psc . '</td>';
                    $result .= '<td>' . $value->acc_active . '</td>';
                    $result .= '<td>' . round(($value->acc_gh / $sum_acc_phone) * 100, 3) . ' %</td>';
                    $result .= '<td>' . $value->acc_dk_sms . '</td>';
                    $result .= '<td>' . $value->acc_dk_vasgate . '</td>';
                    $result .= '<td>' . $value->acc_dk_wap . '</td>';
                    $result .= '<td>' . ($value->acc_dk_sms + $value->acc_dk_vasgate + $value->acc_dk_wap) . '</td>';
                    $result .= '<td>' . $value->revenue_day . '</td>';
                    $result .= '</tr>';
                }
            $result .= '<tr>';
            $result .= '<td> Tổng </td>';
            $result .= '<td></td>';
            $result .= '<td>'.$count_sub.'</td>';
            $result .= '<td>'.$count_unsub_pp.'</td>';
            $result .= '<td>'.$count_unsub_stm.'</td>';
            $result .= '<td>'.$count_psc.'</td>';
            $result .= '<td>'.$count_active.'</td>';
            $result .= '<td></td>';
            $result .= '<td>'.$count_dk_sms.'</td>';
            $result .= '<td>'.$count_dk_vasgate.'</td>';
            $result .= '<td>'.$count_dk_wap.'</td>';
            $result .= '<td></td>';
            $result .= '<td>'.$total_revenue_day.'</td></tr>';
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
        }
        return $result;
    }

    public function exportFile($datetime){

        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));

        try {
            $total = $this->getQueryDB($start, $end, '', '', 'totalReport');
            $acc_sub = $this ->getQueryDB($start,$end,'','','countSub');
            foreach ($total as $value){
                foreach ($acc_sub as $value1){
                    if($value->date === $value1->date){
                        $value->acc_sub = $value1->count_sub;
                    }
                }
            }
            $acc_unsub_people = $this ->getQueryDB($start,$end,'','','countUnSubPP');
            foreach ($total as $value){
                foreach ($acc_unsub_people as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_pp = $value1->count_sub_pp;
                    }
                }
            }
            $acc_unsub_system = $this -> getQueryDB($start,$end,'','','countUnSubST');
            foreach ($total as $value){
                foreach ($acc_unsub_system as $value1){
                    if($value->date === $value1->date){
                        $value->acc_unsub_stm = $value1->count_sub_stm;
                    }
                }
            }
            $acc_psc = $this -> getQueryDB($start,$end,'','','countPsc');
            foreach ($total as $value){
                foreach ($acc_psc as $value1){
                    if($value->date === $value1->date){
                        $value->acc_psc = $value1->count_psc;
                    }
                }
            }
            $acc_active = $this -> getQueryDB($start,$end,'','','countActive');
            foreach ($total as $value){
                foreach ($acc_active as $value1){
                    if($value->date === $value1->date){
                        $value->acc_active = $value1->count_active;
                    }
                }
            }
            $acc_gh = $this -> getQueryDB($start,$end,'','','countGH');
            foreach ($total as $value){
                foreach ($acc_gh as $value1){
                    if($value->date === $value1->date){
                        $value->acc_gh = $value1->count_gh;
                    }
                }
            }
            $acc_dk_sms = $this -> getQueryDB($start,$end,'','','countDkSMS');
            foreach ($total as $value){
                foreach ($acc_dk_sms as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_sms = $value1->count_dk_sms;
                    }
                }
            }
            $acc_dk_wap = $this -> getQueryDB($start,$end,'','','countDkWap');
            foreach ($total as $value){
                foreach ($acc_dk_wap as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_wap = $value1->wap;
                    }
                }
            }

            $acc_dk_vasgate = $this -> getQueryDB($start,$end,'','','countDkVasgate');
            foreach ($total as $value){
                foreach ($acc_dk_vasgate as $value1){
                    if($value->date === $value1->date){
                        $value->acc_dk_vasgate = $value1->count_sub_vasgate;
                    }
                }
            }
            $revenue_day = $this -> getQueryDB($start,$end,'','','revenueDay');
            foreach ($total as $value){
                foreach ($revenue_day as $value1){
                    if($value->date === $value1->date){
                        $value->revenue_day = $value1->revenue_day;
                    }
                }
            }
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        $result = $total-> transform(function ($item){
           return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/report.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\report.csv');
        $file1 = file_get_contents($file);
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }

}
