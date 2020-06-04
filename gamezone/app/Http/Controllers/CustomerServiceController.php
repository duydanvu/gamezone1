<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CustomerServiceController extends Controller
{
    public function regTransactions(){
        $reg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> get();
        return view('customer_service.reg_transactions')->with('reg_tran',$reg_tran);
    }

    public function unregTransactions(){
        $unreg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
            -> where('request','=','UNSUB')
            -> whereNotNull('reg_datetime')
            -> get();
        return view('customer_service.unreg_transactions')->with('unreg_tran',$unreg_tran);
    }

    public function moMt(){
        $momt = DB::table('mtobj_201908')
            -> select('username','isdn','timerequest','command_code','timeaction','content')
            -> addSelect(DB::raw("'null' as result"))
            -> get();
        foreach ($momt as $value){
            if($value->timeaction == null){
                $value->result = "Không thành công";
            }else{
                $value->result = "Thành công";
            }
        }
        return view('customer_service.momt')->with('momt',$momt);
    }

    public function historyAccount(){
        $history_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
            -> whereNotNull('reg_datetime')
            -> where('request','=','SUB')
            ->orWhere('request','=','GH')
            -> get();

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

    public function historyAccountUse(){
        $history_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
            -> whereNotNull('reg_datetime')
            -> get();

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

    public function extenAcc(){
        $exten_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code','channel','charge_price')
            -> addSelect(DB::raw("'Gia Hạn' as type"))
            -> addSelect(DB::raw("'Thành Công' as tt"))
            -> where('request','=','GH')
            -> whereNotNull('reg_datetime')
            -> get();
        return view('customer_service.exten_acc')->with('exten_acc',$exten_acc);
    }

    public  function subUnSubAcc(){
        $subUnsub_acc_phone = DB::table('cdr_201908')
                -> select(DB::raw('DISTINCT isdn'))
                -> whereNotNull('isdn')
                -> get();
        $result = [];
        foreach ($subUnsub_acc_phone as $key => $value){
            $infor_phone = DB::table('cdr_201908')
                -> select('id','isdn','package_code','channel','sta_datetime','expire_datetime','end_datetime','request')
                -> addSelect(DB::raw("'Không có gói cước' as tt"))
                -> addSelect(DB::raw("'Không' as gh"))
                -> where('isdn','=',$value->isdn)
                -> orderBy('id','DESC')
                -> first();
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

    public function subUnSubViewUpdate($id){
        $data = DB::table('cdr_201908')
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

    public  function  SearchDateTimeRegTran(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $reg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Đăng ký gói dịch vụ' as type"))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> get();

        foreach($reg_tran as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->reg_datetime . '</td>';
            $result .= '<td>' . $value->type . '</td>';
            $result .= '<td>' . $value->package_code . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    public  function  SearchDateTimeUnRegTran(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $unreg_tran = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code')
            -> addSelect(DB::raw("'Hủy gói dịch vụ' as type"))
            -> where('request','=','UNSUB')
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> get();

        foreach($unreg_tran as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->isdn . '</td>';
            $result .= '<td>' . $value->reg_datetime . '</td>';
            $result .= '<td>' . $value->type . '</td>';
            $result .= '<td>' . $value->package_code . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }

    public  function SearchDateTimeMOMT(Request $request)
    {
        $startEnd = $request->startEnd;
        $date_range = explode(' - ', $startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $momt = DB::table('mtobj_201908')
            ->select('username', 'isdn', 'timerequest', 'command_code', 'timeaction', 'content')
            ->addSelect(DB::raw("'null' as result"))
            ->whereBetween('timerequest', [$start, $end])
            ->get();
        foreach ($momt as $value) {
            if ($value->timeaction == null) {
                $value->result = "Không thành công";
            } else {
                $value->result = "Thành công";
            }
        }

        foreach ($momt as $key => $value){
            $result .= '<tr>';
        $result .= '<td>' . $value->username . '</td>';
        $result .= '<td>' . substr($value->isdn, 0, 4) . '</td>';
        $result .= '<td>' . $value->timerequest . '</td>';
        $result .= '<td>' . $value->command_code . '</td>';
        $result .= '<td>' . $value->timeaction . '</td>';
        $result .= '<td>' . $value->content . '</td>';
        $result .= '<td>' . $value->result . '</td>';
        $result .= '<td><a href="#" class="btn dropdown-item">
                        <i class="fas fa-edit"> Gửi lại</i>
                        </a></td>';
        $result .= '</tr>';
        }

        return $result;
    }

    public  function SearchDateTimeHisAcc(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $history_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','request','package_code','message_send','channel','charge_price')
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> where(function ($query){
                    $query -> where('request','=','SUB')
                    -> orWhere('request','=','GH');
            })
            -> get();

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

    public  function SearchDateTimeHisAccUse(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $history_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','request','channel','package_code','charge_price','message_send')
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> get();

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

    public function SearchDateTimeExtenAcc(Request $request){
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;

        $exten_acc = DB::table('cdr_201908')
            -> select('isdn','reg_datetime','package_code','channel','charge_price')
            -> addSelect(DB::raw("'Gia Hạn' as type"))
            -> addSelect(DB::raw("'Thành Công' as tt"))
            -> where('request','=','GH')
            -> whereBetween('reg_datetime',[$start,$end])
            -> whereNotNull('reg_datetime')
            -> get();

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

//            $file->move('upload_file',$file->getClientOriginalName());

            $notification = array(
                'message' => 'Upload file thành công!',
                'alert-type' => 'success'
            );

            $csv = array();
            $file = fopen('../public/upload_file/member.csv', 'r');

            while (($result = fgetcsv($file)) !== false)
            {
                $csv[] = $result;
            }

            fclose($file);

//            dd($csv);
            $phone = [];
            foreach ($csv as $value){
                array_push($phone,$value[1]);
            }
            dd($phone);
//            return Redirect::back()->with($notification);
        }
    }

    public function DoUploadToUnSub(Request $request){
        //Kiểm tra file
//        dd($request);
        if ($request->hasFile('filesTest1')) {
            $file = $request->filesTest1;

            $file->move('upload_file',$file->getClientOriginalName());

            $csv = array();
            $file = fopen('../public/upload_file/'.$file->getClientOriginalName(), 'r');

            while (($result = fgetcsv($file)) !== false)
            {
                $csv[] = $result;
            }

            fclose($file);

//            dd($csv);
            $phone = [];
            foreach ($csv as $value){
                array_push($phone,$value[1]);
            }
            foreach ($phone as $value){
                $this->unsubWithPhone($value);

            }

            $notification = array(
                'message' => 'Upload file thành công!',
                'alert-type' => 'success'
            );

            return Redirect::back()->with($notification);
        }
    }

    public  function  unsubWithPhone($phonenumber){
        $subUnsub_acc_phone = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT isdn'))
            -> whereNotNull('isdn')
            -> get();
        $result = [];
        foreach ($subUnsub_acc_phone as $key => $value){
            $infor_phone = DB::table('cdr_201908')
                -> select('id','isdn',
                    'request' , 'service_code', 'group_code' , 'package_code', 'command_code',
                    'reg_datetime', 'sta_datetime', 'end_datetime', 'expire_datetime', 'status',
                    'channel', 'charge_price', 'message_send', 'org_request', 'date_receive_request')
                -> where('isdn','=',$value->isdn)
                -> orderBy('id','DESC')
                -> first();
            $result[$key] = $infor_phone;
        }
        foreach ($result as $data)
            if($data->isdn == $phonenumber && ($data->request == "SUB" || $data->request == "GH" )){
                $createUnSub = DB::table('cdr_201908')->insert([
                    'isdn' => $data->isdn,
                    'request' => "UNSUB",
                    'service_code' => $data->service_code,
                    'group_code' => $data->group_code,
                    'package_code' => $data->package_code,
                    'command_code' => $data->command_code,
                    'reg_datetime' => $data->reg_datetime,
                    'sta_datetime' => $data->sta_datetime,
                    'end_datetime' => $data->end_datetime,
                    'expire_datetime' => $data->expire_datetime,
                    'status' => $data->status,
                    'channel' => $data->channel,
                    'charge_price' => $data->charge_price,
                    'message_send' => $data->message_send,
                    'org_request' => $data->org_request,
                    'date_receive_request' => $data->date_receive_request
                ]);
        }
    }
}
