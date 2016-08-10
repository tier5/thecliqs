var feedTempUnlike = 0;
var feedTempLike = 0;
en4.ynfeed = {
    like: function(el, action_id, comment_id) 
    {
    	if (feedTempLike == 0) 
    	{
			feedTempUnlike = feedTempLike = 1;
	        if (el.retrieve('isActive', false))
	            return;
	        var oldHtml = el.innerHTML;
	        el.store('isActive', true);
	        el.innerHTML = el.get('action-title');
	        var element = el.getParent('.comment-likes-activity-item');
	        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
	        var comment_like_box_show_value = 0;
	        var show_all_comments_value = 0;
			if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
				show_all_comments_value = show_all_comments[action_id];
			}
			if ( typeof comment_like_box_show != 'undefined' && comment_like_box_show[action_id]) {
				comment_like_box_show_value = comment_like_box_show[action_id];
			}
	        en4.core.request.send(new Request.JSON({
	            url: en4.core.baseUrl + 'ynfeed/index/like',
	            data: {
	                format: 'json',
	                action_id: action_id,
	                comment_id: comment_id,
	                subject: en4.core.subject.guid,
	                onViewPage: hasViewPage,
	                comment_like_box_show: comment_like_box_show_value,
	                show_all_comments: show_all_comments_value
	            },
	            onSuccess: function(response, response2, response3, response4) {
	            	feedTempUnlike = feedTempLike = 0;
	                if ((!response && !response3 && $type(options.updateHtmlElement)) || ($type(response) == 'object' && $type(response.status) && response.status == false)) {
	                    el.store('isActive', false);
	                    el.innerHTML = oldHtml;
	                }
	                $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
	            }
	        }), {
	            'force': true,
	            'element': element
	        });
	    }
    },
    unlike: function(el, action_id, comment_id) {
    	if (feedTempUnlike == 0) 
    	{
    		feedTempUnlike = feedTempLike = 1;
	        if (el.retrieve('isActive', false))
	            return;
	        var oldHtml = el.innerHTML;
	        el.store('isActive', true);
	        el.innerHTML = el.get('action-title');
	        var element = el.getParent('.comment-likes-activity-item');
	        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
	        var comment_like_box_show_value = 0;
			if ( typeof comment_like_box_show != 'undefined' && comment_like_box_show[action_id]) {
				comment_like_box_show_value = comment_like_box_show[action_id];
			}
			var show_all_comments_value = 0;
			if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
				show_all_comments_value = show_all_comments[action_id];
			}
	        en4.core.request.send(new Request.JSON({
	            url: en4.core.baseUrl + 'ynfeed/index/unlike',
	            data: {
	                format: 'json',
	                action_id: action_id,
	                comment_id: comment_id,
	                subject: en4.core.subject.guid,
	                onViewPage: hasViewPage,
	                comment_like_box_show: comment_like_box_show_value,
	                show_all_comments: show_all_comments_value
	            },
	            onSuccess: function(response, response2, response3, response4) {
	            	feedTempUnlike = feedTempLike = 0;
	                if ((!response && !response3 && $type(options.updateHtmlElement)) || ($type(response) == 'object' && $type(response.status) && response.status == false)) {
	                    el.store('isActive', false);
	                    el.innerHTML = oldHtml;
	                }
	                $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
	            }
	        }), {
	            'force': true,
	            'element': element
	        });
	    }
    },
    viewComments: function(action_id) {

        if($('show_view_all_loading')) {
           $('show_view_all_loading').style.display ='block';
        }
        
        if($('comments_viewall')) {
           $('comments_viewall').style.display ='none';
        }
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'ynfeed/index/viewComment',
            data: {
                format: 'json',
                action_id: action_id,
                nolist: true,
            },
            onComplete : function(e) {
				$(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
			}
        }), {
            'element': $('activity-item-' + action_id),
            'updateHtmlMode': 'comments'
        });
    },
    removePreview: function(el, action_id, comment_id)
    {
    	if (el.retrieve('isActive', false))
            return;
        var oldHtml = el.innerHTML;
        el.store('isActive', true);
        el.innerHTML = el.get('action-title');
        var element = el.getParent('.comment-likes-activity-item');
        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
        var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'ynfeed/index/remove-link',
            data: {
                format: 'json',
                action_id: action_id,
                comment_id: comment_id,
                subject: en4.core.subject.guid,
                onViewPage: hasViewPage,
                show_all_comments: show_all_comments_value
            },
            onSuccess: function(response, response2, response3, response4) {
                if ((!response && !response3 && $type(options.updateHtmlElement)) || ($type(response) == 'object' && $type(response.status) && response.status == false)) {
                    el.store('isActive', false);
                    el.innerHTML = oldHtml;
                }
                $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
            }
        }), {
            'force': true,
            'element': element
        });
    },
    openHideComment: function(el, action_id, comment_id) 
    {
        if (el.retrieve('isActive', false))
            return;
        el.store('isActive', true);
        var element = el.getParent('.comment-likes-activity-item');
        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
        var oldHtml = '';
    	if ($('comment-' + comment_id)) 
    	{
    		oldHtml = $('comment-' + comment_id).innerHTML;
			$('comment-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		if ($('reply-' + comment_id)) 
    	{
    		oldHtml = $('reply-' + comment_id).innerHTML;
			$('reply-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'ynfeed/index/open-hide-comment',
            data: {
                format: 'json',
                action_id: action_id,
                comment_id: comment_id,
                subject: en4.core.subject.guid,
                onViewPage: hasViewPage,
                show_all_comments: show_all_comments_value
            },
            onSuccess: function(response, response2, response3, response4) {
                if ((!response && !response3 && $type(options.updateHtmlElement)) || ($type(response) == 'object' && $type(response.status) && response.status == false)) {
                    el.store('isActive', false);
                    if ($('comment-' + comment_id)) 
    				{
                    	$('comment-' + comment_id).innerHTML = oldHtml;
                    }
                    if ($('reply-' + comment_id)) 
    				{
                    	$('reply-' + comment_id).innerHTML = oldHtml;
                    }
                }
                $(document.body).addLiveEvent('click', 'span.yncomment_comment_dropbox', openCommentOptions);
            }
        }), {
            'force': true,
            'element': element
        });
    },
    unHideComment: function(el, action_id, comment_id) 
    {
        if (el.retrieve('isActive', false))
            return;
        el.store('isActive', true);
        var element = el.getParent('.comment-likes-activity-item');
        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
        var oldHtml = '';
    	if ($('comment-' + comment_id)) 
    	{
    		oldHtml = $('comment-' + comment_id).innerHTML;
			$('comment-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		if ($('reply-' + comment_id)) 
    	{
    		oldHtml = $('reply-' + comment_id).innerHTML;
			$('reply-' + comment_id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
		}
		var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'ynfeed/index/un-hide-comment',
            data: {
                format: 'json',
                action_id: action_id,
                comment_id: comment_id,
                subject: en4.core.subject.guid,
                onViewPage: hasViewPage,
                show_all_comments: show_all_comments_value
                
            },
            onSuccess: function(response, response2, response3, response4) {
                if ((!response && !response3 && $type(options.updateHtmlElement)) || ($type(response) == 'object' && $type(response.status) && response.status == false)) {
                    el.store('isActive', false);
                    if ($('comment-' + comment_id)) 
    				{
                    	$('comment-' + comment_id).innerHTML = oldHtml;
                    }
                    if ($('reply-' + comment_id)) 
    				{
                    	$('reply-' + comment_id).innerHTML = oldHtml;
                    }
                }
            }
        }), {
            'force': true,
            'element': element
        });
    },
    deleteComment: function(action_id, comment_id)
    {
        var element = '';
        if ($('comment-' + comment_id))
        {
        	element = $('comment-' + comment_id).getParent('.comment-likes-activity-item');
            $('comment-' + comment_id).destroy();
        }
        if ($('reply-' + comment_id))
        {
        	element = $('reply-' + comment_id).getParent('.comment-likes-activity-item');
            $('reply-' + comment_id).destroy();
        }
        var hasViewPage = element.get('id').indexOf('view') < 0 ? 0 : 1;
        var show_all_comments_value = 0;
		if ( typeof show_all_comments != 'undefined' && show_all_comments[action_id]) {
			show_all_comments_value = show_all_comments[action_id];
		}
        YnfeedSmoothboxClose();
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'ynfeed/index/delete',
            data: {
                format: 'json',
                action_id: action_id,
                comment_id: comment_id,
                subject: en4.core.subject.guid,
                onViewPage: hasViewPage,
                show_all_comments: show_all_comments_value
            },
            onSuccess: function(response, response2, response3, response4) {
            }
        }), {
            'force': true,
            'element': element
        });
    },
};


