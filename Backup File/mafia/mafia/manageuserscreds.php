<?

include("html.php");

mysql_query("UPDATE $tab[user] SET currently='Issuing Prizes', online='$time' WHERE id='$id'"); 

$user = mysql_fetch_array(mysql_query("SELECT status,credits FROM $tab[user] WHERE id='$id';"));

$menu='pimp/';

secureheader();

siteheader();

?>
<style type="text/css">
<!--
.style8 {font-size: medium}
.style11 {font-size: large}
.style19 {color: #FFFFFF; font-weight: bold; font-size: small; }
.style23 {font-size: small; color: #FF0000; }
.style25 {color: #FF0000; font-weight: bold; font-size: small; }
.style10 {font-size: large; font-weight: bold; }
.style12 {color: #990000}
body,td,th {
	color: #FFFFFF;
}
body {
	background-color: #000000;
}
a:link {
	color: #FFFFFF;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFFFF;
}
a:hover {
	text-decoration: none;
	color: #666666;
}
a:active {
	text-decoration: none;
}
.style28 {
	color: green;
	font-weight: bold;
}
.style29 {
	color: red;
	font-weight: bold;
}
.style30 {color: green; font-weight: bold; font-size: large; }
-->
</style>
   <table width="100%" height="100%">
    <tr>
     <td height="12" align="center">
	 <?
	 if($user[0] == admin)
	           {
if($givepimpc){
if($_POST['credits_amount']&&$_POST['username'])
{
$roo=1;
$errorz=mysql_fetch_array(mysql_query("select id from users where username='".$_POST['username']."'"));
if($errorz[0]!="") $roo=0;

					$rez=mysql_query("update users set credits=credits+'".$_POST['credits_amount']."' where username='".$_POST['username']."'");
					if(!$roo) print $_POST['credits_amount']." credits have been added to ".$_POST['username']."<br>";
					else print "Error adding credits<br>";
	}			
}
	 ?>
     <br>
     <font size="+1"><b>Give PimpCredits</b></font>	 <form method="post" name="givepimpc" action="manageuserscreds.php">

	 Add <input type="text" name="credits_amount" size="6"> credits to <input type="text" name="username">
	 <input type="submit" name="givepimpc" value="Add credits"> 
	 </form>	 

	

<center>

     <div align="center"; padding:0px; height:auto; width:320px; overflow:no">
       <span class="style8">
       <?

       $getgames = mysql_query("SELECT round,ends,starts FROM $tab[game] WHERE ends<$time ORDER BY ends DESC Limit 5;");

        while ($game = mysql_fetch_array($getgames))

        {

       ?>

       <small><span class="style11">round
       <?=$game[0]?>
       <span class="style10"><span class="style12"><font size="+1">::</font></span> </span><span class="style11">ended:
       <?=dayhour($game[1])?>
       <br>
       <span class="style28">Supporter Ranks </span><span class="style29"></span><br>
</span><span class="style10">       </span> </span>       </span>       
       <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="">
         <tr bgcolor="">
           <td width=17 bgcolor="">ID</td>
           <td width="47" bgcolor="">Master</td>
           <td width="71" height="16" bgcolor=""><nobr>Pimp</nobr></td>
           <td width="170" bgcolor="" align=right>Networth</td>
         </tr>
         <?

       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 AND status='supporter' ORDER BY nrank ASC LIMIT 20;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));

	   

	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#000000";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
         <tr bgcolor="<?=$rankcolor?>">
           <td><small>
             <?=$pimp[3]?>
      .</small></td>
           <td><?=$username[0]?>
               <? if($pimp[3] == 1){?>
               <b><font color=Green>
               <?=$game[3]?>
               </font> / <font color="red">
               <?=$game[11]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 2){?>
               <b><font color=Green>
               <?=$game[4]?>
               </font> / <font color="red">
               <?=$game[12]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 3){?>
               <b><font color=Green>
               <?=$game[5]?>
               </font> / <font color="red">
               <?=$game[13]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 4){?>
               <b><font color=Green>
               <?=$game[6]?>
               </font> / <font color="red">
               <?=$game[14]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 5){?>
               <b><font color=Green>
               <?=$game[6]?>
               </font> / <font color="red">
               <?=$game[14]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 6){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 7){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 8){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 9){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 10){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?></td>
           <td height="16"><nobr>
             <?if($crw[0]){?>
             <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle">
             <?}?>
             <Small><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
             <?=$pimp[1]?>
           </a></small></nobr></td>
           <td align=right><small>$
                 <?=commas($pimp[2])?>
           </small></td>
         </tr>
         <?}?>
       </table>
       <br>
       <span class="style28"><span class="style11">Normal Ranks </span></span><br>
       <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333" bgcolor="">
         <tr bgcolor="">
           <td width=17 bgcolor="">ID</td>
           <td width="47" bgcolor="">Master</td>
           <td width="71" height="16" bgcolor=""><nobr>Pimp</nobr></td>
           <td width="170" bgcolor="" align=right>Networth</td>
         </tr>
         <?

       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew,code,status FROM "."r".$game[0]."_".$tab[pimp]." WHERE rank>0 AND status='normal' ORDER BY nrank ASC LIMIT 20;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

       $username=mysql_fetch_array(mysql_query("select username from users where code='".$pimp['code']."'"));

	   

	    $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$game[0]."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#000000";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
         <tr bgcolor="<?=$rankcolor?>">
           <td><small>
             <?=$pimp[3]?>
      .</small></td>
           <td><?=$username[0]?>
               <? if($pimp[3] == 1){?>
               <b><font color=Green>
               <?=$game[3]?>
               </font> / <font color="red">
               <?=$game[11]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 2){?>
               <b><font color=Green>
               <?=$game[4]?>
               </font> / <font color="red">
               <?=$game[12]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 3){?>
               <b><font color=Green>
               <?=$game[5]?>
               </font> / <font color="red">
               <?=$game[13]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 4){?>
               <b><font color=Green>
               <?=$game[6]?>
               </font> / <font color="red">
               <?=$game[14]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 5){?>
               <b><font color=Green>
               <?=$game[6]?>
               </font> / <font color="red">
               <?=$game[14]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 6){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 7){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 8){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 9){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?>
               <? if($pimp[3] == 10){?>
               <b><font color=Green>
               <?=$game[7]?>
               </font> / <font color="red">
               <?=$game[15]?>
               </font></b>
               <?}?></td>
           <td height="16"><nobr>
             <?if($crw[0]){?>
             <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle">
             <?}?>
             <Small><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
             <?=$pimp[1]?>
           </a></small></nobr></td>
           <td align=right><small>$
                 <?=commas($pimp[2])?>
           </small></td>
         </tr>
         <?}?>
       </table>
              <br>
              <span class="style30">Crew Ranks </span><br>
       <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
         <tr>
           <td width="40" align="center"><small>ID</small></td>
           <td width="16" align="center"><small>Master</small></td>
           <td align="left"><small>Pimp</small></td>
           <td align="right"><small>networth</small></td>
           </tr>
         <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='1';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='1' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
         <tr bgcolor="<?=$color?>">
           <td width="40" align="center"><?=$crw1[id]?></td>
           <td><nobr>
               <?=$username1[0]?></td>
           <td><b><font color="">
             <a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$crw1[pimp]?></a>
    <b><font color=Green><?=$game[crewprize]?></font> / <font color="red"><?=$game[crewprizes]?></font></b>
          </font></b></td>
           <td align="right"><?=commas($crw1[networth])?></td>
           </tr>
         <?
   }
   ?>
       </table><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
         <tr>
           <td width="40" align="center"><small>ID</small></td>
           <td width="16" align="center"><small>Master</small></td>
           <td align="left"><small>Pimp</small></td>
           <td align="right"><small>networth</small></td>
           </tr>
         <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='2';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='2' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
         <tr bgcolor="<?=$color?>">
           <td width="40" align="center"><?=$crw1[id]?></td>
           <td><nobr>
               <?=$username1[0]?></td>
           <td><b><font color="">
             <a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$crw1[pimp]?></a>
    <b><font color=Green><?=$game[crewprize]?></font> / <font color="red"><?=$game[crewprizes]?></font></b>
          </font></b></td>
           <td align="right"><?=commas($crw1[networth])?></td>
           </tr>
         <?
   }
   ?>
       </table><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
         <tr>
           <td width="40" align="center"><small>ID</small></td>
           <td width="16" align="center"><small>Master</small></td>
           <td align="left"><small>Pimp</small></td>
           <td align="right"><small>networth</small></td>
           </tr>
         <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='3';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='3' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
         <tr bgcolor="<?=$color?>">
           <td width="40" align="center"><?=$crw1[id]?></td>
           <td><nobr>
               <?=$username1[0]?></td>
           <td><b><font color="">
             <a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$crw1[pimp]?></a>
    <b><font color=Green><?=$game[crewprize]?></font> / <font color="red"><?=$game[crewprizes]?></font></b>
          </font></b></td>
           <td align="right"><?=commas($crw1[networth])?></td>
           </tr>
         <?
   }
   ?>
       </table><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
         <tr>
           <td width="40" align="center"><small>ID</small></td>
           <td width="16" align="center"><small>Master</small></td>
           <td align="left"><small>Pimp</small></td>
           <td align="right"><small>networth</small></td>
           </tr>
         <?
$msg="";
$user1 = mysql_fetch_array(mysql_query("SELECT credits,id,code FROM $tab[user] WHERE id='$id';"));
$userr = mysql_fetch_array(mysql_query("SELECT code,id,pimp,crew FROM "."r".$game[0]."_".$tab[pimp].";"));


$get11 = mysql_fetch_array(mysql_query("SELECT id,rank FROM "."r".$game[0]."_".$tab[crew]." WHERE rank='4';"));
$get112 = mysql_query("SELECT id,pimp,networth,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE crew='$get11[id]' AND $get11[rank]='4' ORDER BY networth DESC;");
   while ($crw1 = mysql_fetch_array($get112)){

       $username1=mysql_fetch_array(mysql_query("select username from users where code='".$crw1['code']."'"));


         if($row==0){$color="#000000";$row++;}elseif($row==1){$color="#000000";$row--;}

         ?>
         <tr bgcolor="<?=$color?>">
           <td width="40" align="center"><?=$crw1[id]?></td>
           <td><nobr>
               <?=$username1[0]?></td>
           <td><b><font color="">
             <a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>"><?=$crw1[pimp]?></a>
    <b><font color=Green><?=$game[crewprize]?></font> / <font color="red"><?=$game[crewprizes]?></font></b>
          </font></b></td>
           <td align="right"><?=commas($crw1[networth])?></td>
           </tr>
         <?
   }
   ?>
       </table>
                     <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#333333" valign="top">
         <tr>
           <?          $thugk_result = mysql_query("SELECT pimp,thugk,code FROM "."r".$game[0]."_".$tab[pimp]." ORDER BY thugk DESC LIMIT 20"); 
   $hoek_result = mysql_query("SELECT pimp,whorek,code FROM "."r".$game[0]."_".$tab[pimp]." ORDER BY whorek DESC LIMIT 20"); 
     $user_info = mysql_fetch_array(mysql_query("SELECT pimp,whore,thug,crack,weed,thugk,whorek,attackin,attackout,msgsent,code FROM "."r".$game[0]."_".$tab[pimp]." WHERE id='$id'")); 
           ?>
           <td><b>Pimp with most hoe kills</b></td>
         </tr>
         <tr>
           <td><table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                 <td width="150"><table width="100%" cellspacing="1">
                     <tr bgcolor="">
                       <td><nobr>Hoes worst enemy</nobr></td>
                     </tr>
                 </table></td>
                 <td><table width="100%" border="1" cellspacing="1" bordercolor="#FF0000">
                     <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($hoek_result)) { 
            if($bgcolor==0){$rankcolor="#000000";$bgcolor++;}
        ?>
                     <tr bgcolor="<?=$rankcolor?>">
                       <td width="70%" align="right"><div align="center"><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
                           <?=$info[0]?>
                       </a>   <b><font color=Green> <?=$game[tophoekiller]?></font> / <font color="red"><?=$game[tophoekillers]?></font></b>
</div></td>
                       <td width="70%" align="right"> <?=commas($info[1])?></td>
                     </tr>
                     <?
        } 
        ?>
                 </table></td>
               </tr>
           </table></td>
         </tr>
       </table>
                     <br><form method="post" name="givepimpc" action="manageuserscreds.php">

	 Add <input type="text" name="credits_amount" size="6"> credits to <input type="text" name="username">
	 <input type="submit" name="givepimpc" value="Add credits"> 
	 </form>	
                     <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#333333" valign="top">
         <tr>
           <td><b>Pimp with most thug kills</b></td>
         </tr>
         <tr>
           <td><table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                 <td width="150"><table width="100%" cellspacing="1">
                     <tr bgcolor="">
                       <td><nobr>Your thugs fear me</nobr></td>
                     </tr>
                 </table></td>
                 <td><table width="100%" border="1" cellspacing="1" bordercolor="#FF0000">
                   <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($thugk_result)) { 
            if($bgcolor==0){$rankcolor="#000000";$bgcolor++;}
        ?>
                   <tr bgcolor="<?=$rankcolor?>">
                     <td width="69%" align="right"><div align="center"><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$game[0]?>">
                         <?=$info[0]?>
                     </a>  <b><font color=Green><?=$game[topthugkiller]?></font> / <font color="red"><?=$game[topthugkillers]?></font></b></div></td>
                     <td width="69%" align="right"> <?=commas($info[1])?></td>
                   </tr>
                   <?
        } 
        ?>
                 </table></td>
               </tr>
           </table></td>
         </tr>
       </table>
       <? $nohide=0;
	   if($nohide==1){?><table width="450" border="1" cellpadding="0" cellspacing="0" bordercolor="#333333">
         <tr>
           <th width="148" align="left" class="style25" scope="col">Prize for Place </th>
           <th width="142" align="left" class="style23" scope="col">Prize Non-Supporter</th>
           <th width="142" align="left" class="style23" scope="col">Prize Supporter</th>
         </tr>
		 <tr>
           <th width="148" align="left" class="style19" scope="col">1st</th>
           <th width="142" align="left" class="style23" scope="col">
             <?=$game[3]?>
           </th>
           <th width="142" align="left" class="style23" scope="col"> <?=$game[11]?>
           </th>
		 </tr>
         <tr>
           <th align="left" class="style19">2nd</th>
           <th align="left" class="style23">
             <?=$game[4]?>
           </th>
           <th align="left" class="style23"> <?=$game[12]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">3rd</th>
           <th align="left" class="style23">
             <?=$game[5]?>
           </th>
           <th align="left" class="style23"> <?=$game[13]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">4th-5th</th>
           <th align="left" class="style23">
             <?=$game[6]?>
           </th>
           <th align="left" class="style23"> <?=$game[14]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">6th-10th</th>
           <th align="left" class="style23">
             <?=$game[7]?>
           </th>
           <th align="left" class="style23"> <?=$game[15]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">top hoe killer </th>
           <th align="left" class="style23">
             <?=$game[8]?>
           </th>
           <th align="left" class="style23"> <?=$game[16]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">top thug killer </th>
           <th align="left" class="style23">
             <?=$game[9]?>
           </th>
           <th align="left" class="style23"> <?=$game[17]?>
           </th>
         </tr>
         <tr>
           <th align="left" class="style19">Top Crew Prize </th>
           <th align="left" class="style23"><?=$game[10]?></th>
           <th align="left" class="style23"><?=$game[18]?></th>
         </tr>
       </table><?}?>
       <br>
 	      <br>
          <?}?>
     </div>
</center>
	 <? }
	 else print "FUCK OFF WHAT ARE YOU TRYING TO DO..!!!";
	 ?>
</td>
    </tr>
   </table>
<?
sitefooter();
?>