<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="360_union_verify" content="95e8c7627cad8e1eca2cd4d639dcbf05">
    <meta name="HandheldFriendly" content="True" /> 
<?php include "./head.html";
echo "<title>$title - 关于我们</title>";
?>
<style>
.aboutchainwon{
    z-index:100!important;
    position:inherit;
	width:960px;
	margin-top:30px;
	margin-bottom:30px;
	background:#fff;
	border-radius:5px;
}
.aboutchainwon th,.aboutchainwon td{
width:33.333333333%;
text-align:center;
}
</style>
</head>
<body>
<center>
<div class="aboutchainwon">
<br/>
<h1>关于轻惋</h1>
<div class="fengexian"></div>
<p>轻惋不仅仅是一个名词，他代表着深刻的含义<br/>就像轻轻的叹惋着曾经所获得的成功…<br/>旗下域名：chainwon.com</p><br/>
<h1 id="showsectime"></h1>
<div class="fengexian"></div>
<p>建于：2016年4月29日<br/>五一前的最后一个星期五</p><br/>
<h1>轻惋导航</h1>
<div class="fengexian"></div>
<p>主页UI基于bootstrap<br/>代码由轻梦一人完成，完完全全一字一句码起来的</p><br/>
<h1>赞助榜</h1>
<div class="fengexian"></div>
<table class="table table-striped table-bordered table-condensed" style="width:350px;">
<tr><th>时间</th><th>捐助人</th><th>金额</th></tr>
<tr><td>2016-05-21</td><td>冷亦</td><td>66.66</td></tr>
</table>
<br/>
<h1>联系我</h1>
<div class="fengexian"></div><br/>
<h4>QQ群:482634342</h4>
<img src="./images/about/qqqun.png" width="300px">
<h1>支持我</h1>
<div class="fengexian"></div>
<p><a rel="nofollow" style="color:#ff95b8;" href="https://nexmoe.com/" target="_blank">https://nexmoe.com/</a></p><br/>
</div>
</center>
<script type="text/javascript">
function NewDate(str) { 
    str = str.split('-'); 
    var date = new Date(); 
    date.setUTCFullYear(str[0], str[1] - 1, str[2]); 
    date.setUTCHours(0, 0, 0, 0); 
    return date; 
} 
function showsectime() {
    var birthDay =NewDate("2016-4-29");
    var today=new Date();
    var timeold=today.getTime()-birthDay.getTime();
    
    var sectimeold=timeold/1000
    var secondsold=Math.floor(sectimeold);
    var msPerDay=24*60*60*1000;

    var e_daysold=timeold/msPerDay;
    var daysold=Math.floor(e_daysold);
    var e_hrsold=(daysold-e_daysold)*-24;
    var lifeday=daysold+1;
    document.getElementById("showsectime").innerHTML = "第"+lifeday+"天";
    setTimeout(showsectime, 1000);
}
showsectime();
</script>
</body>
<?php include "./footer.html";?>