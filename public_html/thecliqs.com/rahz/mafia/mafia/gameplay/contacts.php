<?
include("html.php");
GAMEHEADER("Allies and Enemies");

?>
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="top"><p><strong>Enemies</strong></p>
      <table width="250" cellspacing="1">
        <tr bgcolor="<?=$color?>">
          <td align="right"><?bitches();?></td>
        </tr>
      </table></td>
    <td align="center" valign="top"><p><strong>Allies</strong></p>
      <table width="250" cellspacing="1">
        <tr bgcolor="<?=$color?>">
          <td align="right"><?contacts();?></td>
        </tr>
      </table></td>
  </tr>
</table>
<p><br>
  <?=bar($id)?>
  <br>
  <?
GAMEFOOTER();
?>
</p>
