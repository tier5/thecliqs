<?
include("html.php");
$err="";
$step=0;
mysql_query("UPDATE $tab[pimp] SET page='pimp profile' WHERE id='$id'");
$pimp = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,online,whore,thug,lowrider,networth,profile,lastattackby,lastattack,crew,description,city,id,status,ip,attin,attout,dealers,bootleggers,hustlers,punks,hitmen,bodyguards,ctitle,subscribe FROM $tab[pimp] WHERE pimp='$pid';"));
$city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pimp[13]';"));
$crew = mysql_fetch_array(mysql_query("SELECT id,name,icon,founder FROM $tab[crew] WHERE id='$pimp[11]';"));
$attacker = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$pimp[9]';"));
$pmp = mysql_fetch_array(mysql_query("SELECT networth,city,status,subscribe FROM $tab[pimp] WHERE id='$id';"));
$profile = mysql_fetch_array(mysql_query("SELECT id,status FROM $tab[pimp] WHERE pimp='$pid' AND id='$id';"));
$profileUsr = mysql_fetch_array(mysql_query("SELECT username,status FROM $tab[user] WHERE id='$profile[id]';"));
 
$hit_high=$pimp[7]*40;
$hit_low=$pimp[7]/2;
if(($alert) && ($pmp[2] == admin)){ mysql_query("UPDATE $tab[pimp] SET alert='$alert', newalert='1' WHERE id>0 AND pimp='$pid';"); }

if(($pimp["status"] == admin) && ($reset == "description")){
   mysql_query("UPDATE $tab[pimp] SET description='' WHERE pimp='$pimp[id]'");
  }
if(($pimp["status"] == admin) && ($reset == "worth")){
   mysql_query("UPDATE $tab[pimp] SET networth='0' WHERE id='$pimp[id]'");
  }
  
if(($pimp["status"] == admin) && ($reset == "attksin")){
   mysql_query("UPDATE $tab[pimp] SET attin='0' WHERE pimp='$pimp[id]'");
  }
  
if(($pimp["status"] == admin) && ($reset == "attksout")){
   mysql_query("UPDATE $tab[pimp] SET attout='0' WHERE id='$pimp[id]'");
  }
  
  
