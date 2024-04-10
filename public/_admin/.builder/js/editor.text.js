/*
 * @autor: MultiFour
 * @version: 1.0.0
 */

"use strict";

var EditorText = function(el) {
    var _this = this;

    this._DOMIdentif = el.tagName.toLowerCase();
    this._DOMEditingEl = el;
    this.wrapContainer = el.parentElement;
    this.overflow = null;
    this.content = el.innerHTML;
    this.href = '';

    this.condition = function() {
        return true;
    };

    var li = controls.findParent(el, ['section-item', 'modal-dialog']);

    var nav = controls.findParent(el, ['nav']);

    if (nav) {
        _this.wrapContainer = controls.findParent(_this.wrapContainer, ['ul']);
        var menu = el;
        while(menu) {
            if (
                menu.classList.contains('sub-menu')
                || menu.classList.contains('mega-menu-container')
            ) {
                menu.classList.add('spr-child-active');
            }
            menu = menu.parentElement;
        }
    }

    if (builder.documentIframe.querySelector('.edit-typography') && !_this._triggerMouseEnter) {
        el.setAttribute('contenteditable', 'true');
        if (builder.currentEditorText && !builder.currentEditorText._triggerMouseEnter) {
            builder.documentIframe.removeEventListener('mousemove', builder.currentEditorText.mouseLeave);
            builder.currentEditorText._DOMEditingEl.removeEventListener('mouseleave', builder.currentEditorText.mouseLeave);
            builder.currentEditorText._DOMEditingEl.removeEventListener('mouseup', builder.currentEditorText.mouseUp);
            //builder.currentEditorText._DOMEditingEl.removeEventListener('touchend', builder.currentEditorText.touchEnd);
            builder.currentEditorText._DOMEditingEl.removeEventListener('keydown', builder.currentEditorText.keyDown);
            builder.currentEditorText._DOMEditingEl.removeEventListener('mouseleave', builder.currentEditorText.mouseLeave);
            builder.currentEditorText._DOMEditingEl.removeEventListener('paste', builder.currentEditorText.paste);
        } else if (builder.currentEditorText && builder.currentEditorText._triggerMouseEnter) {
            builder.currentEditorText.mouseLeave();
        }
        builder.currentEditorText = _this;

        _this.overflow = controls.findParent(_this.wrapContainer, ['nb-off-canvas']);

        //crutch for sarari
        if (el.parentElement.tagName === 'A'){
            _this.href = el.parentElement.getAttribute('href');
            el.parentElement.removeAttribute('href');
        }

        var magnific = el.classList.contains('single-iframe-popup')
            || el.classList.contains('single-image-popup')
            || _this.wrapContainer.classList.contains('single-iframe-popup')
            || _this.wrapContainer.classList.contains('single-image-popup');
        if (magnific) {
            builder.editingText = true;
            builder.windowIframe.builder.editingText = true;
        }

        this.content = el.innerHTML;

        _this._triggerMouseEnter = true;
    }

    this.clickIngalleryEditTagA = function (e) {
        if (builder.documentIframe.querySelector('.edit-typography') && _this._triggerMouseEnter) {
            e.preventDefault();
            e.stopImmediatePropagation();
            el.style.overflow = 'visible';
            this.style.zIndex = '10';
        }
    };

    this.mouseUp = function (e) {
        if (_this._triggerMouseEnter) {
            _this._showActiveButton(this);
        }
    };

    this.keyDown = function(e) {
        _this._eventKeyboard(_this, e, el);
    };

    this.mouseLeave = function(e){
        if (
            document.querySelector('.edit-typography')
            && _this._triggerMouseEnter
            && (e && _this.condition(e) || !e)
        ) {
            if (_this._triggerChangeText) {
                var newContent = el.innerHTML;
                builder.setStep(function () {
                    _this._changeContent(_this, el, _this.content, newContent);
                });
                _this._triggerChangeText = false;
            }

            if (el.hasAttribute('contenteditable')) {
                builder.windowIframe.getSelection().removeAllRanges();
                el.removeAttribute('contenteditable');
            }

            if (el.parentElement.tagName === 'A'){
                el.parentElement.href = _this.href;
            }

            if (_this.wrapContainer
                &&_this.wrapContainer.style.hasOwnProperty('z-index')
                && _this.wrapContainer.style.zIndex === '10'
            ) {
                _this.wrapContainer.style.removeProperty('z-index');
            }

            if (el.style.hasOwnProperty('overflow')
                && el.style.overflow === 'visible'
            ) {
                el.style.removeProperty('overflow');
            }

            _this._triggerMouseEnter = false;
            _this._triggerShowControls = false;
            builder.editingText = false;
            builder.windowIframe.builder.editingText = false;

            builder.removeNowrapSuperStructure(li, '.wrap-control-element.type-typography.nowrap');

            builder.documentIframe.removeEventListener('mousemove', _this.mouseLeave);
            builder.currentEditorText = null;
            el.removeEventListener('mouseleave', _this.mouseLeave);
            el.removeEventListener('mouseup', _this.mouseUp);
            //el.removeEventListener('touchend', _this.touchEnd);
            el.removeEventListener('keydown', _this.keyDown);
            el.removeEventListener('mouseleave', _this.mouseLeave);
            el.removeEventListener('paste', _this.paste);

            if (_this.wrapContainer) {
                _this.wrapContainer.removeEventListener('click', _this.clickIngalleryEditTagA );
            }
// console.log('leave: ', builder.activeEditElement);
            builder.typographyEnter = false;
            builder.activeEditElement = null;
            builder._triggerElementEnter = false;
            // console.log('leave: ', builder.activeEditElement);
        }
    };

    this.paste = function(e) {
        e = e.originalEvent || e;
        e.preventDefault();
        var cnt = e.clipboardData.getData('text/plain');
        cnt = cnt.replace(/[\n\r]/i, '<br>');
        builder.documentIframe.execCommand('insertHTML', false, cnt);
    };

    //if (_this.wrapContainer && ( _this.wrapContainer.tagName === 'A'
    //    || _this.wrapContainer.tagName === 'UL' )
    //){
        _this.wrapContainer.addEventListener('click', _this.clickIngalleryEditTagA );
    //}

    el.addEventListener('mouseup', _this.mouseUp);

    el.addEventListener('keydown', _this.keyDown);

    el.addEventListener('mouseleave', _this.mouseLeave);

    el.addEventListener('paste', _this.paste);
};

