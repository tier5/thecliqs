<?
include("../setup.php");

if(!$tru){ header("Location: ../play.php"); }
if($logout==yes){ header("Location: welcome.php?pimp=play&tru=".$tru); }

function maxlength($input){
@preg_match_all('/.{1,1}/s', $input, $matches);
if(sizeof($matches[0]) > 18) {
        return bad;
}
}

function fetch ($query)
{
	$data = @mysql_fetch_row(mysql_query($query));
	return $data[0];
}

function commas ($str){
	return number_format(floor($str));
}


function fixinput ($input){
    return number_format(($input),0,",","");
}

function getlast($toget){
	$pos=strrpos($toget,".");
	$lastext=substr($toget,$pos+1);
	return $lastext;
}

function stri_replace($old, $new, $haystack){
    return preg_replace('/'.quotemeta($old).'/i', $new, $haystack);
}

function censor($input){
	return $output = str_repeat("*", (strlen($input)));
}

function filter($input){
	global $censorwords;
	foreach ($censorwords as $correct){
		$input = stri_replace($correct, censor($correct), $input);
	}
	$input = wordwrap($input, 30, " ", 1);
	$input = strip_tags($input, "");
    $input = str_replace("\n","<br>", $input);
	return $input;
}
//GAME FUNCTIONS
function hoehappy ($id)
{
global $tab;
$hap = mysql_fetch_array(mysql_query("SELECT thug,condom,crack,medicine,payout,whore FROM $tab[pimp] WHERE id='$id';"));

$thu=$hap[0];
$con=$hap[1];
$cra=$hap[2];
$med=$hap[3];
$pay=$hap[4];
$hoe=$hap[5];

if ($hoe == 0){$hoe=1;}

$tak1=(($hoe/15)+2);
if($tak1 > 50)
{
$cond=round((($con*0.75)/$hoe)*50);
if($cond >= 61){$cond=60;}
}else{
$cond=round(((($con*0.99)/$hoe)*100)-$tak1);
if($cond >= 80){$cond=100-$tak1;}
}

$thug=round((($thu*0.50)/$hoe)*50); if($thug >=5){$thug=25;}
$crac=round((($cra*0.75)/$hoe)*25); if($crac >=22){$crac=50;}
$medi=round((($med*0.99)/$hoe)*25); if($medi >=20){$medi=50;}
$payo=round(($pay*0.01)*10); if($payo >=10){$payo=10;}

$whap=round($payo+$cond+$crac+$medi+$thug);

if($whap>100){$whap=100;}

return"$whap";
}

function thughappy ($id)
{
global $tab;
$hap = mysql_fetch_array(mysql_query("SELECT glock,shotgun,uzi,ak47,weed,thug FROM $tab[pimp] WHERE id='$id';"));

$thu=$hap[5];if($thu == 0){$thu=1;}$wmax=($thu+($thu*0.25));
$glo=$hap[0];if($glo > $wmax){$glo=$wmax;}
$sho=$hap[1];if($sho > $wmax){$sho=$wmax;}
$uzi=$hap[2];if($uzi > $wmax){$uzi=$wmax;}
$ak4=$hap[3];if($ak4 > $wmax){$ak4=$wmax;}
$wee=$hap[4];

$weed=round((($wee*0.75)/$thu)*50); if($weed >=25){$weed=25;}

$gloc=round((($glo*0.86)/$thu)*80); if($gloc >=100){$gloc=100;}
$shot=round((($sho*0.88)/$thu)*80); if($shot >=100){$shot=100;}
 $uzi=round((($uzi*0.90)/$thu)*80);  if($uzi >=100) {$uzi=100;}
$ak47=round((($ak4*0.92)/$thu)*80); if($ak47 >=100){$ak47=100;}
$guns=round($gloc+$shot+$uzi+$ak47);

if($weed > $guns){$weed=$guns;}
$thughappy=round($weed+$guns);
if($thughappy>100){$thughappy=100;}
return"$thughappy";
}

function net ($id){
global $tab;
$pmp = mysql_fetch_array(mysql_query("SELECT whore,thug,dealers,bootleggers,hustlers,punks,bank,hitmen,bodyguards,money,crew FROM $tab[pimp] WHERE id='$id';"));
$net=($pmp[0]*2500+$pmp[1]*500+$pmp[2]*2000+$$pmp[3]*1500+$pmp[4]*1000+$pmp[5]*500+$pmp[6]*1+$pmp[7]*1000+$pmp[8]*500+$pmp[9]*1);
$net=number_format(($net),0,",","");
$crew_net = 0;
if ($pmp[crew] > 0) {
	$sql = mysql_query("select networth from $tab[pimp] where crew='$pmp[crew]'");
	while ($add = mysql_fetch_array($sql)) {
		$crew_net+= $add[networth];
	}
	mysql_query("update $tab[crew] set networth='$crew_net' where id='$pmp[crew]'");
}
return"$net";
}

