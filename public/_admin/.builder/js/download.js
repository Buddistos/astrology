function download(_this) {
    if (!_this._triggerDownload) {
        _this._triggerDownload = true;
        var data = {};
        data.pages = [];

        _this._videoBg = false;
        _this._owlGallery = false;
        _this._formSection = false;
        _this._parallax = false;
        _this._smooth = false;
        _this._magnific = false;
        _this._aos = false;

        var footer = document.createElement('footer');
        var styleMain = _this.main.children[0].innerHTML;

        _this._pages.forEach(function (pObj, indx) {
            var pageName = replaceSpace(pObj.getPageName());
            var sectionNames = {};
            var js = "\"use strict\";\n";
            var style = '';
            var contentJs = '';
            var aos = '';

            for (var group in pObj.sections) {
                sectionNames[group] = [];
                for (var name in pObj.sections[group]) {
                    sectionNames[group].push(name.split('--')[0]);
                }
            }
            var body = document.createElement('body');
            var modalContainer = document.createElement('div');
            modalContainer.className = 'modal-container';
            var container = document.createElement('div');
            // if a left bar of navigation is exist than container id need to be named "wrap"
            container.id = 'wrap';

            var page = pObj.getDOMSelf().cloneNode(true);
            if (_this._idActivePage * 1 !== indx) {
                page.innerHTML = pObj.html;
            }

            _this._findElForOptions(page);
            _this.clearGalleryOnPage(page);
            _this._clearOptionClasses(page);
            _this.clearControlElements(page);
            _this._reloadVideoBg(page, 'clear');

            if (pObj.preloader && pObj.preloader.html && _this._aos) {
                js += "\n $(window).load(function () {\n"
                    + "\t//------------------------------------------------------------------------\n"
                    + "\t//						PRELOADER SCRIPT\n"
                    + "\t//------------------------------------------------------------------------\n"
                    + "\t$(\"#preloader\").delay(400).fadeOut(\"slow\", function() {\n"
                    + "\t\tAOS.init({\n"
                    + "\t\t\teasing: 'ease-in-out-sine'\n"
                    + "\t\t});\n"
                    + "});\n"
                    + "\t$(\"#preloader .clock\").fadeOut();\n"
                    + "});\n";
            } else if (_this._aos && !(pObj.preloader && pObj.preloader.html)) {
                js += "window.addEventListener('load', function() {\n"
                    + "\tAOS.init({\n"
                    + "\t\teasing: 'ease-in-out-sine'\n"
                    + "\t});\n"
                    + "});\n";;
            } else if (pObj.preloader && pObj.preloader.html && !_this._aos) {
                js += "\n $(window).load(function () {\n"
                    + "\t//------------------------------------------------------------------------\n"
                    + "\t//						PRELOADER SCRIPT\n"
                    + "\t//------------------------------------------------------------------------\n"
                    + "\t$(\"#preloader\").delay(400).fadeOut(\"slow\");\n"
                    + "\t$(\"#preloader .clock\").fadeOut();\n"
                    + "});\n";
            }

            var openPopup = page.querySelector('.modal.in');
            if (openPopup) {
                openPopup.classList.remove('in');
                openPopup.style.display = 'none';
            }

            var sections = page.querySelectorAll('ul.' + pageName + ' > li');

            if (sections) {
                Array.prototype.forEach.call(sections, function (li) {
                    var styleEl = li.querySelector('style');
                    var script = li.querySelector('script');
                    if (
                        li.children.length < 1
                        || !styleEl
                        || !script
                        || !li.children[0]
                        || li.children[0].tagName === 'STYLE'
                        || li.children[0].tagName === 'SCRIPT'
                    ) {
                        return;
                    }

                    if (li.querySelector('.spr-gallery')) {
                        li = _this.cloneOwlGallery(li);
                    }
                    var cloneSection = li.children[0];
                    if (pObj.sections &&
                        pObj.sections[li.dataset.group] &&
                        pObj.sections[li.dataset.group][cloneSection.id] &&
                        pObj.sections[li.dataset.group][cloneSection.id].modal !== undefined) {
                        pObj.sections[li.dataset.group][cloneSection.id].modal.forEach(function (modalId) {
                            var modal = _this.modalContainer.querySelector('#' + modalId);
                            modalContainer.appendChild(modal.cloneNode(true));
                        });
                    }
                    var hasModal = li.children[0].classList.contains('modal');
                    if (li.dataset.group === "navigations") {
                        var nav = li.querySelector('.show-menu');
                        if (nav) nav.classList.remove('show-menu');
                        while (li.children[0].tagName !== "STYLE") {
                            body.appendChild(li.children[0]);
                        }
                    } else if (li.children[0].classList.contains('modal')) {
                        //for popup
                        modalContainer.appendChild(li.children[0]);
                    } else {
                        container.appendChild(cloneSection);
                        //container.innerHTML = '\n' + cloneSection.outerHTML.replace(/<(?!\/)/g, '\t\t<') + '\n';
                    }


                    var maps = cloneSection.querySelectorAll('.g-map');
                    Array.prototype.forEach.call(maps, function (map) {
                        map.innerHTML = '';
                        map.removeAttribute('style');
                    });

                    style += styleEl.innerHTML;

                    if (hasModal) {
                        var patternMapFuncInit = new RegExp('(initialize.*\\(\\);)','img');
                        script.innerHTML = script.innerHTML.replace(
                            patternMapFuncInit
                            , '$("#' + cloneSection.id + '").on("shown.bs.modal", function(){\n'
                            + '$1\n'
                            + '$("#' + cloneSection.id + '").off("shown.bs.modal");'
                            + '});\n'
                        );
                    }
                    contentJs += script.innerHTML.replace(/\/\/delete[\n\s\w\/;:'"#(){}\[\]\|$@!?\\=+<>,.-]*?\/\/deleteend|\n?\/\/magnific[\s\S]*?\/\/magnificend/img, '') || '';
                });
            }

            if (contentJs !== '') {
                js += "\ndocument.addEventListener('DOMContentLoaded', function() {\n"
                    + contentJs
                    + "\n});\n";
            }

            var mscripts = modalContainer.querySelectorAll('script');
            Array.prototype.forEach.call(mscripts, function ( el ) {
                el.parentElement.removeChild(el);
            });

            container.innerHTML = '\n' + container.innerHTML.replace(/((?:\n|^)\s*)</g, '$1\t\t\t<') + '\n\t\t';
            body.innerHTML += '\t\t' + container.outerHTML + '\n';
            body.innerHTML += '\t\t' + footer.outerHTML + '\n';
            body.innerHTML += '\t\t' + modalContainer.outerHTML;

            _this._findElForOptions(modalContainer);

            if (_this._parallax) {
                _this._clearParallaxSuperstructure(body);
            }

            data.pages.push({
                page_name: pageName
                , sections: sectionNames
                , content: body.innerHTML + '\n\t\t' + htmldecode(pObj.getJs()).replace(/<(?!\s+)/g, '\n\t\t\t<') + '\n\t\t'
                , style_options: page.className.match(/dark-page|light-page/i)[0]
                , title: pObj.getPageTitle()
                , favicon: pObj.getPageFavicon()
                , meta_description: pObj.getMetaDes()
                , meta_keywords: pObj.getMetaKey()
                , preloader: (pObj.preloader && pObj.preloader.html) ? pObj.preloader.html.replace(/\?t=[0-9]*/ig, '') : ''
                , js: js
                , style: style
            });
        });

        var jsOverAll = '';

        if (_this._smooth) {
            jsOverAll += "window.addEventListener('load', function() {"
                + "\n\t$('a.smooth').smoothScroll({speed: 800});"
                + "\n});";
        }

        if (_this._parallax) {
            jsOverAll += "\nwindow.addEventListener('load', function() {"
                + "\nif (!navigator.userAgent.match(/iPhone|iPad|iPod|Android|BlackBerry|IEMobile/i)) {"
                + "\n\tvar skr = skrollr.init("
                + "\n\t\t{"
                + "\n\t\tsmoothScrolling: false"
                + "\n\t\t, forceHeight: false"
                + "\n\t\t, mobileDeceleration: 0.004"
                + "\n\t\t}"
                + "\n\t\t);"
                + "\n\t}"
                + "\n});";
        }

        if (_this._datepicker) {
            jsOverAll += "\nwindow.addEventListener('load', function() {"
                + "\n\t$('body').delegate('input[type=text].datepicker-input', 'focusin', function(){"
                    + "\n\t\t$(this).datepicker({"
                        + "\n\t\t\tformat: 'dd.mm.yyyy',"
                        + "\n\t\t\tweekStart: 1,"
                        + "\n\t\t\tautoclose: true,"
                        + "\n\t\t\ttodayHighlight: true"
                    + "\n\t\t});"
                + "\n\t});"
            + "\n});";
        }

        if (_this._filefield) {
            jsOverAll += "\nwindow.addEventListener('load', function() {"
                + "\n\t$('body').delegate('.inputfile', 'change', customFileField);"
                + "\n});";
        }

        data.style = styleMain;
        data.video_bg = _this._videoBg;
        data.gallery = _this._owlGallery;
        data.form_section = _this._formSection;
        data.smooth = _this._smooth;
        data.parallax = _this._parallax;
        data.datepicker = _this._datepicker;
        data.filefield = _this._filefield;
        data.countup = _this._countup;
        data.countdown = _this._countdown;
        data.magnific = _this._magnific;
        data.aos = _this._aos;
        data.js_over_all = jsOverAll + '\n' + _this._magnificScript;
        data.forms = _this.forms;
        data.fonts_project = _this.arrayFontsOnProject;
        data.baseFilesForProject = options.baseFilesForProject;

        var form = new FormData();
        form.append('data', JSON.stringify(data));
        _this.ajax(form, 'download', function (data) {
            var data = JSON.parse(data);
            window.downloadFile('/.builder/tmp/' + data.file, data.file);
            setTimeout(function () {
                _this._triggerDownload = false;
            }, 2000);
            _this._resetIndExist();
        });
    }
}
