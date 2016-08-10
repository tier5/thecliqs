<?
include("html.php");

admin();
secureheader();
siteheader();
//5.00= 30 days status=2592000
//10.00= 30 days status
//20.00= 60 days status=5184000

/*<font color="white">Linux Server Time<BR>
Current Time: <?=$time?><BR>
Time + 24 hours: <?=$time+86400?><BR>
Time + 5 days: <?=$time+432000?><BR>
Time + 10 days: <?=$time+864000?><BR>
Time + 15 days: <?=$time+1296000?><BR>
Time + 20 days: <?=$time+1728000?><BR>
Time + 25 days: <?=$time+2160000?><BR>
Time + 30 days: <?=$time+2592000?><BR>
Time + 60 days: <?=$time+5184000?><BR>
Time + 90 days: <?=$time+7776000?><BR>
<br><br>
</font>

::::::::: End Current Times :::::::::
*/


$current = time();
$datestamp = date("m/d/Y G:i:s");

$month = "00";
if(isset($_REQUEST["month"])) {
	$month = $_REQUEST["month"];
}
$day = "00";
if(isset($_REQUEST["day"])){
	$day = $_REQUEST["day"];
}
$year = "0000";
if(isset($_REQUEST["year"])) {
	$year = $_REQUEST["year"];
}
$hour = "";
if(isset($_REQUEST["hour"])) {
	$hour = $_REQUEST["hour"];
}
$min = "";
if(isset($_REQUEST["min"])) {
	$min = $_REQUEST["min"];
}
$sec = "";
if(isset($_REQUEST["sec"])){
	$sec = $_REQUEST["sec"];
}
$output = "";
if($month != "00" && $day != "00" && $year != "0000" && $hour != "" && $min != "" && $sec != "") {
	$output = strtotime($year . "-" . $month . "-" . $day . " " . $hour . ":" . $min . ":" . $sec);
}

if(isset($_REQUEST["unix_time"])) {
	$unix_time = $_REQUEST["unix_time"];
	
	$month = date("m",$unix_time);
	$day = date("d",$unix_time);
	$year = date("Y",$unix_time);
	$hour = date("G",$unix_time);
	$min = date("i",$unix_time);
	$sec = date("s",$unix_time);
}

?>
<html>
<head>
<title>Unix Time Conversion Tool</title>
<style>
   body {
      font-family: Tahoma, Verdana, Arial;
   }
   input {
	  font-size:10px;
   }
</style>
<script language="JavaScript">
function Submit(){
	var frm = document.form1;
	if((frm.month.value == '' || frm.day.value == '' || frm.year.value == '' || frm.hour.value == '' || frm.min.value == '' || frm.sec.value == '') && frm.unix_time.value == '') {
		alert('You have not specified a proper date to convert.');
		return false;
	}
	frm.submit();
}
</script>
</head>
<body>
<div align="center">
<div style="width:400px;">
<b>Current Time is <?=$datestamp?> ( <?=$current?> )</b>
<p>
<form action="<?=$_SERVER["SCRIPT_NAME"]?>" method="POST" name="form1">
<select name="month" size="1">
   <option value=""></option>
   <option value="01" <?if($month=="01")printf("selected");?>>January</option>
   <option value="02" <?if($month=="02")printf("selected");?>>February</option>
   <option value="03" <?if($month=="03")printf("selected");?>>March</option>
   <option value="04" <?if($month=="04")printf("selected");?>>April</option>
   <option value="05" <?if($month=="05")printf("selected");?>>May</option>
   <option value="06" <?if($month=="06")printf("selected");?>>June</option>
   <option value="07" <?if($month=="07")printf("selected");?>>July</option>
   <option value="08" <?if($month=="08")printf("selected");?>>August</option>
   <option value="09" <?if($month=="09")printf("selected");?>>September</option>
   <option value="10" <?if($month=="10")printf("selected");?>>October</option>
   <option value="11" <?if($month=="11")printf("selected");?>>November</option>
   <option value="12" <?if($month=="12")printf("selected");?>>December</option>
