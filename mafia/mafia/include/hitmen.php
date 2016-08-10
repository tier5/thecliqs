<?php
  
  #hitmen system related functions
  
  function getAvailableCash($pimp_id){
  global $db1;
  global $tab; 
      
     $pimp = $db1->getRow("SELECT money FROM $tab[pimp] WHERE id=$pimp_id"); 
     return $pimp['money'];     
  }    
  
  function getDuAndOp($pimp_id){
  global $db1;
  global $tab;
      
      return $db1->getRow("SELECT hitmen, thug, smuggler, whore FROM $tab[pimp] WHERE id=$pimp_id");      
  }
  
  function hitmen_requestIsActive($request_id){
  global $db1;
  global $tru;
        
        $req =       $db1->getRow("SELECT hr.id
                                     FROM r".$tru."_hitmen_requests hr
                                    WHERE hr.date_expires >= ".time("U")."
                                      AND hr.canceled = 'No'
                                      AND hr.cash > 0
                                      AND hr.id = $request_id");
       
       if ($req['id']):
            return true;
       endif;     
       
      return false; 
  }
  
  function hitmen_cancelRequest($request_id, $pimp_id){
  global $db1; 
  global $tru;
  global $tab;
         
       if (hitmen_requestIsActive($request_id)):
           $row = $db1->getRow("SELECT pimp_id, cash FROM r".$tru."_hitmen_requests WHERE id = $request_id");
           
           if ($row['pimp_id'] == $pimp_id){
                $db1->updateField("r".$tru."_hitmen_requests", "id", $request_id, "canceled", "Yes");
                $db1->doSql("UPDATE $tab[pimp] SET money = money + ".$row['cash']." WHERE id = $pimp_id");
                return 'Request canceled.'; 
           }else {
                return 'You can not cancel a request you don\'t own.';
           }           
       endif;    
  } 
                                 
  function hitmen_acceptRequest($request_id, $pimp_id){
  global $db1;
  global $tru;
    
          if (hitmen_requestIsActive($request_id) && !hitmen_requestAccepted($request_id, $pimp_id)):
               $db1->insertFields("r".$tru."_hitmens", array("hitmen_request_id", "pimp_id"), array($request_id, $pimp_id));
          endif;
  }   
  
  function hitmen_requestAccepted($request_id, $pimp_id){
  global $db1; 
  global $tru;
      
      $req =         $db1->getRow("SELECT h.id
                                     FROM r".$tru."_hitmens h
                                    WHERE h.hitmen_request_id = $request_id
                                      AND h.pimp_id = $pimp_id");
       
       if ($req['id']):
            return true;
       endif;     
       
      return false;
      
  }
  
  function  hitmen_checkAtack($atacker_id, $defender_id, $dus_killed, $ops_killed){
  global $db1;
  global $tru; 
  global $tab;     
        
        if (!$dus_killed){
            $dus_killed = 0;
        }
         if (!$ops_killed){
            $ops_killed = 0;
        }
        
        $rows = $db1->getAllRows("SELECT hr.id, 
                                         hr.target_id,
                                         hr.cash, 
                                         hr.pay_per_du,
                                         hr.pay_per_op,
                                         h.id as hitmen_id,
                                         h.pimp_id as atacker_id,
                                         h.op_killed,
                                         h.du_killed,
                                         h.cash_earned
                                    FROM r".$tru."_hitmen_requests  hr
                              INNER JOIN r".$tru."_hitmens h
                                      ON hr.id = h.hitmen_request_id
                                   WHERE hr.date_expires >= ".time("U")."
                                     AND hr.canceled = 'No'
                                     AND hr.cash > 0
                                GROUP BY h.hitmen_request_id");
                                
       $total = 0;
       if (sizeof($rows)):       
            foreach ($rows as $row):            
                $cash_earned = $row['pay_per_op']*$ops_killed + $row['pay_per_du']*$dus_killed;                
                $db1->doSql("UPDATE r".$tru."_hitmen_requests SET cash = cash -".$cash_earned." WHERE id = ".$row['id']);
                $db1->doSql("UPDATE $tab[pimp] SET money = money +".$cash_earned." WHERE id = $atacker_id");
                $db1->doSql("UPDATE r".$tru."_hitmens SET du_killed = du_killed +".$dus_killed.", op_killed = op_killed +".$ops_killed.", cash_earned = cash_earned +".$cash_earned." WHERE id = ".$row['hitmen_id']);
                $total = $total+$cash_earned;
            endforeach;       
       endif;
       
       return $total;                        
  }
  
  function hitmen_logAction($pimp_id, $action){
  global $db1;
  global $tab;
  global $tru; 
  global $REMOTE_ADDR;
  
        $pimp_name = $db1->getField($tab['pimp'], "id", $pimp_id, "pimp");
        
        $db1->insertFields($tab['logs'], array("time", "round", "pimpname", "action", "ip"), array(time("U"), $tru, $pimp_name, $action, $REMOTE_ADDR));
        
  }
  
?>
