<?php 
ob_implicit_flush();

include("html.php");
error_reporting(E_ALL-E_WARNING-E_NOTICE);

$menu='pimp/';
admin();
secureheader();
siteheader();
?>
<?php
$stat = mysql_fetch_array(mysql_query("select * from users where id='$id'"));
if ($stat[status] != admin) {
print "You're not an admin.";
exit;
}
?>

<center>
  <strong>Best to do this 1 at a time so not to drag down the server..</strong>
  <br />
  <br />
  <table width="90%" border="0"><form action="cronjob1234567891.php" method="post">
    <? /*<tr>
      <td>Unbank &amp; Sell for round<br />
        <input type="text" name="round1"/>
        <input name="Submit32" type="submit" value="Submit" /></td>
      <td>-Wait for page to show done in the process area for the page.</td>
    </tr> */?>
    <tr>
      <td>Update Nets / Rank for round <br />
      Round#:
      <input name="round2" type="text"/>
        <input name="Submit3" type="submit" value="Submit" />
        <br />
        <br /></td><td>-Wait for page to show done in the process area for the page. </td>
    </tr>
    <tr>
      <td>Update Crew Nets for round<br />
        Round#:
        <input name="round3" type="text"/>
        <input name="Submit2" type="submit" value="Submit" />
        <br />
        <br /></td>
      <td>-Wait for page to show done in the process area for the page. </td>
    </tr>
    <tr>
      <td>Update Crew Ranks for round<br />
        Round#:
        <input name="round4" type="text" id="round4"/>
        <input name="Submit4" type="submit" value="Submit" />
        <br />
        <br /></td>
      <td>-Wait for page to show done in the process area for the page. </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr></form>
  </table>



<table width="90%" border="0">
  <form action="cronjob1234567891.php" method="post">
  <tr><td colspan="2">Auto hand out credits</td></tr>
  <tr>
    <td width="25%">Round#:
      <input name="round5" type="text" id="round5"/>
      <br />
      <br /></td><td width="16%"><input name="Submit5" type="submit" id="Submit5" value="Submit" /></td>
    <td width="59%">-Wait for page to show done in the process area for the page. </td>
  </tr>
  <tr><td colspan="2">Auto hand out awards</td></tr>
  <tr><td><input name="round6" type="text" id="round6"/></td>
  <td><input name="Submit6" type="submit" id="Submit6" value="Submit" /></td>
    <td>-Wait for page to show done in the process area for the page. </td>
  </tr>
  
  </form>
</table>
</center>
<?
if($round1 != ""){
     
    echo '<br /><br />Unbank & Sell for round<br />';   
	//mysql connect and function library load
	include("setup.php");
	//get all games that had ended
	#$games = mysql_query("select * from games where ends<".time()."and round>0 order by round asc");
	$games = mysql_query("select * from games where ends<".time()." and round='$round1' order by round asc");
	//cycle through every game
	while ($game = mysql_fetch_assoc($games)) { 
		//THE UPDATE ROUNDS QUERY WILL GO HERE
		//get all the players in that game
		$users = mysql_query("select * from r".$game[round]."_$tab[pimp] where status!='banned'");
		//cycle through the players and update every one of them
		while ($user = mysql_fetch_assoc($users)) {
			//get the new net
			$pmp = mysql_fetch_array(mysql_query("SELECT bank,money,networth FROM r".$game[round]."_pimp WHERE id='$user[id]';"));
            $net=($pmp[0]*1+$pmp[1]*1+$pmp[2]*1);
			$net = number_format(($net),0,",","");
			//update new net
			mysql_query("update r".$game[round]."_$tab[pimp] set networth=$net where id=".$user[id]);
            echo $user['pimp'].' got networth '.$net.'<br />'; 
		}
		//all users will be updated here
	}
}
?>

