<?
include("html.php");
//error_reporting(E_ALL);
admin();
if($install){

      if(!$type){$msg="What type of round?";}
   else{
   $days = $days*86400;
   $hours = $hours*3600;
   $mins = $mins*60;
       $begins=$days+$hours+$mins+$time;
       $ends=$begins+$endin;
       $maxbuild=$maxbuilds;
		
	  	  	   
      mysql_query("INSERT INTO $tab[game] (type,speed,maxbuild,reserves,credits,crewmax,starts,ends,gamename,attin,attout,attindown,attoutdown,protection,free1,free2,free3,free4,free5,free6,free7,free8,free9,free10,sup_11,sup_12,sup_13,sup_14,sup_15,sup_16,sup_17,sup_18,sup_19,sup_110,sup_21,sup_22,sup_23,sup_24,sup_25,sup_26,sup_27,sup_28,sup_29,sup_210,sup_31,sup_32,sup_33,sup_34,sup_35,sup_36,sup_37,sup_38,sup_39,sup_310,du1,du2,du3,du4,du5,du6,du7,du8,du9,du10,op1,op2,op3,op4,op5,op6,op7,op8,op9,op10,c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,fdu1,fdu2,fdu3,fdu4,fdu5,fdu6,fdu7,fdu8,fdu9,fdu10,cash11,cash22,cash33) VALUES ('$type','$speed','$maxbuild','$reserves','$addon','$crewmax','$begins','$ends','$name','$attin','$attout','$attindown','$attoutdown','$protection','$free1','$free2','$free3','$free4','$free5','$free6','$free7','$free8','$free9','$free10','$sup_11','$sup_12','$sup_13','$sup_14','$sup_15','$sup_16','$sup_17','$sup_18','$sup_19','$sup_110','$sup_21','$sup_22','$sup_23','$sup_24','$sup_25','$sup_26','$sup_27','$sup_28','$sup_29','$sup_210','$sup_31','$sup_32','$sup_33','$sup_34','$sup_35','$sup_36','$sup_37','$sup_38','$sup_39','$sup_310','$du1','$du2','$du3','$du4','$du5','$du6','$du7','$du8','$du9','$du10','$op1','$op2','$op3','$op4','$op5','$op6','$op7','$op8','$op9','$op10','$c1','$c2','$c3','$c4','$c5','$c6','$c7','$c8','$c9','$c10','$fdu1','$fdu2','$fdu3','$fdu4','$fdu5','$fdu6','$fdu7','$fdu8','$fdu9','$fdu10','$cash11','$cash22','$cash33'
			);");
		
			
       $round = mysql_fetch_array(mysql_query("SELECT round FROM $tab[game] ORDER BY round DESC LIMIT 1"));

      include("install2.php");

       if($city1)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city1');"); }
       if($city2)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city2');"); }
       if($city3)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city3');"); }
       if($city4)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city4');"); }
       if($city5)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city5');"); }
       if($city6)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city6');"); }
       if($city7)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city7');"); }
       if($city8)
         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city8');"); }
       if($city9)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city9');"); }

       if($city10)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city10');"); }

       if($city11)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city11');"); }

       if($city12)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city12');"); }

       if($city13)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city13');"); }

       if($city14)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city14');"); }

       if($city15)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city15');"); }

       if($city16)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city16');"); }
	  if($city17)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city17');"); }
	 if($city18)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city18');"); }
	 if($city19)

         { mysql_query("INSERT INTO r$round[0]_$tab[city] (name) VALUES ('$city19');"); }
		 
       $msg="Round $round[0] has been installed.";
       }

}


//pull round prizes to auto load prize area
$rnddd=1;
$geterdone = mysql_fetch_array(mysql_query("SELECT free1,free2,free3,free4,free5,free6,free7,free8,free9,free10,sup_11,sup_12,sup_13,sup_14,sup_15,sup_16,sup_17,sup_18,sup_19,sup_110,sup_21,sup_22,sup_23,sup_24,sup_25,sup_26,sup_27,sup_28,sup_29,sup_210,sup_31,sup_32,sup_33,sup_34,sup_35,sup_36,sup_37,sup_38,sup_39,sup_310,du1,du2,du3,du4,du5,du6,du7,du8,du9,du10,op1,op2,op3,op4,op5,op6,op7,op8,op9,op10,c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,fdu1,fdu2,fdu3,fdu4,fdu5,fdu6,fdu7,fdu8,fdu9,fdu10,round FROM $tab[game] WHERE round>0 limit 1;"));
			   
$menu='pimp/';
secureheader();
siteheader();
?>
   <div align="center">
   <table width="80%" height="100%">  
    <tr>
