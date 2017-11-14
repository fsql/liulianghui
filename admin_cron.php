<?php
require 'dataBase.class.php';
$cookie_jar_index = dir(__FILE__)."/".'cookie.txt'; 

function GetList(){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`adminlast`!=  '$today' and `phone`!= '18366883261'");
    $db->order('rand()');
    global $rowList;
    $rowList=$db->select();
    if (!$rowList){die('no more mobile need row');}
}

function loadLogin($Login_cookie,$uid){
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, 'http://shake.sd.chinamobile.com/');
    curl_setopt ( $ch, CURLOPT_HEADER, 1);
    curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch, CURLOPT_COOKIE,$Login_cookie);
    curl_setopt ( $ch, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt ( $ch, CURLOPT_POSTFIELDS,$formdata);
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
        die('Login_error:'.$uid);
        }
}


function set_mobile($Do_cookie){
    $ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=setDrawMobile&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ( $ch3, CURLOPT_POSTFIELDS,"drawMobile=18366883261");
    $re = curl_exec ( $ch3 );
    $re1 = json_decode($re);
    curl_close ( $ch3 );
    if($re1->message=='ok'){
        return true;
    }else{
        die('wrong');
}
}

function do_row($Do_cookie,$uid){
    $ch2 = curl_init ();
    curl_setopt ( $ch2, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=draw&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch2, CURLOPT_HEADER, 0);
    curl_setopt ( $ch2, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch2, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch2, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch2, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch2 );
    $re1 = json_decode($re);
    curl_close ( $ch2 );
    if($re1->message=='ok'){
        return '³É¹¦1´Î';
    }elseif($re1->code=='50001'){
        $today=date('Y-m-d');
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`uid` =  $uid");
        $dbi->set("`adminlast` = '$today'");
        $dbi->update();
        die('finish');
    }else{
        die('row_error:'.$uid);
    }
    
}


function cron($uid,$phone,$Login_cookie){
    $Do_cookie=loadLogin($Login_cookie,$uid);
    $endtime = time() + 30;
    if(set_mobile($Do_cookie)==true){
        while($endtime > time()){
            echo '</br>'.do_row($Do_cookie,$uid);
            sleep(2);
  }
  }
  }
GetList();
$uid=$rowList[0]['uid'];
$phone=$rowList[0]['phone'];
$Login_cookie=$rowList[0]['cookie'];
cron($uid,$phone,$Login_cookie);
  
?>