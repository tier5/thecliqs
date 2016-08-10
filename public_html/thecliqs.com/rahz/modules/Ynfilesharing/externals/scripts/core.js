/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

en4.ynfilesharing = {
	foldersPermissions : [],
	parentType : null,
	parentId : null,

	_getURI : function(action) {
		var uri = null;
		url = '';
		if (action == 'delete') {
			url = en4.core.baseUrl + 'filesharing/index/delete/parent_type/{parent_type}/parent_id/{parent_id}';
		} else if (action == 'move') {
			url = en4.core.baseUrl + 'filesharing/index/move/parent_type/{parent_type}/parent_id/{parent_id}';
		}
		if (url.length > 0) {
			url = url.substitute({
				'parent_type' : this.parentType,
				'parent_id' : this.parentId
			});
			uri = new URI(url);
		}
		
		return uri;
	},
	
	_handleDeleteAndMove : function(event, action) {
		event.stop();
		var listFilesAndFolders = [];
		Array.each($$('.ynfs_browse li.ynfs_item input[type="checkbox"]'), function(item, index) {
			if (item.checked) {
				listFilesAndFolders.push(item);
			}
		});
		
		if (listFilesAndFolders.length > 1) 
		{
			var folderIds = [];
			var fileIds = [];
			
			for (var i = 0; i < listFilesAndFolders.length; i++) {
				var item = listFilesAndFolders[i];
				if (item.get('name') == 'folderIds[]') {
					var ck = true;
					var folderId = item.get('value');	
					var folderPermissions = en4.ynfilesharing.getPermissionOfFolder(folderId);
					if (action == 'move') {
						var parent = en4.ynfilesharing.getParentItem(folderId, 'folder');
						if (!(parent.parentType == this.parentType && parent.parentId == this.parentId)) {
							ck = false;
						}
					} else {
						if (!en4.ynfilesharing.getPermissionOfFolder(folderId).contains(action)) {
							ck = false;
						}
					}
					if (ck == false) {
						alert(en4.core.language.translate('You do not permission to %s the folder %s', 
							action, en4.ynfilesharing.getItemName(folderId, 'folder')));
						return;
					}
					
					folderIds.push(folderId);
				} else {
					var fileId = item.get('value');
					if (action == 'move') {
						var parent = en4.ynfilesharing.getParentItem(fileId, 'file');
						if (!(parent.parentType == this.parentType && parent.parentId == this.parentId)) {
							alert(en4.core.language.translate('You do not permission to %s the file %s', 
									action, en4.ynfilesharing.getItemName(fileId, 'file')));
							return;
						}
					}
					fileIds.push(fileId);
				}
			}
			
			var uri = this._getURI(action);
			if (folderIds.length > 0) {
				uri.setData('folderIds', folderIds);
			}
			if (fileIds.length > 0) {
				uri.setData('fileIds', fileIds);
			}
			Smoothbox.open(uri.toString());
		} 
		else 
		{			
			Smoothbox.open(event.target);
		}
	},
	
	_deleteFoldersAndFiles : function(event) {
		en4.ynfilesharing._handleDeleteAndMove(event, 'delete');
	},
	
	_moveFoldersAndFiles : function(event) {
		en4.ynfilesharing._handleDeleteAndMove(event, 'move');
	},
	
	setOptions : function(options) {
		for (opt in options) {
			if (this.hasOwnProperty(opt)) {
				this[opt] = options[opt];
			}
		}
	},

	setFoldersPermissions : function(folderPerms) {
		en4.ynfilesharing.foldersPermissions = folderPerms;
	},

	getPermissionOfFolder : function(folderId) {
		return this.foldersPermissions[folderId];
	},

	getItemName : function(itemId, type) {
		var name = '';
		var ele;
		var items = $$('.ynfs_browse.ynfs_control > ul > li.ynfs_item');
		for (var i = 0; i < items.length; i++) {
			if (type == 'folder') {
				if (items[i].getProperty('folderId') == itemId) {
					ele = items[i];
				}
			} else {
				if (type == 'file') {
					if (items[i].getProperty('fileId') == itemId) {
						ele = items[i];
					}
				}
			}
		}
		
		var nameEle = ele.getFirst('.ynfs_name_column  > a');
		if (nameEle) {
			return nameEle.get('text').trim();
		}
	},
	
	getParentItem : function(itemId, type) {
		var items = $$('.ynfs_browse.ynfs_control > ul > li.ynfs_item');
		var ele = null;
		for (var i = 0; i < items.length; i++) {
			if (type == 'folder') {
				if (items[i].getProperty('folderId') == itemId) {
					ele = items[i];
					break;
				}
			} else {
				if (items[i].getProperty('fileId') == itemId) {
					ele = items[i];
					break;
				}
			}
		}
		if (ele) {
			return {
				'parentType' : ele.getProperty('parentType'),
				'parentId' : ele.getProperty('parentId')
			};
		}
	},
	
	showControlForFolder : function(folderId, controlColumn, parentType, parentId) {
		var permissions = en4.ynfilesharing.getPermissionOfFolder(folderId);
		var editElement = null;
		var deleteElement = null;
		var moveElement = null;
		var shareElement = null;
		
		if (permissions == null || permissions.contains('edit')) {
			editElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/folder/edit/' + folderId,
				'html' : en4.core.language.translate('Edit'),
				'class' : 'buttonlink ynfs_control_edit'
			});
			if (parentType == this.parentType && parentId == this.parentId) {
				var moveUrl = 'filesharing/index/move/folder_id/{folder_id}/parent_type/{parent_type}/parent_id/{parent_id}/format/smoothbox';
				moveElement = new Element('a', {
					'href' : en4.core.baseUrl + moveUrl.substitute({
						'parent_type' : this.parentType,
						'parent_id' : this.parentId,
						'folder_id' : folderId
					}),
					'html' : en4.core.language.translate('Move'),
					'class' : 'buttonlink ynfs_control_move'
				});
				moveElement.addEvent('click', this._moveFoldersAndFiles);
			}
		}

		if (permissions == null || permissions.contains('delete')) {
			deleteElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/folder/delete/' + folderId,
				'html' : en4.core.language.translate('Delete'),
				'class' : 'buttonlink ynfs_control_delete'
			});

			deleteElement.addEvent('click', this._deleteFoldersAndFiles);
		}

		if (parentType == this.parentType && parentId == this.parentId) {
			shareElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/index/share/folder_id/' + folderId + '/format/smoothbox',
				'html' : en4.core.language.translate('Share'),
				'class' : 'buttonlink ynfs_control_share'
			});
			shareElement.addEvent('click', function(event){
				event.stop();
				Smoothbox.open(shareElement);
			});
		}
		
		
		if (shareElement != null) {
			shareElement.inject(controlColumn);
		}
		
		if (editElement != null) {
			editElement.inject(controlColumn);
		}
		if (deleteElement != null) {
			deleteElement.inject(controlColumn);
		}
		if (moveElement != null) {
			moveElement.inject(controlColumn);
		}
		
		fileSizeElement =  $("ynfilesharing_filesize");
		if (fileSizeElement != null)
			fileSizeElement.destroy();
	},

	showControlForFile : function(currentFolderId, fileId, controlColumn, parentType, parentId) {
		var permissions = en4.ynfilesharing.getPermissionOfFolder(currentFolderId);
		var editElement = null;
		var deleteElement = null;
		var moveElement = null;
//		var downloadElement = null;
		var fileSizeElement = null;
		var shareElement = null;
		
		if (permissions != null && permissions.contains('edit')) {
			editElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/file/edit/' + fileId,
				'html' : en4.core.language.translate('Edit'),
				'class' : 'buttonlink ynfs_control_edit'
			});

			editElement.addEvent('click', function(event){
				event.stop();
				Smoothbox.open(editElement);
			});

			if (parentType == this.parentType && parentId == this.parentId) {
				var moveUrl = 'filesharing/index/move/file_id/{file_id}/parent_type/{parent_type}/parent_id/{parent_id}';
				moveElement = new Element('a', {
					'href' : en4.core.baseUrl + moveUrl.substitute({
						'parent_type' : this.parentType,
						'parent_id' : this.parentId,
						'file_id' : fileId
					}),
					'html' : en4.core.language.translate('Move'),
					'class' : 'buttonlink ynfs_control_move'
				});
			}
			if (moveElement) {
				moveElement.addEvent('click', this._moveFoldersAndFiles);
			}
		}

		if (permissions != null && permissions.contains('delete')) {
			deleteElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/file/delete/' + fileId,
				'html' : en4.core.language.translate('Delete'),
				'class' : 'buttonlink ynfs_control_delete'
			});

			deleteElement.addEvent('click', en4.ynfilesharing._deleteFoldersAndFiles);
		}

