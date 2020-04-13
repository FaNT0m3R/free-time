<?php
function sendMessageMail($to, $from, $title, $message)
 {
   //Адресат с отправителем

   
   //Формируем заголовок письма
   $subject = $title;
   $subject = '=?utf-8?b?'. base64_encode($subject) .'?=';
   
   //Формируем заголовки для почтового сервера
   $headers = "Content-type: text/html; charset=\"utf-8\"\r\n";
   $headers .= "From: ". $from ."\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";

   //Отправляем данные на ящик админа сайта
   if(!mail($to, $subject, $message, $headers))
      return false;  
   else  
      return true;
 }
 
 
 function getHourStr($hour)
{
	$one = $hour%10;
	if ($one == 1)
		return "час";
	if (($one >= 2) && ($one <= 4))
		return "часа";
	return "часов";
}

function getMinutStr($minut)
{
	$one = $minut%10;
	if ($one == 1)
		return "минута";
	if (($one >= 2) && ($one <= 4))
		return "минуты";
	return "минут";
}


function testchar($charr)
{
	if (((strcmp($charr,'0')>=0) and (strcmp($charr,'9')<=0)) or
		((strcmp($charr,'a')>=0) and (strcmp($charr,'z')<=0)) or
		((strcmp($charr,'A')>=0) and (strcmp($charr,'Z')<=0)) or
		((strcmp($charr,'а')>=0) and (strcmp($charr,'я')<=0)) or
		((strcmp($charr,'А')>=0) and (strcmp($charr,'Я')<=0)) or
		(strcmp($charr,'_')==0))
		return true;
	else
		return false;
	
}

function testchar2($charr)
{
	if (((strcmp($charr,'0')>=0) and (strcmp($charr,'9')<=0)) or
		((strcmp($charr,'a')>=0) and (strcmp($charr,'z')<=0)) or
		((strcmp($charr,'A')>=0) and (strcmp($charr,'Z')<=0)) or
		((strcmp($charr,'а')>=0) and (strcmp($charr,'я')<=0)) or
		((strcmp($charr,'А')>=0) and (strcmp($charr,'Я')<=0)))
		return true;
	else
		return false;
	
} 

function mysql_escape_sym($inp)
{

	if(is_array($inp))
		return array_map(__METHOD__,$inp);
	if(!empty($inp) && is_string($inp)){
		return str_replace(array('\\',"\0","\n","\r","'",'"',"\x1a","-"),
					array('\\\\',"\\0","\\n","\\r","\\'",'\\"',"\\Z","\-"),$inp);
	}
	return $inp;
}

 ?>