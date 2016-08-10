/*
---

name: Form.Upload
description: Create a multiple file upload form
license: MIT-style license.
authors: Arian Stolwijk
requires: [Form.MultipleFileInput, Request.File]
provides: Form.Upload

...
*/

(function(){
"use strict";

if (!this.Form) this.Form = {};
var Form = this.Form;

Form.Upload = new Class({

	Implements: [Options, Events],

	options: {
		dropMsg: en4.core.language.translate('Please drop your files here'),
		dropZone: null,
		fireAtOnce: false,
		onComplete: function(){
			// reload
			window.location.href = window.location.href;
		}
	},

	initialize: function(input, options){
		input = this.input = document.id(input);

		this.setOptions(options);

		// Our modern file upload requires FormData to upload
		if ('FormData' in window) {
			this.modernUpload(input);
		}
		else {
			this.legacyUpload(input);
		}
	},

	modernUpload: function(input){

		this.modern = true;

		var form = input.getParent('form');
		if (!form) return;

		var self = this;

//		var drop = new Element('div.ynfs_droppable', {
//			text: this.options.dropMsg
//		}).inject(input, 'after');

		var drop = this.options.dropZone;
		if (drop == null)
		{
			drop = new Element('div.ynfs_droppable', {
				text: this.options.dropMsg
			}).inject(input, 'after');
		}
			
		//var list = new Element('ul.uploadList').inject(drop, 'after');
		var list = new Element('ul.ynfs_uploadList').inject($("ynfs_upload"));
		
		var progress = new Element('div.ynfs_progress')
			.setStyle('display', 'none').inject(list, 'after');

		var actionUrl = form.get('action');
		var uri = new URI(actionUrl);
		uri.setData('format', 'json');
		var uploadReq = new Request.File({
			url: uri.toString(),
			onRequest: progress.setStyles.pass({display: 'block', width: 0}, progress),
			onProgress: function(event){
				var loaded = event.loaded, total = event.total;
				progress.setStyle('width', parseInt(loaded / total * 100, 10).limit(0, 100) + '%');
			},
			onComplete: function(){
				progress.setStyle('width', '100%');
				progress.setStyle('display', 'none');
				self.fireEvent('complete', Array.slice(arguments));
				this.reset();
			}
		});

		var inputname = input.get('name');

		var inputFiles = new Form.MultipleFileInput(input, list, drop, {
			onDragenter: drop.addClass.pass('hover', drop),
			onDragleave: drop.removeClass.pass('hover', drop),
			onDrop: function(){
				drop.removeClass.pass('hover', drop);
				if (self.options.fireAtOnce){
					self.submit(inputFiles, inputname, uploadReq);
				}
			},
			onChange: function(){
				if (self.options.fireAtOnce){
					self.submit(inputFiles, inputname, uploadReq);
				}
			}
		});
		this.inputFiles = inputFiles;	
		form.addEvent('submit', function(event){
			if (event) event.preventDefault();
			self.submit(inputFiles, inputname, uploadReq);
		});

		self.reset = function() {
			var files = inputFiles.getFiles();
			for (var i = 0; i < files.length; i++){
				inputFiles.remove(files[i]);
			}
		};
	},

	submit: function(inputFiles, inputname, uploadReq){
		if (inputFiles.getFiles().length > 0){
			inputFiles.getFiles().each(function(file){
				uploadReq.append(inputname , file);
			});
			uploadReq.send();
		}
		else{
			alert(en4.core.language.translate("No file to upload"));
		}
		
	},

	legacyUpload: function(input){

		var rows = [];

		var row = input.getParent('.formRow');
		var rowClone = row.clone(true, true);
		var add = function(event){
			event.preventDefault();

			var newRow = rowClone.clone(true, true),
				inputID = String.uniqueID(),
				label = newRow.getElement('label');

			newRow.getElement('input').set('id', inputID).grab(new Element('a.ynfs_delInputRow', {
				text: 'x',
				events: {click: function(event){
					event.preventDefault();
					newRow.destroy();
				}}
			}), 'after');
			
			if (label) label.set('for', inputID);
			newRow.inject(row, 'after');
			rows.push(newRow);
		};

		new Element('a.ynfs_addInputRow', {
			text: '+',
			events: {click: add}
		}).inject(input, 'after');

		this.reset = function() {
			for (var i = 0; i < rows.length; i++){
				rows[i].destroy();
			}
			rows = [];
		};

	},

	isModern: function(){
		return !!this.modern;
	}

});

}).call(window);