//		downloadElement = new Element('a', {
//			'href' : en4.core.baseUrl + 'filesharing/file/download/' + fileId,
//			'html' : en4.core.language.translate('Download'),
//			'class' : 'buttonlink ynfs_control_download'
//		});
		
		if (parentType == this.parentType && parentId == this.parentId) {
			shareElement = new Element('a', {
				'href' : en4.core.baseUrl + 'filesharing/index/share/file_id/' + fileId + '/format/smoothbox',
				'html' : en4.core.language.translate('Share'),
				'class' : 'buttonlink ynfs_control_share'
			});
			shareElement.addEvent('click', function(event){
				event.stop();
				Smoothbox.open(shareElement);
			});
		}

		fileSizeElement =  $("ynfilesharing_filesize");
		if (fileSizeElement != null)
			fileSizeElement.destroy();
		fileSizeElement = document.createElement('span');
		fileSizeElement.setAttribute('id', 'ynfilesharing_filesize');
		//fileSizeElement.setAttribute('html', en4.ynfilesharing.formatFileSize($$("li[fileid="+fileId+"]").get('size')));
		fileSizeElement.innerHTML = en4.ynfilesharing.formatFileSize($$("li[fileid="+fileId+"]").get('size'));

		if (shareElement != null) {
			shareElement.inject(controlColumn);
		}
		if (editElement != null) {
			editElement.inject(controlColumn);
		}
		if (deleteElement != null) {
			deleteElement.inject(controlColumn);
		}
		if (moveElement != null) {
			moveElement.inject(controlColumn);
		}
