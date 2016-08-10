<?

include("html.php");



if (($usecredits != 0) && (preg_match('/^[0-9][0-9]*$/i', $usecredits)) && is_numeric($usecredits))

{

     $cre = mysql_fetch_array(mysql_query("SELECT credits,username,code FROM $tab[user] WHERE id='$id';"));

     $max = mysql_fetch_array(mysql_query("SELECT credits FROM $tab[game] WHERE round='$round';"));

     $used = mysql_fetch_array(mysql_query("SELECT credits FROM $tab[stat] WHERE round='$round' AND user='$cre[1]';"));



     $canuse=$max[0]-$used[0];



     if($usecredits > $cre[0]){$error="You dont have that many credits!";}

 elseif(!fetch("SELECT round FROM $tab[game] WHERE round='$round';"))

       {$error="That round doesnt exist!";}

 elseif(!fetch("SELECT user FROM $tab[stat] WHERE round='$round' AND user='$cre[1]';"))

       {$error="You are not in round $round";}

 elseif($usecredits > $canuse){$error="You can only add $canuse more turns to round $round!";}   

   else{

       $give_credit=$used[0]+$usecredits;

       $take_credit=$cre[0]-$usecredits;

       mysql_query("UPDATE $tab[user] SET credits='$take_credit' WHERE id='$id'");

       mysql_query("UPDATE $tab[stat] SET credits='$give_credit' WHERE user='$cre[1]' AND round='$round'");



       $findres = mysql_fetch_array(mysql_query("SELECT res FROM "."r".$round."_".$tab[pimp]." WHERE code='$cre[2]';"));

       $giveres=$findres[0]+$usecredits;

       mysql_query("UPDATE "."r".$round."_".$tab[pimp]." SET res='$giveres', transfered=transfered+$usecredits WHERE code='$cre[2]'");

       $error="$usecredits credits have been added to round $round";

       	   //log files



$userlog = mysql_fetch_array(mysql_query("SELECT username FROM $tab[user] WHERE id='$id';"));



$logpimp = $userlog[0];



$action = "transfered $usecredits to round $round";



			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','MASTERS','$logpimp','$action','$REMOTE_ADDR');");}



}



$user = mysql_fetch_array(mysql_query("SELECT username,credits,status FROM $tab[user] WHERE id='$id';"));



if (fetch("SELECT username FROM $tab[user] WHERE username='$buyfor';"))

   {$person=$buyfor;}else{$person=$user[0];}



$menu='pimp/';

secureheader();

siteheader();

?>
<div align="center"><strong>Earn GCash for credits.  <a href="onreward.php">Click Here
  </a>
</strong></div>
<div align="center">
<table border=0 cellpadding=0 cellspacing=0  width="500">

	<tr align="left" valign="top">

	<td>
	            
      <div align="left">
        <?if($error){echo"<b>$error</b><br>";}?>
        
            <b>Use Credits</b>
        
        <br>
        <b>You have <font size="+1">
        <?=commas($user[1])?>
          </font> credits!</b>
      </div>
      <form method="post" action="credits.php">
    
        <b><small>
          <?if($error){echo"$error";}else{echo"Add turns";}?>
          </small></b>
        <table cellspacing="1">
    
    <tr>
      
      <td><strong>select round:</strong></td>
  
  <td><strong>use credits:</strong></td>
  
  <td></td>
 </tr>
    
    <tr  >
      
      <td><select name="round">
        
        <option>-select one-</option>
        
        <?

    $getrounds = mysql_query("SELECT round,credits FROM $tab[game] WHERE ends>$time ORDER BY round ASC;");

     while ($round = mysql_fetch_array($getrounds))

    {?><option value="<?=$round[0]?>" selected>round <?=$round[0]?></option><?}?>
        
        </select>        </td>
  
  <td align="center"><input type="text" class="text" name="usecredits" value="0" size="5"></td>
  
  <td><input type="submit" class="button" value="add turns"></td>
 </tr>
  </table>
  
<br>
    
  <form method="post" action="credits.php"><strong>If you are purchasing turns for a friend, please enter their account username. 
    
    <input type="text" name="buyfor" class="text" size="10"> <input type="submit" class="button" value=" set ">
    
    <br>
    
    <br>
    </form>
<table width="500" align="center" cellspacing="1"  >
  
  <tr>
    
    <td colspan="4" valign="middle"><nobr> &nbsp; &nbsp; <font size="2"><b>Are you purchasing credits for
      
      <?=$person?>
      
      ?</b></font></nobr></td>
  </tr>
  
  <tr  >
    
    <td><span class="style1"><small>price</small></span></td>
  
    <td><span class="style1"><small>credits</small></span></td>
  
    <td align="center"  ><small> paypal </small></td>
    <td align="center"  >alertpay</td>
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$1000.00</span></td>
  
      <td><span class="style1">700,000</span></td>
  
      <td align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="700000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="700000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="1000.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />      </form>  </td>
      <td align="center"><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="700000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="1000"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="1000.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$600.00</span></td>
  
      <td  ><span class="style1">350,000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="350000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="350000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="600.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />  </form>      </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="350000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="600"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="600.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$400.00</span></td>
  
      <td><span class="style1">200,000</span></td>
  
      <td align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="200000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="200000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="400.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />      </form>  </td>
      <td align="center"><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="200000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="400"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="400.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$200.00</span></td>
  
      <td  ><span class="style1">95,000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="95000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="95000 Mafia GameCredits" />
        
        <input type="hidden" name="amount" value="200.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />     </form>   </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="95000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="200"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="200.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
   
      
      <td  ><span class="style1">$100.00</span></td>
  
      <td><span class="style1">45,000</span></td>
  
      <td align="center"> <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="45000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="45000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="100.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" /> </form>       </td>
      <td align="center"><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="45000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="100"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="100.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$50.00</span></td>
  
      <td  ><span class="style1">20,000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="20000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="20000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="50.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />     </form>   </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="20000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="50"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="50.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$25.00</span></td>
  
      <td  ><span class="style1">8000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="8000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="8000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="25.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />      </form>  </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="8000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="25"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="25.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$10.00</span></td>
  
      <td  ><span class="style1">3000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="3000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="3000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="10.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />    </form>    </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="3000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="10"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="10.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  
  <tr  >
    
    
      
      <td  ><span class="style1">$5.00</span></td>
  
      <td  ><span class="style1">1000</span></td>
  
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
        
        <input type="hidden" name="custom" value="<?=$person?>" />
        
        <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
        
        <input type="hidden" name="item_name" value="1000 Mafia Game Credits" />
        
        <input type="hidden" name="item_number" value="1000 Mafia Game Credits" />
        
        <input type="hidden" name="amount" value="5.00" />
        
        <input type="hidden" name="no_shipping" value="1" />
        
        <input type="hidden" name="no_note" value="1" />
        
        <input type="hidden" name="currency_code" value="USD" />
        
        <input type="hidden" name="notify_url" value="<?=$site[location]?>ipn.php" />
        
        <input type="hidden" name="return" value="<?=$site[location]?>" />
        
        <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
        
        <input type="submit" border="0" class="button" name="submit" value="purchase" />   </form>     </td>
      <td align="center"  ><form action="https://www.alertpay.com/PayProcess.aspx" method="post">
<input type="hidden" name="ap_purchasetype" value="item-goods"/>
<input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/> 
<input type="hidden" name="ap_itemname" value="1000 Account credits"/> 
<input type="hidden" name="ap_currency" value="USD"/> 
<input type="hidden" name="ap_returnurl" value="<?=$site[location]?>/"/> 
<input type="hidden" name="ap_itemcode" value="1"/> 
<input type="hidden" name="ap_quantity" value="1"/> 
<input type="hidden" name="apc_1" value="<?=$person?>"/>
<input type="hidden" name="apc_2" value="credits"/>
<input type="hidden" name="apc_3" value="5"/>
<input type="hidden" name="ap_description" value="Credits that dont expire after rounds"/> 
<input type="hidden" name="ap_amount" value="5.00"/> 
<input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>"/> 
<input type="submit" border="0" class="button" name="submit" value="purchase" />
</form></td>
    
  </tr>
  </table>
  
<br />
	    
  <br />

<table width="500" align="center" cellspacing="1"  >
  <tr>
    <td colspan="5" valign="middle"><nobr> &nbsp; &nbsp; <font size="2"><b>Are you purchasing a subscription for 
      <?=$person?>
    ?</b></font></nobr></td>
  </tr>
  <tr  >
    <td><span class="style1"><small>price</small></span></td>
    <td><span class="style1"><small>credits</small></span></td>
    <td align="center"  ><small>purchase</small></td>
    <td align="center"><small> paypal </small></td>
    <td align="center">alertpay</td>
  </tr>
  <tr  >
    <td><font size="2">$45.00</font></td>
    <td><font size="2">100 turns per 10 min, Max hold 10,000   (turbo) </font></td>
    <td align="center"><font size="2">10 days</font></td>
    
      <td align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd2" value="_xclick" />
          <input type="hidden" name="custom" value="<?=$person?>" />
          <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
          <input type="hidden" name="item_name" value="45.00 subscription" />
          <input type="hidden" name="item_number" value="45.00 subscription" />
          <input type="hidden" name="amount" value="45.00" />
          <input type="hidden" name="no_shipping" value="1" />
          <input type="hidden" name="no_note" value="1" />
          <input type="hidden" name="currency_code" value="USD" />
          <input type="hidden" name="notify_url" value="<?=$site[location]?>ipnsub.php" />
          <input type="hidden" name="return" value="<?=$site[location]?>" />
          <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
          <input type="submit" border="0" class="button" name="submit" value="purchase" />     </form> </td>
      <td align="center"><form method="post" action="https://www.alertpay.com/PayProcess.aspx" >
 <input type="hidden" name="ap_purchasetype" value="subscription"/>
 <input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/>
 <input type="hidden" name="ap_itemname" value="Subscription"/>
 <input type="hidden" name="ap_currency" value="USD"/>
 <input type="hidden" name="ap_returnurl" value="<?=$site[location]?>play.php"/>
 <input type="hidden" name="ap_itemcode" value="45"/>
 <input type="hidden" name="ap_quantity" value="1"/>
 <input type="hidden" name="ap_description" value="Subscription for better features"/>
 <input type="hidden" name="ap_amount" value="45.00"/>
 <input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>play.php"/>
<input type="submit" border="0" class="button" name="submit" value="purchase" />
 <input type="hidden" name="apc_1" value="<?=$person?>"/>
 <input type="hidden" name="apc_2" value="subscription"/>
 <input type="hidden" name="ap_timeunit" value="day"/>
 <input type="hidden" name="ap_periodlength" value="10"/>
</form></td>
    
  </tr>
  <tr  >
    <td><font size="2">$30.00</font></td>
    <td><font size="2">65 turns per 10 min, Max hold 8,000   (gold) </font></td>
    <td align="center"><font size="2">10 days</font></td>
    
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
          <input type="hidden" name="custom" value="<?=$person?>" />
          <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
          <input type="hidden" name="item_name" value="30.00 subscription" />
          <input type="hidden" name="item_number" value="30.00 subscription" />
          <input type="hidden" name="amount" value="30.00" />
          <input type="hidden" name="no_shipping" value="1" />
          <input type="hidden" name="no_note" value="1" />
          <input type="hidden" name="currency_code" value="USD" />
          <input type="hidden" name="notify_url" value="<?=$site[location]?>ipnsub.php" />
          <input type="hidden" name="return" value="<?=$site[location]?>" />
          <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
          <input type="submit" border="0" class="button" name="submit" value="purchase" />   </form>   </td>
      <td align="center"  ><form method="post" action="https://www.alertpay.com/PayProcess.aspx" >
 <input type="hidden" name="ap_purchasetype" value="subscription"/>
 <input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/>
 <input type="hidden" name="ap_itemname" value="Subscription"/>
 <input type="hidden" name="ap_currency" value="USD"/>
 <input type="hidden" name="ap_returnurl" value="<?=$site[location]?>play.php"/>
 <input type="hidden" name="ap_itemcode" value="30"/>
 <input type="hidden" name="ap_quantity" value="1"/>
 <input type="hidden" name="ap_description" value="Subscription for better features"/>
 <input type="hidden" name="ap_amount" value="30.00"/>
 <input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>play.php"/>
<input type="submit" border="0" class="button" name="submit" value="purchase" />
 <input type="hidden" name="apc_1" value="<?=$person?>"/>
 <input type="hidden" name="apc_2" value="subscription"/>
 <input type="hidden" name="ap_timeunit" value="day"/>
 <input type="hidden" name="ap_periodlength" value="10"/>
</form></td>
    
  </tr>
  <tr  >
    <td><font size="2">$15.00</font></td>
    <td><font size="2">45 turns per 10 min,   Max hold 4,500 (silver) </font></td>
    <td align="center"><font size="2">10 days</font></td>
    
      <td align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
          <input type="hidden" name="custom" value="<?=$person?>" />
          <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
          <input type="hidden" name="item_name" value="15.00 subscription" />
          <input type="hidden" name="item_number" value="15.00 subscription" />
          <input type="hidden" name="amount" value="15.00" />
          <input type="hidden" name="no_shipping" value="1" />
          <input type="hidden" name="no_note" value="1" />
          <input type="hidden" name="currency_code" value="USD" />
          <input type="hidden" name="notify_url" value="<?=$site[location]?>ipnsub.php" />
          <input type="hidden" name="return" value="<?=$site[location]?>" />
          <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
          <input type="submit" border="0" class="button" name="submit" value="purchase" />    </form>  </td>
      <td align="center"><form method="post" action="https://www.alertpay.com/PayProcess.aspx" >
 <input type="hidden" name="ap_purchasetype" value="subscription"/>
 <input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/>
 <input type="hidden" name="ap_itemname" value="Subscription"/>
 <input type="hidden" name="ap_currency" value="USD"/>
 <input type="hidden" name="ap_returnurl" value="<?=$site[location]?>play.php"/>
 <input type="hidden" name="ap_itemcode" value="15"/>
 <input type="hidden" name="ap_quantity" value="1"/>
 <input type="hidden" name="ap_description" value="Subscription for better features"/>
 <input type="hidden" name="ap_amount" value="15.00"/>
 <input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>play.php"/>
<input type="submit" border="0" class="button" name="submit" value="purchase" />
 <input type="hidden" name="apc_1" value="<?=$person?>"/>
 <input type="hidden" name="apc_2" value="subscription"/>
 <input type="hidden" name="ap_timeunit" value="day"/>
 <input type="hidden" name="ap_periodlength" value="10"/>
</form></td>
    
  </tr>
  <tr  >
    <td><font size="2">$5.00</font></td>
    <td><font size="2">35 turns per 10 min,   Max hold 3,500 (bronze) </font></td>
    <td align="center"><font size="2">10 days</font></td>
    
      <td align="center"  ><form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_xclick" />
          <input type="hidden" name="custom" value="<?=$person?>" />
          <input type="hidden" name="business" value="<?=$paypal_email_address?>" />
          <input type="hidden" name="item_name" value="5.00 subscription" />
          <input type="hidden" name="item_number" value="5.00 subscription" />
          <input type="hidden" name="amount" value="5.00" />
          <input type="hidden" name="no_shipping" value="1" />
          <input type="hidden" name="no_note" value="1" />
          <input type="hidden" name="currency_code" value="USD" />
          <input type="hidden" name="notify_url" value="<?=$site[location]?>ipnsub.php" />
          <input type="hidden" name="return" value="<?=$site[location]?>" />
          <input type="hidden" name="cancel_return" value="<?=$site[location]?>" />
          <input type="submit" border="0" class="button" name="submit" value="purchase" /> </form>     </td>
      <td align="center"  ><form method="post" action="https://www.alertpay.com/PayProcess.aspx" >
 <input type="hidden" name="ap_purchasetype" value="subscription"/>
 <input type="hidden" name="ap_merchant" value="<?=$paypal_email_address?>"/>
 <input type="hidden" name="ap_itemname" value="Subscription"/>
 <input type="hidden" name="ap_currency" value="USD"/>
 <input type="hidden" name="ap_returnurl" value="<?=$site[location]?>play.php"/>
 <input type="hidden" name="ap_itemcode" value="5"/>
 <input type="hidden" name="ap_quantity" value="1"/>
 <input type="hidden" name="ap_description" value="Subscription for better features"/>
 <input type="hidden" name="ap_amount" value="5.00"/>
 <input type="hidden" name="ap_cancelurl" value="<?=$site[location]?>play.php"/>
<input type="submit" border="0" class="button" name="submit" value="purchase" />
 <input type="hidden" name="apc_1" value="<?=$person?>"/>
 <input type="hidden" name="apc_2" value="subscription"/>
 <input type="hidden" name="ap_timeunit" value="day"/>
 <input type="hidden" name="ap_periodlength" value="10"/>
</form></td>
    
  </tr>
</table>
<br />

<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	  <div align="center">
	  <table width="300" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;<img src="https://www.paypal.com/en_US/i/logo/logo_ccVisa.gif" width="35" height="21" /></td>
        <td>&nbsp;<img src="https://www.paypal.com/en_US/i/logo/logo_ccMC.gif" width="35" height="21" /></td>
        <td>&nbsp;<img src="https://www.paypal.com/en_US/i/logo/logo_ccAmex.gif" width="35" height="21" /></td>
        <td>&nbsp;<img src="https://www.paypal.com/en_US/i/logo/logo_ccDiscover.gif" width="35" height="21" /></td>
      </tr>
    </table></div>
    <p>Don't Have a Pay-Pal account. Pay with credit card Secure Checkout. Click Purchase and see options</p>
      <p> <font color="blue"><b>Supporter Features :</b></font> <br />
        There are different supporter features currently installed and more 
        
        soon to come. Currently supporters can upload crew icons up to 10,000kb 
        
        letting them upload a much higher size of icon. They can Play in 
        
        the supporter only round obviously. Things available and to come 
        
        will be Name changes while in game, Attack resets, hide Mode, And 
        
        much more. Always looking for more ideas in the forums under Suggestions <a href="<?=$paypal_email_address?>" target="_blank">CLICK HERE</a> </p>
      <p><font color="blue"><b>Paying with E-Checks:</b></font> <br />
        If you purchase credits with the e-check method, it will take up to 4 business days to be process. This means you will not receive your credits instant. If you do not want to wait for your credits, make sure there is money in your Paypal account before you purchase credits. <br />
  <br />
  <font color="blue"><b>What are credits?:</b></font> <br />
        Credits are what help keep this site running; bandwidth and server fees are not cheap. Not to mention this also pays for the prizes we will offer and the items that will be found in the store. We are not exactly just pocketing the money. This will also help us to upgrade to better servers. Therefore, instead of advertising, loading banners, spy ware, etc all over the site, we sell credits to keep us giving you great entertainment. Credits are basically considered turns, once you purchase credits, you may add them to the games to use as turns. If you purchase credits, your account status also changed from normal, to supporter and stays at supporter for 90 days. With supporter status, you can join the supporters round, and win prizes. Supporter status also gives you more available options in the game, like being able to upload a bigger size crew icon. <br />
  <br />
  <font color="blue"><b>How many credits can I buy?:</b></font> <br />
        You can buy up to as many credits as you wish, they will always be in your account for you to use for later games. If you want to see how many credits you can add to a round, click on play, and look for the credit add-on.</p></td>
  </tr>
</table>
<p><BR>
</p></td></tr>
</table>

     </td>

    </tr>

   </table>
</div>
<?

sitefooter();

?>