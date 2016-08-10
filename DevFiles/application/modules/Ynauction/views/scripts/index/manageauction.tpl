
	<div class="generic_layout_container layout_right">
		<div class="generic_layout_container">

			<?php if( count($this->quickNavigation) > 0 ): ?>
				<div class="quicklinks">
					<?php
		// Render the menu
					echo $this->navigation()
					->menu()
					->setContainer($this->quickNavigation)
					->render();
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

			<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
				<ul class="ynauctions_browse">
					<?php foreach( $this->paginator as $item ): ?>
						<li>
							<div class='ynauctions_browse_photo' style="margin-right: 15px;">
							<a href="<?php echo $item->getHref();?>">
								<img src="<?php echo $item->getPhotoUrl("thumb.profile")?>" style = "width:100px;" /></a>
							</div>
							<div class='ynauctions_browse_options'>   
								<?php if ($item->isEditable() && $item->approved == 0): ?> 
									<?php echo $this->htmlLink(array(
										'action' => 'edit',
										'auction' => $item->getIdentity(),
										'route' => 'ynauction_general',
										'reset' => true,
										), $this->translate('Edit auction'), array(
										'class' => 'buttonlink icon_ynauction_edit',
										)) ?>
									<?php echo $this->htmlLink(array(
										'route' => 'default',
										'module' => 'ynauction',
										'controller' => 'photo',
										'action' => 'upload',
										'auction' => $item->getIdentity(),
										), $this->translate('Add Photos'), array(
										'class' => 'buttonlink icon_ynauction_photo_new'
										)) ?>
									<?php endif; ?>
									<?php if($item->display_home == 0): ?>  
										<?php echo $this->htmlLink(array(
											'action' => 'display',
											'auction' => $item->getIdentity(),
											'route' => 'ynauction_general',
											'reset' => true,
											'session_id' => session_id(),     
											), $this->translate('Publish auction'), array(
											'class' => 'buttonlink icon_ynauction_online',
											)) ?>
										<?php elseif($item->stop == 0 && $item->status == 0 && $item->approved == 1 && $item->start_time <= date('Y-m-d H:i:s')):  ?>
											<?php echo $this->htmlLink(array(
												'action' => 'stop',
												'product_id' => $item->getIdentity(),
												'route' => 'ynauction_general',
												'reset' => true,
												), $this->translate('Stop auction'), array(
												'class' => 'buttonlink smoothbox icon_ynauction_stop',
												)) ?>
											<?php  elseif($item->status == 0 && $item->approved == 1 && $item->start_time <= date('Y-m-d H:i:s')):?> 
												<?php echo $this->htmlLink(array(
													'action' => 'start',
													'product_id' => $item->getIdentity(),
													'route' => 'ynauction_general',
													'reset' => true,
													), $this->translate('Start auction'), array(
													'class' => 'buttonlink smoothbox icon_ynauction_start',
													)) ?>  
												<?php  endif;?>
												<?php if ($item->isDeleteable()): ?> 
													<?php echo $this->htmlLink(array(
														'action' => 'delete',
														'ynauction' => $item->getIdentity(),
														'route' => 'ynauction_general',
														'reset' => true,
														), $this->translate('Delete auction'), array(
														'class' => 'buttonlink smoothbox icon_ynauction_delete',
														)) ?>
													<?php  endif;?>
													<?php if(count($item->getProposals()) > 0):?>
														<?php echo $this->htmlLink(array(
															'action' => 'proposal-seller',
															'auction' => $item->getIdentity(),
															'route' => 'ynauction_proposal',
															'reset' => true,
															), $this->translate('Proposal list'), array(
															'class' => 'buttonlink icon_ynauction_proposal',
															)) ?>
														<?php endif;?> 
													</div>
													<div class='ynauctions_browse_info' style="width: 60%;">
														<p class='ynauctions_browse_info_title'>
															<?php echo $this->htmlLink($item->getHref(), $item->title) ?>
														</p>
														<p class='ynauctions_browse_info_date'>
															<?php echo $this->translate('Posted by');?>
															<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
															<?php echo $this->translate('about');?>
															<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
														</p>
														<p class='ynauctions_browse_info_blurb' style="margin-bottom: 15px;">
															<span> 
																<?php echo $this->translate("Status:");?>
															</span>
															<span>
																<?php 
																if($item->status == 0 && $item->display_home == 0 && $item->approved != -1): ?>
																<?php echo $this->translate("Created") ?>
															<?php elseif($item->approved == -1): ?> 
																<?php echo $this->translate("Denied") ?>
															<?php elseif($item->status == 0 && $item->display_home == 1 && $item->approved == 0): ?> 
																<?php echo $this->translate("Pending") ?>
															<?php elseif($item->status == 0 && $item->display_home == 1 && $item->start_time > date('Y-m-d H:i:s')): ?> 
																<?php echo $this->translate("Upcoming") ?>
															<?php elseif($item->status == 0 && $item->display_home == 1): ?>
																<?php echo $this->translate("Running") ?>
															<?php elseif($item->status == 1): ?>
																<?php echo $this->translate("Won") ?> 
															<?php elseif($item->status == 2): ?>
																<?php echo $this->translate("Paid") ?>
															<?php elseif($item->status == 3): ?>
																<?php echo $this->translate("Ended") ?>
															<?php endif; ?>
														</span> 
														<br />
														<span> 
															<?php echo $this->translate("Start Time:");?>
														</span>
														<span>
															<?php echo $this->locale()->toDateTime($item->start_time)?>
														</span>
														<br />
														<span> 
															<?php echo $this->translate("End Time:");?>
														</span>
														<span>
															<?php echo $this->locale()->toDateTime($item->end_time)?>
														</span>  
														<br />  
														<?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
														<?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
				<?php // Not mbstring compat
				echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>349) echo "...";
				?>
			</p>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php elseif($this->search): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('You do not have any auctions that match your search criteria.');?>
		</span>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('You do not have any auctions.');?>
			<?php if( $this->canCreate ): ?>
				<?php echo $this->translate('Get started by %1$spost%2$s a new auction.', '<a href="'.$this->url(array('action' => 'create'), 'ynauction_general').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/auctionpagination.tpl","ynauction"), array("orderby"=>$this->orderby)); ?>

