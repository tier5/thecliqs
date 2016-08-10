<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
 
?>
<?php
	$this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfilesharing/externals/scripts/mif.tree-v1.2.6.4.js');
?>
<div>
	<h4><?php echo $this->translate('Move To...')?></h4>
</div>

<div id="ynfs_tree"></div>

<form method="post" class="ynfs_form_move" id="ynfs_form_move">
	<input type="hidden" name="dest_folder_id" id="dest_folder_id" />
	<button type="submit">
		<?php echo $this->translate('Move')?>
	</button>
</form>

<script language="javascript" type="text/javascript">
	window.addEvent('domready', function() {
		var tree;
		var folderId = '<?php echo $this->sourceFolderId?>';
		var fileId = '<?php echo $this->fileId?>';
		$('ynfs_form_move').addEvent('submit', function() {
			var destFolderId = $('dest_folder_id').get('value');
			if (Number.from(destFolderId) != null && Number.from(destFolderId) != 0) {
				if (folderId == destFolderId) {
					alert('<?php echo $this->translate('The source folder and the destination folder are the same. Please choose a different destination folder.')?>');
					return false;
				}
			} else {
				alert('<?php echo $this->translate('Please choose a destination folder.')?>');
				return false;
			}
		});
		
		tree = new Mif.Tree({
			container: $('ynfs_tree'),
			initialize: function(){
				this.initSortable();
				new Mif.Tree.KeyNav(this);
				this.addEvent('nodeCreate', function(node){
					node.set({
						property:{
							id:	node.data.id
						}
					});
				});
				var storage = new Mif.Tree.CookieStorage(this);
				this.addEvent('load', function(){
					storage.restore();
				});
			},
			types: {
				folder: {
					openIcon: 'mif-tree-open-icon',
					closeIcon: 'mif-tree-close-icon',
					loadable: true
				},
				file: {
					openIcon: 'mif-tree-file-open-icon',
					closeIcon: 'mif-tree-file-close-icon'
				},
				loader: {
					openIcon: 'mif-tree-loader-open-icon',
					closeIcon: 'mif-tree-loader-close-icon',
					DDnotAllowed: ['inside','after']
				}
			},
			dfltType: 'folder'
		});
		
		tree.load({
			json : <?php echo Zend_Json::encode($this->data)?>
		});

		tree.addEvent('select', function(node) {
			if (node.type.contains('folder')) {
				$('dest_folder_id').set('value', node.data.id);
			} 
		});

		tree.loadOptions = function(node){
			if (node.data.parent_id && node.data.parent_type) {
				data = {
					'parent_type' : node.data.parent_type,
					'parent_id' : node.data.parent_id	
				}
			} else {
				data = {
					'folder_id' : node.data.id
				}
			}
			
			return {
				url: '<?php echo $this->url(array('action' => 'browse', 'folder_only' => 1), 'ynfilesharing_general', true)?>',
				data: data
			};
		};
	});
</script>