// Emoticons
function setEmotionLabelPlate(label, symbol) {
	$('emotion_label').innerHTML = label;
	$('emotion_symbol').innerHTML = symbol;
}

function addEmotionIcon(iconCode) {
	var hi = $('ynfeed_activity_body_hightlighter');
	var hi2 = $('ynfeed_input_hidden');
	var input = $('ynfeed_activity_body');
	if (input.value == input.getAttribute('placeholder')) {
		input.value = '';
	}
	if (input.value == '') {
		if ($('ynfeed_composer_tab')) {
			$('ynfeed_composer_tab').style.display = '';
		}
	}
	if (hi)
		hi.innerHTML += iconCode;
	if (hi2)
		hi2.value += iconCode;
	input.value += iconCode;
}

function setEmoticonsBoard() {
	$('emotion_label').innerHTML = "";
	$('emotion_symbol').innerHTML = "";
	hideEmotionIconClickEnable = true;
	var a = $('emoticons-button');
	a.toggleClass('emoticons_active');
	a.toggleClass('');
	var el = $('emoticons-board');
	el.toggleClass('ynfeed_embox_open');
	el.toggleClass('ynfeed_embox_closed');
}

// Tag with friends
function toogleTagWith() {
	var el = $('ynfeed_withfriends');
	if (el.style.display == 'block') 
	{
		el.style.display = 'none';
	} 
	else 
	{
		el.style.display = 'block';
		el.focus();
	}
	if($('ynfeed_checkin'))
		$('ynfeed_checkin').style.display = 'none';
	if($('ynfeed_checkin'))
		$('ynfeed_atbusiness').style.display = 'none';
}
function openTagWith()
{
	if($('ynfeed_withfriends'))
	{
		$('ynfeed_withfriends').style.display = 'block';
		$('ynfeed_withfriends').focus();
	}
	if($('ynfeed_checkin'))
		$('ynfeed_checkin').style.display = 'none';
	if($('ynfeed_atbusiness'))
		$('ynfeed_atbusiness').style.display = 'none';
}
// Business
function toogleBusiness() {
	var el = $('ynfeed_atbusiness');
	if (el.style.display == 'block') 
	{
		el.style.display = 'none';
	} 
	else 
	{
		el.style.display = 'block';
		el.focus();
	}
	if($('ynfeed_withfriends'))
		$('ynfeed_withfriends').style.display = 'none';
	if($('ynfeed_checkin'))
		$('ynfeed_checkin').style.display = 'none';
}
function openBusiness()
{
	if($('ynfeed_atbusiness'))
	{
		$('ynfeed_atbusiness').style.display = 'block';
		$('ynfeed_atbusiness').focus();
	}
	if($('ynfeed_withfriends'))
		$('ynfeed_withfriends').style.display = 'none';
	if($('ynfeed_checkin'))
		$('ynfeed_checkin').style.display = 'none';
}
// Checkin 
function toogleCheckin() {
	var el = $('ynfeed_checkin');
	if (el.style.display == 'block') 
	{
		el.style.display = 'none';
	} 
	else 
	{
		el.style.display = 'block';
		el.focus();
	}
	if($('ynfeed_withfriends'))
		$('ynfeed_withfriends').style.display = 'none';
	if($('ynfeed_atbusiness'))
		$('ynfeed_atbusiness').style.display = 'none';
}
function openCheckin()
{
	if($('ynfeed_checkin'))
	{
		$('ynfeed_checkin').style.display = 'block';
		$('ynfeed_checkin').focus();
	}
	if($('ynfeed_withfriends'))
		$('ynfeed_withfriends').style.display = 'none';
	if($('ynfeed_atbusiness'))
		$('ynfeed_atbusiness').style.display = 'none';
}
function changeCheckin()
{
	if($('ynfeed_checkin_display'))	
		$('ynfeed_checkin_display').innerHTML = "";
	if($('ynfeed_withfriends_content').innerHTML == "")
	{
		$('ynfeed_mdash').innerHTML = '';
		$('ynfeed_dot').innerHTML = '';
	}
	
	$('ynfeed_checkinValue').removeClass('checkin_selected');		
		
	if($('ynfeed_checkin'))
	{
		$('ynfeed_checkin').removeClass('checkin_selected');
	}
	$('checkin-button').removeClass('checkin_active');
	$('checkin_lat').value = '';
	$('checkin_long').value = '';
	$('ynfeed_removeCheckin').style.display = 'none';
}
function removeCheckin()
{
	$('ynfeed_checkinValue').value = '';
	changeCheckin();
	if($('business-button'))
	{
		$('business-button').style.display = 'block';
	}
}

