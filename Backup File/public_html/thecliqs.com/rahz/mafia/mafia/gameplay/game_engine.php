<?

//USE THOSE RESOURCES

$usedope=dope($pmp[3],$pmp[2],$trn);

$usecondom=condom($pmp[4],$pmp[1],$trn);

$usecrack=crack($pmp[5],$pmp[1],$trn);


mysql_query("UPDATE $tab[pimp] SET weed=$usedope, condom=$usecondom, crack=$usecrack WHERE id='$id'");





//IF THEY DONT HAVE CONDOMS, THEY USE MEDS, OR DIE

$infected=meds($pmp[4],$pmp[6],$pmp[1],$trn);

$killbystd=nomeds($pmp[4],$pmp[6],$pmp[1],$trn);



//TAKE AWAY IF NOT HAPPY

if($pmp[7] < 80)

  {

  $maxleave=round($trn*.75); $leftrand=rand(0, $maxleave);

  $thugleft=round($leftrand*(($bonus/7)+1));

  if($thugleft >= $pmp[2]){$thugleft=$pmp[2];}

  }



if($pmp[8] < 80)

  {

  $maxleave=round($trn*.75); $leftrand=rand(0, $maxleave);

  $hoeleft=round($leftrand*(($bonus/9)+1));

  if($hoeleft >= $pmp[1]){$hoeleft=$pmp[1];}

  }



//HOW MUCH DID THE HOES MAKE?

$cash=money($trn);



?>