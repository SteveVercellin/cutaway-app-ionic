(function($) {
	$( "#div_datepicker" ).datepicker({
		onSelect: function(date, ui) {
			var d = new Date();

			var cur_date = new Date(d).getTime();

			var myDate = date.split("-");

			var newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];

			var date_select = new Date(newDate).getTime();

			if ( cur_date >= date_select ) {
				alert("La data selezionata deve essere maggiore della data corrente");
				setTimeout(function () {
		            $("#div_datepicker").find(".ui-state-active").removeClass("ui-state-active");
		        },100);
			}
			else {
	            $("#home-datepicker").val(date);
            }
        },
        dateFormat: "yy-mm-dd",
	});

	/* Search Input Placeholder */
	var Defaults = $.fn.select2.amd.require('select2/defaults');

    $.extend(Defaults.defaults, {
        searchInputPlaceholder: ''
    });

    var SearchDropdown = $.fn.select2.amd.require('select2/dropdown/search');

    var _renderSearchDropdown = SearchDropdown.prototype.render;

    SearchDropdown.prototype.render = function(decorated) {

        // invoke parent method
        var $rendered = _renderSearchDropdown.apply(this, Array.prototype.slice.apply(arguments));

        this.$search.attr('placeholder', this.options.get('searchInputPlaceholder'));

        return $rendered;
    };
    /* End - Search Input Placeholder */

	$(document).ready(function() {

		$('.wrap-select-address .select-address').select2({
			width: '100%',
		  	placeholder: 'Selezionare la posizione',
		  	searchInputPlaceholder: 'Cerca posizione'
		});

		// $("#multipleSelect_service").multiselect({
		// 	numberDisplayed: 1,
		// 	nonSelectedText: 'Nessuno selezionato',
		// });

		$(".find_barber_sort_by, .list-sort-by > div").click(function() {
			$(".wrap-sort-by").toggleClass("show");
		});

		$(".list-sort-by > div").click(function() {
			var location = $(".find_barber_variable").attr("location");
			var date = $(".find_barber_variable").attr("date");
			var services = $(".find_barber_variable").attr("services");
			var sort_type = $(this).attr("sort-type");
			var order = $(this).attr("order")
			if ( $(this).attr('order') == 'desc' ) {
				$(this).attr('order', 'asc');
			}
			else {
				$(this).attr('order', 'desc');
			}

			$.ajax({
				type:'GET',
				url: cutaway_ajax.ajax_url,
				data: 		{
					location: location,
					date: date,
					services: services,
					sort_type: sort_type,
					order: order,
					action: 'find_barber_filter',
				},
				cache:true,
				dataType:"json",
				beforeSend: function(xhr) {
					$('.barber-lists .block_overlay').addClass('show');
				},
				success: function(respond, status, xhr) {
					$(".barber-lists").html(respond.data + '<div class="block_overlay"></div>');
					$('.barber-lists .block_overlay').removeClass('show');
				}
			});
			return false;
		});

		/*$(".back-to-previou-page").click(function(e){
			e.preventDefault();
			javascript:history.back();
		});*/

		$(".slots-time .time").click(function() {
			var val = $(this).html();
			var parents = $(this).parents(".wrap-slots");
			var slots_time = $(this).parent();
			slots_time.find(".time").removeClass("selected");
			parents.find(".input_time").val(val);
			$(this).addClass("selected");
		});

		// Show menu when click menu icon
		$(".show-menu-icon").click(function() {
			if ( $(".main-menu").attr("show-menu") == 'hide' ) {
				$(".main-menu").addClass("show");
				$(".main-menu").attr("show-menu", 'show');
			}
		});

		$(".open-list-services").click(function() {
			$.ajax({
				url: cutaway_ajax.ajax_url,
				data: {
					action: 'generate_list_services_html'
				},
				dataType: 'html',
				method: 'get',
				beforeSend: function(jqXHR, settings) {
					$('#list_services_modal .modal-body').html('<div class="icon-ajax-loading"></div>');
					$('#list_services_modal').modal('show');
				},
				success: function(html, textStatus, jqXHR) {
					$('#list_services_modal .modal-body').html(html);
					setupListServicesModal();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$('#list_services_modal').modal('hide');
					$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.load_bookly_services_fail);
				}
			});
		});

		if ($(".barber-available-book").length) {
			$(".barber-available-book").bootstrapSwitch('onText', cutaway_i18n.on);
			$(".barber-available-book").bootstrapSwitch('offText', cutaway_i18n.off);
			var state = $(".barber-available-book").is(':checked') ? true : false;
			$(".barber-available-book").bootstrapSwitch('state', state);
			$(".barber-available-book").on('switchChange.bootstrapSwitch', function(event, state) {
				state = state ? 1 : 0;
				var revertState = !state;
				$waitingBox = null;

				var dataAjax = {
					'action': 'change_barber_available_booking',
					'state': state
				};

				$.ajax({
					url: cutaway_ajax.ajax_url,
					data: dataAjax,
					dataType: 'json',
					method: 'post',
					beforeSend: function(jqXHR, settings) {
						$waitingBox = $.windowWaiting();
					},
					success: function(res, textStatus, jqXHR) {
						$waitingBox.close();

						if (res.status == 'ok') {
							$.windowAlert(jquery_adapter_i18n.success_box_title, cutaway_i18n.change_barber_available_booking_success);
						} else {
						    $.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.change_barber_available_booking_fail);
						    $(".barber-available-book").bootstrapSwitch('state', revertState, true);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$waitingBox.close();
						$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.change_barber_available_booking_fail);
						$(".barber-available-book").bootstrapSwitch('state', revertState, true);
					}
				});

				return true;
			});
		}

		$(".barber-get-available-time").click(function() {
			var date = $('.barber_config_time_slots_date').val();
			var services = $('.list_services_selected').val();

			if (date == '' || services == '') {
				$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_missing_data);
			} else {
				$.ajax({
					url: cutaway_ajax.ajax_url,
					data: {
						action: 'get_staff_config_available_time_slots',
						date: date,
						services: services
					},
					dataType: 'json',
					method: 'get',
					beforeSend: function(jqXHR, settings) {
						$('#staff_time_slots_modal .modal-body').html('<div class="icon-ajax-loading"></div>');
						$('#staff_time_slots_modal').modal('show');
					},
					success: function(res, textStatus, jqXHR) {
						if (res.status == 'ok') {
							if (res.time != '') {
								$('#staff_time_slots_modal .modal-body').html(res.time);
								setupConfigTimeSlotsModal();
							} else {
								$('#staff_time_slots_modal').modal('hide');
								$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_no_time);
							}
						} else {
							$('#staff_time_slots_modal').modal('hide');
							$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_load_fail);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$('#staff_time_slots_modal').modal('hide');
						$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_load_fail);
					}
				});
			}
		});

		if ($('.service-book-submit').length) {
			$('.service-book-submit').click(function (e) {
				e.preventDefault();
				var $form = $(this).parent().find('form');
				if ($form.length) {
					$form.submit();
				}
			})
		}
	});

	$(document).mouseup(function(e) {
	    var container = $(".main-menu");

	    // if the target of the click isn't the container nor a descendant of the container
	    if (!container.is(e.target) && container.has(e.target).length === 0)
	    {
	    	if ( container.attr("show-menu") == 'show' ) {
				container.removeClass("show");
				container.attr("show-menu", 'hide');
				container.attr("clickoutsite", "0");
			}

	    }
	});

	$(window).resize(function() {
		calculateHeightServiceItem();
		correctPageHeight();
	});

	if ($('body.logged-in').length) {
		var swiper = new Swipe('body.logged-in');
		swiper.onLeft(function() { swipeleftHandler() });
		swiper.onRight(function() { swiperightHandler() });
		swiper.run();
	}


	// $( "body.logged-in" ).on( "swipeleft", swipeleftHandler );

	// $( "body.logged-in" ).on( "swiperight", swiperightHandler );

	function swipeleftHandler( event ){
		$(".main-menu").removeClass("show");
		$(".main-menu").attr("show-menu", 'hide');
	}

	function swiperightHandler( event ){
		$(".main-menu").addClass("show");
		$(".main-menu").attr("show-menu", 'show');
	}

	function setupListServicesModal()
	{
		var list_services_selected = $(".list_services_selected");
		if ( list_services_selected.length > -1 && list_services_selected.val() != "" ) {
			var val = list_services_selected.val();
			temp = val;
			var arr = val.split(",");

			for (var i = arr.length - 1; i >= 0; i--) {
				if ( arr[i] != "" ) {
					var service_book = $(".service-item-" + arr[i] + " .service-book");
					service_book.addClass("selected");
					service_book.html(cutaway_i18n.booked);
				}
			}
		}

		$(".service-item .service-book").click(function(e) {
			e.preventDefault();

			if ( $(this).hasClass("selected") ) {
				$(this).html(cutaway_i18n.book);
			}
			else {
				$(this).html(cutaway_i18n.booked);
			}

			$(this).toggleClass("selected");
		});

		$("#list_services_modal .modal-footer button").off('click');
		$("#list_services_modal .modal-footer button").on('click', function() {
			if ( !$(this).hasClass("button-cancle") ) {
				$(".wrap_services_selected ul").html('');
				$(".wrap_services_selected").hide();
				$(".list_services_selected").val('');

				if ($('.service-item .service-book.selected').length) {
					var ids = '';
					$('.service-item .service-book.selected').each(function (index, item) {
						var id = $(this).attr('service-id');
						var title = $(this).attr('service-title');

						if (ids == '') {
							ids = id;
						} else {
							ids += ',' + id;
						}

						$(".wrap_services_selected ul").append('<li>' + title + '</li>');
					});

					$(".list_services_selected").val(ids);
					$(".wrap_services_selected").show();
				}
			}
		});

		calculateHeightServiceItem();
	}

	function calculateHeightServiceItem() {
		var service = $(".services .service-item");
		var first_item = service.first();
		var ratio = 0.807;
		var w_service = first_item.outerWidth();
		var h_service = ratio * w_service;
		service.css("height", h_service + "px");
	}

	function setupConfigTimeSlotsModal()
	{
		$("#staff_time_slots_modal .slots-time .time").off('click');
	    $("#staff_time_slots_modal .slots-time .time").on('click', function() {
			$(this).toggleClass("selected");
		});

		$("#staff_time_slots_modal .modal-footer button").off('click');
		$("#staff_time_slots_modal .modal-footer button").on('click', function(e) {
			if ( !$(this).hasClass("button-cancle") && !$(this).hasClass("processing") ) {
				e.preventDefault();
				var $btn = $(this);

				var unAvaiTimeSlots = '';
				$("#staff_time_slots_modal .slots-time .time.selected").each(function () {
					var timeHtml = $(this).html();
					if (unAvaiTimeSlots == '') {
						unAvaiTimeSlots = timeHtml;
					} else {
						unAvaiTimeSlots += ',' + timeHtml;
					}
				});
				var date = $('.barber_config_time_slots_date').val();
				var services = $('.list_services_selected').val();

				if (date != '' && services != '') {
					$waitingBox = null;

					$.ajax({
						url: cutaway_ajax.ajax_url,
						data: {
							action: 'set_staff_config_available_time_slots',
							date: date,
							services: services,
							un_available_time: unAvaiTimeSlots
						},
						dataType: 'json',
						method: 'post',
						beforeSend: function(jqXHR, settings) {
							$waitingBox = $.windowWaiting();
							$btn.addClass("processing");
						},
						success: function(res, textStatus, jqXHR) {
							$waitingBox.close();
							$btn.removeClass("processing");

							if (res.status == 'ok') {
								$.windowAlert(jquery_adapter_i18n.success_box_title, cutaway_i18n.barber_config_available_time_slots_save_success);
							} else {
								$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_save_fail);
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$waitingBox.close();
							$btn.removeClass("processing");
							$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_save_fail);
						}
					});
				} else {
					$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.barber_config_available_time_slots_save_fail);
				}

				return false;
			}
		});
	}

	$(window).load(function () {
		correctPageHeight();
	});

	function correctPageHeight()
	{
		var mainMenuHeight = $('.main-menu').length ? $('.main-menu').height() : 0;
		if (mainMenuHeight) {
			$('#page').css({
				'min-height': mainMenuHeight + 'px',
				'overflow-y': 'visible'
			});
		}
	}

	$('.favourite-staff-icon').on('click', function (e) {
		e.preventDefault();
		var $favIcon = $(this);
		var $parent = $favIcon.parent();
		var $unFavIcon = $parent.find('.unfavourite-staff-icon');
		var staffId = $favIcon.attr('data-id');
		var $waitingBox = null;

		if (!$parent.hasClass('processing')) {
			var dataAjax = {
				'action': 'mark_staff_unfavourite',
				'staff': staffId
			};
			$.ajax({
				url: cutaway_ajax.ajax_url,
				data: dataAjax,
				dataType: 'json',
				method: 'post',
				beforeSend: function(jqXHR, settings) {
					$waitingBox = $.windowWaiting();
					$parent.addClass('processing');
				},
				success: function(res, textStatus, jqXHR) {
					$parent.removeClass('processing');
					$waitingBox.close();

					if (res.status == 'ok') {
						$.windowAlert(jquery_adapter_i18n.success_box_title, cutaway_i18n.mark_barber_unfavourite_success);
						$favIcon.hide();
						$unFavIcon.show();
					} else {
						$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.mark_barber_unfavourite_fail);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$parent.removeClass('processing');
					$waitingBox.close();
				}
			});
		}
	});

	$('.unfavourite-staff-icon').on('click', function (e) {
		e.preventDefault();
		var $unFavIcon = $(this);
		var $parent = $unFavIcon.parent();
		var $favIcon = $parent.find('.favourite-staff-icon');
		var staffId = $unFavIcon.attr('data-id');
		var $waitingBox = null;

		if (!$parent.hasClass('processing')) {
			var dataAjax = {
				'action': 'mark_staff_favourite',
				'staff': staffId
			};
			$.ajax({
				url: cutaway_ajax.ajax_url,
				data: dataAjax,
				dataType: 'json',
				method: 'post',
				beforeSend: function(jqXHR, settings) {
					$waitingBox = $.windowWaiting();
					$parent.addClass('processing');
				},
				success: function(res, textStatus, jqXHR) {
					$parent.removeClass('processing');
					$waitingBox.close();

					if (res.status == 'ok') {
						$.windowAlert(jquery_adapter_i18n.success_box_title, cutaway_i18n.mark_barber_favourite_success);
						$unFavIcon.hide();
						$favIcon.show();
					} else {
						$.windowAlert(jquery_adapter_i18n.error_box_title, cutaway_i18n.mark_barber_favourite_fail);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$parent.removeClass('processing');
					$waitingBox.close();
				}
			});
		}
	});

})(jQuery);