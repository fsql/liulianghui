/*var key = new RSAKeyPair(publicExponent, "", modulus);
	mobile = encryptedString(key, mobile);
	password = encryptedString(key, password);*/
var share;
var step = 1;
function $(str){
		return document.getElementById(str);
	}

function return_get_msgnum(){

    $('submit').style.display='none';
    $('submit1').style.display='none';
	var postStr
	if(step==1){
		postStr='phone=' + $('phone').value;
		postStr+='&pass=' + $('pass').value
	}else if(step==2){
		var phonem;
		var passm;
		phonem=$('phone').value;
		passm=$('pass').value;
		setMaxDigits(129);
		var key = new RSAKeyPair("10001", "", $('modulus').value);
		phonem = encryptedString(key, phonem);
		passm = encryptedString(key, passm);
		postStr='phone=' + phonem;
		postStr+='&pass=' + passm;
		postStr+='&phoneid=' + $('phone').value;
	}
	postStr+='&step=' + step;
	if ($('shareplan').checked==true){
                postStr+='&share=' + 'true';
    }
        XHR=new XMLHttpRequest();
		XHR.open('POST','main.php',true);
		XHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		XHR.send(postStr);
        XHR.onreadystatechange=function(){
			if (XHR.readyState==4 && XHR.status==200){
			 if(step == 1){alert(XHR.responseText);}
             if(step == 1 && XHR.responseText!= '验证码已发送,5分钟内有效'){
                location.reload();
             }else{
                step = 2;
    $('submit').style.display='';
    $('msgnum').style.display='';
    $('submit').value="提交";
	if($('pass').value!=""){
                window.location.href='http://llh.fsql.net/t.php?msg='+XHR.responseText;
            }
             }	
             
			}
            
		}
    
}

function join_share_plan(){
    alert('加入互摇分享计划，系统会自动每天用您的帐号与其他用户互相使用[帮朋友摇奖]功能');
}

function return_phone_query(){
    $('submit').style.display='none';
    $('submit1').value="正在查询.......";
     var postStr='phone=' + $('phone').value;
     postStr+='&step=3';
     
     XHR=new XMLHttpRequest();
		XHR.open('POST','main.php',true);
		XHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		XHR.send(postStr);
        XHR.onreadystatechange=function(){
            window.location.href='http://llh.fsql.net/t.php?msg='+XHR.responseText;
        }
}