if(($add == bitch) || ($add == contact) || ($add == block))
{
if((fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE id='$pimp[id]';")) && (!fetch("SELECT COUNT(id) FROM $tab[clist] WHERE contact='$pimp[id]' AND pimp='$id';")))
  {
  mysql_query("INSERT INTO $tab[clist] VALUES ('null','$id','$pimp[id]','$add');");
  $buddymsg="<center><b>$pimp[0] added to $add list.</b></center></span>";
  }
}



if(($del == bitch) || ($del == contact) || ($del == block))
{
if((fetch("SELECT COUNT(id) FROM $tab[pimp] WHERE id='$pimp[id]';")) && (fetch("SELECT COUNT(id) FROM $tab[clist] WHERE contact='$pimp[id]' AND pimp='$id';")))
  {
  mysql_query("DELETE from $tab[clist] WHERE contact='$pimp[id]' AND pimp='$id'");

  $buddymsg="<center><b>$pimp[0] deleted from $del list.</b></center></span>";
  }
}

if (($search == matching) && ($find != ""))
{
         if (!fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$find';"))
            { $restart=true; }
       else {
            $pid = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE pimp='$find';"));
            header("Location: mobster.php?pid=$pid[0]&tru=$tru");
            }
}

  if($pimp){ GAMEHEADER("profile of $pimp[0]"); }
else{ GAMEHEADER("find pimp"); }
if($pimp){?>

<style type="text/css">
<!--
.style1 {color: red}
.style3 {color: #ff0000}
-->
</style>
<table width="500" border="0" align="center" cellpadding="12" cellspacing="0" class="maintxt">
 <tr>
  <td align="center" valign="top">
<table width="500" class="maintxt">
 <tr>
  <td align="center">
  <?if($pimp[14]==$id){?><a href="options.php?opt=profile&tru=<?=$tru?>">click here to edit your profile media, description.</a>
  <?}else{?><a href="out.php?camefrom=pimp&pid=<?=$pid?>&tru=<?=$tru?>">send a message</a> 
  &nbsp; &nbsp; &nbsp; <!--BItCH LIST SHIT!-->
  <?
   if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='block' AND contact='$pimp[id]';"))
     {?>
  <b>already blocked <a href="mobster.php?pid=<?=$pid?>&amp;del=block&amp;tru=<?=$tru?>">delete block </a></b>
  <?}
 else{
         if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='block' AND contact='$pimp[id]';"))
           {?>
  
  <?}
       else{?>
  <a href="mobster.php?pid=<?=$pid?>&amp;add=block&amp;tru=<?=$tru?>">add to blocked </a>
  <?}
     }
  ?>&nbsp; &nbsp; &nbsp;<strong> 
  <?
   if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='contact' AND contact='$pimp[id]';"))
     {?>
  <b>already a contact</b>
  <?}
 else{
         if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='bitch' AND contact='$pimp[id]';"))
           {?>
  <a href="mobster.php?pid=<?=$pid?>&amp;del=bitch&amp;tru=<?=$tru?>">delete bitch</a>
  <?}
       else{?>
  <a href="mobster.php?pid=<?=$pid?>&amp;add=bitch&amp;tru=<?=$tru?>">add to bitches</a>
  <?}
     }
  ?> 
  </strong>  &nbsp; &nbsp; &nbsp; <!--CONtACT LIST SHIT!-->
  <?
   if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='bitch' AND contact='$pimp[id]';"))
     {?><b>already a bitch</b><?}
 else{
         if(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='contact' AND contact='$pimp[id]';"))
           {?><a href="mobster.php?pid=<?=$pid?>&del=contact&tru=<?=$tru?>">delete contact</a><?}
       else{?><a href="mobster.php?pid=<?=$pid?>&add=contact&tru=<?=$tru?>">add to contacts</a> <?}
     }
  ?>
  &nbsp; &nbsp; &nbsp; <!-- END ALL THE BULLSHIT!-->
  <?if($pimp[13] != $pmp[1]){?><a href="travel.php?cty=<?=$pimp[13]?>&tru=<?=$tru?>">travel</a><?}else{?><a href="hit.php?pid=<?=$pid?>&tru=<?=$tru?>">attack</a><?}?>
  <?}?>
  <br>
  <br>
  </td>
 </tr>
 <tr>
  <td align="center">
  <?=$buddymsg?>

  <?if($pmp[2]==admin){?>
  <b><small>admin: 
  <a href="javascript://" onClick="window.open('viewmailbox.php?pid=<?=$pimp[14]?>&tru=<?=$tru?>', '', 'scrollbars=yes,resizable=no,width=500,height=450')">View Mailbox</a> ::
  <a href="javascript://" onClick="window.open('viewinfo69.php?pid=<?=$pimp[14]?>&tru=<?=$tru?>', '', 'scrollbars=yes,resizable=no,width=500,height=450')">View Info</a>
  </small></b>
  <?}?>

  <table cellspacing="1" cellpadding="0">
    <tr><td valign="middle">
  <table class="border" width="500" height="100%" border="0" cellspacing="0" cellpadding="3">
   <tr>
    <td align="right"><?if($pimp[8]){ $pimp[8]=securepic($pimp[8]); $pro=strrchr($pimp[8],'.');if($pro == ".swf"){?><embed src="<?=$pimp[8]?>" menu="false" quality="high" width="200" height="200" type="application/x-shockwave-flash" pluginspage"=http://www.macromedia.com/go/getflashplayer"></embed><?}else{?><img src="<?=$pimp[8]?>" width="200" height="200"><?}}?></td>
    <td <? if(!$pimp[8]){?>align="center" <? } ?>valign="middle">
    <table cellspacing="0" cellpadding=="0"><tr><td><nobr><br><font size="+1"><? if($crew[2]){?><a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><img src="<?=$crew[2]?>" align="absmiddle" border="0"></a> <?}?><?=$pimp[0]?></font></nobr></td></tr></table>
    <nobr>
    ranked <b><?=commas($pimp[1])?></b> in <a href="travel.php?tru=<?=$tru?>"><?=$city[0]?></a>, <b><?=commas($pimp[2])?></b> national
    <? if($crew[0] > 0){?><br>
    <? if($crew[3] == $pimp[0]){?>boss<? } else { ?>member<? } ?> of <a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><?=$crew[1]?></a>. Title of <?=$pimp[ctitle]?><?}?>
    <br>
    <? if($pimp[15] == banned){?><br><b><font color="red">This </font><span class="style1">mafioso</span><font color="red"> has been removed from the game</font></b><br><?}?>
    <br><font color="red"><? if($pimp[3]>0){?>last seen <?=countdown($pimp[3])?> ago.<? } else { ?>this <b><span class="style1">mafioso</span></b> hasnt logged in yet.<?}?></font>
    <br>
    This mafioso got <br><b><?=commas($pimp[4]+$pimp[19]+$pimp[20]+$pimp[21]+$pimp[22])?></b> <span class="style1"><B><B>operatives</B></B></span> <span class="style1">and</span> <b><?=commas($pimp[5]+$pimp[23]+$pimp[24])?></b> <span class="style1"><B><B>defensives</B></B></span>
    <? if($pimp[6]>0){?><br>
    <?}?>
    <br>worth <b>$<?=commas($pimp[7])?></b>
    <br>
    <?if($pimp[9]){?><br>last attacked by <a href="mobster.php?pid=<?=$attacker[0]?>&tru=<?=$tru?>"><?=$attacker[0]?></a><br><font color="red"><?=countdown($pimp[10])?> ago.</font><?}?>
    <br>
<br>
<font color="#ff0000"><b></font><span class="style3">Mafioso</span><font color="#ff0000"> Status</font> = <font color="ff0000"><?=$pimp[15]; ?></b></font></a>
    <br>
	<? if($pmp[subscribe] == 1){ echo "Bronze Member";}if($pmp[subscribe] == 2){ echo "Silver Member";}if($pmp[subscribe] == 3){ echo "Gold Member";}if($pmp[subscribe] == 4){ echo "ULTRA Member";}?>
<br>
	<? 
	if($pmp[2] == admin){
		if($reset == worth){
			mysql_query("UPDATE $tab[pimp] SET networth='25000' WHERE pimp='$pid'");
			}
		if($reset == attin){
			mysql_query("UPDATE $tab[pimp] SET attin='0' WHERE pimp='$pid'");
			}
		if($reset == attout){
			mysql_query("UPDATE $tab[pimp] SET attout='0' WHERE pimp='$pid'");
			}
		if($reset == description){
			mysql_query("UPDATE $tab[pimp] SET description='This pimps description has been reset by the admins' WHERE pimp='$pid'");
			}
		if($reset == profile){
			mysql_query("UPDATE $tab[pimp] SET profile='' WHERE pimp='$pid'");
			}
	}
	?>
	<br><? if($pmp[2] == admin){?><strong>Reset:</strong> <a href=mobster.php?pid=<?=$pid?>&tru=<?=$tru?>&reset=worth>Worth</a> &raquo; <a href=mobster.php?pid=<?=$pid?>&tru=<?=$tru?>&reset=attin>Attks In</a> &raquo; <a href=mobster.php?pid=<?=$pid?>&tru=<?=$tru?>&reset=attout>Attks Out</a> &raquo; <a href=mobster.php?pid=<?=$pid?>&tru=<?=$tru?>&reset=description>Description</a> &raquo; <a href=mobster.php?pid=<?=$pid?>&tru=<?=$tru?>&reset=profile>Profile</a><?}?>
  	<br><? if($pmp[2] == admin){?><form method="post" action="mobster.php?tru=<?=$tru?>&pid=<?=$pid?>"><b>admin panel:<br></b> alert pimp: <input type="text" class="text" name="alert" size="20"> <input type="submit" name="alertpimp" value="alert!"></form><?}?>
    </nobr>
    </td>
   </tr>
  </table>
  </td></tr></table>
  <table width="500"><tr><td align="center"><? if($pimp[12]){echo"<br>$pimp[12]";}?></td></tr></table>
  </td>
 </tr>
</table>
<br>
  <table width="500" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center" valign="top"><? 
	$statt1 = mysql_fetch_array(mysql_query("SELECT code FROM $tab[pimp] WHERE pimp='$pid';"));
	$statt2 = mysql_fetch_array(mysql_query("SELECT goldbrick,silverbrick,bronzebrick,goldak,silverak,bronzeak,goldfree,silverfree,bronzefree,goldglock,silverglock,bronzeglock FROM $tab[user] WHERE code='$statt1[0]';"));
			?>
        <span class="style4">Previously Won Medals for past rounds.</span><br />
<table width="500" height="164" border="0">
  <tr>
    <td height="22" align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[0] > 0){?>
      #1 Supporter Ranking </strong></font><font size="2"><br />
      <img src="../new/Medalsupporterwinnergold.jpg" width="150" height="150" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[1] > 0){?>
      #2 Supporter Ranking </strong></font><font size="2"><br />
      <img src="../new/medalsupportersilver.jpg" width="150" height="150" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[2] > 0){?>
      #3 Supporter Ranking </strong></font><font size="2"><br />
      <img src="../new/MedalSupporterBronze.jpg" width="150" height="150" /><br />
      <?}?>
      </font></td>
  </tr>
  <tr>
    <td height="23" align="center" valign="top"><span class="style8">
      <?if($statt2[0] > 0){?>
      <?=commas($statt2[0])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[1] > 0){?>
      <?=commas($statt2[1])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[2] > 0){?>
      <?=commas($statt2[2])?>
      <?}?>
    </span></td>
  </tr>
  <tr>
    <td height="26" align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[3] > 0){?>
      #1 Supporter DU Killer </strong></font><font size="2"><br />
      <img src="../new/medalsupporterdukillergold.jpg" width="150" height="150" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[4] > 0){?>
      #2 Supporter DU Killer </strong></font><font size="2"><br />
      <img src="../new/Medalsupporterdukillersilver.jpg" width="150" height="150" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[5] > 0){?>
      #3 Supporter DU Killer </strong></font><font size="2"><br />
      <img src="../new/MedalSupporterkillerbronze.jpg" width="150" height="150" />      <br />
      <?}?>
      </font></td>
  </tr>
  <tr>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[3] > 0){?>
      <?=commas($statt2[3])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[4] > 0){?>
      <?=commas($statt2[4])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[5] > 0){?>
      <?=commas($statt2[5])?>
      <?}?>
    </span></td>
  </tr>
  <tr>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[6] > 0){?>
      #1 Free Ranking </strong></font><font size="2"><br />
      <img src="../new/medalnormalrankgoldsm.jpg" width="75" height="75" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[7] > 0){?>
      #2 Free Ranking </strong></font><font size="2"><br />
      <img src="../new/medalnormalranksilversm.jpg" width="75" height="75" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[8] > 0){?>
      #3 Free Ranking </strong></font><font size="2"><br />
      <img src="../new/medalnormalrankbronzesm.jpg" width="75" height="75" /><br />
      <?}?>
      </font></td>
  </tr>
  <tr>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[6] > 0){?>
      <?=commas($statt2[6])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[7] > 0){?>
      <?=commas($statt2[7])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[8] > 0){?>
      <?=commas($statt2[8])?>
      <?}?>
    </span></td>
  </tr>
  <tr>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[9] > 0){?>
      #1 Free Killer </strong></font><font size="2"><br />
      <img src="../new/normaldukillergoldsm.jpg" width="59" height="38" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[10] > 0){?>
      #2 Free Killer </strong></font><font size="2"><br />
      <img src="../new/normaldukillersilversm.jpg" width="61" height="40" /><br />
      <?}?>
      </font></td>
    <td align="center" valign="top"><font color="#990000" size="2"><strong>
      <?if($statt2[11] > 0){?>
      #3 Free Killer </strong></font><font size="2"><br />
      <img src="../new/normaldukillerbronzesm.jpg" width="61" height="40" /><br />
      <?}?>
      </font></td>
  </tr>
  <tr>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[9] > 0){?>
      <?=commas($statt2[9])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[10] > 0){?>
      <?=commas($statt2[10])?>
      <?}?>
    </span></td>
    <td align="center" valign="top"><span class="style8">
      <?if($statt2[11] > 0){?>
      <?=commas($statt2[11])?>
      <?}?>
    </span></td>
  </tr>
