<?php
if (isset($_POST['step']) && isset($_POST['phone'])){
    require 'dataBase.class.php';
    $cookie_jar_index = dir(__FILE__)."/".'cookie.txt'; 
    $step=safe_replace(trim($_POST['step']));
    $pass=safe_replace(trim($_POST['pass']));
    $phone=safe_replace(trim($_POST['phone']));
	$phoneid=safe_replace(trim($_POST['phoneid']));
    $share_plan=safe_replace(trim($_POST['share']));
    if($step=='1'){
        $config = require_once 'dataBaseConfig.include.php';
        $mysqli = new mysqli($config['host'],$config['user'],$config['password'],$config['dbName'],$config['port']);
        $sql = "DELETE FROM `llh_member` WHERE `phone`=".$phone;
        $mysqli->query($sql);
        $formdata = 'mobile='.$phone;
        $ch = curl_init ();
        $header = array ("Content-Type: application/x-www-form-urlencoded");
        curl_setopt ( $ch, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=getPassword&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
        curl_setopt ( $ch, CURLOPT_HEADER, 1);
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS,$formdata);
        $re = curl_exec ( $ch );
        preg_match('/Set-Cookie:(.*);/iU',$re,$str);
        preg_match('/\"message\":\"(.*)\"/iU',$re,$re1);
        $cookie=$str[1];
        curl_close ( $ch );
        $rej=json_decode($re1[0]);
        if($re1[1]!='ok'){
            die($re1[1]);
        }
        if($cookie!=""){
            $db = new DataBase();
            $db->table('llh_member');
            $db->data(array(array('phone' => $phone,'jsessionid' => $cookie)));
            if($db->insert()==1){
               // echo $re;
                echo '验证码已发送,5分钟内有效';
            }else{die('服务器出错！请重试');}
        }else{die('服务器出错！请重试');}
    }elseif($step=='2'){
        if($pass==""){die('请填写验证码');}
        $dbi = new DataBase();
        $dbi->table('llh_member');
        $dbi->where("phone = '$phone'");
        $jsessionid = $dbi->select();
        $jsessionid = $jsessionid['0']['jsessionid'];
        $formdata = 'mobile='.$phone.'&password='.$pass;
        $ch = curl_init ();
        $header = array ("Content-Type: application/x-www-form-urlencoded");
        curl_setopt ( $ch, CURLOPT_URL, 'http://shake.sd.chinamobile.com/shake?method=loginDo&r=0.'.rand(1000,9999).rand(1000,9999).rand(1000,9999));
        curl_setopt ( $ch, CURLOPT_HEADER, 1);
        curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
        curl_setopt ( $ch, CURLOPT_COOKIE,$jsessionid);
        curl_setopt ( $ch, CURLOPT_COOKIEFILE, $cookie_jar_index); 
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS,$formdata);//http_build_query
        $re = curl_exec ( $ch );
        preg_match('/userSign_wap_encrypt=(.*);/iU',$re,$str1);
        preg_match('/prov_wap_encrypt=(.*);/iU',$re,$str2);
        preg_match('/nickName_encrypt=(.*);/iU',$re,$str3);
        preg_match('/loginToken_encrypt=(.*);/iU',$re,$str4);
		preg_match('/\"message\":\"(.*)\"/iU',$re,$re5);
        $cookie=$str1[0].' '.$str2[0].' '.$str3[0].' '.$str4[0];
        curl_close ( $ch );
        if($str2[0]!=""){
            $dbii = new DataBase();
            $dbii->table('llh_member');
            $dbii->where("phone = '$phoneid'");
            global $share_plan;
			$today=date('Y-m-d');
            if($share_plan=='true'){
                $dbii->set("`cookie` = '$cookie' , `share_switch` = 1 , `addtime`='$today'");
                $notic='，并一同加入互摇分享计划。';
            }else{
                $dbii->set("`cookie` = '$cookie' , `share_switch` = 0 , `addtime`='$today'");
                $notic='';
            }
            
            if($dbii->update()){
                echo '您的手机号码【'.$phoneid.'】已成功加入队列'.$notic;
            }
            }else{print_r($re5);}
}elseif($step=='3'){
    $list=checkphone($phone);
    $Login_cookie=$list[0]['cookie'];
    $Do_cookie=loadLogin($Login_cookie);
    if($Do_cookie==false){die ($phone.'信息有误，请重新获取验证码并提交！');
    }else{
    $error=$list[0]['error'];
    if($error==1){die($phone.'验证失败，正在等待服务器重新验证。或者您可以返回重新获取验证码并提交');}  
//    $uid=$list[0]['uid'];
//    first_row($Do_cookie,$uid);
    $last=$list[0]['last'];
    echo '号码：【'.$phone.'】状态：';
    if($last=='1111-11-11'){echo '新加入用户，正在等待服务器更新数据';}
    else{
        $credit=$list[0]['credit'];
//        echo '上次摇一摇日期：'.$last.'</br>当前流量币：'.$credit.'个</br>是否加入互摇分享计划：';
echo '上次摇一摇日期：'.$last.'是否加入互摇分享计划：';
        $share_switch=$list[0]['share_switch'];
        if($share_switch==0){echo '未加入';}
        else{
            echo '已加入</br>';
//            $share_last=$list[0]['share_last'];
//            $share_getlast=$list[0]['share_getlast'];
//            echo '上次接受互摇日期：'.$share_last.'</br>上次自动领取流量日期：'.$share_getlast;
        }
        
    }
}
}
}
function checkphone($phone){
    
    $cookie_jar_index = dir(__FILE__)."/".'cookie.txt'; 
    $db=new DataBase();
    $db->table('llh_member');
    $db->where("`phone`= '$phone' ");
    $list=$db->select();
    if(!$list){die($phone.'未加入云服务器，请检查号码！新用户请返回，输入号码并点击【获取短信验证码】');
    }else{return $list;}
}
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','',$string);
	$string = str_replace('>','',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	$string = str_replace('\0','',$string);
    $string = str_replace('=','',$string);
    $string = str_replace('/','',$string);
	return $string;
}
function loadLogin($Login_cookie){
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
//function first_row($Do_cookie,$uid){
//    $ch3 = curl_init ();
//    curl_setopt ( $ch3, CURLOPT_URL, 'http://shake.sd.chinamobile.com/score?method=addDrawScore&_='.rand(1000,9999).rand(1000,9999).rand(1000,9999));
//    curl_setopt ( $ch3, CURLOPT_HEADER, 0);
//    curl_setopt ( $ch3, CURLOPT_COOKIEJAR, $cookie_jar_index);
//    curl_setopt ( $ch3, CURLOPT_COOKIE,$Do_cookie);
//    curl_setopt ( $ch3, CURLOPT_COOKIEFILE, $cookie_jar_index); 
//	curl_setopt ( $ch3, CURLOPT_RETURNTRANSFER, 1);
//    $re = curl_exec ( $ch3 );
//    curl_close ( $ch3 );
//    $js=json_decode($re);
//    $credit=$js->result->list[0]->credit;
//    $dbiii=new DataBase();
//        $dbiii->table('llh_member');
//        $dbiii->where("`uid` =  $uid");
//        $dbiii->set("`credit` = '$credit'");
//        $dbiii->update();
//}
?>