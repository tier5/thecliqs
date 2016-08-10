function favourite(game_id)
{
	new Request.JSON ({
		'url' : en4.core.baseUrl + 'socialgames/ajax/addfavourite',
		'method' : 'post',
		'data' : {
			'game_id' : game_id,
			'format':'json'
		},
	'onComplete' : function(result) 
	{
		if (result["status"])
		{
			$("fav_text").innerHTML = "<i class='fa fa-star-o'></i> " + en4.core.language.translate("Add favourite");
		}
		else
		{
			$("fav_text").innerHTML = "<i class='fa fa-star'></i> " + en4.core.language.translate("Remove favourite");
		}
	}
	}).send();
}
function play(game_id)
{
	new Request.JSON ({
		'url' : en4.core.baseUrl + 'socialgames/ajax/play',
		'method' : 'post',
		'data' : {
			'game_id' : game_id,
			'format':'json'
		},
	'onComplete' : function(result) 
	{
		if (result["status"])
		{
			$$("div.game_button").hide();
			$$("div.game_object").show();
		}
	}
	}).send();
}