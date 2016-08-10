<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>
<div class="headline">
    <h2>
        <?php echo $this->translate('File Sharing'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<h3><?php echo $this->translate('Edit Folder Permission')?></h3>

<div class="ynfs_tags ynfs_block">
	<span class="ynfs_text_header">
		<?php echo $this->translate('Tags') ?>
	</span>
	<?php
		if (count($this->folderTags)) {
			$tags = array();
			foreach ($this->folderTags as $tag) {
				$t = $tag->getTag();
				$text = $t->text;
				if (!empty($text)) {
					$href = $this->url(array('action' => 'index'), 'ynfilesharing_general', true) . '?tag=' . $t->tag_id . '&type=folder';
					$html = "<a href='$href'>" . $text . "</a>";
					array_push($tags, $html);
				}
			}
			echo $this->fluentList($tags);
		} 
	?>
</div>

<div class="ynfs_owner ynfs_block">
	<span class="ynfs_text_header">
		<?php echo $this->translate('Owner') ?>
	</span>
	<?php
		echo $this->folder->getParent(); 
	?>
</div>

<div class="ynfs_folder_navigation ynfs_block">
	<ul>
		<li>
			<a href="<?php echo $this->url(
				array('action' => 'manage', 'parent_type' => $this->parentType, 'parent_id' => $this->parentId), 
				'ynfilesharing_general', 
				true)?>">
				<?php echo $this->translate('Home')?>
			</a>
		</li>
		<?php foreach ($this->folder->getParentFolders() as $folder) : ?>
			<li>
				<img class="ynfs_next_arrow" src="<?php echo $this->baseUrl()?>/application/modules/Ynfilesharing/externals/images/next.png" />
				<a href="<?php echo $folder->getHref(array('parent_id' => $this->parentId, 'parent_type' => $this->parentType))?>">
					<?php echo $folder->title?>
				</a>
			</li>
		<?php endforeach;?>
		<li>
			<img class="ynfs_next_arrow" src="<?php echo $this->baseUrl()?>/application/modules/Ynfilesharing/externals/images/next.png" />
			<a href="<?php echo $this->folder->getHref(array('parent_id' => $this->parentId, 'parent_type' => $this->parentType))?>">
				<?php echo $this->folder->title?>
			</a>
		</li>
	</ul>
</div>

<?php echo $this->form->render($this); ?>