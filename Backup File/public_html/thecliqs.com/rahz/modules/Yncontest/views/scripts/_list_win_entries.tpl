<div>  	
	<div id="yncontest_recent_win_entries_container_<?php echo $this->tab?>">
	<?php $contestCount = $this->paginator->getTotalItemCount(); ?>
	
	<?php if ($contestCount > 0) : ?>
	    <script type="text/javascript">
	        en4.core.runonce.add(function(){	            
	            <?php if (!$this->renderOne): ?>
	              <?php echo $this->paginator->getCurrentPageNumber();?>
	                
	                $('yncontest_win_entries_previous_<?php echo $this->tab?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
	                $('yncontest_win_entries_next_<?php echo $this->tab?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
					
	                $('yncontest_win_entries_previous_<?php echo $this->tab?>').removeEvents('click').addEvent('click', function(){	                	
	                    en4.core.request.send(new Request.HTML({
	                    	url : en4.core.baseUrl + 'contest/my-entries/ajax-win-entries/',
	                        data : {
	                        	format : 'html',
	                        	contestId : <?php echo $this->contestId?>,
	                        	tab : '<?php echo $this->tab?>',
 	                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
	                    	},
	                    	 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                    		$('yncontest_recent_win_entries_container_<?php echo $this->tab?>').innerHTML = responseHTML;
		                            //element.set('html', responseHTML);  
		                           // eval(responseJavaScript);                       
		                    }
	                	}));
	                });
	
	                $('yncontest_win_entries_next_<?php echo $this->tab?>').removeEvents('click').addEvent('click', function(){	                	
	                    en4.core.request.send(new Request.HTML({
	                        url : en4.core.baseUrl + 'contest/my-entries/ajax-win-entries/',
	                        data : {
	                            format : 'html',
	                            contestId : <?php echo $this->contestId?>,
	                             tab : '<?php echo $this->tab?>',
	                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
	                        },
	                    
	                        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	                    		$('yncontest_recent_win_entries_container_<?php echo $this->tab?>').innerHTML = responseHTML;
	                            //element.set('html', responseHTML);  
	                           // eval(responseJavaScript);                       
	                        }
	                    })); 
	                });            
	            <?php endif; ?>
	        });
	    </script>
	
	    <ul class="generic_list_widget yncontest_widget ynContests_browse yncontest_frame yncontest_list " id="yncontest_recent_contests">
	        <?php foreach ($this->paginator as $item) : ?>
	            <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
	            <?php
	        		echo $this->partial('_win_entries_listing.tpl', 'yncontest', array(
	        			'entries'     => $item,
	        			'recentCol' => $this->recentCol,
						'tab' => $this->tab,
	        		));
	            ?>
	            </li>
	        <?php endforeach; ?>        
	    </ul>
		
		<div>
	        <div id="yncontest_win_entries_previous_<?php echo $this->tab?>" class="paginator_previous">
	            <?php
	            	echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
	            		'onclick' => '',
	            		'class'   => 'buttonlink icon_previous'
	            	));
	            ?>
	        </div>
	        <div id="yncontest_win_entries_next_<?php echo $this->tab?>" class="paginator_next">
	            <?php
	            	echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
	            		'onclick' => '',
	            		'class'   => 'buttonlink link_yncontest_right icon_next'
	            	));
	            ?>
	        </div>
	        <div class="clear"></div>
		</div>	
	    
	<?php else : ?>
<!-- 	    <div class="tip"> -->
<!-- 	        <span> -->
	            <?php //echo $this->translate('There are no winning entries.'); ?>
	           
<!-- 	        </span> -->
<!-- 	    </div> -->
	<?php endif; ?>
</div>
</div>