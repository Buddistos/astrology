/*
 * @autor: MultiFour
 * @version: 1.0.0
 */


"use strict";

/**
 * controls object contains tools for creating and working the controls buttons
 */
var controls = {
    /**
     *
     * @param _this
     * @param classButton {string}
     * @param classIcon {string} icon into button
     * @param Obj {HTMLElement} editing element on current page
     * @param ObjControl {object} EditorText or some else
     * @returns {Element}
     * @private
     */
    _button: function(_this, classButton, classIcon, Obj, ObjControl) {
        var button = document.createElement('button');
        button.className = classButton;
        button.setAttribute('type', 'button');
        var icon = document.createElement('i');
        icon.className = classIcon;
        button.appendChild(icon);

        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            _this.doThis(Obj, ObjControl, this);
        });

        return button;
    }
    , _getPositionInGallery: function(element, owl) {
        var countScreen = owl.querySelectorAll('.owl-item.active').length;
        var countItems = owl.querySelectorAll('.owl-item:not(.cloned)').length;
        var arr = owl.querySelectorAll('.owl-item');
        if (arr.length !== countItems) arr = Array.prototype.slice.call(arr, countScreen);
        var index = null;
        Array.prototype.forEach.call(arr, function(el, indx) {
            if (el === element) {
                index = indx;
            }
        });

        return index > (countItems - 1) ? index - countItems - 1 : index;
    }
    /**
     * History poin
     * @param li {HTMLElement}
     * @private
     */
    , _moveUp: function(li) {
        if (li.previousSibling
            && !li.previousSibling.classList.contains('nav')
            && !li.classList.contains('footer')) {
            li.parentNode.insertBefore(li, li.previousSibling);

            builder.setStep(function() {
                controls._moveDown(li);
            });
        }
    }
    /**
     * History poin
     * @param li {HTMLElement}
     * @private
     */
    , _moveDown: function(li) {
        if (li.nextSibling
            && !li.nextSibling.classList.contains('footer')
            && !li.classList.contains('nav')) {
            li.parentNode.insertBefore(li, li.nextSibling.nextSibling);

            builder.setStep(function() {
                controls._moveUp(li);
            });
        }
    }
    /**
     * Find paren element for child el
     * @param el {HTMLElement}
     * @param arrElIdentif {Array} exemple ['buttons-control', 'buttons-control-form', 'ul']
     * @returns {*} HTMLElement or NULL
     */
    , findParent: function(el, arrElIdentif) {
        var DOM = el;
        for (var indx in arrElIdentif) {
            while (
                DOM !== null
                && DOM.classList !== undefined
                && !DOM.classList.contains(arrElIdentif[indx])
            ) {
                DOM = DOM.parentElement;
            }
            
            if (DOM === null) {
                DOM = el;
                while (DOM !== null && DOM.tagName.toLowerCase() !== arrElIdentif[indx]) {
                    DOM = DOM.parentElement;
                }
                if (DOM === null && arrElIdentif.length > (indx+1)) {
                    DOM = el;
                } else {
                    break;
                }
            } else {
                break;
            }
        }
        return DOM;
    }
    , childOf: function(c, p) {
        while((c=c.parentNode)&&c!==p);
        return !!c;
    }
    , getUpSection: {
        html: function(li) {
            var unactive = '';
            if (li.classList.contains('nav') || li.classList.contains('footer')) unactive = ' unactive';
            return controls._button(this, 'supra-btn btn-control-1' + unactive, 'supra icon-arrow-up', li);
        }
        , doThis: function(li) {
            controls._moveUp(li);
            builder.reloadScript(li);
            //if (AOS) AOS.refresh();
            if (builder.windowIframe.AOS) builder.windowIframe.AOS.refresh();
        }
    }
    , getDownSection: {
        html: function(li) {
            var unactive = '';
            if (li.classList.contains('nav') || li.classList.contains('footer')) unactive = ' unactive';
            return controls._button(this, 'supra-btn btn-control-1' + unactive, 'supra icon-arrow-down', li);
        }
        , doThis: function(li) {
            controls._moveDown(li);
            builder.reloadScript(li);
            //if (AOS) AOS.refresh();
            if (builder.windowIframe.AOS) builder.windowIframe.AOS.refresh();
        }
    }
    , getBgSection: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-section-bg', li);
        }
        , doThis: function(li) {
            //PopupBg
            var modal = new Modal('supra-modal', 'SectionBg', li);
            $(modal).modal('show');
        }
    }
    , getMainSettingsSection: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-equalizer', li);
        }
        , doThis: function(li) {
            if (li.dataset.group === "navigations") {
                var modal = new Modal('supra-modal', 'NavSectionSettings', li);
                $(modal).modal('show');
            } else {
                var modal = new Modal('supra-modal', 'SectionSettings', li);
                $(modal).modal('show');
            }
        }
    }
    , getMainSettingsPopup: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-equalizer', li);
        }
        , doThis: function(li) {
            var modal = new Modal('supra-modal', 'PopupSettings', li);
            $(modal).modal('show');
        }
    }
    , getCodeEditor: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-code', li);
        }
        , doThis: function(li) {
            var modal = new Modal('supra-modal', 'CodeEditor', li);
            $(modal).modal('show');
        }
    }
    , getCopy: {
        html: function(li) {
            var unactive = '';
            if (li.classList.contains('nav') || li.classList.contains('footer')) unactive = ' unactive';
            return controls._button(this, 'supra-btn btn-control-1' + unactive, 'supra icon-copy', li);
        }
        , doThis: function(li) {
            if (!li.classList.contains('nav') && !li.classList.contains('footer')) {
                if (li.querySelector('.spr-gallery')) {
                    var cloneSection = builder.cloneOwlGallery( li );
                    builder.reloadScript( li );
                } else {

                    var cloneSection = li.cloneNode(true);
                    li.parentNode.insertBefore(cloneSection, li.nextSibling);
                }

                var bg = cloneSection.querySelector('.parallax-bg');
                if (bg) {
                    bg.removeAttribute('style');
                    if (skr) skr.refresh();
                }

                var pageObj = builder.getActivePageObject();

                builder.setStep(function () {
                    pageObj.deleteSection(cloneSection);
                });

                //need to be first addSectionToDataPage for correct showing section id
                pageObj.addSectionToDataPage(cloneSection);
                controls.rebuildControl(cloneSection);

                //for popup
                if (cloneSection.children[0].classList.contains('modal')) {
                    builder.createPopupThumb(cloneSection.children[0]);
                    cloneSection.children[0 ].style.display = 'none';
                }

                //TODO: check is this need at present
                var form = cloneSection.querySelector('form');
                if (form) {
                    builder.addNewForm(form, cloneSection.children[0], li.children[0].id);
                }

                var maps = cloneSection.querySelectorAll('.g-map');
                if (maps) {
                    Array.prototype.forEach.call(maps, function(map){
                        var oldId = map.id;
                        var id = builder.addNewGMap(map, cloneSection.children[0]);
                        builder.changeIdGMapInScript(cloneSection.querySelector('script'), oldId, id);
                        builder.reloadScript(cloneSection.parentElement);
                    });
                }

                if (cloneSection.children[0].classList.contains('counter-up')) {
                    builder.changeIdCountUpInScript(
                        cloneSection.querySelector('script'), li.children[0].id, cloneSection.children[0].id
                    );
                }

                var countDowns = cloneSection.querySelectorAll('.countdown');
                if (countDowns) {
                    Array.prototype.forEach.call(countDowns, function(countDown){
                        var oldId = countDown.id;
                        var id = builder.addNewCountDown(countDown, cloneSection.children[0]);
                        builder.changeIdCountDownInScript(cloneSection.querySelector('script'), oldId, id);
                        builder.reloadScript(cloneSection.parentElement);
                    });
                }

                var navsTabs = cloneSection.querySelectorAll('.nav-tabs');
                if (navsTabs) {
                    Array.prototype.forEach.call(navsTabs, function (navTabs, indx) {
                        controls.copySectionWithNavTab(navTabs, indx, cloneSection);
                    });
                }

                var accordions = cloneSection.querySelectorAll('.accordion');
                if (accordions) {
                    Array.prototype.forEach.call(accordions, function (accordion, indx) {
                        controls.copySectionWithAccordion(accordion, indx, cloneSection);
                    });
                }

                builder.listenerSectionsMouseDown(null, cloneSection.lastChild);
                //if (AOS) AOS.refresh();
                if (builder.windowIframe.AOS) builder.windowIframe.AOS.refresh();
            }
        }
    }
    , copySectionWithNavTab: function (navTabs, sIndx, cloneSection) {
        var section = cloneSection.children[0];
        var tabs = navTabs.parentElement;
        var titleTabs = navTabs.querySelectorAll('li a');
        var contentTabs = tabs.querySelector('.tab-content').querySelectorAll('.tab-pane');
        var sectionId = section.id.replace(/-/g,'_') + '_' + sIndx;

        Array.prototype.forEach.call(titleTabs, function (el, indx) {
            el.href = '#' + sectionId + '--' + indx;
            el.setAttribute('aria-controls', sectionId + '--' + indx);
            el.id = sectionId + '--' + indx + '-tab';
            contentTabs[indx].id = sectionId + '--' + indx;
            contentTabs[indx].setAttribute('aria-labelledby', sectionId + '--' + indx + '-tab');
        });
    }
    , copySectionWithAccordion: function (accordion, sIndx, cloneSection) {
        var section = cloneSection.children[0];
        var titleAccordion = accordion.querySelectorAll('.panel');
        var sectionId = section.id.replace(/-/g,'_') + '_' + sIndx;
        accordion.id = sectionId + '-accordion';

        Array.prototype.forEach.call(titleAccordion, function (el, indx) {
            var title = el.querySelector('a.panel-heading');
            var collapse = el.querySelector('.panel-collapse');
            title.href = '#' + sectionId + '--' + indx;
            title.dataset.parent = '#' + sectionId + '-accordion';
            collapse.id = sectionId + '--' + indx;
        });
    }
    , getDel: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-2', 'supra icon-trash2', li);
        }
        , doThis: function(li) {
            if (builder) {
                var modal = new Modal('supra-modal', 'Delete', {
                        page: builder.getActivePageObject()
                        , section: li
                    }
                );
                $(modal).modal('show');
            }
        }
    }
    , getID: {
        html: function(li) {
            var label = document.createElement('label');
            label.className = 'supra label-section';
            label.dataset.sectionId = '#' + li.children[0].id;
            if (li.dataset.minWidth) label.style.minWidth = li.dataset.minWidth;
            return label;
        }
    }
    , getButtonSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-choose-btn', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'ButtonSettings', Obj);
            $(modal).modal('show');
        }
    }
    , getImageSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-picture', Obj);
        }
        , doThis: function(Obj) {
            if (Obj.classList.contains('spr-option-link-img')) Obj = Obj.querySelector('img');
            var modal = new Modal('supra-modal', 'ImageSettings', Obj);
            $(modal).modal('show');
        }
    }
    , getGMapSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-map-marker', Obj);
        }
        , doThis: function(Obj) {
            var li = controls.findParent(Obj, ['section-item']);
            var modal = new Modal('supra-modal', 'GMapSettings', {
                script: li.querySelector('script')
                , li: li
                , map: Obj
            });
            $(modal).modal('show');
        }
    }
    , getElementH: {
        html: function(Obj) {
            var h = Obj.tagName.match(/h([1-4])/i);
            var buttons = document.createDocumentFragment();
            if (h) {
                for(var i = 1; i <= 4; i++) {
                    var active = '';
                    if (i == h[1])
                        active = ' active';
                    buttons.appendChild(controls._button(this, 'supra-btn btn-control-1 h-element' + active, 'supra icon-h' + i, Obj, {n: 'H' + i}));
                }
            }
            return buttons;
        }
        , doThis: function(Obj, H) {
            if (Obj.tagName !== H.n) {
                var prevTimeMark = Date.now();
                var CTimeMark = Date.now()+1;
                var h = document.createElement(H.n);
                var parent = Obj.parentElement;
                h.innerHTML = Obj.innerHTML;
                Obj.parentElement.insertBefore(h, Obj);
                for (var index = Obj.attributes.length - 1; index >= 0; --index) {
                    h.attributes.setNamedItem(Obj.attributes[index].cloneNode());
                }
                Obj.classList.add('spr-tm-' + prevTimeMark);
                if (h.className.match(/(\s?spr-tm-[^\s]*)+/i)) {
                    h.className = h.className.replace(/(\s?spr-tm-[^\s]*)+/ig, '').trim();
                }
                h.classList.add('spr-tm-' + CTimeMark);
                Obj.parentElement.removeChild(Obj);
                Obj.classList.add('spr-outline-control');
                controls.rebuildControl(h);

                var li = controls.findParent(h, ['section-item', 'modal-dialog']);
                var controlGroup = li.querySelector('.wrap-control-element.nowrap');

                if (controlGroup) controlGroup.parentElement.removeChild(controlGroup);

                builder.setStep(function() {
                    controls.historyElementH(Obj.tagName, h, Obj, parent, CTimeMark, prevTimeMark);
                });
            }
        }
    }
    , getElementSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-equalizer', Obj);
        }
        , doThis: function(Obj) {
            var li = controls.findParent(Obj, ['section-item', 'modal-dialog']);
            var modal = new Modal('supra-modal', 'ElementSettings', {
                li: li
                , element: Obj
            });
            $(modal).modal('show');
        }
    }
    , getLink: {
        html: function(Obj) {
            var active = '';
            var link = Obj;
            //<a><img></a> or <a><i></i></a>
            if (Obj.tagName !== 'A' && Obj.parentElement.tagName === 'A') link = Obj.parentElement;
            //<li><a></a></li>
            if (Obj.tagName !== 'A' && Obj.children[0] && Obj.children[0].tagName === 'A') link = Obj.children[0];
            if (link.tagName === 'A') active = ' active';
            return controls._button(this, 'supra-btn btn-control-1' + active, 'supra icon-link2', Obj);
        }
        , doThis: function(Obj, x, button) {
            if (Obj.nodeName === 'LI') Obj = Obj.children[0];
            var modal = new Modal('supra-modal', 'LinkSettings', {element: Obj, button: button});
            $(modal).modal('show');
        }
    }
    , getStaticLink: {
        html: function(Obj) {
            var active = '';
            var link = Obj;
            //<a><img></a> or <a><i></i></a>
            if (Obj.tagName !== 'A' && Obj.parentElement.tagName === 'A') link = Obj.parentElement;
            //<li><a></a></li>
            if (Obj.tagName !== 'A' && Obj.children[0] && Obj.children[0].tagName === 'A') link = Obj.children[0];
            if (link.tagName === 'A') active = ' active';
            return controls._button(this, 'supra-btn btn-control-1' + active, 'supra icon-link2', Obj);
        }
        , doThis: function(Obj, x, button) {
            if (Obj.nodeName === 'LI') Obj = Obj.children[0];
            var modal = new Modal('supra-modal', 'LinkSettings', {element: Obj, button: button, mode: 'static'});
            $(modal).modal('show');
        }
    }
    , getVideoLink: {
        html: function(Obj) {
            var active = '';
            if (Obj.nodeName === 'DIV') Obj = Obj.children[0];
            if (Obj.nodeName === 'IFRAME' && Obj.src.search(/player\.vimeo\.com|embed/i) !== -1) active = ' active';
            return controls._button(this, 'supra-btn btn-control-1' + active, 'supra icon-iframe', Obj);
        }
        , doThis: function(Obj, x, button) {
            var modal = new Modal('supra-modal', 'VideoLinkSettings', {element: Obj, button: button});
            $(modal).modal('show');
        }
    }
    , getFormSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-envelope-open', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'FormSettings', Obj);
            $(modal).modal('show');
        }
    }
    , getSubscribeFormSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-envelope-open', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'SubscribeFormSettings', Obj);
            $(modal).modal('show');
        }
    }
    , getConstructorForm: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-form-constructor', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'ConstructorForm', Obj);
            $(modal).modal('show');
        }
    }
    , getAOSSettings: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-css-animation', Obj);
        }
        , doThis: function(Obj) {
            var li = controls.findParent(Obj, ['section-item', 'modal-dialog']);
            var modal = new Modal('supra-modal', 'AOSSettings', {
                li: li
                , element: Obj
            });
            $(modal).modal('show');
        }
    }
    //@TODO: need to be refactoring this crutches
    , getCopyElement: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-copy', Obj);
        }
        , doThis: function(Obj) {
            var wrap = controls.findParent(Obj, ['buttons-control', 'buttons-control-form']);
            var clone = wrap ? wrap.cloneNode(true) : Obj.cloneNode(true);
            var wrap = wrap ? wrap : Obj;
            var owlItem = controls.findParent(Obj, ['owl-item']);
            var nav = controls.findParent(Obj, ['nav']);
            var navTabs = controls.findParent(Obj, ['nav-tabs']);
            var accordion = controls.findParent(Obj, ['accordion']);
            var li = controls.findParent(Obj, ['section-item', 'modal-dialog']);

            if (owlItem && Obj.classList.contains('item')) {
                var script = li.querySelector('script');
                var owl = controls.findParent(owlItem, ['spr-gallery']);

                var position = controls._getPositionInGallery(owlItem, owl);
                var newOwlItem = owlItem.children[0].cloneNode(true);
                if (script.innerHTML.search(/magnificPopup/m) !== -1) {
                    builder.windowIframe.jQuery(owl).magnificPopup({
                        delegate: 'a:not(.external)', // the selector for gallery item
                        type: 'image',
                        gallery: {
                            enabled: true
                        },
                        image: {
                            titleSrc: function (item) {
                                return item.el.find('span.caption').text();
                            }
                        }
                    });
                }
                builder.windowIframe.jQuery(owl).trigger(
                    'add.owl.carousel'
                    , [builder.windowIframe.jQuery(newOwlItem), position]
                ).trigger('refresh.owl.carousel');

                clone = newOwlItem;
            } else if (Obj.classList.contains('g-map')) {
                var oldId  = Obj.id;
                var script = li.querySelector( 'script' );
                var id     = builder.addNewGMap( Obj, li.children[ 0 ] );
                builder.copyFunctionInitGmap( script, oldId, id );
                wrap.parentNode.insertBefore( clone, wrap.nextSibling );
                builder.reloadScript( li );
            //} else if(nav && !navTabs) {
            //    var navLi = controls.findParent( Obj, [ 'li' ] );
            //    clone     = wrap.cloneNode( true );
            //    navLi.parentNode.insertBefore( clone, navLi.nextSibling );
            } else if(navTabs && wrap.tagName === 'LI') {
                this.copyNavTabs( {
                    Obj      : Obj
                    , navTabs: navTabs
                    , wrap   : wrap
                    , clone  : clone
                } );
                return;
            } else if(accordion && wrap.classList.contains('panel')) {
                this.copyAccordionPanel( {
                    Obj      : Obj
                    , accordion: accordion
                    , wrap   : wrap
                    , clone  : clone
                } );
                return;
            } else {
                wrap.parentNode.insertBefore(clone, wrap.nextSibling);
            }

            var child = clone;

            if (Obj.tagName === 'FORM') {
                builder.addNewForm(child, li.children[0], li.children[0].id, true);
            }

            builder.stylingWrapParent(clone);

            controls.rebuildControl(clone);

            controls.nowrapCorrectigPosition(clone);

            //controls.changePropertyMargin(Obj, wrap);
            //controls.changePropertyMargin(child, clone);

            if(nav) {
                clone.removeAttribute('style');
            }

            var pageObj = builder.getActivePageObject();

            builder.setStep(function() {
                pageObj.deleteElement(child);
            });
        }
        , copyNavTabs: function(args) {
            var clone = args.wrap.cloneNode( true );
            var a = clone.querySelector('a');
            var tabs = args.navTabs.parentElement;
            var tabPane = tabs.querySelector('[aria-labelledby="' + a.id + '"]');
            var tabPaneClone = tabPane.cloneNode(true);

            var navLi = args.Obj;

            navLi.parentNode.insertBefore( clone, navLi.nextSibling );
            clone.classList.remove('active');

            tabPane.parentNode.insertBefore(tabPaneClone, tabPane.nextSibling);
            tabPaneClone.classList.remove('active');

            var child = navLi;

            var tabsID = tabs.querySelectorAll('.tab-pane');
            var namesTabsID = [];

            Array.prototype.forEach.call(tabsID, function(el){
                namesTabsID.push(el.id);
            });

            var newID = builder.uniqueName( tabPaneClone.id , namesTabsID);

            a.href = '#' + newID;

            tabPaneClone.id = newID;

            builder.stylingWrapParent(clone);

            controls.rebuildControl(clone);

            var pageObj = builder.getActivePageObject();

            builder.setStep(function() {
                pageObj.deleteElement(child);
            });
        }
        , copyAccordionPanel: function(args) {
            var clone = args.clone;
            var panelHeading = clone.querySelector('.panel-heading');
            var panelCollapse = clone.querySelector('.panel-collapse');


            args.wrap.parentNode.insertBefore(args.clone, args.wrap.nextSibling);
            args.clone.querySelector('.panel-heading').classList.add('collapsed');
            panelCollapse.classList.remove('in');

            var child = clone.children[0];

            var panels = args.accordion.querySelectorAll('.panel-collapse');
            var names = [];

            Array.prototype.forEach.call(panels, function(el){
                names.push(el.id);
            });

            panelCollapse.id = builder.uniqueName( panelCollapse.id , names);
            panelHeading.href = '#' + panelCollapse.id;

            builder.stylingWrapParent(clone);

            controls.rebuildControl(clone);

            var pageObj = builder.getActivePageObject();

            builder.setStep(function() {
                pageObj.deleteElement(child);
            });
        }
    }
    , getDelElement: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-2', 'supra icon-trash2', Obj);
        }
        , doThis: function(Obj) {
            if (builder) {
                var modal = new Modal('supra-modal', 'DeleteElement', Obj);
                $(modal).modal('show');
            }
        }
    }
    , getSettingsCountdownElement: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-timer', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'SettingsCountdownElement', Obj);
            $(modal).modal('show');
        }
    }
    , getIconsCheck: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-icons', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'IconsGallery', Obj);
            $(modal).modal('show');
        }
    }
    , getTextBold: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 strong', 'supra icon-bold', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeBold(button);
            } else {
                editorText.setBold(button);
            }
        }
    }
    , getTextItalic: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 em', 'supra icon-italic', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeItalic(button);
            } else {
                editorText.setItalic(button);
            }
        }
    }
    , getTextRtl: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 rtl', 'supra icon-rtl', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeRtl(button);
            } else {
                editorText.setRtl(button);
            }
        }
    }
    , getTextUpper: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 upper', 'supra icon-text-size', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeUpper(button);
            } else {
                editorText.setUpper(button);
            }
        }
    }
    , getTextMarker: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 mark', 'supra icon-highlight', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeMarker(button);
            } else {
                editorText.setMarker(button);
            }
        }
    }
    , getTextUnderline: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 ins', 'supra icon-underline', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeUnderline(button);
            } else {
                editorText.setUnderline(button);
            }
        }
    }
    , getTextStrikethrough: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 del', 'supra icon-strikethrough', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeStrikethrough(button);
            } else {
                editorText.setStrikethrough(button);
            }
        }
    }
    , getTextLink: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 link', 'supra icon-link2', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            var modal = new Modal('supra-modal', 'LinkSettings', {
                element: Obj,
                editor: editorText,
                button: button
            });
            $(modal).modal('show');
        }
    }
    , getTextAlignLeft: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 left', 'supra icon-text-align-left', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeTextAlignLeft(Obj, button);
            } else {
                editorText.setTextAlignLeft(Obj, button);
            }
        }
    }
    , getTextAlignCenter: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 center', 'supra icon-text-align-center', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeTextAlignCenter(Obj, button);
            } else {
                editorText.setTextAlignCenter(Obj, button);
            }
        }
    }
    , getTextAlignRight: {
        html: function(Obj, editorText) {
            return controls._button(this, 'supra-btn btn-control-1 right', 'supra icon-text-align-right', Obj, editorText);
        }
        , doThis: function(Obj, editorText, button) {
            if (button.classList.contains('active')) {
                editorText.removeTextAlignRight(Obj, button);
            } else {
                editorText.setTextAlignRight(Obj, button);
            }
        }
    }
    , getBgDiv: {
        html: function(Obj) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-section-bg', Obj);
        }
        , doThis: function(Obj) {
            var modal = new Modal('supra-modal', 'DivBg', Obj);
            $(modal).modal('show');
        }
    }
    , historyElementH: function(tag, Obj, prevObj, parent, CTimeMark, prevTimeMark) {
        if (!Obj.parentElement || !Obj.parentElement.parentElement) {
        Obj = parent.querySelector('.spr-tm-' + CTimeMark)
        }
        parent = Obj.parentElement && Obj.parentElement.parentElement ?
            Obj.parentElement.parentElement : parent;
        Obj.parentElement.insertBefore(prevObj, Obj);
        Obj.parentElement.removeChild(Obj);

        var currPageObj = builder.getActivePageObject();
        var editType = currPageObj.getDOMSelf().className.match(/edit-([^ ]*).*/i);
        if (editType && editType[0] !== 'edit-elements') {
            prevObj.removeAttribute('style');
        } else {
            controls.rebuildControl(parent);
        }

        builder.setStep(function() {
            controls.historyElementH(Obj.tagName, prevObj, Obj, parent, prevTimeMark, CTimeMark);
        });
    }
    , getBgPopup: {
        html: function(li) {
            return controls._button(this, 'supra-btn btn-control-1', 'supra icon-section-bg', li);
        }
        , doThis: function(li) {
            var modal = new Modal('supra-modal', 'PopupBg', li);
            $(modal).modal('show');
        }
    }
    /**
     * Creage group button control according to arrControl
     * @param arrControl {Array} setting in options
     * @param Obj {HTMLElement} For which creating group button control
     * @param className {string}
     * @param ObjControl {object} EditorText or some else
     * @returns {Element}
     */
    , getGroupControl: function(arrControl, Obj, className, ObjControl) {
        var _this = this;
        var buttonGroup = document.createElement('div');
        buttonGroup.className = className;
        buttonGroup.dataset.controls = JSON.stringify(arrControl);
        buttonGroup.setAttribute('role', 'group');
        buttonGroup.dataset.objControl = ObjControl ? 'editor-text' : '';

        if (buttonGroup.classList.contains('wrap-control')) {
            var wrapper = document.createElement('div');
            wrapper.className = 'wrapper flex-center';
            buttonGroup.appendChild(wrapper);
            var div = document.createElement('div');

            if (arrControl) {
                arrControl.forEach(function (element) {
                    if (element === 'getID') {
                        wrapper.appendChild(_this[element].html(Obj, ObjControl));
                    } else {
                        div.appendChild(_this[element].html(Obj, ObjControl));
                    }
                });
            }

            wrapper.appendChild(div);
        } else {
            if (arrControl) {
                arrControl.forEach(function (element) {
                    buttonGroup.appendChild(_this[element].html(Obj, ObjControl));
                });
            }
        }

        return buttonGroup;
    }
    , changePropertyMargin: function(child, wrap) {
        var margin = child.style.margin;
        child.style.transition = 'all 0s ease 0s';
        child.style.removeProperty('width');
        child.style.removeProperty('margin');
        var computedElStyle = window.getComputedStyle(child,null);
        wrap.style.margin = '0';
        var elementStyleMarginTop = computedElStyle.getPropertyValue("margin-top");
        var elementStyleMarginRight = computedElStyle.getPropertyValue("margin-right");
        var elementStyleMarginBottom = computedElStyle.getPropertyValue("margin-bottom");
        var elementStyleMarginLeft = computedElStyle.getPropertyValue("margin-left");

        wrap.style.marginTop = elementStyleMarginTop;
        wrap.style.marginRight = elementStyleMarginRight;
        wrap.style.marginBottom = elementStyleMarginBottom;
        wrap.style.marginLeft = elementStyleMarginLeft;
        child.style.margin = margin;
        if (child.tagName !== 'I' && child.tagName !== 'IMG') {
            child.style.width = '100%';
        }
        setTimeout(function () {
            child.style.removeProperty('transition');
        }, 10);
    }
    /**
     * to build control buttons for elements anew
     * @param wrap
     */
    , rebuildControl: function(wrap) {
        var _this = this;
        if (wrap.classList.contains('spr-outline-control')) {
            wrap.classList.remove('spr-outline-control');

            var li = _this.findParent(wrap, ['section-item']);
            var controlGroup = li.querySelector(
                '.wrap-control-element.nowrap'
                + ', .wrap-control-element-icons.nowrap'
                + ', .wrap-control-element-typography.nowrap'
            );
            if (controlGroup) {
                var arrControl    = JSON.parse( controlGroup.dataset.controls );
                var classControls = controlGroup.className.replace( /btn-group|wrap-control-element|nowrap/ig, '' );
                builder.addWrapEvventMouseEnterEditEelement.call(
                    builder
                    , li
                    , { controlsElement: arrControl }
                    , classControls
                    , wrap
                );

                builder.setControlsElementOnSection( wrap, li, builder.getPageMode(), false );
            }
            return;
        }
        var wrapControl = wrap.querySelectorAll('.wrap-control, .wrap-control-element');
        Array.prototype.forEach.call(wrapControl, function(element) {
            var arrControl = JSON.parse(element.dataset.controls);
            var parent = element.parentElement;
            var el = parent.children[0];
            var ObjControl = null;

            if (element.dataset.objControl === 'editor-text') {
                ObjControl = new EditorText(el);
            }

            if (element.classList.contains('wrap-control')) {
                el = parent;
            } else {
                var section = _this.findParent(parent, ['section-item', 'modal-dialog']);
                //_this.correctingPosition(parent, section);
            }

            parent.removeChild(element);
            parent.appendChild(controls.getGroupControl(arrControl, el, element.className, ObjControl));
        });
    }
    , nowrapCorrectigPosition: function(el) {
        var li = this.findParent(el, ['section-item', 'modal-dialog']);
        var controlGroup = li.querySelector(
            '.wrap-control-element.nowrap'
            + ', .wrap-control-element-icons.nowrap'
            + ', .wrap-control-element-typography.nowrap'
        );

        var boundingEl = el.getBoundingClientRect();
        var boundingLi = li.getBoundingClientRect();
        var topEl = boundingEl.top;
        var leftEl = boundingEl.left;
        var topLi = boundingLi.top;
        var leftLi = boundingLi.left;

        var absoluteTop = topEl - topLi;
        var absoluteLeft = leftEl - leftLi;

        controlGroup.style.top = absoluteTop + 'px';
        controlGroup.style.left = absoluteLeft + 'px';
    }
    , correctingPosition: function(wrap, section) {
        if (!wrap.classList.contains('buttons-control-typography')) {
            wrap.addEventListener('mouseenter', function(e) {
                controls.listenerCorrectingPosition(this, e, section);
            });
            wrap.addEventListener('touchstart', function(e) {
                controls.listenerCorrectingPosition(this, e, section);
            });
        }
    }
    , listenerCorrectingPosition: function(wrap, e, section) {
        var wrapControls = wrap.children[wrap.children.length - 1];
        var display = window.getComputedStyle(wrapControls, null).getPropertyValue("display");
        if (display !== 'none') {
            wrapControls.removeAttribute('style');
            var topButtonsControl = 42;

            var top = wrapControls.querySelector('button.supra-btn:first-child').getBoundingClientRect().top;
            var right = wrapControls.querySelector('button.supra-btn:last-child').getBoundingClientRect().right;

            var topSection = section.getBoundingClientRect().top;

            var elBounding = wrap.children[0].getBoundingClientRect();
            var heightElement = elBounding.height;
            var rightElement = elBounding.right;
            var windowsWidth = window.innerWidth;

            var condition = top < topSection || top < 0;

            var owlStage = this.findParent(wrap, ['owl-stage-outer']);
            var nav = builder.main.querySelector('.navbar');
            if (this.findParent(wrap, ['modal-dialog'])) {
                condition = !wrap.lastChild.classList.contains('popup-bg') && top < 0;
            } else if (owlStage) {
                var topOwlStage = owlStage.getBoundingClientRect().top;
                condition = top < topOwlStage;
            } else if (nav && !this.findParent(wrap, ['navbar'])) {
                var topNav = nav.getBoundingClientRect().bottom;
                condition = top < topNav;
            }

            if (condition) {
                if (wrap.lastChild.classList.contains('inside-top')) {
                    wrapControls.style.top = (heightElement - topButtonsControl) + 'px';
                } else {
                    wrapControls.style.top = (heightElement + topButtonsControl) + 'px';
                }
            }
            //if (wrap.children[0].className.search(/half-container|threequarter-container|quarter-container|elm-bg/) !== -1) {
            //    if (topSection < 0) {
            //        wrapControls.style.top = (heightElement) + 'px';
            //    } else {
            //        wrapControls.style.top = '42px';
            //    }
            //}
            if (windowsWidth - right < 20) {
                wrapControls.style.marginLeft = (rightElement - right) + 'px';
            }
        }
    }
};