function money ($trn)
{
global $id, $tab;
$pmp = mysql_fetch_array(mysql_query("SELECT whore,payout FROM $tab[pimp] WHERE id='$id';"));

$rand1=(rand(4, 6));
$rand2=(rand(8, 12));

$money=$trn*(rand($rand1, $rand2));
$money=($money*$pmp[0]);
$tax=round($money*($pmp[1]/100));
$money=number_format(($money-$tax)*.7,0,",","");
return"$money";
}

function condom ($condom,$hoe,$trn)
{
$usecondom=round(($condom-($hoe*0.08)*$trn));
if($usecondom <= 0){$usecondom=0;}
return"$usecondom";
}

function crack ($crack,$hoe,$trn)
{
$usecrack=round(($crack-($hoe*0.05)*$trn));
if($usecrack <= 0){$usecrack=0;}
return"$usecrack";
}

function dope ($dope,$thug,$trn)
{
$usedope=round(($dope-($thug*0.10)*$trn));
if($usedope <= 0){$usedope=0;}
return"$usedope";
}

function meds ($condom,$medicine,$hoe,$trn)
{
global $tab, $id;
if($condom <= 0)
  {
    $figrand=round(($hoe*0.012)*$trn);
    $infected=rand(0, $figrand);
    if($infected > $medicine){$infected=$medicine;}
    $medsused=$medicine-$infected;
    if($medsused <= 0){$medsused=0;}
    mysql_query("UPDATE $tab[pimp] SET medicine=$medsused WHERE id='$id'");
  }
return"$infected";
}

function nomeds ($condom,$medicine,$hoe,$trn)
{
if($condom <= 0)
  {
  if($medicine <= 0)
    {
    $killrand=round(($hoe*0.0009)*$trn);
    $killhoe=rand(0, $killrand);
    $killhoe=round($killhoe);
    if($killhoe >= $hoe){$killhoe=$hoe;}
    }
  }
return"$killhoe";
}

function countdown ($online){
global $time;

$difference=$time-$online;
$num = $difference/86400;
$days = intval($num);
$num2 = ($num - $days)*24;
$hours = intval($num2);
$num3 = ($num2 - $hours)*60;
$mins = intval($num3);
$num4 = ($num3 - $mins)*60;
$secs = intval($num4);

if($days != 0){echo"$days days, ";}
if($hours != 0){echo"$hours hours, ";}
if($mins != 0){echo"$mins mins, ";}
echo"$secs secs";
}

function countup ($online){
global $time;

$difference=$online-$time;
$num = $difference/86400;
$days = intval($num);
$num2 = ($num - $days)*24;
$hours = intval($num2);
$num3 = ($num2 - $hours)*60;
$mins = intval($num3);
$num4 = ($num3 - $mins)*60;
$secs = intval($num4);

if($days != 0){echo"$days days, ";}
if($hours != 0){echo"$hours hours, ";}
if($mins != 0){echo"$mins mins, ";}
echo"$secs secs";
}

