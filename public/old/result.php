<?php

function escape($str)  
{  
$str = rawurlencode($str);  
$str = str_replace(array('%3A','%26','%3D','%40', '%2A', '%2B', '%2F'), array(':','&','=','@', '*', '+', '/'), $str);  
return $str;  
}  

$data='';
if(isset($_REQUEST) && count($_REQUEST)){
	foreach($_REQUEST as $name=>$value){$data.='&'.$name.'='.$value;}
}

if(!$data)
{
header('Content-type: text/html; charset=UTF-8');
echo "No Data";
exit;
}

$data=substr($data, 1, strlen($data)-1);

$fp = fopen ('ecommtools_result.log', "a");
fwrite ($fp, "$data\n");
fclose ($fp);

if(in_array('curl', get_loaded_extensions()))
{
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://ecommtools.com/cgi-bin/getpaid.cgi");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_exec($ch);
curl_close($ch);

$result="OK";
if($_REQUEST['InvId']){$result="OK".$_REQUEST['InvId'];}
if($_REQUEST['WMI_ORDER_STATE']){$result="WMI_RESULT=OK";}
if($_REQUEST['GATEWAY']=='ZPAYMENT'){$result="YES";}

header('Content-type: text/html; charset=UTF-8');
echo $result;
exit;
}

Header("Location: http://ecommtools.com/cgi-bin/getpaid.cgi?$data");

exit;
?>