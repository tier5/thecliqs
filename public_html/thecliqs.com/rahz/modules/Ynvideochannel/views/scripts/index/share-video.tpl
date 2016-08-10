<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
        var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
        var validationUrl = '<?php echo $this->url(array('action' => 'validation-video'), 'ynvideochannel_general', true) ?>';
        var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"); ?>";
        var checkingUrlMessage = "<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>";
        var current_code = "";
        var bTitleAutoFill = bDesAutoFill = false;

        var submitable = false;
        $('form-upload').addEvent('submit', function(obj) {
            if (submitable == true) {
                var btn = this.getElement('#upload-wrapper');
                if (btn) btn.hide();
            }
            return submitable;
        });

        var ignoreValidation = window.ignoreValidation = function()
        {
            $('upload-wrapper').show();
            $('validation').hide();
            $('code').value = current_code;
            submitable = true;
        }

        var ynvideochannel = {
            active : false,
            debug : false,
            currentUrl : null,
            currentTitle : null,
            currentDescription : null,
            currentImage : 0,
            currentImageSrc : null,
            imagesLoading : 0,
            images : [],
            maxAspect : (10 / 3), //(5 / 2), //3.1,
            minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),
            minSize : 50,
            maxPixels : 500000,
            monitorInterval: null,
            monitorLastActivity : false,
            monitorDelay : 500,
            maxImageLoading : 5000,
            attach : function() {
                var bind = this;
                $('url').addEvent('keyup', function() {
                    bind.monitorLastActivity = (new Date).valueOf();
                });
                var url_element = document.getElementById("url-element");
                var myElement = new Element("p");
                myElement.innerHTML = "test";
                myElement.addClass("description");
                myElement.id = "validation";
                myElement.style.display = "none";
                url_element.appendChild(myElement);

                var body = $('url');
                var lastBody = '';
                var lastMatch = '';
                (function() {
                    // Ignore if no change or url matches
                    if( body.value == lastBody || bind.currentUrl ) {
                        return;
                    }
                    // Ignore if delay not met yet
                    if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay ) {
                        return;
                    }
                    // Check for link
                    var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);

                    if( $type(m) && $type(m[0]) && lastMatch != m[0] )
                    {
                        ynvideochannel.validate(body.value);
                    }
                    lastBody = body.value;
                }).periodical(250);
            },
            validate : function(url)  {
                // extract v from url
                var myURI = new URI(url);
                var youtube_code = myURI.get('data')['v'];
                if( youtube_code === undefined ) {
                    youtube_code = myURI.get('file');
                }

                (new Request.JSON({
                    'url' : validationUrl,
                    'method': 'post',
                    'data' : {
                        'ajax' : true,
                        'code' : youtube_code
                    },
                    'onRequest' : function(){
                        $('validation').style.display = "block";
                        $('validation').innerHTML = checkingUrlMessage;
                        $('upload-wrapper').hide();
                    },
                    'onSuccess' : function(responseJSON) {
                        if (responseJSON.valid) {
                            $('upload-wrapper').show();
                            $('validation').hide();
                            $('code').value = youtube_code;
                            $('duration').value = responseJSON.duration;
                            $('largeThumbnail').value = responseJSON.largeThumbnail;
                            if($('title') && responseJSON.title)
                            {
                                if($('title').value == '')
                                {
                                    $('title').value = responseJSON.title;
                                    bTitleAutoFill = true;
                                }
                                else if(bTitleAutoFill)
                                {
                                    $('title').value = responseJSON.title;
                                }
                            }
                            if($('description') && responseJSON.description)
                            {
                                if($('description').value == '')
                                {
                                    $('description').value = responseJSON.description;
                                    bDesAutoFill = true;
                                }
                                else if(bDesAutoFill)
                                {
                                    $('description').value = responseJSON.description;
                                }
                            }
                            if($('video_image-element') && responseJSON.mediumThumbnail)
                            {
                                $('video_image-wrapper').show();
                                var imgElement = new Element("img");
                                imgElement.src = responseJSON.mediumThumbnail;
                                imgElement.className = 'ynvideochannel_sharevideo_image';
                                $('video_image-element').empty();
                                $('video_image-element').appendChild(imgElement);
                            }
                        } else {
                            $('upload-wrapper').hide();
                            current_code = youtube_code;
                            $('validation').innerHTML = validationErrorMessage;
                        }
                        submitable = responseJSON.valid;
                    }
                })).send();
            }
        }

        ynvideochannel.attach();

        var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
            'postVar' : 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices' : true,
            'filterSubset' : true,
            'multiple' : true,
            'injectChoice': function(token){
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
                new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
    });
    window.addEvent('domready', function() {
        $('category_id').addEvent('change', function () {
            $(this).getParent('form').submit();
        });

        if ($('0_0_1-wrapper')) {
            $('0_0_1-wrapper').setStyle('display', 'none');
        }

        $('upload-wrapper').hide();
        $('video_image-wrapper').hide();

    });
</script>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
<div class="tip">
        <span>
            <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.'); ?>
            <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage-videos'), 'ynvideochannel_general')); ?>
        </span>
</div>
<br/>
<?php else: ?>
<?php echo $this->form->render($this); ?>
<?php endif; ?>