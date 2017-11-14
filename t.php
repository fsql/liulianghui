<?php

if(isset($_GET['msg'])){
    print(urldecode($_GET['msg']).'</br><a href="http://llh.fsql.net/">点此返回</a>');
    //header("refresh:10;url=http://llh.fsql.net/");
}else{header("location:http://llh.fsql.net/");}

?>
