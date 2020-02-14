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

        //\Request::setTrustedProxies(['192.168.0.0/16']);

        $db_write = DB::table('quests_table')->insert(
            ['party_name'=> $request->quest_name,
            'userid'=> Auth::user()->userid,
            'max'=>$request->max_member,
            'ip'=>\Request::ip(),
            'deadline'=>$deadline,
            'count'=> $cnt,
            'comment'=> $fix_comment]
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
                'content' => Auth::user()->name.'さんが新しいイベント「'.$request->quest_name.'」を作成したよ！！、みんなも参加しよう。'."\n".$url,
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

        if($quest != null){
            return view('edit_event',['auths'=>Auth::user(),'quest'=>$quest,'time'=>$time,'date'=>$date]);
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

        //\Request::setTrustedProxies(['192.168.0.0/16']);

        $db_write = DB::table('quests_table')->where('id',$request->id)->update(
            ['party_name'=> $request->quest_name,
            'max'=>$request->max_member,
            'ip'=>\Request::ip(),
            'deadline'=>$deadline,
            'comment'=> $fix_comment]
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
}