</select>
/
<select name="day" size="1">
   <option value=""></option>
   <option value="01" <?if($day=="01")printf("selected");?>>01</option>
   <option value="02" <?if($day=="02")printf("selected");?>>02</option>
   <option value="03" <?if($day=="03")printf("selected");?>>03</option>
   <option value="04" <?if($day=="04")printf("selected");?>>04</option>
   <option value="05" <?if($day=="05")printf("selected");?>>05</option>
   <option value="06" <?if($day=="06")printf("selected");?>>06</option>
   <option value="07" <?if($day=="07")printf("selected");?>>07</option>
   <option value="08" <?if($day=="08")printf("selected");?>>08</option>
   <option value="09" <?if($day=="09")printf("selected");?>>09</option>
   <option value="10" <?if($day=="10")printf("selected");?>>10</option>
   <option value="11" <?if($day=="11")printf("selected");?>>11</option>
   <option value="12" <?if($day=="12")printf("selected");?>>12</option>
   <option value="13" <?if($day=="13")printf("selected");?>>13</option>
   <option value="14" <?if($day=="14")printf("selected");?>>14</option>
   <option value="15" <?if($day=="15")printf("selected");?>>15</option>
   <option value="16" <?if($day=="16")printf("selected");?>>16</option>
   <option value="17" <?if($day=="17")printf("selected");?>>17</option>
   <option value="18" <?if($day=="18")printf("selected");?>>18</option>
   <option value="19" <?if($day=="19")printf("selected");?>>19</option>
   <option value="20" <?if($day=="20")printf("selected");?>>20</option>
   <option value="21" <?if($day=="21")printf("selected");?>>21</option>
   <option value="22" <?if($day=="22")printf("selected");?>>22</option>
   <option value="23" <?if($day=="23")printf("selected");?>>23</option>
   <option value="24" <?if($day=="24")printf("selected");?>>24</option>
   <option value="25" <?if($day=="25")printf("selected");?>>25</option>
   <option value="26" <?if($day=="26")printf("selected");?>>26</option>
   <option value="27" <?if($day=="27")printf("selected");?>>27</option>
   <option value="28" <?if($day=="28")printf("selected");?>>28</option>
   <option value="29" <?if($day=="29")printf("selected");?>>29</option>
   <option value="30" <?if($day=="30")printf("selected");?>>30</option>
   <option value="31" <?if($day=="31")printf("selected");?>>31</option>
</select>
/
<select name="year" size="1">
   <option value=""></option>
   <option value="2007" <?if($year=="2007")printf("selected");?>>2007</option>
   <option value="2008" <?if($year=="2008")printf("selected");?>>2008</option>
   <option value="2009" <?if($year=="2009")printf("selected");?>>2009</option>
   <option value="2010" <?if($year=="2010")printf("selected");?>>2010</option>
   <option value="2011" <?if($year=="2011")printf("selected");?>>2011</option>
   <option value="2012" <?if($year=="2012")printf("selected");?>>2012</option>
   <option value="2013" <?if($year=="2013")printf("selected");?>>2013</option>
   <option value="2014" <?if($year=="2014")printf("selected");?>>2014</option>
   <option value="2015" <?if($year=="2015")printf("selected");?>>2015</option>
   <option value="2016" <?if($year=="2016")printf("selected");?>>2016</option>
   <option value="2017" <?if($year=="2017")printf("selected");?>>2017</option>
   <option value="2018" <?if($year=="2018")printf("selected");?>>2018</option>
   <option value="2019" <?if($year=="2019")printf("selected");?>>2019</option>
   <option value="2020" <?if($year=="2020")printf("selected");?>>2020</option>
   <option value="2021" <?if($year=="2021")printf("selected");?>>2021</option>
   <option value="2022" <?if($year=="2022")printf("selected");?>>2022</option>
   <option value="2023" <?if($year=="2023")printf("selected");?>>2023</option>
   <option value="2024" <?if($year=="2024")printf("selected");?>>2024</option>
   <option value="2025" <?if($year=="2025")printf("selected");?>>2025</option>   
