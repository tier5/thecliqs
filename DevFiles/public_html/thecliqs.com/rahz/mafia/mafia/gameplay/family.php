<?
include("html.php");
$dacrewmax = mysql_fetch_array(mysql_query("SELECT crewmax FROM $tab[game] WHERE round='$tru';"));
if (($menu==join) && (fetch("SELECT id FROM $tab[invite] WHERE crew='$cid' AND cancelled='no' AND pimp='$id';")))
{
$crewmax = mysql_fetch_array(mysql_query("SELECT members FROM $tab[crew] WHERE id='$cid';"));
if (($join==yes) && ($crewmax[0] != $dacrewmax[0]))
{
$bpimp = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
mysql_query("UPDATE $tab[invite] SET cancelled='yes' WHERE pimp='$id' AND crew='$cid'");
mysql_query("UPDATE $tab[pimp] SET crew='$cid' WHERE id='$id'");
mysql_query("UPDATE $tab[crew] SET members=members+1 WHERE id='$cid'");
mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','<font color=6CA6CD><b>$bpimp[0] has joined the Family!</b></font>','no','$cid');");
mysql_query("DELETE FROM $tab[mail] WHERE crew='$cid' AND inbox='invite' AND dest='$id'");         }
}
$pimp = mysql_fetch_array(mysql_query("SELECT crew,pimp,city,status,cmsg,maxadd FROM $tab[pimp] WHERE id='$id';"));
$crew = @mysql_fetch_array(mysql_query("SELECT name,founder,cofounder,members,profile,icon,city,rank,networth,cbank FROM $tab[crew] WHERE id='$cid';"));
echo mysql_error();
$fid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[pimp] WHERE pimp='$crew[1]';"));
if ($pimp[3] == supporter)
{ $wei_fileupload=$maxupload[sup]; }
else { $wei_fileupload=$maxupload[nor]; }
if (($search == matching) && ($find != ""))
{
if (!fetch("SELECT name FROM $tab[crew] WHERE name='$find';"))
{ $restart=true; }
else {
$cid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[crew] WHERE name='$find';"));
header("Location: family.php?cid=$cid[0]&tru=$tru");
}
}
elseif(($search == bypimpname) && ($find != ""))
{
if (!fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$find' AND crew>0;"))
{ $restart2=true; }
else {
$apimpscrew = mysql_fetch_array(mysql_query("SELECT crew FROM $tab[pimp] WHERE pimp='$find';"));
$cid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[crew] WHERE id='$apimpscrew[0]';"));
header("Location: family.php?cid=$cid[0]&tru=$tru");
}
}
elseif(($make_crew) && ($crew_name != "") && ($pimp[0] == 0))
{
if (!preg_match ('/^[a-z0-9][a-z0-9\-_]*$/i', $crew_name)) {$error="Invalid Family name, a-Z 0-9 characters only.";}
elseif (fetch("SELECT name FROM $tab[crew] WHERE name='$crew_name';")) {$error="That allaince already exists.";}
else {
mysql_query("INSERT INTO $tab[crew] (name,founder,city) VALUES ('$crew_name','$pimp[1]','$pimp[2]');");
$crewid = mysql_fetch_array(mysql_query("SELECT id FROM $tab[crew] WHERE founder='$pimp[1]' AND cofounder='$cofounder';"));
mysql_query("UPDATE $tab[pimp] SET crew='$crewid[0]' WHERE id='$id'");
header("Location: family.php?cid=$crewid[0]&tru=$tru");
}
}
if (($leave==yes) && ($pimp[1] != $crew[1]) && ($pimp[0] == $cid))
{
mysql_query("UPDATE $tab[crew] SET members=members-1 WHERE id='$cid'");
mysql_query("UPDATE $tab[pimp] SET crew='0' WHERE id='$id'");
mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','<font color='FFEBCD'><b>$pimp[1] has left the Family!</b></font>','no','$cid');");
header("Location: index.php?tru=$tru");
}
$URL=$setmedia;
preg_match("/(.+?)\.(\w+)$/",$URL,$matches);
if ($setmedia)
{
if($crew[1] != ($pimp[1] || $cofounder)){$error='you are not the founder of this Family.';}
elseif (!strstr($URL,"http://")){$error='your url must contain <font color="#FFFFFF">http://</font>';}
elseif(strstr($mediaurl,"java")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"php")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"cgi")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"html")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"jsp")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"options")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"account")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"bank")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"purchase")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"?")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"transfer")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"index")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,">")){$error='Please do not use < > in your url.';}
elseif(strstr($mediaurl,"<")){$error='Please do not use < > in your url.';}
elseif(strstr($mediaurl,"width")){$error='You cant set width.';}
elseif(strstr($mediaurl,"heigth")){$error='you cant set size features';}
elseif (($matches[2] == swf) || ($matches[2] == gif) || ($matches[2] == jpg) || ($matches[2] == png))
{
mysql_query("UPDATE $tab[crew] SET profile='$setmedia' WHERE id='$cid'");
$error="Family media has been updated.";
}
else {$error="invalid media format";}
}
if ($remove==media)
{
if($crew[1] != ($pimp[1] || $cofounder)){$error='you are not the founder of this Family.';}
else { mysql_query("UPDATE $tab[crew] SET profile='$setmedia' WHERE id='$cid'");
$error="Family media has been removed.";
}
}
//add cofounder
if($cofounder)
{
if ($crew[1] != ($pimp[1] || $cofounder))
{
$error='you are not the founder of this Family.';
}
elseif($cofounder == $crew[1] || $cofounder == $crew[2])
{
$error='You must select someone else from the drop down menu.';
}
else{
mysql_query("UPDATE $tab[crew] SET cofounder='$cofounder' WHERE id='$cid'");
$error="You made <font color='B0C4DE'>$cofounder</font> co-founder to help run your Family.";
}
}
//Addition by CODER tmg-corporation.com - logic to transfer funds from crewbank to crewmember
if($transfermember)
{
if ($crew[1] != ($pimp[1] || $cofounder)) { $error='you are not the founder.'; }
elseif ($transferamount == ""){$error='You must enter the amount of funds to give.';}
elseif (preg_match ("^([0-9]+)$/^", $transferamount)) {$error='Invalid Amount.';}
elseif ($transferamount > $crew[9]){$error='You cannot transfer more funds than the crew has.';}
elseif ($transferamount <= 0){$error='Must be higher then 0.';}
elseif (!fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$transfermember' AND crew='$cid';")){$error='please select someone from the drop down menu.';}
else  {
$premoney = mysql_fetch_array(mysql_query("SELECT money from $tab[pimp] WHERE pimp='$transfermember'")); 
//transfer money to member and update db (crew funds minus, member funds add)
if (($premoney[0]+$transferamount) >= 500000000000000000000000000000000001){$error='That member can only be given a total of $5,000,000,000.';}
elseif ($premoney[1]>50000000000000000000000000000000001){$error='That player has been given the max allowed already.';}
else{$postmoney = $premoney[0] + $transferamount;
$crewfunds = $transferamount;
mysql_query("UPDATE $tab[pimp] SET money=money+$transferamount WHERE pimp='$transfermember' AND crew='$cid'");
$cpremoney = mysql_fetch_array(mysql_query("SELECT cbank from $tab[crew] WHERE id='$cid'")); 
$cpostmoney = $cpremoney[0] - $transferamount;
mysql_query("UPDATE $tab[crew] SET cbank='$cpostmoney' WHERE id='$cid'"); 
mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','<font color=BCD2EE><b>$pimp[1] has given $".commas($transferamount)." to ".$transfermember."</b></font>','no','$cid');");
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "transfered $transferamount to $transfermember in Family id# $cid";
mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");
$error="you have donated $<font color=FFFFFF>".commas($transferamount)."</font> to <font color=FFFFFF>$transfermember</font>";
}
}      
}
//END addition by CODER tmg-corporation.com - logic to transfer funds from crewbank to crewmember
//Give title option by TMG Corporation
if($ctitle)
{
if ($crew[1] != $pimp[1]) { $error='you are not the founder of this family.'; }
elseif($ctitle == ""){$error='You must select someone from the drop down menu.';}
elseif(!fetch("SELECT COUNT(pimp) FROM $tab[pimp] WHERE crew='$cid';")){$error='You must select a member from the drop down menu.';}
else{mysql_query("UPDATE $tab[pimp] SET ctitle='$ctitle1' WHERE pimp='$ctitle'");
$error="You promoted <font color='FFEBCD'>$ctitle</font> to $ctitle1.";}
}
//End update
if ($invite)
{
$invpmp = mysql_fetch_array(mysql_query("SELECT crew,id FROM $tab[pimp] WHERE pimp='$invite';"));
if ($crew[1] != ($pimp[1] || $cofounder)) { $error='you are not the founder of this Family.'; }
elseif (fetch("SELECT id FROM $tab[invite] WHERE crew='$cid' AND pimp='$invpmp[1]' AND cancelled='no';")) { $error="you have already invited <font color=FFFFFF>$invite</font>."; }
elseif ($cid == $invpmp[0]) { $error='that pimp is already in your Family'; }
elseif (fetch("SELECT id FROM $tab[pimp] WHERE pimp='$invite';"))
{
mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del,crew) VALUES ('$id','$invpmp[1]','<center>$pimp[1] has invited you to join <b>$crew[0]</b>.<br> <a href=family.php?cid=$cid&menu=join&tru=$tru>click here to view $crew[0]</a></center>','$time','invite','no','$cid');");
mysql_query("INSERT INTO $tab[invite] (crew,pimp) VALUES ('$cid','$invpmp[1]');");
mysql_query("DELETE FROM $tab[invite] WHERE cancelled='yes';");
mysql_query("UPDATE $tab[pimp] SET ivt=ivt+1 WHERE pimp='$invite'");
$error="you have invited <font color=FFFFFF>$invite</font> to join your Family";
}
else{ $error='no such pimp exists.'; }
}
if($promote)
{
if ($crew[1] != $pimp[1]) { $error='you are not the founder of this Family.'; }
elseif($promote == ""){$error='You must select someone from the drop down menu.';}
elseif(!fetch("SELECT COUNT(pimp) FROM $tab[pimp] WHERE pimp='$promote' AND crew='$cid';")){$error='You must select someone from the drop down menu.';}
else{mysql_query("UPDATE $tab[crew] SET founder='$promote' WHERE id='$cid'");
$error="You promoted <font color='FFEBCD'>$promote</font> to run your Family.";}
}
if ($cancel)
{
if ($crew[1] != ($pimp[1] || $cofounder)) { $error='you are not the founder.'; }
elseif (!fetch("SELECT id FROM $tab[invite] WHERE pimp='$cancel' AND crew='$cid';")){ $error='You must select someone from the drop down menu.'; }
else {
$civname = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$cancel';"));
mysql_query("DELETE FROM $tab[invite] WHERE pimp='$cancel' AND crew='$cid'");
mysql_query("UPDATE $tab[mail] SET msg='<center>$pimp[1] has cancelled your invitation to <font color='6CA6CD'>$crew[0]</font>.</center>' WHERE crew='$cid' AND dest='$cancel' AND inbox='invite'");
$error="you have removed <font color=FFFFFF>$civname[0]</font> from the invites.";
}
}
if($remove)
{
if ($crew[1] != ($pimp[1] || $cofounder)) { $error='you are not the founder.'; }
elseif ($remove == $pimp[1]) { $error='if you want to abandon your Family, disban it!.'; }
elseif ($remove == ""){$error='please select someone from the drop down menu.';}
elseif (!fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$remove' AND crew='$cid';")){$error='please select someone from the drop down menu.';}
else  {
mysql_query("UPDATE $tab[crew] SET members=members-1 WHERE id='$cid'");
mysql_query("UPDATE $tab[pimp] SET crew='0' WHERE pimp='$remove' AND crew='$cid'");
$error="you have removed <font color=FFFFFF>$remove</font> from your Family.";
}
}
if ($disban==yes)
{
if ($crew[1] != ($pimp[1] || $cofounder)) { $error='you are not the founder.'; }
else {
mysql_query("DELETE FROM $tab[crew] WHERE id='$cid'");
mysql_query("UPDATE $tab[pimp] SET crew='0' WHERE crew='$cid'");
header("Location: index.php?tru=$tru");
}
}
if ($fileupload)
{
$uploadpath=$dir."";
$source=$_FILES[fileupload][tmp_name];
$fileupload_name=$_FILES[fileupload][name];
$weight=$_FILES[fileupload][size];
$imagehw = GetImageSize($fileupload);
$imagewidth = $imagehw[0];
$imageheight = $imagehw[1];
if ($crew[1] != $pimp[1]) { $error='you are not the founder of this Family.'; }
elseif ($imagewidth >= 33){$error="image width too big ($imagewidth pixels)";}
elseif ($imageheight >= 33){$error="image height too big ($imageheight pixels)";}
else{
for($i=0;$i<count($arr_allow_ex);$i++)
{
if(getlast($fileupload_name)!=$arr_allow_ex[$i])
$test.="~~";
}
$exp=explode("~~",$test);
if(strstr($fileupload_name, " ")) {$error='you cannot have spaces in file name.';}
elseif(strstr($fileupload_name, ".php")) {$error='you cannot upload that kind of file.';}
elseif(strstr($fileupload_name, ".cgi")) {$error='you cannot upload that kind of file.';}
elseif(strstr($fileupload_name, ".html")) {$error='you cannot upload that kind of file.';}
elseif(strstr($fileupload_name, ".js")) {$error='you cannot upload that kind of file.';}
elseif(strstr($fileupload_name, ".jsp")) {$error='you cannot upload that kind of file.';}
elseif(strstr($fileupload_name, ".pl")) {$error='you cannot upload that kind of file.';}
elseif(count($exp)==(count($arr_allow_ex)+1)){$error='Invalid image type.';}
elseif($weight>$wei_fileupload){$error="File is to large (".$wei_fileupload." bytes)";}
else{
$fileupload_name="$fileupload_name$time";
$dest = '';
if ( ($source != 'none') && ($source != '' ))
{
$dest=$uploadpath.$fileupload_name;
if ($dest != '')
{
if (copy($source,$dest))
{
$exfile=explode(".",$fileupload_name);
$error="icon has been set successfully!";
mysql_query("UPDATE $tab[crew] SET icon='$dir$fileupload_name' WHERE id='$cid'");
}
}
}
}
}
}
$img = mysql_fetch_array(mysql_query("SELECT profile,icon FROM $tab[crew] WHERE id='$cid';"));
if($crew){ GAMEHEADER("$crew[0]"); }
else{ GAMEHEADER("start or locate a Family"); }
if($crew){
$cnet = fetch("SELECT SUM(networth) FROM $tab[pimp] WHERE crew='$cid' AND status!='banned';");
$cpmp = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE crew='$cid';");
mysql_query("UPDATE $tab[crew] SET networth='$cnet', members='$cpmp' WHERE id='$cid'");
?>
<table width="95%" border="0" bordercolor="#ACA899" class="maintxt">
<tr>
<td valign="top" align="right">
<?if(($menu==options) && ($pimp[1] == $crew[1] || ($pimp[1] == $crew[2]))){ //START THE OPTIONS CODE
?><form method="post" action="family.php?cid=<?=$cid?>&menu=options&tru=<?=$tru?>" enctype="multipart/form-data">
<center><font color="red"><b><?=$error?></b></font>
<br><b>&middot; &middot; &middot; <font color="red">Family options</font> &middot; &middot; &middot;</b></center>
<br>
<!--add cofounder option-->
<table>
<tr>
<td align="right">add coboss:</td>
<td>
<select name="cofounder"><option value="">&middot; SELECT ONE &middot;</option>
<?if(fetch("SELECT COUNT(pimp) FROM $tab[pimp] WHERE crew='$cid';") > 1){?>
<?
$get = mysql_query("SELECT pimp FROM $tab[pimp] WHERE crew='$cid' AND pimp!='$crew[1]' ORDER BY id DESC;");
while ($cofounders = mysql_fetch_array($get))
{
?><option value="<?=$cofounders[0]?>"><?=$cofounders[0]?></option>
<?
}
?>
</select>
<!--end add cofounder--></td>
</tr>
<?}?>
<tr>
<td align="right">invite <small><B>mafioso</B></small>:</td>
<td><input type="text" class="text" name="invite"></td>
</tr>
<?if(fetch("SELECT pimp FROM $tab[invite] WHERE crew='$cid' AND cancelled='no';")){?>
<tr>
<td align="right">cancel invite:</td>
<td>
<select name="cancel"><option value="">&middot; SELECT ONE &middot;</option>
<?
$get = mysql_query("SELECT pimp FROM $tab[invite] WHERE crew='$cid' AND cancelled='no' ORDER BY id DESC;");
while ($invites = mysql_fetch_array($get))
{
$invitedpimps = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$invites[0]';"));
?><option value="<?=$invites[0]?>"><?=$invitedpimps[0]?></option><?
}
?>
</select></td>
</tr>
<?}?>
<?if($img[0]){?><tr><td colspan="2"><b><small><font color="red"><?=$img[0]?></font> &nbsp;(<a href="family.php?cid=<?=$cid?>&menu=options&remove=media&tru=<?=$tru?>">remove</a>)</small></b></td></tr><?}?>
<tr>
<td align="right">set Family media:</td><td><input type="text" class="text" name="setmedia"></td>
</tr>
<tr>
<td align="right">upload Family icon:</td><td><input type="file" class="text" size="9" name="fileupload"> <nobr><font color="red"><small><b>"32x32 pixels, <?=$wei_fileupload?> bytes max"</b></small></font></nobr></td>
</tr>
<?if(fetch("SELECT COUNT(pimp) FROM $tab[pimp] WHERE crew='$cid';") > 0){?>
<tr>
<td align="right">promote <small><B>mafioso</B></small>:<br />
<br />
Give Title: </td>
<td>
<select name="promote"><option value="">&middot; SELECT ONE &middot;</option>
<?
$get = mysql_query("SELECT pimp FROM $tab[pimp] WHERE crew='$cid' AND pimp!='$crew[1]' ORDER BY id DESC;");
while ($promotes = mysql_fetch_array($get))
{
?><option value="<?=$promotes[0]?>"><?=$promotes[0]?></option><?
}
?>
</select>
<br />
<br />
<select name="ctitle" id="ctitle">
<option value="">&middot; SELECT ONE &middot;</option>
<?
$get3 = mysql_query("SELECT pimp,id FROM $tab[pimp] WHERE crew='$cid' ORDER BY id DESC;");
while ($promotes1 = mysql_fetch_array($get3))
{
$cp = $promotes1[1]
?>
<option value="<?=$promotes1[0]?>">
<?=$promotes1[0]?>
</option>
<?
}
?>
</select>
<select name="ctitle1" id="ctitle1">
<option value="">&middot; SELECT ONE &middot;</option>
<option value="-=Boss=-">-=Boss=-</option>
<option value="-=Underboss=-">-=Underboss=-</option>
<option value="-=Captain=-">-=Captain=-</option>
<option value="-=Enforcer=-">-=Enforcer=-</option>
<option value="-=Associate=-">-=Associate=-</option>
</select></td>
</tr>
<tr>
<td align="right">remove <small><B>mafioso</B></small>:<br>
<br></td>
<td>
<select name="remove"><option value=""> &middot; SELECT ONE &middot;</option>
<?
$get = mysql_query("SELECT pimp FROM $tab[pimp] WHERE crew='$cid' AND pimp!='$crew[1]' ORDER BY id DESC;");
while ($removes = mysql_fetch_array($get))
{
?><option value="<?=$removes[0]?>"><?=$removes[0]?></option><?
}
?>
</select><br><br></td>
</tr>
<?}?>
<tr>
<td align="right"><br><nobr>disban <b><?=$crew[0]?></b>?:</nobr></td><td><br><select name="disban"><option selected value="no">no</option><option value="yes">yes</option></select> <font color="red"><small><b>warning: this is a no undo!</b></small></font></td>
</tr>
<!-- give money to crewmember -->
<tr>
<td align="right"><font color="#FFFFFF" size="2">give funds to:</font></td>
<td><font size="2">
<select name="transfermember">
<option value=""> &middot; SELECT ONE &middot;</option>
<?
$get = mysql_query("SELECT pimp FROM $tab[pimp] WHERE crew='$cid' ORDER BY id DESC;");
while ($funds = mysql_fetch_array($get))
{
?>
<option value="<?=$funds[0]?>">
<?=$funds[0]?>
</option>
<?
}
?>
</select>
</font></td>
</tr>
<tr>
<td><font color="#FFFFFF" size="2">amount of funds:</font></td>
<td><font size="2">
<input name="transferamount" type="text" maxlength="20">
</font></td>
</tr>
<!-- end give money to crewmember -->
<tr>
<td colspan="2" align="center"><input type="submit" class="button" value="update options"></td>
</tr>
</table>
</form>
<?}elseif(($menu==options) && ($pimp[0] == $cid)){?>
<font color="red"><b>caution:</b> this is irreversible!</font><br>
<a href="family.php?cid=<?=$cid?>&leave=yes&tru=<?=$tru?>"><font color="FFFFFF">click</font> here <font color="FFFFFF">to leave <?=$crew[0]?></font></a>
<?}else{  //SHOW THE PIMPS IN THE Family
$ahoe = fetch("SELECT SUM(whore) FROM $tab[pimp] WHERE crew='$cid';") / $crew[3];
$athug = fetch("SELECT SUM(thug) FROM $tab[pimp] WHERE crew='$cid';") / $crew[3];
$aworth = fetch("SELECT SUM(networth) FROM $tab[pimp] WHERE crew='$cid';") / $crew[3];
?>
<?
if (($menu==join) && (fetch("SELECT id FROM $tab[invite] WHERE crew='$cid' AND cancelled='no' AND pimp='$id';")))
{
if($crew[3] >= $dacrewmax[0]){echo"<b>you can't join this Family because it is maxed out.</b>";}
else{
if ($join!=yes){?><b>invitation found:</b> <?if($pimp[0] > 0){echo"you must leave your current Family if you want to join $crew[0]";}else{?><a href="family.php?cid=<?=$cid?>&menu=join&join=yes&tru=<?=$tru?>">click here to join <?=$crew[0]?></a><?}}?><br>

<br><?}}?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top"><?if($img[1]){?><img src="<?=$img[1]?>" align="absmiddle"><?}?> <b><font size="3"><?=$crew[0]?></font></b>
<br>
ranked: <?=$crew[7]?>
<br>
boss of: <a href="mobster.php?pid=<?=$crew[1]?>&tru=<?=$tru?>"> <?=$crew[1]?> </a><br>
<?if($crew[2]){?>  coboss: <a href="mobster.php?pid=<?=$crew[2]?>&tru=<?=$tru?>"> <?=$crew[2]?> </a><?}?>
<br>
<?if($img[0]){ $img[0]=securepic($img[0]); $pro=strrchr($img[0],'.');if($pro == ".swf"){?><embed src="<?=$img[0]?>" background="#000022" menu="false" quality="high" width="200" height="200" type="application/x-shockwave-flash" pluginspage"=http://www.macromedia.com/go/getflashplayer"></embed><?}else{?><img src="<?=$img[0]?>" width="200" height="200"><?}}?>
<?if($pimp[0] ==$cid){?></td>
    <td align="left" valign="bottom"><b>
      <?if($menu==options){?>
      <a href="family.php?cid=<?=$cid?>&tru=<?=$tru?>">Family Home</a>
      <?}else{?>
      <br />
::: <a href="family.php?cid=<?=$cid?>&menu=options&tru=<?=$tru?>">Family Options</a>
<?}?>
<br />
::: <a href="cboard.php?cid=<?=$cid?>&tru=<?=$tru?>">Family Board</a><br />
::: <a href="cranks.php?tru=<?=$tru?>">Family Ranks</a> </b>
      <?}?></td>
  </tr>
</table><?
//addition by CODER tmg-corporation.com, crewmember --> crew donation logic
if ($_GET['donation'] == "1" and $_POST['donate'] > 0){ 
$donateamount = fixinput($_POST['donate']);                       //how much is member attempting to donate
$dpimp = mysql_fetch_array(mysql_query("SELECT money FROM $tab[pimp] WHERE id='$id';"));
$cpmp1 = fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE crew='$cid';");
if (preg_match ("/^([0-9]+)$/", $_POST['donate'])) {
if ($dpimp[0] < $donateamount){                    //does member have as much money as they are trying to donate
$cbankmessage = "<br><font color=red>You cannot donate more money than you have!</font>";}   
//    elseif ($crew[cbank] >= 10000000000000){                    //if the bank is more then $10,000,000,000,000 wont allow
//		$cbankmessage = "<br><font color=red>You cannot donate more money because the bank is full!</font>";}
//    elseif (($crew[cbank]+$donateamount) >= 10000000000001){    //if the bank is more then $10,000,000,000,000 wont allow
//		$cbankmessage = "<br><font color=red>You cannot donate more then your donation and crew bank totaling $1,000,000,000,000!</font>";}
elseif ($dpimp[0] < $donateamount){                    //does member have as much money as they are trying to donate
$cbankmessage = "<br><font color=red>You cannot donate more money than you have!</font>";
}else{
$cbank = $crew[9] + $donateamount;
mysql_query("UPDATE $tab[crew] SET cbank='$cbank' WHERE id='$cid'"); //update crewbanks money
$curmoney = $dpimp[0] - $donateamount;
mysql_query("UPDATE $tab[pimp] SET money='$curmoney' WHERE id='$id'");  //subtract donation from crewmembers money
$crew = mysql_fetch_array(mysql_query("SELECT name,founder,cofounder,members,profile,icon,city,rank,networth,cbank FROM $tab[crew] WHERE id='$cid';"));
$cbankmessage = "<br><font color=green>The family thanks you for the $".commas($donateamount)." donation!</font>";
mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','<font color=BCD2EE><b>$pimp[1] has donated $".commas($donateamount)." to the family!</b></font>','no','$cid');");
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "donated $donateamount to Family id# $cid";
mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");
//UPGRADE THERE NETWORTH
$networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);
mysql_query("UPDATE $tab[pimp] SET whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");
}
}else{$cbankmessage = "<br><font color=red>Invalid Amount!</font>";}
}
//end addition by CODER tmg-corporation.com, crewmember --> crew donation logic
?>
<b>Family Stats:</b>
<?if($cid==$pimp[0]){echo"<b>Family Bank: $".$crew[9]."</b><br>";}?>
<?=$crew[3]?> <?if($crew[3]==1){echo"member";}else{echo"members";}?>, combined worth $<?=commas($crew[8])?>
<br>
<font color="red">Average Member:  $
<?=commas($aworth)?> Worth.</font>
<?if($cid==$pimp[0]){echo "<table width=100%><tr><form method=post action=family.php?cid=".$cid."&tru=".$tru."&donation=1><td align=right>Donate: $<input type=text id=entry name=donate size=8 maxlength=10> <input type=submit id=button name=donatefunds value=Donate></td></form></tr></table>";}?>
<?if($pimp[4] > 0){?><br>
<a href="cboard.php?cid=<?=$cid?>&tru=<?=$tru?>">there is <font color="#FFFFFF"><?=$pimp[4]?></font> new Family message!</a></font><br><?}?>
<br>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintxt">
  <tr>
    <td ><div align="center"><small><b>rank</b></small></div></td>
    <td ><div align="center"></div></td width="20">
    <td ><div align="center"><small><b>city</b></small></div></td>
    <td ><div align="center"><small><b>mafioso</b></small></div></td>
    <?if($cid==$pimp[0]){?>
    <td align="center"><div align="center"><small><b>attacks</b></small></div></td>
    <?}?>
    <td align="center"><div align="center"><small><b>operatives</b></small></div></td>
    <td ><div align="center"><small><b>def. units</b></small></div></td>
    <td ><div align="right"><small><b>worth</b></small></div></td>
    <td ></td>
  </tr>
  <?
$get = mysql_query("SELECT id,pimp,whore,thug,networth,nrank,online,attin,attout,status,city,maxadd,dealers,bootleggers,hustlers,punks,hitmen,bodyguards FROM $tab[pimp] WHERE crew='$cid' ORDER BY nrank ASC;");
while ($crw = mysql_fetch_array($get))
{
$ctynme = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$crw[10]';"));
$online=$time-$crw[6];
$cmoney = $crw[11];
$city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='crw[10]';"));
if ($online < 600){$on="<img src=images/online.gif align=absmiddle width=16 height=16>";}else{$on="";}
if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}
?>
  <tr bgcolor="<?=$rankcolor?>">
    <td align="center"><div align="center">
      <?=$crw[5]?>
      .</div></td>
    <td align="center"><div align="center">
      <?=$on?>
    </div></td>
    <td align="center"><div align="center">
      <?=$ctynme[0]?>
    </div></td>
    <td><nobr>
      <div align="center">
        <?if($img[1]){?>
        <img src="<?=$img[1]?>" align="absmiddle" width="16" height="16" />
        <?}?>
        <a href="mobster.php?pid=<?=$crw[1]?>&tru=<?=$tru?>">
          <?=$crw[1]?>
          </a>
        <?if($crw[9] == banned){?>
        &nbsp; <font color="#6CA6CD"><b>FROZEN</b></font>
        <?}?>
      </div>
    </nobr></td>
    <?if($cid == $pimp[0]){?>
    <td width="100"><div align="center">
      <table cellspacing="0" cellpadding="0" width="100%">
        <?if($crw[7]>0){?>
        <tr>
          <td><img src="../images/in.gif" vspace="1" width="<?=($crw[7]/(.2))?>%" height="1" /></td>
        </tr>
        <?}?>
        <?if($crw[8]>0){?>
        <tr>
          <td><img src="../images/out.gif" vspace="1" width="<?=($crw[8]/(.2))?>%" height="1" /></td>
        </tr>
        <?}?>
      </table>
    </div></td>
    <?}?>
    <td align="right"><div align="center">
      <?=commas($crw[2]+$crw[12]+$crw[13]+$crw[14]+$crw[15])?>
    </div></td>
    <td align="right"><div align="center">
      <?=commas($crw[3]+$crw[16]+$crw[17])?>
    </div></td>
    <td align="right"><div align="right">$<?=commas($crw[4])?>
    </div></td>
  </tr>
  <?}?>
</table>
<?
$get = mysql_query("SELECT pimp FROM $tab[invite] WHERE crew='$cid' AND cancelled='no' ORDER BY id ASC;");
$com = 0;
if (($cid == $pimp[0]) && (fetch("SELECT pimp FROM $tab[invite] WHERE crew='$cid' AND cancelled='no';")))
{?>
<table width=100%>
<tr>
<td align=left valign=top>
<small><b>invitations found:
<?while ($invite = mysql_fetch_array($get))
{
$pmpnic = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$invite[0]';"));
?><?if($com != 0){echo", ";}?><a href="mobster.php?pid=<?=$pmpnic[0]?>&tru=<?=$tru?>"><?=$pmpnic[0]?></a><?
$com++;}
?>
</b></small>
<br></td>
</tr>
</table>
<?}?>
<br><center>
<img src="../images/online.gif" width="14" height="14"> = online <br />
attacks in: <img src="../images/in.gif" width="3" height="5"> , out: <img src="../images/out.gif" width="3" height="5">
</center>
<br>
<br>
<br>
<?}?></tr>
</table>
<br>
<?}else{?>
<table width="100%" border="0" align="center" cellpadding="12" cellspacing="0" class="maintxt">
<tr>
<td align="center" valign="top">
<form method="post" action="family.php?tru=<?=$tru?>">
<B><FONT size=+1>find a family</FONT> </B><br>
<font color="red">or you can start a new family...</font></b>
<br>
<br>
<br>
<?
if($restart==true){?>there are no familys matching "<b><font color="red"><?=$find?></font></b>".<br>
<br>
<?}
if($restart2==true){?>no familys found with "<b><font color="red"><?=$find?></font></b>" as a member.<br>
<br>
<?}
if(($search==containing) && ($find != ""))
{?>
Familys containing "<b><font color="red"><?=$find?></font></b>"
<table width="95%" cellspacing="1">
<tr>
<td align="center" width="5%">&nbsp;</td><td><SMALL><B>mafioso</B></SMALL></td>
<td width="50%"><small>location</small></td><td align="center"><small>members</small></td><td align="center"><small>worth</small></td>
</tr>
<?
$get = mysql_query("SELECT name,founder,cofounder,members,icon,city,rank,networth,id FROM $tab[crew] WHERE name LIKE '%$find%' ORDER BY rank asc limit 20;");
while ($results = mysql_fetch_array($get))
{
if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}
$city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$results[4]';"));
?>
<tr bgcolor="<?=$rankcolor?>">
<td align="center"><?=$results[5]?>.</td>
<td><nobr><?if($results[3]){?><a href="family.php?cid=<?=$results[7]?>&tru=<?=$tru?>"><img src="<?=$results[3]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="family.php?cid=<?=$results[7]?>&tru=<?=$tru?>"><?=$results[0]?></a></nobr></td>
<td><nobr><small>located in <?=$city[0]?></small></nobr></td>
<td align="right"><?=$results[2]?></td>
<td align="right">$<?=commas($results[6])?></td>
</tr>
<?}
?></table><br><?
}
?>
<table cellspacing="1"><tr><td valign="middle">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td align="center" width="500">
search for Familyss: <input type="radio" name="search" value="matching" <?if(($search!=containing) && ($search!=bypimpname)){echo"checked";}?>> <b>matching <input type="radio" name="search" value="containing" <?if($search==containing){echo"checked";}?>> containing <input type="radio" name="search" value="bypimpname" <?if($search==bypimpname){echo"checked";}?>>
by mafioso</b> <br>
&nbsp; <input type="input" class="text" name="find"> <input type="submit" class="button" name="do_search" value="find da bitches"> &nbsp;
</td>
</tr>
</table>
</td></tr></table>
<?if($pimp[0] == 0){?>
<br><font size="2" color="#FFEBCD"><b>&middot; &middot; &middot; <font color="#FFFFFF">or</font> &middot; &middot; &middot;</b></font>
<br><?if($error){?><br><font color="red"><b><?=$error?></b></font><?}?>
<br>
start a new family<br>
&nbsp; <input type="input" class="text" name="crew_name" maxlength="20"> <input type="submit" class="button" name="make_crew" value="create Family">
&nbsp;
<br>
<?}?>
<br>
<br>
<br>
<br>
<br>
<br>
</form>
</td>
</tr>
</table>
<?}
GAMEFOOTER();
?>

