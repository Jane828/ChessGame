/*!
 * jQuery Copy Plugin
 * version: 1.0.0-2018.01.23
 * Requires jQuery v1.5 or later
 * Copyright (c) 2018 Tiac
 * http://www.cnblogs.com/tujia/p/8336671.html
 */

// AMD support
(function (factory) {
    "use strict";
    if (typeof define === 'function' && define.amd) {
        // using AMD; register as anon module
        define(['jquery'], factory);
    } else {
        // no AMD; invoke directly
        factory( (typeof(jQuery) != 'undefined') ? jQuery : window.Zepto );
    }
}

(function($) {
"use strict";

/*
    Basic Usage:
    -----------

    Html:
        <button type="button" class="btn-copy" data-clipboard-text="Copy Me!">Copy</button>
    JS:
        $('.btn-copy').copy();

    
    Html:
        <div class="input-group">
            <input type="text" class="form-control inp-link">
            <span class="input-group-btn">
                <button class="btn btn-primary btn-copy" type="button">Copy</button>
            </span>
        </div>
    JS:
        $('.btn-copy').copy({
            copy: function(_this){
                return _this.parents('div').find('.inp-link').val();
            },
            afterCopy: function(res){
                if(res==true){
                    alert('Copied text to clipboard。');
                }else{
                    alert('Copy failed！');
                }
            }
        });
*/

var clipboard_text = '';

function copyTextToClipboard(_this, text) {
    var oTa = jQuery('<textarea style="position:fixed;left:0;top:0;z-index:9999999999"></textarea>');
    oTa.val(text);

    _this.after(oTa);

    oTa.select();

    try {
        var result = document.execCommand('copy');
        oTa.remove();
        return result;
    } catch (err) {
        console.log(err);
        return false;
    }
}

$.fn.copy = function(options) {
    if(options===undefined) options = {};
    var defaults = {};
    defaults.copy = function(_this){
        clipboard_text = _this.data('clipboard-text');
        return clipboard_text;
    };

    defaults.afterCopy = function(res, _this){
        if(res){
            console.log('Copied text to clipboard: ' + clipboard_text);
        }else{
            console.log('Copy failed！');
        }
    };

    options = $.extend(defaults, options);

    this.on('click', function(){
        clipboard_text = options.copy($(this));
        var res = copyTextToClipboard($(this), clipboard_text);
        options.afterCopy(res, $(this));
    });
};

}));
