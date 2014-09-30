<?php 
$g_ids = array();
class T
{
	public function go($url)
	{
		header('Location: '.$url);
		exit();
	}
	
	public static function debug($obj)
	{
		
	}
	
	public static function preZero($num)
	{
		return str_pad ( $num, 2, '0', STR_PAD_LEFT );
	}
	
	public static function getIp()
	{
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		return false;
	}
	
	public static function getSelf()
	{
		$out = explode('?',$_SERVER['REQUEST_URI']);
		return $out[0];
	}
	
	public static function tt($str,$length = 160)
	{
		if(strlen($str) > $length)
		{
			$str = preg_replace("/[^ ]*$/", '', substr($str, 0, $length)).' ...';
		}
		return $str;
	}
	
	public static function jsSafe($str,$quote = "'")
	{
		$replace = "\\'";
		if($quote == '"')
		{
			$replace = '\\"';
		}
		return str_replace($quote, $replace, $str);
	}
	
	public static function dateTime($mongoDate)
	{
		return date('d.m.Y H:i',$mongoDate->sec).' Uhr';
	}
	
	public static function cleanUriName($name)
	{
		$name = strtolower($name);
		$name = trim($name);
		$name = str_replace(array('  ','	','_','/','\\'), ' ', $name);
		$name = str_replace(array(' ','ä','ö','ü','ß','é','á'), array('-','ae','oe','ue','ss','e','a'), $name);
		return preg_replace('/[^a-z0-9\-]/', '', $name);
	}
	
	public static function linkify($text,$new_window = true) 
	{
		$target = '';
		if($new_window)
		{
			$target = ' target="_blank"';
		}
		$reg_exUrl = "/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/";
		if(preg_match($reg_exUrl, $text)) 
		{
			return preg_replace($reg_exUrl, '<a href="${1}" '.$target.'>${1}</a> ', $text);
		} 
		else 
		{
			return $text;
		}
	}
	
	public static function time($mongoDate)
	{
		return date('H:i',$mongoDate->sec).' Uhr';
	}
	
	public static function date($mongoDate)
	{
		return date('d.',$mongoDate->sec).' '.s('month_'.date('n',$mongoDate->sec)).' '.date('Y',$mongoDate->sec);
	}
}