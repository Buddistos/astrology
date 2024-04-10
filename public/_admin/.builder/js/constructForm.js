"use strict";

var ConstructForm = function() {

	this.innerContent = document.createElement('div');
	this.innerContent.innerHTML =  	'<div class="construct-form-sidebar col-md-4  ">'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="text-field-sidebar-btn">'+
		'<i class="icon-pencil-line"></i>'+
		'Text field'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="email-field-sidebar-btn">'+
		'<i class="icon-at-sign"></i>'+
		'Email field'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="select-field-sidebar-btn">'+
		'<i class="icon-menu3"></i>'+
		'Select field'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="textarea-sidebar-btn">'+
		'<i class="icon-document"></i>'+
		'Textarea'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="radio-button-sidebar-btn">'+
		'<i class="icon-radio-button"></i>'+
		'Radio button'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="checkbox-sidebar-btn">'+
		'<i class="icon-check-square"></i>'+
		'Checkbox'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="date-sidebar-btn">'+
		'<i class="icon-calendar-empty"></i>'+
		'Date'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="text-sidebar-btn">'+
		'<i class="icon-text-align-left"></i>'+
		'Text'+
		'</button>'+
		'<button type="button" class="btn-default sidebar-btn draggable" id="file-sidebar-btn">'+
		'<i class="icon-paperclip"></i>'+
		'Attach file'+
		'</button>'+
		'</div>'+
		'<div class="construct-form-main col-md-8 ">'+
		'<div class="construct-form-main-inner droppable"></div>'+
		'</div>';

};

