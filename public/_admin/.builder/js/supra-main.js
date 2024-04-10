/*
 * @autor: MultiFour
 * @version: 1.0.0
 */

"use strict";

/* --------------------------------------------- Preloader ------------------------------------------------------- */
var loadPreloader = document.querySelector('.progress .load');
var baseUri = location.protocol + '//' + location.hostname + location.pathname;
function removePreloader() {
    var iframeWindow = document.querySelector('iframe#main').contentWindow;
    var iframeDocument = iframeWindow.document;
    var preloader = document.querySelector('.supra-preloader');
    var body = document.querySelector('body');
    var iframeBody = iframeDocument.querySelector('body');
    iframeBody.classList.add('run');
    body.classList.add('run');
    setTimeout(function () {
        if (preloader && preloader.parentElement) {
            preloader.parentElement.removeChild(preloader);
        }
    }, 1600);
    //AOS.init({
    //    easing: 'ease-in-out-sine'
    //});
    if (iframeWindow.AOS) iframeWindow.AOS.init({
        easing: 'ease-in-out-sine'
    });
}


/* ----------------------------------------------- Init Builder --------------------------------------------------- */

var builder = null;

var skr = null;

var loaded = false;

document.addEventListener('DOMContentLoaded', function(){
    var iframe = document.querySelector('iframe#main');

    var initSuprabuilder = function(){
        if(!loaded) {
            builder = new Suprabuilder();
            loaded = true;

            $(iframe.contentWindow.document ).find('.main').delegate( '.inputfile', 'change', iframe.contentWindow.customFileField);

            $(iframe.contentWindow.document ).find('.main').delegate("input[type=text].datepicker-input", "focusin", function(){
                $(this).datepicker({
                    format: "dd.mm.yyyy",
                    weekStart: 1,
                    autoclose: true,
                    todayHighlight: true,
                    updateViewDate: true
                });
            });

            if (!navigator.userAgent.match(/iPhone|iPad|iPod|Android|BlackBerry|IEMobile/)) {
                skr = iframe.contentWindow.skrollr.init(
                    {
                        smoothScrolling: false
                        , forceHeight: false
                        , mobileDeceleration: 0.004
                    }
                );
            }

            $(iframe.contentWindow.document ).find('.main').delegate( 'a', 'click', function (e) {
                if (/\.html/.test(this.getAttribute('href'))) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    };

    var loadingIframe = function() {
        setTimeout(function() {
            if(iframe.contentWindow.document.querySelector('.main')) {
                initSuprabuilder();
            } else {
                loadingIframe();
            }
        }, 500);
    };

    loadingIframe();
    // $(document).delegate(".datepicker-input input[type=text]", "focusin", function(){
    //     $(this).datepicker({
    //         format: "yyyy/mm/dd",
    //         weekStart: 1,
    //         autoclose: true,
    //         todayHighlight: true
    //     });
    // });

    iframe.contentWindow.HTMLElement.prototype.unWrapOne = HTMLElement.prototype.unWrapOne;
});

window.addEventListener('load',function(){
    setTimeout(function(){
            $('aside.control-panel').niceScroll({cursorcolor: "#555555", cursorborder: "1px solid #555555", autohidemode: "scroll", hidecursordelay: 0});
            $('aside.add-sections-items').niceScroll({cursorcolor: "#555555", cursorborder: "1px solid #555555", autohidemode: "scroll", hidecursordelay: 0});
            iframeLoaded('main');
    }, 1600);

    if (!window.localStorage.project) {
        loadPreloader.style.width = '100%';
        removePreloader();
    }

});

/* --------------------------------------------- LIBRARY --------------------------------------------------------- */
window.downloadFile = function(sUrl, fileName) {
    if (window.downloadFile.isChrome || window.downloadFile.isSafari) {
        var link = document.createElement('a');
        link.href = sUrl;

        if (link.download !== undefined){
            link.download = fileName;
        }

        if (document.createEvent) {
            var e = document.createEvent('MouseEvents');
            e.initEvent('click' ,true ,true);
            link.dispatchEvent(e);
            return true;
        }
    } else {
        var query = '?download';
        window.open(sUrl + query);
    }
};
window.downloadFile.isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') != -1;
window.downloadFile.isSafari = navigator.userAgent.toLowerCase().indexOf('safari') != -1;
/**
 * wrap only one elements
 * @param elms {HTMLElement}
 */
HTMLElement.prototype.wrap = function(elms) {
    var child = this;

    var parent  = elms.parentNode;
    var sibling = elms.nextSibling;

    child.appendChild(elms);

    if (sibling) {
        parent.insertBefore(child, sibling);
    } else {
        parent.appendChild(child);
    }
};
/**
 * unwrap only one elements
 */
HTMLElement.prototype.unWrapOne = function(mode) {
    var child = this.children[0];

    var parent = this.parentElement;
    var sibling = this.nextSibling;

    try {
        //if (child.hasAttribute('style')) {
        //    child.removeAttribute('style');
        //}
        //
        //if (this.dataset.style) {
        //    child.setAttribute( 'style', this.dataset.style );
        //}
    }
    catch (e) {
        console.log(this, child);
    }


    if (sibling) {
        parent.insertBefore(child, sibling);
    } else {
        parent.appendChild(child);
    }
    parent.removeChild(this);
};

function firstUp(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function firstDown(str) {
    return str.charAt(0).toLowerCase() + str.slice(1);
}

function replaceSpace(str) {
    return str.replace(/\s/ig, '_');
}

function toPageName(str) {
    return str.replace(/_/ig, ' ');
}

function cloneArray(arr) {
    return arr.slice(0, arr.length);
}

function htmlencode(str) {
    return str.replace(/[&<>"']/g, function($0) {
        return "&" + {"&":"amp", "<":"lt", ">":"gt", '"':"quot", "'":"#39"}[$0] + ";";
    });
}

function htmldecode(str) {
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#39;/g, function($0) {
        return {"&amp;":"&", "&lt;":"<", "&gt;":">", '&quot;':"\"", "&#39;":"'"}[$0];
    });
}

function addSlashes(str) {
    return str.replace(/[\*\(\)\[\]]/g, function($0) {
        return {"*":"\\*", "(":"\\(", ")":"\\)", "[":"\\[", "]":"\\]"}[$0];
    });
}

function clearTimeStamp(str) {
    var imgPattern = new RegExp('\\?t=[0-9]*', 'i');
    if (imgPattern.test(str))
        str = str.replace(imgPattern, '');
    return str;
}

function getDomPath(el) {
    var stack = [];
    while ( el.parentNode != null && !el.classList.contains('section-item')) {
        var sibCount = 0;
        var sibIndex = 0;
        if (!el.classList.contains('buttons-control')) {
            var elBase = el;
            if (el.parentElement.classList.contains('buttons-control')) {
                elBase = el.parentElement;
            }
            for (var i = 0; i < elBase.parentNode.childNodes.length; i++) {
                var sib = elBase.parentNode.childNodes[i];
                if (sib.nodeName == elBase.nodeName) {
                    if (sib === elBase) {
                        sibIndex = sibCount;
                    }
                    sibCount++;
                }
            }
            if (el.hasAttribute('id') && el.id != '') {
                stack.unshift(el.nodeName.toLowerCase() + '#' + el.id);
            } else if (sibCount > 1) {
                stack.unshift(el.nodeName.toLowerCase() + ':nth-child(' + (sibIndex + 1) + ')');
            } else {
                stack.unshift(el.nodeName.toLowerCase());
            }
        }
        el = el.parentNode;
    }

    return stack.join(' > ');
}

//crutch for ie
(function () {
    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent( 'CustomEvent' );
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();
