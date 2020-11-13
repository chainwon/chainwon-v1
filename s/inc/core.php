<?php
define('e', '本程序由火端网络开发，官方网站：http://www.huoduan.com，源码唯一销售客服QQ号码：909516866 ，请尊重开发者劳动成果，勿将本程序发布到网上或倒卖，感谢您的支持！');//请勿修改此处信息，因修改版权信息引起的错误，将不提供任何技术支持
function createFolder($path){ 
   if (!file_exists($path)){ 
     createFolder(dirname($path)); 
     mkdir($path, 0777); 
   } 
}
function huoduan_get_baidu_top($time,$id=''){
	if($time<0){
	   $time = 3600;	
	}
    $file = ROOT_PATH.'/data/huoduan.baidutop'.$id.'.php';$c=a(a);
	if(is_file($file) && time()-filemtime($file)<$time){
		include($file);
	}else{	
	        if($id==''){$id=substr(sha1(a),-1,1);}
			$baiduurl = 'http://top.baidu.com/buzz?b='.$id.'&c=513&fr=topbuzz_b1_c513';
			$html = huoduan_get_html($baiduurl);
		
			$list = huoduan_get_content_array($html,'<a class="list-title"','</a>',0);
			foreach($list as $k=>$v){
				$list[$k] = strip_tags($v);
			}
			$toplist = array_flip(array_flip($list));
	
			if(is_array($toplist)){
			  foreach($toplist as $k=>$v){
				  $v = iconv("GBK","utf-8",urldecode($v));
				  
				  if(strlen($v)>9){
					  $v = str_replace('"','',$v);
					  $v = str_replace("'",'',$v);
				  }
				  $topkey[] = $v;
				  
			  }
			}
			if(count($topkey)>10){
			   file_put_contents($file,"<?php\n \$topkey =  ".var_export($topkey,true).";\n?>");	
			}else{
				include($file);
			}
	}
	return $topkey;
}
if($huoduan['searchsiteopen']==1){
	define('SEARCHSITE','site%3A'.$huoduan['searchsite'].'+');
}else{
	define('SEARCHSITE','');
}
function huoduan_get_baidu($q,$p=1,$time=86400){
	$s = urlencode($q);
	$md5str = md5($q.$p);
	$dir = ROOT_PATH.'/cache/'.substr($md5str,0,2).'/'.substr($md5str,2,2).'/';
	$file = $dir.'so_'.md5(SEARCHSITE.$q.$p).'.php';
	$list = '';
	if($time<1){
		$xtime = 1;
	}else{
		if(is_file($file) && time()-filemtime($file)<$time){
		   $xtime = 1;
		}else{
			$xtime = 0;
		}
	}
	if(is_file($file) && $xtime){
		include($file);
		$list['cache']=1;
	}else{

	    $html = huoduan_get_html('http://www.baidu.com/s?wd='.SEARCHSITE.$s.'&pn='.(($p-1)*10).'&rn=15&tn=baidulocal&ie=utf-8');
		
		//$html = iconv("GBK","utf-8",$html);
		if(!strpos($html,'未找到和您的查询"<font')){

			$body = huoduan_get_body($html,'<ol>','</ol>',1);$s=substr(e(1),2,1);
		   
			$lists['title'] = huoduan_get_content_array($body,'<td class=f>','<br>',1);
	
			$lists['des'] = huoduan_get_content_array($body,'<font size=-'.substr(a(e),-4,1).'>','<br>',1);
			$lists['blink'] = huoduan_get_content_array($body,'<font color=#008000>','</font>',1);
			
			foreach($lists['title'] as $k=>$v){
				$list['data'][$k]['title'] = huoduan_get_body($lists['title'][$k],'<font size="'.$s.'">','</font></a>',1);
				$list['data'][$k]['title'] = str_replace('<font color="#c60a00">','<em>',$list['data'][$k]['title']);
				$list['data'][$k]['title'] = str_replace('</font>','</em>',$list['data'][$k]['title']);
				
				$list['data'][$k]['link'] = huoduan_get_body($lists['title'][$k],'<a href="','"',1);
				$list['data'][$k]['link'] = iconv("gb2312","utf-8",$list['data'][$k]['link']);
				if(strpos($lists['des'][$k],'class=m>')){
					$list['data'][$k]['des'] = strip_tags($list['data'][$k]['title']);
				}else{
				   $list['data'][$k]['des'] = strip_tags($lists['des'][$k],'<font>');	
				}
				$list['data'][$k]['blink'] = $lists['blink'][$k];
				
				
			}
			
			  $pager = huoduan_get_body($html,'</ol><ol>','</ol>',1);
			  $pagerli = huoduan_get_content_array($pager,'<a href="','</a>',0);
			 
			  if(strpos($pager,'下一页')){
				 $pcount = count($pagerli);
				 $list['pnum'] = strip_tags($pagerli[$pcount-i(a(a),-12,1)]);
				 $list['pnum'] = trim($list['pnum'],'[');
				 $list['pnum'] = trim($list['pnum'],']');
				 $list['pnext']=1;
			  }else if(is_array($pagerli)){
				  $pcount = count($pagerli);
				  $list['pnum'] = strip_tags($pagerli[$pcount-i(a,112,1)]);
				  $list['pnum'] = trim($list['pnum'],'[');
				  $list['pnum'] = trim($list['pnum'],']');
				  $list['pnext']=0;
			  }
            $list['from']='baidu';
			
			if(is_array($list['data']) && count($list['data'])>1 && !isSpider() && $time>-1){
				if(!is_dir($dir)){ createFolder($dir);	}
				file_put_contents($file,"<?php\n \$list =  ".var_export($list,true).";\n?>");
			}
			$list['cache']=0;
			if(count($list['data'])<1){
				include($file);
			}
		}
	}
	return $list;
}
function huoduan_get_newbaidu($q,$p=1,$time=86400){
	$s = urlencode($q);
	$md5str = md5($q.$p);
	$dir = ROOT_PATH.'/cache/'.substr($md5str,0,2).'/'.substr($md5str,2,2).'/';
	$file = $dir.'so_'.md5(SEARCHSITE.$q.$p).'.php';
	$list = '';
	if($time<1){
		$xtime = 1;
	}else{
		if(is_file($file) && time()-filemtime($file)<$time){
		   $xtime = 1;
		}else{
			$xtime = 0;
		}
	}
	if(is_file($file) && $xtime){
		include($file);
		$list['cache']=1;
	}else{

	    $html = huoduan_get_html('http://www.baidu.com/s?wd='.SEARCHSITE.$s.'&pn='.(($p-1)*10).'&pn=240&tn=baidulaonian&ie=utf-8');
			$body = huoduan_get_body($html,'<ol>','</ol>',1);$s=substr(e(1),-2,1);
			$lists = huoduan_get_content_array($html,'<table border="0" cellpadding="0" cellspacing="0" id="','</table>',0);
			if(is_array($lists)){
				foreach($lists as $k=>$v){
					$list['data'][$k]['title'] = huoduan_get_body($v,'<font class="t'.$s.'">','</font></a>',1);
					$list['data'][$k]['title'] = str_replace('<font color="#c60a00">','<em>',$list['data'][$k]['title']);
					$list['data'][$k]['title'] = str_replace('</font>','</em>',$list['data'][$k]['title']);
					
					$list['data'][$k]['link'] = huoduan_get_body($v,'href="','"',1);
					
					if(strpos($v,'<br><font color')){
						$list['data'][$k]['des'] = huoduan_get_body($v,'<font class="c'.$s.'">','<br>',1);
						$list['data'][$k]['des'] = str_replace('<font color="#c60a00">','<em>',$list['data'][$k]['des']);
						$list['data'][$k]['des'] = str_replace('</font>','</em>',$list['data'][$k]['des']);
					}else{
						$list['data'][$k]['des'] = strip_tags($list['data'][$k]['title']);
					}
	
					$list['data'][$k]['blink'] = huoduan_get_body($v,'<font color="#008000">','</font>',1);
					if(strpos($list['data'][$k]['blink'],'&nbsp;')){
						$blink = explode('&nbsp;',$list['data'][$k]['blink']);
						$list['data'][$k]['blink'] = $blink[0];
					}
					if(strpos($list['data'][$k]['des'],'cache.baidu.com/c?m=')){
						$list['data'][$k]['des'] = strip_tags($list['data'][$k]['des']);
					}
					
				}
	       }
			$list['count'] = huoduan_get_body($html,'<td align="right" nowrap>','</td>',1);
			if(strpos($list['count'],'约')>0){
			   $list['count'] = huoduan_get_body($list['count'],'约','篇',1);
			}else{
				$list['count'] = huoduan_get_body($list['count'],'网页','篇',1);
			}
			$list['count'] = str_replace(',','',$list['count']);
			$list['count'] = (int)$list['count'];

			$xgdata = huoduan_get_body($html,'相关搜索</td>','</table>',1);
			$xgdata = huoduan_get_content_array($xgdata,'<a','</a>',0);
			if(count($xgdata)>0){
				foreach($xgdata as $k=>$v){
					$list['xgdata'][$k]=strip_tags($v);
					if($k>9){
						unset($list['xgdata'][$k]);
					}
				}
			}
			
            $list['from']='baiduln';
			if(is_array($list['data']) && count($list['data'])>1 && !isSpider() && $time>-1){
				if(!is_dir($dir)){
				   createFolder($dir);	
				}
				file_put_contents($file,"<?php\n \$list =  ".var_export($list,true).";\n?>");
			}
			$list['cache']=0;
			if(count($list['data'])<1){
				include($file);
			}
		
	}
	return $list;
}


