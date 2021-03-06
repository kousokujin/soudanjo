<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Hash;
use File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $auths = Auth::user();
        $mastar_events = DB::table('quests_table')
                        ->where([['userid',Auth::user()->userid],['deadline','>',Carbon::now()->setTimezone('Asia/Tokyo')->addminutes(30)]])
                        ->get();
        $joined_events = DB::table('members')
                        ->select('quest_id','main_class','sub_class','party_name','deadline','users.name','max','count')
                        ->join('quests_table','quests_table.id','=','members.quest_id')
                        ->join('users','quests_table.userid','=','users.userid')
                        ->where([['members.userid',Auth::user()->userid],['deadline','>',Carbon::now()->setTimezone('Asia/Tokyo')->addminutes(30)]])->get();

        return view('home',['auths'=>Auth::user(),'joined_events'=>$joined_events,'mastar_event'=>$mastar_events]);
    }

    public function new_quest(){

        $auths = Auth::user();
        return view('new_quest',['auths'=>Auth::user()]);
    }

    private function fix_time($date,$time){
        if($time == null && $date == null){
            return null;
        }else{
            return $date.' '.$time;
        }
    }

    private function vaild_time($start_date,$start_time,$end_date,$end_time){
        $start_gen = $this->fix_time($start_date,$end_time);
        $end_gen = $this->fix_time($end_date,$end_time);
        
        $start = new Carbon($start_gen);
        $end = new Carbon($end_gen);

        if($start->lt($end) == false){
            $start = $this->fix_time($end_date,$end_time);
            $end = $this->fix_time($start_date,$start_time);
        }

        return ['start'=>$start,'end'=>$end];
    }

    public function newquest_register(Request $request){

        $request->validate([
            'max_member' => 'required|numeric|min:1'
        ]);

        if($request->comment == null){
            $fix_comment = "";
        }else{
            $fix_comment = $request->comment;
        }

        $deadline = $request->date .' '.$request->time;

        $cnt = 0;
        if($request->is_join == true){
            $cnt = 1;
        }

        $quest_time = $this->vaild_time($request->start_date,$request->start_time,$request->end_date,$request->end_time);
        if($request->start_date != null && $request->start_time != null && $request->end_date != null && $request->end_time != null){
            $start_time = $quest_time['start'];
            $end_time = $quest_time['end'];
            $isTimeSet = true;
        }else{
            $start_time = null;
            $end_time = null;
            $isTimeSet = false;
        }

        //\Request::setTrustedProxies(['192.168.0.0/16']);
            $db_write = DB::table('quests_table')->insert(
                ['party_name'=> $request->quest_name,
                'userid'=> Auth::user()->userid,
                'max'=>$request->max_member,
                'ip'=>\Request::ip(),
                'deadline'=>$deadline,
                'count'=> $cnt,
                'comment'=> $fix_comment,
                'start_time' => $start_time,
                'end_time'=> $end_time,
                'isTimeSet' => $isTimeSet]
            );

        $res = DB::table('quests_table')->select('id')->where([['userid',Auth::user()->userid],['party_name',$request->quest_name]])
        ->latest()->first();

        if($request->is_join == true){


            DB::table('members')->insert(
                [
                    'name'=>Auth::user()->name,
                    'userid'=>Auth::user()->userid,
                    'ip'=>\Request::ip(),
                    'quest_id'=>$res->id,
                    'comment'=>'',
                    'main_class'=>'none',
                    'sub_class'=>'none'
                ]
                );
        }

        $client = new Client();
        $webhookurl = config('discord_webhooks.discord_webhooks');
        $url = 'https://soudanjo.kousokujin.com/quests/'.$res->id;
        $options = [
            'json'=> [
                'content' => Auth::user()->name.'さんが新しいイベント「'.$request->quest_name.'」を作成したよ！！みんなも参加しよう。'."\n".$url,
                "username" => "固定相談所",
                "avatar_url" => "https://soudanjo.kousokujin.com/discord_icon.png",
            ],
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
                ]
            ];
        try{
            $request = $client->Request('POST', $webhookurl, $options);
        }catch(Exception $ex){
            \Log::error($ex->getmessage());
        }

        return redirect('/quests/'.$res->id)->with('alert','新しいイベントを作成しました。');
    }

    public function event_join(Request $request){

        $request->validate([
            'comment' => 'max:30'
        ]);

        if($request->comment == null){
            $fix_comment = "";
        }else{
            $fix_comment = $request->comment;
        }

        //\Request::setTrustedProxies(['192.168.0.0/16']);

        if($joined_member = DB::table("members")->where([['quest_id',$request->id],['userid',Auth::user()->userid]])->first() == null){
            
            DB::table('members')->insert(
                [
                    'quest_id' => $request->id,
                    'name' => Auth::user()->name,
                    'userid' => Auth::user()->userid,
                    'main_class' => $request->main_class,
                    'sub_class' => $request->sub_class,
                    'comment' => $fix_comment,
                    'ip' => \Request::ip()
                ]);
            DB::table('quests_table')->where('id',$request->id)->increment('count');

            return redirect('/quests/'.$request->id)->with('alert','イベントに参加しました。');
        }else{
            DB::table('members')->where([['quest_id',$request->id],['userid',Auth::user()->userid]])->update(
                [
                    'main_class' => $request->main_class,
                    'sub_class' => $request->sub_class,
                    'comment' => $fix_comment,
                ]);
                return redirect('/quests/'.$request->id)->with('alert','変更しました。');
        }
    }

    public function event_cancel($id){
        DB::table('members')->where([['quest_id',$id],['userid',Auth::user()->userid]])->delete();
        DB::table('quests_table')->where('id',$id)->decrement('count');
        return redirect('/quests/'.$id)->with('alert','キャンセルしました。');

    }

    public function edit_event($id){
        $quest = DB::table('quests_table')->where('id',$id)->first();
        if($quest->userid != Auth::user()->userid){
            return redirect('/quests/'.$id);
        }

        $deadline = new Carbon($quest->deadline);
        $date = $deadline->format('Y-m-d');
        $time = $deadline->format('H:i:s');

        $start_time =null;
        $start_date = null;

        if($quest->start_time != null){
            $start = new Carbon($quest->start_time);
            $start_date = $start->format('Y-m-d');
            $start_time = $start->format('H:i:s');
        }

        $end_time =null;
        $end_date = null;

        if($quest->end_time != null){
            $end = new Carbon($quest->end_time);
            $end_date = $end->format('Y-m-d');
            $end_time = $end->format('H:i:s');
        }

        if($quest != null){
            return view('edit_event',['auths'=>Auth::user(),'quest'=>$quest,'time'=>$time,'date'=>$date,'start_date'=>$start_date,'start_time'=>$start_time,'end_date'=>$end_date,'end_time'=>$end_time]);
        }else{
            abort('404');
        }
    }

    public function event_modify(Request $request){
        $request->validate([
            'max_member' => 'required|numeric|min:1'
        ]);

        if($request->comment == null){
            $fix_comment = "";
        }else{
            $fix_comment = $request->comment;
        }

        $deadline = $request->date .' '.$request->time;

        $quest_time = $this->vaild_time($request->start_date,$request->start_time,$request->end_date,$request->end_time);
        if($request->start_date != null && $request->start_time != null && $request->end_date != null && $request->end_time != null){
            $start_time = $quest_time['start'];
            $end_time = $quest_time['end'];
            $isTimeSet = true;
        }else{
            $start_time = null;
            $end_time = null;
            $isTimeSet = false;
        }

        //\Request::setTrustedProxies(['192.168.0.0/16']);

        $db_write = DB::table('quests_table')->where('id',$request->id)->update(
            ['party_name'=> $request->quest_name,
            'max'=>$request->max_member,
            'ip'=>\Request::ip(),
            'deadline'=>$deadline,
            'comment'=> $fix_comment,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'isTimeSet'=>$isTimeSet]
        );

        return redirect('/quests/'.$request->id)->with('alert','変更しました。');
    }

    public function event_delete($id){
        $quest = DB::table('quests_table')->where('id',$id)->first();
        if($quest->userid != Auth::user()->userid){
            return redirect('/quests/'.$id);
        }

        DB::table('quests_table')->where('id',$id)->delete();
        DB::table('members')->where('quest_id',$id)->delete();

        return redirect('/')->with('alert','イベントを削除しました。');
    }

    public function edit_profile($userid){
        if($userid != Auth::user()->userid){
            return redirect('/');
        }

        return view('edit_profile',['auths'=>Auth::user()]);
    }

    public function modify_profile(Request $request){
        $request->validate([
            'name' => ['required','string','max:255'],
            'icon' => ['file','image','mimes:jpeg,png,jpg,gif','max:4096']
        ]);
        
        if($request->file('icon')->isValid()){
            $image=$request->file('icon')->store('public/profile_images');

            DB::table('users')->where('userid',Auth::user()->userid)->update(
                [
                    'name'=>$request->name,
                    'icon'=>basename($image)
                ]);
        }else{

            DB::table('users')->where('userid',Auth::user()->userid)->update(
                [
                    'name'=>$request->name
                ]);
        }
        DB::table('members')->where('userid',Auth::user()->userid)->update(
            [
                'name'=>$request->name
            ]);
        return redirect('/')->with('alert','プロフィールを変更しました。');
    }

    public function edit_password($userid){
        if($userid != Auth::user()->userid){
            return redirect('/');
        }

        return view('edit_password',['auths'=>Auth::user()]);

    }

    public function admin_cancel($quest_id,$id){
        $quest_owner = DB::table('quests_table')->where('id',$quest_id)->first();
        if($quest_owner->userid != Auth::user()->userid){
            return redirect('/quests/'.$quest_id);
        }


        DB::table('members')->where([['quest_id',$quest_id],['id',$id]])->delete();
        DB::table('quests_table')->where('id',$quest_id)->decrement('count');
        return redirect('/quests/'.$quest_id)->with('alert','キャンセルしました。');
    }

    public function modify_password(Request $request){
        $request->validate([
            'old_pass' => [
                function($attribute, $value, $fail){
                    if(Hash::check($value,Auth::user()->password) == false){
                        return $fail('パスワードが違います。');
                    }
                }
            ],
            'password' => ['required','string', 'min:8', 'confirmed']
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return redirect('/edit_profile/'.$user->userid)->with('alert','パスワード変更しました。');

    }

    public function icon(){
        $icon_path = 'storage/profile_images/'.Auth::user()->icon;
        $icon_file = asset($icon_path);

        $mime_type = File::mimeType($icon_file);
        $headers = [
            'Content-type' => $mime_type
        ];

        return response()->file($file, $headers);
    }

    public function join_out_page($id){
        $quest = DB::table("quests_table")->where('id',$id)->first();
        $users = DB::table('users')->select('userid','name')->get();


        return view('join_event_page',['quest'=>$quest,'users'=>$users,'auths'=>Auth::user()]);
    }

    public function outuser_join(Request $request){
        $data = DB::table('quests_table')->where('id',$request->id)->first();

        if($data->count >= $data->max){
            return redirect('/quests/'.$request->id)->with('alert','定員に達しました。');
        }


        $request->validate([
            'display_name' => ['required','string'],
            'comment' => ['max:30']
        ]);

        if($request->comment == null){
            $fix_comment = "";
        }else{
            $fix_comment = $request->comment;
        }

        if(0 === strpos($request->display_name,'@')){
            $display_str = ltrim($request->display_name,'@');
            $exist_user = DB::table('users')->select('userid','name')->where('userid',$display_str)->first();
            
            $request->validate(
                ['display_name' =>[
                    function($attribute,$value,$fail){
                        #2回DBに問い合わせしてるのなんとかしたい
                        $display_str = ltrim($value,'@');
                        $exist_user = DB::table('users')->select('userid','name')->where('userid',$display_str)->first();
                        if($exist_user == null){
                            return $fail('ユーザーが存在しません。');
                        }
                    }
                ]
            ]);

            $isjoined = DB::table('members')->select('userid','quest_id')->where([['userid',$display_str],['quest_id',$request->id]])->first();

            if($isjoined != null){

                DB::table('members')->where([['userid',$display_str],['quest_id',$request->id]])->update(
                    [
                        'main_class' => $request->main_class,
                        'sub_class' => $request->sub_class,
                        'comment' => $fix_comment,
                    ]
                );
                return redirect('/quests/'.$request->id)->with('alert','変更しました。');
            }else{

                DB::table('members')->insert(
                    [
                        'quest_id' => $request->id,
                        'name' => $exist_user->name,
                        'main_class' => $request->main_class,
                        'sub_class' => $request->sub_class,
                        'comment' => $fix_comment,
                        'userid' => $exist_user->userid,
                        'ip' => \Request::ip()
                    ]);
            }

        }else{
        //\Request::setTrustedProxies(['192.168.0.0/16']);
            
            DB::table('members')->insert(
                [
                    'quest_id' => $request->id,
                    'name' => $request->display_name,
                    'main_class' => $request->main_class,
                    'sub_class' => $request->sub_class,
                    'comment' => $fix_comment,
                    'ip' => \Request::ip()
                ]);
        }

        $count = $data->count + 1;
        DB::table('quests_table')->where('id',$request->id)->increment('count');

        return redirect('/quests/'.$request->id)->with('alert','追加しました。');
    }
}