// view more options
function updateSaveFeed(action_id)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/update-save-feed',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function updateNotification(action_id, value)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/update-notification',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid,
        value: value
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function updateComment(action_id)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/update-comment',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function updateLock(action_id)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/update-lock',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function removeTag(action_id)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/remove-tag',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function removePreview(action_id)
{
	en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + 'ynfeed/index/remove-preview',
      data: {
        format: 'json',
        action_id: action_id,
        subject: en4.core.subject.guid
      }
    }), {
      'element': $('activity-item-' + action_id),
      'updateHtmlMode': 'comments'
    });

}

function hideOptions()
{
	$$('.ynfeed_pulldown_open').each(function(item, index){
        item.removeClass('ynfeed_pulldown_open');
    });	
}
function hideItemFeeds (type, id, parent_type, parent_id, parent_html, report_url) 
{
	 if (en4.core.request.isRequestActive())
          return;
        var url = en4.core.baseUrl + 'ynfeed/index/hide-item';
        var req = new Request.JSON({
          url: url,
          data: {
            format: 'json',
            type: type,
            id: id
          },
          onComplete: function(responseJSON) 
          {
            if (type == 'activity_action' && $('activity-item-' + id)) 
            {
              if ($('activity-item-undo-' + id))
              {
                $('activity-item-undo-' + id).destroy();
              }
              $('activity-item-' + id).style.display = 'none';
              var innerHTML = "<li id='activity-item-undo-" + id + "'><div class='feed_item_hide'>"
                      + "<b>"+ langs['message_hide_feed'] +".</b>" + " <a href='javascript:void(0);' onclick='unhideItemFeed(\"" + type + "\" , \"" + id + "\")'>" + langs['Undo'] + " </a> <br /> ";
              if (report_url == '') {
	              innerHTML = innerHTML + "<span> <a href='javascript:void(0);' onclick='hideItemFeeds(\"" + parent_type + "\" , \"" + parent_id + "\",\"\",\"" + id + "\", \"" + parent_html + "\",\"\")'>"
	                    + langs['hide_all_by'] + ' ' + parent_html + "</a></span>";
			  }
			  else
			  {
			  		innerHTML = innerHTML + "<span> " + en4.core.language.translate(langs['message_report'], "<a href='javascript:void(0);' onclick='Smoothbox.open(\"" + report_url + "\")'>" + langs['file a report'] + "</a>") + ".</span>";
			  }
              innerHTML = innerHTML + "</div></li>";
              Elements.from(innerHTML).inject($('activity-item-' + id), 'after');

            } 
            else 
            {
              if ($('activity-item-undo-' + parent_id))
                $('activity-item-undo-' + parent_id).destroy();
              var innerHTML = "<li id='activity-item-undo-" + id + "'><b>" + en4.core.language.translate(langs['message_hide_from_user'], parent_html) + "</b> <a href='javascript:void(0);' onclick='unhideItemFeed(\"" + type + "\" , \"" + id + "\")'>" + langs['Undo'] + " </a>" + "</li>";
              Elements.from(innerHTML).inject($('activity-item-' + parent_id), 'after');

              var className = '.Hide_' + type + '_' + id;
              var myElements = $$(className);
              for (var i = 0; i < myElements.length; i++) {
                myElements[i].style.display = 'none';
              }
            }

            $$('.ynfeed_pulldown_open').each(function(item, index){
		        item.removeClass('ynfeed_pulldown_open');
		    });		
          }
        });
        req.send();
}
function unhideItemFeed(type, id) 
{
    if (unhideReqActive)
      return;
    unhideReqActive = true;
    var url = en4.core.baseUrl + 'ynfeed/index/un-hide-item';
    var req = new Request.JSON({
      url: url,
      data: {
        format: 'json',
        type: type,
        id: id
      },
      onComplete: function(responseJSON) {
        if ($('activity-item-undo-' + id))
          $('activity-item-undo-' + id).destroy();
        if (type == 'activity_action' && $('activity-item-' + id)) {

          $('activity-item-' + id).style.display = '';
        } else {
          var className = '.Hide_' + type + '_' + id;
          var myElements = $$(className);
          for (var i = 0; i < myElements.length; i++) {
            myElements[i].style.display = '';
          }
        }
        unhideReqActive = false;

        $$('.ynfeed_pulldown_open').each(function(item, index){
	        item.removeClass('ynfeed_pulldown_open');
	    });		
      }
    });
    req.send();
}