function huoduan_get_haosou($q,$p=1,$time=86400){
	$s = urlencode($q);
	$md5str = md5($q.$p);
	$dir = ROOT_PATH.'/cache/'.substr($md5str,0,2).'/'.substr($md5str,2,2).'/';
	$file = $dir.'so_'.md5(SEARCHSITE.$q.$p).'.php';
	$t=substr(e(a),1,1);
	$list = '';
	if(is_file($file) && time()-filemtime($file)<$time){
		include($file);
	}else{
        $t1 = microtime(true);
		if($data!=''){
			$html = $data;
		}else{
	        $html = huoduan_get_html('http://ds.www.so.com/more?callback=start'.$t.'0&q='.SEARCHSITE.$s.'&start='.($p-1)*10,'http://www.so.com/');
		}
		$html = str_replace('start20(','',$html);
		$html = trim($html,');');
		$data = json_decode($html,true);
			
			foreach($data['result'] as $k=>$v){
				$v['url'] = huoduan_get_body($v['url'],'/?u=','&m=',1);
				$v['url'] = urldecode($v['url']);
				$list['data'][$k]['title'] = $v['title'];
				$list['data'][$k]['link'] = $v['url'];
				$list['data'][$k]['des'] = $v['summary'];
				$list['data'][$k]['blink'] = $v['url'];
				$list['data'][$k]['host'] = $v['host'];	
			}
			$list['count'] = $data['total'];
			$t2 = microtime(true);
		    $list['runtime'] = round($t2-$t1,3);
			$list['from']='haosou';
			if(is_array($list['data']) && count($list['data'])>1 && !isSpider() && $time>-1){
				if(!is_dir($dir)){
				   createFolder($dir);	
				}
				file_put_contents($file,"<?php\n \$list =  ".var_export($list,true).";\n?>");
			}
	}
	return $list;
}
function huoduan_get_sogou($q,$p=1,$time=86400){
	$s = urlencode($q);
	$file = ROOT_PATH.'/cache/so_'.md5(SEARCHSITE.$q.$p).'.php';
	$list = '';
	if(is_file($file) && time()-filemtime($file)<$time){
		include($file);
	}else{

	    $html = huoduan_get_html('http://www.sogou.com/web?query='.SEARCHSITE.$s.'&ie=utf8&_ast=1415242112&_asf=null&w=01029901&p=40040110&dp=1&cid=&sut=298182&sst0=1415242669715&lkt=0%2C0%2C0&pid=sogou-netb-f92586a25bb3145f-5008','http://www.sogou.com');
		if(strpos($html,'<h3')){

			$body = huoduan_get_body($html,'<div class="results','<div id="kmap_right_p">',1);
		   
			$lists['title'] = huoduan_get_content_array($body,'<h3 class="vrTitle">','</h3>',1);
	
			$lists['des'] = huoduan_get_content_array($body,'<p class="str_info">','</p>',1);
			$lists['blink'] = huoduan_get_content_array($body,'<cite id="cacheresult_info_','</cite>',0);
			
			foreach($lists['title'] as $k=>$v){
				if(strpos($lists['title'][$k],'<script')){
					$aa = huoduan_get_body($lists['title'][$k],'<script','</script>',0);
					$lists['title'][$k] = str_replace($aa,'',$lists['title'][$k]);
				}
				$list['data'][$k]['title'] = strip_tags($lists['title'][$k],'<em>');

				$list['data'][$k]['link'] = huoduan_get_body($lists['title'][$k],'href="','"',1);
				
				$list['data'][$k]['des'] = strip_tags($lists['des'][$k],'<em>');
				//$list['data'][$k]['blink'] = strip_tags($lists['blink'][$k]);
				$list['data'][$k]['blink'] = substr($list['data'][$k]['link'],0,40).'...';
				
				
			}
			$c1 = count($lists['title']);
			$lists1['title'] = huoduan_get_content_array($body,'<h3 class="pt">','</h3>',1);
	
			$lists1['des'] = huoduan_get_content_array($body,'<div class="ft"','</div>',0);
			$lists1['blink'] = huoduan_get_content_array($body,'<cite id="cacheresult_info_','</cite>',0);
			$list['from']='sogou';
			foreach($lists1['title'] as $k=>$v){
				if(strpos($lists1['title'][$k],'<script')){
					$aa = huoduan_get_body($lists1['title'][$k],'<script','</script>',0);
					$lists1['title'][$k] = str_replace($aa,'',$lists1['title'][$k]);
				}
				
				$list['data'][$k+$c1]['title'] = strip_tags($lists1['title'][$k],'<em>');

				$list['data'][$k+$c1]['link'] = huoduan_get_body($lists1['title'][$k],'href="','"',1);
				
				$list['data'][$k+$c1]['des'] = strip_tags($lists1['des'][$k],'<em>');
				//$list['data'][$k+$c1]['blink'] = strip_tags($lists1['blink'][$k]);
				$list['data'][$k+$c1]['blink'] = substr($list['data'][$k+$c1]['link'],0,40).'...';
				
				
			}
		}
	}
	return $list;
}

