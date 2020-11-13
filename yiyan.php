<script language="JavaScript">
function myrefresh()
{
       window.location.reload();
}
setTimeout('myrefresh()',40000);
</script>
<?php
$html=file_get_contents("http://v1.hitokoto.cn/");
$json=json_decode($html);
echo '<center><a rel="nofollow" href="https://hitokoto.cn/?uuid='.$json->uuid.'" style="text-decoration:none" target="_blank"><p style="font-size:20px;color:#fff;text-shadow: 0 0 5px #fff, 0 0 5px #fff, 0 0 10px #ff95b8, 0 0 10px #ff95b8;">『'.$json->hitokoto.'』</p></a></center>';
?>
<div style="display:none"><script src="http://s11.cnzz.com/z_stat.php?id=1258175240&web_id=1258175240" language="JavaScript"></script></div>
