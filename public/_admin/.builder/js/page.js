/*
 * @autor: MultiFour
 * @version: 1.0.0
 */

"use strict";

var Page = function(name, id, context, mode, targetPageObj) {
    var _this = this;
    this.sections = {};
    this.html = '';
    this.htmlDOM = null;
    this.preloader = null;
    this.load = true;
    this.id = 0;

    switch (mode) {
        case 'copy':
            _this._copy(targetPageObj, name, id);
            break;
        case 'load':
            _this._load(context, name, id);
            break;
        default :
            _this._createPage(context, name, id);
            break;
    }

    this._title = name;
    this._name = toPageName(name);
    this._className = replaceSpace(name);
    this._metaDes = '';
    this._metaKey = '';
    this._metaJs = '';
};

Page.prototype = {

    _selfDOM: null
    , _name: ''
    , _className: ''
    , _title: ''
    , _defaultStyle: 'light-page loading'
    , _metaDes: ''
    , _metaKey: ''
    , _metaJs: ''
    , _favicon: '/.builder/images/favicons/favicon.png'

    /**
     * Create new Page object which is copy of targetPageObj
     * @param targetPageObj
     * @param name
     * @param id
     * @private
     */
    , _copy: function(targetPageObj, name, id) {
        var _this = this;
        var targetPage = targetPageObj.getDOMSelf();
        var oldPageName = replaceSpace(targetPageObj.getPageName());
        var page = targetPage.cloneNode(true);
        this._selfDOM = page;
        page.classList.remove(oldPageName);
        page.classList.add(name);
        page.dataset.id = id;
        this.id = id;
        page.dataset.name = name;
        if (page.classList.contains('spr-active-page')) {
            page.classList.remove('spr-active-page');
        }

        this.setMetaDes(targetPageObj.getMetaDes());
        this.setMetaKey(targetPageObj.getMetaKey());
        this.setJs(targetPageObj.getJs());
        this.preloader = builder.cloneObject(targetPageObj.preloader);
        var liArray = page.querySelectorAll('.section-item');

        if (page.innerHTML === '' && targetPageObj.htmlDOM) {
            _this.htmlDOM = targetPageObj.htmlDOM.cloneNode(true);
            var buffer = document.createElement('div');
            buffer.appendChild(_this.htmlDOM);
            _this.html = buffer.innerHTML;
            liArray = buffer.querySelectorAll('.section-item');
        } else {
            _this.moveChildrenToHtmlDOM(page);
        }
        
        Array.prototype.forEach.call(liArray, function (li) {
            _this.addSectionToDataPage(li);
        });

        var next = targetPage.nextElementSibling;
        var parent = targetPage.parentElement;
        parent.insertBefore(page, next);
    }
    /**
     * Create new Page object which is loaded from storaje or project.supra
     * @param context
     * @param name
     * @param id
     * @private
     */
    , _load: function(context, name, id) {
        var targetPage = context.querySelector('.' + name);
        var page = targetPage;
        page.dataset.id = id;
        this.id = id;
        this._selfDOM = page;
        var animations = page.querySelectorAll('.aos-animate');
        Array.prototype.forEach.call(animations, function(el) {
            if (el.classList.contains('aos-animate')) {
                el.classList.remove('aos-animate')
            }
        });
    }
    /**
     * Create new Page object
     * @param context
     * @param name
     * @param id
     * @private
     */
    , _createPage: function(context, name, id) {
        var page = document.createElement('ul');
        page.style.minHeight = window.innerHeight + 'px';
        this._selfDOM = page;
        page.className = name + ' ' +  this._defaultStyle;
        page.dataset.id = id;
        this.id = id;
        page.dataset.name = name;
        context.appendChild(page);
        page.innerHTML = '<div class="wrap-drag flex-center">'
                            + '<p><i class="icon-plus-square"></i>Начните создавать страницы, перетаскивая разделы из левой панели</p>'
                        + '</div>';
        if (id > 0) {
            this.moveChildrenToHtmlDOM(page);
            this.load = true;
        }
    }
    /**
     * Add section to page
     * @param sectionsItem {HTMLElement} from right submenu
     * @param className {string}
     * @param defaultStyleType {string}
     * @param li {HTMLElement}
     * @param next {HTMLElement} section after new section
     */
    , addSection: function(sectionsItem, className, defaultStyleType, li, next) {
        var _this = this;
        var triggerCElements = false;
        if (!li) {
            li = _this._createSection(sectionsItem, className, defaultStyleType, next);
            triggerCElements = true;
        } else {
            _this.addSectionToDataPage(li);
        }

        var section = li.children[0];

        //for popup
        if (section.classList.contains('modal')) {
            builder.createPopupThumb(section);
        }

        //if (triggerCElements && section.id.search(/--/) === -1) {
        //    _this.sections[li.dataset.group][section.id].modal = [];
        //    var modals = li.querySelectorAll('.modal');
        //    if (modals) {
        //        Array.prototype.forEach.call(modals, function(modal){
        //            builder.modalContainer.appendChild(modal);
        //            _this.sections[li.dataset.group][section.id].modal.push(modal.id);
        //        });
        //    }
        //} else if (triggerCElements && section.id.search(/--/) !== -1) {
        //    var modals = li.querySelectorAll('.modal');
        //    if (modals) {
        //        Array.prototype.forEach.call(modals, function(modal){
        //            modal.parentElement.removeChild(modal);
        //        });
        //    }
        //}

        if (triggerCElements) {
            //forms
            var form = section.querySelector('form');
            if (form) {
                builder.addNewForm(form, section);
            }

            //maps
            var map = section.querySelector('.g-map');
            if (map) {
                var id = builder.addNewGMap(map, section, li.querySelector('script'));
                builder.changeIdGMapInScript(li.querySelector('script'), id);
            }

            //counter down
            var countDown = section.querySelector('.countdown');
            if (countDown) {
                var id = builder.addNewCountDown(countDown, section, li.querySelector('script'));
                builder.changeIdCountDownInScript(li.querySelector('script'), id);
            }

            var navsTabs = li.querySelectorAll('.nav-tabs');
            if (navsTabs) {
                Array.prototype.forEach.call(navsTabs, function (navTabs, indx) {
                    controls.copySectionWithNavTab(navTabs, indx, li);
                });
            }

            var accordions = li.querySelectorAll('.accordion');
            if (accordions) {
                Array.prototype.forEach.call(accordions, function (accordion, indx) {
                    controls.copySectionWithAccordion(accordion, indx, li);
                });
            }
        }

        var position = window.getComputedStyle(section, null).getPropertyValue("position");
        if ( (position === "fixed" || position === "absolute") && !section.classList.contains('modal') ) {
            builder.setPosition(section, li, position, 1095);
            var heightLi = window.getComputedStyle(section, null).getPropertyValue("height");
            li.style.height = heightLi;
        }

        if (li.classList.contains('nav')) {
            _this._selfDOM.dataset.nav = 1;
        } else if (li.classList.contains('footer')) {
            _this._selfDOM.dataset.footer = 1;
        }

        if (!builder.triggerImport) {
            var currPageObj = _this;
            var activeItem = builder.getActivePageItem(currPageObj.id);
            var parent = li.parentElement;
            var next = li.nextElementSibling;
            builder.setStep( function() {
                var currentPageObj = builder.getActivePageObject();
                if (currPageObj && currPageObj.id !== currentPageObj.id ) {
                    builder.chengeActivePage(currPageObj, activeItem, currPageObj.id)
                }
                if (builder.activeFormModal) {
                    $(builder.activeFormModal).modal('hide');
                    builder.activeFormModal.style.display = 'none';
                }
                _this.deleteSection(li, parent, next);
            });
        }

        builder.sectionClicked = null;
    }
    /**
     * Creating new section. Content will be extracted from sectionsPreview
     * @param sectionsItem
     * @param className
     * @param defaultStyleType
     * @param next
     * @returns {Element}
     * @private
     */
    , _createSection: function(sectionsItem, className, defaultStyleType, next) {
        var _this = this;
        var li = document.createElement('li');
        li.className = 'section-item ' + className;
        li.dataset.group = sectionsItem.dataset.group;
        li.dataset.img = sectionsItem.querySelector('img').src;
        li.innerHTML = sectionsPreview[sectionsItem.dataset.group].sections[sectionsItem.dataset.id].html;

        var section = li.children[0];
        var modalContent = section.querySelector('.modal-content');

        if (defaultStyleType !== ''
            && !section.classList.contains('light')
            && !section.classList.contains('dark')
        ) {
            if (section.classList.contains('modal')
                && modalContent
                && !modalContent.classList.contains('light')
                && !modalContent.classList.contains('dark')
            ) {
                modalContent.classList.add( defaultStyleType );
            } else if (!section.classList.contains('modal')) {
                section.classList.add( defaultStyleType );
            }
        }

        var style = document.createElement('style');
        style.innerHTML = sectionsPreview[sectionsItem.dataset.group].sections[sectionsItem.dataset.id].style;
        li.appendChild(style);

        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        var overAllJs = sectionsPreview[sectionsItem.dataset.group].overallJs;
        if (overAllJs) {
            script.innerHTML = "//delete\n"
                + "setTimeout(function(){"
                + sectionsPreview[sectionsItem.dataset.group].overallJs
                + "\n}, 2);\n"
                + "//deleteend";
        }
        var sectionScript = sectionsPreview[sectionsItem.dataset.group].sections[sectionsItem.dataset.id].script || '';
        var startTimeOut = "//delete\n"
            + "setTimeout(function(){\n"
            + "//deleteend\n";
        var endTimeOut = "\n//delete\n"
            + "}, 2);\n"
            + "//deleteend";
        if (sectionScript.search(/owlCarousel/im) === -1) {
            startTimeOut = endTimeOut = '';
        }
        script.innerHTML += "\n"
            + startTimeOut
            + sectionScript
            + endTimeOut;
        li.appendChild(script);

        //for countdown
        if ( /(countdown\(\s*')[^']*/i.test(script.innerHTML) ) {
            var date = new Date();
            date.setMonth(date.getMonth() + 1);
            var year = date.getFullYear();
            var month = ( date.getMonth() + 1 ).toString().length < 2 ? '0' + ( date.getMonth() + 1 ) : ( date.getMonth() + 1 );
            var day = date.getDate().toString().length < 2 ? '0' + date.getDate() : date.getDate();
            var dateStr = year + '/' + month + '/' + day + ' 23:59:59';
            script.innerHTML = script.innerHTML.replace(/(countdown\(\s*')[^']*/i, '$1' + dateStr);
        }

        var option = options.controlsSection;

        if (section.classList.contains('modal')) {
            option = options.controlsSectionModal;
        }

        li.dataset.minWidth = (option.length - 1) * 42 + 'px';

        _this.addSectionToDataPage(li);

        //counter up
        if (section.classList.contains('counter-up')) {
            builder.changeIdCountUpInScript(script, section.id);
        }

        var wrapControls = controls.getGroupControl(
            option
            , li
            , 'btn-group flex-center wrap-control'
        );
        li.appendChild(wrapControls);

        builder.listenerSectionsMouseDown(null, wrapControls);

        if (className === 'nav' && this._selfDOM.querySelector('li')) {
            this._selfDOM.insertBefore(li, this._selfDOM.querySelector('li'));
        } else if (next) {
            this._selfDOM.insertBefore(li, next);
        } else if (className === '' && this._selfDOM.querySelector('li.footer')) {
            this._selfDOM.insertBefore(li, this._selfDOM.querySelector('li.footer'));
        } else {
            this._selfDOM.appendChild(li);
        }
        
        if (li.querySelector('.parallax-bg')) {
            li.classList.add('parallax');
            var bg = li.querySelector('.bg');
            bg.dataset.topBottom = 'transform:translate3d(0px, 25%, 0px)';
            bg.dataset.bottomTop = 'transform:translate3d(0px, -25%, 0px)';
            if (skr) {
                skr.refresh();
            }
        }

        return li;
    }
    /**
     *
     * @param li {HTMLElement} current section
     * @param parent {HTMLElement} page
     * @param next {HTMLElement} next section
     */
    , deleteSection: function(li, parent, next, mode) {
        var _this = this;
        var section = li.children[0];
        var sectionGroup = li.dataset.group;

        if (li.classList.contains('nav')) {
            _this._selfDOM.dataset.nav = 0;
        } else if (li.classList.contains('footer')) {
            _this._selfDOM.dataset.footer = 0;
        }

        var formConfirms = null;
        var modals = {};
        var form = section.querySelector('form');
        if (form) {
            formConfirms = builder.forms[section.id];
            if (builder.forms[section.id].popupsForForm) {
                for (var popup in builder.forms[section.id].popupsForForm) {
                    var modalId = builder.forms[section.id].popupsForForm[popup];
                    var modal = document.getElementById(modalId);
                    modals[popup] = modal;
                    builder.modalFormContainer.removeChild(modal);
                }
            }

            delete builder.forms[section.id];
        }

        var parent = parent || li.parentElement;
        var next = next || li.nextElementSibling;

        var currPageObj = _this;
        var activeItem = builder.getActivePageItem(currPageObj.id);

        builder.setStep( function() {

            var currentPageObj = builder.getActivePageObject();
            if (currPageObj && currPageObj.id !== currentPageObj.id ) {
                builder.chengeActivePage(currPageObj, activeItem, currPageObj.id);
            }

            builder.removeWrapDrag();
            if (next) {
                parent.insertBefore(li, next);
            } else {
                parent.appendChild(li);
            }
            _this.addSection(null, null, null, li);
            controls.rebuildControl(li);
            builder._refreshParallax(li);
            builder._reloadVideoBg(li, 'run');
            builder.listenerSectionsMouseDown(null, li.lastChild);
            if (formConfirms) {
                builder.forms[section.id] = formConfirms;
                if (Object.keys(modals).length > 0) {
                    for (var popup in modals) {
                        builder.modalFormContainer.appendChild(modals[popup]);
                    }
                }
            }
        });

        //for popup
        if (section.classList.contains('modal')) {
            builder.windowIframe.jQuery(section).modal('hide');
            var item = builder.popupThumb.querySelector('[data-target="#' + section.id + '"]');
            builder.popupThumb.removeChild(item);

            var index = 0;
            builder.popupThumbArray.forEach( function (el, indx) {
                if (el.id === section.id) {
                    index = indx;
                    return index;
                }
            } );

            builder.popupThumbArray.splice( index, 1 );

            if (builder.popupThumbArray.length < 1) {
                builder.collapsePopupThumbEl.classList.remove('show');
            }
        }

        parent.removeChild(li);

        delete this.sections[sectionGroup][section.id];
        if (Object.keys(this.sections[sectionGroup]).length === 0) {
            delete this.sections[sectionGroup];
        }

        if (this._selfDOM.innerHTML.trim() === '' && mode !== 'replace') {
            builder.addWrapDrag();
        }
        iframeLoaded('main');
    }
    /**
     *
     * @param DOM {HTMLElement}
     * @TODO: need to be refactoring this crutches
     */
    , deleteElement: function(DOM, wrap) {
        var _this = this;
        var ElForDel = DOM;
        var delFuncGMap = '';
        var script = null;
        var nav = controls.findParent(DOM, ['nav']);

        var parent = ElForDel.parentElement;
        var next = ElForDel.nextElementSibling;
        
        var nextElement = DOM.nextElementSibling;
        if (DOM.parentElement.className.search(/buttons-control/i) !== -1) {
            ElForDel = DOM.parentElement;
            wrap = DOM.parentElement;
            nextElement = ElForDel.nextElementSibling ? ElForDel.nextElementSibling.children[0] : null;
        } else if (nav && parent.tagName === 'LI') {
            ElForDel = parent;
            wrap = ElForDel;
            nextElement = ElForDel.nextElementSibling;
            parent = parent.parentElement;
        }
        //if (ElForDel.parentElement.className.search(/buttons-control/i) !== -1) ElForDel = ElForDel.parentElement;


        var tabPane = null;
        var tabPaneParent = null;
        var tabPaneNext = null;

        var owlItem = controls.findParent(DOM, ['owl-item']);
        if (owlItem && DOM.classList.contains('item')) {
            var owl = controls.findParent(owlItem, ['spr-gallery']);
            var position = controls._getPositionInGallery(owlItem, owl);
            builder.windowIframe.jQuery(owl).trigger('remove.owl.carousel', position).trigger('refresh.owl.carousel');
        }
        
        if (DOM.classList.contains('g-map')) {
            var li = controls.findParent(DOM, ['section-item']);
            script = li.querySelector('script');
            delFuncGMap = builder.deleteFunctionInitGmap(script, DOM.id);
        }

        if (DOM.children[0] && DOM.children[0].getAttribute("role") === "tab") {
            var li = controls.findParent(DOM, ['section-item']);
            var hash = DOM.children[0].href.match(/#.*/) ? DOM.children[0].href.match(/#.*/)[0] : '';
            if (hash !== '') {
                tabPane = li.querySelector( hash );
                tabPaneParent = tabPane.parentElement;
                tabPaneNext = tabPane.nextSibling;
                tabPane.parentElement.removeChild( tabPane );
            }
        }

        var currPageObj = _this;
        var activeItem = builder.getActivePageItem(currPageObj.id);

        builder.setStep(function() {
            var ElForAppend = DOM;
            if (nav) {
                ElForAppend = ElForDel;
            }

            var mode = document.querySelector('aside.left nav li.active').id;

            if (mode !== 'default-styles') {
                ElForAppend.removeAttribute('style');
                next = nextElement;
            } else {
                ElForAppend = wrap;
                if (wrap && wrap.children.length < 2 && !nav) {
                    wrap.appendChild(DOM);
                    next = next.parentElement;
                } else {
                    ElForAppend = DOM;
                }
            }
            var currentPageObj = builder.getActivePageObject();
            if (currPageObj && currPageObj.id !== currentPageObj.id ) {
                builder.chengeActivePage(currPageObj, activeItem, currPageObj.id)
            }
            if (owlItem && DOM.classList.contains('item')) {
                builder.windowIframe.jQuery(owl).trigger('add.owl.carousel', [$(ElForAppend), position]).trigger('refresh.owl.carousel');
            } else {
                if (delFuncGMap !== '' && script) {
                    script.innerHTML += delFuncGMap;
                }
                if (parent) {
                    if (next) {
                        parent.insertBefore(ElForAppend, next);
                    } else {
                        parent.appendChild(ElForAppend);
                    }
                }

                if (mode === 'default-styles') {
                    controls.rebuildControl(ElForAppend);
                }

            }

            if (tabPane) {
                if (tabPaneParent) {
                    if (tabPaneNext) {
                        tabPaneParent.insertBefore(tabPane, tabPaneNext);
                    } else {
                        tabPaneParent.appendChild(tabPane);
                    }
                }

                var activeS = parent.parentElement.querySelectorAll('.active');
                Array.prototype.forEach.call(activeS, function(el){
                    el.classList.remove('active');
                    if (el.classList.contains('in')) {
                        el.classList.remove('in');
                    }
                });

                ElForAppend.classList.add('active');
                tabPane.classList.add('active');
                tabPane.classList.add('in');
            }


            var currPageObjS = _this;
            var activeItemS = builder.getActivePageItem(currPageObjS.id);

            builder.setStep(function() {
                var currentPageObj = builder.getActivePageObject();
                if (currPageObjS && currPageObjS.id !== currentPageObj.id ) {
                    builder.chengeActivePage(currPageObjS, activeItemS, currPageObjS.id)
                }
                currPageObjS.deleteElement(DOM, wrap);
            });
        });

        ElForDel.parentElement.removeChild(ElForDel);
    }
    /**
     *
     * @param li {HTMLElement}
     */
    , addSectionToDataPage: function(li) {
        var section = li.children[0];
        var group = li.dataset.group;
        var style = li.querySelector('style');
        if (this.sections[group] === undefined) {
            this.sections[group] = {};
        }

        while (this.sections[group][section.id] !== undefined) {
            var args = section.id.split('--');
            if (args[1]) {
                section.id = args[0] + '--' + ((args[1] * 1) + 1);
            } else {
                section.id = args[0] + '--0';
            }

        }
        if (section.id.split('--')[1]) {
            var pattern = new RegExp('#' + section.id.split('--')[0] + '(--[0-9]*)?\\s', 'gim');
            style.innerHTML = style.innerHTML.replace(pattern, '#' + section.id + ' ');
        }

        builder.sectionsName.push(section.id);

        if (this.sections[group][section.id] === undefined) {
            this.sections[group][section.id] = {};
        }
        this.sections[group][section.id].html = li;
    }
    , getDOMSelf: function() {
        return this._selfDOM;
    }
    , getPageName: function() {
        return this._name;
    }
    , setPageName: function(newName, encode) {
        if (!encode)
            newName = htmlencode(newName);
        else newName = newName;
        this._selfDOM.classList.remove(this._className);
        this._name = newName;
        newName = replaceSpace(newName);
        this._className = newName;
        this._selfDOM.classList.add(newName);
        this._selfDOM.dataset.name = newName;
    }
    , getMetaDes: function() {
        return this._metaDes;
    }
    , setMetaDes: function(metaDes, encode) {
        if (!encode)
            this._metaDes = htmlencode(metaDes);
        else this._metaDes = metaDes;
    }
    , getMetaKey: function() {
        return this._metaKey;
    }
    , setMetaKey: function(metaKey, encode) {
        if (!encode)
            this._metaKey = htmlencode(metaKey);
        else this._metaKey = metaKey;
    }
    , getJs: function() {
        return this._metaJs;
    }
    , setJs: function(metaJs, encode) {
        // metaJs = metaJs.replace(/<script>|<\/script>/ig, '');
        if (!encode)
            this._metaJs = htmlencode(metaJs);
        else this._metaJs = metaJs;
    }
    , getPageTitle: function() {
        return this._title;
    }
    , setPageTitle: function(newTitle, encode) {
        if (!encode)
            this._title = htmlencode(newTitle);
        else this._title = newTitle;
        if (this._selfDOM.classList.contains('spr-active-page'))
            document.title = this._title;
    }
    , getPageFavicon: function() {
        return this._favicon || this.__proto__._favicon;
    }
    , setPageFavicon: function(newFavicov, encode) {
        if (!encode)
            this._favicon = htmlencode(newFavicov);
        else this._favicon = newFavicov;
        if (this._selfDOM.classList.contains('spr-active-page'))
            document.querySelector('link[rel=icon]').href = this._favicon;
    }
    , moveChildrenToHtmlDOM: function(page) {
        var animations = page.querySelectorAll('.aos-animate');
        Array.prototype.forEach.call(animations, function(el) {
            if (el.classList.contains('aos-animate')) {
                el.classList.remove('aos-animate')
            }
        });
        var vides = page.querySelectorAll('.bg-video');
        if (vides) {
            Array.prototype.forEach.call(vides, function (vide, indx) {
                if ($(vide).data('vide')) {
                    $(vide).data('vide').destroy();
                } else {
                    var video = vide.querySelector('video');
                    if (video) video.pause();
                }
            });
        }
        this.html = page.innerHTML;
        var childrenPage = document.createDocumentFragment();
        while (page.children.length > 0) {
            childrenPage.appendChild(page.children[0]);
        }
        this.htmlDOM = childrenPage;
        this.load = false;
    }
    , extractContent: function() {
        var _this = this;
        if (_this.load) {
            var buffer = document.createElement('div');
            buffer.innerHTML = _this.html;
            _this.moveChildrenToHtmlDOM(buffer);
            _this.load = false;
            _this._selfDOM.appendChild(_this.htmlDOM);
            builder.clearGalleryOnPage(_this._selfDOM);
            builder.reloadScript(_this._selfDOM);
        } else {
            _this._selfDOM.appendChild(_this.htmlDOM);
        }
    }
};