EditorText.prototype = {
    _range: null
    , _currentNode: null
    , _startOffset: null
    , _triggerChangeText: false
    , _triggerMouseEnter: false
    , _triggerShowControls: false

    , _showActiveButton: function(element) {
        var _this = this;

        var li = controls.findParent(element, ['section-item', 'modal-dialog']);
        var controlGroup = li.querySelector(
            '.wrap-control-element.nowrap'
            + ', .wrap-control-element-icons.nowrap'
            + ', .wrap-control-element.type-typography.nowrap'
        );

        var select = builder.windowIframe.getSelection();
        this._range = select.getRangeAt(0);
        if (builder.main.querySelector('.edit-typography')
            && element.tagName !== 'I'
            && !navigator.userAgent.match(/iPhone|iPad|iPod|Android|BlackBerry|IEMobile/i)
            && this._range.startOffset !== this._range.endOffset
            && controlGroup
        ) {
            _this._triggerShowControls = true;
            setTimeout(function(){
                _this._triggerShowControls = false;
            }, 1000);
            controlGroup.style.display = 'block';

            var activeButtons = controlGroup.querySelectorAll('.active');

            Array.prototype.forEach.call(activeButtons, function (element) {
                element.classList.remove('active');
            });

            var textAlign = element.className.match(/text-([^ ]*)/i);
            if (textAlign) {
                switch (textAlign[1]) {
                    case 'left':
                        controlGroup.querySelector('button.left').classList.add('active');
                        break;
                    case 'center':
                        controlGroup.querySelector('button.center').classList.add('active');
                        break;
                    case 'right':
                        controlGroup.querySelector('button.right').classList.add('active');
                        break;
                }
            }



            var parentNodeSelect = select.anchorNode.parentNode;
            var style = parentNodeSelect.nodeName.toLowerCase();
            if (select.anchorNode.nodeName.toLowerCase() === this._DOMIdentif
                && select.anchorNode.childNodes.length === 1
            ) {
                this._range.insertNode(select.anchorNode.childNodes[0]);
                this._range.selectNodeContents(select.anchorNode.childNodes[0]);
                try {
                    select.removeAllRanges();
                    select.addRange(this._range);
                }
                catch (e) {}

                parentNodeSelect = select.anchorNode;
                style = parentNodeSelect.nodeName.toLowerCase();
            } else if (
                select.anchorNode.nodeName.toLowerCase() === this._DOMIdentif
                && select.anchorNode.childNodes.length > 1
            ){
                parentNodeSelect = select.anchorNode;
                style = parentNodeSelect.nodeName.toLowerCase();
            }
            var activeStyles = [{style: style, nodeSelect: parentNodeSelect}];
            while (style !== this._DOMIdentif) {
                parentNodeSelect = parentNodeSelect.parentNode;
                style = parentNodeSelect.nodeName.toLowerCase();
                activeStyles.push({style: style, nodeSelect: parentNodeSelect});
            }
            activeStyles.forEach(function (element) {
                switch (element.style) {
                    case 'strong':
                        var bt = controlGroup.querySelector('button.strong');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'em':
                        var bt = controlGroup.querySelector('button.em');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'span':
                        if(element.nodeSelect.classList.contains('text-uppercase'))
                            controlGroup.querySelector('button.upper').classList.add('active');
                        break;
                    case 'mark':
                        var bt = controlGroup.querySelector('button.mark');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'ins':
                        var bt = controlGroup.querySelector('button.ins');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'del':
                        var bt = controlGroup.querySelector('button.del');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'bdo':
                        var bt = controlGroup.querySelector('button.rtl');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                    case 'a':
                        var bt = controlGroup.querySelector('button.link');
                        if (!bt) break;
                        bt.classList.add('active');
                        break;
                }
            });

            if (this._range.cloneContents().hasChildNodes()) {
                var padding = 20;
                var rangeBounding = this._range.getBoundingClientRect();
                var rangeTop = rangeBounding.top;
                var rangeLeft = rangeBounding.left;
                var rangeBottom = rangeBounding.bottom;
                var rangeWidth = rangeBounding.width;

                var controlGroupBounding = controlGroup.getBoundingClientRect();
                var widthControl = controlGroupBounding.width;
                var rightControl = controlGroupBounding.right;
                var topControl = controlGroupBounding.top;
                var leftControl = controlGroupBounding.left;

                var elementBounding = element.getBoundingClientRect();
                var elementTop = elementBounding.top;
                var elementLeft = elementBounding.left;
                var elementRight = elementBounding.right;
                var elementBottom = elementBounding.bottom;

                var liBounding = li.getBoundingClientRect();
                var liTop = liBounding.top;
                var liRight = liBounding.right;
                var liLeft = liBounding.left;

                if (controlGroup.lastChild.classList.contains('arrow')) {
                    controlGroup.removeChild(controlGroup.lastChild);
                }
                var arrow = document.createElement('div');
                arrow.className = 'arrow';

                var toCenterRange = ( rangeLeft + rangeWidth / 2 ) - liLeft;
                var controlGroupToLeft = toCenterRange - widthControl / 2;

                controlGroup.style.top = rangeTop - liTop - 5 + 'px';
                controlGroup.style.left = controlGroupToLeft + 'px';

                //correction horizontal left position
                if ( controlGroupToLeft < liLeft + padding ) {
                    controlGroupToLeft = padding;
                    controlGroup.style.left = controlGroupToLeft + 'px';
                }

                //correction horizontal right position
                if ( ( controlGroupToLeft + widthControl ) >= liRight ) {
                    controlGroupToLeft = liRight - widthControl - 60;
                    controlGroup.style.left = controlGroupToLeft + 'px';


                    controlGroupBounding = controlGroup.getBoundingClientRect();
                    rightControl = controlGroupBounding.right;
                    leftControl = controlGroupBounding.left;
                }

                arrow.style.borderBottom = 'none';
                arrow.style.borderTop = '5px solid rgba(0,0,0,0.9)';
                arrow.style.top = '0px';
                arrow.style.left = toCenterRange - controlGroupToLeft - 5 + 'px';
                arrow.style.margin = '0';
                controlGroup.appendChild(arrow);

                //correction vertical top position
                var controlGroupBBounding = controlGroup.children[0].getBoundingClientRect();
                var topCB = controlGroupBBounding.top;
                if(topCB < liTop) {
                    controlGroup.style.top = rangeBottom - liTop + 42 + 5 + 'px';
                    arrow.style.borderTop = '5px solid transparent';
                    arrow.style.borderBottom = '5px solid rgba(0,0,0,0.9)';
                    arrow.style.top = '-52px';
                }

                //arter correcting position controls of group
                controlGroupBounding = controlGroup.getBoundingClientRect();
                rightControl = controlGroupBounding.right;
                topControl = controlGroup.children[0 ].getBoundingClientRect().top;
                var bottomControl = controlGroup.children[0 ].getBoundingClientRect().bottom;
                leftControl = controlGroupBounding.left;

                controlGroup.style.visibility = 'visible';
                controlGroup.style.opacity = 1;
                controlGroup.classList.add('anim');

                _this.condition = function(e) {

                    if (
                        leftControl < elementLeft && e.clientY > topControl && e.clientY < bottomControl && e.clientX <= leftControl
                        || leftControl < elementLeft && ( e.clientY < topControl || e.clientY > bottomControl ) && e.clientX <= elementLeft
                        || leftControl > elementLeft && e.clientX <= elementLeft
                        || topControl < elementTop && e.clientX > leftControl && e.clientX < rightControl && e.clientY <= topControl
                        || topControl < elementTop && ( e.clientX < leftControl || e.clientX > rightControl ) && e.clientY <= elementTop
                        || topControl > elementTop && e.clientY <= elementTop
                        || rightControl > elementRight && e.clientY > topControl && e.clientY < bottomControl && e.clientX >= rightControl
                        || rightControl > elementRight && ( e.clientY < topControl || e.clientY > bottomControl ) && e.clientX >= elementRight
                        || rightControl < elementRight && e.clientX >= elementRight
                        || bottomControl > elementBottom && e.clientX > leftControl && e.clientX < rightControl && e.clientY >= bottomControl
                        || bottomControl > elementBottom && ( e.clientX < leftControl || e.clientX > rightControl ) && e.clientY >= elementBottom
                        || bottomControl < elementBottom && e.clientY >= elementBottom
                    ) {
                        return true;
                    }
                    return false;
                };

                element.removeEventListener('mouseleave', _this.mouseLeave);
                builder.documentIframe.addEventListener('mousemove', _this.mouseLeave);

                if (_this._triggerChangeText) {
                    var newContent = element.innerHTML;
                    builder.setStep(function () {
                        _this._changeContent(_this, element, _this.content, newContent);
                    });
                    //_this.content = newContent;
                    _this._triggerChangeText = false;
                }
            } else {
                if (controlGroup) {
                    controlGroup.removeAttribute('style');
                    if (controlGroup.classList.contains('anim')) {
                        controlGroup.classList.remove('anim');
                    }
                }

            }
        } else {
            if (controlGroup) {
                controlGroup.removeAttribute('style');
                if (controlGroup.classList.contains('anim')) {
                    controlGroup.classList.remove('anim');
                }
            }
        }
    }
    /**
     * onKeydown
     * @param _this
     * @param e {Event} onKeydown
     * @param element {HTMLElement} editing
     * @private
     */
    , _eventKeyboard: function(_this, e, element) {
        if (!(e.keyCode === 13 && element.tagName === 'SPAN')) {
            _this._triggerChangeText = true;
        }
        if (e.keyCode === 13 && element.tagName === 'SPAN') {
            e.preventDefault();
            e.stopPropagation();
        } else if (e.keyCode === 13) {
            e.preventDefault();
            e.stopPropagation();
            _this._setEnterInEndLine(element);
        } else if (e.keyCode === 65 && (e.ctrlKey || e.metaKey)) {
            if (_this._triggerMouseEnter) {
                e.preventDefault();
                e.stopPropagation();
                var select = builder.windowIframe.getSelection();
                var range = builder.documentIframe.createRange();
                range.selectNodeContents(element);
                select.removeAllRanges();
                select.addRange(range);
                _this._showActiveButton(element);
            }
        }
        if (e.keyCode === 32) {
            e.preventDefault();
            e.stopPropagation();
            builder.documentIframe.execCommand('insertText', false, ' ');
        }
    }
    , _setEnterInEndLine: function(element) {
        var select = builder.windowIframe.getSelection();
        if (select.anchorNode.length === select.anchorOffset
            || select.anchorNode === element) {
            var range = builder.documentIframe.createRange();
            var br = document.createElement('br');
            var br2 = document.createElement('br');
            var fragment = document.createDocumentFragment();
            fragment.appendChild(br2);
            if (select.anchorNode === element) {
                var node = select.anchorNode;
                var next = element.childNodes[select.anchorOffset - 1].nextSibling;
            } else {
                var next = select.anchorNode.nextSibling;
                var node = select.anchorNode.parentNode;
            }

            if ((next && next.tagName !== 'BR') || !next) {
                fragment.appendChild(br);
            }

            if (next) {
                node.insertBefore(fragment, next);
            } else {
                node.appendChild(fragment);
            }
            range.setStartAfter(br2);
            range.setEndAfter(br2);
            range.collapse(false);
            select.removeAllRanges();
            select.addRange(range);
        } else {
            builder.documentIframe.execCommand('insertHTML', false, '<br>');
        }
    }
    /**
     * History point
     * @param _this
     * @param el
     * @param content
     * @param newContent
     * @private
     */
    , _changeContent: function(_this, el, content, newContent) {
        el.innerHTML = content;
        builder.setStep(function () {
            _this._changeContent(_this, el, newContent, content);
        });
    }
    /**
     * Change style selection text
     * @param button
     * @param tagName
     * @param className
     * @param href
     * @param targetLink
     * @private
     */
    , _setNode: function(button, tagName, className, href, targetLink) {
        var _this = this;
        if (this._range) {
            var select = builder.windowIframe.getSelection();
            select.removeAllRanges();
            select.addRange(this._range);
            var valElBefore = _this._DOMEditingEl.innerHTML;
            var insertText = this._range.extractContents();
            Array.prototype.forEach.call(insertText.childNodes, function(n){
                if (n && n.nodeType === 3 && n.data === "") {
                    insertText.removeChild(n);
                }
            });

            var node = document.createElement(tagName);
            if (className === 'rtl') {
                node.setAttribute('dir', 'rtl');
            } else if (className) {
                node.className = className;
            }
            if (href) {
                node.href = href;
                node.target = targetLink;
                node.classList.add('smooth');
                builder.windowIframe.jQuery(node).smoothScroll({speed: 800});
            }
            node.appendChild(insertText);
            this._range.insertNode(node);
            this._range.selectNodeContents(node);
            select.removeAllRanges();
            select.addRange(this._range);

            if (node.parentElement.classList.contains('count-up-data')) {
                node.classList.add('count-up-data');
                node.parentElement.classList.remove('count-up-data');
                if (node.parentElement.className === '') {
                    node.parentElement.removeAttribute('class');
                }
            }

            button.classList.add('active');

            var valElAfter = _this._DOMEditingEl.innerHTML;

            builder.setStep(function() {
                _this._setNodePoint(_this, valElBefore, valElAfter);
            });

            return node;
        }
    }
    , _removeNode: function(button, tagName) {
        var _this = this;
        if (this._range) {
            var fragmentTrigger = false;
            var select = builder.windowIframe.getSelection();
            select.removeAllRanges();
            select.addRange(this._range);
            var valElBefore = _this._DOMEditingEl.innerHTML;
            var anhor = select.anchorNode;
            var node = select.anchorNode.parentNode;
            var nodePrev = anhor;

            if (select.anchorNode.nodeType !== 3) {
                anhor = select.anchorNode.childNodes[0];
                node = select.anchorNode;
                nodePrev = anhor;
            }

            while (node.nodeName.toLowerCase() !== tagName) {
                nodePrev = nodePrev.parentNode;
                node = node.parentNode;
            }

            var nextNode = node.nextSibling;
            var parent = node.parentNode;
            if (node.classList.contains('count-up-data')) {
                parent.classList.add('count-up-data');
                node.classList.remove('count-up-data');
            }
            parent.removeChild(node);

            if (parent.tagName.toLowerCase() !== this._DOMIdentif) {
                if (nextNode) {
                    parent.insertBefore(nodePrev, nextNode);
                } else {
                    parent.appendChild(nodePrev);
                }
                this._range.selectNodeContents(anhor.parentElement);
            } else {
                var baseNode = anhor.parentElement.childNodes;
                if (baseNode.length > 1) {
                    fragmentTrigger = true;
                    var fragment = document.createDocumentFragment();
                    for (var i = 0; i < baseNode.length; i++) {
                        fragment.appendChild(baseNode[i].cloneNode(true));
                    }
                    this._range.insertNode(fragment);
                    this._range.selectNodeContents(fragment);
                } else if (node.childNodes.length > 1) {
                    fragmentTrigger = true;
                    var fragment = document.createDocumentFragment();
                    while(node.childNodes.length > 0) {
                        fragment.appendChild(node.childNodes[0]);
                    }
                    this._range.insertNode(fragment);
                    this._range.selectNodeContents(nodePrev);
                } else {
                    this._range.insertNode(nodePrev);
                    this._range.selectNodeContents(nodePrev);
                }
            }
            try {
                select.removeAllRanges();
                if (!fragmentTrigger) {
                    select.addRange( this._range );
                }
            }
            catch (e) {}

            button.classList.remove('active');
            button.blur();

            var valElAfter = _this._DOMEditingEl.innerHTML;

            builder.setStep(function() {
                _this._setNodePoint(_this, valElBefore, valElAfter);
            });
        }
    }
    /**
     * History poin
     * @param _this
     * @param valElBefore {string}
     * @param valElAfter {string}
     * @private
     */
    , _setNodePoint: function(_this, valElBefore, valElAfter) {
        _this._DOMEditingEl.innerHTML = valElBefore;

        builder.setStep(function() {
            _this._setNodePoint(_this, valElAfter, valElBefore);
        });
    }
    , setTextAlign: function(DOM, button, className) {
        var _this = this;
        var textAlign = DOM.className.match(/text-([^ ]*)/i);
        if (textAlign) {
            DOM.classList.remove(textAlign[0]);
            button.parentElement.querySelector('.' + textAlign[1]).classList.remove('active');
        }
        DOM.classList.add(className);
        button.classList.add('active');

        builder.setStep(function() {
            _this.removeTextAlign(DOM, button, className);
        });
    }
    , removeTextAlign: function(DOM, button, className) {
        var _this = this;
        DOM.classList.remove(className);
        button.classList.remove('active');
        button.blur();

        builder.setStep(function() {
            _this.setTextAlign(DOM, button, className);
        });
    }
    , setBold: function(button) {
        this._setNode(button, 'strong');
    }
    , removeBold: function(button) {
        this._removeNode(button, 'strong');
    }
    , setItalic: function(button) {
        this._setNode(button, 'em');
    }
    , removeItalic: function(button) {
        this._removeNode(button, 'em');
    }
    , setMarker: function(button) {
        this._setNode(button, 'mark');
    }
    , removeMarker: function(button) {
        this._removeNode(button, 'mark');
    }
    , setUnderline: function(button) {
        this._setNode(button, 'ins');
    }
    , removeUnderline: function(button) {
        this._removeNode(button, 'ins');
    }
    , setStrikethrough: function(button) {
        this._setNode(button, 'del');
    }
    , removeStrikethrough: function(button) {
        this._removeNode(button, 'del');
    }
    , setUpper: function(button) {
        this._setNode(button, 'span', 'text-uppercase');
    }
    , removeUpper: function(button) {
        this._removeNode(button, 'span');
    }
    , setRtl: function(button) {
        this._setNode(button, 'bdo', 'rtl');
    }
    , removeRtl: function(button) {
        this._removeNode(button, 'bdo');
    }
    , setLink: function(button, href, targetLink) {
        return this._setNode(button, 'a', null, href, targetLink);
    }
    , changeLink: function(DOMEditingEl, DOMElement, href, targetLink) {
        var _this = this;
        var valElBefore = DOMEditingEl.innerHTML;

        DOMElement.href = href;
        DOMElement.target = targetLink;
        DOMElement.classList.add('smooth');

        builder.windowIframe.jQuery(DOMElement).smoothScroll({speed: 800});

        var valElAfter = DOMEditingEl.innerHTML;

        builder.setStep(function() {
            _this._setNodePoint(_this, valElBefore, valElAfter);
        });

        return DOMElement;
    }
    , removeLink: function(button) {
        this._removeNode(button, 'a');
    }
    , setTextAlignLeft: function(DOM, button) {
        this.setTextAlign(DOM, button, 'text-left');
    }
    , removeTextAlignLeft: function(DOM, button) {
        this.removeTextAlign(DOM, button, 'text-left');
    }
    , setTextAlignCenter: function(DOM, button) {
        this.setTextAlign(DOM, button, 'text-center');
    }
    , removeTextAlignCenter: function(DOM, button) {
        this.removeTextAlign(DOM, button, 'text-center');
    }
    , setTextAlignRight: function(DOM, button) {
        this.setTextAlign(DOM, button, 'text-right');
    }
    , removeTextAlignRight: function(DOM, button) {
        this.removeTextAlign(DOM, button, 'text-right');
    }
};