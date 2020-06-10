<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function listReportDayAction(){
        $total = DB::table('cdr_201908')
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
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        $acc_sub = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub'))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('date')
            -> get();
        foreach ($total as $value){
            foreach ($acc_sub as $value1){
                if($value->date === $value1->date){
                    $value->acc_sub = $value1->count_sub;
                }
            }
        }
        $acc_unsub_people = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_pp'))
            -> where('request','=','UNSUB')
            -> where(function($query){
                $query->where('channel','=','SMS')
                    ->orwhere('channel','=','WAP');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('date')
            -> get();
        foreach ($total as $value){
            foreach ($acc_unsub_people as $value1){
                if($value->date === $value1->date){
                    $value->acc_unsub_pp = $value1->count_sub_pp;
                }
            }
        }
        $acc_unsub_system = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_stm'))
            -> where('request','=','UNSUB')
            -> where(function($query){
                $query->where('channel','<>','SMS')
                    ->orwhere('channel','<>','WAP');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('date')
            -> get();
        foreach ($total as $value){
            foreach ($acc_unsub_system as $value1){
                if($value->date === $value1->date){
                    $value->acc_unsub_stm = $value1->count_sub_stm;
                }
            }
        }
        $acc_psc = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_psc'))
            -> where('request','=','SUB')
            -> where('charge_price','>',0)
            -> whereNotNull('reg_datetime')
            -> groupBy('date')
            -> get();
        foreach ($total as $value){
            foreach ($acc_psc as $value1){
                if($value->date === $value1->date){
                    $value->acc_psc = $value1->count_psc;
                }
            }
        }
        $acc_active = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_active'))
            -> where(function($query){
                $query -> where('request','=','SUB')
                    -> orwhere('request','=','GH');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('date')
            -> get();
        foreach ($total as $value){
            foreach ($acc_active as $value1){
                if($value->date === $value1->date){
                    $value->acc_active = $value1->count_active;
                }
            }
        }
        $acc_gh = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_gh'))
            -> where('request','=','GH')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_gh as $value1){
                if($value->date === $value1->date){
                    $value->acc_gh = $value1->count_gh;
                }
            }
        }
        $acc_dk_sms = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_dk_sms'))
            -> where('channel','=','SMS')
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_dk_sms as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_sms = $value1->count_dk_sms;
                }
            }
        }
        $acc_dk_wap = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as wap'))
            -> where('channel','=','WAP')
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_dk_wap as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_wap = $value1->wap;
                }
            }
        }
        $acc_dk_vasgate = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(DISTINCT isdn) as count_sub_vasgate'))
//            -> where(function($query){
//                $query->where('channel','<>','SMS')
//                    ->orwhere('channel','<>','WAP');
//            })
            -> where('channel','<>','SMS')
            -> where('channel','<>','WAP')
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_dk_vasgate as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_vasgate = $value1->count_sub_vasgate;
                }
            }
        }
        $sum_acc_phone = DB::table('cdr_201908')
            -> select(DB::raw('COUNT(DISTINCT isdn) as sum'))
            -> whereNotNull('isdn')
            -> get();
        return view('report.report_day')->with(['total'=>$total,'sum' =>$sum_acc_phone]);
    }

    public function SearchDateTime(Request $request)
    {
        $startEnd = $request->startEnd;
        $date_range = explode( ' - ',$startEnd);
        $start = date("Y-m-d", strtotime($date_range[0]));
        $end = date("Y-m-d", strtotime($date_range[1]));
        $result = null;
        $total = DB::table('cdr_201908')
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
            -> whereNotNull('reg_datetime')
            -> whereBetween('reg_datetime',[$start,$end])
            -> groupBy('reg_datetime')
            -> get();
        $acc_sub = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_sub'))
            -> where('request','=','SUB')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_sub as $value1){
                if($value->date === $value1->date){
                    $value->acc_sub = $value1->count_sub;
                }
            }
        }
        $acc_unsub_people = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_sub_pp'))
            -> where('request','=','UNSUB')
            -> where(function($query){
                $query->where('channel','=','SMS')
                    ->orwhere('channel','=','WAP');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_unsub_people as $value1){
                if($value->date === $value1->date){
                    $value->acc_unsub_pp = $value1->count_sub_pp;
                }
            }
        }
        $acc_unsub_system = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_sub_stm'))
            -> where('request','=','UNSUB')
            -> where(function($query){
                $query->where('channel','<>','SMS')
                    ->orwhere('channel','<>','WAP');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_unsub_system as $value1){
                if($value->date === $value1->date){
                    $value->acc_unsub_stm = $value1->count_sub_stm;
                }
            }
        }
        $acc_psc = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_psc'))
            -> where('request','=','SUB')
            -> where('charge_price','>',0)
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_psc as $value1){
                if($value->date === $value1->date){
                    $value->acc_psc = $value1->count_psc;
                }
            }
        }
        $acc_active = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_active'))
            -> where(function($query){
                $query -> where('request','=','SUB')
                    -> orwhere('request','=','GH');
            })
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_active as $value1){
                if($value->date === $value1->date){
                    $value->acc_active = $value1->count_active;
                }
            }
        }
        $acc_gh = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_gh'))
            -> where('request','=','GH')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_gh as $value1){
                if($value->date === $value1->date){
                    $value->acc_gh = $value1->count_gh;
                }
            }
        }
        $acc_dk_sms = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as count_dk_sms'))
            -> where('channel','=','SMS')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_dk_sms as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_sms = $value1->count_dk_sms;
                }
            }
        }
        $acc_dk_wap = DB::table('cdr_201908')
            -> select(DB::raw('DISTINCT DATE(reg_datetime) as date,COUNT(request) as wap'))
            -> where('channel','=','WAP')
            -> whereNotNull('reg_datetime')
            -> groupBy('reg_datetime')
            -> get();
        foreach ($total as $value){
            foreach ($acc_dk_wap as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_wap = $value1->wap;
                }
            }
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
        foreach ($total as $value){
            foreach ($acc_dk_vasgate as $value1){
                if($value->date === $value1->date){
                    $value->acc_dk_vasgate = $value1->count_sub_vasgate;
                }
            }
        }

        foreach($total as $key => $value) {
            $result .= '<tr>';
            $result .= '<td>' . ($key + 1) . '</td>';
            $result .= '<td>' . $value->date . '</td>';
            $result .= '<td>' . $value->acc_sub . '</td>';
            $result .= '<td>' . $value->acc_unsub_pp . '</td>';
            $result .= '<td>' . $value->acc_unsub_stm . '</td>';
            $result .= '<td>' . $value->acc_psc . '</td>';
            $result .= '<td>' . $value->acc_active . '</td>';
            $result .= '<td>' . round(($value->acc_gh / $value->acc_active) * 100, 3) . ' %</td>';
            $result .= '<td>' . $value->acc_dk_sms . '</td>';
            $result .= '<td>' . $value->acc_dk_vasgate . '</td>';
            $result .= '<td>' . $value->acc_dk_wap . '</td>';
            $result .= '<td>' . ($value->acc_dk_sms + $value->acc_dk_vasgate + $value->acc_dk_wap) . '</td>';
            $result .= '</tr>';
        }
        return $result;
    }


}