<p></p>
    </tr>
    <tr>    </tr>
    <tr>
     <td height="12"><b>Install New Round</b></td>
    </tr>
    <tr>    </tr>
    <tr>
     <td align="center" valign="top">
     <?if($msg){?><font color="red"><?=$msg?></font><?}?>
     <form method="post" action="in$taLl.php">
     <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0">
      <tr>
       <td width="259" align="right">Round Type:</td>
       <td width="459"><select name="type">
            <option value="">-select one-</option>
            <option value="public">Public</option>
            <option value="supporters" selected="selected" <?if($type==supporters){echo"selected";}?>>Supporters</option>
           </select>       </td>
      </tr>
	   <tr>
       <td width="259" align="right">Name the Round :</td>
       <td width="459"> 
         <input name="name" type="text" class="text" id="name" size="20" />
        </td>
      </tr>
      <tr>
       <td align="right">Speed:</td>
       <td><select name="speed">
            <option value="">-select one-</option>
            <option <?if($speed==1){echo"selected";}?> value="1">1 turn</option>
            <option <?if($speed==2){echo"selected";}?> value="2">2 turns</option>
            <option <?if($speed==3){echo"selected";}?> value="3">3 turns</option>
            <option <?if($speed==4){echo"selected";}?> value="4">4 turns</option>
            <option <?if($speed==5){echo"selected";}?> value="5">5 turns</option>
            <option <?if($speed==6){echo"selected";}?> value="6">6 turns</option>
            <option <?if($speed==8){echo"selected";}?> value="8">8 turns</option>
            <option <?if($speed==9){echo"selected";}?> value="9">9 turns</option>
            <option <?if($speed==10){echo"selected";}?> value="10">10 turns</option>
            <option selected value="15">15 turns</option>
            <option <?if($speed==20){echo"selected";}?> value="20">20 turns</option>
            <option <?if($speed==25){echo"selected";}?> value="25">25 turns</option>
            <option <?if($speed==50){echo"selected";}?> value="50">50 turns</option>
            <option <?if($speed==75){echo"selected";}?> value="75">75 turns</option>
            <option <?if($speed==100){echo"selected";}?> value="100">100 turns</option>
            <option <?if($speed==200){echo"selected";}?> value="200">200 turns</option>
            <option <?if($speed==300){echo"selected";}?> value="300">300 turns</option>
            <option <?if($speed==400){echo"selected";}?> value="400">400 turns</option>
            <option <?if($speed==500){echo"selected";}?> value="500">500 turns</option>
            <option <?if($speed==1000){echo"selected";}?> value="1000">1,000 turns</option>
            <option <?if($speed==2500){echo"selected";}?> value="2500">2,500 turns</option>
            <option <?if($speed==5000){echo"selected";}?> value="5000">5,000 turns</option>
           </select> 
         <font color="red"><small>every (10 minties)</small></font> default 15      </td>
      </tr>
       <td align="right">Maxbuild's:</td>
       <td><select name="maxbuilds">
            <option value="">-select one-</option>
            <option <?if($maxbuilds==500){echo"selected";}?> value="500">500 turns</option>
            <option <?if($maxbuilds==800){echo"selected";}?> value="800">800 turns</option>
            <option <?if($maxbuilds==900){echo"selected";}?> value="900">900 turns</option>
            <option <?if($maxbuilds==1000){echo"selected";}?> value="1000">1,000 turns</option>
            <option <?if($maxbuilds==1200){echo"selected";}?> value="1200">1,200 turns</option>
            <option <?if($maxbuilds==1500){echo"selected";}?> value="1500">1,500 turns</option>
            <option <?if($maxbuilds==1750){echo"selected";}?> value="1750">1,750 turns</option>
            <option selected value="2000">2,000 turns</option>
            <option <?if($maxbuilds==2200){echo"selected";}?> value="2200">2,200 turns</option>
            <option <?if($maxbuilds==2500){echo"selected";}?> value="2500">2,500 turns</option>
            <option <?if($maxbuilds==2750){echo"selected";}?> value="2750">2,750 turns</option>
            <option <?if($maxbuilds==3000){echo"selected";}?> value="3000">3,000 turns</option>
            <option <?if($maxbuilds==3200){echo"selected";}?> value="3200">3,200 turns</option>
            <option <?if($maxbuilds==3500){echo"selected";}?> value="3500">3,500 turns</option>
            <option <?if($maxbuilds==3750){echo"selected";}?> value="3750">3,750 turns</option>
            <option <?if($maxbuilds==4000){echo"selected";}?> value="4000">4,000 turns</option>
            <option <?if($maxbuilds==4200){echo"selected";}?> value="4200">4,200 turns</option>
            <option <?if($maxbuilds==4500){echo"selected";}?> value="4500">4,500 turns</option>
            <option <?if($maxbuilds==4750){echo"selected";}?> value="4750">4,750 turns</option>
            <option <?if($maxbuilds==5000){echo"selected";}?> value="5000">5,000 turns</option>
            <option <?if($maxbuilds==5200){echo"selected";}?> value="5200">5,200 turns</option>
            <option <?if($maxbuilds==5500){echo"selected";}?> value="5500">5,500 turns</option>
            <option <?if($maxbuilds==5750){echo"selected";}?> value="5750">5,750 turns</option>
            <option <?if($maxbuilds==6000){echo"selected";}?> value="6000">6,000 turns</option>
            <option <?if($maxbuilds==6500){echo"selected";}?> value="6500">6,500 turns</option>
            <option <?if($maxbuilds==6750){echo"selected";}?> value="6750">6,750 turns</option>
            <option <?if($maxbuilds==7200){echo"selected";}?> value="7200">7,200 turns</option>
            <option <?if($maxbuilds==7500){echo"selected";}?> value="7500">7,500 turns</option>
            <option <?if($maxbuilds==7750){echo"selected";}?> value="7750">7,750 turns</option>
            <option <?if($maxbuilds==8000){echo"selected";}?> value="8000">8,000 turns</option>
            <option <?if($maxbuilds==8200){echo"selected";}?> value="8200">8,200 turns</option>
            <option <?if($maxbuilds==8500){echo"selected";}?> value="8500">8,500 turns</option>
            <option <?if($maxbuilds==8750){echo"selected";}?> value="8750">8,750 turns</option>
            <option <?if($maxbuilds==9000){echo"selected";}?> value="9000">9,000 turns</option>
            <option <?if($maxbuilds==9200){echo"selected";}?> value="9200">9,200 turns</option>
            <option <?if($maxbuilds==9500){echo"selected";}?> value="9500">9,500 turns</option>
            <option <?if($maxbuilds==9750){echo"selected";}?> value="9750">9,750 turns</option>
            <option <?if($maxbuilds==10000){echo"selected";}?> value="10000">10,000 turns</option>
            <option <?if($maxbuilds==15000){echo"selected";}?> value="15000">15,000 turns</option>
            <option <?if($maxbuilds==20000){echo"selected";}?> value="20000">20,000 turns</option>
            <option <?if($maxbuilds==25000){echo"selected";}?> value="25000">25,000 turns</option>
            <option <?if($maxbuilds==30000){echo"selected";}?> value="30000">30,000 turns</option>
            <option <?if($maxbuilds==35000){echo"selected";}?> value="35000">35,000 turns</option>
            <option <?if($maxbuilds==40000){echo"selected";}?> value="40000">40,000 turns</option>
            <option <?if($maxbuilds==45000){echo"selected";}?> value="45000">45,000 turns</option>
            <option <?if($maxbuilds==50000){echo"selected";}?> value="50000">50,000 turns</option>
            <option <?if($maxbuilds==75000){echo"selected";}?> value="75000">75,000 turns</option>
            <option <?if($maxbuilds==100000){echo"selected";}?> value="100000">100,000 turns</option>
            <option <?if($maxbuilds==500000){echo"selected";}?> value="500000">500,000 turns</option>
            <option <?if($maxbuilds==1000000){echo"selected";}?> value="1000000">1,000,000 turns</option>
           </select> 
         <font color="red"><small>default 2000 </small></font>       </td>
      </tr>
      <tr>
       <td align="right">Reserves:</td>
       <td><select name="reserves">
            <option value="">-select one-</option>
            <option <?if($reserves==0){echo"selected";}?> value="0">0</option>
            <option <?if($reserves==500){echo"selected";}?> value="500">500</option>
            <option <?if($reserves==800){echo"selected";}?> value="800">800</option>
            <option <?if($reserves==1000){echo"selected";}?> value="1000">1,000</option>
            <option <?if($reserves==1250){echo"selected";}?> value="1250">1,250</option>
            <option <?if($reserves==1500){echo"selected";}?> value="1500">1,500</option>
            <option <?if($reserves==2000){echo"selected";}?> value="2000">2,000</option>
            <option <?if($reserves==2500){echo"selected";}?> value="2500">2,500</option>
            <option <?if($reserves==3000){echo"selected";}?> value="3000">3,000</option>
            <option <?if($reserves==3500){echo"selected";}?> value="3500">3,500</option>
            <option <?if($reserves==4250){echo"selected";}?> value="4250">4,250</option>
            <option <?if($reserves==4500){echo"selected";}?> value="4500">4,500</option>
            <option <?if($reserves==5000){echo"selected";}?> value="5000">5,000</option>
            <option <?if($reserves==5500){echo"selected";}?> value="5500">5,500</option>
            <option <?if($reserves==6000){echo"selected";}?> value="6000">6,000</option>
            <option <?if($reserves==8000){echo"selected";}?> value="8000">8,000</option>
            <option <?if($reserves==10000){echo"selected";}?> value="10000">10,000</option>
            <option <?if($reserves==12500){echo"selected";}?> value="12500">12,500</option>
            <option <?if($reserves==15000){echo"selected";}?> value="15000">15,000</option>
            <option <?if($reserves==20000){echo"selected";}?> value="20000">20,000</option>
            <option <?if($reserves==25000){echo"selected";}?> value="25000">25,000</option>
            <option <?if($reserves==30000){echo"selected";}?> value="30000">30,000</option>
            <option <?if($reserves==40000){echo"selected";}?> value="40000">40,000</option>
            <option <?if($reserves==50000){echo"selected";}?> value="50000">50,000</option>
           </select> 
         <font color="red">(Starting Reserves) </font></td>
      </tr>
      <tr>
       <td align="right">Crew Max:</td>
       <td><select name="crewmax">
            <option value="">-select one-</option>
            <option <?if($crewmax==1){echo"selected";}?> value="1">1 members</option>
            <option <?if($crewmax==2){echo"selected";}?> value="2">2 members</option>
            <option <?if($crewmax==3){echo"selected";}?> value="3">3 members</option>
            <option <?if($crewmax==4){echo"selected";}?> value="4">4 members</option>
            <option <?if($crewmax==5){echo"selected";}?> value="5">5 members</option>
            <option <?if($crewmax==10){echo"selected";}?> value="10">10 members</option>
            <option selected value="15">15 members</option>
            <option <?if($crewmax==20){echo"selected";}?> value="20">20 members</option>
            <option <?if($crewmax==25){echo"selected";}?> value="25">25 members</option>
            <option <?if($crewmax==40){echo"selected";}?> value="40">40 members</option>
            <option <?if($crewmax==50){echo"selected";}?> value="50">50 members</option>
            <option <?if($crewmax==100){echo"selected";}?> value="100">100 members</option>
            <option <?if($crewmax==200){echo"selected";}?> value="200">200 members</option>
            <option <?if($crewmax==300){echo"selected";}?> value="300">300 members</option>
            <option <?if($crewmax==400){echo"selected";}?> value="400">400 members</option>
            <option <?if($crewmax==500){echo"selected";}?> value="500">500 members</option>
            <option <?if($crewmax==600){echo"selected";}?> value="600">600 members</option>
            <option <?if($crewmax==700){echo"selected";}?> value="700">700 members</option>
            <option <?if($crewmax==800){echo"selected";}?> value="800">800 members</option>
            <option <?if($crewmax==900){echo"selected";}?> value="900">900 members</option>
            <option <?if($crewmax==1000){echo"selected";}?> value="1000">1,000 members</option>
           </select>       </td>
      </tr>
      <tr>
       <td align="right">Credit Addon:</td>
       <td><select name="addon">
            <option value="">-select one-</option>
            <option <?if($addon==0){echo"selected";}?> value="0">0 turns</option>
            <option <?if($addon==100){echo"selected";}?> value="100">100</option>
            <option <?if($addon==500){echo"selected";}?> value="500">500</option>
            <option <?if($addon==800){echo"selected";}?> value="800">800</option>
            <option <?if($addon==900){echo"selected";}?> value="900">900</option>
            <option <?if($addon==1000){echo"selected";}?> value="1000">1,000</option>
            <option <?if($addon==1500){echo"selected";}?> value="1500">1,500</option>
            <option <?if($addon==1750){echo"selected";}?> value="1750">1,750</option>
            <option <?if($addon==2000){echo"selected";}?> value="2000">2,000</option>
            <option <?if($addon==2500){echo"selected";}?> value="2500">2,500</option>
            <option <?if($addon==3000){echo"selected";}?> value="3000">3,000</option>
            <option <?if($addon==3250){echo"selected";}?> value="3250">3,250</option>
            <option <?if($addon==3500){echo"selected";}?> value="3500">3,500</option>
            <option <?if($addon==4000){echo"selected";}?> value="4000">4,000</option>
            <option <?if($addon==4500){echo"selected";}?> value="4500">4,500</option>
            <option <?if($addon==5000){echo"selected";}?> value="5000">5,000</option>
            <option <?if($addon==5500){echo"selected";}?> value="5500">5,500</option>
            <option <?if($addon==6000){echo"selected";}?> value="6000">6,000</option>
            <option <?if($addon==8000){echo"selected";}?> value="8000">8,000</option>
            <option <?if($addon==10000){echo"selected";}?> value="10000">10,000</option>
            <option <?if($addon==15000){echo"selected";}?> value="15000">15,000</option>
            <option <?if($addon==20000){echo"selected";}?> value="20000">20,000</option>
            <option <?if($addon==25000){echo"selected";}?> value="25000">25,000</option>
            <option <?if($addon==50000){echo"selected";}?> value="50000">50,000</option>
            <option <?if($addon==75000){echo"selected";}?> value="75000">75,000</option>
            <option <?if($addon==100000){echo"selected";}?> value="100000">100,000</option>
            <option selected value="1000000000">UNLIMITED</option>
           </select>       </td>
      </tr>
      <tr>
    <td align="right">Attacks in:</td>
            <td><select name="attin">
            <option value="">-select one-</option>
            <option <?if($attin==5){echo"selected";}?> value="5">5 attacks</option>
            <option <?if($attin==10){echo"selected";}?> value="10">10 attacks</option>
            <option <?if($attin==15){echo"selected";}?> value="15">15 attacks</option>
            <option <?if($attin==20){echo"selected";}?> value="20">20 attacks</option>
            <option "selected" value="24">24 attacks</option>
          <option <?if($attin==30){echo"selected";}?> value="30">30 attacks</option>
            <option <?if($attin==35){echo"selected";}?> value="35">35 attacks</option>
            <option <?if($attin==40){echo"selected";}?> value="40">40 attacks</option>
            <option <?if($attin==45){echo"selected";}?> value="45">45attacks</option>
            <option <?if($attin==50){echo"selected";}?> value="50">50 attacks</option>
            <option <?if($attin==75){echo"selected";}?> value="75">75 attacks</option>
            <option <?if($attin==100){echo"selected";}?> value="100">100 attacks</option>
            <option <?if($attin==500){echo"selected";}?> value="500">500 attacks</option>
            <option <?if($attin==1000){echo"selected";}?> value="1000">1000 attacks</option>
            <option <?if($attin==2000){echo"selected";}?> value="2000">2000 attacks</option>
            <option <?if($attin==3000){echo"selected";}?> value="3000">3000 attacks</option>
            <option <?if($attin==4000){echo"selected";}?> value="4000">4000 attacks</option>
            <option <?if($attin==5000){echo"selected";}?> value="5000">5000 attacks</option>
            <option <?if($attin==1000000){echo"selected";}?> value="1000000">UNLIMITED attacks</option>
           </select> 
               set to 24   </td>
      </tr>
	        <tr>
              <td align="right">Attacks out :</td>
              <td><select name="attout">
                  <option value="">-select one-</option>
                  <option <?if($attout==5){echo"selected";}?> value="5">5 attacks</option>
                  <option <?if($attout==10){echo"selected";}?> value="10">10 attacks</option>
                  <option <?if($attout==15){echo"selected";}?> value="15">15 attacks</option>
                  <option <?if($attout==20){echo"selected";}?> value="20">20 attacks</option>
                  <option <?if($attout==25){echo"selected";}?> value="25">25 attacks</option>
                  <option <?if($attout==30){echo"selected";}?> value="30>30 attacks</option>
                  <option <?if($attout==35){echo"selected";}?> value="35>35 attacks</option>
                  <option <?if($attout==40){echo"selected";}?> value="40">40 attacks</option>
                  <option <?if($attout==45){echo"selected";}?> value="45">45attacks</option>
                  <option <?if($attout==50){echo"selected";}?> value="50">50 attacks</option>
                  <option <?if($attout==75){echo"selected";}?> value="75">75 attacks</option>
                  <option <?if($attout==100){echo"selected";}?> value="100">100 attacks</option>
                  <option <?if($attout==500){echo"selected";}?> value="500">500 attacks</option>
                  <option selected value="1000">1000 attacks</option>
                  <option <?if($attout==2000){echo"selected";}?> value="2000">2000 attacks</option>
                  <option <?if($attout==3000){echo"selected";}?> value="3000">3000 attacks</option>
                  <option <?if($attout==4000){echo"selected";}?> value="4000">4000 attacks</option>
                  <option <?if($attout==5000){echo"selected";}?> value="5000">5000 attacks</option>
                  <option "selected" value="1000000">UNLIMITED attacks</option>
                </select>
                   set unlimited   </td>
          </tr>
      <tr>
    <td align="right">Attacks:</td>
            <td><select name="attindown">
            <option value="">-select one-</option>
            <option "selected" value="1">1 attacks</option>
            <option <?if($attindown==2){echo"selected";}?> value="2">2 attacks</option>
            <option <?if($attindown==3){echo"selected";}?> value="3">3 attacks</option>
            <option <?if($attindown==4){echo"selected";}?> value="4">4 attacks</option>
            <option <?if($attindown==5){echo"selected";}?> value="5">5 attacks</option>
            <option <?if($attindown==6){echo"selected";}?> value="6">6 attacks</option>
            <option <?if($attindown==7){echo"selected";}?> value="7">7 attacks</option>
            <option <?if($attindown==8){echo"selected";}?> value="8">8 attacks</option>
            <option <?if($attindown==9){echo"selected";}?> value="9">9 attacks</option>
            <option <?if($attindown==10){echo"selected";}?> value="10">10 attacks</option>
            <option <?if($attindown==15){echo"selected";}?> value="15">15 attacks</option>
            <option <?if($attindown==20){echo"selected";}?> value="20">20 attacks</option>
            <option <?if($attindown==25){echo"selected";}?> value="25">25 attacks</option>
            <option <?if($attindown==50){echo"selected";}?> value="50">50 attacks</option>
            <option <?if($attindown==100){echo"selected";}?> value="100">100 attacks</option>
            <option <?if($attindown==1000){echo"selected";}?> value="1000">1000 attacks</option>
            <option <?if($attindown==2000){echo"selected";}?> value="2000">2000 attacks</option>
            <option <?if($attindown==5000){echo"selected";}?> value="5000">5000 attacks</option>
           </select>
            set 1 down </td>
      </tr>
	        <tr>
              <td align="right">Attacks :</td>
              <td><select name="attoutdown">
                  <option value="">-select one-</option>
            <option <?if($attoutdown==1){echo"selected";}?> value="1">1 attacks</option>
            <option <?if($attoutdown==2){echo"selected";}?> value="2">2 attacks</option>
            <option <?if($attoutdown==3){echo"selected";}?> value="3">3 attacks</option>
            <option <?if($attoutdown==4){echo"selected";}?> value="4">4 attacks</option>
            <option <?if($attoutdown==5){echo"selected";}?> value="5">5 attacks</option>
            <option <?if($attoutdown==6){echo"selected";}?> value="6">6 attacks</option>
            <option <?if($attoutdown==7){echo"selected";}?> value="7">7 attacks</option>
            <option <?if($attoutdown==8){echo"selected";}?> value="8">8 attacks</option>
            <option <?if($attoutdown==9){echo"selected";}?> value="9">9 attacks</option>
            <option <?if($attoutdown==10){echo"selected";}?> value="10">10 attacks</option>
            <option <?if($attoutdown==15){echo"selected";}?> value="15">15 attacks</option>
            <option <?if($attoutdown==20){echo"selected";}?> value="20">20 attacks</option>
            <option <?if($attoutdown==25){echo"selected";}?> value="25">25 attacks</option>
            <option "selected" value="50">50 attacks</option>
            <option <?if($attoutdown==100){echo"selected";}?> value="100">100 attacks</option>
            <option <?if($attoutdown==1000){echo"selected";}?> value="1000">1000 attacks</option>
            <option <?if($attoutdown==2000){echo"selected";}?> value="2000">2000 attacks</option>
            <option <?if($attoutdown==5000){echo"selected";}?> value="5000">5000 attacks</option>
                </select>
                   set 50 down   </td>
          </tr>