function securemsg( $var ) 
{ 
    # Strip names and replace with sites name in messages
    $var = str_replace("cold-wars","Censored", $var);
	$var = str_replace("mafiastreetlords","Censored", $var);
    $var = str_replace("mobstar","Censored", $var);
    $var = str_replace("pimpwar","Censored", $var);
    $var = str_replace("mafia-king","Censored", $var);
    $var = str_replace("mafiarivals","Censored", $var);
    $var = str_replace("prisoninmate","Censored", $var);
    $var = str_replace("hostile-grounds","Censored", $var);
    $var = str_replace("mafia-lords","Censored", $var);
    $var = str_replace("pimpinthestreets","Censored", $var);
    $var = str_replace("gangwars","Censored", $var);
    $var = str_replace("themafiafamily","Censored", $var);
    $var = str_replace("pimpaddiction","Censored", $var);
    $var = str_replace("damafiadon","Censored", $var);
    $var = str_replace("streetgamez","Censored", $var);
    $var = str_replace("truepimpsonline","Censored", $var);
    $var = str_replace("mafia-hustle","Censored", $var);
    $var = str_replace("keeppimpin","Censored", $var);
    $var = str_replace("pimpquest","Censored", $var);
    $var = str_replace("pimpslord","Censored", $var);
    $var = str_replace("mafiacombat","Censored", $var);
    $var = str_replace("califaz-gaming","Censored", $var);
    $var = str_replace("milleniumgamingcorporation","Censored", $var);
    $var = str_replace("idlepimps","Censored", $var);
    $var = str_replace("pimpfights","Censored", $var);
    $var = str_replace("pimpriots","Censored", $var);
    $var = str_replace("truehustler","Censored", $var);
    $var = str_replace("themafiasworld","Censored", $var);
    $var = str_replace("gangwars","Censored", $var);
    $var = str_replace("mobsters-life","Censored", $var);
    $var = str_replace("thugwars","Censored", $var);
    $var = str_replace("ghettopunks","Censored", $var);
    $var = str_replace("globalpimps","Censored", $var);
    $var = str_replace("cartel-wars","Censored", $var);
    $var = str_replace("CARTEL-WARS","Censored", $var);
    $var = str_replace("CARTEL WARS","Censored", $var);
    $var = str_replace("mafiaboss","Censored", $var);
    $var = str_replace("themafiaboss","Censored", $var);
    $var = str_replace("allmafia","Censored", $var);
    $var = str_replace("pimpin.com.au","Censored", $var);
    $var = str_replace("all mafia","Censored", $var);
    $var = str_replace("allmafia","Censored", $var);
    $var = str_replace("true-don","Censored", $var);
    $var = str_replace("mafiaguys","Censored", $var);
    $var = str_replace("murderrpimp","Censored", $var);
    $var = str_replace("murdermafia","Censored", $var);
    $var = str_replace("alleywarz","Censored", $var);
    $var = str_replace("globalpimpwars","Censored", $var);
    $var = str_replace("hobowars","Censored", $var);
    $var = str_replace("globalpimps","Censored", $var);
    $var = str_replace("hobowars2","Censored", $var);
    $var = str_replace("hoodwarz","Censored", $var);
    $var = str_replace("kingofthebling","Censored", $var);
    $var = str_replace("hooliganWars","Censored", $var);
    $var = str_replace("riseofchaos","Censored", $var);
    $var = str_replace("mplaya","Censored", $var);
    $var = str_replace("mafialife","Censored", $var);
    $var = str_replace("megaplayers","Censored", $var);
    $var = str_replace("mob-style","Censored", $var);
    $var = str_replace("mobstahs","Censored", $var);
    $var = str_replace("originalmobsters","Censored", $var);
    $var = str_replace("outwar","Censored", $var);
    $var = str_replace("hood-wars","Censored", $var);
    $var = str_replace("thefamilymafia","Censored", $var);
    $var = str_replace("Thefamilymafia","Censored", $var);
    $var = str_replace("phatpimpin","Censored", $var);
    $var = str_replace("pimpcrusaders","Censored", $var);
    $var = str_replace("pimpgrounds","Censored", $var);
    $var = str_replace("pimpsworld","Censored", $var);
    $var = str_replace("pimp-area","Censored", $var);
    $var = str_replace("DaMafiaBoss","Censored", $var);
    $var = str_replace("keeppimpin","Censored", $var);
    $var = str_replace("pimpgamez","Censored", $var);
    $var = str_replace("pimpsstreet","Censored", $var);
    $var = str_replace("thugagency","Censored", $var);
    $var = str_replace("unrealpimps","Censored", $var);
    $var = str_replace("thugbattles","Censored", $var);
    $var = str_replace("trupimpin","Censored", $var);
    $var = str_replace("goldtoofpimpin","Censored", $var);
    $var = str_replace("pimpvalley","Censored", $var);
    $var = str_replace("prison-wars","Censored", $var);
    $var = str_replace("gottapimp","Censored", $var);
    $var = str_replace("pimphell","Censored", $var);
    $var = str_replace("homyz","Censored", $var);
    $var = str_replace("mafiaboss","Censored", $var);
    $var = str_replace("themafiaboss","Censored", $var);
    $var = str_replace("allmafia","Censored", $var);
    $var = str_replace("AllMafia","Censored", $var);
    $var = str_replace("murderpimp","Censored", $var);
    $var = str_replace("murdermafia","Censored", $var);
    $var = str_replace("MurderMafia","Censored", $var);
    $var = str_replace("MurderPimp","Censored", $var);
    $var = str_replace("t r u e p i m p s o n l i n e","Censored!", $var);
    $var = str_replace("nigger","Censored", $var);
    $var = str_replace("spic","Censored", $var);
    $var = str_replace("jew","Censored", $var);
    $var = str_replace("homo","Censored", $var);
    $var = str_replace("ass","walrus", $var);
    $var = str_replace("damn","drumstick", $var);
    $var = str_replace("fuck","i love", $var);
    $var = str_replace("cunt","luv", $var);
    $var = str_replace("bitch","cucumber", $var);
    $var = str_replace("dick","apple juice", $var);
    $var = str_replace("piss","dog leg", $var);
    $var = str_replace("shit","barbie doll", $var);
    $var = str_replace("gueer","fried chicken", $var);
    $var = str_replace("faggot","training wheel", $var);
    $var = str_replace("homo","toothpick", $var);
    $var = str_replace("fag","corn on teh cob", $var);
    $var = str_replace("douche","tv", $var);
    $var = str_replace("cock","tv", $var);

#Spell Checker and replace words
    $var = str_replace("teh","the", $var);

# Search for UBB tags, and make the appropriate replacements 
    $var = str_replace("\n","<br>", $var);
    $var = str_replace("[b]","<b>", $var);$var = str_replace("[/b]","</b>", $var);
    $var = str_replace("[i]","<i>", $var);$var = str_replace("[/i]","</i>", $var);
    $var = str_replace("[u]","<u>", $var);$var = str_replace("[/u]","</u>", $var);
    $var = str_replace("[bish]","<img src=", $var);$var = str_replace("[/bish]","</img>", $var);
    $var = str_replace("[thefont]","<font id=thefont>", $var);
    $var = str_replace("[/thefont]","</font>", $var);
    $var = str_replace("[invite]","<a href=", $var);
    $var = str_replace("[/invite]","</a>", $var);
    $var = str_replace("[small]","<font size=1>", $var);
	$var = str_replace("[/small]","</font>", $var);
    $var = str_replace("[center]","<center>", $var);
	$var = str_replace("[/center]","</center>", $var);
	$var = str_replace("[blink]","<blink>", $var);
	$var = str_replace("[/blink]","</blink>", $var);
	$var = str_replace("<?","", $var);
	$var = str_replace("?>","", $var);
	$var = str_replace("/input>","", $var);
	$var = str_replace("script","", $var);
	$var = str_replace("java","", $var);
    return $var; 
}