//		if (downloadElement != null) {
//			var nSelectedItem = $$('.ynfs_browse .ynfs_item.ynfs_item_selected').length;
//			if (nSelectedItem == 1) {
//				downloadElement.inject(controlColumn);
//			}
//		}
		if (fileSizeElement != null) {
			$(fileSizeElement).inject(controlColumn);
		}
	},

	roundNumber : function (number, digits){
		var multiple = Math.pow(10, digits);
        var rndedNum = Math.round(number * multiple) / multiple;
        return rndedNum;
	},

	formatFileSize : function(size){
		fileSize = parseInt(size);
		sizeKB = 1024; sizeMB = 1024*1024;
		if (fileSize > sizeKB && fileSize < sizeMB)
			return en4.ynfilesharing.roundNumber(fileSize/sizeKB,2) + en4.core.language.translate(" KB");
		else if (fileSize > sizeMB)
			return en4.ynfilesharing.roundNumber(fileSize/sizeMB,2) + en4.core.language.translate(" MB");
		else
			return String(fileSize) + en4.core.language.translate(" Bytes");
	},
	
	cancelUploadFile : function(){
		if (upload.inputFiles){
			upload.inputFiles._files.length = 0;
			$$(".ynfs_uploadList").set('html','');
			$("file").value
			$('uploadForm').hide();
		}
		else
		{
			window.location = window.location;
		}
	},
	
	showControls : function(element) {
		element.addClass('ynfs_item_selected').removeClass('ynfs_item_hover');
		
		var controlsElement = element.getSiblings('.ynfs_browse_control')[0];

		controlsElement.getElements('a').destroy();
		controlsElement.set('styles', {
			'display' : 'block'
		});
		var titleElement = element.getSiblings('.ynfs_browse_title')[0];
		titleElement.set('styles', {
			'display' : 'none'
		});
		var controlColumn = controlsElement.getElement('.ynfs_control_column');
		var folderId = element.getProperty('folderId');

		if (folderId)
		{
			var parentType = element.getProperty('parentType');
			var parentId = element.getProperty('parentId');
			en4.ynfilesharing.showControlForFolder(folderId, controlColumn, parentType, parentId);
		}
		else
		{
			currentFolderId = element.getProperty('currentFolerId');
			fileId = element.getProperty('fileId');
			var parentType = element.getProperty('parentType');
			var parentId = element.getProperty('parentId');
			en4.ynfilesharing.showControlForFile(currentFolderId, fileId, controlColumn, parentType, parentId);
		}
	},
	
	showHeader : function() {
		$(document.body).getElements('li.ynfs_browse_title').set('styles', {
			 'display' : 'block'
		});
		 
		$(document.body).getElements('li.ynfs_browse_control').set('styles', {
			'display' : 'none'
		});
		
		$$('.ynfs_browse.ynfs_control > ul > li.ynfs_item_selected').removeClass('ynfs_item_selected');
	}
}