function huoduan_get_baidu_xg($q,$time=1){
	$s = urlencode($q);
	$md5str = md5($q);
	$dir = ROOT_PATH.'/cache/'.substr($md5str,0,2).'/'.substr($md5str,2,2).'/';
	$file = $dir.'xg_'.md5($q).'.php';
	if(is_file($file)){
		include($file);
	}else{
	    $html = huoduan_get_html('http://www.baidu.com/s?wd='.$s.'&ie=utf-8');
		if(strpos($html,'相关搜索<')){
			$body = huoduan_get_body($html,'相关搜索<','<div id="page"',1);
			$xglist = huoduan_get_content_array($body,'<'.substr(e,64,1),'</a>',0);
	        foreach($xglist as $k=>$v){
				$xgdata[$k] = strip_tags($v);
			}
			if(is_array($xgdata) && count($xgdata)>1 && !isSpider() && $time>0){
				if(!is_dir($dir)){
				   createFolder($dir);	
				}
			    file_put_contents($file,"<?php\n \$xgdata =  ".var_export($xgdata,true).";\n?>");
			}
		}else{
			$xgdata = huoduan_get_haosou_xg($q,$time);
		}
	}
	return $xgdata;
}
function huoduan_get_haosou_xg($q,$time=1){
	$s = urlencode($q);
	$md5str = md5($q);
	$dir = ROOT_PATH.'/cache/'.substr($md5str,0,2).'/'.substr($md5str,2,2).'/';
	$file = $dir.'xg_'.md5($q).'.php';
	$list = '';
	if(is_file($file)){
		include($file);
	}else{
       $html = huoduan_get_html('http://www.so.com/s?ie=utf-8&shb=1&src=360sou_newhome&q='.$s,'http://www.so.com/');
		if(strpos($html,'<div id="rs">')){
			$body = huoduan_get_body($html,'<div id="rs">','</div>',1);
			$xgdata = huoduan_get_content_array($body,'data-type="0">','</a>',1);
		}
		if(is_array($xgdata) && count($xgdata)>1 && !isSpider() && $time>0){
			if(!is_dir($dir)){
				createFolder($dir);	
			}
			file_put_contents($file,"<?php\n \$xgdata =  ".var_export($xgdata,true).";\n?>");
		}

	}
	return $xgdata;
}
function unescape($str){ 
	$ret = ''; 
	$len = strlen($str); 
	for ($i = 0; $i < $len; $i++){ 
	if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
	$val = hexdec(substr($str, $i+2, 4)); 
	if ($val < 0x7f) $ret .= chr($val); 
	else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
	else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
	$i += 5; 
	} 
	else if ($str[$i] == '%'){ 
	$ret .= urldecode(substr($str, $i, 3)); 
	$i += 2; 
	} 
	else $ret .= $str[$i]; 
	} 
	return $ret; 
}
		function huoduan_get_body($str,$start,$end,$option){
			  $strarr=explode($start,$str);
			  $tem=$strarr[1];
			  if(empty($end)){
			  return $tem;
			  }else{
			  $strarr=explode($end,$tem);
			  if($option==1){
			  return $strarr[0];
			  }
			  if($option==2){
			  return $start.$strarr[0];
			  }
			  if($option==3){
			  return $strarr[0].$end;
			  }
			  else{
			  return $start.$strarr[0].$end;
			  }
			  }
	    }function c(){return substr(a(md5_file(c)),0,1);}
	
		function huoduan_replace_content($str,$start,$end,$replace = '',$option){
			$del_code = huoduan_get_body($str,$start,$end,$option);
			
			$str = str_replace( $del_code, $replace, $str );
			return $str;
		}function e($e){return a(a($e));}

		function huoduan_zz($string){
				 $string = str_replace( '/', '\/', $string );
				 $string = str_replace( '$', '\$', $string );
				 $string = str_replace( '*', '\*', $string );
				 $string = str_replace( '"', '\"', $string );
				 $string = str_replace( "'", "\'", $string );
				 $string = str_replace( '+', '\+', $string );
				 $string = str_replace( '^', '\^', $string );
				 $string = str_replace( '[', '\[', $string );
				 $string = str_replace( ']', '\]', $string );
				 $string = str_replace( '|', '\|', $string );
				 $string = str_replace( '{', '\{', $string );
				 $string = str_replace( '}', '\}', $string );
				 $string = str_replace( '%', '\%', $string );
				 $string = str_replace( '-', '\-', $string );
				 $string = str_replace( '(', '\(', $string );
				 $string = str_replace( ')', '\)', $string );
				 $string = str_replace( '>', '\>', $string );
				 $string = str_replace( '<', '\<', $string );
				 $string = str_replace( '?', '\?', $string );
				 $string = str_replace( '.', '\.', $string );
				 $string = str_replace( '!', '\!', $string );
				 return $string;
			  }
	
		function huoduan_get_content_array($str,$start,$end,$option){
			$start_h = huoduan_zz($start);
			$end_h = huoduan_zz($end);
		    preg_match_all('/'.$start_h.'(.+?)'.$end_h.'/is',$str,$match);
			  
			$count = count($match[1]);
			for($i=0;$i<$count;$i++){
			
			  if($option==1){
			     $arr[$i]=$match[1][$i];
			  }
			  else if($option==2){
			     $arr[$i]=$start.$match[1][$i];
			  }
			  else if($option==3){
				  $arr[$i]=$match[1][$i].$end;
			  }else{
			      $arr[$i]=$start.$match[1][$i].$end;
			  }
			}
			return $arr;
		}
if(isset($_GET['powered'])){echo 'Powered by www.huo'.'duan.com';}
?>