<?
if($round2 != ""){
include("setup.php");
$getgames = mysql_query("SELECT round,first,second,third,forthnfifth,sixththrewtenth,tophoekiller,topthugkiller,crewprize,ends FROM $tab[game] WHERE round='$round2' ORDER BY round ASC;");
while ($game = mysql_fetch_array($getgames))
{
       echo '<br /><br />UPGRADE LOCAL RANKS<br />'; 
       //UPGRADE LOCAL RANKS
       $citys = mysql_query("SELECT id FROM r$game[0]_$tab[city] WHERE id>0 ORDER BY id DESC;");
        while ($city = mysql_fetch_array($citys))
              {
              $locals = mysql_query("SELECT id, pimp FROM r$game[0]_$tab[pimp] WHERE status!='banned' AND city='$city[0]' ORDER BY networth DESC;");
              $rank = 0;
               while ($local = mysql_fetch_array($locals))
                     {
           	         $rank++;
	                 mysql_query("UPDATE r$game[0]_$tab[pimp] SET rank=$rank WHERE id=$local[0];");
                     echo $local['pimp'].' got rank '.$rank.'<br />';
                     }
              }

       echo '<br /><br />UPGRADE NATIONAL RANKS<br />';       
       //UPGRADE NATIONAL RANKS
       $nations = mysql_query("SELECT id, pimp FROM r$game[0]_$tab[pimp] WHERE status!='banned' ORDER BY networth DESC;");
       $urank = 0;
        while ($nation = mysql_fetch_array($nations))
              {
	          $urank++;
	          mysql_query("UPDATE r$game[0]_$tab[pimp] SET nrank=$urank WHERE id=$nation[0];");
               echo $nation['pimp'].' got rank '.$urank.'<br />';
              }

       mysql_query("UPDATE r$game[0]_$tab[pimp] SET nrank='$urank', rank='$rank' WHERE status='banned';");

}
}
	#added 11.30.2006
	require_once("db.php");
	
	if (isset($_POST['round3'])) {
		$round = $_POST['round3'];
	
		if ($round != "") {
		    
			$tab = array();
			$tab['crew'] = 'r'.$round.'_crew';#crews table name
			$tab['pimp'] = 'r'.$round.'_pimp';#pimps table name
			
			# Create global objects
		    $db = new DB();
		    $db->connect("localhost",#mysql server
						 "3306",#port
						 "USER",#user
						 "PASS",#password
						 "USER_DB");#database name
						 
			$crews = $db->getAllRows("SELECT id, cbank, name FROM ".$tab['crew']);
		
            echo '<br /><br />Update Crew Nets for round<br />';
			foreach ($crews as $crew){
				$networth = $db->getRow("SELECT SUM(networth) as nw
										   FROM ".$tab['pimp']."
										  WHERE crew = " . $crew['id'] . " 
										    AND status != 'banned'");
						
				$networth['nw'] = $networth['nw'] + $crew['cbank'];							
				if ($networth['nw']) {	
				    $db->updateField($tab['crew'], "id", $crew['id'], "networth", $networth['nw']);
                    echo $crew['name'].' got networth '.$networth['nw'].'<br />';
				}							
			}
			
			}
		}
#added by lowridertj 12.09.06
	if (isset($_POST['round4'])) {
		$round = $_POST['round4'];
require_once("setup.php");
	
		if ($round != "") {

    echo '<br /><br />Update Crew Ranks for round<br />';        
	$getgames = mysql_query("SELECT round, attindown, attoutdown, gamename FROM $tab[game] WHERE round='$round' ORDER BY round ASC;");
	while ($game = mysql_fetch_array($getgames))
	{
	
	$getcrewranks = mysql_query("SELECT id, name FROM r$game[0]_$tab[crew] WHERE id>0 ORDER BY networth DESC;");
	$urank = 0;
	while ($crws = mysql_fetch_array($getcrewranks))
	      { 
		  $urank++;
		      mysql_query("UPDATE r$game[0]_$tab[crew] SET rank=$urank WHERE id='$crws[0]';");
              echo $crws['name'].' got rank '.$urank.'<br />'; 
	      }
	}
	}
	}

	#added 11.30.2006
    require_once("credit_.php");
	
	
	$host = 'localhost'; 
	$user = 'USER'; 
	$pass = 'PASS';
	$database = 'USER_DB'; 
    

				
	# Create global objects
    $db = new DB();
    $db->connect($host,#mysql server
				 "3306",#port
				 $user,#user
				 $pass,#password
				 $database);#database name
		
	
		
		if (isset($_POST['round5'])) {
		    	
			$round = $_POST['round5'];
	        #get round prizes
	        $prizes = $db->getRow("SELECT * FROM games WHERE round='$round'");					
			#get top 10 free
			
			$tab = array();
			$tab['crew'] = 'r'.$round.'_crew';#crews table name
			$tab['pimp'] = 'r'.$round.'_pimp';#pimps table name
			$pimp = 'pimp';
			$crew = 'crew';
			
			if ($round != "") {
			
			echo '<br /><br />Top 10 Free<br />';
			$top10free = $db->getAllRows("SELECT id,
												 pimp,
												 networth,
												 nrank,
												 crew,
												 code,
												 status 
										    FROM "."r".$round."_".$pimp." 
										   WHERE rank>0 
										     AND status!='supporter' 
											 AND status!='banned'  
										ORDER BY nrank ASC 
										   LIMIT 10");
										   
			foreach ($top10free as $k => $user){
				$rank = $k + 1;
				credit_set($user['code'], $prizes['free'.$rank]);
                echo $user['pimp'].' got '.$prizes['free'.$rank]."<br />";
			}	
			
            
            echo '<br /><br />Top 10 supporter level 1<br />';
			#get top 10 supporter level 1
			$top10supp1= $db->getAllRows("SELECT id,
												 pimp,
												 networth,
												 nrank,
												 crew,
												 code,
												 status
										    FROM "."r".$round."_".$pimp." 
										   WHERE rank>0 
										     AND status='supporter' 
										ORDER BY nrank ASC 
										   LIMIT 10");
										   
			foreach ($top10supp1 as $k => $user){
				$rank = $k + 1;
				credit_set($user['code'], $prizes['sup_2'.$rank]);
                 echo $user['pimp'].' got '.$prizes['sup_2'.$rank]."<br />";
			}							   

            echo '<br /><br />Top 10 du killers supporter<br />'; 
			#get top 10 du killers supporter
			$dukillerssup = $db->getAllRows("SELECT id,
													pimp,
													networth,
													thugk,
													whorek,
													status,
													code 
											   FROM "."r".$round."_".$pimp." 
											  WHERE status='supporter' 
										   ORDER BY thugk DESC 
										      LIMIT 10");
			foreach ($dukillerssup as $k => $user){
				$rank = $k + 1;
				credit_set($user['code'], $prizes['du'.$rank]);
                echo $user['pimp'].' got '.$prizes['du'.$rank]."<br />";
			}							  
			
            echo '<br /><br />Top 10 du killers free<br />';
			#get top 10 du killers free
			$dukillersfree = $db->getAllRows("SELECT id,
													 pimp,
													 networth,
													 thugk,
													 whorek,
													 status,
													 code 
											    FROM "."r".$round."_".$pimp." 
											   WHERE status='normal' 
											ORDER BY thugk DESC 
											   LIMIT 10");
											   
			foreach ($dukillersfree as $k => $user){
				$rank = $k + 1;
				credit_set($user['code'], $prizes['fdu'.$rank]);
                echo $user['pimp'].' got '.$prizes['fdu'.$rank]."<br />";  
			}
			
            echo '<br /><br />Top 10 families members<br />'; 
			#get top 10 families members			
			$fams = $db->getAllRows("SELECT id,
											rank 
									   FROM "."r".$round."_".$crew." 
									  WHERE rank >=1
									  	AND rank <= 10
								   ORDER BY rank ASC");
								   
			foreach ($fams as $k => $fam){
				$members = $db->getAllRows("SELECT id,
												   pimp,
												   networth,
												   code 
											  FROM "."r".$round."_".$pimp." 
											 WHERE crew=".$db->quoteSql($fam['id'])."
										  ORDER BY networth DESC");
				
				foreach ($members as $user){
					$rank = $k + 1;
					credit_set($user['code'], $prizes['c'.$rank]);
                    echo $user['pimp'].' got '.$prizes['c'.$rank]."<br />";
				}		 
			}
                        
          
            echo '<br /><br />Top 3 subscribers general<br />';
            $pimps   = $db->getAllRows("SELECT id,
                                               pimp,
                                               networth,
                                               code 
                                          FROM ".$tab['pimp']." 
                                         WHERE subscribe >= 1
                                      ORDER BY networth DESC
                                         LIMIT 10");
             
             foreach ($pimps as $k => $pimp1):
                     credit_set($pimp1['code'], $subscribers_credits[$k]);
                     echo $pimp1['pimp'].' got '.$subscribers_credits[$k]."<br />";
             endforeach;
            
            
            
			echo 'Done';
			    
			}
			
			
		}
		
		
		if (isset($_POST['round6'])) {
		    $round6 = $_POST['round6'];
			
			if ($round6 != "") {
                echo '<br /><br />Top 3 du killers free<br />';    
			    #get top 3 du killers free
				$dufree = 		 $db->getAllRows("SELECT id,
														 pimp,
														 networth,
														 thugk,
														 whorek,
														 status,
														 code 
												    FROM "."r".$round6."_".$pimp." 
										     AND status!='supporter' 
											 AND status!='banned'
												ORDER BY thugk DESC 
												   LIMIT 3");
												   
				award_set($dufree[0]['code'], "goldglock");
                echo $dufree[0]['pimp'].' got goldglock<br />';
				award_set($dufree[1]['code'], "silverglock");
                echo $dufree[1]['pimp'].' got silverglock<br />';
				award_set($dufree[2]['code'], "bronzeglock");
                echo $dufree[2]['pimp'].' got bronzeglock<br />';
			
            echo '<br /><br />Top 3 rank free<br />';
			#get top 3 rank free
			$rankfree = $db->getAllRows("SELECT id,
												 pimp,
												 networth,
												 nrank,
												 crew,
												 code,
												 status 
										    FROM "."r".$round6."_".$pimp." 
										   WHERE rank>0 
										     AND status!='supporter' 
											 AND status!='banned'
										ORDER BY nrank ASC 
										   LIMIT 3");
			award_set($rankfree[0]['code'], "goldfree");
            echo $rankfree[0]['pimp'].' got goldfree<br />';
			award_set($rankfree[1]['code'], "silverfree");
             echo $rankfree[1]['pimp'].' got silverfree<br />'; 
			award_set($rankfree[2]['code'], "bronzefree");
			 echo $rankfree[2]['pimp'].' got bronzefree<br />';
            
            echo '<br /><br />Top 3 supporter rank<br />';             
			#get top 3 supporter level 1 rank 
			$ranksup1   = $db->getAllRows("SELECT id,
												 pimp,
												 networth,
												 nrank,
												 crew,
												 code,
												 status
										    FROM "."r".$round6."_".$pimp." 
										   WHERE rank>0 
										     AND status='supporter'  
										ORDER BY nrank ASC 
										   LIMIT 3");
										   
			award_set($ranksup1[0]['code'], "goldbrick");
             echo $ranksup1[0]['pimp'].' got goldbrick<br />'; 
			award_set($ranksup1[1]['code'], "silverbrick");
             echo $ranksup1[1]['pimp'].' got silverbrick<br />';
			award_set($ranksup1[2]['code'], "bronzebrick");
             echo $ranksup1[2]['pimp'].' got bronzebrick<br />';
			
            
            echo '<br /><br />Top 3 du killers supporter<br />'; 
			#get top 3 du killers supporter
			$dukillerssup = $db->getAllRows("SELECT id,
													pimp,
													networth,
													thugk,
													whorek,
													status,
													code 
											   FROM "."r".$round6."_".$pimp." 
											  WHERE status='supporter' 
										   ORDER BY thugk DESC 
										      LIMIT 3");
											  
			award_set($dukillerssup[0]['code'], "goldak");
            echo $dukillerssup[0]['pimp'].' got goldak<br />';

			award_set($dukillerssup[1]['code'], "silverak");
            echo $dukillerssup[1]['pimp'].' got silverak<br />';
			award_set($dukillerssup[2]['code'], "bronzeak");
            echo $dukillerssup[2]['pimp'].' got bronzeak<br />';
            }
            
		}		
?>
<?php
sitefooter();
?>