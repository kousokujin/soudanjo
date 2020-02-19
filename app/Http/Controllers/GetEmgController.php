<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class GetEmgController extends Controller
{
    //

    public function get_Emg(){

        $time = '20200214';
        $client = new Client();
        $webhookurl = 'https://pso2.akakitune87.net/api/emergency';
        
        $post_json = [
            'EventDate' => $time,
            'EventType' => '緊急'
        ];

        $option = [
            'json' => $post_json,
            'http_errors' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
                ]
            ];
        $request = $client->Request('POST', $webhookurl, $option);
        /*
        try{
            $request = $client->Request('POST', $webhookurl, $option);
        }catch(Exception $ex){
            \Log::error($ex->getmessage());
        }
        */

        $raw_json = mb_convert_encoding($request->getBody(),'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $json_data = json_decode($raw_json,true);

        $events = [];
        foreach($json_data as $j){
            $year = Carbon::today()->year;
            $time = new Carbon($year.'/'.$j['Month'].'/'.$j['Date'].' '.$j['Hour'].':'.$j['Minute'].':00');
            $event_name = $j['EventName'];

            array_push($events,['Event' => $event_name, 'time'=> $time]);
        }

        //return $events;

        return view('emg_list',['event'=>$events,'json'=>$events]);
    }
}
