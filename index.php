<?php
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_URL, 'http://www.liulianghui.com.cn/');
curl_setopt ( $ch, CURLOPT_HEADER, 1);
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_jar_index);
curl_setopt ( $ch, CURLOPT_COOKIE,$cookievalue);
$re = curl_exec ( $ch );
preg_match('/name="modulus" value="(.+)"/',$re,$modulus_id);
curl_close ( $ch );
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<title>流量汇---云摇流量 By FreezingTiny</title>
    <meta charset='utf-8'/>
    <link rel="shortcut icon" href="ico.ico"/>
	<link href="css/bootstrap.min.css" rel="stylesheet">
  	<link href="css/font-awesome.min.css" rel="stylesheet">
</head>
<div class=" container col-sm-7 col-sm-offset-3"><br>
<div class="panel panel-primary ">
<div class="panel-body"><ol class="breadcrumb"><li class="active">流量汇---云摇流量   Design by FreezingTiny<?php echo $my_qq; ?></li></ol>
<div class="form-group"><label class="col-sm-3 control-label">手机号码：</label><div class="col-sm-9"><input class="form-control" type="number" name="phone" id="phone"  /></div></div></br></br>
<div style='display:none;' id="msgnum" name="msgnum" class="form-group"><label class="col-sm-3 control-label">短信验证码：</label><div class="col-sm-9"><input class="form-control" type="number" name="pass" id="pass"/></div></div></br>
<div class="form-group"><div class="col-sm-offset-3 col-sm-9"><input type="checkbox" checked="true" id="shareplan" name="shareplan" value="true" />加入<a href="#" onclick= "join_share_plan();">互摇分享计划</a></div></div></br>
<div class="form-group"><div class="col-sm-offset-3 col-sm-9"><input class="btn btn-primary btn-block" type="submit" id="submit" name="submit" value="获取短信验证码" onclick="return_get_msgnum();"/><input class="btn btn-primary btn-block" type="submit" id="submit1" name="submit1" value="查询状态" onclick="return_phone_query();"/></div></div>
<input style="display:none;" type="text" name="modulus" id="modulus" value="<?php echo $modulus_id[1];?>" />
<p><strong>本程序永久免费开放，近日发现有人用摇流量骗钱，谨防上当！</strong></p>
<p>说明：</p>
<p>1.仅山东移动用户可用。</p>
<p>2.建议关闭短信提醒。</p>
<p>3.添加完成后不要在其他端退出登陆，否则会导致cookies失效，如果失效，重新添加即可。</p>
<p>4.cookies可能会不定期失效，建议一整周收不到提示时来本页面重新添加</p>
<p>5.更新：</p>
<p>[2016.07.12]每天翻9宫格和16宫格增加流量币（每月1-9号翻9宫格，10-25号翻16宫格，每天只翻固定位置）。</p>
<p>[2016.03.01]修复了无法添加问题</p>
<p>Design by <a href="http://blog.fsql.net/" target="_blank">FreezingTiny</a></p>
<script src="/rsa/RSA.js" type="text/javascript"></script>
<script src="/rsa/BigInt.js" type="text/javascript"></script>
<script src="/rsa/Barrett.js" type="text/javascript"></script>
<script language='javascript' src='/js/js.js'></script>
<span style="display:none"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1000475048'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1000475048%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script></span>
</html>
