<?php
if(!defined("a")) exit("Error 001");
$ip = get_ip();
if(strpos($ip,'121.42.0.')>-1){//判断是不是阿里云绿网监控IP，屏蔽掉
	header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
	exit;
}

$listnum = array('①','②','③','④','⑤','⑥','⑦','⑧','⑨','⑩');
$myurl = myurl();
if(isset($_GET['q'])){
	$q = hd_clearStr($_GET['q']);

}
if(strlen($q)<1){
	header("location: ".SYSPATH);
	exit;
}
if(isset($_GET['cr']) && strlen($_GET['cr'])>1){
	$q = iconv($_GET['cr'],"utf-8",$q);
	$gourl = huoduansourl($q);
    header("location: $gourl");
    exit;
}
$ref = $_SERVER['HTTP_REFERER'];
if(strpos($ref,'m.baidu.com') && strpos($q,'%')>-1){
	$q = urldecode(urldecode($q));
	$gourl = huoduansourl($q);
    header("location: $gourl");
    exit;
}
if(isset($_GET['re'])){
	$q = htmlspecialchars_decode($q);
	$gourl = huoduansourl($q);
    header("location: $gourl");
    exit;
}
if(isset($_GET['p'])){
	$p=$_GET['p'];
	if($p>50){
	   $p=50;
	}
	if(REWRITE==1 && strpos(URLRULE2,'{qe}')>-1 && strpos($myurl,'q=')<1&&  strpos($myurl,'more=1')<1){
		$q = qdecode($q);
	}
}else{
	$p=1;
	if(REWRITE==1 && strpos(URLRULE1,'{qe}')>-1 && strpos($myurl,'q=')<1&&  strpos($myurl,'more=1')<1){
		$q = qdecode($q);
	}
}
 $killword = file_get_contents(ROOT_PATH.'/data/huoduan.killword.txt');
  if(strpos($killword,"\r\n")>-1){
	$killword = trim($killword,"\r\n");
	$killwordlist = explode("\r\n",$killword);
  }else{
	   $killword = trim($killword,"\n");
	   $killwordlist = explode("\n",$killword);
  }

  foreach($killwordlist as $k=>$v){
	  $b404 = 0;
	  if(substr($v,0,1)=='~'){
		 $v = ltrim($v,'~');
		 $b404 = 1;
	  }
	  if(substr($v,0,1)=='|'){
		  $v = ltrim($v,'|');
		  if(strtolower($q) == strtolower($v)){
			  $listcount=0;
			  $kill=1;
			  $list['count']=0;$list['pnum']=0;
			  if($b404==1){back404();}
			  break;
		  }
	  }else if(strlen($v)>2){
		  if(strpos(strtolower($q),strtolower($v))>-1 || strtolower($q) == strtolower($v)){
			  $listcount=0;
			  $kill=1;
			  $list['count']=0;$list['pnum']=0;
			  if($b404==1){back404();}
			  break;
		  }
	  }
  }