function bar($id)
{
global $tab, $tru;
$bar = @mysql_fetch_array(mysql_query("SELECT trn,money,bank,tbank,crack,condom,medicine,whore,thug,weed,networth FROM $tab[pimp] WHERE id='$id';"));
$turnupdate = @mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='turns';"));
$game = mysql_fetch_array(mysql_query("SELECT speed,maxbuild FROM $tab[game] WHERE round='$tru';"));
?>

<style>
				.graph {
					position: relative; /* IE is dumb */
					width: 100px;
					border: 1px solid #225c84;
					padding: 2px;
					margin-bottom: .5em;
					text-align: left;
					color: #fff;
					float: left;
				}
				.graph .bar {
					display: block;
					position: relative;
					background: red;
					text-align: center;
					color: #fff;
					font-size: 80%;
					height: 1em;
					line-height: 1em;
				}
				.graph .bar span { position: absolute; left: 4em; } /* This extra markup is necessary because IE doesn't want to follow the rules for overflow: visible */

</style>

<? $pimp = mysql_fetch_array(mysql_query("SELECT whappy,thappy,glock,shotgun,uzi,ak47,whore,condom,medicine,crack,thug,hitmen,bodyguards,dealers,bootleggers,hustlers,punks FROM $tab[pimp] WHERE id='$id';"));
?>
	<table style="border: solid 1px #FFFFFF;" cellpadding="2" cellspacing="2" align="center" width="500">
      <tr>
        <td><div align="center"><strong>turns</strong></div></td>
        <td><div align="center"><strong>cash</strong></div></td>
        <td><div align="center"><strong>bank</strong></div></td>
        <td><div align="center"><strong>hitmen</strong></div></td>
        <td><div align="center"><strong>bodyguards</strong></div></td>
      </tr>
      <tr>
        <td><div align="center">
          <?=commas($bar[0])?>
        </div></td>
        <td><div align="center">$<?=commas($bar[1])?>
        </div></td>
        <td><div align="center">$<?=commas($bar[2])?>
        </div></td>
        <td><div align="center">
          <?=commas($pimp[11])?>
        </div></td>
        <td><div align="center">
          <?=commas($pimp[12])?>
        </div></td>
      </tr>
      <tr>
        <td><div align="center"><strong>card.dealers</strong></div></td>
        <td><div align="center"><strong>whores</strong></div></td>
        <td><div align="center"><strong>bootleggers</strong></div></td>
        <td><div align="center"><strong>punks</strong></div></td>
        <td><div align="center"><strong>hustlers</strong></div></td>
      </tr>
      <tr>
        <td><div align="center">
          <?=commas($pimp[13])?>
        </div></td>
        <td><div align="center">
          <?=commas($bar[7])?>
        </div></td>
        <td><div align="center">
          <?=commas($pimp[14])?>
        </div></td>
        <td><div align="center">
          <?=commas($pimp[16])?>
        </div></td>
        <td><div align="center">
          <?=commas($pimp[15])?>
        </div></td>
      </tr>
      <tr>
        <td><div align="center"><strong>coke</strong></div></td>
        <td><div align="center"><strong>alcohol</strong></div></td>
        <td><div align="center"><strong>weed</strong></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"><strong>thugs</strong></div></td>
      </tr>
      <tr>
        <td><div align="center">
          <?=commas($pimp[7])?>
        </div></td>
        <td><div align="center">
          <?=commas($bar[4])?>
        </div></td>
        <td><div align="center">
          <?=commas($bar[9])?>
        </div></td>
        <td><div align="center"></div></td>
        <td><div align="center">
          <?=commas($pimp[10])?>
        </div></td>
      </tr>
      <tr>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
      </tr>
      <tr>
        <td><div align="center"><strong>Op:Happy</strong></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"><strong>Du:Happy</strong></div></td>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
      </tr>
      <tr>
        <td><div align="center">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" bordercolor="#FF0000">
            <tr>
              <td><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
                <tr height="8">
                  <td align="center" width="<?=$pimp[0]?>%" bgcolor="#FFFFFF"><font color="#990000"><span class="xsmall"><b>
                    <?=$pimp[0]?>
                    %</b></span></font></td>
                  <td width="100"></td>
                </tr>
              </table></td>
            </tr>
          </table>
        </div></td>
        <td><div align="center"></div></td>
        <td><div align="center">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" bordercolor="#FF0000">
            <tr>
              <td><table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
                <tr height="8">
                  <td align="center" width="<?=$pimp[1]?>%"  bgcolor="#FFFFFF"><font color="#990000"><span class="xsmall"><b>
                    <?=$pimp[1]?>
                    %</b></span></font></td>
                  <td width="100"></td>
                </tr>
              </table></td>
            </tr>
          </table>
        </div></td>
        <td><div align="center"></div></td>
        <td><div align="center"></div></td>
      </tr>
    </table>
	<?
}

