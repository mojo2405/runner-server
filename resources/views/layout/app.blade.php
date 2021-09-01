
<html>
<head>
    <title>Runner Tracker</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.css"/>

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css"/>



    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>>
    <script type="text/javascript" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>





</head>
<body>
<div class="topnav" id="myTopnav" hiden>
    <a href="admin">בית</a>
    <a href="admin-track">חיפוש</a>
    <a onclick="deleteDB()" style="background-color: tomato;color: white">מחיקת נתונים</a>
</div>

<div class="container-fluid">
    @yield('content')
</div>

<!-- Modal -->
<div id="sosModal" class="modal fade" role="dialog" dir="rtl">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="float: left">&times;</button>
                <h4 class="modal-title">מצוקה</h4>
            </div>
            <div class="modal-body" id="sosModal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
            </div>
        </div>

    </div>
</div>

</body>
</html>

<script>

    setInterval(function() {
        $.ajax({
            type: "GET",
            url: 'get-unseen-sos',
            success: function (data) {
                if (data.results && data.results.length>0) {
                    var mymodal = $('#sosModal');
                    var html = "<p>נמצאו קריאות לעזרה</p><ul dir=\"rtl\">";
                    for (i = 0; i < data.results.length; i++) {
                        html+="<li>תז : "+data.results[i].id+", טלפון :"+data.results[i].phone+"</li>";
                    }
                    html += "</ul>";
                    $('#sosModal-body').html(html );
                    mymodal.modal('show');
                }
            }
        });
    },10000);
    
    function deleteDB() {
        swal({
            title: "?האם אתה בטוח",
            text: " כל הנתונים יימחקו ולא יהיה ניתן לשחזר אותם.",
            icon: "warning",
            buttons: ["לא תודה", "בטוח"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    type: "POST",
                    url: 'truncate-all-tables',
                    success: function (data) {
                        swal("הנתונים נמחקו", {
                            icon: "success",
                        });
                    }
                });

            } else {
                swal("הנתונים לא נמחקו");
            }
    });
    }
</script>
<style>

    /* Add a black background color to the top navigation */
    .topnav {
        background-color: #333;
        overflow: hidden;
    }

    /* Style the links inside the navigation bar */
    .topnav a {
        float: right;
        display: block;
        color: #f2f2f2;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 17px;
    }

    /* Change the color of links on hover */
    .topnav a:hover {
        background-color: #ddd;
        color: black;
        cursor: pointer;
    }

    /* Add a color to the active/current link */
    .topnav a.active {
        background-color: #4CAF50;
        color: white;
    }


</style>