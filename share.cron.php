<?php

global $endtime2;
$endtime2 = time() + 20;
share_init();

function share_init(){
    $DoList=share_GetDoPhone();
    $Do_uid=$DoList[0]['uid'];
    $Do_phone=$DoList[0]['phone'];
    $Do_cookie=$DoList[0]['cookie'];
    if(!share_GetSharePhone('0')){
        require 'puzzle_cron.php';
        return;
    }
    share_cron($Do_uid,$Do_phone,$Do_cookie);
}

function share_GetSharePhone($Do_phone){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`share_last`!=  '$today' and `error`=0 and `share_switch`=1 and `phone`!= $Do_phone and `cookie`!= '' ");
    $db->order('rand()');
    $rowList=$db->select();
    if (!$rowList){
        return false;
        }else{return $rowList[0]['phone'];}
}
function share_GetDoPhone(){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where(" `error`=0 and `share_switch`=1");
    $db->order('rand()');
    $DoList=$db->select();
    if (!$DoList){die('All Done');}else{return $DoList;}
}
function share_loadLogin($Login_cookie,$uid){
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

function share_set_phone($Do_cookie,$share_phone){
    $ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=setDrawMobile&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_POSTFIELDS,"drawMobile=".$share_phone);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch3 );
    $re1 = json_decode($re);
    curl_close ( $ch3 );
    if($re1->message=='ok'){
        return true;
    }else{return false;}
}

function share_do_row($Do_cookie,$share_phone,$Do_phone){
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
        return true;
    }elseif($re1->code=='50001'){
        $today=date('Y-m-d');
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`phone` =  $share_phone");
        $dbi->set("`share_last` = '$today'");
        $dbi->update();
        return false;
    }else{
//        $dbi=new DataBase();
//        $dbi->table('llh_member');
//        $dbi->where("`phone` =  $Do_phone");
//        $dbi->set("`error` = 1");
//        $dbi->update();
        die('wrong!');
    }
    
}




function share_cron($uid,$phone,$Login_cookie){
    $Do_cookie=share_loadLogin($Login_cookie,$uid);
    if($Do_cookie){
        $share_phone=share_GetSharePhone($phone);
        $re1 = share_set_phone($Do_cookie,$share_phone);
        if($re1==true){
            global $endtime2;
            if($endtime2 > time()){
                do {$re=share_do_row($Do_cookie,$share_phone,$phone);}while($re==true);
                if($re==false){
                    share_init();
                }
        }else{
            $t=time();
            die('ok');
            }
  }
  }
  }
?>