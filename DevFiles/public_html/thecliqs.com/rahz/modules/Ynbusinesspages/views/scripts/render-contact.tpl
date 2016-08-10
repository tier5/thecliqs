<li class="business-contact">
    <div class="business-phone">
    <?php $url = $this -> url(
        array(
            'action' => 'tooltip', 
            'type' => 'phone', 
            'business_id' => $this->business -> getIdentity(),
        )
        ,'ynbusinesspages_specific', true);
    ?>
        <a href="<?php echo $url?>" onclick="event.preventDefault();" class="ynbusinesspages-business-listing-item-tooltip">
            <i class="fa fa-phone"></i><?php echo $this->translate('Phone Number')?>
        </a>
    </div>
    <div class="business-email have-popup">
        <a href="mailto:<?php echo $this->business->email;?>">
        <i class="fa fa-envelope"></i><?php echo $this->translate('Email')?>
    </a>
    </div>
    <div class="business-website">
    <?php $url = $this -> url(
        array( 
            'action' => 'tooltip', 
            'type' => 'website', 
            'business_id' => $this->business -> getIdentity(),
           )
        ,'ynbusinesspages_specific', true);
    ?>
    <a href="<?php echo $url?>" onclick="event.preventDefault();" class="ynbusinesspages-business-listing-item-tooltip">
        <i class="fa fa-globe"></i><?php echo $this->translate('Website')?>
    </a>
    </div>
    
    <script type="text/javascript">
         //script for tooltip
    
        var businessTooltip = {
            ele : 0,
            href : 0,
            timeoutId : 0,
            isShowing: 0,
            cached: {},
            dir: {cx:0,cy:0},
            isShowing: 0,
            isMouseOver: 1,
            mouseOverTimeoutId: 0,
            box:0,
            timeoutOpen: 300,
            timeoutClose: 300,
            boxContent: 0,
            setTimeoutOpen: function(time){
                businessTooltip.timeoutOpen = time;
                return businessTooltip;
            },
            clearCached: function(){
                businessTooltip.cached = {};
            },
            openSmoothBox: function(href){
                // create an element then bind to object
                var a = new Element('a', {
                    href : href,
                    'style' : 'display:none'
                });
                var body = document.getElementsByTagName('body')[0];
                a.inject(body);
                Smoothbox.open(a);
            },
            setTimeoutClose: function(time){
                businessTooltip.timeoutClose = time;
                return businessTooltip;
            },
            boot : function() {
                $$('.ynbusinesspages-business-listing-item-tooltip').each(function(el) {
                    el.addEvent('mouseover', businessTooltip.check);
                });
            },
            check : function(e) {
                if(e.target == null && e.target == undefined){
                    return;
                }
        
                var a = e.target;
                var ele = e.target;
        
                if(a.getAttribute == null || a.getAttribute == undefined){
                    return;
                }
        
                var href = a.getAttribute('href');
                if(href == null && href == undefined){
                    return;
                }
        
                businessTooltip.ele = $(ele);
                businessTooltip.href = href;
                if(businessTooltip.timeoutId) {
                    try {
                        window.clearTimeout(businessTooltip.timeoutId);
                    } catch(e) {
        
                    }
                }
                $(a).addEvent('mouseleave',function(){businessTooltip.resetTimeout(0);});
                businessTooltip.timeoutId = 0;
                businessTooltip.isRunning = 0;
                businessTooltip.dir.cx = e.event.clientX;
                businessTooltip.dir.cy = e.event.clientY;
                businessTooltip.timeoutId = window.setTimeout('businessTooltip.requestPopup()', businessTooltip.timeoutOpen);
                return ;
    
        
            },
            updateBoxContent: function(html){
              businessTooltip.boxContent.innerHTML = html;
              return businessTooltip;
            },
            startSending: function(html){
              businessTooltip.boxContent.innerHTML = '<div class="uiContextualDialogContent"> \
                                        <div class="uibusinessTooltipHovercardStage"> \
                                            <div class="uibusinessTooltipHovercardContent"> \
                                            ' +html+ ' \
                                            </div> \
                                        </div> \
                                    </div> \
                                    ';
                return businessTooltip;
        
            },
            requestPopup : function() {
                businessTooltip.timeoutId = 0;
                var box = businessTooltip.getBox();
                box.style.display = 'none';
        
                var key = businessTooltip.href;
                if(businessTooltip.cached[key] != undefined){
                    businessTooltip.showPopup(businessTooltip.cached[key]);
                    return;
                }
                var jsonRequest = new Request.JSON({
                    url : businessTooltip.href,
                    onSuccess : function(json, text) {
                        businessTooltip.cached[key] = json;
                        businessTooltip.showPopup(json);
                    }
                }).get({type_show:'ajax'});
                businessTooltip.startSending(en4.core.language.translate('Loading...'));
                businessTooltip.resetPosition(1);
                return businessTooltip;
        
            },
            resetTimeout: function($flag){
                businessTooltip.isMouseOver = $flag;
                if(businessTooltip.mouseOverTimeoutId){
                    try{
                        window.clearTimeout(businessTooltip.mouseOverTimeoutId);
                        businessTooltip.mouseOverTimeoutId = 0;
                        if(businessTooltip.timeoutId){
                            try{
                                window.clearTimeout(businessTooltip.timeoutId);
                                businessTooltip.timeoutId = 0;
                            }catch(e){
                            }
                        }
                    }catch(e){
                    }
                }
                if($flag ==0){
                    businessTooltip.mouseOverTimeoutId = window.setTimeout('businessTooltip.closePopup()',businessTooltip.timeoutClose);
                }
                return businessTooltip;
        
            },
            closePopup: function(){
                box = businessTooltip.getBox();
                box.style.display = 'none';
                businessTooltip.isShowing = 0;
                return businessTooltip;
            },
            resetPosition: function(flag){
                businessTooltip.isShowing = 1;
                var box = businessTooltip.getBox();
                var ele =  businessTooltip.ele;
        
                if(!ele){
                    return ;
                }
                var pos = ele.getPosition();
                var size = ele.getSize();
        
                if(pos == null || pos == undefined){
                    return ;
                }
                
                if(businessTooltip.dir.cy >180){
                    box.style.top =  pos.y  +'px';
                    box.removeClass('uibusinessTooltipDialogDirDown').addClass('uibusinessTooltipDialogDirUp');
                }else{
                    box.style.top =  pos.y + size.y +'px';
                    box.removeClass('uibusinessTooltipDialogDirUp').addClass('uibusinessTooltipDialogDirDown');
                }
        
        
                if(en4.orientation=='ltr'){
                    // check the position of the content
        
                    if(window.getSize().x - businessTooltip.dir.cx > 350){
                        box.removeClass('uibusinessTooltipDialogDirLeft').addClass('uibusinessTooltipDialogDirRight');
                        var px = size.x > 200? businessTooltip.dir.cx:pos.x;
                        box.style.left =  px + 'px';
                    }else{
                        box.removeClass('uibusinessTooltipDialogDirRight').addClass('uibusinessTooltipDialogDirLeft');
                        var px = size.x > 200? businessTooltip.dir.cx:(pos.x+size.x);
                        box.style.left =  px + 'px';
                    }
                }else{
                    // right to left
                    if(businessTooltip.dir.cx< 310){
                        box.removeClass('uibusinessTooltipDialogDirLeft').addClass('uibusinessTooltipDialogDirRight');
                        var px = size.x > 200? businessTooltip.dir.cx:pos.x;
                        box.style.left =  px + 'px';
                    }else{
                        var px = size.x > 200? businessTooltip.dir.cx:(pos.x+size.x);
                        box.style.left =  px + 'px';
                        box.removeClass('uibusinessTooltipDialogDirRight').addClass('uibusinessTooltipDialogDirLeft');
                    }
        
                }
                if(flag){
                    box.style.display = 'block';
                }
        
        
            },
            showPopup : function(json) {
                if(json == null || json == undefined){
                    return ;
                }
                businessTooltip.resetPosition(1);
                var box = businessTooltip.getBox();
                businessTooltip.updateBoxContent(json.html);
                box.style.display='block';
                return businessTooltip;
            },
            getBox: function(){
                if(businessTooltip.box){
                    return businessTooltip.box;
                }
                
                var ct = document.createElement('DIV');
                ct.setAttribute('id','uibusinessTooltipDialog');
                var html = '<div class="uibusinessTooltipDialogOverlay" id="businessTooltipUiOverlay" onmouseover="businessTooltip.resetTimeout(1)" onmouseout="businessTooltip.resetTimeout(0)">'
                            + '<div class="uibusinessTooltipOverlayContent" id="businessTooltipUiOverlayContent">'
                            + '</div>'
                            + '<i class="uibusinessTooltipContextualDialogArrow"></i>'
                            + '</div>';
                ct.innerHTML = html;
                var body = document.getElementsByTagName('body')[0];
                body.appendChild(ct);
                $(ct).addClass('uibusinessTooltipDialog');
                businessTooltip.box = $('uibusinessTooltipDialog');
                businessTooltip.boxContent = $('businessTooltipUiOverlayContent');
                return businessTooltip.box;
            }
        };
        
        window.addEvent('domready', businessTooltip.boot);
    
    </script>
</li>