ConstructForm.prototype = {

	/**
	 * Render window of Constructor Form
	 * @returns {Node}
	 */
	getConstructorForm: function(outputData){

		/**
		 * Init ConstructorForm & EventListeners
		 */
		this.initEventListeners();
		this.initDragDrop();

		/**
		 * Work with the transferred object and render default form
		 */
		for(var key in outputData) {
			var obj = outputData[key];

			switch (obj.type) {
				case 'textField':
					this._getTextField(obj);
					break;
				case 'emailField':
					this._getEmailField(obj);
					break;
				case 'selectField':
					this._getSelectField(obj);
					break;
				case 'textarea':
					this._getTextArea(obj);
					break;
				case 'radioButton':
					this._getRadioButton(obj);
					break;
				case 'checkbox':
					this._getCheckbox(obj);
					break;
				case 'dateField':
					this._getDate(obj);
					break;
				case 'text':
					this._getText(obj);
					break;
				case 'file':
					this._getFileField(obj);
					break;
				default:
					console.log('ERROR: object incorrect');
			}
		}

		return this.innerContent;

	},
	/**
	 * Initialise EventListeners when window loading complete
	 */
	initEventListeners: function (){
		var _this = this;
		var buttonTextField = this.innerContent.querySelector('#text-field-sidebar-btn');
		buttonTextField.addEventListener('click', function(){
			_this._getTextField();
		});

		var buttonEmail = this.innerContent.querySelector('#email-field-sidebar-btn');
		buttonEmail.addEventListener('click', function(){
			_this._getEmailField();
		});

		var buttonSelect = this.innerContent.querySelector('#select-field-sidebar-btn');
		buttonSelect.addEventListener('click', function(){
			_this._getSelectField();
		});

		var buttonTextarea = this.innerContent.querySelector('#textarea-sidebar-btn');
		buttonTextarea.addEventListener('click', function(){
			_this._getTextArea();
		});

		var buttonRadio = this.innerContent.querySelector('#radio-button-sidebar-btn');
		buttonRadio.addEventListener('click', function(){
			_this._getRadioButton();
		});

		var buttonCheckbox = this.innerContent.querySelector('#checkbox-sidebar-btn');
		buttonCheckbox.addEventListener('click', function(){
			_this._getCheckbox();
		});

		var buttonDate = this.innerContent.querySelector('#date-sidebar-btn');
		buttonDate.addEventListener('click', function(){
			_this._getDate();
		});

		var buttonFile = this.innerContent.querySelector('#file-sidebar-btn');
		buttonFile.addEventListener('click', function(){
			_this._getFileField();
		});

		var buttonText = this.innerContent.querySelector('#text-sidebar-btn');
		buttonText.addEventListener('click', function(){
			_this._getText();
		});

	},

	/**
	 * Initialise Sidebar Drag'n'Drop
	 */
	initDragDrop: function (){
		var _selfSidebarDrop = this;
		var DragManager = new function() {
			var dragObject = {};
			var self = this;

			function onMouseDown(e) {

				if (e.which != 1) return;

				if(e.target.closest('.draggable')){
					var elem = e.target.closest('.draggable');
					// Save params for clone object
					var objectClone = elem.cloneNode(true);
					objectClone.style.position = 'absolute';
					objectClone.style.width = '200px';
					var shiftX = e.pageX - getCoords(elem).left;
					var shiftY = e.pageY - getCoords(elem).top;
					objectClone.style.border = '1px #aaaaaa dotted';
					objectClone.style.backgroundColor = '#f4f4f4';
					objectClone.style.zIndex = 1000;
					objectClone.style.left = e.pageX - shiftX + 'px';
					objectClone.style.top = e.pageY - shiftY + 'px';
					dragObject.objectClone = objectClone;
					dragObject.dragType = 'sidebar';
					dragObject.elem = objectClone;
				}
				if(e.target.closest('.object-title-bar')){
					var elem = e.target.closest('.object-title-bar').closest('.object-area');
					dragObject.dragType = 'title';
					dragObject.elem = elem;
				}

				if (!elem) return;

				/*
				 * remember that the element is pressed on the current coordinates pageX / pageY
				 */
				dragObject.downX = e.pageX;
				dragObject.downY = e.pageY;

				return false;
			}

			function onMouseMove(e) {

				if (!dragObject.elem){
					return;
				}

				/*
				 * If the transfer is not started
				 */
				if (!dragObject.avatar) {
					var moveX = e.pageX - dragObject.downX;
					var moveY = e.pageY - dragObject.downY;

					if (Math.abs(moveX) < 3 && Math.abs(moveY) < 3) {
						return;
					}

					if(dragObject.dragType == 'sidebar'){
						/*
						 * create clone object
						 */
						document.body.appendChild(dragObject.objectClone);
					}

					/*
					 * transfer start
					 */
					dragObject.avatar = createAvatar(e); // создать аватар
					if(dragObject.dragType == 'sidebar'){
						dragObject.avatar.className += ' destroy-me';
					}

					if (!dragObject.avatar) {
						dragObject = {};
						return;
					}

					/*
					 * avatar created done
					 */
					var coords = getCoords(dragObject.avatar);
					dragObject.shiftX = dragObject.downX - coords.left;
					dragObject.shiftY = dragObject.downY - coords.top;

					startDrag(e);
				}

				/*
				 * render obj with each mouse movement
				 */
				dragObject.avatar.style.left = e.pageX - dragObject.shiftX + 'px';
				dragObject.avatar.style.top = e.pageY - dragObject.shiftY + 'px';

				selectDropArea(e);

				return false;
			}

			/**
			 * when transfer is going
			 */
			function onMouseUp(e) {
				if (dragObject.avatar) {
					finishDrag(e);
				}

				/*
				 * Transfer either did not start, or ended... Clear the "transfer state"
				 */
				dragObject = {};
			}

			function finishDrag(e) {
				var dropElem = findDroppable(e);

				if (!dropElem) {
					self.onDragCancel(dragObject);
				} else {
					self.onDragEnd(dragObject, dropElem);
				}
			}

			function createAvatar(e) {

				var avatar = dragObject.elem;
				var old = {
					parent: avatar.parentNode,
					nextSibling: avatar.nextSibling,
					position: avatar.position || '',
					left: avatar.left || '',
					top: avatar.top || '',
					zIndex: avatar.zIndex || ''
				};

				avatar.rollback = function() {
					old.parent.insertBefore(avatar, old.nextSibling);
					avatar.style.position = old.position;
					avatar.style.left = old.left;
					avatar.style.top = old.top;
					avatar.style.zIndex = old.zIndex
				};

				return avatar;
			}

			function startDrag(e) {

				var avatar = dragObject.avatar;

				/*
				 * init transfer
				 */
				if(dragObject.dragType == 'sidebar'){
					var modalContainer = document.querySelector('#modal-container');
					modalContainer.appendChild(avatar);
					avatar.style.zIndex = 9999;
					avatar.style.position = 'absolute';
					avatar.style.width = '195px';
				}
				if(dragObject.dragType == 'title'){
					var modalContainer = document.querySelector('#modal-container');
					modalContainer.appendChild(avatar);
					avatar.style.width = '348px';
					avatar.style.zIndex = 9999;
					avatar.style.position = 'absolute';
				}


			}

			function selectDropArea(event){
				dragObject.avatar.hidden = true;
				var elem = document.elementFromPoint(event.clientX, event.clientY);
				var topDropBlock = document.querySelector('.top-drop');
				var bottomDropBlock = document.querySelector('.bottom-drop');

				/*
				 * REMOVE drop area the TOP of object
				 */
				if(!elem){
					return;
				}
				if(elem.closest('.object-title-bar') == null && elem.closest('.top-drop') == null){
					var forDestroy = document.querySelector('.top-drop');
					if(forDestroy){
						var prevObj = forDestroy.previousSibling;
						if(prevObj){
							prevObj.style.marginBottom = '10px';
						}
						forDestroy.remove();
					}
				}

				/*
				 * REMOVE drop area a BOTTOM of object
				 */
				if(elem.closest('.toggle-area') == null & elem.closest('.bottom-drop') == null){
					var forDestroy = document.querySelector('.bottom-drop');
					if(forDestroy){
						var prevObj = forDestroy.previousSibling;
						prevObj.style.marginBottom = '10px';
						forDestroy.remove();
					}
				}

				/*
				 * CREATE drop area the TOP of object
				 */
				if(elem.closest('.object-title-bar') != null && !topDropBlock){
					var objectEl = elem.closest('.object-area');
					var parentEl = elem.closest('.object-area').parentNode;
					var blockForDrop = document.createElement("div");
					blockForDrop.className = "block-for-drop top-drop";
					var insertedElement = parentEl.insertBefore(blockForDrop, objectEl);
					var prevObj = insertedElement.previousSibling;
					if(prevObj){
						prevObj.style.marginBottom = '0px';
					}
				}

				/*
				 * CREATE drop area on BOTTOM of object
				 */
				if(elem.closest('.toggle-area') != null && !bottomDropBlock){
					if(elem.closest('.toggle-area').lastChild != null){
						var objectEl = elem.closest('.object-area');
						objectEl.style.marginBottom = '0px';
						var parentEl = elem.closest('.object-area').parentNode;
						var blockForDrop = document.createElement("div");
						blockForDrop.className = "block-for-drop bottom-drop";
						var insertedElement = parentEl.insertBefore(blockForDrop, objectEl.nextSibling);
					}
				}

				dragObject.avatar.hidden = false;
			}

			function findDroppable(event) {
				dragObject.avatar.hidden = true;

				var elem = document.elementFromPoint(event.clientX, event.clientY);

				dragObject.avatar.hidden = false;

				if (elem == null) {
					return null;
				}

				var blockForDrop = document.querySelector('.block-for-drop');
				if(elem.closest('.droppable') != null){
					if(blockForDrop){
						if(blockForDrop.className.match('top-drop')){
							var dropElem = {
								block: blockForDrop.nextSibling,
								type: 'insertBefore'
							};
							return dropElem;
						}
						if(blockForDrop.className.match('bottom-drop')){
							var dropElem = {
								block: blockForDrop.previousSibling,
								type: 'insertAfter'
							};
							return dropElem;
						}
					} else {
						var dropElem = {
							block: elem.closest('.droppable'),
							type: 'append'
						};
						return dropElem;
					}
				}

			}

			document.onmousemove = onMouseMove;
			document.onmouseup = onMouseUp;
			document.onmousedown = onMouseDown;

			this.onDragEnd = function(dragObject, dropElem) {

				if(dragObject.dragType == 'sidebar'){
					var forDestroy = document.querySelector('.block-for-drop');

					if(forDestroy){
						forDestroy.remove();
					}
					if(dragObject.elem.id.match('text-field-sidebar-btn') != null){
						_selfSidebarDrop._getTextField(null, dropElem);
					}
					if(dragObject.elem.id.match('email-field-sidebar-btn') != null){
						_selfSidebarDrop._getEmailField(null, dropElem);
					}
					if(dragObject.elem.id.match('select-field-sidebar-btn') != null){
						_selfSidebarDrop._getSelectField(null, dropElem);
					}
					if(dragObject.elem.id.match('textarea-sidebar-btn') != null){
						_selfSidebarDrop._getTextArea(null, dropElem);
					}
					if(dragObject.elem.id.match('radio-button-sidebar-btn') != null){
						_selfSidebarDrop._getRadioButton(null, dropElem);
					}
					if(dragObject.elem.id.match('checkbox-sidebar-btn') != null){
						_selfSidebarDrop._getCheckbox(null, dropElem);
					}
					if(dragObject.elem.id.match('date-sidebar-btn') != null){
						_selfSidebarDrop._getDate(null, dropElem);
					}
                    if(dragObject.elem.id.match('file-sidebar-btn') != null){
                        _selfSidebarDrop._getFileField(null, dropElem);
                    }
					if(dragObject.elem.id.match('text-sidebar-btn') != null){
						_selfSidebarDrop._getText(null, dropElem);
					}
					// delete avatar
					var forDestroy = document.querySelectorAll('.destroy-me');
					forDestroy[0].remove();
				}

				if(dragObject.dragType == 'title'){
					dragObject.elem.style.border = 'none';
					if(dropElem.type === 'append'){
						dragObject.elem.style.width = '100%';
						dragObject.elem.rollback();
					}
					if(dropElem.type === 'insertBefore'){
						var forDestroy = document.querySelector('.block-for-drop');
						if(forDestroy){
							forDestroy.remove();
						}
						var nextObj = dropElem.block;
						var prev = 	nextObj.previousSibling;
						if(prev){
							nextObj.previousSibling.style.marginBottom = '10px';
						}
						dragObject.elem.style.position = 'inherit';
						dragObject.elem.style.width = '100%';
						var parentBlock =  nextObj.closest('.object-area').parentNode;
						parentBlock.insertBefore(dragObject.elem, nextObj);
					}
					if(dropElem.type === 'insertAfter'){
						var forDestroy = document.querySelector('.block-for-drop');
						if(forDestroy){
							forDestroy.remove();
						}
						if(dropElem.type === 'insertAfter'){
							dragObject.elem.style.position = 'inherit';
							dragObject.elem.style.width = '100%';
							var previousObj = dropElem.block;
							previousObj.style.marginBottom = '10px';
							var parentBlock =  previousObj.closest('.object-area').parentNode;
							parentBlock.insertBefore(dragObject.elem, previousObj.nextSibling);
						}
					}
				}

			};
			this.onDragCancel = function(dragObject) {
				if(dragObject.dragType == 'sidebar'){
					dragObject.elem.style.display = 'none';
					// delete avatar
					var forDestroy = document.querySelectorAll('.destroy-me');
					forDestroy[0].remove();
				}
				if(dragObject.dragType == 'title'){
					dragObject.elem.style.width = '100%';
					dragObject.avatar.rollback();
				}
			};

		};


		function getCoords(elem) {
			var box = elem.getBoundingClientRect();

			return {
				top: box.top + pageYOffset,
				left: box.left + pageXOffset
			};
		}
	},
	/**
	 * Get form elements (_formParser) and building DocumentFragment
	 * @returns {DocumentFragment}
	 */
	getFormElements: function (){

		var parserData = this._formParser();
		if(parserData != undefined){
			var documentFragment = document.createDocumentFragment();
			parserData.forEach(function(e) {
				documentFragment.appendChild(e);
			});

			return documentFragment;
		} else {
			return parserData;
		}
	},
	/**
	 * Document Parser when client click on APPLY button
	 * @returns {Array}
	 * @private
	 */
	_formParser: function (){
		var constructForm = this.innerContent.querySelector('.construct-form-main-inner');
		var objects = constructForm.querySelectorAll('.object-area');

		if(objects.length > 0){
			var outputData = new Array;

			Array.prototype.forEach.call(objects, function(element, i, item) {
				var counter = i;
				/**
				 * if object - Text field
				 */
				if(item[i].className.match('object-textfield-item') != null){
					var placeholder = item[i].querySelector('.textfield-input').value;
					var attrName = item[i].querySelector('.textfield-name').value;
					if(attrName === ''){
						attrName = 'textfield_'+counter;
					}

					var checkbox = item[i].querySelector('.textfield-checkbox').checked;

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

					var innerContent = '<input type="text" class="spr-text-field form-control '+checkbox_class+'" placeholder="'+placeholder+'" name="'+attrName+'" size="10">';
					var div = document.createElement('div');
					div.className = "form-group";
					div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Email field
				 */
				if(item[i].className.match('object-email-item') != null){
					var placeholder = item[i].querySelector('.email-input').value;
					var attrName = item[i].querySelector('.email-name').value;
					if(attrName === ''){
						attrName = 'email_'+counter;
					}
					var checkbox = item[i].querySelector('.email-checkbox').checked;

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

					var innerContent = '<input type="email" class="spr-email-field form-control '+checkbox_class+'" placeholder="'+placeholder+'" name="'+attrName+'" size="10">';
					var div = document.createElement('div');
					div.className = "form-group";
					div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Select field
				 */
				if(item[i].className.match('object-select-item') != null){
					var optionsArray = new Array;
					var selectInputs = item[i].querySelectorAll('.select-input');
					var attrName = item[i].querySelector('.select-name').value;
					var check = item[i].querySelector('.check-field-btn.active');
					if(attrName === ''){
						attrName = 'select_'+counter;
					}
					var checkbox = item[i].querySelector('.select-checkbox').checked;

					Array.prototype.forEach.call(selectInputs, function(element, j, item) {
						if(j == 0){
							var select = check ? '' : ' selected';
							optionsArray[j] = '<option value="'+item[j].value+'"' + select + ' disabled>'+item[j].value+'</option>';
						} else {
							check = element.parentElement.querySelector('.check-field-btn.active');
							var select = check ? ' selected' : '';
							optionsArray[j] = '<option value="'+item[j].value+'"' + select + '>'+item[j].value+'</option>';
						}
					});

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

                    var innerContent = '<select class="spr-select-field form-control '+checkbox_class+'" name="'+attrName+'">'+
                                            optionsArray.join('') +
                                        '</select>';
                    var div = document.createElement('div');
                        div.className = "form-group select-group";
                        div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - TextArea field
				 */
				if(item[i].className.match('object-textarea-item') != null){
					var placeholder = item[i].querySelector('.textarea-input').value;
					var attrName = item[i].querySelector('.textarea-name').value;
					if(attrName === ''){
						attrName = 'textarea_'+counter;
					}
					var rows = item[i].querySelector('.textarea-rows').value;
					var checkbox = item[i].querySelector('.textarea-checkbox').checked;

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

					var innerContent = '<textarea class="spr-textarea form-control '+checkbox_class+'"  placeholder="'+placeholder+'" rows="'+rows+'" name="'+attrName+'"></textarea>';
					var div = document.createElement('div');
					div.className = "form-group";
					div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - RadioButton
				 */
				if(item[i].className.match('object-radio-item') != null){
					var valuesArray = new Array;
					var radioLabel = item[i].querySelector('.radiolabel-input').value;
					var attrName = item[i].querySelector('.radio-name').value;
					if(attrName === ''){
						attrName = 'radio_'+counter;
					}
					var valuesInputs = item[i].querySelectorAll('.radio-input');

					Array.prototype.forEach.call( valuesInputs, function(element, i, item) {
						var buttonChecked = element.parentElement.querySelector('.check-field-btn');

						var checked = buttonChecked.classList.contains('active');
						var checkedAttribute = '';

						if(checked){
							var checkedAttribute = 'checked="true"';
						}

						var j = i+1;
						valuesArray[i] = '<label class="radio-inline">' +
							'<input type="radio" class="spr-radio-button" name="'+attrName+'" id="radio'+j+'" ' +
							'value="option'+j+'" ' + checkedAttribute + '>' +
							'<span class="lbl">' +
							''+item[i].value+''+
							'</span>'+
							'</label>';
					});


					var innerContent = 	'<div class="label-name">'+
						radioLabel+
						'</div>'+
						valuesArray.join('');

					var div = document.createElement('div');
					div.className = 'spr-radio-block text-left radio nomargintop';
					div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Checkbox
				 */
				if(item[i].className.match('object-checkbox-item') != null){
					var label = item[i].querySelector('.checkbox-input').value;
					var attrName = item[i].querySelector('.checkbox-name').value;
					if(attrName === ''){
						attrName = 'checkbox_'+counter;
					}
					var checkbox = item[i].querySelector('.checkbox-checkbox').checked;
					var checkbox_class = '';

					if(checkbox){
						var checkbox_class = 'spr-form-required';
					}

					var checked = item[i].querySelector('.checked-checkbox').checked;
					var checkedAttribute = '';

					if(checked){
						var checkedAttribute = 'checked="true"';
					}

                    var innerContent = '<input type="checkbox" class="spr-checkbox check '+checkbox_class+'" ' +
	                    'placeholder="'+label+'" name="'+attrName+'"' + checkedAttribute + '>' +
                                       '<span class="lbl lbl-style">'+label+'</span>';
                    var div = document.createElement('label');
                        div.className = "form-group checkbox text-left";
                        div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Date
				 */
				if(item[i].className.match('object-date-item') != null){
					var placeholder = item[i].querySelector('.date-input').value;
					var attrName = item[i].querySelector('.date-name').value;
					if(attrName === ''){
						attrName = 'date_'+counter;
					}
					var checkbox = item[i].querySelector('.date-checkbox').checked;

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

                    var innerContent = '<input type="text" class="spr-date-field form-control datepicker-input '+checkbox_class+'" placeholder="'+placeholder+'" name="'+attrName+'" size="10">';
                    var div = document.createElement('div');
                        div.className = "form-group datepicker-group";
                        div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Text
				 */
				if(item[i].className.match('object-text-item') != null){
					var text = item[i].querySelector('.text-div').innerHTML;

					var innerContent = text;
					var div = document.createElement('p');
					if (item[i].dataset.data) {
						div.className = item[i].dataset.data;
					} else {
						div.className = "text txt-form spr-option-no";
					}
					div.innerHTML = innerContent;

					outputData[i]= div;
				}

				/**
				 * if object - Date
				 */
				if(item[i].className.match('object-file-item') != null){
					var placeholder = item[i].querySelector('.file-input').value;
					var attrName = item[i].querySelector('.file-name').value;
					if(attrName === ''){
						attrName = 'file_'+counter;
					}
					var checkbox = item[i].querySelector('.file-checkbox').checked;

					if(checkbox == true){
						var checkbox_class = 'spr-form-required';
					} else {
						var checkbox_class = '';
					}

					var innerContent = '<label class="form-control" for="file_' + counter + '"><span class="placeholder">'
						+ placeholder+'</span></label>'
						+ '<input type="file" name="'+attrName+'" id="file_' + counter + '" class="inputfile spr-file-field '
						+ checkbox_class+'" data-multiple-caption="{count} files selected" multiple="">';
					var div = document.createElement('div');
					div.className = "form-group file-group";
					div.innerHTML = innerContent;

					outputData[i]= div;
				}
			});
		}

		return outputData;

	},
	/**
	 * HTML Templates
	 * @param titleLabel
	 * @returns {string}
	 * @private
	 */
	_htmlTitleBar: function (titleLabel){
		return '<div class="col-md-12 object-title-bar">'+
			titleLabel +
			'<button type="button" class="btn btn-sm btn-default delete-object-btn">'+
			'<i class="icon-trash2"></i>'+
			'</button>'+
			'</div>';
	},
	_htmlTextInput: function (textInputLabel, objectName, type, inputData){
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			textInputLabel +
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<input type="'+type+'" class="form-control field-input '+objectName+'-input" name="'+objectName+'_input" value="'+inputData+'">'+
			'</div>'+
			'</div>';
	},
	_htmlTextInputWithChecked: function (textInputLabel, objectName, type, inputData){
		var check = '';
		if (inputData.checked === 'true') check = ' active';
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			textInputLabel +
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<div class="input-group input-group-border">'+
			'<input type="'+type+'" class="form-control field-input '+objectName+'-input" name="'+objectName+'_input" ' +
			'value="'+inputData.label+'">'+
			'<span class="input-group-btn align-up">'+
			'<button type="button" class="check-field-btn btn btn-default' + check + '" title="checked">'+
			'<i class="icon-check"></i>'+
			'</button>'+
			'</span>'+
			'</div>'+
			'</div>'+
			'</div>';
	},
	_htmlNameInput: function (textInputLabel, objectName, type, inputData){
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			textInputLabel +
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<input type="'+type+'" class="form-control field-input name-attr '+objectName+'-name" name="'+objectName+'_input" value="'+inputData+'">'+
			'</div>'+
			'</div>';
	},
	_htmlTextValue: function (objectName, options){
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			'Value'+
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<div class="input-group input-group-border">'+
			'<input type="text" class="form-control field-input '+objectName+'-input" required="required" name="'+objectName+'_input" value="'+options+'">'+
			'<span class="input-group-btn align-up">'+
			'<button type="button" class="del-field-btn btn btn-default" title="Delete">'+
			'<i class="icon-cross"></i>'+
			'</button>'+
			'</span>'+
			'</div>'+
			'</div>'+
			'</div>';
	},
	_htmlTextValueWithChecked: function (objectName, options){
		var check = '';
		if (options.checked === 'true') check = ' active';
		var innerHTML = '<div class="field-label col-sm-4 control-label">' +
			'Value'+
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<div class="input-group input-group-border">'+
			'<input type="text" class="form-control field-input '+objectName+'-input" required="required" name="'+objectName+'_input" value="'+options.label+'">'+
			'<span class="input-group-btn align-up">'+
			'<button type="button" class="del-field-btn btn btn-default" title="Delete">'+
			'<i class="icon-cross"></i>'+
			'</button>'+
			'<button type="button" class="check-field-btn btn btn-default' + check + '" title="Check">'+
			'<i class="icon-check"></i>'+
			'</button>'+
			'</span>'+
			'</div>'+
			'</div>';
		var div = document.createElement('div');
		div.className = "form-horizontal field-container row";
		div.innerHTML = innerHTML;

		return div;
	},
	_htmlAddButton: function (objectName){
		return '<div class="field-container button-row">'+
			'<div class="col-sm-12 text-right add-btn-panel">'+
			'<button type="button" class="btn btn-sm add-btn btn-default '+objectName+'-add-value-btn" value="'+objectName+'">'+
			'+ Add value'+
			'</button>'+
			'</div>'+
			'</div>';
	},
	_htmlAddButtonNew: function (objectName){
		return '<div class="field-container button-row">'+
			'<div class="col-sm-12 text-right add-btn-panel">'+
			'<button type="button" class="btn btn-sm adding-btn btn-default '+objectName+'-add-value-btn" value="'+objectName+'">'+
			'+ Add value'+
			'</button>'+
			'</div>'+
			'</div>';
	},
	_htmlRowsInput: function (objectName, inputRowsData){
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			'Rows' +
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<input type="number" class="field-input-number form-control '+objectName+'-rows" min="1" placeholder="4" name="'+objectName+'_rows" value="'+inputRowsData+'">'+
			'</div>'+
			'</div>';
	},
	_htmlCheckbox: function (label, objectName, checkBoxData){
		if(checkBoxData == 'true'){
			var checked = 'checked';
		} else {
			var checked = '';
		}
		return '<div class="form-horizontal field-container row">'+
			'<div class="field-label col-sm-4 control-label">' +
			label +
			'</div>'+
			'<div class="col-sm-8 input-width">'+
			'<div class="checkbox">'+
			'<input type="checkbox" class="'+objectName+'-checkbox check" name="'+objectName+'_checkbox" data-required="'+checkBoxData+'" '+checked+'>' +
			'<span class="lbl"></span>'+
			'</div>'+
			'</div>'+
			'</div>';
	},
	/**
	 * Rendering Objects on the page
	 * @param innerContent
	 * @param objectName
	 * @private
	 */
	_appendHtml: function (innerContent, objectName, dropPlace, data){
		/* Create Element and Render it */
		var div = document.createElement('div');
		div.className = "object-area object-"+objectName+"-item";
		div.innerHTML = innerContent;

		if (data) {
			div.dataset.data = data;
		}

		if(!dropPlace){
			var parentBlock =  this.innerContent.querySelector(".construct-form-main-inner");
			parentBlock.appendChild(div);
		}
		if(dropPlace){
			if(dropPlace.type === 'append'){
				var parentBlock =  this.innerContent.querySelector(".construct-form-main-inner");
				parentBlock.appendChild(div);
			}
			if(dropPlace.type === 'insertBefore'){
				var nextObj = dropPlace.block;
				var prev = 	nextObj.previousSibling;
				if(prev){
					nextObj.previousSibling.style.marginBottom = '10px';
				}

				var parentBlock =  nextObj.closest('.object-area').parentNode;
				parentBlock.insertBefore(div, nextObj);
			}
			if(dropPlace.type === 'insertAfter'){
				var previousObj = dropPlace.block;
				previousObj.style.marginBottom = '10px';
				var parentBlock =  previousObj.closest('.object-area').parentNode;
				parentBlock.insertBefore(div, previousObj.nextSibling);
			}
		}

		/* Hanging  EventListener on Object*/

		/**
		 * Name attribute validation
		 */
		var nameAttrInput = div.querySelector('.name-attr');

		if(nameAttrInput != null){
			nameAttrInput.onkeypress  = function(event){
				/**
				 * get Char from keyCode
				 */
				function getChar(event) {
					if (event.which == null) {
						if (event.keyCode < 32) return null;
						return String.fromCharCode(event.keyCode);
					}

					if (event.which != 0 && event.charCode != 0) {
						if (event.which < 32) return null;
						return String.fromCharCode(event.which);
					}

					return null;
				}
				/**
				 * validate char
				 */
				if (event.ctrlKey || event.altKey || event.metaKey) return;
				var char = getChar(event);
				if (!char) return;
				var charValidate = char;

				if(!charValidate.match(/^[a-zA-Z0-9_]$/i)){
					return false;
				}
			};

			/**
			 * length validate
			 */
			//nameAttrInput.onkeyup  = function(event) {
			//	if ( this.value.length >= 10 ) {
			//		this.value = this.value.substr(0, 9);
			//	}
			//};
		}

		/**
		 * Object toggle
		 */
		var titleBar = div.querySelector('.object-title-bar');
		var content = titleBar.nextElementSibling;
		titleBar.addEventListener("click", function(){
			content.classList.toggle("hide");
		}, false);

		/**
		 * Delete Form Object
		 * @type {NodeList}
		 */
		var deleteButtons = div.querySelectorAll('.delete-object-btn');
		for (var i = 0; i < deleteButtons.length; i++) {
			var deleteButton = deleteButtons[i];
			deleteButton.onclick = function () {
				var el = this.closest(".object-area");
				el.parentNode.removeChild(el);
			};
		}

		/**
		 * Delete Value field
		 * @type {NodeList}
		 */
		var valueButtons = div.querySelectorAll('.del-field-btn');
		if(valueButtons.length > 0){
			for (var i = 0; i < valueButtons.length; i++) {
				var valueButton = valueButtons[i];
				valueButton.onclick = function () {
					var el = this.closest(".field-container");
					el.parentNode.removeChild(el);
				};
			}
		}

		/**
		 * Check Value field
		 * @type {NodeList}
		 */
		var valueButtonsCheck = div.querySelectorAll('.check-field-btn');
		if (valueButtonsCheck.length > 0) {
			for ( var i = 0; i < valueButtonsCheck.length; i++ ) {
				var valueButtonCheck     = valueButtonsCheck[ i ];
				valueButtonCheck.onclick = function () {
					var active = div.querySelector( '.check-field-btn.active' );

					if (active) {
						active.classList.remove('active');
					}

					if (this !== active) {
						this.classList.add('active');
					}
				};
			}
		}

		/**
		 * Add Value field
		 * @type {NodeList}
		 */
		var addButtons = div.querySelectorAll('.add-btn');

		if(addButtons.length > 0){
			for (var i = 0; i < addButtons.length; i++) {
				var addButton = addButtons[i];
				addButton.onclick = function () {
					var objectName = this.getAttribute('value');
					var el = this.closest(".row").querySelector('.value-html-area');
					var innerHTML = '<div class="field-label col-sm-4 control-label">' +
						'Value'+
						'</div>'+
						'<div class="col-sm-8 input-width">'+
						'<div class="input-group input-group-border">'+
						'<input type="text" class="form-control field-input '+objectName+'-input" required="required" name="'+objectName+'_input">'+
						'<span class="input-group-btn align-up">'+
						'<button type="button" class="del-field-btn btn btn-default" title="Delete">'+
						'<i class="icon-cross"></i>'+
						'</button>'+
						'</span>'+
						'</div>'+
						'</div>';
					var div = document.createElement('div');
					div.className = "form-horizontal field-container row";
					div.innerHTML = innerHTML;
					el.appendChild(div);
					/**
					 * Delete Value field
					 */
					var valueButtons = div.querySelectorAll('.del-field-btn');
					for (var i = 0; i < valueButtons.length; i++) {
						var valueButton = valueButtons[i];
						valueButton.onclick = function () {
							var el = this.closest(".field-container");
							el.parentNode.removeChild(el);
						};
					}
				};
			}
		}

		/**
		 * Adding Value field
		 * @type {NodeList}
		 */
		var addingButtons = div.querySelectorAll('.adding-btn');

		if(addingButtons.length > 0){
			var _this = this;
			for (var i = 0; i < addingButtons.length; i++) {
				var addingButton = addingButtons[i];
				addingButton.onclick = function () {
					var objectName = this.getAttribute('value');
					var el = this.closest(".row").querySelector('.value-html-area');

					var div = _this._htmlTextValueWithChecked(objectName, '' );

					el.appendChild(div);
					/**
					 * Delete Value field
					 */
					var valueButtons = div.querySelectorAll('.del-field-btn');
					for (var i = 0; i < valueButtons.length; i++) {
						var valueButton = valueButtons[i];
						valueButton.onclick = function () {
							var el = this.closest(".field-container");
							el.parentNode.removeChild(el);
						};
					}

					var valueButtonsCheck = div.querySelectorAll('.check-field-btn');
					for (var i = 0; i < valueButtonsCheck.length; i++) {
						var valueButtonCheck = valueButtonsCheck[i];
						valueButtonCheck.onclick = function () {
							var scope = this.closest('.value-html-area');
							var active = scope.querySelector('.check-field-btn.active');

							if (active) {
								active.classList.remove('active');
							}

							if (this !== active) {
								this.classList.add('active');
							}
						};
					}
				};
			}
		}

	},
	/**
	 * Get Object Action (Building form)
	 * @param obj
	 * @private
	 */
	_getTextField: function(obj, dropElem){
		if(obj != undefined & obj != null){
			var inputData = obj.placeholder || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
		} else {
			var inputData = '';
			var nameData = '';
			var checkBoxData = 'false';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Text Field';
		var objectClass = 'textfield';
		var type = 'text';
		var inputLabel = 'Placeholder';

		var innerContent =  '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData)+
			this._htmlNameInput('Field name', objectClass, type, nameData)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getEmailField: function (obj, dropElem){
		if(obj != undefined & obj != null){
			var inputData = obj.placeholder || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
		} else {
			var inputData = '';
			var nameData = '';
			var checkBoxData = 'false';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Email Field';
		var inputLabel = 'Placeholder';
		var objectClass = 'email';
		var type = 'email';
		var innerContent =      '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData)+
			this._htmlNameInput('Field name', objectClass, type, nameData)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getFileField: function (obj, dropElem){
		if(obj != undefined & obj != null){
			var inputData = obj.placeholder || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
		} else {
			var inputData = '';
			var nameData = '';
			var checkBoxData = 'false';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Attach file';
		var inputLabel = 'Placeholder';
		var objectClass = 'file';
		var type = 'text';
		var innerContent =      '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData)+
			this._htmlNameInput('Field name', objectClass, type, nameData)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getSelectField: function (obj, dropElem){
		if(obj != undefined && obj != null){
			var optionsData = obj.options || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
		} else {
			var optionsData = '';
			var nameData = '';
			var checkBoxData = 'false';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Select Field';
		var inputLabel = 'Default';
		var objectClass = 'select';
		var type = 'text';

		if(optionsData != ''){
			var inputData = obj.options[0].label;
			var htmlInput = this._htmlTextInput(inputLabel, objectClass, type, inputData);
			var htmlName = this._htmlNameInput('Field name', objectClass, type, nameData);
			var htmlValueArray = new Array();
			for (var i = 1; i < optionsData.length; i++) {
				htmlValueArray[i] = this._htmlTextValueWithChecked(objectClass, optionsData[i] ).outerHTML;
			}
			var htmlValue = htmlValueArray.join('');
		} else {
			var inputData = '';
			var htmlInput = this._htmlTextInput(inputLabel, objectClass, type, inputData);
			var htmlName = this._htmlNameInput('Field name', objectClass, type, nameData);
			var options = '';
			var htmlValue = this._htmlTextValueWithChecked(objectClass, options).outerHTML;
		}
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			htmlInput+
			htmlName +
			'<div class="value-html-area">'+
			htmlValue+
			'</div>'+
			this._htmlAddButtonNew(objectClass)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getTextArea: function (obj, dropElem){
		if(obj != undefined & obj != null){
			var inputData = obj.placeholder || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
			var inputRowsData = obj.rows || 4;
		} else {
			var inputData = '';
			var nameData = '';
			var checkBoxData = 'false';
			var inputRowsData = 4;
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Textarea';
		var inputLabel = 'Placeholder';
		var objectClass = 'textarea';
		var type = 'text';
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel)+
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData)+
			this._htmlNameInput('Field name', objectClass, type, nameData)+
			this._htmlRowsInput(objectClass, inputRowsData)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getRadioButton: function (obj, dropElem){
		var optionsData = '';
		var nameData = '';

		if(obj != undefined & obj != null){
			optionsData = obj.buttons || '';
			nameData = obj.name || '';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Radio button';
		var inputLabel = 'Label';
		var objectClass = 'radio';
		var type = 'text';
		if(optionsData != ''){
			var inputData = obj.label;
			var htmlInput = this._htmlTextInput(inputLabel, objectClass+'label', type, inputData);
			var htmlName = this._htmlNameInput('Field name', objectClass, type, nameData);
			var htmlValueArray = new Array();
			for (var i = 0; i < optionsData.length; i++) {
				if(i < 2){
					htmlValueArray[i] = this._htmlTextInputWithChecked('Value', objectClass, type, optionsData[i]);
				} else {
					htmlValueArray[i] = this._htmlTextValueWithChecked(objectClass, optionsData[i] ).outerHTML;
				}
			}
			var htmlValue = htmlValueArray.join('');
		} else {
			var inputData = '';
			var htmlInput = this._htmlTextInput(inputLabel, objectClass+'label', type, inputData);
			var htmlName = this._htmlNameInput('Field name', objectClass, type, nameData);
			var htmlValueArray = new Array();
			for (var i = 1; i < 3; i++) {
				htmlValueArray[i] = this._htmlTextInputWithChecked('Value', objectClass, type, inputData);
			}
			var htmlValue = htmlValueArray.join('');
		}
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel)+
			'<div class="toggle-area col-md-12">' +
			htmlInput+
			htmlName +
			'<div class="value-html-area">'+
			htmlValue+
			'</div>'+
			this._htmlAddButtonNew(objectClass)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getCheckbox: function (obj, dropElem){
		var inputData = '';
		var nameData = '';
		var checkBoxData = 'false';
		var cheked = 'false';

		if(obj != undefined & obj != null){
			inputData = obj.label || '';
			nameData = obj.name || '';
			checkBoxData = obj.required || 'false';
			cheked = obj.checked || 'false';
		}

		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Checkbox';
		var inputLabel = 'Label';
		var objectClass = 'checkbox';
		var type = 'text';
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData) +
			this._htmlNameInput('Field name', objectClass, type, nameData) +
			this._htmlCheckbox('Required', objectClass, checkBoxData) +
			this._htmlCheckbox('Checked', 'checked', cheked) +
			'</div>' +
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getDate: function (obj, dropElem){
		if(obj != undefined & obj != null){
			var inputData = obj.placeholder || '';
			var nameData = obj.name || '';
			var checkBoxData = obj.required || 'false';
		} else {
			var inputData = '';
			var nameData = '';
			var checkBoxData = 'false';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}

		var titleLabel = 'Date';
		var inputLabel = 'Placeholder';
		var objectClass = 'date';
		var type = 'text';
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			this._htmlTextInput(inputLabel, objectClass, type, inputData)+
			this._htmlNameInput('Field name', objectClass, type, nameData)+
			this._htmlCheckbox('Required', objectClass, checkBoxData)+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace);
	},
	_getText: function (obj, dropElem){
		var data;
		if(obj != undefined & obj != null){
			var inputData = obj.text || 'Пункт будет добавлен. Его можно редактировать в конструкторе.';
			data = obj.class;
		} else {
			var inputData = 'Пункт будет добавлен. Его можно редактировать в конструкторе.';
		}
		if(dropElem != undefined){
			var dropPlace = dropElem || '';
		}
		var titleLabel = 'Text';
		var objectClass = 'text';
		var innerContent = '<div class="row">'+
			this._htmlTitleBar(titleLabel) +
			'<div class="toggle-area col-md-12">' +
			'<div class="field-label text-div">'+
				inputData +
			'</div>'+
			'</div>'+
			'</div>';

		this._appendHtml(innerContent, objectClass, dropPlace, data);
	}
};