</select>
&nbsp;&nbsp;
<select name="hour" size="1">
   <option value=""></option>
   <option value="00" <?if($hour=="00")printf("selected");?>>00</option>
   <option value="01" <?if($hour=="01")printf("selected");?>>01</option>
   <option value="02" <?if($hour=="02")printf("selected");?>>02</option>
   <option value="03" <?if($hour=="03")printf("selected");?>>03</option>
   <option value="04" <?if($hour=="04")printf("selected");?>>04</option>
   <option value="05" <?if($hour=="05")printf("selected");?>>05</option>
   <option value="06" <?if($hour=="06")printf("selected");?>>06</option>
   <option value="07" <?if($hour=="07")printf("selected");?>>07</option>
   <option value="08" <?if($hour=="08")printf("selected");?>>08</option>
   <option value="09" <?if($hour=="09")printf("selected");?>>09</option>
   <option value="10" <?if($hour=="10")printf("selected");?>>10</option>
   <option value="11" <?if($hour=="11")printf("selected");?>>11</option>
   <option value="12" <?if($hour=="12")printf("selected");?>>12</option>
   <option value="13" <?if($hour=="13")printf("selected");?>>13</option>
   <option value="14" <?if($hour=="14")printf("selected");?>>14</option>
   <option value="15" <?if($hour=="15")printf("selected");?>>15</option>
   <option value="16" <?if($hour=="16")printf("selected");?>>16</option>
   <option value="17" <?if($hour=="17")printf("selected");?>>17</option>
   <option value="18" <?if($hour=="18")printf("selected");?>>18</option>
   <option value="19" <?if($hour=="19")printf("selected");?>>19</option>
   <option value="20" <?if($hour=="20")printf("selected");?>>20</option>
   <option value="21" <?if($hour=="21")printf("selected");?>>21</option>
   <option value="22" <?if($hour=="22")printf("selected");?>>22</option>
   <option value="23" <?if($hour=="23")printf("selected");?>>23</option>
