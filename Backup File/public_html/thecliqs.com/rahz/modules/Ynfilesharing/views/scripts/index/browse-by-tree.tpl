<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */
 
?>
<div>
	<?php echo $this->translate('Move To...')?>
</div>


<div id="ynfs_tree"></div>

<script language="javascript">
	window.addEvent('domready', function() {
		var tree = new Mif.Tree({
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

		tree.loadOptions = function(node){
			console.log(node);
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
				url: '<?php echo $this->url(array('action' => 'browse'), 'ynfilesharing_general', true)?>',
				data: data
			};
		};
	});
</script>