function securepic( $var ){

     if(strstr($var,"diamondswebpages"))
       {$var="images/banned.swf";}


return $var;
}

function contacts(){
global $tab, $id, $tru;
?>
<select name=contact onChange="MM_jumpMenu('parent',this,0,this.options[this.selectedIndex].value,'_main','toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes')">
 <option>--SEE BELOW--</option><?
 $contacts = mysql_query("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='contact' ORDER BY id ASC;");
  while ($contact = @mysql_fetch_array($contacts))
        {
        $pimp2 = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$contact[0]';"));
        ?><option value="mobster.php?pid=<?=$pimp2[0]?>&tru=<?=$tru?>"><?=$pimp2[0]?></option><?
        }
?>
<option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option>
</select>
<?
}
function bitches(){
global $tab, $id, $tru;
?>
<select name=contact onChange="MM_jumpMenu('parent',this,0,this.options[this.selectedIndex].value,'_main','toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes')">
 <option>--SEE BELOW--</option><?
 $bitches = mysql_query("SELECT contact FROM $tab[clist] WHERE pimp='$id' AND type='bitch' ORDER BY id ASC;");
  while ($bitch = @mysql_fetch_array($bitches))
        {
        $pimp1 = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$bitch[0]';"));
        ?><option value="mobster.php?pid=<?=$pimp1[0]?>&tru=<?=$tru?>"><?=$pimp1[0]?></option><?
        }
 ?>
 <option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option><option></option>
 </select>
<? }

function banktrans(){

global $tab, $id, $tru;

?>

<select name=username>

 <option>--choose one--</option><?

 $pimpss = mysql_query("SELECT pimp FROM $tab[pimp] WHERE id<>'$id' ORDER BY pimp;");

  while ($pimpsss = @mysql_fetch_array($pimpss))

        {

        $pimppp = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id <> '$id' ORDER BY pimp;"));

        ?><option value="<?=$pimpsss[0]?>"><?=$pimpsss[0]?></option><?

        }

 ?>

 </select>

<?}

?>