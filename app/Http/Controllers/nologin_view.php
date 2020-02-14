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
                ->select('quests_table.id','quests_table.party_name','name','quests_table.userid','count','max','deadline','comment','icon')
                ->join('users','quests_table.userid','=','users.userid')
                ->where('quests_table.id',$id)->first();
        
        $start = new Carbon($quest->deadline);
        $start_day = $start->format('Y-m-d');
        $start_time = $start->format('H-i-s');
        $end = $start->addMinutes(30);
        $end_day=$end->format('Y-m-d');
        $end_time=$end->format('H-i-s');

        $start_enc = $start_day.'T'.$start_time;
        $end_enc = $end_day.'T'.$end_time;

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
                                        'start_time'=>$start_enc,
                                        'end_time' => $end_enc]);
            }else{
                return view('show_quest',['member'=> $member_list,
                                        'quest'=>$quest,
                                        'start_time'=>$start_enc,
                                        'end_time' => $end_enc]);
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
