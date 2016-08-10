<?
if(($pmp[9] > 0) || ($pmp[24] > 0) || ($pmp[25] > 0)){
	print("<br><b>$pmp[1]</b> is too well protected, try to kill his men first!");
	$cash = 0;
	$infected = 0;
}else{
	$limit = 350000000000;
	if ($pmp[22] > $limit && $pmp[22] > 100*$limit) {
	    $temp = $pmp[22] / $limit;
		$mult = $limit;
	}else {
		$temp = $pmp[22];
		$mult = 1;
	}
	
	$found=$mult*rand($temp/100, $temp/20);
	$std=round(rand(0,($found*.50)));
	?>
	<br>You sent your boys out and found <font color="#B5CDE6">$<?=commas($found)?></font> in <b><?=$pmp[1]?>'s</b> bank.

	<?
	$stdC=commas($std);
	$foundC=commas($found);
	$meds=fixinput($pmp[22]-$std);
	$medis=fixinput($pimp[14]+$std);
	$u_award=fixinput($pimp[27]+$std);
	
	$db1->insertFields($tab['mail'], array("src", "dest", "msg", "time", "inbox"), array('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> found <font color=#B5CDE6>$foundC</font> dollars in your bank account and had time to jack <font color=#B5CDE6>$stdC</font> of it.','$time','attacks'));
    $db1->doSql("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2 WHERE id=$id");                 
	$db1->doSql("UPDATE $tab[pimp] SET bank=bank-$found, atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id=$pmp[0]");
	$db1->doSql("UPDATE $tab[pimp] SET money=money+$found, cashstolen='$u_award' WHERE id=$id");
}
?>