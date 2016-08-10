<?php 

require("html.php");
$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status FROM $tab[user] WHERE id='$id';"));

mysql_query("UPDATE $tab[user] SET currently='Viewing winners page', online='$time' WHERE id='$id'"); 

siteheader(); 

?>
  <title></title>
<base target="_top" />
<link rel="SHORTCUT ICON" href="/images/favicon.ico" />
<meta http-equiv="Content-Language" content="en-us">
<body bgcolor="#000000" text="#990000"><center><b><br />
<font color="">view round:</font></b>
<form method="post" action="winners.php">
<select name="round" class="text">
<?
$rnd=fetch("SELECT round FROM $tab[game] WHERE ends<$time ORDER BY round DESC;");;
while($rnd != 0){ ?><option value="<?=$rnd?>" <?if($round==$rnd){echo"selected";}?>>&nbsp;<?=$rnd?>&nbsp;</option><? $rnd--; }
?>
</select>
<input type="submit" class="submit" name="view" value="&nbsp;go&nbsp;">
<br>
<br>
<?
if(fetch("SELECT round FROM $tab[game] WHERE round='$round';")){


//TOP 10 CREWS
   ?>       <?

       $getgames = mysql_query("SELECT round,ends,starts,gamename FROM $tab[game] WHERE round=$round AND ends<$time ORDER BY round ASC LIMIT 1;");

        while ($game = mysql_fetch_array($getgames))

        {

       ?>

       <span class="style10"><strong>Round:       
       <?=$game[0]?>
            </strong> <font size="+1"> </font> Name:
       <?=$game[3]?>
       <br>
       Ended:
       <?=date("M d, Y",$game[1])?>
       <br>
       </span>
       <center>
         <div align="center"; padding:0px; height:auto; width:320px; overflow:no">Top 10 Free
           <table width="100%" border="0" bordercolor="#990000">
             <tr>
               <td align="center" valign="middle"><table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="">
               <tr bgcolor="">
                 <td width="464" height="16" bgcolor=""><nobr>member</nobr></td>
                 <td width="464" bgcolor="" align=right>Networth</td>
               </tr>
               <?

       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 AND status='normal' ORDER BY nrank ASC LIMIT 10;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));

	   

	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#000000";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
             <tr bgcolor="<?=$rankcolor?>">
               <td height="16">
                   <strong><font size="2">
                   <?if($crw[0]){?>
                   <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle">
                   <?}?>
                   <a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
                   <?=$pimp[1]?>
                 </a></font></strong></td>
               <td align=right><font size="2">$
                  <?=commas($pimp[2])?>
               </font></td>
             </tr>
               <?}?>
           </table></td>
             </tr>
           </table>
           
           <br>
           <strong>upporter Rank's..</strong><br> 
           <br>
           Top 10 Supporter
           
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="">
               <tr bgcolor="">
                 <td width="464" height="16" bgcolor="">member</td>
                 <td width="464" bgcolor="" align="right">Networth</td>
               </tr>
               <?

       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 AND status='supporter' ORDER BY nrank ASC LIMIT 10;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));

	   

	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#000000";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
               <tr bgcolor="<?=$rankcolor?>">
                 <td height="16">
                   <font size="2"><strong>
                   <?if($crw[0]){?>
                   <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle" />
                   <?}?>
                   <a href="pimp.php?pid=<?=$pimp[1]?>&=<?=$game[0]?>">
                   <?=$pimp[1]?>
                   </a></strong></font></td>
                 <td align="right"><font size="2">$
                 <?=commas($pimp[2])?>
                 </font></td>
               </tr>
               <?}?>
           </table>
           <br />
           <? /* Top 10 Supporter
           lvl 2
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="">
             <tr bgcolor="">
               <td width="466" height="16" bgcolor=""><nobr>member</nobr></td>
               <td width="462" bgcolor="" align="right">Networth</td>
             </tr>
             <?

       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code,status,transfered FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 AND status='supporter' AND transfered>=300001 ORDER BY nrank ASC LIMIT 10;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));

	   

	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#000000";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
             <tr bgcolor="<?=$rankcolor?>">
               <td height="16"><nobr>
                 <?if($crw[0]){?>
                 <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle" />
                 <?}?>
                 <small><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
                   <?=$pimp[1]?>
                 </a></small></nobr></td>
               <td align="right"><small>$
                 <?=commas($pimp[2])?>
               </small></td>
             </tr>
             <?}?>
           </table> */?>
           <br>
           <strong>Family Ranks are based on after players are auto un-banked and auto sell of weapons.           </strong><br>
           <br>
           <?
