var map;
var infowindow;
var marker;
var autocomplete;
var defaultLat = 45.116177;
var defaultLng = 7.742615;
jQuery(function($) {

    var
        $locations_list      = $('#bookly-locations'),
        $check_all_button    = $('#bookly-locations-check-all'),
        $location_modal      = $('#bookly-location-modal'),
        $location_name       = $('#bookly-location-address'),
        $location_info       = $('#bookly-location-info'),
        $staff               = $('#bookly-js-staff'),
        $location_new_title  = $('#bookly-new-locations-title'),
        $location_edit_title = $('#bookly-edit-locations-title'),
        $save_button         = $('#bookly-location-save'),
        $delete_button       = $('#bookly-delete'),
        $add_button          = $('#bookly-location-add'),
        row
    ;

    /**
     * Init DataTables.
     */
    var dt = $locations_list.DataTable({
        paging: false,
        info: false,
        searching: false,
        processing: true,
        responsive: true,
        ajax: {
            url: ajaxurl,
            data: { action: 'bookly_locations_get_locations', csrf_token : BooklyL10n.csrfToken }
        },
        rowReorder: {
            dataSrc: 'position',
            snapX: true,
            selector: '.bookly-icon-draghandle'
        },
        order: [0, 'asc'],
        columnDefs: [
            { visible: false, targets: 0 },
            { orderable: false, targets: '_all' }
        ],
        columns: [
            {
                data: 'position'
            },
            {
                render: function (data, type, row, meta) {
                    return '<i class="bookly-icon bookly-icon-draghandle bookly-cursor-move" title="' + BooklyL10n.reorder + '"></i>';
                }
            },
            { data: 'name' },
            {
                data: 'staff_ids',
                render: function (data, type, row, meta) {
                    if (data.length == 0) {
                        return BooklyL10n.staff.nothingSelected;
                    } else if (data.length == 1) {
                        return BooklyL10n.staff.collection[data[0]].full_name;
                    } else {
                        if (data.length == Object.keys(BooklyL10n.staff.collection).length) {
                            return BooklyL10n.staff.allSelected;
                        } else {
                            return data.length + '/' + Object.keys(BooklyL10n.staff.collection).length;
                        }
                    }
                }
            },
            {
                responsivePriority: 1,
                render: function (data, type, row, meta) {
                    return '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#bookly-location-modal"><i class="glyphicon glyphicon-edit"></i> ' + BooklyL10n.edit + '</button>';
                }
            },
            {
                responsivePriority: 1,
                render: function (data, type, row, meta) {
                    return '<input type="checkbox" class="bookly-js-delete" value="' + row.id + '" />';
                }
            }
        ],
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing:  BooklyL10n.processing
        }
    }).on( 'row-reordered', function ( e, diff, edit ) {
        let positions = [];
        dt.data().each(function (item) {
            positions.push({position: parseInt(item.position), id: item.id});
        });
        $.ajax({
            url : ajaxurl,
            type: 'POST',
            data: {
                action     : 'bookly_locations_update_locations_position',
                csrf_token : BooklyL10n.csrfToken,
                positions  : (positions.sort((a, b) => a.position - b.position))
                    .map(function (value) {
                        return value.id;
                    })
            },
            dataType: 'json',
            success: function (response) {

            }
        });
    });

    /**
     * Select all locations.
     */
    $check_all_button.on('change', function () {
        $locations_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On location select.
     */
    $locations_list.on('change', 'tbody input:checkbox', function () {
        $check_all_button.prop('checked', $locations_list.find('tbody input:not(:checked)').length == 0);
    });

    /**
     * Edit location.
     */
    $locations_list.on('click', 'button', function () {
        row = dt.row($(this).closest('td'));
    });

    /**
     * Add new location.
     */
    $add_button.on('click', function () {
        row = null;
    });

    /**
     * On show modal.
     */
    $location_modal.on('show.bs.modal', function (e) {
        var data;
        if (row) {
            data = row.data();
            $location_new_title.hide();
            $location_edit_title.show();
        } else {
            data = {title: '', staff_ids: []};
            $location_new_title.show();
            $location_edit_title.hide();
        }
        $location_name.val(data.name);
        $('#bookly-location-name').val(data.name);
        $('#bookly-location-lat').val(data.lat);
        $('#bookly-location-long').val(data.lng);
        $location_info.val(data.info);
        $staff.booklyDropdown('setSelected', data.staff_ids);
        if (data.lat != '' && data.lng != '') {
            var latlng = new google.maps.LatLng(data.lat, data.lng);
        } else {
            var latlng = new google.maps.LatLng(defaultLat, defaultLng);
        }

        map.setCenter(latlng);
        marker.setPosition(latlng);
    });

    /**
     * Staff drop-down.
     */
    $staff.booklyDropdown();

    /**
     * Save location.
     */
    $save_button.on('click', function (e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        var data = $form.serializeArray();
        data.push({name: 'action', value: 'bookly_locations_save_location'});
        if (row){
            data.push({name: 'id', value: row.data().id});
        }
        var ladda = Ladda.create(this, {timeout: 2000});
        ladda.start();
        $.ajax({
            url  : ajaxurl,
            type : 'POST',
            data : data,
            dataType : 'json',
            success  : function(response) {
                if (response.success) {
                    if (row) {
                        row.data(response.data).draw();
                    } else {
                        dt.row.add(response.data).draw();
                    }
                    $location_modal.modal('hide');
                } else {
                    alert(response.data.message);
                }
                ladda.stop();
            }
        });

    });

    /**
     * Delete locations.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $locations_list.find('input.bookly-js-delete:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url  : ajaxurl,
                type : 'POST',
                data : {
                    action     : 'bookly_locations_delete_locations',
                    csrf_token : BooklyL10n.csrfToken,
                    locations  : data
                },
                dataType : 'json',
                success  : function(response) {
                    ladda.stop();
                    if (response.success) {
                        dt.rows($checkboxes.closest('td')).remove().draw();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });
});
function googleMapInitialize() {
    var latlng = new google.maps.LatLng(defaultLat, defaultLng);
    map = new google.maps.Map(document.getElementById('google-map-wrap'), {
        center: latlng,
        zoom: 13
    });
    marker = new google.maps.Marker({
        map: map,
        position: latlng,
        draggable: true,
        anchorPoint: new google.maps.Point(0, -29)
    });
    var input = document.getElementById('bookly-location-name');
    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var geocoder = new google.maps.Geocoder();
    autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);
    //infowindow = new google.maps.InfoWindow();
    autocomplete.addListener('place_changed', function() {
        //infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
        //infowindow.setContent(place.formatted_address);
        //infowindow.open(map, marker);
    });
    // this function will work on marker move event into map
    google.maps.event.addListener(marker, 'dragend', function() {
        geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
                    //infowindow.setContent(results[0].formatted_address);
                    //infowindow.open(map, marker);
                }
            }
        });
    });
 }

function bindDataToForm(address,lat,lng){
    document.getElementById('bookly-location-address').value = address;
    document.getElementById('bookly-location-lat').value = lat;
    document.getElementById('bookly-location-long').value = lng;
}


google.maps.event.addDomListener(window, 'load', googleMapInitialize);