</select>
:
<select name="min" size="1">
   <option value=""></option>
   <option value="00" <?if($min=="00")printf("selected");?>>00</option>
   <option value="01" <?if($min=="01")printf("selected");?>>01</option>
   <option value="02" <?if($min=="02")printf("selected");?>>02</option>
   <option value="03" <?if($min=="03")printf("selected");?>>03</option>
   <option value="04" <?if($min=="04")printf("selected");?>>04</option>
   <option value="05" <?if($min=="05")printf("selected");?>>05</option>
   <option value="06" <?if($min=="06")printf("selected");?>>06</option>
   <option value="07" <?if($min=="07")printf("selected");?>>07</option>
   <option value="08" <?if($min=="08")printf("selected");?>>08</option>
   <option value="09" <?if($min=="09")printf("selected");?>>09</option>
   <option value="10" <?if($min=="10")printf("selected");?>>10</option>
   <option value="11" <?if($min=="11")printf("selected");?>>11</option>
   <option value="12" <?if($min=="12")printf("selected");?>>12</option>
   <option value="13" <?if($min=="13")printf("selected");?>>13</option>
   <option value="14" <?if($min=="14")printf("selected");?>>14</option>
   <option value="15" <?if($min=="15")printf("selected");?>>15</option>
   <option value="16" <?if($min=="16")printf("selected");?>>16</option>
   <option value="17" <?if($min=="17")printf("selected");?>>17</option>
   <option value="18" <?if($min=="18")printf("selected");?>>18</option>
   <option value="19" <?if($min=="19")printf("selected");?>>19</option>
   <option value="20" <?if($min=="20")printf("selected");?>>20</option>
   <option value="21" <?if($min=="21")printf("selected");?>>21</option>
   <option value="22" <?if($min=="22")printf("selected");?>>22</option>
   <option value="23" <?if($min=="23")printf("selected");?>>23</option>
   <option value="24" <?if($min=="24")printf("selected");?>>24</option>
   <option value="25" <?if($min=="25")printf("selected");?>>25</option>
   <option value="26" <?if($min=="26")printf("selected");?>>26</option>
   <option value="27" <?if($min=="27")printf("selected");?>>27</option>
   <option value="28" <?if($min=="28")printf("selected");?>>28</option>
   <option value="29" <?if($min=="29")printf("selected");?>>29</option>
   <option value="30" <?if($min=="30")printf("selected");?>>30</option>
   <option value="31" <?if($min=="31")printf("selected");?>>31</option>
   <option value="32" <?if($min=="32")printf("selected");?>>32</option>
   <option value="33" <?if($min=="33")printf("selected");?>>33</option>
   <option value="34" <?if($min=="34")printf("selected");?>>34</option>
   <option value="35" <?if($min=="35")printf("selected");?>>35</option>
   <option value="36" <?if($min=="36")printf("selected");?>>36</option>
   <option value="37" <?if($min=="37")printf("selected");?>>37</option>
   <option value="38" <?if($min=="38")printf("selected");?>>38</option>
   <option value="39" <?if($min=="39")printf("selected");?>>39</option>
   <option value="40" <?if($min=="40")printf("selected");?>>40</option>
   <option value="41" <?if($min=="41")printf("selected");?>>41</option>
   <option value="42" <?if($min=="42")printf("selected");?>>42</option>
   <option value="43" <?if($min=="43")printf("selected");?>>43</option>
   <option value="44" <?if($min=="44")printf("selected");?>>44</option>
   <option value="45" <?if($min=="45")printf("selected");?>>45</option>
   <option value="46" <?if($min=="46")printf("selected");?>>46</option>
   <option value="47" <?if($min=="47")printf("selected");?>>47</option>
   <option value="48" <?if($min=="48")printf("selected");?>>48</option>
   <option value="49" <?if($min=="49")printf("selected");?>>49</option>
   <option value="50" <?if($min=="50")printf("selected");?>>50</option>
   <option value="51" <?if($min=="51")printf("selected");?>>51</option>
   <option value="52" <?if($min=="52")printf("selected");?>>52</option>
   <option value="53" <?if($min=="53")printf("selected");?>>53</option>
   <option value="54" <?if($min=="54")printf("selected");?>>54</option>
   <option value="55" <?if($min=="55")printf("selected");?>>55</option>
   <option value="56" <?if($min=="56")printf("selected");?>>56</option>
   <option value="57" <?if($min=="57")printf("selected");?>>57</option>
   <option value="58" <?if($min=="58")printf("selected");?>>58</option>
   <option value="59" <?if($min=="59")printf("selected");?>>59</option>
