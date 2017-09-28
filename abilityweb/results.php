<? include 'head-tags.php';
require_once '../connect-pdo.php';





$category = $_GET['category'];
$status = $_GET['status'];
$location = $_GET['location'];
$make = $_GET['make'];
$conversion = $_GET['conversion'];
$orderby = $_GET['orderby'];
$updown = $_GET['updown'];
$search = $_GET['search'];

if(isset($_GET['search'])) {
    $searchsql = "AND searchstring LIKE '%$search%'";
}


// ResetAll
if(isset($_GET['resetall'])) {

    $_SESSION['category'] = '';
    $_SESSION['status'] = '';
    $_SESSION['locationx'] = '';
    $_SESSION['make'] = '';
    $_SESSION['conversion'] = '';
    $_SESSION['orderbyx'] = '';
    $_SESSION['updownx'] = '';

}



// Categories
$filtersql = '';
$idstring = '';
if(isset($_GET['category'])) { $_SESSION['category'] = $category; }
if($_SESSION['category'] != '') {
    $category = $_SESSION['category'];

    $sql = $mysqli->query( "SELECT vehicleid FROM vehicle_meta WHERE `type`='category' AND `value`='$category'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
}






// Locations
if(isset($_GET['location'])) { $_SESSION['locationx'] = $location; }
if($_SESSION['locationx'] != '') {
    $location = $_SESSION['locationx'];
    if($idstring!='') { $addor = " AND vehicleid IN($idstring) "; } else { $addor = ' '; }

    $sql = $mysqli->query( "SELECT vehicleid FROM vehicle_meta WHERE (`type`='location' AND `value`='$location') $addor");
    $idstring = '';
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
}





// Makes
if(isset($_GET['make'])) { $_SESSION['make'] = $make; }
if($_SESSION['make'] != '') {
    $make = $_SESSION['make'];
    if($idstring!='') { $addor = " AND vehicleid IN($idstring) "; } else { $addor = ' '; }

    $sql = $mysqli->query( "SELECT vehicleid FROM vehicle_meta WHERE `type`='make' AND `value`='$make' $addor");
    $idstring = '';
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
}



// Makes
if(isset($_GET['conversion'])) { $_SESSION['conversion'] = $conversion; }
if($_SESSION['conversion'] != '') {
    $conversion = $_SESSION['conversion'];
    if($idstring!='') { $addor = " AND vehicleid IN($idstring) "; } else { $addor = ' '; }


    $sql = $mysqli->query( "SELECT vehicleid FROM vehicle_meta WHERE `type`='conversion' AND `value`='$conversion' $addor");
    $idstring = '';
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);

}





// Status
if(isset($_GET['status'])) { $_SESSION['status'] = $status; }
if($_SESSION['status'] != '') {
    $status = $_SESSION['status'];
    if($idstring!='') { $addor = " AND vehicleid IN($idstring) "; } else { $addor = ' '; }


    if($status=='sale-pending') {
        $statussql = " AND salepending='true' AND hold!='available' ";
    }
    if($status=='on-hold') {
        $statussql = " AND hold!='available' AND salepending!='true' ";
    }

    $sql = $mysqli->query( "SELECT vehicleid FROM vehiclelookup WHERE available!='sold' $statussql ");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
} else {
    $statussql = " ";
}







if($idstring!='') {
    $meta_ids = " AND vehiclelookup.vehicleid IN($idstring) ";
} else {
    $meta_ids = " ";
}








if($var1 == 'On Hold') { $sql1 = " AND hold!='available' "; $match=true; }



//Conversions
if($var2 == 'braunability') { $sql2 = " AND conversion LIKE '%BraunAbility%' "; $conversions=true; }
if($var2 == 'vmi') { $sql2 = " AND conversion LIKE '%VMI%' "; $conversions=true; }
if($var2 == 'wheelchair-trucks') { $sql2 = " AND conversion LIKE '%SVM%' "; $conversions=true; }
if($var2 == 'full-size') { $sql2 = " AND conversion LIKE '%Full%' "; $conversions=true; }

// Specials
if($var1 == 'specials') { $sql1 = " AND specials='true' "; $specials=true; }






