<?php
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');
?>
<html>
	<head>
		<title>Mobile API Debug Console</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="pragma" content="no-cache">
		<style type="text/css">
			input.text, textarea.textarea {
				width: 100%;
				padding: 3px 5px;
			}
			textarea,.textarea {
				height: 100px;
				width: 700px;
			}
			td {
				padding: 10px;
			}
			td.right {
				vertical-align: top;
				border-left: 1px solid #ccc;
			}
		</style>
		<script type="text/javascript" src="./application/modules/Ynmobile/externals/scripts/jquery-1.9.0.min.js"></script>
		<script type="text/javascript" src="./application/modules/Ynmobile/externals/scripts/jquery.json-2.4.js"></script>
		<script type="text/javascript">
            $(document).ready(function() {
                $('#gform').bind('submit', function(evt) {
                    evt.preventDefault();
                    doSubmit();
                });
            });
            function doSubmit(form) {
            	try{
	            	var sService = $('#sService').val().trim('/');

	            	if(!sService){
	            		alert('Request uri is required');
	            		$('#sService').focus();
	            		return ;
	            	}

	                var sData = $('#sData').val().trim();
	                var sToken = $('#sToken').val().trim();
	                var aData = {};
	                if (sData) {
	                    aData = $.evalJSON(sData);
	                }

					if(sToken)
					{
						aData['token'] = sToken;
					}

	                $('#send_data').html($.toJSON(aData));
	                $('#response_object').html('');

	                $.post('?m=lite&name=api2&module=ynmobile&request=' + sService, aData, function(text) {
	                	  try
	                	  {
		                		$('#response_text').html(text);
			                    var json = $.evalJSON(text);
			                    var json_text = $.toJSON(json);
			                    $('#response_object').html(json_text);
	                	  }
	                	  catch(err)
	                	  {
	                	  		console.log(text);
	                	  }
	                });
            	}catch(e){
                    console.log(e);
            		//alert(e.getMessage());
            	}
               	return false;
            }
		</script>
	</head>
	<body>
		<div style="width: 1100px;text-overflow: scroll;"><table>
			<tr>
				<td width="500">
				<form method="post" onsubmit="return 0;" id="gform" enctype="multipart/form-data">
					<div>
						<div>
							Request URI:
						</div>
						<div>
							<input class="text" maxlength="200" type="text" name="sService" value="" id="sService"/>
						</div>
					</div>
					<div>
						<div>
							Token:
						</div>
						<div>
							<input class="text" maxlength="200" type="text" name="sToken" value="" id="sToken"/>
						</div>
					</div>
					<div>
						<div>
							JSON notation
						</div>
						<div>
							<textarea class="textarea" name="sData" id="sData"></textarea>
						</div>
					</div>
					<div>
						<button type="submit" name="_submit">
							Submit
						</button>
						<button type="reset" name="_reset">
							Reset
						</button>
					</div>
				</form></td>
				<td class="right">
				<div>
					Send Data:
				</div>
				<div>
					<textarea id="send_data"></textarea>
				</div>
				<div>
					Response Data
				</div><textarea id="response_object"></textarea></td>
			</tr>
		</table></div>

	</body>
</html>