<tr>
	           <td align="right" valign="top">Prizes:</td>
               <td><table width="100%" border="0">
                   <tr>
                     <td>&nbsp;</td>
                     <td><div align="center"><strong>Free</strong></div></td>
                     <td><div align="center"><strong>Supporter<br />
                     </strong></div></td>
                     <td><div align="center"><strong>DU<br />
                     Killer</strong></div></td>
                     <td><div align="center"><strong>Family</strong></div></td>
                     <td><div align="center"><strong>FREE<br /> 
                      DU<br />
                     Killer</strong></div></td>
                 </tr>
                   <tr>
                     <td><strong>1st</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free1" type="text" class="text" id="free1" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                     Cash:<input name="cash22" type="text" class="text" id="cash22" value="250.00" size="5" />
                       <br />
                     Credit:<input name="sup_21" type="text" class="text" id="sup_21" value="0" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du1" type="text" class="text" id="du1" value="150000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c1" type="text" class="text" id="c1" value="30000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu1" type="text" class="text" id="fdu1" value="50000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>2nd</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free2" type="text" class="text" id="free2" value="40000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_22" type="text" class="text" id="sup_22" value="200000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du2" type="text" class="text" id="du2" value="125000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c2" type="text" class="text" id="c2" value="20000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu2" type="text" class="text" id="fdu2" value="40000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>3rd</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free3" type="text" class="text" id="free3" value="30000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_23" type="text" class="text" id="sup_23" value="150000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du3" type="text" class="text" id="du3" value="100000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c3" type="text" class="text" id="c3" value="10000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu3" type="text" class="text" id="fdu3" value="30000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>4th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free4" type="text" class="text" id="free4" value="20000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_24" type="text" class="text" id="sup_24" value="100000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du4" type="text" class="text" id="du4" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c4" type="text" class="text" id="c4" value="5000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu4" type="text" class="text" id="fdu4" value="20000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>5th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free5" type="text" class="text" id="free5" value="10000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_25" type="text" class="text" id="sup_25" value="75000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du5" type="text" class="text" id="du5" value="25000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c5" type="text" class="text" id="c5" value="2500" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu5" type="text" class="text" id="fdu5" value="10000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>6th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free6" type="text" class="text" id="free6" value="9000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_26" type="text" class="text" id="sup_26" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du6" type="text" class="text" id="du6" value="20000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c6" type="text" class="text" id="c6" value="1000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu6" type="text" class="text" id="fdu6" value="8000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>7th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free7" type="text" class="text" id="free7" value="8000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_27" type="text" class="text" id="sup_27" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du7" type="text" class="text" id="du7" value="15000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c7" type="text" class="text" id="c7" value="1000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu7" type="text" class="text" id="fdu7" value="6000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>8th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free8" type="text" class="text" id="free8" value="7000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_28" type="text" class="text" id="sup_28" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du8" type="text" class="text" id="du8" value="15000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c8" type="text" class="text" id="c8" value="1000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu8" type="text" class="text" id="fdu8" value="4000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>9th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free9" type="text" class="text" id="free9" value="6000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_29" type="text" class="text" id="sup_29" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du9" type="text" class="text" id="du9" value="10000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c9" type="text" class="text" id="c9" value="1000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu9" type="text" class="text" id="fdu9" value="2000" size="5" />
                     </td>
                   </tr>
                   <tr>
                     <td><strong>10th</strong></td>
                     <td align="center" valign="middle"> 
                       <input name="free10" type="text" class="text" id="free10" value="5000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="sup_210" type="text" class="text" id="sup_210" value="50000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="du10" type="text" class="text" id="du10" value="10000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="c10" type="text" class="text" id="c10" value="1000" size="5" />
                     </td>
                     <td align="center" valign="middle"> 
                       <input name="fdu10" type="text" class="text" id="fdu10" value="1000" size="5" />
                     </td>
                   </tr>
                 </table>            </td>
      </tr>
      <tr>
        <td align="right" valign="top">Choose Cities:</td>
        <td><table width="100%"  border="0">
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="left" valign="top"><input name="city9" type="checkbox" value="Italy" checked="checked" />
Italy<br />
<input type="checkbox" name="city10" value="New York" checked="checked" />
New York <br />
<input type="checkbox" name="city11" value="Chicago" checked="checked" />
Chicago <br />
<input type="checkbox" name="city13" value="Los Angeles" checked="checked" />
Los Angeles <br />
<input type="checkbox" name="city14" value="Las Vegas" checked="checked" />
Las Vegas <br />
<input type="checkbox" name="city15" value="Detroit" checked="checked" />
Detroit <br />
<input type="checkbox" name="city16" value="Toronto" checked="checked" />
Toronto <br />
<input type="checkbox" name="city17" value="Tokyo" checked="checked" />
Tokyo <br />
<input type="checkbox" name="city18" value="Baghdad" checked="checked"/>
Baghdad <br />
<input type="checkbox" name="city19" value="Beijing" checked="checked" />
Beijing
 </td>
                  <td align="left" valign="top"><input name="city1" type="checkbox" value="Miami Beach" checked="checked" />
