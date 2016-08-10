

<div id="yncontest_recent_entries_container_<?php echo $this->tab?>">
	<?php $contestCount = $this->paginator->getTotalItemCount(); ?>
	<?php if ($contestCount > 0) : ?>
	    <script type="text/javascript">
	        en4.core.runonce.add(function(){	            
	            <?php if (!$this->renderOne): ?>
	                $('yncontest_entries_previous_<?php echo $this->tab?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
	                $('yncontest_entries_next_<?php echo $this->tab?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
					
	                $('yncontest_entries_previous_<?php echo $this->tab?>').removeEvents('click').addEvent('click', function(){	                	
	                    en4.core.request.send(new Request.HTML({
	                    	url : en4.core.baseUrl + 'contest/my-entries/ajax-tab-entries/',
	                        data : {
	                        	format : 'html',
	                        	contestId : <?php echo $this->contestId?>,
	    	                    tab : <?php echo $this->tab?>,
 	                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
	                    	},
	                    	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	                    		$('yncontest_recent_entries_container_<?php echo $this->tab?>').set('html', responseHTML);
	                    		console.log($('yncontest_recent_entries_container_<?php echo $this->tab?>').get('html'));
	                    		$('yncontest_recent_entries_container_<?php echo $this->tab?>').getChildren('a.smoothbox').addEvent('click', function() {
	                    			event.stop();
	                				Smoothbox.open(this);	
	                    		});
	                        }
	                	}));
	                });
	
	                $('yncontest_entries_next_<?php echo $this->tab?>').removeEvents('click').addEvent('click', function(){	                	
	                    en4.core.request.send(new Request.HTML({
	                        url : en4.core.baseUrl + 'contest/my-entries/ajax-tab-entries/',
	                        data : {
	                            format : 'html',
	                            contestId : <?php echo $this->contestId?>,
	                            tab : <?php echo $this->tab?>,
	                            page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
	                        },
	                        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	                    		$('yncontest_recent_entries_container_<?php echo $this->tab?>').set('html', responseHTML);
	                    		console.log($('yncontest_recent_entries_container_<?php echo $this->tab?>').get('html'));
	                    		$('yncontest_recent_entries_container_<?php echo $this->tab?>').getChildren('a.smoothbox').addEvent('click', function() {
	                    			event.stop();
	                				Smoothbox.open(this);	
	                    		});
	                        }
	                    })); 
	                });            
	            <?php endif; ?>
	        });
	    </script>	
	    <ul class="generic_list_widget yncontest_widget ynContests_browse yncontest_frame yncontest_list" id="yncontest_recent_contests">	        
	        <?php foreach ($this->paginator as $item) : ?>	        
	            <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
	            <?php	           		
	        		echo $this->partial('_entries_listing.tpl', 'yncontest', array(
	        			'entries'     => $item,
	        			'recentCol' => $this->recentCol,
						//'id' => $this->id,
	        		));
	            ?>
	            </li>
	        <?php endforeach; ?>    
			<!--<li>
	            <div class="wrap_contest">
					<div class="wrap_images">
						<div class="wrap_hover">
							<strong><a href="#">Video</a></strong>
							<p>by <a href="#">Helen</a>
						</div>
						<a href="/qc/xuannth/se4/luannd/contest/my-entries/view/id/2/slug/entry-2"><img class="thumb_profile item_photo_yncontest_entry  thumb_profile" alt="" src="/qc/xuannth/se4/luannd/public/album_photo/0e/000e_5975.jpg?c=ba74"></a>
					</div>
					<div class="wrap_vote">
						<span>1</span>
						<span>22</span>
					</div>
				</div>	            
			</li>-->
	    </ul>		
		<div>
	        <div id="yncontest_entries_previous_<?php echo $this->tab?>" class="paginator_previous">
	            <?php
	            	echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
	            		'onclick' => '',
	            		'class'   => 'buttonlink icon_previous'
	            	));
	            ?>
	        </div>
	        <div id="yncontest_entries_next_<?php echo $this->tab?>" class="paginator_next">
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
	    <div class="tip">
	        <span>
	            <?php echo $this->translate('There are no entries.'); ?>
	           
	        </span>
	    </div>
	<?php endif; ?>
</div>