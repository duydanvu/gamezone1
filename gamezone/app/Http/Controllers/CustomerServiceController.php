<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CustomerServiceController extends Controller
{
    /*
     * trả về một loạt các tên bảng cần thiết để truy vấn database
     */
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

    /*
     * Trả về View Giao dịch đăng ký
     */
    public function regTransactions(){
        $user_id_sign_in = Auth::id();
        $name_use = DB::table('manager_user')->find($user_id_sign_in);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        try {
            $reg_tran = $this->getQueryRegTransactions("", "", "", $name_use->first_name);
        }catch (QueryException $ex){
            $reg_tran = [];
        }
        return view('customer_service.reg_transactions')->with('reg_tran',$reg_tran);
    }
    /*
     * function export file csv
     */
    public function exportRegFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $reg_tran = $this->getQueryRegTransactions($start, $end, "", "");
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        foreach ($reg_tran as $value){
            $value->type = "Register";
        }
        $result = $reg_tran-> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/registeredTransactions.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\registeredTransactions.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }
    /*
     * Gọi Query để tìm kiếm các giao dịch đăng ký
     */
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
    /*
     * thực hiện tra cứu database các giao dịch đăng ký
     */
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
    /*
     * Trả về view Giao dịch hủy
     */
    public function unregTransactions(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $datestart = $arrdate[0].'-'.$arrdate[1].'-01';
        try {
            $unreg_tran = $this->getQueryUnRegTransactions('', '', "", '');
        }catch (QueryException $ex){
            $unreg_tran = [];
        }
        return view('customer_service.unreg_transactions')->with('unreg_tran',$unreg_tran);
    }

    /*
     * function export file csv
     */
    public function exportUnRegFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $unreg_tran = $this->getQueryUnRegTransactions($start, $end, "", '');
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        foreach ($unreg_tran as $value){
            $value->type = "Un-Register";
        }
        $result = $unreg_tran-> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/unRegisteredTransactions.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\unRegisteredTransactions.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }

    /*
     * Gọi Query để tìm kiếm các giao dịch hủy
     */
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
    /*
     * Thực hiện tra cứu database các giao dịch hủy
     */
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
    /*
     * Trả về view Giao dịch MOMT
     */
    public function moMt(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $datestart = $arrdate[0].'-'.$arrdate[1].'-01';
        try {
            $momt = $this->getQueryMOMT('', '', '', '');
            foreach ($momt as $value) {
                if ($value->timeaction == null) {
                    $value->result = "Không thành công";
                } else {
                    $value->result = "Thành công";
                }
            }
        } catch (QueryException $ex){
            $momt = [];
        }
        return view('customer_service.momt')->with('momt',$momt);
    }

    /*
    * function export file csv
    */
    public function exportMOMTFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $momt = $this->getQueryMOMT($start, $end, "", '');
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        $result = $momt-> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/momt.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\momt.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }
    /*
     * Gọi các query tìm kiếm giao dịch MOMT
     */
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
    /*
     * thực hiện tra cứu database các giao dịch MOMT
     */
    public function getMOMT($table,$start,$end,$momt_0,$index){
        if($index = 1){
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> addSelect(DB::raw("'null' as result"))
                -> whereBetween('timerequest', [$start, $end])
                -> get();
            return $momt;
        }elseif ($index = 2){
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> whereBetween('timerequest', [$start, $end])
                -> addSelect(DB::raw("'null' as result"));
            return $momt;
        }else{
            $momt = DB::table($table)
                -> select('username','isdn','timerequest','command_code','timeaction','content')
                -> addSelect(DB::raw("'null' as result"))
                -> whereBetween('timerequest', [$start, $end])
                -> union($momt_0)
                -> get();
            return $momt;
        }

    }
    /*
     * Trả view tra cứu lịch sử gói cước
     */
    public function historyAccount(){
        try {
            $history_acc = $this->getQueryHistoryAcc('', '', '', '');

            foreach ($history_acc as $value) {
                if ($value->message_send == null && $value->request != 'GH') {
                    $value->message_send = "Không thành công";
                } else {
                    $value->message_send = "Thành công";
                }
            }

            foreach ($history_acc as $value) {
                if ($value->request == 'SUB') {
                    $value->request = "Đăng ký gói cước";
                } else {
                    $value->request = "Gia hạn gói cước";
                }

            }
        } catch (QueryException $ex){
            $history_acc = [];
        }

        return view('customer_service.history_trucuoc')->with('history_acc',$history_acc);
    }
    /*
    * function export file csv
    */
    public function exportHistoryAccFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $history_acc = $this->getQueryHistoryAcc($start, $end, "", '');
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        $result = $history_acc-> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/historyAccount.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\historyAccount.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }
    /*
     * Gọi các query để tra cứu lịch sử gói cước
     */
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
    /*
     * thực hiện các query database để tra cứu lịch sử gói cước
     */
    public function getHistoryAccout($table,$start,$end,$his_acc_0,$index){
        if($index = 1){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> where(function ($query){
                    $query -> where('request','=','SUB')
                        -> orWhere('request','=','GH');
                })
                -> get();
            return $history_acc;
        }elseif ( $index = 2){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> where(function ($query){
                    $query -> where('request','=','SUB')
                        -> orWhere('request','=','GH');
                });
            return $history_acc;
        }else{
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> where(function ($query){
                    $query -> where('request','=','SUB')
                        -> orWhere('request','=','GH');
                })
                -> union($his_acc_0)
                -> get();
            return $history_acc;
        }
    }
    /*
     * Thực hiện các Query database để tra cứu lịch sử sử dụng gói cước
     */
    public function getHistoryAccountUse($table,$start,$end,$his_acc_use_0,$index){
        if($index = 1){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> get();
            return $history_acc;
        }elseif ($index = 2){
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereBetween('reg_datetime', [$start, $end])
                -> whereNotNull('reg_datetime');
            return $history_acc;
        }else{
            $history_acc = DB::table($table)
                -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
                -> whereNotNull('reg_datetime')
                -> whereBetween('reg_datetime', [$start, $end])
                -> union($his_acc_use_0)
                -> get();
            return $history_acc;
        }
    }
    /*
     * Gọi các query để tra cứu lịch sử sử dụng gói cước
     */
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

    /*
     * Trả về view chứa các thông tin về lịch sử sử dụng
     */
    public function historyAccountUse(){

//        $history_acc = $this->getDataHistoryAccUse();
        try {
            $history_acc = $this->getQueryHistoryAccUse('', '', '', '');

            foreach ($history_acc as $value) {
                if ($value->message_send == null && $value->request != 'GH') {
                    $value->message_send = "Không thành công";
                } else {
                    $value->message_send = "Thành công";
                }
            }

            foreach ($history_acc as $value) {
                if ($value->request == 'SUB') {
                    $value->request = "Đăng ký gói cước";
                } else if ($value->request == 'GH') {
                    $value->request = "Gia hạn gói cước";
                } else {
                    $value->request = "Hủy gói cước";
                }

            }
        } catch (QueryException $ex){
            $history_acc = [];
        }
        return view('customer_service.history_acc_use')->with('history_acc_use',$history_acc);
    }
    /*
    * function export file csv
    */
    public function exportHistoryAccUseFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $history_acc = $this->getQueryHistoryAccUse($start, $end, "", '');
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        $result = $history_acc-> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/historyAccountUse.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\historyAccountUse.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
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


    /*
    *   Gọi các query để trả về lịch sử gia hạn
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
    /*
     * Trả về view lịch sử gia hạn
     */
    public function extenAcc(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $datenow = date('Y-m-d');
        $arrdate = explode("-",$datenow);
        $dateend = $arrdate[0].'-'.$arrdate[1].'-01';
//        $exten_acc = $this ->getHistoryRenew($dateend,$datenow,'','');
        try {
            $exten_acc = $this->getHistoryRenew('', '', '', '');
        }catch (QueryException $ex){
            $exten_acc = [];
        }
        return view('customer_service.exten_acc')->with('exten_acc',$exten_acc);
    }
    /*
    * function export file csv
    */
    public function exportEntendAccFile($datetime){
        $date_range = explode( '-',$datetime);
        $start_0 = str_replace(".","/",$date_range[0]);
        $end_0 = str_replace(".","/",$date_range[1]);
        $start = date("Y-m-d", strtotime($start_0));
        $end = date("Y-m-d", strtotime($end_0));
        try{
            $exten_acc = $this->getHistoryRenew($start, $end, "", '');
        }catch (QueryException $ex){
            $notification = array(
                'message' => 'Export fail! Please try again with time',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        foreach ($exten_acc as $value){
            $value->type = "Renew";
            $value->tt = "Success";
        }
        $result = $exten_acc -> transform(function ($item){
            return (array)$item;
        })->toArray();
        $fileCsv = fopen('../storage/app/public/renew.csv', 'w+');
        foreach ($result as $k=>$line) {
            if ($k == 0) {
                fputcsv($fileCsv, array_keys($line));
            }
            fputcsv($fileCsv, array_values($line));
        }
        fclose($fileCsv);
        $file = storage_path('app\public\renew.csv');
        $filename=basename($file);
        $headers = ['Content-Type'=>'application/json; charset=UTF-8','charset'=>'utf-8'];
        return response()->download($file,$filename, $headers);
    }
    /*
     * Trả về danh sách sdt riêng biệt
     */
    public  function getDistinctPhone($table,$start,$end,$distinct_phone_0,$index){
        if($index == 1){
            $subUnsub_acc_phone = DB::table($table)
                -> select(DB::raw('DISTINCT isdn'))
                -> whereNotNull('isdn')
                -> whereBetween('reg_datetime',[$start,$end])
                -> get();
            return $subUnsub_acc_phone;
        }else if ($index == 2){
            $subUnsub_acc_phone = DB::table($table)
                -> select(DB::raw('DISTINCT isdn'))
                -> whereBetween('reg_datetime',[$start,$end])
                -> whereNotNull('isdn');
            return $subUnsub_acc_phone;
        }else{
            $subUnsub_acc_phone = DB::table($table)
                -> select(DB::raw('DISTINCT isdn'))
                -> whereNotNull('isdn')
                -> whereBetween('reg_datetime',[$start,$end])
                -> union($distinct_phone_0)
                -> get();
            return $subUnsub_acc_phone;
        }
    }
    /*
     * Lấy ra danh sách thông tin tài khoản
     */
    public  function getListPhone($table,$start,$end,$listPhone_0,$index){
        if($index == 1){
            $infor_phone = DB::table($table)
                -> select('id','isdn','package_code','channel','sta_datetime','expire_datetime','end_datetime','request')
                -> addSelect(DB::raw("'Không có gói cước' as tt"))
                -> addSelect(DB::raw("'Không' as gh"))
                -> whereBetween('reg_datetime',[$start,$end])
                -> orderBy('id','DESC')
                -> get();
            return $infor_phone;
        }elseif ($index == 2){
            $infor_phone = DB::table($table)
                -> select('id','isdn','package_code','channel','sta_datetime','expire_datetime','end_datetime','request')
                -> addSelect(DB::raw("'Không có gói cước' as tt"))
                -> addSelect(DB::raw("'Không' as gh"))
                -> whereBetween('reg_datetime',[$start,$end])
                -> orderBy('id','DESC');
            return $infor_phone;
        }else{
            $infor_phone = DB::table($table)
                -> select('id','isdn','package_code','channel','sta_datetime','expire_datetime','end_datetime','request')
                -> addSelect(DB::raw("'Không có gói cước' as tt"))
                -> addSelect(DB::raw("'Không' as gh"))
                -> whereBetween('reg_datetime',[$start,$end])
                -> union ($listPhone_0)
                -> orderBy('id','DESC')
                -> get();
            return $infor_phone;
        }
    }

    /*
     * Thực hiện gọi các câu lệnh query để tìm kiếm thông tin thuê bao
     */
    public function getQueryInforAcc($start ,$end ,$phone ,$username,$functionQuery ){
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
            $exten_acc = $this->$functionQuery($table, $start, $end, null, 1);
        }
        else {
            $exten_acc1 = $this->$functionQuery($table[0], $start, $end, null,2);
            for ($i = 1; $i < sizeof($table); $i++) {
                $exten_acc = $this->$functionQuery($table[$i], $start, $end, $exten_acc1,3);
            }
        }
        return $exten_acc;
    }
    /*
     * Lấy ra thông tin từ của tài khoản được tìm thấy đầu tiên trong danh sách
     */
    public  function getListInforPhone($listPhone,$isdn){
        $infor_phone = $listPhone -> where('isdn','=',$isdn)->first();
        return $infor_phone;
    }
    /*
     * Trả về view đăng ký / hủy dịch vụ
     */
    public  function subUnSubAcc(){
        try {
            $subUnsub_acc_phone = $this->getQueryInforAcc('', '', '', '', 'getDistinctPhone');
            $getlistPhone = $this->getQueryInforAcc('', '', '', '', 'getListPhone');
            $result = [];
            foreach ($subUnsub_acc_phone as $key => $value) {
                foreach ($getlistPhone as $value2) {
                    $infor_phone = $this->getListInforPhone($getlistPhone, $value->isdn);
                    $result[$key] = $infor_phone;
                }
            }
            foreach ($result as $value) {
                if ($value->request == 'GH') {
                    $value->tt = "Có Gói Cước";
                    $value->gh = "Có";
                } else if ($value->request == 'SUB') {
                    $value->tt = "Có Gói Cước";
                }

            }
            foreach ($result as $value) {
                if ($value->request == 'GH' || $value->request == 'SUB') {
                    $value->request = "Hủy Đăng Ký";
                } else {
                    $value->request = "Đăng Ký";
                }
            }
        }catch (QueryException $ex){
            $result = [];
        }
//        dd($result);
        return view('customer_service.sub_unsub_acc')->with('sub_unsub',$result);
    }
    /*
     * View thông tin đăng ký / hủy dv
     */
    public function subUnSubViewUpdate($id,$epiTime){
        $arr = explode("-",$epiTime);
        $name_table = 'cdr_'.$arr[0].$arr[1];
        $data = DB::table($name_table)
            ->find($id);
        return view('customer_service.infor_acc')->with('data',$data);
    }
    /*
     * update thông tin insert vào bảng cdr trong đăng ký hủy dv
     */
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
        try {
            $name_use = DB::table('manager_user')->find($user_id_sign_in);
            $reg_tran = $this->getQueryRegTransactions($start, $end, '', '');

            foreach ($reg_tran as $key => $value) {
                $result .= '<tr>';
                $result .= '<td>' . ($key + 1) . '</td>';
                $result .= '<td>' . $value->isdn . '</td>';
//            $result .= '<td>' . substr($value->regDatetime,-9,8).', '.substr($value->regDatetime,0,10) . '</td>';
                $result .= '<td>' . $value->reg_datetime . '</td>';
                $result .= '<td>' . 'Đăng ký gói dịch vụ' . '</td>';
                $result .= '<td>' . $value->package_code . '</td>';
                $result .= '</tr>';
            }
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
        }
        return $result;
    }

    // Ajax search data User - UnReg
    public  function  SearchDateTimeUnRegTran(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        try {
            $unreg_tran = $this->getQueryUnRegTransactions($start, $end, '', '');

            foreach ($unreg_tran as $key => $value) {
                $result .= '<tr>';
                $result .= '<td>' . ($key + 1) . '</td>';
                $result .= '<td>' . $value->isdn . '</td>';
//            $result .= '<td>' . substr($value->regDatetime,-9,8).', '.substr($value->regDatetime,0,10) . '</td>';
                $result .= '<td>' . $value->reg_datetime . '</td>';
                $result .= '<td> Hủy Dịch Vụ Gói</td>';
                $result .= '<td>' . $value->package_code . '</td>';
                $result .= '</tr>';
            }
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
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

        try{
        $momt = $this->getQueryMOMT($start,$end,'','');

        foreach ($momt as $value){
            if($value->timeaction == null){
                $value->result = "Không thành công";
            }else{
                $value->result = "Thành công";
            }
        }

        foreach($momt as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . $value->username . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->timerequest . '</td>';
            $result .= '<td>' . $value->command_code . '</td>';
            $result .= '<td>' . $value->timeaction . '</td>';
            $result .= '<td style="-webkit-line-clamp: 3;overflow : hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-box-orient: vertical">'
                . $value->content . '</td>';
            $result .= '<td>' . $value->result . '</td>';
            $result .= '<td><a href="#" class="btn dropdown-item">
                        <i class="fas fa-edit"> Gửi lại</i>
                        </a></td>';
            $result .= '</tr>';
        }
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
        }
        return $result;
    }


    public  function SearchDateTimeHisAcc(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        try{
        $history_acc = $this->getQueryHistoryAcc($start,$end,'','');

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
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
        }
        return $result;
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

        try{
        $history_acc = $this->getQueryHistoryAccUse($start,$end,'','');

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
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
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
        try{
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
        }catch (QueryException $ex){
            $result .= '<td colspan="8" style="text-align: center">
                        <h3>Empty Data</h3>
                        </td>';
        }
        return $result;
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
                        $this->subDayWithPhone($phone[$i],$channel[$i],$chargePrice[$i],$commandCode[$i],$endDatetime[$i],$expireDatetime[$i],$groupCode[$i],$packageCode[$i],$regDatetime[$i],$staDatetime[$i]);
                    }
                } else {
                    for ($i = 0; $i < sizeof($phone);$i ++) {
                        $this->subWeekWithPhone($phone[$i],$channel[$i],$chargePrice[$i],$commandCode[$i],$endDatetime[$i],$expireDatetime[$i],$groupCode[$i],$packageCode[$i],$regDatetime[$i],$staDatetime[$i]);
                    }
                }
            }
            return Redirect::back()->with($notification);
        }
    }


    public function  subDayWithPhone($phone ,$channel,$chargePrice,$commandCode,$endDatetime,$expireDatetime,$groupCode,$packageCode,$regDatetime,$staDatetime){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date = date('Y-m-d H:i:s');
        $time = explode("-",$date);
        $table = 'cdr_'.$time[0].$time[1];

        $table_user = 'user_g_'.$phone[strlen($phone)-1];

        $createSubDay_user = DB::table($table_user)->insert([
            'isdn' => $phone,
            'service_code' => null,
            'group_code' => $groupCode,
            'package_code' => $packageCode,
            'command_code' => $commandCode,
            'reg_datetime' => $regDatetime,
            'sta_datetime' => $regDatetime,
            'end_datetime' => $endDatetime,
            'expire_datetime' => $expireDatetime,
            'status' => 1,
            'channel' => $channel,
            'charge_price' => $chargePrice,
            'message_send' => "(DK) Chúc mừng Quý khách đã đăng ký thành công gói ngày(G) – Chơi Game PubG Mobile Miễn phí cước 3G/4G của dịch vụ gameOn. Gói cước tự động gia hạn. Để hủy dịch vụ, soạn HUY G gửi 9129. Chi tiết truy cập http://game.freedata.vn/pubgm hoặc gọi 9090. Giá cước 2000đ/ngày. Trân trọng cảm ơn!",
            'org_request' => "Dk g",
        ]);


        $createSubDay = DB::table($table)->insert([
            'isdn' => $phone,
            'request' => "SUB",
            'service_code' => null,
            'group_code' => $groupCode,
            'package_code' => $packageCode,
            'command_code' => $commandCode,
            'reg_datetime' => $regDatetime,
            'sta_datetime' => $staDatetime,
            'end_datetime' => $endDatetime,
            'expire_datetime' => $expireDatetime,
            'status' => 1,
            'channel' => $channel,
            'charge_price' => $chargePrice,
            'message_send' => "(DK) Chúc mừng Quý khách đã đăng ký thành công gói ngày(G) – Chơi Game PubG Mobile Miễn phí cước 3G/4G của dịch vụ gameOn. Gói cước tự động gia hạn. Để hủy dịch vụ, soạn HUY G gửi 9129. Chi tiết truy cập http://game.freedata.vn/pubgm hoặc gọi 9090. Giá cước 2000đ/ngày. Trân trọng cảm ơn!",
            'org_request' => "Dk g",
            'date_receive_request' => $date
        ]);

        return $createSubDay;
    }

    public function subWeekWithPhone($phone,$channel,$chargePrice,$commandCode,$endDatetime,$expireDatetime,$groupCode,$packageCode,$regDatetime,$staDatetime){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date = date('Y-m-d H:i:s');

        $time = explode("-",$date);
        $table = 'cdr_'.$time[0].$time[1];

        $createSubWeek = DB::table($table)->insert([
            'isdn' => $phone,
            'request' => "SUB",
            'service_code' => null,
            'group_code' => $groupCode,
            'package_code' => $packageCode,
            'command_code' => $commandCode,
            'reg_datetime' => $regDatetime,
            'sta_datetime' => $staDatetime,
            'end_datetime' => $endDatetime,
            'expire_datetime' => $expireDatetime,
            'status' => 1,
            'channel' => $channel,
            'charge_price' => $chargePrice,
            'message_send' => "(DK) Chúc mừng Quý khách đã đăng ký thành công gói ngày(G) – Chơi Game PubG Mobile Miễn phí cước 3G/4G của dịch vụ gameOn. Gói cước tự động gia hạn. Để hủy dịch vụ, soạn HUY G gửi 9129. Chi tiết truy cập http://game.freedata.vn/pubgm hoặc gọi 9090. Giá cước 2000đ/ngày. Trân trọng cảm ơn!",
            'org_request' => "Dk g",
            'date_receive_request' => $date
        ]);
        return $createSubWeek;
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
            $packageCode = [];

            $patern = '/[0-9]{10}/';
            foreach ($csv as $value){
                if (preg_match($patern,$value[1])) {
                    array_push($phone, $value[1]);
                    array_push($packageCode,$value[2]);
                }
            }
            for ($i = 0; $i < sizeof($phone);$i ++){
                $this->userRegs($phone[$i],$packageCode[$i]);

            }

            $notification = array(
                'message' => 'Upload file thành công!',
                'alert-type' => 'success'
            );

            return Redirect::back()->with($notification);
        }
    }

//    public function userUnRegs($phone,$packageCode){
//        $data = array(
//            "ISDN" => $phone,
//            "ServiceCode" => '9129',
//            "CommandCode" => 'HUY_G',
//            "PackageCode" => $packageCode,
//            "SourceCode" => 'CP',
//            "User" => 'GAMEON',
//            "Password" => 'Gameon@132',
//        );
//        $data_string = json_encode($data);
//
//        $curl = curl_init('http://10.54.3.37:8888/cms-unregister');
//
//        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json',
//                'Accept: application/json',
//                'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhZG1pbiIsImF1dGgiOiJST0xFX0FETUlOLFJPTEVfVVNFUiIsImV4cCI6MTU5NDE5NTE1NX0.jm9bQk97-7AYTsN8lgOixbUoG7-psPGoDMIa-L2ZIx8P3T9F_hXIYSczn-m6qEkxu9XJScAaTlGxB8IigZlPYw')
//        );
//
//        $result = curl_exec($curl);
//        $dd = json_decode($result);
//        curl_close($curl);
//    }


}