Miami Beach<br />
<input name="city2" type="checkbox" value="Ontario" checked="checked" />
Ontario<br />
<input name="city5" type="checkbox" value="Bogata" checked="checked" />
Bogata  <br />
<input name="city6" type="checkbox" value="Bangkok" checked="checked" />
Bangkok<br />
<input name="city7" type="checkbox" value="Sydney" checked="checked" />
Sydney<br />
<input name="city8" type="checkbox" value="Moscow" checked="checked" />
Moscow</td>
                </tr>
              </table>
              </td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td align="right">Begin in:</td>
        <td> 
           
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%"><p class="style7">
                Days: <input name="days" type="text" class="text" id="days" size="4" /> Hours: <input name="hours" type="text" class="text" id="hours" size="4" /> Minutes: <input name="mins" type="text" class="text" id="mins" size="4" />
                <br />
              </p>
                </td>
            </tr>
          </table>
          </td>
      </tr>
      <tr>
        <td align="right">End in:</td>
        <td><select name="endin">
            <option value="">-select one-</option>
            <option <?if($endin==120){echo"selected";}?> value="120">2 minites</option>
            <option <?if($endin==3600){echo"selected";}?> value="3600">1 hr</option>
            <option <?if($endin==7200){echo"selected";}?> value="7200">2 hr</option>
            <option <?if($endin==10800){echo"selected";}?> value="10800">3 hr</option>
            <option <?if($endin==14400){echo"selected";}?> value="14400">4 hr</option>
            <option <?if($endin==21600){echo"selected";}?> value="21600">6 hr</option>
            <option <?if($endin==43200){echo"selected";}?> value="43200">12 hr</option>
            <option <?if($endin==86400){echo"selected";}?> value="86400">1 day</option>
            <option <?if($endin==172800){echo"selected";}?> value="172800">2 days</option>
            <option <?if($endin==259200){echo"selected";}?> value="259200">3 days</option>
            <option <?if($endin==345600){echo"selected";}?> value="345600">4 days</option>
            <option <?if($endin==354000){echo"selected";}?> value="354000">5 days</option>
            <option <?if($endin==354000){echo"selected";}?> value="604800">7 days</option>
            <option <?if($endin==864000){echo"selected";}?> value="864000">10 days</option>
            <option <?if($endin==1296000){echo"selected";}?> value="1296000">15 days</option>
            <option <?if($endin==1728000){echo"selected";}?> value="1728000">20 days</option>
            <option <?if($endin==2160000){echo"selected";}?> value="2160000">25 days</option>
            <option <?if($endin==2592000){echo"selected";}?> value="2592000">30 days</option>
            <option <?if($endin==3888000){echo"selected";}?> value="3888000">45 days</option>
            <option <?if($endin==5184000){echo"selected";}?> value="5184000">60 days</option>
            <option <?if($endin==999999999999999){echo"selected";}?> value="999999999999999">Forever</option>
          </select> 
        *this will end in this much time from the time it starts         </td>
      </tr>
      <tr>
        <td></td>
        <td><input type="submit" name="install" value="create game"></td>
      </tr>
     </table>
     </form>
     <table width="300">
      <tr><td><small><font color="red">
      <b>WARNING</b>: installing a supporters round with a credit addon is agaisnt the law. This is considered Gambling, if you do not have a gambling license, you can get sued. 
      <br>
      <br><b>NOTE:</b> All non-supporter games must have a credit addon, if you make a round without a credit addon, then a supporters round is pointless, think business...
      </font></small></td></tr>
     </table>     </td>
    </tr>
         <tr>
     <td height="12"><div align="center"><b>Current Rounds</b></div></td>
    </tr>
	<tr>
     <td align="center" valign="top"><table width="95%" cellspacing="1">
       <tr align="center" bgcolor="#000000">
         <td width="79" align="left" bgcolor="#666666"><strong><font color="#FFFFFF"><small>Round</small></font></strong></td>
         <td width="78" align="left" bgcolor="#666666"><strong><font color="#FFFFFF"><small>Type</small></font></strong></td>
         <td width="96" align="left" bgcolor="#666666"><strong><font color="#FFFFFF"><small>Name</small></font></strong></td>
         <td width="105" align="center" bgcolor="#666666"><div align="center"><font color="#FFFFFF"><strong><small>Starts</small></strong></font></div></td>
         <td width="104" align="center" bgcolor="#666666"><div align="center"><font color="#FFFFFF"><strong><small>Ends</small></strong></font></div></td>
       </tr>
       <?
     $get = mysql_query("SELECT round,type,starts,ends,gamename FROM $tab[game] WHERE status!='done' ORDER BY round DESC LIMIT 4;");
     while ($game = mysql_fetch_array($get)){

            if($tr==0){$color="#CCCCCC";$tr++;}
        elseif($tr==1){$color="#999999";$tr--;}
     ?>
       <tr align="center" bgcolor="<?=$color?>">
         <td align="left">Round
           <?=$game[0]?></td>
         <td align="left"><?=$game[1]?></td>
         <td align="left"><?=$game[4]?></td>
         <td align="center"><?=dayhour($game[2])?>
             <div align="center"></div></td>
         <td align="center"><?=dayhour($game[3])?>
             linux- <?=$game[3]?>
             <div align="center"></div></td>
       </tr>
       <?
     }
     ?>
     </table></td>
   </tr>   </table>
</div>
<?
sitefooter();
?>