(function() {// START NAMESPACE
	var $ = 'id' in document ? document.id : window.$;

	en4.activity = {

		load : function(next_id, subject_guid) {
			if (en4.core.request.isRequestActive())
				return;

			$('feed_viewmore').style.display = 'none';
			$('feed_loading').style.display = '';

			en4.core.request.send(new Request.HTML({
				url : en4.core.baseUrl + 'ynfeed/widget/feed',
				data : {
					'maxid' : next_id,
					'feedOnly' : true,
					'nolayout' : true,
					'subject' : subject_guid
				}
			}), {
				'element' : $('activity-feed'),
				'updateHtmlMode' : 'append'
			});
		},

		like : function(action_id, comment_id) {
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/index/like',
				data : {
					format : 'json',
					action_id : action_id,
					comment_id : comment_id,
					subject : en4.core.subject.guid
				}
			}), {
				'element' : $('comment-likes-activity-item-' + action_id),
				'updateHtmlMode' : 'comments2'
			});
		},

		unlike : function(action_id, comment_id) {
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/index/unlike',
				data : {
					format : 'json',
					action_id : action_id,
					comment_id : comment_id,
					subject : en4.core.subject.guid
				}
			}), {
				'element' : $('comment-likes-activity-item-' + action_id),
				'updateHtmlMode' : 'comments2'
			});
		},

		comment : function(action_id, body) {
			if (body.trim() == '') {
				return;
			}

			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/index/comment',
				data : {
					format : 'json',
					action_id : action_id,
					body : body,
					subject : en4.core.subject.guid
				}
			}), {
				'element' : $('comment-likes-activity-item-' + action_id),
				'updateHtmlMode' : 'comments2'
			});
		},

		attachComment : function(formElement) {
			var bind = this;
			formElement.addEvent('submit', function(event) {
				event.stop();
				bind.comment(formElement.action_id.value, formElement.body.value);
			});
		},

		viewComments : function(action_id) {
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/index/viewComment',
				data : {
					format : 'json',
					action_id : action_id,
					nolist : true
				}
			}), {
				'element' : $('activity-item-' + action_id),
				'updateHtmlMode' : 'comments'
			});
		},

		viewLikes : function(action_id) {
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/index/viewLike',
				data : {
					format : 'json',
					action_id : action_id,
					nolist : true
				}
			}), {
				'element' : $('activity-item-' + action_id),
				'updateHtmlMode' : 'comments'
			});
		},

		hideNotifications : function(reset_text) {
			en4.core.request.send(new Request.JSON({
				'url' : en4.core.baseUrl + 'activity/notifications/hide'
			}));
			$('updates_toggle').set('html', reset_text).removeClass('new_updates');

			if ($('notifications_main')) {
				var notification_children = $('notifications_main').getChildren('li');
				notification_children.each(function(el) {
					el.setAttribute('class', '');
				});
			}

			if ($('notifications_menu')) {
				var notification_children = $('notifications_menu').getChildren('li');
				notification_children.each(function(el) {
					el.setAttribute('class', '');
				});
			}
		},

		updateNotifications : function() {
			if (en4.core.request.isRequestActive())
				return;
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/notifications/update',
				data : {
					format : 'json'
				},
				onSuccess : this.showNotifications.bind(this)
			}));
		},

		showNotifications : function(responseJSON) {
			if (responseJSON.notificationCount > 0) {
				$('updates_toggle').set('html', responseJSON.text).addClass('new_updates');
			}
		},

		markRead : function(action_id) {
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/notifications/test',
				data : {
					format : 'json',
					'actionid' : action_id
				}
			}));
		},

		cometNotify : function(responseObject) {
			$('core_menu_mini_menu_updates').style.display = '';
			$('core_menu_mini_menu_updates_count').innerHTML = responseObject.text;
		}
	};

	NotificationUpdateHandler = new Class({

		Implements : [Events, Options],
		options : {
			debug : false,
			baseUrl : '/',
			identity : false,
			delay : 5000,
			admin : false,
			idleTimeout : 600000,
			last_id : 0,
			subject_guid : null
		},

		state : true,

		activestate : 1,

		fresh : true,

		lastEventTime : false,

		title : document.title,

		initialize : function(options) {
			this.setOptions(options);
		},

		start : function() {
			this.state = true;

			// Do idle checking
			this.idleWatcher = new IdleWatcher(this, {
				timeout : this.options.idleTimeout
			});
			this.idleWatcher.register();
			this.addEvents({
				'onStateActive' : function() {
					this.activestate = 1;
					this.state = true;
				}.bind(this),
				'onStateIdle' : function() {
					this.activestate = 0;
					this.state = false;
				}.bind(this)
			});

			this.loop();
		},

		stop : function() {
			this.state = false;
		},

		updateNotifications : function() {
			if (en4.core.request.isRequestActive())
				return;
			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'activity/notifications/update',
				data : {
					format : 'json'
				},
				onSuccess : this.showNotifications.bind(this)
			}));
		},

		showNotifications : function(responseJSON) {
			if (responseJSON.notificationCount > 0) {
				$('updates_toggle').set('html', responseJSON.text).addClass('new_updates');
			}
		},

		loop : function() {
			if (!this.state) {
				this.loop.delay(this.options.delay, this);
				return;
			}

			try {
				this.updateNotifications().addEvent('complete', function() {
					this.loop.delay(this.options.delay, this);
				}.bind(this));
			} catch( e ) {
				this.loop.delay(this.options.delay, this);
				this._log(e);
			}
		},

		// Utility

		_log : function(object) {
			if (!this.options.debug) {
				return;
			}

			// Firefox is dumb and causes problems sometimes with console
			try {
				if ( typeof (console) && $type(console)) {
					console.log(object);
				}
			} catch( e ) {
				// Silence
			}
		}
	});

	//(function(){

	en4.activity.compose = {

		composers : {},

		register : function(object) {
			name = object.getName();
			this.composers[name] = object;
		},

		deactivate : function() {
			for (var x in this.composers ) {
				this.composers[x].deactivate();
			}
			return this;
		}
	};

	en4.activity.compose.icompose = new Class({

		Implements : [Events, Options],

		name : false,

		element : false,

		options : {},

		initialize : function(element, options) {
			this.element = $(element);
			this.setOptions(options);
		},

		getName : function() {
			return this.name;
		},

		activate : function() {
			en4.activity.compose.deactivate();
		},

		deactivate : function() {

		}
	});

	//})();

	ActivityUpdateHandler = new Class({

		Implements : [Events, Options],
		options : {
			debug : true,
			baseUrl : '/',
			identity : false,
			delay : 5000,
			admin : false,
			idleTimeout : 600000,
			last_id : 0,
			next_id : null,
			subject_guid : null,
			showImmediately : false
		},

		state : true,

		activestate : 1,

		fresh : true,

		lastEventTime : false,

		title : document.title,

		//loopId : false,

		initialize : function(options) {
			this.setOptions(options);
		},

		start : function() {
			this.state = true;

			// Do idle checking
			this.idleWatcher = new IdleWatcher(this, {
				timeout : this.options.idleTimeout
			});
			this.idleWatcher.register();
			this.addEvents({
				'onStateActive' : function() {
					this._log('activity loop onStateActive');
					this.activestate = 1;
					this.state = true;
				}.bind(this),
				'onStateIdle' : function() {
					this._log('activity loop onStateIdle');
					this.activestate = 0;
					this.state = false;
				}.bind(this)
			});
			this.loop();
		},

		stop : function() {
			this.state = false;
		},

		checkFeedUpdate : function(action_id, subject_guid) {
			if (en4.core.request.isRequestActive())
				return;

			function getAllElementsWithAttribute(attribute) {
				var matchingElements = [];
				var values = [];
				var allElements = document.getElementsByTagName('*');
				for (var i = 0; i < allElements.length; i++) {
					if (allElements[i].getAttribute(attribute)) {
						// Element exists with attribute. Add to array.
						matchingElements.push(allElements[i]);
						values.push(allElements[i].getAttribute(attribute));
					}
				}
				return values;
			}

			var list = getAllElementsWithAttribute('data-activity-feed-item');
			this.options.last_id = Math.max.apply(Math, list);
			min_id = this.options.last_id + 1;

			var req = new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/name/ynfeed.feed',
				data : {
					'format' : 'html',
					'minid': min_id,
					'feedOnly' : true,
					'nolayout' : true,
					'subject' : this.options.subject_guid,
					'getUpdate' : true,
					'checkUpdate': true,
        			'actionFilter': this.options.filter_type,
        			'filterValue': this.options.filter_id
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			        if ($('feed-update')) {
			          $('feed-update').innerHTML = responseHTML;
			        }
			
			      }

			});
			en4.core.request.send(req, {
				'force': true
			});

			req.addEvent('complete', function() {
				(function() {
					if (this.options.showImmediately && $('feed-update').getChildren().length > 0) {
						$('feed-update').setStyle('display', 'none');
						$('feed-update').empty();
						this.getFeedUpdate(this.options.next_id);
					}
				}).delay(50, this);
			}.bind(this));

			// Start LOCAL STORAGE STUFF
			if (localStorage) {
				var pageTitle = document.title;
				//@TODO Refill Locally Stored Activity Feed

				// For each activity-item, get the item ID number Data attribute and add it to an array
				var feed = document.getElementById('activity-feed');
				// For every <li> in Feed, get the Feed Item Attribute and add it to an array
				var items = feed.getElementsByTagName("li");
				var itemObject = { };
				// Loop through each item in array to get the InnerHTML of each Activity Feed Item
				var c = 0;
				for (var i = 0; i < items.length; ++i) {
					if (items[i].getAttribute('data-activity-feed-item') != null) {
						var itemId = items[i].getAttribute('data-activity-feed-item');
						itemObject[c] = {
							id : itemId,
							content : document.getElementById('activity-item-' + itemId).innerHTML
						};
						c++;
					}
				}
				// Serialize itemObject as JSON string
				var activityFeedJSON = JSON.stringify(itemObject);
				localStorage.setItem(pageTitle + '-activity-feed-widget', activityFeedJSON);
			}

			// Reconstruct JSON Object, Find Highest ID
			if (localStorage.getItem(pageTitle + '-activity-feed-widget')) {
				var storedFeedJSON = localStorage.getItem(pageTitle + '-activity-feed-widget');
				var storedObj = eval("(" + storedFeedJSON + ")");

				//alert(storedObj[0].id); // Highest Feed ID
				// @TODO use this at min_id when fetching new Activity Feed Items
			}
			// END LOCAL STORAGE STUFF

			return req;
		},

		getFeedUpdate : function(last_id) {
			if (en4.core.request.isRequestActive())
				return;
			var min_id = this.options.last_id + 1;
			this.options.last_id = last_id;
			document.title = this.title;
			var req = new Request.HTML({
				url : en4.core.baseUrl + 'widget/index/name/ynfeed.feed',
				data : {
					'format' : 'html',
					'minid' : min_id,
					'feedOnly' : true,
					'nolayout' : true,
					'getUpdate' : true,
					'subject' : this.options.subject_guid,
					'actionFilter': this.options.filter_type,
        			'filterValue': this.options.filter_id
				}
			});
			en4.core.request.send(req, {
				'element' : $('activity-feed'),
				'updateHtmlMode' : 'prepend'
			});
			return req;
		},

		loop : function() {
			this._log('activity update loop start');

			if (!this.state) {
				this.loop.delay(this.options.delay, this);
				return;
			}

			try {
				this.checkFeedUpdate().addEvent('complete', function() {
					try {
						this._log('activity loop req complete');
						this.loop.delay(this.options.delay, this);
					} catch( e ) {
						this.loop.delay(this.options.delay, this);
						this._log(e);
					}
				}.bind(this));
			} catch( e ) {
				this.loop.delay(this.options.delay, this);
				this._log(e);
			}

			this._log('activity update loop stop');
		},

		// Utility
		_log : function(object) {
			if (!this.options.debug) {
				return;
			}

			try {
				if ('console' in window && typeof (console) && 'log' in console) {
					console.log(object);
				}
			} catch( e ) {
				// Silence
			}
		}
	});

})();
// END NAMESPACE