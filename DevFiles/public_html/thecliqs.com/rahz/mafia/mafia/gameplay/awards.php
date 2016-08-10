<?
include("html.php");

    $hoe_result = mysql_query("SELECT pimp,whore,status FROM $tab[pimp] WHERE status!='banned' ORDER BY whore DESC LIMIT 5"); 
   $thug_result = mysql_query("SELECT pimp,thug,status FROM $tab[pimp] WHERE status!='banned' ORDER BY thug DESC LIMIT 5"); 
   $dope_result = mysql_query("SELECT pimp,weed,status FROM $tab[pimp] WHERE status!='banned' ORDER BY weed DESC LIMIT 5"); 
  $crack_result = mysql_query("SELECT pimp,crack,status FROM $tab[pimp] WHERE status!='banned' ORDER BY crack DESC LIMIT 5"); 
  $thugk_result = mysql_query("SELECT pimp,thugk,status FROM $tab[pimp] WHERE status!='banned' ORDER BY thugk DESC LIMIT 5"); 
   $hoek_result = mysql_query("SELECT pimp,whorek,status FROM $tab[pimp] WHERE status!='banned' ORDER BY whorek DESC LIMIT 5"); 
  $attin_result = mysql_query("SELECT pimp,attackin,status FROM $tab[pimp] WHERE status!='banned' ORDER BY attackin DESC LIMIT 5"); 
 $attout_result = mysql_query("SELECT pimp,attackout,status FROM $tab[pimp] WHERE status!='banned' ORDER BY attackout DESC LIMIT 5"); 
   $talk_result = mysql_query("SELECT pimp,msgsent,status FROM $tab[pimp] WHERE status!='banned' ORDER BY msgsent DESC LIMIT 5");
     $user_info = mysql_fetch_array(mysql_query("SELECT pimp,whore,thug,crack,weed,thugk,whorek,attackin,attackout,msgsent,status FROM $tab[pimp] WHERE id='$id' AND status!='banned' ")); 

GAMEHEADER("pimp awards");
?><body>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
                  <FONT size=+1><B>Mafioso Awards</B></FONT>
