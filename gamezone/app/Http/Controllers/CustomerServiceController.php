<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CustomerServiceController extends Controller
{
    public function listTable($from ,$to ,$nameSub){
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

    public function regTransactions(){
        $user_id_sign_in = Auth::id();
        $name_use = DB::table('manager_user')->find($user_id_sign_in);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $reg_tran = $this->getQueryRegTransactions("","","",$name_use->first_name);
        return view('customer_service.reg_transactions')->with('reg_tran',$reg_tran);
    }

    public function getQueryRegTransactions($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $regTran_acc = $this->getRegTransaction($table, $start, $end, null, 1);
        }
        else {
            $regTran_acc1 = $this->getRegTransaction($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $regTran_acc = $this->getRegTransaction($table[$i], $start, $end, $regTran_acc1,3);
            }
        }
        return $regTran_acc;

    }

    public function getRegTransaction($table,$start,$end,$regtb,$index){
        if($index = 1){
            $reg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> get();
            return $reg_tran;
        } else if($index = 3){
            $reg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end]);
            return $reg_tran;
        }else{
            $reg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
                -> where('request','=','SUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union($regtb)
                -> get();
            return $reg_tran;
        }
    }

    public function unregTransactions(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $datestart = $arrdate[0].'-'.$arrdate[1].'-01';
        $unreg_tran = $this->getUnRegTransaction($datestart,$datenow,"");
        return view('customer_service.unreg_transactions')->with('unreg_tran',$unreg_tran);
    }

    public function getQueryUnRegTransactions($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $unregTran_acc = $this->getUnRegTransaction($table, $start, $end, null, 1);
        }
        else {
            $unregTran_acc1 = $this->getUnRegTransaction($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $unregTran_acc = $this->getUnRegTransaction($table[$i], $start, $end, $unregTran_acc1,3);
            }
        }
        return $unregTran_acc;

    }

    public function getUnRegTransaction($table,$start,$end,$unRegTran,$index){
        if($index = 1){
            $unreg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
                -> where('request','=','UNSUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> get();
            return $unreg_tran;
        } elseif ($index = 2){
            $unreg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
                -> where('request','=','UNSUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end]);
            return $unreg_tran;
        } else{
            $unreg_tran = DB::table($table)
                -> select('isdn','reg_datetime','package_code')
                -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
                -> where('request','=','UNSUB')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union($unRegTran)
                -> get();
            return $unreg_tran;
        }

    }

    public function moMt(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $datestart = $arrdate[0].'-'.$arrdate[1].'-01';
        $momt = $this -> getQueryMOMT($datenow,$datenow,"");
        return view('customer_service.momt')->with('momt',$momt);
    }

    public function getQueryMOMT($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'mtobj_');
        }else{
            $table = $this->listTable($dateend,$datenow,'mtobj_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $momt = $this->getMOMT($table, $start, $end, null, 1);
        }
        else {
            $momt1 = $this->getMOMT($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $momt = $this->getMOMT($table[$i], $start, $end, $momt1,3);
            }
        }
        return $momt;

    }

    public function getMOMT($table,$start,$end,$momt_0,$index){
        if($index = 1){
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> addSelect(DB::raw("'null' as result"))
                -> get();
            return $momt;
        }elseif ($index = 2){
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> addSelect(DB::raw("'null' as result"));
            return $momt;
        }else{
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> addSelect(DB::raw("'null' as result"))
                -> union($momt_0)
                -> get();
            return $momt;
        }

    }

    public function historyAccount(){
        $history_acc = $this->getQueryHistoryAcc('','','','');

        foreach ($history_acc as $value){
            if($value->message_send == null && $value->request != 'GH'){
                $value->message_send = "Không thành công";
            }else{
                $value->message_send = "Thành công";
            }
        }

        foreach ($history_acc as $value){
            if ($value->request == 'SUB'){
                $value->request = "Đăng ký gói cước";
            }else{
                $value->request = "Gia hạn gói cước";
            }

        }

        return view('customer_service.history_trucuoc')->with('history_acc',$history_acc);
    }

    public function getQueryHistoryAcc($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $hisAcc = $this->getHistoryAccout($table, $start, $end, null, 1);
        }
        else {
            $hisAcc1 = $this->getHistoryAccout($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $hisAcc = $this->getHistoryAccout($table[$i], $start, $end, $hisAcc1,3);
            }
        }
        return $hisAcc;

    }

    public function getHistoryAccout($table,$start,$end,$his_acc_0,$index){
        if($index = 1){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> where('request','=','SUB')
                ->orWhere('request','=','GH')
                -> get();
            return $history_acc;
        }elseif ( $index = 2){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> where('request','=','SUB')
                ->orWhere('request','=','GH');
            return $history_acc;
        }else{
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> where('request','=','SUB')
                -> orWhere('request','=','GH')
                -> union($his_acc_0)
                -> get();
            return $history_acc;
        }
    }

    public function getHistoryAccountUse($table,$start,$end,$his_acc_use_0,$index){
        if($index = 1){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereNotNull('reg_datetime')
                -> get();
            return $history_acc;
        }elseif ($index = 2){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereNotNull('reg_datetime');
            return $history_acc;
        }else{
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereNotNull('reg_datetime')
                -> union($his_acc_use_0)
                -> get();
            return $history_acc;
        }
    }

    public function getQueryHistoryAccUse($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $hisAccUse = $this->getHistoryAccountUse($table, $start, $end, null, 1);
        }
        else {
            $hisAccUse1 = $this->getHistoryAccountUse($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $hisAccUse = $this->getHistoryAccountUse($table[$i], $start, $end, $hisAccUse1,3);
            }
        }
        return $hisAccUse;

    }

    public function historyAccountUse(){

//        $history_acc = $this->getDataHistoryAccUse();
        $history_acc = $this->getQueryHistoryAccUse('','','','');

        foreach ($history_acc as $value){
            if($value->message_send == null && $value->request != 'GH'){
                $value->message_send = "Không thành công";
            }else{
                $value->message_send = "Thành công";
            }
        }

        foreach ($history_acc as $value){
            if ($value->request == 'SUB'){
                $value->request = "Đăng ký gói cước";
            }else if ($value->request == 'GH'){
                $value->request = "Gia hạn gói cước";
            }else{
                $value->request = "Hủy gói cước";
            }

        }
        return view('customer_service.history_acc_use')->with('history_acc_use',$history_acc);
    }

    public function getExtendAcc($table,$start,$end,$extend_acc_0,$index){
        if($index = 1){
            $exten_acc = DB::table($table)
                -> select('isdn','reg_datetime','package_code','channel','charge_price')
                -> addSelect(DB::raw("'Gia Hạn' as type"))
                -> addSelect(DB::raw("'Thành Công' as tt"))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime')
                -> get();
            return $exten_acc;
        }elseif ($index = 2){
            $exten_acc = DB::table($table)
                -> select('isdn','reg_datetime','package_code','channel','charge_price')
                -> addSelect(DB::raw("'Gia Hạn' as type"))
                -> addSelect(DB::raw("'Thành Công' as tt"))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime');
            return $exten_acc;
        }else{
            $exten_acc = DB::table($table)
                -> select('isdn','reg_datetime','package_code','channel','charge_price')
                -> addSelect(DB::raw("'Gia Hạn' as type"))
                -> addSelect(DB::raw("'Thành Công' as tt"))
                -> where('request','=','GH')
                -> whereNotNull('reg_datetime')
                -> union($extend_acc_0)
                -> get();
            return $exten_acc;
        }
    }

    public function getQueryExtendAcc($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $hisAccUse = $this->getExtendAcc($table, $start, $end, null, 1);
        }
        else {
            $hisAccUse1 = $this->getExtendAcc($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $hisAccUse = $this->getExtendAcc($table[$i], $start, $end, $hisAccUse1,3);
            }
        }
        return $hisAccUse;

    }

    public function extenAcc(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
//        $exten_acc = $this ->getHistoryRenew($dateend,$datenow,'','');
        $exten_acc = $this->getQueryExtendAcc('','','','');
        return view('customer_service.exten_acc')->with('exten_acc',$exten_acc);
    }

    public  function getDistinctPhone($time){
        $subUnsub_acc_phone = DB::table($time)
            -> select(DB::raw('DISTINCT isdn'))
            -> whereNotNull('isdn')
            -> get();
        return $subUnsub_acc_phone;
    }

    public  function getListInforPhone($time,$isdn){
        $infor_phone = DB::table($time)
            -> select('id','isdn','package_code','channel','sta_datetime','expire_datetime','end_datetime','request')
            -> addSelect(DB::raw("'Không có gói cước' as tt"))
            -> addSelect(DB::raw("'Không' as gh"))
            -> where('isdn','=',$isdn)
            -> orderBy('id','DESC')
            -> first();
        return $infor_phone;
    }

    public  function subUnSubAcc(){
        $subUnsub_acc_phone = $this->getDistinctPhone('cdr_201908');
        $result = [];
        foreach ($subUnsub_acc_phone as $key => $value){
            $infor_phone = $this->getListInforPhone('cdr_201908',$value->isdn);
            $result[$key] = $infor_phone;
        }
        foreach ($result as $value){
            if($value->request == 'GH' ){
                $value->tt = "Có Gói Cước";
                $value->gh = "Có";
            }else if($value->request == 'SUB'){
                $value->tt = "Có Gói Cước";
            }

        }
        foreach ($result as $value){
            if($value->request == 'GH' || $value->request == 'SUB' ){
                $value->request = "Hủy Đăng Ký";
            }else{
                $value->request = "Đăng Ký";
            }
        }
//        dd($result);
        return view('customer_service.sub_unsub_acc')->with('sub_unsub',$result);
    }

    public function subUnSubViewUpdate($id,$epiTime){
        $arr = explode("-",$epiTime);
        $name_table = 'cdr_'.$arr[0].$arr[1];
        $data = DB::table($name_table)
            ->find($id);
        return view('customer_service.infor_acc')->with('data',$data);
    }

    public function subUnSubUpdateRequest(Request $request){
        $data = DB::table('cdr_201908')->find($request->account_id);
        if($request->account_request == "GH" || $request->account_request == "SUB"){
            $createUnSub = DB::table('cdr_201908')->insert([
               'isdn' => $data->isdn,
                'request' => "UNSUB",
                'service_code' =>$data->service_code,
                'group_code' => $data->group_code,
                'package_code' => $data->package_code,
                'command_code' => $data->command_code,
                'reg_datetime' => $data->reg_datetime,
                'sta_datetime' => $data->sta_datetime,
                'end_datetime' => $data->end_datetime,
                'expire_datetime' => $data->expire_datetime,
                'status' =>$data->status,
                'channel' => $data->channel,
                'charge_price' => $data->charge_price,
                'message_send' =>$data->message_send,
                'org_request' => $data->org_request,
                'date_receive_request' => $data->date_receive_request
            ]);
        } else {
            $createUnSub = DB::table('cdr_201908')->insert([
                'isdn' => $data->isdn,
                'request' => "SUB",
                'service_code' =>$data->service_code,
                'group_code' => $data->group_code,
                'package_code' => $data->package_code,
                'command_code' => $data->command_code,
                'reg_datetime' => $data->reg_datetime,
                'sta_datetime' => $data->sta_datetime,
                'end_datetime' => $data->end_datetime,
                'expire_datetime' => $data->expire_datetime,
                'status' =>$data->status,
                'channel' => $data->channel,
                'charge_price' => $data->charge_price,
                'message_send' =>$data->message_send,
                'org_request' => $data->org_request,
                'date_receive_request' => $data->date_receive_request
            ]);
        }
        $notification = array(
            'message' => 'Cập nhật thông tin thành công!',
            'alert-type' => 'success'
        );

        return Redirect::back()->with($notification);
    }


    // Ajax search data User - Reg
    public  function  SearchDateTimeRegTran(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;
        $user_id_sign_in = Auth::id();
        $name_use = DB::table('manager_user')->find($user_id_sign_in);
        $reg_tran = $this->getRegTransaction($start,$end,"",$name_use->first_name);

        foreach($reg_tran as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . substr($value->regDatetime,-9,8).', '.substr($value->regDatetime,0,10) . '</td>';
            $result .= '<td>' . 'Đăng ký gói dịch vụ'. '</td>';
            $result .= '<td>' . $value->packageCode . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

//    public function getSearchRegTran($time,$start, $end){
//        $reg_tran = DB::table($time)
//            -> select('isdn','reg_datetime','package_code')
//            -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
//            -> where('request','=','SUB')
//            -> whereNotNull('reg_datetime')
//            -> whereBetween('reg_datetime',[$start,$end])
//            -> get();
//        return $reg_tran;
//    }

    // Ajax search data User - UnReg
    public  function  SearchDateTimeUnRegTran(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $unreg_tran = $this->getUnRegTransaction($start,$end,'');

        foreach($unreg_tran as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . substr($value->regDatetime,-9,8).', '.substr($value->regDatetime,0,10) . '</td>';
            $result .= '<td> Hủy Dịch Vụ Gói</td>';
            $result .= '<td>' . $value->packageCode . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    // Ajax search data MOMT
    public  function SearchDateTimeMOMT(Request $request)
    {
        $startEnd = $request->startEnd;
        $date_range = explode(' - ', $startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $momt = $this->getMOMT($start,$end,"");

        foreach($momt as $key => $value) {
            if (count($value->joinDTOS) > 0) {
                foreach ($value->joinDTOS as $valueDtos) {
                $result .= '<tr>';
                $result .= '<td>' . $value->groupCode . '</td>';
                $result .= '<td>' . $value->isdn . '</td>';
                $result .= '<td>' . substr($value->dateReceiveRequest, -17, 8) . ', ' . substr($value->dateReceiveRequest, 0, 10) . '</td>';
                $result .= '<td>' . $value->commandCode . '</td>';
                    $result .= '<td>' . substr($valueDtos->timerequest, -17, 8) . ', ' . substr($valueDtos->timerequest, 0, 10) . '</td>';
                    $result .= '<td>' . $valueDtos->content . '</td>';
                    $result .= '<td>Thành Công</td>';
                    $result .= '<td><a href="#" class="btn dropdown-item">
                                            <i class="fas fa-edit"> Gửi lại</i>
                                        </a></td>';
                    $result .= '</tr>';
                }
            } else {
                $result .= '<tr>';
                $result .= '<td>' . $value->groupCode . '</td>';
                $result .= '<td>' . $value->isdn . '</td>';
                $result .= '<td>' . substr($value->dateReceiveRequest, -17, 8) . ', ' . substr($value->dateReceiveRequest, 0, 10) . '</td>';
                $result .= '<td>' . $value->commandCode . '</td>';
                $result .= '<td></td>
                            <td></td>
                            <td></td>
                            <td></td>';
                $result .= '</tr>';
            }
        }
        return $result;
    }

    public function getSearchMOMT($time,$start,$end){
        $momt = DB::table($time)
            ->select('username', 'isdn', 'timerequest', 'command_code', 'timeaction', 'content')
            ->addSelect(DB::raw("'null' as result"))
            ->whereBetween('timerequest', [$start, $end])
            ->get();
        return $momt;
    }

    public  function SearchDateTimeHisAcc(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $history_acc = $this->getSearchTimeHisAcc('cdr_201908',$start,$end);

        foreach ($history_acc as $value){
            if($value->message_send == null && $value->request != 'GH'){
                $value->message_send = "Không thành công";
            }else{
                $value->message_send = "Thành công";
            }
        }

        foreach ($history_acc as $value){
            if ($value->request == 'SUB'){
                $value->request = "Đăng ký gói cước";
            }else{
                $value->request = "Gia hạn gói cước";
            }

        }
        foreach($history_acc as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->reg_datetime . '</td>';
            $result .= '<td>' . $value->request . '</td>';
            $result .= '<td>' . $value->package_code . '</td>';
            $result .= '<td>' . $value->message_send . '</td>';
            $result .= '<td>' . $value->channel . '</td>';
            $result .= '<td>' . $value->charge_price . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    public  function  getSearchTimeHisAcc($time,$start,$end){

        $history_acc = DB::table($time)
            -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> where(function ($query){
                $query -> where('request','=','SUB')
                    -> orWhere('request','=','GH');
            })
            -> get();
        return $history_acc;
    }

    /*
     * Ajax to search history Acc use Data
     */
    public  function SearchDateTimeHisAccUse(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $history_acc = $this->getDataHistoryAccUse($start,$end,'','');

        foreach ($history_acc as $value){
            if($value->message_send == null && $value->request != 'GH'){
                $value->message_send = "Không thành công";
            }else{
                $value->message_send = "Thành công";
            }
        }

        foreach ($history_acc as $value){
            if ($value->request == 'SUB'){
                $value->request = "Đăng ký gói cước";
            }else if ($value->request == 'GH'){
                $value->request = "Gia hạn gói cước";
            }else{
                $value->request = "Hủy gói cước";
            }

        }

        foreach($history_acc as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->reg_datetime . '</td>';
            $result .= '<td>' . $value->message_send . '</td>';
            $result .= '<td>' . $value->request . '</td >';
            $result .= '<td>'.$value->channel. '</td >';
            $result .= '<td>' . $value->package_code . '</td>';
            $result .= '<td>' . $value->charge_price . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    /*
     * get all table with list table
     */

    public function getDataHistoryAccUse($start  ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $exten_acc = $this->getSearchHisAccUse($table, $start, $end, null, 1);
        }
        else {
            $exten_acc1 = $this->getSearchHisAccUse($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $exten_acc = $this->getSearchHisAccUse($table[$i], $start, $end, $exten_acc1,3);
            }
        }
        return $exten_acc;

    }

    /* Query Database history_acc_use
       input
       $start, $end time
       $time - name table
       $his_acc_first - table to union
       index - compare to select query
    */
    public function getSearchHisAccUse($time,$start,$end,$his_acc_first,$index){
        if($index == 1) {
            $history_acc = DB::table($time)
                ->select('isdn', 'reg_datetime', 'request', 'channel', 'package_code', 'charge_price', 'message_send')
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end])
                ->get();
            return $history_acc;
        }
        if($index == 2){
            $history_acc = DB::table($time)
                ->select('isdn', 'reg_datetime', 'request', 'channel', 'package_code', 'charge_price', 'message_send')
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end]);
            return $history_acc;
        }
        else{
            $history_acc = DB::table($time)
                ->select('isdn', 'reg_datetime', 'request', 'channel', 'package_code', 'charge_price', 'message_send')
                ->whereNotNull('reg_datetime')
                ->whereBetween('reg_datetime', [$start, $end])
                ->union($his_acc_first)
                ->get();
            return $history_acc;
        }
    }

    /*
     * Ajax return result search Exten Acc
     */
    public function SearchDateTimeExtenAcc(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $exten_acc = $this->getHistoryRenew($start,$end,"","");

        foreach($exten_acc as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->reg_datetime . '</td>';
            $result .= '<td>' . $value->type . '</td>';
            $result .= '<td>' . $value->package_code . '</td>';
            $result .= '<td>' . $value->tt . '</td>';
            $result .= '<td>' . $value->channel . '</td>';
            $result .= '<td>' . $value->charge_price . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }


    /* Query date to database
       input
       $start, $end time
       $time - name table
       $exten - table to union
       index - compare to select query
    */
    public function getSearchExtenAcc($time,$start,$end,$exten,$index){
        if($index == 1){
            $exten_acc = DB::table($time)
                -> select('isdn','reg_datetime','package_code','channel','charge_price')
                -> addSelect(DB::raw("'Gia Hạn' as type"))
                -> addSelect(DB::raw("'Thành Công' as tt"))
                -> where('request','=','GH')
                -> whereBetween('reg_datetime',[$start,$end])
                -> whereNotNull('reg_datetime')
                -> get();
            return $exten_acc;
        }
        if($index = 2){
            $exten_acc = DB::table($time)
                -> select('isdn','reg_datetime','package_code','channel','charge_price')
                -> addSelect(DB::raw("'Gia Hạn' as type"))
                -> addSelect(DB::raw("'Thành Công' as tt"))
                -> where('request','=','GH')
                -> whereBetween('reg_datetime',[$start,$end])
                -> whereNotNull('reg_datetime');
            return $exten_acc;
        }else {
            $exten_acc = DB::table($time)
                ->select('isdn', 'reg_datetime', 'package_code', 'channel', 'charge_price')
                ->addSelect(DB::raw("'Gia Hạn' as type"))
                ->addSelect(DB::raw("'Thành Công' as tt"))
                ->where('request', '=', 'GH')
                ->whereBetween('reg_datetime', [$start, $end])
                ->whereNotNull('reg_datetime')
                ->union($exten)
                ->get();
            return $exten_acc;
        }
    }


    /*  get History Renew with time
        input
        $start, $end, $phone , $username(maybe user)
    */
    public function getHistoryRenew($start ,$end ,$phone ,$username ){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
        if($start != null && $end != null){
            $table = $this->listTable($start,$end,'cdr_');
        }else{
            $table = $this->listTable($dateend,$datenow,'cdr_');
        }
        if (substr($start,5,2) == substr($end,5,2) && substr($start,0,4)== substr($start,0,4)) {
            $exten_acc = $this->getSearchExtenAcc($table, $start, $end, null, 1);
        }
        else {
            $exten_acc1 = $this->getSearchExtenAcc($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $exten_acc2 = $this->getSearchExtenAcc($table[$i], $start, $end, $exten_acc1,3);
            }
            $exten_acc = $exten_acc2->get();
        }
        return $exten_acc;
    }

    public function  HistoryLog(){
        $history_log = DB::table('history_action')
            -> get();
        return view('customer_service.history_log')->with('history_log',$history_log);
    }

    public function  UploadFileSub(){
        return view('customer_service.uploadFile_Sub');
    }

    public function DoUploadToSub(Request $request){
        //Kiểm tra file

        if ($request->hasFile('filesTest')) {
            $file = $request->filesTest;
            $name = $file->getClientOriginalName();

            $file->move('upload_file',$file->getClientOriginalName());

            $notification = array(
                'message' => 'Upload file thành công!',
                'alert-type' => 'success'
            );

            $csv = array();
            $file = fopen('../public/upload_file/'.$file->getClientOriginalName(), 'r');

            while (($result = fgetcsv($file)) !== false)
            {
                $csv[] = $result;
            }

            fclose($file);
            unlink('../public/upload_file/'.$name);

//            dd($csv);
            $phone = [];
            $channel = [];
            $chargePrice = [];
            $commandCode = [];
            $endDatetime = [];
            $expireDatetime = [];
            $groupCode = [];
            $packageCode = [];
            $regDatetime = [];
            $staDatetime = [];

            $patern = '/[0-9]{10}/';

            foreach ($csv as $value){
                if (preg_match($patern,$value[1])) {
                    array_push($phone, $value[1]);
                    array_push($channel,$value[2]);
                    array_push($chargePrice,$value[3]);
                    array_push($commandCode,$value[4]);
                    array_push($endDatetime,$value[5]);
                    array_push($expireDatetime,$value[6]);
                    array_push($groupCode,$value[7]);
                    array_push($packageCode,$value[8]);
                    array_push($regDatetime,$value[9]);
                    array_push($staDatetime,$value[10]);
                }
            }
            if($request['day'] == null && $request['week'] == null){
                $notification = array(
                    'message' => 'Chọn gói cước cho danh sách!',
                    'alert-type' => 'error'
                );
            }else {
                if ($request['day'] == 'day') {
                    for ($i = 0; $i < sizeof($phone);$i ++) {
                        $this->userRegs($phone[$i],$channel[$i],$chargePrice[$i],$commandCode[$i],$endDatetime[$i],$expireDatetime[$i],$groupCode[$i],$packageCode[$i],$regDatetime[$i],$staDatetime[$i]);
//                        $this->subDayWithPhone($phone[$i]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($phone);$i ++) {
                        $this->userRegs($phone[$i],$channel[$i],$chargePrice[$i],$commandCode[$i],$endDatetime[$i],$expireDatetime[$i],$groupCode[$i],$packageCode[$i],$regDatetime[$i],$staDatetime[$i]);
//                        $this->subWeekWithPhone($phone[$i]);
                    }
                }
            }
            return Redirect::back()->with($notification);
        }
    }

    public function userRegs($phone,$channel,$chargePrice,$commandCode,$endDatetime,$expireDatetime,$groupCode,$packageCode,$regDatetime,$staDatetime){
        $data = array(
            "channel" => $channel,
            "chargePrice" => $chargePrice,
            "commandCode" => $commandCode,
            "endDatetime" => $endDatetime,
            "expireDatetime" => $expireDatetime,
            "groupCode" => $groupCode,
            "isdn" => $phone,
            "messageSend" => "",
            "orgRequest" => "string",
            "packageCode" => $packageCode,
            "regDatetime" => $regDatetime,
            "serviceCode" => "",
            "staDatetime" => $staDatetime,
            "status" => 0
        );
        $data_string = json_encode($data);

        $curl = curl_init('http://192.168.100.4:9000/api/user-regs');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhZG1pbiIsImF1dGgiOiJST0xFX0FETUlOLFJPTEVfVVNFUiIsImV4cCI6MTU5NDE5NTE1NX0.jm9bQk97-7AYTsN8lgOixbUoG7-psPGoDMIa-L2ZIx8P3T9F_hXIYSczn-m6qEkxu9XJScAaTlGxB8IigZlPYw')
        );

        $result = curl_exec($curl);
        $dd = json_decode($result);
        curl_close($curl);
    }

    public function DoUploadToUnSub(Request $request){
        //Kiểm tra file
//        dd($request);
        if ($request->hasFile('filesTest1')) {
            $file = $request->filesTest1;
            $name = $file->getClientOriginalName();

            $file->move('upload_file',$file->getClientOriginalName());

            $csv = array();
            $file = fopen('../public/upload_file/'.$file->getClientOriginalName(), 'r');

            while (($result = fgetcsv($file)) !== false)
            {
                $csv[] = $result;
            }

            fclose($file);
            unlink('../public/upload_file/'.$name);
//            dd($csv);
            $phone = [];
            $channel = [];
            $chargePrice = [];
            $commandCode = [];
            $endDatetime = [];
            $expireDatetime = [];
            $groupCode = [];
            $packageCode = [];
            $regDatetime = [];
            $staDatetime = [];

            $patern = '/[0-9]{10}/';
            foreach ($csv as $value){
                if (preg_match($patern,$value[1])) {
                    array_push($phone, $value[1]);
                    array_push($channel,$value[2]);
                    array_push($chargePrice,$value[3]);
                    array_push($commandCode,$value[4]);
                    array_push($endDatetime,$value[5]);
                    array_push($expireDatetime,$value[6]);
                    array_push($groupCode,$value[7]);
                    array_push($packageCode,$value[8]);
                    array_push($regDatetime,$value[9]);
                    array_push($staDatetime,$value[10]);
                }
            }
            for ($i = 0; $i < sizeof($phone);$i ++){
                $this->userRegs($phone[$i],$channel[$i],$chargePrice[$i],$commandCode[$i],$endDatetime[$i],$expireDatetime[$i],$groupCode[$i],$packageCode[$i],$regDatetime[$i],$staDatetime[$i]);

            }

            $notification = array(
                'message' => 'Upload file thành công!',
                'alert-type' => 'success'
            );

            return Redirect::back()->with($notification);
        }
    }

    public function userUnRegs($phone,$channel,$chargePrice,$commandCode,$endDatetime,$expireDatetime,$groupCode,$packageCode,$regDatetime,$staDatetime){
        $data = array(
            "channel" => $channel,
            "chargePrice" => $chargePrice,
            "commandCode" => $commandCode,
            "endDatetime" => $endDatetime,
            "expireDatetime" => $expireDatetime,
            "groupCode" => $groupCode,
            "isdn" => $phone,
            "messageSend" => "",
            "orgRequest" => "string",
            "packageCode" => $packageCode,
            "regDatetime" => $regDatetime,
            "serviceCode" => "",
            "staDatetime" => $staDatetime,
            "status" => 0
        );
        $data_string = json_encode($data);

        $curl = curl_init('http://192.168.100.4:9000/api/user-cancels');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhZG1pbiIsImF1dGgiOiJST0xFX0FETUlOLFJPTEVfVVNFUiIsImV4cCI6MTU5NDE5NTE1NX0.jm9bQk97-7AYTsN8lgOixbUoG7-psPGoDMIa-L2ZIx8P3T9F_hXIYSczn-m6qEkxu9XJScAaTlGxB8IigZlPYw')
        );

        $result = curl_exec($curl);
        $dd = json_decode($result);
        curl_close($curl);
    }


}