$s = urlencode($q);
if($huoduan['searchtype']=='baidu'){
	$list = huoduan_get_newbaidu($q,$p,$huoduan['cachetime']);
	$listcount = count($list['data']);
	if($listcount<2){
		$list = huoduan_get_haosou($q,$p,$huoduan['cachetime']);
		$listcount = count($list['data']);
		if($listcount<2){
			$list = huoduan_get_sogou($q,$p,$huoduan['cachetime']);
			$listcount = count($list['data']);
		}
	}
}else{
	$list = huoduan_get_haosou($q,$p,$huoduan['cachetime']);
	$listcount = count($list['data']);
	if($listcount<2){
		$list = huoduan_get_newbaidu($q,$p,$huoduan['cachetime']);
		$listcount = count($list['data']);
		if($listcount<2){
			$list = huoduan_get_sogou($q,$p,$huoduan['cachetime']);
			$listcount = count($list['data']);
		}
	}
}
if(is_array($list)){
	$description = $q.'相关信息，'.strip_tags($list['data'][1]['title']).strip_tags($list['data'][2]['des']);
	$description = strip_tags($description);
	$description = str_replace('"','',$description);
}
if($host==strtolower(MOBILEDOMAIN)){
   	include(ROOT_PATH.'/inc/mobile_search.php');
	exit;
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $q?><?php if($p!=1){echo '_第'.$p.'页';}?> - <?php echo $huoduan['sitename']?></title>
<meta name="keywords" content="<?php echo $q?>" />
<meta name="description" content="<?php echo $description?>" />
<script type="text/javascript" src="<?php echo SYSPATH?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo SYSPATH?>js/main.js"></script>
<?php
if(MOBILEDOMAIN!=''){
	$mobileurl = huoduansourl($q,$p,MOBILEDOMAIN);
	echo '<meta name="mobile-agent" content="format=html5;url='.$mobileurl.'">
<meta name="mobile-agent" content="format=xhtml;url='.$mobileurl.'">
<meta name="mobile-agent" content="format=wml; url='.$mobileurl.'">';
	$ref = $_SERVER['HTTP_REFERER'];
	if($host!=MOBILEDOMAIN && !refdn($ref,MOBILEDOMAIN)){
?>
<script type="text/javascript">gotomurl('<?php echo $mobileurl?>');</script>
<?php
	}
}
?>
<link href="<?php echo SYSPATH?>images/style.css" rel="stylesheet" type="text/css" />
<link href="http://www.chainwon.com/css/cebianlan.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SYSPATH?>images/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SYSPATH?>images/app.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="http://www.chainwon.com/favicon.ico">
<link rel="canonical" href="<?php echo huoduansourl($q,$p)?>" />
<link rel="alternate" media="only screen and(max-width: 640px)" href="<?php echo $mobileurl?>">
  <script src="http://www.chainwon.com/js/jquery.min.js"></script>
  <script src="http://www.chainwon.com/js/jquery.appear.min.js"></script>
  <script src="http://www.chainwon.com/js/script.js"></script>
<script type="text/javascript">
function subck(){
	var q = document.getElementById("kw").value;
	if(q=='' || q=='请输入关键字搜索网页'){return false;}else{return true;}
}
</script>
</head>

<body>
<div class="loader">
      <div class="fading-line"></div>
    </div>
<div id="header">
  <div class="con">
      <div class="searchbox">
         <table  width="100%" border="0">
  <tbody>
   <tr>
      <td><form action="<?php echo SYSPATH?>" method="get" onsubmit="return subck();">
       <input class="form-control" align="middle" name="q" class="q" id="kw" value="<?php echo $q?>" maxlength="100" size="50" autocomplete="off" baiduSug="1" style="100%"/><?php if(REWRITE=='1'){?></td>
       <input name="re" type="hidden" value="1" /><?php }?>
      <td><input style="margin-left:10px;" id="btn" class="btn btn-s-md btn-default" align="middle" value="搜索一下" type="submit" /></td>
   </tr>
  </tbody>
  </table>
       </form>
      </div>
  </div>
</div><!--header-->

<div id="hd_main">
<div id="res" class="res">
 <?php

if($listcount>1&& $kill!=1){

  include(ROOT_PATH.'/data/huoduan.ads.php');
  if($list['count']>0){
	 $countstr = '约'.strrev(implode(',', str_split(strrev($list['count']), 3))).'个';
  }
  echo '<div id="resinfo">'.$huoduan['sitename'].'为您找到"'.$q.'"的相关结果'.$countstr.'</div>';
  echo '<div id="result">';
   include(ROOT_PATH.'/inc/plus.php');

  for($i=0;$i<$listcount;$i++){
		$ii = $i;
		$ni = $i;
		if($listcount==10){
			$sort = explode(',',$huoduan['sort']);
			$ni = $sort[$i]-1;
		}

		if(is_array($plusnum)){
			foreach($plusnum as $k=>$v){
				if($pluscontent[$k]!='' && ($ii+1)==$v && ($plususer[$k]==2 || $plususer[$k]==1)){
					echo $pluscontent[$k];

				}
			}
		}
		if(($ii+1)==$ads['search']){
			include(ROOT_PATH.'/data/huoduan.ads_search.php');
		}
		$yurl = $list['data'][$ni]['blink'];
		$blink = trim($list['data'][$ni]['blink']);
		include(ROOT_PATH.'/inc/seturl.php');

		$gourl = qencode($list['data'][$ni]['link']);
		$gotitle = qencode(strip_tags($list['data'][$ni]['title']));
		$gokey = qencode($q);

		 if($huoduan['link_open']==0){
			 $sourl = $list['data'][$ni]['link'];
		 }else{
			 $sourl = SYSPATH.'?a=url&k='.substr(a($gourl.$gotitle.$gokey),0,8).'&u='.$gourl.'&t='.$gotitle.'&s='.$gokey;
		 }

		if(substr($blink,0,8)=='https://'){
			$blink ='huoduan|'.$blink ;
			$blink = str_replace('huoduan|https://','',$blink);
		}
		if(substr($blink,0,7)=='http://'){
			$blink ='huoduan|'.$blink ;
			$blink = str_replace('huoduan|http://','',$blink);
		}


		if(strpos($blink,'&nbsp;')){
		   $blink = explode('&nbsp;',$blink);
		   $blink = $blink[0];
		}

		$blink = huoduan_msubstr($blink,0,50,true);
	   $kurl=0;
	   if(is_file(ROOT_PATH.'/data/huoduan.killurls.txt')){
		  $killurls = file_get_contents(ROOT_PATH.'/data/huoduan.killurls.txt');
		  if(strpos($killurls,"\r\n")>-1){
			$killurls = trim($killurls,"\r\n");
			$killurlslist = explode("\r\n",$killurls);
		  }else{
			   $killurls = trim($killurls,"\n");
			   $killurlslist = explode("\n",$killurls);
		  }

		  foreach($killurlslist as $k=>$v){

			  if(substr($v,0,1)=='|'){
				  $v = ltrim($v,'|');
				  if(clear_url($yurl) == clear_url($v)){
					  $kurl=1;
					  break;
				  }
			  }else if(strlen($v)>2){
				  if(strpos(clear_url($yurl),clear_url($v))>-1 || clear_url($yurl) == clear_url($v)){
					  $kurl=1;
					  break;
				  }
			  }
		  }
	   }
	  if($kurl!=1 ){
		?>
	<div class="g"><h2>
	<?php if($huoduan['listnum']==1){?>
		<span class="nums">
		<?php echo $listnum[$ii]?></span>
		 <?php }?>
		 <a href="<?php echo $sourl?>" target="_blank" class="s" rel="nofollow">
		 <?php echo $list['data'][$ni]['title']?></h2>
		 <span class="a"><?php echo $blink?></span></a>
		 <div class="line-search"></div>
		 <div class="std"><?php echo $list['data'][$ni]['des']?></div>
		 </div>
		<?php
	  }
  }
  echo '</div>';
}else{
	if($kill==1){
		echo '<div id="result"><div style="padding:30px 10px; text-align:center; color:#F00; font-size:16px;">该关键词已被屏蔽，请更换关键词搜索</div></div>';
	}else{
	    echo '<div id="result"><div style="padding:30px 10px; text-align:center; color:#F00; font-size:16px;">对不起，没有找到相关内容！请更换关键词搜索，或刷新本页重试。</div></div>';
	}
}


?>
<div class="xiangguan">
	 <?php include(ROOT_PATH.'/data/huoduan.ads_search1.php'); ?>

	<?php
	if($huoduan['xg_open']==1 && $kill!=1){
		if(is_array($list['xgdata'])){
			$xgdata = $list['xgdata'];
		}else{
		 $xgdata = huoduan_get_baidu_xg($q,$huoduan['cachetime']);
		}
		if(is_array($xgdata)){
			echo '<ul class="ranklist">';

			foreach($xgdata as $k=>$v){
				 if(strlen($v)<100){
				echo '<li><a href="'.huoduansourl($v).'">'.$v.'</a></li>';
			 }
				if($k==8){break;}
			}
			echo '</ul></div>';
		}
	}

	?>
	 </div><!--相关搜索-->
<center>
 <ul class="pagination">
 <?php
 if(isset($list['count']) && $list['count']>10 && $kill!=1){
	 $pagecount = ceil($list['count']/10);
	 if($pagecount>51){$pagecount=51;}
	   if($pagecount>10){
		  if($p<8){
		    $ii=1;
		    $jj=11;
		  }else{
			$ii= $p-5;
			$jj = $p+5;

			if($jj>$pagecount){
				$jj=$pagecount;
			}
			if($jj-$ii<10){
				$ii = $jj-10;
			}
		  }
	  }else{
		  $ii=1;
		  $jj=$pagecount;
	  }
	   if($pagecount>0){
		   if($p>1){
				echo '<li><a href="'.huoduansourl($q,$p-1).'" title="上一页">上一页</a></li>';
		   }
		   for($i=$ii;$i<$jj;$i++){
			   if($i==$p){
				   echo '<li class="active"><a href="'.huoduansourl($q,$i).'" title="第'.$i.'页">'.$i.'</a></li>';
			   }else{
				   echo '<li><a href="'.huoduansourl($q,$i).'" title="第'.$i.'页">'.$i.'</a></li>';
			   }
			}
			if($p<($jj-1)){
				echo '<li><a href="'.huoduansourl($q,$p+1).'" title="下一页">下一页</a></li>';
			}

	   }

 }else{

   if($list['pnum']>0 && $list['pnum']<11){
	   $ii=1;
	   $jj=$list['pnum']+1;

   }else if($list['pnum']>10){

	  $jj=$list['pnum']+1;
	  $ii=$jj-10;
   }
   if($list['pnum']>0){
	   if($p>1){
			echo '<li><a href="'.huoduansourl($q,$p-1).'" title="上一页">上一页</a></li>';
		   }
	   for($i=$ii;$i<$jj;$i++){

			   if($i==$p){
				   echo '<li><a href="'.huoduansourl($q,$i).'" title="第'.$i.'页">'.$i.'</a></li>';
			   }else{
				   echo '<li><a href="'.huoduansourl($q,$i).'" title="第'.$i.'页">'.$i.'</a></li>';
			   }
		}
		if($list['pnext']==1){
			echo '<li><a href="'.huoduansourl($q,$p+1).'" title="下一页">下一页</a></li>';
		}

   }
}
   ?></ul>
	 <p>&copy; <a rel="nofollow" href="http://www.chainwon.com/">轻惋网络</a>丨鄂ICP备16000678号</p>
 </center>
</div><!--res-->
</div><!--main-->


<div style="display:none" id="footer"><?php echo $huoduan['foot']?></div>
<script charset="gbk" src="http://www.baidu.com/js/opensug.js"></script>
<!--<?php echo $list['from']?>-->
</body>
</html>
