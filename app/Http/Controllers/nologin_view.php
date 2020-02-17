<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use File;

class nologin_view extends Controller
{
    public function show_quest($id){
        //$quest = DB::table("quests_table")->where('id',$id)->first();
        $quest = DB::table("quests_table")
                ->select('quests_table.id','quests_table.party_name','name','quests_table.userid','count','max','deadline','comment','icon','start_time','end_time')
                ->join('users','quests_table.userid','=','users.userid')
                ->where('quests_table.id',$id)->first();

        
        if($quest->start_time != '1970-01-01 09:00:00' and $quest->end_time != '1970-01-01 09:00:00'){
            $start = new Carbon($quest->start_time);
            $end = new Carbon($quest->end_time);
        }else{        
            $start = new Carbon($quest->deadline);
            $end_obj = new Carbon($quest->deadline);
            $end = $end_obj->addMinutes(30);
        }

        $start_day = $start->format('Ymd');
        $start_time = $start->format('His');
        
        $end_day=$end->format('Ymd');
        $end_time=$end->format('His');

        $start_enc = $start_day.'T'.$start_time;
        $end_enc = $end_day.'T'.$end_time;

        $title_enc = rawurlencode($quest->party_name);
        $detail_enc = rawurldecode($quest->comment);

        $google_url = 'https://www.google.com/calendar/event?action=TEMPLATE&text='.$title_enc.'&details='.$detail_enc.'&dates='.$start_enc.'/'.$end_enc;
        

        if($quest != null){
            $member_list = DB::table("members")
            ->select('members.name','main_class','sub_class','comment','members.userid','icon')
            ->join('users','members.userid','=','users.userid')
            ->where('quest_id',$id)
            ->get();
            //$master_user = DB::table('users')->where('userid',$quest->userid)->first();

            $carbon_obj = new Carbon($quest->deadline);
            $now = Carbon::now();
            $isdeadline = $now->gt($carbon_obj);

            if(Auth::check()){
                $joined_member = DB::table("members")->where([['quest_id',$id],['userid',Auth::user()->userid]])->first();
                $joined=false;

                if($joined_member !=null){
                    $joined = true;
                    $main_class = $joined_member->main_class;
                    $sub_class = $joined_member->sub_class;
                    $comment = $joined_member->comment;
                }else{
                    $main_class = 'none';
                    $sub_class = 'none';
                    $comment = '';
                }
                return view('show_quest',['member'=> $member_list,
                                        'quest'=>$quest,
                                        'auths'=>Auth::user(),
                                        'isjoin'=> $joined,
                                        'main_class'=> $main_class,
                                        'sub_class'=>$sub_class,
                                        'comment'=>$comment,
                                        'isdeadline'=>$isdeadline,
                                        'google_url'=>$google_url]);
            }else{
                return view('show_quest',['member'=> $member_list,
                                        'quest'=>$quest,
                                        'google_url'=>$google_url]);
            }
        }else{
            abort('404');
        }
    }

    public function index(){
        $quest = DB::table("quests_table")
                ->select('quests_table.id','quests_table.party_name','name','count','max','deadline','comment')
                ->join('users','quests_table.userid','=','users.userid')
                ->where('quests_table.deadline','>',Carbon::now()->setTimezone('Asia/Tokyo'))
                ->get();

        if(Auth::check()){
            return view('index',['auths'=>Auth::user(),'quests'=>$quest,'now'=>Carbon::now()]);
        }else{
            return view('index',['quests'=>$quest]);
        }
    }

    public function ogp()
    {

        $file = storage_path('app/public/ogp.png');
        $mime_type = File::mimeType($file);
        $headers = [
            'Content-type' => $mime_type
        ];
    
        return response()->file($file, $headers);
    }

    public function discord_icon(){
        $file = storage_path('app/public/discord_icon.png');
        $mime_type = File::mimeType($file);
        $headers = [
            'Content-type' => $mime_type
        ];
    
        return response()->file($file, $headers);
    }
}