$get111 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='1';"));
         ?>Family Rank 1  Final Networth: 
           <?=commas($get111[2])?>
           <br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='1';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='1' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get112 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='2';"));
         ?>
           Family Rank 2 Final Networth: 
           <?=commas($get112[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='2';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='2' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get113 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='3';"));
         ?>
           Family Rank 3 Final Networth: 
           <?=commas($get113[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='3';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='3' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get114 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='4';"));
         ?>
           Family Rank 4 Final Networth: 
           <?=commas($get114[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='4';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='4' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get115 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='5';"));
         ?>
           Family Rank 5 Final Networth: 
           <?=commas($get115[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='5';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='5' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get116 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='6';"));
         ?>
           Family Rank 6 Final Networth: 
           <?=commas($get116[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='6';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='6' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get117 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='7';"));
         ?>
           Family Rank 7 Final Networth: 
           <?=commas($get117[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='7';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='7' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get118 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='8';"));
         ?>
           Family Rank 8 Final Networth: 
           <?=commas($get118[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='8';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='8' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get119 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='9';"));
         ?>
           Family Rank 9 Final Networth: 
           <?=commas($get119[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='9';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='9' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?
$get1110 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='10';"));
         ?>
           Family Rank 10 Final Networth: 
           <?=commas($get1110[2])?>
<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left"><font size="2">&nbsp;</font></td>
               <td width="50%" align="right"><font size="2"><small>networth</small></font></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank,networth FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='10';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='10' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[networth])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           <?          $thugk_resultf = mysql_query("SELECT pimp,thugk,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='normal' ORDER BY thugk DESC LIMIT 10");
		   $thugk_result = mysql_query("SELECT pimp,thugk,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='supporter' ORDER BY thugk DESC LIMIT 10");
   $hoek_result = mysql_query("SELECT pimp,whorek,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='supporter' ORDER BY whorek DESC LIMIT 10");
     $user_info = mysql_fetch_array(mysql_query("SELECT pimp,whore,thug,crack,weed,thugk,whorek,attackin,attackout,msgsent,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE id='$id'"));
           ?>
           Top OP Killers -=Supporter=- <br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));

$get112 = mysql_query("SELECT id,pimp,networth,thugk,whorek,status,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='supporter' ORDER BY whorek DESC LIMIT 10;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[whorek])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br>
           <strong>Top Killer. Both Top DU Killers and Top OP killers</strong><br> 
           <br />
           Top DU Killers -=Supporter=-<br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='10';"));
$get112 = mysql_query("SELECT id,pimp,networth,thugk,whorek,status,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='supporter' ORDER BY thugk DESC LIMIT 10;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[thugk])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
           <br />
           Top DU Killers -=Free=- <br />
           <table width="90%" border="0" cellpadding="0" cellspacing="0" bordercolor="#333333">
             <tr>
               <td width="50%" align="left">&nbsp;</td>
               <td width="50%" align="right"><small>networth</small></td>
             </tr>
             <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='10';"));
$get112 = mysql_query("SELECT id,pimp,networth,thugk,whorek,status,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE status='normal' ORDER BY thugk DESC LIMIT 10;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
             <tr bgcolor="<?=$color?>">
               <td><b><font color="" size="2"> <a href="pimp.php?pid=<?=$crw1[pimp]?>&rnd=<?=$game[0]?>">
                 <?=$crw1[pimp]?>
               </a></font></b></td>
               <td align="right"><font size="2">
               <?=commas($crw1[thugk])?>
               </font></td>
             </tr>
             <?
   }
   ?>
           </table>
         </div>
       </center>
       <br>
  <?}?>
  <?}?>
</div>
</center>
</form><?

sitefooter();

?>