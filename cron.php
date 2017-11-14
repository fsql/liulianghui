<?php
require 'dataBase.class.php';
$cookie_jar_index = dir(__FILE__)."/".'cookie.txt'; 



$endtime = time() + 20;
init_cron();


function init_cron(){
    $List=GetList();
    if(!$List){
        require 'share.cron.php';
        return ;
    }
    $uid=$List[0]['uid'];
    $phone=$List[0]['phone'];
    $Login_cookie=$List[0]['cookie'];
    cron($uid,$phone,$Login_cookie);
}

function GetList(){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`last`!=  '$today' and `error`=0 and `cookie`!= ''");
    $db->order('rand()');
    $rowList=$db->select();
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
        $dbii=new DataBase();
        $dbii->table('llh_member');
        $dbii->where("`uid` =  $uid");
        $dbii->set("`error` = 1");
        $dbii->update();
        die('Login_error');
        }
}

function first_row($Do_cookie,$uid){
    $ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/score?method=addDrawScore&_='.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch3 );
    curl_close ( $ch3 );
    $js=json_decode($re);
    $credit=$js->result->list[0]->credit;
    $dbiii=new DataBase();
        $dbiii->table('llh_member');
        $dbiii->where("`uid` =  $uid");
        $dbiii->set("`credit` = '$credit'");
        $dbiii->update();
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
	$credit=$re1->result->list[0]->credit;
	$dbiii=new DataBase();
        $dbiii->table('llh_member');
        $dbiii->where("`uid` =  $uid");
        $dbiii->set("`credit` = '$credit'");
        $dbiii->update();
        return 'ok';
    }elseif($re1->code=='50001'){
        $today=date('Y-m-d');
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`uid` =  $uid");
        $dbi->set("`last` = '$today'");
        $dbi->update();
        return 'finish';
    }else{
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`uid` =  $uid");
        $dbi->set("`error` = 1");
        $dbi->update();
        die('row_error');
    }
    
}



function cron($uid,$phone,$Login_cookie){
    $Do_cookie=loadLogin($Login_cookie,$uid);
    if($phone){
        first_row($Do_cookie,$uid);
        global $endtime;
        if($endtime > time()){
            do{
                $re=do_row($Do_cookie,$uid);
            }while($re=='ok');
            if($re=='finish'){
                first_row($Do_cookie,$uid);
                init_cron();
            }
  }
  }
}
?>