</select>
:
<select name="sec" size="1">
   <option value=""></option>
   <option value="00" <?if($sec=="00")printf("selected");?>>00</option>
   <option value="01" <?if($sec=="01")printf("selected");?>>01</option>
   <option value="02" <?if($sec=="02")printf("selected");?>>02</option>
   <option value="03" <?if($sec=="03")printf("selected");?>>03</option>
   <option value="04" <?if($sec=="04")printf("selected");?>>04</option>
   <option value="05" <?if($sec=="05")printf("selected");?>>05</option>
   <option value="06" <?if($sec=="06")printf("selected");?>>06</option>
   <option value="07" <?if($sec=="07")printf("selected");?>>07</option>
   <option value="08" <?if($sec=="08")printf("selected");?>>08</option>
   <option value="09" <?if($sec=="09")printf("selected");?>>09</option>
   <option value="10" <?if($sec=="10")printf("selected");?>>10</option>
   <option value="11" <?if($sec=="11")printf("selected");?>>11</option>
   <option value="12" <?if($sec=="12")printf("selected");?>>12</option>
   <option value="13" <?if($sec=="13")printf("selected");?>>13</option>
   <option value="14" <?if($sec=="14")printf("selected");?>>14</option>
   <option value="15" <?if($sec=="15")printf("selected");?>>15</option>
   <option value="16" <?if($sec=="16")printf("selected");?>>16</option>
   <option value="17" <?if($sec=="17")printf("selected");?>>17</option>
   <option value="18" <?if($sec=="18")printf("selected");?>>18</option>
   <option value="19" <?if($sec=="19")printf("selected");?>>19</option>
   <option value="20" <?if($sec=="20")printf("selected");?>>20</option>
   <option value="21" <?if($sec=="21")printf("selected");?>>21</option>
   <option value="22" <?if($sec=="22")printf("selected");?>>22</option>
   <option value="23" <?if($sec=="23")printf("selected");?>>23</option>
   <option value="24" <?if($sec=="24")printf("selected");?>>24</option>
   <option value="25" <?if($sec=="25")printf("selected");?>>25</option>
   <option value="26" <?if($sec=="26")printf("selected");?>>26</option>
   <option value="27" <?if($sec=="27")printf("selected");?>>27</option>
   <option value="28" <?if($sec=="28")printf("selected");?>>28</option>
   <option value="29" <?if($sec=="29")printf("selected");?>>29</option>
   <option value="30" <?if($sec=="30")printf("selected");?>>30</option>
   <option value="31" <?if($sec=="31")printf("selected");?>>31</option>
   <option value="32" <?if($sec=="32")printf("selected");?>>32</option>
   <option value="33" <?if($sec=="33")printf("selected");?>>33</option>
   <option value="34" <?if($sec=="34")printf("selected");?>>34</option>
   <option value="35" <?if($sec=="35")printf("selected");?>>35</option>
   <option value="36" <?if($sec=="36")printf("selected");?>>36</option>
   <option value="37" <?if($sec=="37")printf("selected");?>>37</option>
   <option value="38" <?if($sec=="38")printf("selected");?>>38</option>
   <option value="39" <?if($sec=="39")printf("selected");?>>39</option>
   <option value="40" <?if($sec=="40")printf("selected");?>>40</option>
   <option value="41" <?if($sec=="41")printf("selected");?>>41</option>
   <option value="42" <?if($sec=="42")printf("selected");?>>42</option>
   <option value="43" <?if($sec=="43")printf("selected");?>>43</option>
   <option value="44" <?if($sec=="44")printf("selected");?>>44</option>
   <option value="45" <?if($sec=="45")printf("selected");?>>45</option>
   <option value="46" <?if($sec=="46")printf("selected");?>>46</option>
   <option value="47" <?if($sec=="47")printf("selected");?>>47</option>
   <option value="48" <?if($sec=="48")printf("selected");?>>48</option>
   <option value="49" <?if($sec=="49")printf("selected");?>>49</option>
   <option value="50" <?if($sec=="50")printf("selected");?>>50</option>
   <option value="51" <?if($sec=="51")printf("selected");?>>51</option>
   <option value="52" <?if($sec=="52")printf("selected");?>>52</option>
   <option value="53" <?if($sec=="53")printf("selected");?>>53</option>
   <option value="54" <?if($sec=="54")printf("selected");?>>54</option>
   <option value="55" <?if($sec=="55")printf("selected");?>>55</option>
   <option value="56" <?if($sec=="56")printf("selected");?>>56</option>
   <option value="57" <?if($sec=="57")printf("selected");?>>57</option>
   <option value="58" <?if($sec=="58")printf("selected");?>>58</option>
   <option value="59" <?if($sec=="59")printf("selected");?>>59</option>
</select>
<p>
<span style="font-size:10px;padding-left:100px;">Or, convert unix time to standard date format: </span><br>
<span style="padding-left:75px;"><input type="text" size="50" name="unix_time"></span>
<p>
</form>
<span style="width:450px;padding-left:200px;"><input type="button" value="Convert" onClick="Submit();"></span>
<? if ($output != "") {
	printf("<p><span style=\"color:red;font-weight:900;\">%s</span>",$output);
}
?>
</div>
<input type="hidden" name="t_month" value="<?=$month?>">
</div></body>
</html>
<?
sitefooter();
?>