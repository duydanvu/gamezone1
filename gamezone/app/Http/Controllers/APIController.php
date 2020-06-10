<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    public  function testCurl(){
        $data = array(
            "from" => "2020-06-01",
            "isdn" => "" ,
            "status" => 0,
            "to" => "2020-06-10",
            "username" => "admin");
        $data_string = json_encode($data);

        $curl = curl_init('http://192.168.100.4:9000/api/cdr-historys');

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
        dd($dd);
        curl_close($curl);
    }

    public function userRegs(){
        $data = array(
            "channel" => "SMS",
            "chargePrice" => "2000",
            "commandCode" => "GH_PUBGM",
            "endDatetime" => "2020-06-10T06:30:17.222Z",
            "expireDatetime" => "2020-06-10T06:30:17.222Z",
            "groupCode" => "GAMEON",
            "isdn" => "84934444836",
            "messageSend" => "",
            "orgRequest" => "string",
            "packageCode" => "GH_PUBGM G",
            "regDatetime" => "2020-06-10T06:30:17.222Z",
            "serviceCode" => "",
            "staDatetime" => "2020-06-10T06:30:17.222Z",
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
        dd($dd);
        curl_close($curl);
    }


}
