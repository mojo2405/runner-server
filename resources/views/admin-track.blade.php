@extends('layout.app')

@section('content')
    <div class="container" style="padding:1vh">
        <div class="col-md-3 pull-right" dir="rtl">
            <form id="search" class="navbar-form" role="search">
                <label for="srch-term">הכנס פרטי תז</label>
                <div class="input-group">
                    <input class="form-control" placeholder="חיפוש..." name="srch-term" id="srch-term" type="text">
                    <div class="input-group-btn">
                    <button type="submit" class="btn btn-default" style="border-radius: 4px;margin-right: 1vh;">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="container">
        <table id="runners" class="table" dir="rtl" style="table-layout: fixed;">
            <thead>
            <tr>
                <th class="text-right" >מספר מתמודד</th>
                <th class="text-right" >תז</th>
                <th class="text-right">טלפון</th>
                <th class="text-right">פעולות</th>
            </tr>
            </thead>
            <tbody style="word-break: break-all">
            </tbody>
        </table>


        <div id="map_wrapper">
            <div id="map_canvas" class="mapping"></div>
        </div>


        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog" dir="rtl">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" style="float: left">&times;</button>
                        <h4 class="modal-title">אין תוצאות</h4>
                    </div>
                    <div class="modal-body">
                        <p>לא נמצאו תוצאות תואמות לחיפוש</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="markers-container" class="container" style="padding-top: 400px;">
        <table hidden id="markers" class="table" dir="rtl" style="table-layout: fixed;">
            <thead>
            <tr>
                <th class="text-right" >זמן</th>
                <th class="text-right" >קו אורך</th>
                <th class="text-right">קו רוחב</th>
            </tr>
            </thead>
            <tbody style="word-break: break-all">
            </tbody>
        </table>
    </div>
<style>
    #map_wrapper {
        height: 400px;
    }

    #map_canvas {
        width: 100%;
        height: 100vh;
    }


</style>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAf-6G15D16dmNUombo05NG9ceI2a5Oy0g"></script>
<script>
    $(document).ready(function(){
        $(window).scrollTop(0);
    });
    $("#search").submit(function(event){
        // cancels the form submission
        event.preventDefault();

        //Empty Table
        $("#runners > tbody").html("");
        $("#runners").hide();
        submitForm();
    });


    function submitForm (){
        // Initiate Variables With Form Content
        var search_value = $("#srch-term").val();

        $.ajax({
            type: "GET",
            url: "search-user-id",
            data: "search=" + search_value,
            success : function(data){
                if (data.results.length>0){
                    $(".table").show();
                    for (i = 0; i < data.results.length; i++) {
                        $('#runners').append('<tr><td>'+data.results[i].runner_number+'</td><td>'+data.results[i].id+'</td><td>'+data.results[i].phone+'</td><td><button onclick="mapInit(\''+data.results[i].id+'\');" type="button" class="btn btn-default">\n' +
                            '      <span class="glyphicon glyphicon-search"></span>\n' +
                            '    </button></td></tr>');
                    }
                }else{
                    $('#myModal').modal('toggle');
                }
            }
        });
    }

    function mapInit(id){
        $("#markers > tbody").html("");
        $.ajax({
            type: "GET",
            url: "get-user-route",
            data: "id=" + id,
            success : function(data){
                if (data.results && data.results.locations.length > 0){
                    function initialize() {
                        var map;
                        var bounds = new google.maps.LatLngBounds();
                        var mapOptions = {
                            mapTypeId: "roadmap",
                            center: new google.maps.LatLng(32.818616899999995, 35.0773109),
                            zoom: 3,
                        };

                        // Display a map on the page
                        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                        map.setTilt(45);

                        // Multiple Markers
                        var markers = data.results.locations;

                        // Display multiple markers on a map
                        var infoWindow = new google.maps.InfoWindow();
                        var marker, i ;
                        var google_markers=[];
                        var cordinates = [];

                        for (i = markers.length-1; i >= 0; i--) {
                            $('#markers').append('<tr><td>' + markers[i].created_at + '</td><td>' + markers[i].latitude + '</td><td>' + markers[i].longitude + '</td><</tr>');
                        }
                            // Loop through our array of markers & place each one on the map
                        for (i = 0; i < markers.length; i++) {
                            var position = new google.maps.LatLng(markers[i].latitude, markers[i].longitude);
                            cordinates.push(position);
                            bounds.extend(position);
                            if (i==0 || i+1 == markers.length){
                                var label;
                                if (i==0){//Start
                                    label="A";
                                }else{ //End
                                    label="B";
                                }
                                marker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    title: id,
                                    label:label
                                });

                            }

//                            google_markers.push(marker);
                        }

                        var runPath = new google.maps.Polyline({
                            path: cordinates,
                            geodesic: true,
                            strokeColor: '#FF0000',
                            strokeOpacity: 1.0,
                            strokeWeight: 2
                        });

                        runPath.setMap(map);

                        // Automatically center the map fitting all markers on the screen
                        map.fitBounds(bounds);
                        //Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
                        var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function (event) {
                            this.setZoom(14);
                            google.maps.event.removeListener(boundsListener);
                        });
                    }
                    initialize();
                }else{
                    $('#myModal').modal('toggle');
                }
            }
        });
    }

</script>

@endsection