<?php
require 'dataBase.class.php';
$cookie_jar_index = dir(__FILE__)."/".'cookie.txt'; 


$endtime = time() + 30;
cron();
function cron(){
    $List=GetList();
    $uid=$List[0]['uid'];
    $Login_cookie=$List[0]['cookie'];
    global $endtime;
    if($endtime > time()){
        $re=loadLogin($Login_cookie,$uid);
        if($re){
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`uid` =  $uid");
        $dbi->set("`error` = 0");
        $dbi->update();
    }
    cron();
    }else{die('ok');}
    
}



function GetList(){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`error`=1 and `cookie`!= ''");
    $db->order('rand()');
    $rowList=$db->select();
    if(!$rowList){die('All Done');}
    return $rowList;
}


function loadLogin($Login_cookie,$uid){
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, 'http://shake.sd.chinamobile.com/');
    curl_setopt ( $ch, CURLOPT_HEADER, 1);
    curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch, CURLOPT_COOKIE,$Login_cookie);
    curl_setopt ( $ch, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ( $ch, CURLOPT_POSTFIELDS,$formdata);
    $re = curl_exec ( $ch );
    preg_match('/Set-Cookie:(.*);/iU',$re,$str);
    $jsessionid=$str[1];
    curl_close ( $ch );
    $ch1 = curl_init ();
    curl_setopt ( $ch1, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=loadLoginMobile');
    curl_setopt ( $ch1, CURLOPT_HEADER, 0);
    curl_setopt ( $ch1, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch1, CURLOPT_COOKIE,$Login_cookie.$jsessionid);
    curl_setopt ( $ch1, CURLOPT_COOKIEFILE, $cookie_jar_index); 
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch1 );
    $re1 = json_decode($re);
    curl_close ( $ch1 );
    if($re1->result->loginMobile!=''){
        $cookie=$Login_cookie.$jsessionid;
        return $cookie;
    }else{
        return false;
        }
}



?>