// ORDER BY
if(isset($_GET['orderby'])) { $_SESSION['orderbyx'] = $orderby; $_SESSION['updownx'] = $updown; }
if($_SESSION['orderbyx'] != '') {
    $orderby = $_SESSION['orderbyx'];
    $updown = $_SESSION['updownx'];
    $orderbysql = " ORDER BY $orderby $updown ";
    if($orderby == 'price') { $orderbysql = " ORDER BY price = 0, price $updown "; }
}
if($orderbysql == '') { $orderbysql =  " ORDER BY vehicleid ASC ";  }




$fl = array();
$arc = 0;
$sql = $mysqli->query( "SELECT vehicle_meta.type as type, vehicle_meta.value as value, count(vehiclelookup.vehicleid) as count
                        FROM vehicle_meta, vehiclelookup
                        WHERE vehiclelookup.available!='sold'
                        AND vehiclelookup.vehicleid=vehicle_meta.vehicleid $statussql
                        $meta_ids
                        AND vehicle_meta.type IN('category','body','make','location','certified','arrival_status','conversion','newused','conversion_new/used')
                        GROUP BY vehicle_meta.type, vehicle_meta.value ORDER BY vehicle_meta.type, vehicle_meta.value");
while($xx = $sql->fetch_assoc()) {


        $fl[$arc]['name'] = $xx['value'];
        $fl[$arc]['count'] = $xx['count'];
        $fl[$arc]['type'] = $xx['type'];
        $fl[$arc]['active'] = 'false';

    if($xx['type'] == 'make') {
        $makearray[$makearrc] = $xx['value'];
        $makearrc++;
    }
    $arc++;
}

// Count sale pending
$sql = $mysqli->query( "SELECT vehicleid FROM vehiclelookup WHERE vehiclelookup.available!='sold'  AND salepending='true' AND hold!='available' $meta_ids");
$salependingcount = $sql->num_rows;
// Count vehicles on hold
$sql = $mysqli->query( "SELECT vehicleid FROM vehiclelookup WHERE vehiclelookup.available!='sold'  AND hold!='available' AND salepending!='true' $meta_ids");
$holdcount = $sql->num_rows;

if($salependingcount == 0) {
    $salependingcount = '--';
}
if($holdcount == 0) {
    $holdcount = '--';
}

?>

<title>Inventory</title>
</head>
<script type="text/javascript">




    var meta = <? echo json_encode($fl,999999); ?>;
    console.log(meta);






    $(document).ready(function(){
        $( "#searchbutton, #searchbuttontwo" ).click(function( event ) {
            event.preventDefault();
            $( "#browes, #searching" ).toggle( "slow", function() {
                $( "#search-wheelchair-vans" ).focus();

                // Animation complete.
            });
        });


        $( "#gosearch" ).click(function( event ) {
            var searchstring = $( "#search-wheelchair-vans" ).val();
            window.location = 'results.php?search=' + searchstring;

        });


        $( "#exportbtn" ).click(function( event ) {
            $('#export_excel_submit').trigger('click');
        });

        $( "#exportusednotreadybtn" ).click(function( event ) {
            $('#export_excel_used_not_ready_submit').trigger('click');
        });


        $("#search-wheelchair-vans").keypress(function(e) {
            if(e.which == 13) {
                var searchstring = $( "#search-wheelchair-vans" ).val();
                window.location = 'results.php?search=' + searchstring;
            }
        });


    });














    $(document).ready(function(){

        function showSubmit() {
            $('#stickysubmit').slideDown(300);
        }


        $( '.holdpop' ).hover( function( event ) {
            $(this).popover('show');
        }, function( event ) {
            $(this).popover('hide');
        });


        $( '.adminnotespop' ).hover( function( event ) {
            $(this).popover('show');
        }, function( event ) {
            $(this).popover('hide');
        });




        $( '.locationjump' ).on( "click", function( event ) {
            var section = $(this).attr('title');
            $("html, body").animate({ scrollTop: $(section).offset().top - 130 }, 1000);
        });



        $( '.leadrow' ).on( "click", function( event ) {
            $( '.comments-tr' ).hide();
            $( '.leadrow' ).removeClass('nutural');
            $(this).closest('tr').next('tr').toggle();
            $(this).addClass('nutural');
        });

        $( '.closebtn' ).on( "click", function( event ) {
            $(this).closest('tr').hide();
            $(this).closest('tr').prev('tr').removeClass('nutural');
        });

        $( '.no' ).on( "click", function( event ) {
            $(this).parent().siblings('#mainlabel').removeClass();
            $(this).parent().siblings('#mainlabel').addClass('label label-default');
            showSubmit();
        });

        $( '.droprole' ).on( "change", function( event ) {
            var userid = $(this).attr('title');
            var role = $(this).val();
            showSubmit();

        });

        $(".alert").alert();
        window.setTimeout(function() { $(".alert").alert('close'); }, 6000);


        $( '.optionsbtn' ).on( "click", function( event ) {
            $('#allvehicles').slideUp(200, function( event ) {
                $('.vehicleoptions').slideDown(200);
            });

            //$(this).closest('.vehicleoptions').slideDown(900);


            //alert('fds');
        });



    });





</script>


<body>

<? include 'navbar.php'; ?>


<div class="container adminresults">


    <form id="exportexcel" action="van-manager/exportexcel_users_print.php" method="post" style="display: none">
        <input type="hidden" id="excel_vehicleids" name="excel_vehicleids">
        <button type="submit" id="export_excel_submit" style="display: none"></button>
    </form>
    <form id="exportexcelusednotready" action="van-manager/exportexcel_used_not_ready.php" method="post" style="display: none">
        <input type="hidden" id="excel_vehicleids" name="excel_vehicleids">
        <button type="submit" id="export_excel_used_not_ready_submit" style="display: none"></button>
    </form>

    <h1><span class="glyphicon glyphicon-bookmark"></span>
        <div class="pull-right hidden-xs">
            <button type="button" id="exportusednotreadybtn" class="btn btn-default"><i class="fa fa-cloud-download"></i> Export Used Not Ready</button>
            <button type="button" id="exportbtn" class="btn btn-default"><i class="fa fa-cloud-download"></i> Export To Excel</button>
        </div>
        Vehicle Inventory</h1>








    <div style="margin-bottom:20px;">

        <div id="searching" style="display:none;">
            <div class="row">

                <div class="col-xs-12">
                    <div class="input-group">
                        <input class="form-control input-lg" id="search-wheelchair-vans" type="text" placeholder="Seach by VIN (Full or Partial), Stock, Options, Color Etc...">
                        <span class="input-group-btn">
                 <button class="btn btn-default search-btn" id="gosearch" style="height:64px; margin-right:5px;" type="button">Search</button>
                 <button class="btn btn-default" id="searchbuttontwo" style="height:64px;" type="button">Cancel</button>
              </span>

                    </div><!-- /input-group -->
                </div>
            </div>
        </div>

        <div id="browes">
            <ul class="nav nav-pills" style="margin-bottom:30px;">

                <li class="pull-right active" id="searchbutton"><a href="#"><i class="fa fa-search"></i> Search</a></li>




                <?
                // The Cateogory Menu
                if($_SESSION['category'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['category'] != '') { echo ucfirst($_SESSION['category']); } else { echo 'Category'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?category=">All</a></li>
                        <?
                        $c = 0;
                        foreach($fl as $gg) {
                            if($gg['type']=='category') {
                                ?>
                                <li ><a href = "results.php?category=<? echo $gg['name']; ?>" style = "width:230px;" ><span class="badge  pull-right total" ><? echo $gg['count']; ?></span><? echo $gg['name']; ?></a></li>
                            <? } } ?>
                    </ul>
                </li>



                <?
                // The Status Menu
                if($_SESSION['status'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?> add-new-badge-before-dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['status'] != '') { echo ucwords(str_replace('-',' ',$_SESSION['status'])); } else { echo 'Status'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?status=">All</a></li>
                        <li ><a href = "results.php?status=on-hold" style = "width:230px;" ><span class="badge  pull-right total" ><? echo $holdcount;; ?></span>On Hold</a></li>
                        <li ><a href = "results.php?status=sale-pending" style = "width:230px;" ><span class="badge  pull-right total" ><? echo $salependingcount;; ?></span>Sale Pending</a></li>
                    </ul>
                </li>




                <?
                // The Location Menu
                if($_SESSION['locationx'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['locationx'] != '') { echo ucfirst($_SESSION['locationx']); } else { echo 'Location'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?location=">All</a></li>
                        <?
                        $c = 0;
                        foreach($fl as $gg) {
                            if($gg['type']=='location') {
                                ?>
                                <li ><a href = "results.php?location=<? echo $gg['name']; ?>" style = "width:230px;" ><span class="badge  pull-right total" ><? echo $gg['count']; ?></span><? echo $gg['name']; ?></a></li>
                            <? } } ?>
                    </ul>
                </li>



                <?
                // The Make Menu
                if($_SESSION['make'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['make'] != '') { echo ucfirst($_SESSION['make']); } else { echo 'Make'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?make=">All</a></li>
                        <?
                        $c = 0;
                        foreach($fl as $gg) {
                            if($gg['type']=='make') {
                                ?>
                                <li ><a href = "results.php?make=<? echo $gg['name']; ?>" style = "width:230px;" ><span class="badge  pull-right total" ><? echo $gg['count']; ?></span><? echo $gg['name']; ?></a></li>
                            <? } } ?>
                    </ul>
                </li>






                <?
                // The Conversion Menu
                if($_SESSION['conversion'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['conversion'] != '') { echo ucfirst($_SESSION['conversion']); } else { echo 'Conversion'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?conversion=">All</a></li>
                        <?
                        $c = 0;
                        foreach($fl as $gg) {
                            if($gg['type']=='conversion') {
                                ?>
                                <li ><a href = "results.php?conversion=<? echo $gg['name']; ?>" style = "width:430px;" ><span class="badge  pull-right total" ><? echo $gg['count']; ?></span><? echo $gg['name']; ?></a></li>
                            <? } } ?>
                    </ul>
                </li>






                <?
                // The Sorting Menu
                if($_SESSION['orderbyx'] != '') { $sa = 'active'; } else { $sa = ''; }  ?>
                <li class="dropdown <? echo $sa; ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <? if($_SESSION['orderbyx'] != '') { echo ucfirst($_SESSION['orderbyx']) . ' ' . ucfirst($_SESSION['updownx']); } else { echo 'Sort'; } ?> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu inventorydropdown">
                        <li><a href="results.php?orderby=">Date Added</a></li>

                        <li><a href="results.php?orderby=hold&updown=desc">On Hold</a></li>
                        <li><a href="javascript:void(0);" class="sortpricedescbtn">Price - Highest First</a></li>
                        <li><a href="javascript:void(0);" class="sortpriceascbtn">Price - Lowest First</a></li>
                        <li><a href="results.php?orderby=year&updown=desc">Year - Newest First</a></li>
                        <li><a href="results.php?orderby=year&updown=asc">Year - Oldest First</a></li>
                        <li><a href="results.php?orderby=make&updown=asc">Make - Assending</a></li>
                        <li><a href="results.php?orderby=make&updown=desc">Make - Descending</a></li>
                        <li><a href="results.php?orderby=conversion&updown=asc">Conversion - Assending</a></li>
                        <li><a href="results.php?orderby=conversion&updown=desc">Conversion - Descending</a></li>
                        <li><a href="results.php?orderby=miles&updown=asc">Miles - Lowest First</a></li>
                        <li><a href="results.php?orderby=miles&updown=desc">Miles - Highest First</a></li>
                    </ul>
                </li>

                <li>
                    <button type="button" onClick="window.location='results.php?resetall'"  class="btn btn-danger"><i class="fa fa-times-circle"></i> Reset</button>
                </li>


            </ul>

        </div> <!-- end #browes -->

    </div>

    <div class="clearfix"></div>








    <?
    $ccc = 0;
    $thesql = "SELECT vehicleid, hold, salepending FROM vehiclelookup WHERE available!='sold' $meta_ids $statussql $searchsql $orderbysql LIMIT 500";
    $sqltwow = $mysqli->query( "$thesql");
    $totalvans = $sqltwow->num_rows;
    ?>

    <div class="totalvans-count">Total Vans: <? echo $totalvans; ?></div>



    <div class="row" id="allvehicles">
        <?


        while ($deone = $sqltwow->fetch_assoc()) {
            $id = $deone['vehicleid'];
            $hold = $deone['hold'];
            $salepending = $deone['salepending'];


            $vehicleid = $id;


            // Set The Default Image If There Are None Loaded Yet.
            $thumb = '/img/novan.jpg';
            $img = mysql_query( "SELECT large FROM pictures WHERE vehicleid='$id' ORDER BY arrange ASC LIMIT 1");
            while ($img2 = mysql_fetch_array($img)) {
                $thumb = $img2['large'];
                $thumb = "/Express2.0/imageup/$thumb";
            }


            $sql22 = mysql_query( "SELECT name
												FROM locations
												WHERE name='$location'");
            $one23 = mysql_fetch_array($sql22);

            $conversion = str_replace('Chrysler/Dodge ', '', $conversion);
            $conversion = str_replace('Toyota ', '', $conversion);
            $conversion = str_replace('Honda ', '', $conversion);

            $m= array();
            $sqltwox = @mysql_query( "SELECT * FROM vehicle_meta WHERE vehicleid='$id'");
            while ($met = mysql_fetch_array($sqltwox)) {
                $m[$met['type']] = $met['value'];
            }



            $conversioncost = 0;
            $amount = 0;
            $dis = mysql_query("SELECT discountid, name, amount, arrange, vehicleid
							FROM expenses
							WHERE vehicleid='$vehicleid'
							ORDER BY arrange ASC");

            while ($feat22 = mysql_fetch_array($dis)) {
                $discountid = $feat22['discountid'];
                $vehicleid = $feat22['vehicleid'];
                $name = $feat22['name'];
                $amount = $feat22['amount'];
                $arrange = $feat22['arrange'];
                $conversioncost = ($conversioncost + $amount);



            }
            $amount = 0;


            if($hold != 'available') { $holdclass = 'vehiclehold'; } else { $holdclass='nonhold'; }
            if($salepending == 'true' && $hold != 'available') { $holdclass = 'vehiclesalepending'; }
            if($category == 'Demo') { $democlass = 'vehicledemo'; } else { $democlass=''; }

            if($category == 'New') { $spantype = 'label-primary'; }
            if($category == 'Used') { $spantype = 'label-info'; }
            if($category == 'Pre-Owned') { $spantype = 'label-success'; }
            if($category == 'Commercial') { $spantype = 'label-default'; }


            $pos = strpos($category, 'Not Ready');
            if ($pos === false) {
                $notready = false;
            } else {
                $notready = true;
                $spantype = 'label-danger';
            }

            if($category == 'On Order') { $spantype = 'label-warning'; }
            if($category == 'Customer Chassis') { $spantype = 'label-danger'; }
            if($category == 'New/Used') { $spantype = 'label-primary'; }
            if($spantype == '') { $spantype = 'label-primary'; }


            if($retailprice == '') { $retailprice= $price; }


            $ccc++;



            ?>











            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 maxheight" data-price="<? if($m['price_total_public'] == 0) { $xprice = '0'; } else { $xprice = $m['price_total_public']; } echo $xprice; ?>">
                <div class="vehiclewrap"  onClick="window.location='vehiclemain.php?vehicleid=<? echo $id; ?>'" >
                    <div class="invimgwrap">

                        <a href="vehiclemain.php?vehicleid=<? echo $id; ?>">
                            <img src="<? echo $thumb; ?>" class="img-responsive"  alt="<? echo $m['year']; ?> <? echo $m['make']; ?> <? echo $m['model']; ?>"></a></div>

                    <div  title="<? echo $id; ?>" class="invtextwrap vehicledetails">
                        <div class="baseborder"><span class="pull-right"><span class="label label-primary"><? echo $m['category']; ?></span></span><strong><? echo $m['year']; ?> <? echo $m['make']; ?></strong></div>

                        <div class="baseborder"><span class="pull-right"> <? echo $m['model']; ?> <? echo $m['trim']; ?></span>Model:</div>
                        <div class="baseborder"><span class="pull-right"> <? echo $m['miles']; ?></span>Miles:</div>
                        <div class="baseborder"><span class="pull-right"> <? echo $m['ecolor']; ?></span>Color:</div>
                        <div class="baseborder"><span class="pull-right"> <? echo $m['stock']; ?></span>Stock:</div>
                        <div class="baseborder"><span class="pull-right"> <? echo $m['location']; ?></span>Location:</div>



                        <div class="baseborder "><span class="pull-right"> <?
                                if($m['admin_notes'] != '') { ?>
                                    <a class="adminnotespop addpointer" rel="popover" data-content="<? echo $m['admin_notes']; ?>" data-original-title="Admin Notes" data-placement="top">View Notes</a>

                                    <?
                                } else {
                                    echo 'None';
                                }

                                ?></span>Admin Notes:</div>



                        <div class="baseborder <? echo $holdclass; ?>"><span class="pull-right"> <?
                                if($hold != 'available') { ?>

                                    <? if($salepending == 'true') { ?>
                                        <a class="holdpop addpointer" rel="popover" data-content="<? echo $hold; ?>" data-original-title="Sale Pending" data-placement="top">Sale Pending</a>
                                    <? } else { ?>
                                        <a class="holdpop addpointer" rel="popover" data-content="<? echo $hold; ?>" data-original-title="Hold Details" data-placement="top">On Hold</a>
                                    <? } ?>
                                    <?
                                } else {
                                    $etastamp = strtotime($m['eta']);
                                    $m['eta'] = date('m/d/y', $etastamp);

                                    if($m['arrival_status'] == 'On Order') { ?>
                                        <a class="holdpop addpointer" style="color: red; font-weight: bold;" rel="popover" data-content="<? echo $m['eta']; ?>" data-original-title="ETA" data-placement="top">On Order</a>
                                    <? } else {
                                        echo $m['arrival_status'];
                                    }

                                }

                                ?></span>Status:</div>


                        <div class="baseborder <? echo $democlass; ?>"><span class="pull-right"> <?
                                if($category == 'Demo') { ?>
                                    <a class="holdpop addpointer" rel="popover" data-content="This vehicle is currently a Demo Unit" data-original-title="Demo Vehicle" data-placement="top">DEMO VEHICLE</a>

                                    <?
                                }
                                if($category == 'Customer Chassis') { ?>
                                    <a class="holdpop addpointer" rel="popover" data-content="This vehicle is a Customer's Vehicle" data-original-title="Chustomer Chassis" data-placement="top">Customer Chassis</a>

                                    <?
                                }

                                ?></span>Other:</div>


                        <div class="baseborder"><span class="pull-right"> <?
                                if(number_format($m['price_chassis_public']) != '0') {
                                    echo '$'.number_format($m['price_chassis_public']);
                                } else { echo '--'; }
                                ?></span>Chassis:</div>
                        <div class="baseborder"><span class="pull-right"> <?
                                if(number_format($m['price_conversion_public']) != '0' && number_format($m['price_conversion_public']) != '') {
                                    echo '$'.number_format($m['price_conversion_public']);
                                } else { echo '--'; } ?></span><span style="font-size: 12px;"><? echo $m['conversion']; ?></span></div>
                        <div><span class="pull-right"> <strong><?
                                    if(number_format($m['price_total_public']) != '0') {
                                        echo '$'.number_format($m['price_total_public']);
                                    } else { echo '--'; } ?></strong></span><strong>Total:</strong></div>
                        <div><button type="button" onClick="window.location='vehiclemain.php?vehicleid=<? echo $id; ?>'" class="btn btn-default optionsbtn" style="width:100%;">Full Details/Options</button></div>

                    </div>





                </div>

            </div>


        <? } ?>



    </div>
    <div style="height: 50px;"></div>


</div>


<!-- End Div Container -->
<script type="text/javascript">

    $(document).ready(function(){

        var $vans = $('#allvehicles'),
            $vansdiv = $vans.children('div');



        $('.sortpriceascbtn').click(function() {
            $vansdiv.sort(function(a,b){
                var an = a.getAttribute('data-price'),
                    bn = b.getAttribute('data-price');
                if(an == '0') {
                    an = '99999999';
                }
                if(bn == '0') {
                    bn = '99999999';
                }
                if(an > bn) {
                    return 1;
                }
                if(an < bn) {
                    return -1;
                }
                return 0;
            });

            $vansdiv.detach().appendTo($vans);
        });


        $('.sortpricedescbtn').click(function() {
            $vansdiv.sort(function(a,b){
                var an = a.getAttribute('data-price'),
                    bn = b.getAttribute('data-price');


                if(Number(an) < Number(bn)) {
                    return 1;
                }
                if(Number(an) > Number(bn)) {
                    return -1;
                }

                return 0;

            });

            $vansdiv.detach().appendTo($vans);
        });




    });

</script>


</body>
</html>