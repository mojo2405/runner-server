<?php

        $markers = array();
        $info = array();

        foreach ($users as $user){
            $last_location = $user->locations->last();
            if (!isset($last_location->latitude)){
                continue;
            }
            $markers[] = [$user->ID,$last_location->latitude,$last_location->longitude];
            $info[] = ['<div class="info_content" dir="rtl"><h3>פרטי משתמש</h3><p>מספר מתמודד : '.$user->runner_number.'</p>
<p>תעדות זהות : '.$user->ID.'</p><p>מספר טלפון : '.$user->phone.'</p>
<p>קו רוחב: '.$last_location->latitude.'</p>
<p>קו אורך: '.$last_location->longitude.'</p>
<p>עדכון אחרון :'.$last_location->created_at.'</p></div>'];
        }

?>
@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div id="map_wrapper">
                <div id="map_canvas" class="mapping"></div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 align-right" dir="rtl">
            <a href="{{ URL::to('downloadExcel/xlsx') }}" ><button style="margin: 5px" class="btn btn-success">ייצוא לאקסל</button></a>
            <h2>קריאות מצוקה</h2>
            <table class="table" dir="rtl" id="sos_table" style="table-layout: fixed;">
                <thead>
                <tr>
                    <th class="text-right" ></th>
                    <th class="text-right" >שעה</th>
                    <th class="text-right">מספר מתמודד</th>
                    <th class="text-right">תז</th>
                    <th class="text-right">טלפון</th>
                    <th class="text-right">קו אורך</th>
                    <th class="text-right">קו רוחב</th>
                </tr>
                </thead>
                <tbody style="word-break: break-all">
                </tbody>
            </table>
        </div>
    </div>

<style>

    #map_wrapper {
        /*height: 100%;*/
        margin-top:2vh;
    }

    #map_canvas {
        width: 100%;
        height: 80vh;
    }


</style>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAf-6G15D16dmNUombo05NG9ceI2a5Oy0g"></script>
<script>
    var sos =  <?php echo json_encode( $sos ); ?>;
    var t = $('#sos_table').DataTable({
            "data" : sos,
            "order": [[ 1, 'desc' ]],
            "language": {
                "emptyTable": "לא נמצאו קריאות",
                "info":           "מציג _START_ עד _END_ מתוך _TOTAL_ קריאות",
                "infoEmpty":      "אין קריאות",
                "infoFiltered":   "(סונן מתוך _MAX_ סהכ קריאות)",
                "infoPostFix":    "",
                "thousands":      ",",
                "lengthMenu":     "הצג _MENU_ קריאות",
                "loadingRecords": "טוען...",
                "processing":     "מעבד...",
                "search":         "חיפוש:",
                "zeroRecords":    "לא נמצאו קריאות",
                "paginate": {
                    "previous": "הקודם",
                    "next": "הבא"
                }
            }
        }
    );
//    t.on( 'order.dt search.dt', function () {
//        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
//            cell.innerHTML = i+1;
//        } );
//    } ).draw();


    var sos_time = moment().format('YYYY-MM-DD H:mm:ss');
    setInterval(function(){
        $.ajax({
            type: "GET",
            url: 'get-new-sos',
            data : "time="+sos_time,
            success: function (data) {
                console.log("ok");
                if (data.results && data.results.length>0) {
                    var mymodal = $('#sosModal');
                    var html = "<p>נמצאו קריאות לעזרה</p><ul dir=\"rtl\">";
                    for (i = 0; i < data.results.length; i++) {
                        html+="<li>תז : "+data.results[i].id+", טלפון :"+data.results[i].phone+"</li>";
                        $('#sos_table').DataTable().row.add( [
                            moment.unix(data.results[i].created_at).format("YYYY-MM-DD H:mm:ss"),
                            data.results[i].id,
                            data.results[i].phone,
                            data.results[i].latitude,
                            data.results[i].longitude
                        ]).draw( true );
                    }
                    html += "</ul>";
                    $('#sosModal-body').html(html );
                    mymodal.modal('show');

                }
                sos_time = moment().format('YYYY-MM-DD H:mm:ss');
            }
        });

     },5000);

    function initialize() {
        var map;
        var bounds = new google.maps.LatLngBounds();
        // Multiple Markers
        var markers = <?php echo json_encode( $markers ); ?>;

        // Info Window Content
        var infoWindowContent = <?php echo json_encode( $info ); ?>;

        var mapOptions = {
            mapTypeId: "roadmap",
            center: new google.maps.LatLng(32.818616,35.077310), // somewhere
            zoom: 10
        };

        // Display a map on the page
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        map.setTilt(45);

        // Display multiple markers on a map
        var infoWindow = new google.maps.InfoWindow();
        var marker, i ;
        var google_markers=[];

        // Loop through our array of markers & place each one on the map
        for (i = 0; i < markers.length; i++) {
            var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                title: markers[i][0].toString()
            });

            // Allow each marker to have an info window
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infoWindow.setContent(infoWindowContent[i][0]);
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            google_markers.push(marker);
        }

        if (markers.length > 0){
            // Automatically center the map fitting all markers on the screen
            map.fitBounds(bounds);
            //Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
                google.maps.event.removeListener(boundsListener);
            });
        }


        setInterval(function() {
            $.ajax({
                type: "GET",
                url: 'get-users-data',
                success: function (data) {
                    var zoom = map.getZoom();
                    for (i = 0; i < google_markers.length; i++) {
                        google_markers[i].setMap(null);
                    }
                    google_markers=[];
                    for (i = 0; i < data.length; i++) {
                        var position = new google.maps.LatLng(data[i].last_location[1], data[i].last_location[2]);
                        bounds.extend(position);
                        marker = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: data[i].last_location[0].toString()
                        });

                        // Allow each marker to have an info window
                        google.maps.event.addListener(marker, 'click', (function (marker, i) {
                            return function () {
                                infoWindow.setContent(data[i].html[0]);
                                infoWindow.open(map, marker);
                            }
                        })(marker, i));

                        google_markers.push(marker);
                    }
                    // Automatically center the map fitting all markers on the screen
                    map.setZoom(zoom);
                }
            });
        },10000);
    }
    google.maps.event.addDomListener(window, 'load', initialize);



</script>

@endsection