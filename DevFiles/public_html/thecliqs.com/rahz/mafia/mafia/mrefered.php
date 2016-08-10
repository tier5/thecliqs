<?
include("html.php");


admin();
secureheader();
siteheader();
?>

<?
if($pidd != ""){
	mysql_query("UPDATE $tab[user] SET credits=credits+50, referrals=referrals+1 WHERE username='$pidd'");
$error="ID# $pidd  has been given 50 credits";
}
?><body>
<div align="center"><br>
    <font color="red"><b>
    <?=$error?>
      </b></font>
  <br>
  <form method="post" action="ranks.php?c=&tru=<?=$tru?>">
  <table width="90%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td  align="left"><font color="red">ID# <small></small></font> </td>
    <td  align="left"><font color="red">Mafia boss <small></small></font> </td>
    <td  align="center">&nbsp;</td>
    <td  align="right"><font color="red">referedby id#</font></td>
   </tr>
  <?
if((!$r) || ($r < 21))
{
$get = mysql_query("SELECT id,username,status,statusexpire,referredby FROM $tab[user] WHERE id>='1' ORDER BY id DESC limit 5000;");
while ($t10 = mysql_fetch_array($get))
      {
	  		 
      $online=$time-$t10[5];
      if ($online < 600){$on="<img src=$site[img]online1.gif width=16 height=16 align=absmiddle>";}else{$on='';}

            if($id == $t10[0]){$rankcolor = "#FFFFFF";}
        elseif($rankstart==0){$rankcolor="#CCCCCC";$rankstart++;}
        elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

       ?>
    <tr onMouseOver="style.backgroundColor='#FFFFFF'" onMouseOut="style.backgroundColor='<?=$rankcolor?>'" bgcolor="<?=$rankcolor?>">
      <td><font color="white"><nobr><a href="pimpprofile.php?pid=<?=$t10[0]?>"><?=$t10[0]?></a>
      </nobr></font></td>
             <td><font color="red"><nobr><a href="pimpprofile.php?pid=<?=$t10[1]?>"><? if($t10[2] == banned){?><font color="blue"><?}?><?=$t10[1]?><? if($t10[2] == banned){?></font><?}?></a>
      </nobr></font></td>
      <td align="center">&nbsp;</td>
      <td align="right"><font color="red">
          <?=$t10[4]?> 
      </font></td>
       </tr>
    <?$selffont="";
       }
}?>
    </table>
  <br>
</div>
<?
sitefooter();
?>