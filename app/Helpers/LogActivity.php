<?php


namespace App\Helpers;
use App\Models\LogActivity as ModelsLogActivity;

class LogActivity
{

    public static function addToLog($request, $subject)
    {
    	$log = [];
    	$log['subject'] = $subject;
    	$log['url'] = $request->fullUrl();
    	$log['method'] = $request->method();;
    	$log['ip'] = $request->ip();
    	$log['agent'] = $request->header('user-agent');
    	$log['user_id'] = auth()->check() ? auth()->user()->id : 1;
    	ModelsLogActivity::create($log);
    }


    public static function logActivityLists()
    {
    	return ModelsLogActivity::latest()->get();
    }


}