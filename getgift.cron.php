<?php

global $endtime1;
$endtime1 = time() + 20;
getgift_go();

function getgift_go(){

    getgift_init_cron();
}


function getgift_init_cron(){
    $list=getgift_GetPhone();
    $list_uid=$list[0]['uid'];
    $list_phone=$list[0]['phone'];
    $list_cookie=$list[0]['cookie'];
    getgift_cron($list_uid,$list_phone,$list_cookie);
}



function getgift_GetPhone(){
    $today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`share_getlast`!=  '$today' and `error`=0 and `share_switch`=1 and `cookie`!= '' ");
    $db->order('rand()');
    $rowList=$db->select();
    if (!$rowList){die('All Done');}else{return $rowList;}
}

function getgift_loadLogin($Login_cookie,$uid){
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

function getgift_GetGiftList($Do_cookie){
    $ch2 = curl_init ();
    curl_setopt ( $ch2, CURLOPT_URL, 'http://shake.sd.chinamobile.com/flowScore?method=getTransferGiftsList&queryType=all&type=others&status=2');
    curl_setopt ( $ch2, CURLOPT_HEADER, 0);
    curl_setopt ( $ch2, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch2, CURLOPT_COOKIE,$Do_cookie.$jsessionid);
    curl_setopt ( $ch2, CURLOPT_COOKIEFILE, $cookie_jar_index); 
    curl_setopt ( $ch2, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch2 );
    $js=json_decode($re);
    curl_close ( $ch2 );
    $GiftList=$js->result->list;
    $count=count($GiftList);
    if($count==0){return 'nogift';}
    $s=0;
    $id="";
    while($s<$count){
        $hid=$GiftList[$s]->handselID;
        $id=$id.$hid;
        $s=$s+1;
        if($s<$count){
            $id=$id.'%2C';
            }
        }
    return $id;
}


function getgift_GiftGet($list_uid,$Do_cookie,$Gift_id){
    if($Gift_id=='nogift'){
        return 'nogift';
        }
    $ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/flowScore?method=transferGiftsReceive');
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie.$jsessionid);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
    curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ( $ch3, CURLOPT_POSTFIELDS,'id='.$Gift_id);
    $re = curl_exec ( $ch3 );
    $js=json_decode($re);
    curl_close ( $ch3 );
    return $js->message;
}

function getgift_cron($list_uid,$list_phone,$list_cookie){
    $Do_cookie=getgift_loadLogin($list_cookie,$list_uid);
    $Gift_id=getgift_GetGiftList($Do_cookie);
    $re=getgift_GiftGet($list_uid,$Do_cookie,$Gift_id);
    if($re=='ok' || $re=='nogift' ){
        $today=date('Y-m-d');
        $dbi=new DataBase();
        $dbi->table('llh_member');
        $dbi->where("`uid` = $list_uid");
        $dbi->set("`share_getlast` =  '$today' , `share_message`= '$re' ");
        $dbi->update();
    }
    global $endtime1;
      if($endtime1 > time()){
        sleep(2);
        getgift_init_cron();
      }else{die ('ok');}
}

?>