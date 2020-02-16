<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class AdminController extends Controller
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

    public function checkAdmin($level){
        if(Auth::user()->priority < $level){
            return true;
        }
        return false;
    }

    public function show_users(){
        if($this->checkAdmin(5)){
             return redirect('/');
        }
        $alluser = DB::table('users')->get();
        return view('user_list',['users'=>$alluser,'auths'=>Auth::user()]);
    }

    public function show_events(){
        if($this->checkAdmin(5)){
            return redirect('/');
        }
        $quests = DB::table('quests_table')->get();
        return view('quest_list',['quests'=>$quests,'auths'=>Auth::user()]);
    }

    public function show_members(){
        if($this->checkAdmin(5)){
            return redirect('/');
        }
        $members = DB::table('members')->get();
        return view('member_list',['members'=>$members,'auths'=>Auth::user()]);
    }

    public function show_password($userid){
        if($this->checkAdmin(5)){
            return redirect('/');
        }
        return view('admin_edit_pass',['id'=>$userid,'auths'=>Auth::user()]);
    }

    public function modify_password(Request $request){
        if($this->checkAdmin(5)){
            return redirect('/');
        }

        $request->validate([
            'password' => ['required','string', 'min:8', 'confirmed']
        ]);
        
        $hash = password_hash($request->password, PASSWORD_DEFAULT);
        DB::table('users')->where('userid',$request->userid)->
        update(
            ['password' => $hash]
        );

        return redirect('/admin_user')->with('alert','パスワード変更しました。');

    }
    
}
