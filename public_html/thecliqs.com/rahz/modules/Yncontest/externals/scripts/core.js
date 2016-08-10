en4.yncontest = {
		
		//<!-- Fix Display the content (active tab) for Member Profile page  -->
		//*** application/modules/Core/externals/scripts/core.js

				
		changeCategory : function(element, name, model) {
			console.log(element);
			if (element != null) {
				element.form[name].value = element.value;
				//}
				var e = element.name;
				var prefix = 'id_wrapper_' + name + '_';
				var level = element.name.replace(name + '_', '');
				level = parseInt(level);
				var ne = document.getElementById(prefix + (level + 1));
				if (name == 'location_id') {
					var max = 3;
				}
				else {
					var max = 9;
				}
		
				for (i = level; i < max; i++) {
					if ((document.getElementById(prefix + (i + 1)))) {
						document.getElementById(prefix + (i + 1)).setStyle('display', 'none');
					}
				}
				;
		
				var request = new Request({
					'url' : en4.core.baseUrl  + 'contest/my-contest/change-multi-level',
					'data' : {
						'format' : 'html',
						'id' : element.value,
						'name' : name,
						'level' : level,
						'model' : model
					},
					'onComplete' : function(a) {
						if (a != '') {
							ne.setStyle('margin-top', '8px');
							ne.setStyle('display', 'block');
							ne.innerHTML = a;
						}
					}
				});
				request.send();
			}
			else {
			
				
			}		
			
		},
		
		follow : function(contest_id, user_id, text_url) {
			var request = new Request.JSON(
					{
						'format' : 'json',
						'url' :  en4.core.baseUrl + 'contest/my-contest/follow',
						'data' : {
							'user_id' : user_id,
							'contestId' : contest_id
						},
						'onComplete' : function(response) {
							if (response.signin == 0) {
								window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
								return;
							}
							var ele_array = $$('.contest_follow_' + contest_id);
							var length = ele_array.length;
							for (i = 0; i < length; i++) {
								ele_array[i].innerHTML = response.text;
								if(response.follow){
									$(ele_array[i]).removeClass("contest_follow_follow").addClass("contest_follow_follow");	
								}else{
									$(ele_array[i]).removeClass("contest_follow_unfollow").addClass("contest_follow_unfollow");
								}
							}
						}
					});
			request.send();
		},
		fav : function(contest_id, user_id, text_url) {
			var request = new Request.JSON(
					{
						'format' : 'json',
						  'url' :  en4.core.baseUrl + 'contest/my-contest/favourite-contest',
						'data' : {
							'user_id' : user_id,
							'contestId' : contest_id
						},
						'onSuccess' : function(response) {
							if (response.signin == 0) {
								window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
								return;
							}
							var ele_array = $$('.contest_fav_' + contest_id);
							var length = ele_array.length;
							for (i = 0; i < length; i++) {
								ele_array[i].innerHTML = response.text;
								if(response.favourite){
									$(ele_array[i]).removeClass("contest_fav_favourite").addClass("contest_fav_unfavourite");	
								}else{
									$(ele_array[i]).removeClass("contest_fav_unfavourite").addClass("contest_fav_favourite");
								}
							}
						}
					});
			request.send();
		},
		entriesfav : function(entry_id, user_id, text_url) {
			var request = new Request.JSON(
					{
						'format' : 'json',
						  'url' :  en4.core.baseUrl + 'contest/my-entries/favourite-entries',
						'data' : {
							'user_id' : user_id,
							'entriesId' : entry_id
						},
						'onSuccess' : function(response) {
							if (response.signin == 0) {
								window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
								return;
							}
							var ele_array = $$('.entries_fav_' + entry_id);
							var length = ele_array.length;
							for (i = 0; i < length; i++) {
								ele_array[i].innerHTML = response.text;
								if(response.favourite){
									$(ele_array[i]).removeClass("entries_fav_favourite").addClass("entries_fav_unfavourite");	
								}else{
									$(ele_array[i]).removeClass("entries_fav_unfavourite").addClass("entries_fav_favourite");
								}
							}
						}
					});
			request.send();
		},
		
		
}