<?php
/**
 * ?m=lite&name=checksilentpostresponse&module=ynpayment
 * @author MinhNC
 */
$silentPostURL = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'AuthorizeNet'
        ), 'ynpayment_silent_post', true);
?>
<div style="text-align:  center">
	<h3>Check Silent Post Responsive</h3>
	<form action="<?php echo $silentPostURL;?>" method="post">
	    <input type="hidden" name="x_response_code" value="1"/>
	    <input type="hidden" name="x_response_subcode" value="1"/>
	    <input type="hidden" name="x_response_reason_code" value="1"/>
	    <input type="hidden" name="x_response_reason_text" value="This transaction has been approved."/>
	    <input type="hidden" name="x_auth_code" value=""/>
	    <input type="hidden" name="x_avs_code" value="P"/>
	    Transaction ID <input type="input" name="x_trans_id" value="1821199455"/><br/>
	    <input type="hidden" name="x_invoice_num" value=""/>
	    <input type="hidden" name="x_description" value=""/>
	    Amount <input type="input" name="x_amount" value="9.95"/><br/>
	    <input type="hidden" name="x_method" value="CC"/>
	    <input type="hidden" name="x_type" value="auth_capture"/>
	    <input type="hidden" name="x_cust_id" value="1"/>
	    <input type="hidden" name="x_first_name" value="John"/>
	    <input type="hidden" name="x_last_name" value="Smith"/>
	    <input type="hidden" name="x_company" value=""/>
	    <input type="hidden" name="x_address" value=""/>
	    <input type="hidden" name="x_city" value=""/>
	    <input type="hidden" name="x_state" value=""/>
	    <input type="hidden" name="x_zip" value=""/>
	    <input type="hidden" name="x_country" value=""/>
	    <input type="hidden" name="x_phone" value=""/>
	    <input type="hidden" name="x_fax" value=""/>
	    <input type="hidden" name="x_email" value=""/>
	    <input type="hidden" name="x_ship_to_first_name" value=""/>
	    <input type="hidden" name="x_ship_to_last_name" value=""/>
	    <input type="hidden" name="x_ship_to_company" value=""/>
	    <input type="hidden" name="x_ship_to_address" value=""/>
	    <input type="hidden" name="x_ship_to_city" value=""/>
	    <input type="hidden" name="x_ship_to_state" value=""/>
	    <input type="hidden" name="x_ship_to_zip" value=""/>
	    <input type="hidden" name="x_ship_to_country" value=""/>
	    <input type="hidden" name="x_tax" value="0.0000"/>
	    <input type="hidden" name="x_duty" value="0.0000"/>
	    <input type="hidden" name="x_freight" value="0.0000"/>
	    <input type="hidden" name="x_tax_exempt" value="FALSE"/>
	    <input type="hidden" name="x_po_num" value=""/>
	    <input type="hidden" name="x_MD5_Hash" value="A375D35004547A91EE3B7AFA40B1E727"/>
	    <input type="hidden" name="x_cavv_response" value=""/>
	    <input type="hidden" name="x_test_request" value="false"/>
	    Subscription ID <input type="input" name="x_subscription_id" value="365314"/>
	    <br/>
	    <input type="hidden" name="x_subscription_paynum" value="1"/>
	    <input type="submit"/>
	</form>
</div>