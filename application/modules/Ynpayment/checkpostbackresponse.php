<?php
/**
 * ?m=lite&name=checkpostbackresponse&module=ynpayment
 * @author MinhNC
 */
$silentPostURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'iTransact'
        ), 'ynpayment_post_back', true);
?>
<div style="text-align:  center">
	<h3>Check Silent Post Responsive</h3>
	<form action="<?php echo $silentPostURL;?>" method="post">
		Transaction ID <input type="input" name="xid" value="1821199455"/><br/>
	    <input type="hidden" name="authcode" value="ok"/>
	    <input type="hidden" name="avs_response" value="1"/>
	    <input type="hidden" name="cc_last_four" value="1234"/>
	    <input type="hidden" name="cc_name" value="Visa"/>
	    <input type="hidden" name="cvv2_response" value="1234"/>
	    <input type="hidden" name="trans_type" value="order"/>
	    Parent Transaction ID <input type="input" name="orig_xid" value="12346"/><br/>
	    <input type="hidden" name="when" value="20010509134443"/>
	    <input type="hidden" name="status" value="ok"/>
	    Amount <input type="input" name="recur_total" value="9.95"/><br/>
	    <input type="hidden" name="recipe_name" value="3months"/>
	    <input type="hidden" name="cust_id" value="1"/>
	    <input type="hidden" name="first_name" value="John"/>
	    <input type="hidden" name="last_name" value="Smith"/>
	    <input type="hidden" name="address" value=""/>
	    <input type="hidden" name="city" value=""/>
	    <input type="hidden" name="state" value=""/>
	    <input type="hidden" name="zip" value=""/>
	    <input type="hidden" name="ctry" value=""/>
	    <input type="hidden" name="phone" value=""/>
	    <input type="hidden" name="email" value=""/>
	    <input type="hidden" name="sfname" value=""/>
	    <input type="hidden" name="slname" value=""/>
	    <input type="hidden" name="scity" value=""/>
	    <input type="hidden" name="sstate" value=""/>
	    <input type="hidden" name="szip" value=""/>
	    <input type="hidden" name="sctry" value=""/>
	    <input type="submit"/>
	</form>
</div>