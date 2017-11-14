<?php
//九宫格/十六宫格 挑战
global $puzzle_endtime;
$puzzle_endtime = time() + 20;

puzzle_init();

function puzzle_cron($uid,$Login_cookie){
	$Do_cookie=puzzle_loadLogin($Login_cookie,$uid);
	global $puzzle_endtime;
	if($puzzle_endtime>time()){
		puzzle_do($Do_cookie,$uid);
		puzzle_init();
	}
}

function puzzle_init(){
	$List=puzzle_getlist();
	$uid=$List[0]['uid'];
    $Login_cookie=$List[0]['cookie'];
	puzzle_cron($uid,$Login_cookie);
}
function puzzle_getlist(){
	$today=date('Y-m-d');
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`puzzle_last`!= '$today' and `error`=0 and `cookie`!= '' ");
    $db->order('rand()');
    $rowList=$db->select();
	if (!$rowList){die('All Done');}else{return $rowList;}
}

function puzzle_loadLogin($Login_cookie,$uid){
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

function puzzle_do($Do_cookie,$uid){

	if(date("d",time())<10){
		$type="0";
		$position=date("d",time());
	}elseif(date("d",time())>9 && date("d",time())<26){
		if(date("d",time())==10){
			puzzle_complete_9($Do_cookie);
		}
		$type="1";
		$position=date("d",time())-9;
	}else{
		if(date("d",time())==26){
			puzzle_complete_16($Do_cookie);
		}
		return 'finish';
	}
	$ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/puzzle?method=openPuzzle&position='.$position.'&puzzleType='.$type.'&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch3 );
    curl_close ( $ch3 );
    $js=json_decode($re);
	if($js->status=="ok" || $js->code=="50023"){
		$todaytime=date('Y-m-d');
		$dbiii=new DataBase();
        $dbiii->table('llh_member');
        $dbiii->where("`uid` =  $uid");
        $dbiii->set("`puzzle_last` = '$todaytime' , `puzzle_message`='ok'");
        $dbiii->update();
        return 'ok';
		
	}else{
		$err_msg=$js->message;
		$todaytime=date('Y-m-d');
		$dbiii=new DataBase();
        $dbiii->table('llh_member');
        $dbiii->where("`uid` =  $uid");
        $dbiii->set("`puzzle_last` = '$todaytime', `puzzle_message`='$err_msg'");
        $dbiii->update();
        return 'ok';
	}
}

function puzzle_complete_9($Do_cookie){
	$ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/puzzle?method=completePuzzle&puzzleType=0&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch3 );
    curl_close ( $ch3 );
    $js=json_decode($re);
}

function puzzle_complete_16($Do_cookie){
	$ch3 = curl_init ();
    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/puzzle?method=completePuzzle&puzzleType=1&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
    $re = curl_exec ( $ch3 );
    curl_close ( $ch3 );
    $js=json_decode($re);
}




?>