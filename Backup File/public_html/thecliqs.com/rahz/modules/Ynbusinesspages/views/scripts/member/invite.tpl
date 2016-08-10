<?php
 $session = new Zend_Session_Namespace('mobile');
 $onclick = 'parent.Smoothbox.close();';
 if($session -> mobile)
{
$onclick = 'history.go(-1); return false;';
}
?>
<script type="text/javascript">
    var current_page = 1;
    var keysearch = "";
    var url = '<?php echo $this->url(array('controller' => 'member','action'=>'ajax-get-friends', 'business_id' => $this -> business_id), 'ynbusinesspages_extended') ?>'
    function ajaxGetFriends()
    {
        var search_mode = $('search_mode').value;
        var request = new Request.HTML({
            url : url,
            data : {
                page : current_page,
                search : keysearch,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                responseHTML = responseHTML.replace(/<\/?span[^>]*>/g,"");
                if(current_page == 1) {
                    if(search_mode =="on")
                    {
                        document.getElementById("friend_list_container").innerHTML = responseHTML;
                    }
                    else
                    {
                        document.getElementById("friend_list_container").innerHTML += responseHTML;
                    }
                }
                else
                {
                    document.getElementById("load_more_container").outerHTML = responseHTML;
                }
                current_page ++;
                var el = document.getElementById('viewmore');
                if(el) {
                    $('viewmore').addEvent('click', function () {
                        ajaxGetFriends();
                    })
                }
            }
        });
        request.send();
    }

    window.addEvent('domready', function(){
        ajaxGetFriends();
    });

    en4.core.runonce.add(function(){
        $('selectall').addEvent('click', function(){
            var checked  = $(this).checked;
            var checkboxes = $$('input[type=checkbox][name=users[]]');
            checkboxes.each(function(item){
                item.checked = checked;
            });
        })

        $('friends_search').addEvent('keypress', function(e) {
            if( e.key == 'enter' )
            {
                e.preventDefault();
                current_page = 1;
                $('search_mode').value = "on";
                keysearch = this.value;
                ajaxGetFriends();
            }
        })
    });
</script>
<div id="ymb_scroller">
    <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
</div>
<input type="hidden" id="search_mode" name="search_mode" value=""/>