</table></td>
    </tr>
  </table></td>
 </tr>
</table>
<br>
<?}else{?>
<table width="500" border="0" align="center" cellpadding="12" cellspacing="0" class="maintxt">
 <tr>
  <td align="center" valign="top">
<form method=post action="mobster.php?tru=<?=$tru?>">
  <B><FONT size=+1>find mafioso</FONT></B> <BR>
  <B><SPAN style="COLOR: #ee0000">the mafioso directory</SPAN></B> <br>
<br>
<?
  if($restart==true){?>
There are no mafiosos matching "<b><font color="#7777CC"><?=$find?></font></b>"<br>
<?}

  if(($search==containing) && ($find != ""))
    {?>
    Mafiosos containing "<b><font color="red"><?=$find?></font></b>"
    <table width="95%" cellspacing="1">
     <tr>
      <td align="center" width="43"><small>Rank</small></td><td width="15"></td><td width="296"> Mafiosos</td>
      <td width="426"><small>city and local rank</small></td><td width="144" align="center"><small>worth</small></td>
     </tr>
    <?
     $get = mysql_query("SELECT id,pimp,nrank,rank,city,networth,online,whore,thug,crew FROM $tab[pimp] WHERE pimp LIKE '%$find%' ORDER BY nrank asc limit 20;");
     while ($results = mysql_fetch_array($get))
           {
           $online=$time-$results[6];
           if ($online < 600){$on="<img src=$site[img]online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

               if($id == $results[0]){$rankcolor = "#cccccc";}
           elseif($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
           elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

           $city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$results[4]';"));
           $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$results[9]';"));
           ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td align="center"><?=$results[2]?>.</td>
        <td align="center"><?=$on?></td>
        <td><nobr><?if($icn[0]){?><a href="family.php?cid=<?=$t10[9]?>&tru=<?=$tru?>"><img src="<?=$icn[0]?>" align="absmiddle" width="16" height="16" border="0"></a><?}?> <a href="mobster.php?pid=<?=$results[1]?>&tru=<?=$tru?>"><?=$results[1]?></a></nobr></td>
        <td><nobr><small>ranked <?=$results[3]?> in <?=$city[0]?></small></nobr></td>
        <td align="right">$<?=commas($results[5])?></td>
       </tr>
           <?}
    ?></table>
    <br><?
    }
?>
    <br>
    <table cellspacing="1">
      <tr><td valign="middle">
    <table class="td" width="100%" height="100%" border="0" cellspacing="0" cellpadding="3">
     <tr>
      <td align="center" width="500">
search for mafiosos: 
  <input type="radio" name="search" value="matching" <?if($search!=containing){echo"checked";}?>> <b>matching <input type="radio" name="search" value="containing" <?if($search==containing){echo"checked";}?>> containing</b>
<br>&nbsp; <input type="input" class="text" name="find"> <input type="submit" class="button" name="do_search" value="find da bitch"> &nbsp;
     </td>
     </tr>
    </table>
    </td></tr></table>
    <table width="450">
     <tr>
      <td align="center" width="150"><b>searches:</b></td><td align="center" width="150"><b>bitches:</b></td><td align="center" width="150"><b>contacts:</b></td>
     </tr>
    </table>

<br>
<br>
</form>

  </td>
 </tr>
</table>
<br>
<?}?>
<br>
<?=bar($id)?>
<br>
<br>
<?
GAMEFOOTER();
?>