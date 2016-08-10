<?
include("html.php");


$game = mysql_fetch_array(mysql_query("SELECT round,gamename,free1,free2,free3,free4,free5,free6,free7,free8,free9,free10,sup_11,sup_12,sup_13,sup_14,sup_15,sup_16,sup_17,sup_18,sup_19,sup_110,sup_21,sup_22,sup_23,sup_24,sup_25,sup_26,sup_27,sup_28,sup_29,sup_210,sup_31,sup_32,sup_33,sup_34,sup_35,sup_36,sup_37,sup_38,sup_39,sup_310,du1,du2,du3,du4,du5,du6,du7,du8,du9,du10,op1,op2,op3,op4,op5,op6,op7,op8,op9,op10,c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,fdu1,fdu2,fdu3,fdu4,fdu5,fdu6,fdu7,fdu8,fdu9,fdu10,starts,ends,cash11,cash22,cash33 FROM $tab[game] WHERE round='$tru';"));
$round=$game[0];

$bling = mysql_fetch_row(mysql_query("SELECT sum(amount), sum(fee) FROM $tab[paypal] WHERE datebought>='$game[starts]' AND datebought<='$game[ends]';"));

$balance=($bling[0]-$bling[1]);
$balan=($bling[0]-$bling[1]);
if($balance<0){$balance=0;}
if($balance>2000){$balance=$balance-2000;}else{$balance=0;}

$balance11=($balance*.50);
$balance1=round($balance11+$game[cash33]);

$balance22=($balance*.50);
$balance2=round($balance22+$game[cash22]);

$balance33=($game[cash11]);
$balance3=round($balance33);

GAMEHEADER("Prizes");
?>
<div align="center">
  <p><b>Round:
    <?=$round?>
    <b>:: Name:
    <?=($game[1])?>
    </b><br />
    <b><br />
    Prizes
    </b></p>
  <table width="500" border="0">
  <tr>
    <td >&nbsp;</td>
    <td bordercolor="#350000" ><div align="center"><strong>Supporter Prizes</strong></div></td>
    <td bordercolor="#350000" ><div align="center"><strong>DU-Killer Supporter </strong></div></td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>1st</strong></td>
    <td >
      <div align="center">$
        <?=commas($game[cash22])?> 
          <br />
        <?=commas($game[22])?> 
        </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[42])?>    </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>2nd</strong></td>
    <td >
      <div align="center">
        <?=commas($game[23])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[43])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>3rd</strong></td>
    <td >
      <div align="center">
        <?=commas($game[24])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[44])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>4th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[25])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[45])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>5th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[26])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[46])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>6th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[27])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[47])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>7th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[28])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[48])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>8th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[29])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[49])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>9th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[30])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[50])?>     </td>
  </tr>
  <tr>
    <td bordercolor="#350000" ><strong>10th</strong></td>
    <td >
      <div align="center">
        <?=commas($game[31])?>
       </div></td>
    <td align="center" bordercolor="#350000" > 
        <?=commas($game[51])?>     </td>
  </tr>
</table>
  <br />
  <table width="500" border="0" cellspacing="0" bordercolor="#350000">
  <tr>
    <td >&nbsp;</td>
    <td ><div align="center"><strong>Free</strong></strong></div></td>
    <td ><div align="center"><strong>DU-Killer Free</strong></div></td>
    <td ><div align="center"><strong>Family</strong></div></td>
  </tr>
  <tr>
    <td ><strong>1st</strong></td>
    <td >
        <div align="center">
          <?=commas($game[2])?>    
        </div></td>
    <td align="center" > 
        <?=commas($game[72])?>    </td>
    <td align="center" > 
        <?=commas($game[62])?>    </td>
  </tr>
  <tr>
    <td ><strong>2nd</strong></td>
    <td >
        <div align="center">
          <?=commas($game[3])?>    
        </div></td>
    <td align="center" > 
        <?=commas($game[73])?>    </td>
    <td align="center" > 
        <?=commas($game[63])?>    </td>
  </tr>
  <tr>
    <td ><strong>3rd</strong></td>
    <td >
        <div align="center">
          <?=commas($game[4])?>    
        </div></td>
    <td align="center" > 
        <?=commas($game[74])?>    </td>
    <td align="center" > 
        <?=commas($game[64])?>    </td>
  </tr>
  <tr>
    <td ><strong>4th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[5])?>    
        </div></td>
    <td align="center" > 
        <?=commas($game[75])?>    </td>
    <td align="center" > 
        <?=commas($game[65])?>    </td>
  </tr>
  <tr>
    <td ><strong>5th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[6])?>    
        </div></td>
    <td align="center" > 
        <?=commas($game[76])?>    </td>
    <td align="center" > 
        <?=commas($game[66])?>    </td>
  </tr>
  <tr>
    <td ><strong>6th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[7])?>    
        </div></td>
    <td align="center" > 
      <?=commas($game[77])?>    </td>
    <td align="center" > 
      <?=commas($game[67])?>    </td>
  </tr>
  <tr>
    <td ><strong>7th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[8])?>    
        </div></td>
    <td align="center" > 
      <?=commas($game[78])?>    </td>
    <td align="center" > 
      <?=commas($game[68])?>    </td>
  </tr>
  <tr>
    <td ><strong>8th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[9])?>    
        </div></td>
    <td align="center" > 
      <?=commas($game[79])?>    </td>
    <td align="center" > 
      <?=commas($game[69])?>    </td>
  </tr>
  <tr>
    <td ><strong>9th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[10])?>    
        </div></td>
    <td align="center" > 
      <?=commas($game[80])?>    </td>
    <td align="center" > 
      <?=commas($game[70])?>    </td>
  </tr>
  <tr>
    <td ><strong>10th</strong></td>
    <td >
        <div align="center">
          <?=commas($game[11])?>    
        </div></td>
    <td align="center" > 
      <?=commas($game[81])?>    </td>
    <td align="center" > 
      <?=commas($game[71])?>    </td>
  </tr>
</table>
</div><br>
<?=bar($id)?>
<br></div>
<?
GAMEFOOTER();
?>