en4.core.runonce.add(function(){
	$(document.body).addEvent('click',function(e) {
		var parent = e.target.getParent();
		if (parent && parent.contains($('ynfs_control_browse'))) {
			en4.ynfilesharing.showHeader();
		}

	});
	
	$$('.ynfs_checkall').addEvent('click', function(e) {
		var checked = e.target.checked;
		Array.each($$('.ynfs_browse li input[type="checkbox"]'), function(item, index) {
			item.checked = checked;
				var eleLi = item.getParent('li.ynfs_item');
				if (eleLi) {
					if (checked) {
						eleLi.addClass('ynfs_item_selected');
					} else {
						eleLi.removeClass('ynfs_item_selected');
					}
				}
		});
		if (checked) {
			var selectedItems = $$('.ynfs_browse .ynfs_item.ynfs_item_selected');
			if (selectedItems.length > 0) {
				en4.ynfilesharing.showControls(selectedItems[0]);
			}
		} else {
			en4.ynfilesharing.showHeader();
		}
	});
	
	$$('.ynfs_browse li.ynfs_item input[type="checkbox"]').removeEvent('click').addEvent('click', function(e) {
		var checked = e.target.checked;
		if (!checked) {
			Array.each($$('.ynfs_checkall'), function(item, index) {
				item.checked = false;
			});
		} else {
			var checkAll = true;
			var ckboxes = $$('.ynfs_browse li.ynfs_item input[type="checkbox"]');
			for (var i = 0; i < ckboxes.length; i++) {
				if (!ckboxes[i].checked) {
					checkAll = false;					
					break;
				}
			}
			if (checkAll) {
				Array.each($$('.ynfs_checkall'), function(item, index) {
					item.checked = true;
				});
			}
		}
		
		var eleLi = e.target.getParent('li.ynfs_item');
		if (!checked) {
			eleLi.removeClass('ynfs_item_selected');
			var ckboxes = $$('.ynfs_browse li.ynfs_item input[type="checkbox"]');
			var notCheckAll = true;
			for (var i = 0; i < ckboxes.length; i++) {
				if (ckboxes[i].checked) {
					notCheckAll = false;					
					break;
				}
			}
			if (notCheckAll) {
				en4.ynfilesharing.showHeader();
			}
		} else {
			eleLi.addClass('ynfs_item_selected');
			
			en4.ynfilesharing.showControls(eleLi);
		}
	});

	$$('.ynfs_browse.ynfs_control > ul > li.ynfs_item').removeEvent('mouseenter').addEvent('mouseenter', function(event) {
		var element = event.target;
		if (!element.hasClass('ynfs_item_selected')) {
			element.addClass('ynfs_item_hover');
		}
	});

	$$('.ynfs_browse.ynfs_control > ul > li.ynfs_item').removeEvent('mouseleave').addEvent('mouseleave', function(event) {
		var element = event.target;
		element.removeClass('ynfs_item_hover');
	});

	$$('.ynfs_browse.ynfs_control > ul > li.ynfs_item').removeEvent('click').addEvent('click', function(event) {
		var element = event.target;
		if (element.get('tag') != 'input') {
			if (element.get('tag') != 'li') {
				element = element.getParent('li');
			}
			Array.each(element.getSiblings(), function(item, index) {
				if (item.getFirst('.ynfs_check_column').getFirst().checked == false) {
					item.removeClass('ynfs_item_selected');
				}
			});
					
			en4.ynfilesharing.showControls(element);
		}
	});
});