<br>
<br>
<table width="100%" align="center" border="0">
 <tr>
  <td>

  <table width="400" align="center" valign="top">
   <tr>
    <td><B>Mafioso with most operatives</B></td>
   </tr>
   <tr>
    <td>
    <table width="100%" cellpadding="0" cellspacing="0"> 
     <tr> 
      <td width="150">
      <table width="100%" cellspacing="1"> 
       <tr bgcolor="#cccccc"><td><nobr>Sugar Daddy</nobr></td></tr>
       <tr bgcolor="999999"><td><nobr>Hoes R Us</nobr></td></tr>
        <tr bgcolor="#cccccc"><td><nobr>Hoe Train</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr>Pimp Of Da Year</nobr></td></tr> 
       <tr bgcolor="#cccccc"><td><nobr>Hoes...  Anyone?</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr></nobr></td>
       </tr> 
      </table>
      </td>
      <td>
      <table width="100%" cellspacing="1"> 
       <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($hoe_result)) { 
            if($bgcolor==0){$rankcolor="##cccccc";$bgcolor++;}
        elseif($bgcolor==1){$rankcolor="#999999";$bgcolor--;}
        ?><tr bgcolor="<?=$rankcolor?>"><td><a href="mobster.php?pid=<?=$info[0]?>&tru=<?=$tru?>"><?=$info[0]?></a></td><td align="right"><?=commas($info[1])?></td></tr><?
        } 
        ?>
        <tr bgcolor="999999"><td><b><?=$user_info[0]?></b></td><td align="right"><?=commas($user_info[1])?></td></tr>
      </table> 
      </td>
     </tr> 
    </table>
    </td>
   </tr>
  </table>

  </td>
 </tr>
 <tr>
  <td>

  <table width="400" align="center" valign="top">
   <tr>
    <td><B>Mafioso with most defensive units </B></td>
   </tr>
   <tr>
    <td>
    <table width="100%" cellpadding="0" cellspacing="0"> 
     <tr> 
      <td width="150">
      <table width="100%" cellspacing="1"> 
       <tr bgcolor="#cccccc"><td><nobr>Thugs 4 ever</nobr></td></tr>
       <tr bgcolor="999999"><td><nobr>Protected</nobr></td></tr>
       <tr bgcolor="#cccccc"><td><nobr>Thugs Best Homie</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr>Gotta Problem!?</nobr></td></tr> 
       <tr bgcolor="#cccccc"><td><nobr>Thug's Boss</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr></nobr></td>
       </tr> 
      </table>
      </td>
      <td>
      <table width="100%" cellspacing="1"> 
       <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($thug_result)) { 
            if($bgcolor==0){$rankcolor="##cccccc";$bgcolor++;}
        elseif($bgcolor==1){$rankcolor="#999999";$bgcolor--;}
        ?><tr bgcolor="<?=$rankcolor?>"><td><a href="mobster.php?pid=<?=$info[0]?>&tru=<?=$tru?>"><?=$info[0]?></a></td><td align="right"><?=commas($info[1])?></td></tr><?
        } 
        ?>
        <tr bgcolor="999999"><td><b><?=$user_info[0]?></b></td><td align="right"><?=commas($user_info[2])?></td></tr>
      </table> 
      </td>
     </tr> 
    </table>
    </td>
   </tr>
  </table>

  </td>
 </tr>
 <tr>
  <td>&nbsp;  </td>
 </tr>
 <tr>
  <td>

  <table width="400" align="center" valign="top">
   <tr>
    <td><B>Most violent mafioso</B></td>
   </tr>
   <tr>
    <td>
    <table width="100%" cellpadding="0" cellspacing="0"> 
     <tr> 
      <td width="150">
      <table width="100%" cellspacing="1"> 
       <tr bgcolor="#cccccc"><td><nobr> Most Wanted</nobr></td></tr>
       <tr bgcolor="999999"><td><nobr>I will kill you</nobr></td></tr>
       <tr bgcolor="#cccccc"><td><nobr>Fucking Killer King</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr>Is There A Problem</nobr></td></tr> 
       <tr bgcolor="#cccccc"><td><nobr>You looking at me?</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr></nobr></td></tr> 
      </table>
      </td>
      <td>
      <table width="100%" cellspacing="1"> 
       <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($attout_result)) { 
            if($bgcolor==0){$rankcolor="##cccccc";$bgcolor++;}
        elseif($bgcolor==1){$rankcolor="#999999";$bgcolor--;}
        ?><tr bgcolor="<?=$rankcolor?>"><td><a href="mobster.php?pid=<?=$info[0]?>&tru=<?=$tru?>"><?=$info[0]?></a></td><td align="right"><?=commas($info[1])?></td></tr><?
        } 
        ?>
        <tr bgcolor="999999"><td><b><?=$user_info[0]?></b></td><td align="right"><?=commas($user_info[8])?></td></tr>
      </table> 
      </td>
     </tr> 
    </table>
    </td>
   </tr>
  </table>

  </td>
 </tr>
 <tr>
  <td>

  <table width="400" align="center" valign="top">
   <tr>
    <td><B>Most Raped Mafioso</B></td>
   </tr>
   <tr>
    <td>
    <table width="100%" cellpadding="0" cellspacing="0"> 
     <tr> 
      <td width="150">
      <table width="100%" cellspacing="1"> 
       <tr bgcolor="#cccccc"><td><nobr>Own3d!</nobr></td></tr>
       <tr bgcolor="999999"><td><nobr>TPW's lil bitch</nobr></td></tr>
       <tr bgcolor="#cccccc"><td><nobr>Rape Me</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr>MJ's My Buddy</nobr></td></tr> 
       <tr bgcolor="#cccccc"><td><nobr>I Jump Gunless</nobr></td></tr> 
       <tr bgcolor="999999"><td><nobr></nobr></td></tr> 
      </table>
      </td>
      <td>
      <table width="100%" cellspacing="1"> 
       <? 
       $bgcolor=0;
       while($info=mysql_fetch_array($attin_result)) { 
            if($bgcolor==0){$rankcolor="##cccccc";$bgcolor++;}
        elseif($bgcolor==1){$rankcolor="#999999";$bgcolor--;}
        ?><tr bgcolor="<?=$rankcolor?>"><td><a href="mobster.php?pid=<?=$info[0]?>&tru=<?=$tru?>"><?=$info[0]?></a></td><td align="right"><?=commas($info[1])?></td></tr><?
        } 
        ?>
        <tr bgcolor="999999"><td><b><?=$user_info[0]?></b></td><td align="right"><?=commas($user_info[7])?></td></tr>
      </table> 
      </td>
     </tr> 
    </table>
    </td>
   </tr>
  </table>

  </td>
 </tr>
</table>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>
<?
GAMEFOOTER();
?>