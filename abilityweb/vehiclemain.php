<?

include 'head-tags.php';
require_once 'js/sendgrid/vendor/autoload.php';
$sendgrid_username  = 'factorypolaris';
$sendgrid_password  = 'J8TpSerA';

$vehicleid = $_GET['vehicleid'];
if($vehicleid == '') {
    $vehicleid = $_POST['vehicleid'];
}





if(isset($_POST['approvethehold'])) {

    $customername = mysql_real_escape_string($_POST['name']);
    $deposit = mysql_real_escape_string($_POST['deposit']);
    $theusername = mysql_real_escape_string($_POST['salesman']);
    $holdexpires = time() + ($_POST['length'] * 86400);
    $length = $_POST['length'] . ' Days';
    $comments =  mysql_real_escape_string($_POST['comments']);
    $holdtimestamp = time();

    $paytype = mysql_real_escape_string($_POST['paytype']);
    $otherpaytype = mysql_real_escape_string($_POST['otherpaytype']);
    if($paytype == 'Other') { $paytype = $otherpaytype; }

    $leadsource = mysql_real_escape_string($_POST['leadsource']);
    $otherleadsource = mysql_real_escape_string($_POST['otherleadsource']);
    if($leadsource == 'Other') { $leadsource = $otherleadsource; }

    $pendingstatus = $_POST['pending'];
    if($pendingstatus == 'Yes') {
        $holdtext = 'SALE PENDING: '.$theusername;
        $comments .= ' -- SALE PENDING OR CONSIDERED ALL BUT DELIVERED --';
        $holdexpires = time() + ($holdsalependingdays * 86400);
    } else {
        $holdtext = $theusername;

    }


    $addedorremoved = 'added';
    $extention = 'false';

//  Here we are going to insert it into the database
    mysql_query("INSERT IGNORE INTO holds SET
			vehicleid='$vehicleid',
			extention='$extention',
			extendholdnotes='-',
			extendeddays='0',
			salesman='$theusername',
			holdstart='$holdtimestamp',
			holdexpires='$holdexpires',
			holdnotes='$comments',
			holddays='$length',
			deposit='$deposit',
			holdremovedby='Active',
			paytype='$paytype',
			customer='$customername',
			leadsource='$leadsource',
			addedorremoved='$addedorremoved'");
    $lastid = mysql_insert_id();



    mysql_query( "UPDATE vehiclelookup SET
					hold='$holdtext', paytype='$paytype', deposit='$deposit', leadsource='$leadsource', holdstarttimestamp='$holdtimestamp', holdcomments='$comments', holdlength='$length', holduserid='0', holdexpires='$holdexpires', customer='$customername'
					WHERE vehicleid='$vehicleid'");


    $commentswithlink = '';



    $tempFile = $_FILES['buyersorder']['tmp_name'];
    $fileSize = $_FILES['buyersorder']['size'];
    $filename = 'buyers_order_'.$lastid.".pdf";


    move_uploaded_file($tempFile, "../dealeruploads/$filename");
    $commentswithlink = $comments.'<br>'.'<a href="http://'.$domainname.'/dealeruploads/'.$filename.'" target="_blank">Download Buyers Order</a>';




    mysql_query( "UPDATE holds SET
					holdnotes='$commentswithlink'
					WHERE id='$lastid'");

    mysql_query( "UPDATE vehiclelookup SET
					holdcomments='$commentswithlink'
					WHERE vehicleid='$vehicleid'");






// Email the lead notification.  We use the Fsocket so it runs in the background and retuns the page instantly.
    $host = "www.$domainname";
    $fp = fsockopen($host, 80, $errno, $errstr, 10);
    if (!$fp) {
        echo "$errstr ($errno)\n";
    } else {

        $header = "GET /abilityweb/_mailer.php?newvanonhold=true&vehicleid=$vehicleid HTTP/1.1\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Connection: close\r\n\r\n";
        fputs($fp, $header);
        fclose($fp);
    }



} // End Post





$sql = mysql_query( "SELECT *
					FROM vehiclelookup
					WHERE vehicleid='$vehicleid;'");

while ($one = mysql_fetch_array($sql)) {
    $vin = htmlspecialchars($one['vin']);
    $status = htmlspecialchars($one['available']);
    $vehicaleid = htmlspecialchars($one['vehicleid']);
    $stock = htmlspecialchars($one['stock']);
    $icolor = htmlspecialchars($one['icolor']);
    $ecolor = htmlspecialchars($one['ecolor']);
    $specials = htmlspecialchars($one['specials']);
    $hold = htmlspecialchars($one['hold']);
    $salepending = htmlspecialchars($one['salepending']);
    $make = htmlspecialchars($one['make']);
    $trim = htmlspecialchars($one['trim']);
    $year = htmlspecialchars($one['year']);
    $model = htmlspecialchars($one['model']);
    $engine = htmlspecialchars($one['engine']);
    $miles = htmlspecialchars($one['miles']);
    $description = $one['description'];
    $location = htmlspecialchars($one['location']);
    $warranty = htmlspecialchars($one['warranty']);
    $conversionwarranty = htmlspecialchars($one['conversionwarranty']);
    $body = htmlspecialchars($one['body']);
    $transmission = htmlspecialchars($one['transmission']);
    $drivetrain = htmlspecialchars($one['drivetrain']);
    $filedir = htmlspecialchars($one['filedir']);
    $conversion = htmlspecialchars($one['conversion']);
    $price = htmlspecialchars($one['price']);
    $filename = htmlspecialchars($one['filename']);
    $category = htmlspecialchars($one['category']);
    $categoryx = $category;
    $hitcounter = number_format($one['hitcounter']);
    $fueleconomycity = htmlspecialchars($one['fueleconomycity']);
    $fueleconomyhwy = htmlspecialchars($one['fueleconomyhwy']);
    $msrp = htmlspecialchars($one['msrp']);
    $listdate = htmlspecialchars($one['listdate']);
    $adminnotes = htmlspecialchars($one['adminnotes']);
    $newused = htmlspecialchars($one['newused']);
    $soldsalesman = htmlspecialchars($one['soldsalesman']);
    $soldtimestamp = htmlspecialchars($one['soldtimestamp']);
    $soldcustomer = htmlspecialchars(ucwords(strtolower($one['soldcustomer'])));
    $holduserid = htmlspecialchars($one['holduserid']);
    $customer = htmlspecialchars($one['customer']);
    $notreadyeta = htmlspecialchars($one['notreadyeta']);
    $conversionnewused = htmlspecialchars($one['conversionnewused']);
    $youtube = $one['youtube'];
    $paytype = $one['paytype'];
    $leadsource = $one['leadsource'];
    $holdcomments = $one['holdcomments'];
    $holdstarttimestamp = $one['holdstarttimestamp'];
    $holdexpires = $one['holdexpires'];
    $deposit = $one['deposit'];
    $length = $one['holdlength'];
    $customer = $one['customer'];
    $holdremovedby = $one['holdremovedby'];
    $paytype = $one['paytype'];
    $leadsource = $one['leadsource'];
    $standardoptions = $one['standard_options'];
    $optionaloptions = $one['optional_options'];
    $holdremovedcomments = $one['holdremovedcomments'];
    $holdstarttimestamp = $one['holdstarttimestamp'];


    $secondsleft = $holdexpires - time();
    $daysleft = round($secondsleft / 86400);
    if($daysleft < 0) { $daysleft = 'Expired'; }

    $startdate = date('l F jS', $holdstarttimestamp);
    $enddate = date('l F jS', $holdexpires);



    $m= array();
    $sqltwox = @mysql_query( "SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
    while ($met = mysql_fetch_array($sqltwox)) {
        $m[$met['type']] = $met['value'];
    }





    $youtube = substr("$youtube", -11);
    $miles = number_format($miles);
    $sql22 = mysql_query( "SELECT name
						FROM locations
						WHERE name='$location'");
    $one23 = mysql_fetch_array($sql22);
    $stateab = strtoupper(substr($one23['name'],0,2));
    $stock = $stateab . $stock;

    $sql2 = mysql_query( "SELECT logo
						FROM conversions
						WHERE name='$conversion'");


    while ($one2 = mysql_fetch_array($sql2)) {
        $clogo = htmlspecialchars($one2['logo']);
        if($clogo == '') {
            $clogo = 'noConversion.jpg';
        }
        $conversionlogo = "$imagepath/$clogo";
    }



    // Set The Default Image If There Are None Loaded Yet.
    $thumb = '/Express2.0/imageup/novan.jpg';
    $large = '/Express2.0/imageup/novan.jpg';

    $img = mysql_query( "SELECT large FROM pictures WHERE vehicleid='$vehicleid' ORDER BY arrange ASC LIMIT 1");
    while ($img2 = mysql_fetch_array($img)) {
        $thumb = $img2['large'];
        $thumb = "/Express2.0/imageup/$thumb";
    }

    $imgs = mysql_query( "SELECT large FROM vans_pictures WHERE vehicleid='$vehicleid'");
    $totalimages = mysql_num_rows($imgs);




    $conversioncost = 0;
    $amount = 0;
    $dis = mysql_query("SELECT discountid, name, amount, arrange, vehicleid
							FROM expenses
							WHERE vehicleid='$vehicleid'
							ORDER BY arrange ASC");

    while ($feat22 = mysql_fetch_array($dis)) {
        $discountid = $feat22['discountid'];
        $name = $feat22['name'];
        $amount = $feat22['amount'];
        $arrange = $feat22['arrange'];
        $conversioncost = ($conversioncost + $amount);



    }
    $amount = 0;

    $diser = mysql_query("SELECT nada, baseprice, price, msrp, dealerbaseprice, dealerprice, qte, refer, retailprice
							FROM vehiclelookup
							WHERE vehicleid='$vehicleid' LIMIT 1");

    $feat22 = mysql_fetch_array($diser);
    $nada = $feat22['nada'];
    $qte = $feat22['qte'];
    $retailprice = $feat22['retailprice'];
    $refer = $feat22['refer'];
    $baseprice = $feat22['baseprice'];
    $dealerbaseprice = $feat22['dealerbaseprice'];
    $msrp = $feat22['msrp'];
    $dealerbaseprice = $feat22['dealerbaseprice'];
    $dealerprice = $feat22['dealerprice'];

    $price = ($baseprice - $totaldiscounts);
    $retailprice = ($dealerbaseprice + $conversioncost);

    $totaldiscounts = "$" . number_format("$totaldiscounts", "0", ".", ",");
    $price = "$" . number_format("$price", "0", ".", ","); if($price == "$0") $price = '';
    $retailprice = "$" . number_format("$retailprice", "0", ".", ","); if($retailprice == "$0") $retailprice = '';
    $msrp = "$" . number_format("$msrp", "0", ".", ","); if($msrp == "$0") $msrp = '';
    $nada = "$" . number_format("$nada", "0", ".", ","); if($nada == "$0") $nada = '';
    $baseprice = "$" . number_format("$baseprice", "0", ".", ","); if($baseprice == "$0") { $baseprice = ''; }
    $dealerbaseprice = "$" . number_format("$dealerbaseprice", "0", ".", ","); if($dealerbaseprice == "$0") { $dealerbaseprice = ''; }
    $dealercost = "$" . number_format("$dealercost", "0", ".", ","); if($dealercost == "$0") $dealercost = '';
    $totalexpenses = "$" . number_format("$totalexpenses", "0", ".", ",");

    $conversioncost = "$" . number_format("$conversioncost", "0", ".", ","); if($conversioncost == "$0") $conversioncost = '';


    if($retailprice == '') { $retailprice= $price; }

    $pricenoformat = str_replace(',','',$retailprice);
    $pricenoformat = str_replace('$','',$pricenoformat);





    //$price = '45000';

    $tenpercent = $pricenoformat * .1;
    $pricedown = $pricenoformat - $tenpercent;

    // Calculate 36 Months Payments
    $factor = .0314980;
    $thirtysixmonths = ($pricedown * $factor);
    $thirtysixmonths = number_format($thirtysixmonths);

    // Calculate 48 Months Payments
    $factor = .0245775;
    $fortyeightmonths = ($pricedown * $factor);
    $fortyeightmonths = number_format($fortyeightmonths);

    // Calculate 60 Months Payments
    $factor = .0205408;
    $sixtymonths = ($pricedown * $factor);
    $sixtymonths = number_format($sixtymonths);

    // Calculate 72 Months Payments
    $factor = .0179018;
    $seventytwomonths = ($pricedown * $factor);
    $seventytwomonths = number_format($seventytwomonths);



}



?>

<title>Admin | <? echo "$year $make $model - $stock"; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!--[if lt IE 9]>
<div style='text-align:center'><a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." /></a></div>
<link rel="stylesheet" href="assets/tm/css/tm_docs.css" type="text/css" media="screen">
<script src="assets/assets/js/html5shiv.js"></script>
<script src="assets/assets/js/respond.min.js"></script>
<![endif]-->


<!-- Needed FIles For Image Uploader -->
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" />
<link rel="stylesheet" href="plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>





<script type="text/javascript">



    function iniafterajax(){
        //alert('loaded');
        $('.layoutimage').click(function() {

            var url = $(this).attr("src");

            $('#largelayout').attr('src', url);
        });


        $('.thethumbnails').click(function() {
            var rrr = $(this).attr('data-imgnumber');
            $('#currentimage').html(rrr);

        });







    };





    $(document).ready(function(){





        $(document).on({
            mouseenter: function () {

            },
            mouseleave: function () {
                //stuff to do on mouse leave
            },
            click: function() {
                var rid = $( this ).attr('id');
                //alert(rid);
                <? if($permissions_uploadpics == 'true') { $showremovebtn = 'true'; } else { $showremovebtn = 'false'; } ?>
                $('#sortable').load('ajax/images.php?getimages=true&vehicleid=<? echo $vehicleid; ?>&showremove=<? echo $showremovebtn; ?>&removeimage=true&imageid=' + rid,'', iniafterajax());
            }

        }, ".deleteimagebtn"); //pass the element as an argument to .on



        function hidetools() {
            $( "#holdrequest" ).hide( );
            $( "#imageuploads" ).hide(  );
            $( "#soldrequest" ).hide(  );
            $( "#removeholdrequest" ).hide(  );
            $( ".admindivs" ).hide(  );


        }

        $( "#paytype" ).change(function() {
            var v = $( '#paytype option:selected').text();

            if(v == 'Other') {

                $( ".paytypeicon" ).show(100);
                $( "#otherpaytype" ).show(200);
                $('#otherpaytype').animate({backgroundColor:'#ed1c24', borderLeft:'5px solid #ed1c24'}, 300, function() {
                    $('#otherpaytype').animate({backgroundColor:'white', borderLeftColor:'#ed1c24', borderLeftWidth:'15px'}, 300);

                });
            } else {
                $( ".paytypeicon" ).hide();
                $( "#otherpaytype" ).hide();
            }
        });





        $( "#leadsource" ).change(function() {
            var v = $( '#leadsource option:selected').text();

            if(v == 'Other') {

                $( ".leadsourceicon" ).show(100);
                $( "#otherleadsource" ).show(200);
                $('#otherleadsource').animate({backgroundColor:'#ed1c24', borderLeft:'5px solid #ed1c24'}, 300, function() {
                    $('#otherleadsource').animate({backgroundColor:'white', borderLeftColor:'#ed1c24', borderLeftWidth:'15px'}, 300);

                });
            } else {
                $( ".leadsourceicon" ).hide();
                $( "#otherleadsource" ).hide();
            }
        });


        (function( $ ){
            $.fn.hidetoolsx = function() {
                $( "#holdrequest" ).hide( );
                $( "#imageuploads" ).hide(  );
                $( "#soldrequest" ).hide(  );
                $( "#removeholdrequest" ).hide(  );
                $( ".adminbtns" ).hide(  );
                return this;
            };
        })( jQuery );



        $( ".hidealltools" ).click(function() {
            hidetools();
        });



        $( "#uploadimagesbtn" ).click(function() {
            hidetools();
            $( "#imageuploads" ).slideDown( 500 );
        });


        $( "#holdrequestbtn" ).click(function() {
            hidetools();
            $( "#holdrequest" ).slideDown( 500 );
        });


        $( "#removeholdrequestbtn" ).click(function() {
            hidetools();
            $( "#removeholdrequest" ).slideDown( 500 );
        });

        $( "#soldrequestbtn" ).click(function() {
            hidetools();
            $( "#soldrequest" ).slideDown( 500 );
        });


        <? if($permissions_uploadpics == 'true') { $showremovebtn = 'true'; } else { $showremovebtn = 'false'; } ?>
        $('#sortable').load('ajax/images.php?getimages=true&showremove=<? echo $showremovebtn; ?>&vehicleid=<? echo $vehicleid; ?>','', iniafterajax());







        // Manage images




        $('#gotoimages').click(function() {

            $("html, body").animate({ scrollTop: $('#imagessection').offset().top }, 1000);


        });

        $('#nextimage').click(function() {

            var nextimagenumber = Number($('#currentimage').html()) + 1;
            if(nextimagenumber > Number($('#totalimages').html())) {
                nextimagenumber = 1;
            }
            var newurl = $( "[data-imgnumber='"+nextimagenumber+"']" ).attr('src');



            $('#largelayout').attr('src', newurl);
            $('#currentimage').html(nextimagenumber);

            var natwidth = $('#largelayout').prop('naturalWidth') + 30;
            $('.modal-dialog').css( "width", natwidth+"px" );
            //alert(natwidth);

        });


        $('#previmage').click(function() {

            var nextimagenumber = Number($('#currentimage').html()) - 1;
            if(nextimagenumber < 1) {
                nextimagenumber = $('#totalimages').html();
            }
            var newurl = $( "[data-imgnumber='"+nextimagenumber+"']" ).attr('src');
            $('#largelayout').attr('src', newurl);
            $('#currentimage').html(nextimagenumber);

            var natwidth = $('#largelayout').prop('naturalWidth') + 30;
            $('.modal-dialog').css( "width", natwidth+"px" );

        });

        $('#mainimage').click(function() {

            $('#currentimage').html('1');

        });






        $( "#rightbtn" ).click(function() {



            if(curimage < (totimage -2)) {
                curimage++;
                $( "#rightbtn" ).removeClass('fa fa-chevron-circle-right');
                $( "#rightbtn" ).addClass('rightactive fa fa-chevron-circle-right');
                $( "#thumbcontainer" ).animate({ "margin-left": "-=150px" }, "slow" );
            }

            if(curimage > (totimage - 3)) {
                $( "#rightbtn" ).removeClass('fa fa-chevron-circle-right');
                $( "#rightbtn" ).addClass('rightnonactive fa fa-chevron-circle-right');

            }

            if(curimage > 1) {
                $( "#leftbtn" ).removeClass('fa fa-chevron-circle-left');
                $( "#leftbtn" ).addClass('leftactive fa fa-chevron-circle-left');

            }
        });



        $( "#leftbtn" ).click(function() {

            if(curimage > 1) {
                $( "#leftbtn" ).removeClass('fa fa-chevron-circle-left');
                $( "#leftbtn" ).addClass('leftactive fa fa-chevron-circle-left');
                $( "#thumbcontainer" ).animate({ "margin-left": "+=150px" }, "slow" );
                curimage--;
            }

            if(curimage == 1) {
                $( "#leftbtn" ).removeClass('fa fa-chevron-circle-left');
                $( "#leftbtn" ).addClass('leftnonactive fa fa-chevron-circle-left');

            }
            if(curimage < (totimage)) {
                $( "#rightbtn" ).removeClass('fa fa-chevron-circle-right');
                $( "#rightbtn" ).addClass('rightactive fa fa-chevron-circle-right');

            }

        });




        var curimage = 1;
        var totimage = Number($('#totalimages').html());





        var natwidth = $('#mainimage').prop('naturalWidth') + 30;
        $('.modal-dialog').css( "width", natwidth+"px" );


        // End Images
        <? if($permissions_uploadpics == 'true') { ?>
        // Image sorting
        $('#sortable').sortable({
            update: function (event, ui) {
                var result = $(this).sortable('toArray').toString();

                // POST to server using $.post or $.ajax

                $.ajax({
                    data: {'newids' : result},
                    type: 'POST',
                    success: function() {
                        $('#sortable').load('ajax/images.php?getimages=true&vehicleid=<? echo $vehicleid; ?>','', iniafterajax());

                    },
                    url: 'ajax/images.php'
                });

            }
        });

        <? } ?>


        <? if($permissions_uploadpics != 'true') { ?>
        $('#uploadimagesbtn').hide();
        <? } ?>









        var dep_h = '';
        var holdwithdeposit = false;
        $(".dep_radio").click(function() {
            if($(this).val() == 'Yes') {
                holdwithdeposit = true;
                $('.deposit_input').slideDown(200);
                $('#length').val('<? echo $holddayswithdeposit; ?>');
                $(".dep_glowicon" ).show(100);
                $("#otherdep_glow" ).show(200);
                $('#otherdep_glow').animate({backgroundColor:'#ed1c24', borderLeft:'5px solid #ed1c24'}, 300, function() {
                    $('#otherdep_glow').animate({backgroundColor:'white', borderLeftColor:'#ed1c24', borderLeftWidth:'15px'}, 300);
                });
            } else {
                holdwithdeposit = false;
                $('.deposit_input').slideUp(200);
                $('#length').val('<? echo $holddayswithoutdeposit; ?>');
                $( ".dep_glowicon" ).hide();
                $( "#otherdep_glow" ).hide();
            }
            <? if($showmovevehicleoption == true) { ?>
            $("#locationmoveradios").show();
            <? } ?>

        });




        $(".mov_yes").click(function() {
            if(holdwithdeposit == true) {
                $('#length').val('<? echo $holddayswithdeposit_plusmove; ?>');
            } else {
                $('#length').val('<? echo $holddayswithdeposit_plusmove; ?>');
            }


        });

        $(".mov_no").click(function() {
            if(holdwithdeposit == true) {
                $('#length').val('<? echo $holddayswithdeposit_nomove; ?>');
            } else {
                $('#length').val('<? echo $holddayswithoutdeposit_nomove; ?>');
            }



        });


        $(".pending_yes").click(function() {
            $('#pendingstatus').val('Sale Pending');
            $('#length').val('<? echo $holdsalependingdays; ?>');

        });

        $(".pending_no").click(function() {
            $('#pendingstatus').val('');

            $('.dep_radio').each(function() {
                if($(this).is(':checked'))
                { $(this).trigger('click');  }
            });

            $('.mov_no').each(function() {
                if($(this).is(':checked'))
                { $(this).trigger('click');   }
            });

            $('.mov_yes').each(function() {
                if($(this).is(':checked'))
                { $(this).trigger('click');   }
            });


        });







    });










    function holdhistory() {

        $("html, body").animate({ scrollTop: $('#holdhistory').offset().top - 80 }, 1000);


    }

    function goback() {
        history.back();
    }




    function video() {

        $("html, body").animate({ scrollTop: $('#videosection').offset().top }, 1000);


    }
</script>
</head>
<body onLoad="<? if(isset($_GET['history'])) { ?>

    holdhistory();

<? } ?>">
<? include 'navbar.php'; ?>
<? if($_SESSION['name'] == '') { include 'userlogin.php'; die; }


?>

<div></div></div>
<div class="container">





    <?
    if(isset($_POST['marksold']) && $status != 'sold') {
        $customername = mysql_real_escape_string($_POST['customername']);
        $whosold = mysql_real_escape_string($_POST['salesmansold']);
        $soldcomments = mysql_real_escape_string($_POST['soldcomments']);


        mysql_query( "UPDATE vehiclelookup SET
					soldsalesman='$whosold', soldcomments='$soldcomments', soldcustomer='$customername' WHERE vehicleid='$vehicleid'");

        ?>

        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>NICE WORK <? echo $whosold; ?>!</strong> The sold request has been submitted.  Watch your email for a notice when approved.</div>

        <?

        $body = "
<h1>Sold Request Submitted</h1>
<h3 style=\"color:red\">Do not reply to this email. It will not go to the sales person.</h3>


<h2>Vehicle Sale Details</h2>
<p>Customer Name (Sold To): $customername</p>
<p>Sales Person: $whosold</p>
<p>Vehicle: $year $make $model</p>
<p>Location: $location</p>
<p>Stock: $stock</p>
<p>Comments: $soldcomments</p>
<br>

<h2>To Approve This SALE Visit The Link Below</h2>
<p>Clicking the link below will REMOVE the vehicle from the website!</p>
<p><a href=\"http://www.$domainname/abilityweb/vehiclemain.php?vehicleid=$vehicleid&approvesale=1\" target=\"_blank\">APPROVE THE SALE</a></p>

<br>
<h2>Thanks For Choosing AbilityWeb!</h2>
";
        $plaintext = strip_tags($body);


//$leadnotificationsemail = 'chris@abilityweb.net';

        logActivity('Email', "Admin Inventory Managers", "sold-admin@abilityweb.net", "Sold Vehicle Approval Needed", "Inv. Admin");


        $sendgrid = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));
        $email    = new SendGrid\Email();
        $email->setTos($admin_approval_emails)->
        setFrom('sold-admin@dealerexpress.net')->
        setFromName('Mobility Plus')->
        setReplyTo('no-reply@dealerexpress.net')->
        setSubject('Sold Vehicle Approval Needed')->
        setHtml($body)->
        setText(strip_tags($plaintext))->
        addHeader('X-Sent-Using', 'SendGrid-API')->
        addHeader('X-Transport', 'web');
        $response = $sendgrid->send($email);




        ?>

    <? } // End Post ?>






    <?
    if(isset($_GET['approvesale']) && $status != 'sold') {
        $xdate = time();

        mysql_query( "UPDATE vehiclelookup SET
					available='sold',
					soldtimestamp='$xdate'
					WHERE vehicleid='$vehicleid' LIMIT 1");

        mysql_query( "INSERT IGNORE INTO soldlookup SET
					soldamount='Unknown',
					vehicleid='$vehicleid',
					solddate='$xdate'");
        ?>

        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>The vehicle has been marked sold. </strong> Thank You.  You can always move this vehicle back into available at anytime.</div>

        <?

// Email the lead notification.  We use the Fsocket so it runs in the background and retuns the page instantly.
        $host = "www.$domainname";
        $fp = fsockopen($host, 80, $errno, $errstr, 10);
        if (!$fp) {
            echo "$errstr ($errno)\n";
        } else {

            $header = "GET /abilityweb/_mailer.php?soldvehicle=true&vehicleid=$vehicleid HTTP/1.1\r\n";
            $header .= "Host: $host\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header);
            fclose($fp);
        }



    } // End Post ?>





    <?
    if(isset($_POST['approvethehold'])) {
        $theusername = mysql_real_escape_string($_POST['salesman']);
        $length = $_POST['length'] . ' Days';

        ?>
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>The vehicle has been put on hold for <? echo $theusername; ?> - Time: <? echo $length; ?></strong> Thank You.</div>
        <?
    } // End Post ?>






    <?
    if(isset($_POST['notreadynote'])) {
        $notecomments = mysql_real_escape_string($_POST['notecomments']);
        $notetimestamp = time();
        $author = $_SESSION['name'];


        mysql_query("INSERT IGNORE INTO notreadynotes SET
			vehicleid='$vehicleid',
			timestamp='$notetimestamp',
			name='$author',
			comments='$notecomments'");

        ?>


        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Vehicle Not Ready Comments Saved.  </strong> Thank You.</div>
        <?
    } // End Post ?>








    <?
    if(isset($_POST['removethehold'])) {
        $customername = mysql_real_escape_string($_POST['customername']);
        $holdremovedby = mysql_real_escape_string($_POST['holdremovedby']);
        $holdremovedcomments = mysql_real_escape_string($_POST['holdremovedcomments']);





        $rhs =  "UPDATE vehiclelookup SET
					customer='$customername', holdremovedby='$holdremovedby', holdexpires='0', holdremovedcomments='$holdremovedcomments', hold='available'
					WHERE vehicleid='$vehicleid'";
        mysql_query($rhs);




        $nowstamp = time();

        $addedorremoved = 'removed';
        $extention = 'false';
//  Here we are going to insert it into the database
        mysql_query("INSERT IGNORE INTO holds SET
			vehicleid='$vehicleid', 
			extention='$extention', 
			extendholdnotes='-', 
			extendeddays='0', 
			salesman='$hold', 
			customer='$customername', 
			holdstart='$nowstamp', 
			holdexpires='$holdexpires', 
			holdnotes='$holdremovedcomments', 
			holddays='$length', 
			deposit='$deposit', 
			holdremovedby='$holdremovedby', 
			paytype='$paytype', 
			leadsource='$leadsource', 
			addedorremoved='$addedorremoved'");


        ?>

        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Better luck next time... Until then, "Vehicle Hold Removed". An email has been sent to everyone. </strong> Thank You.</div>

        <?

// Email the lead notification.  We use the Fsocket so it runs in the background and retuns the page instantly.
        $host = "www.$domainname";
        $fp = fsockopen($host, 80, $errno, $errstr, 10);
        if (!$fp) {
            echo "$errstr ($errno)\n";
        } else {

            $header = "GET /abilityweb/_mailer.php?removehold=true&vehicleid=$vehicleid HTTP/1.1\r\n";
            $header .= "Host: $host\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header);
            fclose($fp);
        }
        $hold = 'available';

    } // End Post ?>









    <? if($status == 'sold') { ?>
        <div class="jumbotron" style="text-align:center;">
            <h1><i class="fa fa-exclamation-triangle"></i></h1>
            <p>This vehicle is already sold!  It was sold on: <? echo date('F jS Y') . " by $soldsalesman to $soldcustomer"; ?></p>
            <p></p>
        </div>

    <? } ?>

    <? if($m['arrival_status'] == 'On Order') { ?>
        <div class="alert alert-danger" style="margin-bottom:20px;"><i class="fa fa-warning"></i> Vehicle On Order.  ETA: <? echo $m['eta']; ?>
        </div>

    <? } ?>



    <? if($hold != 'available') {
        if($salepending=='true') { ?>
            <div class="alert alert-danger salepending"><i class="fa fa-lock"></i> SALE PENDING. Details: <? echo $hold; ?>.</div>
        <? } else { ?>
            <div class="alert alert-danger hold"><i class="fa fa-lock"></i> VEHICLE ON HOLD.  Hold Details: <? echo $hold; ?>.</div>
        <? } ?>

        <div class="hidden-print <? if($salepending=='true') { echo 'salepending-wrapper'; } else { echo 'hold-wrapper';  }?>">
            <h3><? if($salepending=='true') { echo 'About The Pending Sale'; } else { echo 'About The Hold';  }?></h3>
            <div class="row">
                <div class="col-sm-4">
                    <strong>Salesperson:</strong> <? echo $hold; ?><br>
                    <strong>Customer:</strong> <? echo $customer; ?><br>
                    <strong>Deposit:</strong> $<? echo str_replace('$', '', $deposit); ?><br>
                    <strong>Paytype:</strong> <? echo $paytype; ?><br>
                </div>


                <div class="col-sm-4">
                    <strong>Hold Start:</strong> <? echo $startdate; ?><br>
                    <strong>Hold End:</strong> <? echo $enddate; ?><br>
                    <strong>Days Remaining:</strong> <? echo $daysleft; ?><br>
                    <strong>Vehicle Hold History:</strong> <a href="#" onClick="holdhistory()" id="viewholdhistory">View</a><br>
                </div>


                <div class="col-sm-4">
                    <strong>Comments:</strong><br>
                    <?
                    $cshow = false;
                    if($_SESSION['role'] == 'SuperAdmin') {
                        echo $holdcomments;
                        $cshow = true;
                    }
                    if($_SESSION['name'] == $hold && $cshow==false) {
                        echo $holdcomments;
                        $cshow = true;
                    }
                    if($cshow == false ) {
                        echo str_replace('Download Buyers Order','',strip_tags($holdcomments));

                    }

                    ?>
                </div>

            </div>
        </div>


    <? } ?>

    <? if($specials == 'true') { ?>
        <div class="alert alert-primary"><i class="fa fa-tag"></i> Great News! This Vehicle Is On Special/Hot List.
        </div>
    <? } ?>



    <?  if($categoryx == 'Demo') { ?>
        <div class="alert alert-info"><strong><i class="fa fa-tag"></i> Demo Vehicle</strong><br>This means the milage for the vehicle may not be correctly listed and pricing may vary.
        </div>
    <? } ?>

    <h1 class="large-on-print">
        <div style="float:right;"><button type="button" onClick="goback()" class="btn btn-default hidden-print"><i class="fa fa-arrow-circle-left"></i> &nbsp;Back</button></a></div>
        <? echo "$year $make $model " .$m['trim']; ?></h1>

    <img src="<? echo $thumb; ?>" class="main-image-print print-only">
    <div class="row">
        <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
            <? if($categoryx == 'Used Not Ready') { ?>
                <div class="rowAll"><div class="rowTitle">Category:</div><span class="rowFloat"><span class="label label-warning"><i class="fa fa-exclamation-triangle"></i>
                            <? echo $categoryx; ?> <?




                            ?></span></span></div>
            <? } else { ?>
                <div class="rowAll"><div class="rowTitle">Category:</div><span class="rowFloat"><? echo $categoryx; ?></span></div>
            <? } ?>


            <div class="rowAll"><div class="rowTitle">Status</div><span class="rowFloat"><?



                    $etastamp = strtotime($m['eta']);
                    $m['eta'] = date('m/d/y', $etastamp);

                    if($m['arrival_status'] == 'On Order') { ?>
                        <span style="color: red; font-weight: bold;" >On Order - ETA <? echo $m['eta']; ?></span>
                    <? } else {
                        echo $m['arrival_status'];
                    }





                    ?></span></div>

            <div class="rowAll"><div class="rowTitle">VIN:</div><span class="rowFloat"><? echo $vin; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Location:</div><span class="rowFloat"><? echo $location; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Conversion:</div><span class="rowFloat"><? echo $m['conversion']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Chassis Age:</div><span class="rowFloat">
						 <? if(strtolower($m['newused']) == 'new') {   ?>
                             Brand New Chassis
                         <? } else { ?>
                             Pre-Owned Chassis
                         <? } ?>
                         </span></div>


            <div class="rowAll"><div class="rowTitle">Conversion Age:</div><span class="rowFloat">
						 <? if(strtolower($m['conversion_newused']) == 'new') { ?>
                             Brand New Conversion
                         <? } else { ?>
                             Pre-Owned Conversion
                         <? } ?>
                         </span></div>

            <div class="rowAll"><div class="rowTitle">Stock:</div><span class="rowFloat"><? echo $m['stock']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Miles:</div><span class="rowFloat"><? echo $m['miles']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Exterior:</div><span class="rowFloat"><? echo $m['ecolor']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Interior:</div><span class="rowFloat"><? echo $m['icolor']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Engine:</div><span class="rowFloat"><? echo $m['engine']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Body:</div><span class="rowFloat"><? echo $m['body']; ?></span></div>
            <div class="rowAll"><div class="rowTitle">Date Listed:</div><span class="rowFloat"><? echo date('m/d/Y',$listdate); ?></span></div>
            <div class="rowAll"><div class="rowTitle">Hitcounter:</div><span class="rowFloat"><? echo $hitcounter; ?></span></div>
        </div>



        <?
        $img = mysql_query( "SELECT large FROM pictures WHERE vehicleid='$vehicleid' ORDER BY arrange ASC");
        $totalimages = mysql_num_rows($img);
        if($totalimages != 0) {
            ?>


            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 hidden-print" >

                <a href="#" data-toggle="modal" data-target="#bigimage"><img src="<? echo $thumb; ?>" id="mainimage"  data-imgnumber="<? echo $icounter; ?>" class="img-responsive layoutimage"></a>


                <div class="hidden-xs">
                    <div class="col-xs-1">
                        <div><i class="fa fa-chevron-circle-left leftactive lrbuttonsimg" id="leftbtn" ></i></div>
                    </div>


                    <div cl class="col-xs-10">
                        <div style="width:450px; height:105px; margin-top:5px; margin-left:-5px; overflow:hidden">
                            <div style="width:5000px;" id="thumbcontainer">
                                <?

                                $icounter = 1;
                                while ($img2 = mysql_fetch_array($img)) {
                                    $thumba = $img2['large'];
                                    $thumba = "/Express2.0/imageup/$thumba";

                                    ?>
                                    <div style="float:left; margin:0 5px 0 5px;">
                                        <a href="#" data-toggle="modal" data-target="#bigimage"><img src="<? echo $thumba; ?>" id="thumbnail" data-imgnumber="<? echo $icounter; ?>" class="img-responsive layoutimage thethumbnails" width="140px;"></a>
                                    </div>

                                    <?
                                    $icounter++;
                                }

                                ?>
                            </div>
                        </div>
                    </div>


                    <div class="col-xs-1">
                        <div><i class="fa fa-chevron-circle-right rightactive lrbuttonsimg" id="rightbtn"></i></div>
                    </div>
                </div>





                <!-- Modal -->
                <div class="modal fade" id="bigimage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" >
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><? echo "$year $make $model"; ?></h4>
                            </div>
                            <div class="modal-body">
                                <img src="<? echo $thumb; ?>" id="largelayout" class="img-responsive"/>
                                <div style="text-align:center; margin-top:20px; margin-bottom:-35px;" ma><span id="currentimage">1</span> of <span id="totalimages"><? echo $totalimages; ?></span></div>
                                <div style="margin-top:5px;">
                                    <button type="button" id="nextimage" class="btn btn-primary pull-right" >Next <i class="fa fa-arrow-circle-right"></i></button>
                                    <button type="button" id="previmage" class="btn btn-primary" ><i class="fa fa-arrow-circle-left"></i> Previous</button>
                                </div>
                                <div class="clearfix"></div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>



        <? } else { ?>

            <div class="noimages img-rounded">There are no images available<br>Upload Images</div>
        <? } ?>

    </div>



    <div class="hidden-print">
        <div class="spacediv"></div>
        <h2>Tools</h2>
        <div class="row">
            <div class="col-xs-6 col-sm-3">
                <?
                if($hascrm != 'true') {
                    if($hold != 'available') {
                        $cuidx = $_SESSION['name'];

                        if($_SESSION['role'] != 'fsdfsd') {
                            ?>
                            <button type="button" class="btn btn-warning" style="width:100%"  id="removeholdrequestbtn"><i class="fa fa-minus-circle"></i> Remove Hold</button>

                        <? } } else { ?>
                        <button type="button" class="btn btn-default" style="width:100%" id="holdrequestbtn"><i class="fa fa-lock"></i> Place On Hold</button>
                    <? } } else { echo '<div class="label label-danger">New: Manage Holds In CRM.</div><br>To put a vehicle on hold, go to your CRM, then attach a vehicle within a lead or quote, you will see all your hold options there.'; } ?>

            </div>

            <div class="col-xs-6 col-sm-3">
                <button type="button" class="btn btn-primary" style="width:100%" id="uploadimagesbtn"><i class="fa fa-cloud-upload"></i> Upload Images</button>
            </div>

            <div class="col-xs-6 col-sm-3">
                <?
                if($hascrm != 'true') { ?>
                    <button type="button" class="btn btn-danger" style="width:100%" id="soldrequestbtn"><i class="fa fa-usd"></i> Report As Sold</button>
                <? } else { echo '<div class="label label-danger">New: Mark Sold In CRM.</div><br>To mark vehicles sold, go to your CRM, attach a vehicle to a saleslead or quote. Upon convting it to a sale, you will see the mark sold options.'; } ?>
            </div>


        </div>
    </div>









    <div id="holdrequest" style="display:none;">

        <div class="row">
            <div class="col-xs-12">
                <h2>Put A Vehicle On Hold</h2>
                <p>Please fill in the form below to have a vehicle put on hold.</p>

                <form action="vehiclemain.php?vehicleid=<? echo $vehicleid; ?>" method="post" id="b-form" enctype="multipart/form-data" >
                    <fieldset>
                        <input type="hidden" id="approvethehold" name="approvethehold" value="true" />
                        <input type="hidden" id="vehicleid" name="vehicleid" value="<? echo $vehicleid; ?>" />

                        <div class="row">
                            <div class="col-xs-12 col-sm-12">



                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Customer Name *</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <input type="text" class="input-xlarge myx" name="name" id="name" placeholder="Enter the customer or business name" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Salesperson</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <input type="text" class="input-xlarge" name="salesman" id="salesman" value="<? echo $_SESSION['name']; ?>" required>
                                        </div>
                                    </div>
                                </div>




                                <div class="row rowgap">




                                    <div class="col-xs-3">
                                        <label class="formlabel">Recieved Deposit?</label>
                                    </div>
                                    <div class="col-xs-3">
                                        <div class="control-group has_deposit">
                                            <div style="float:left; width: 80px; line-height:38px"><input type="radio"  class="dep_radio" name="dep" value="Yes" style="width: 25px; height: 38px;">Yes</div>
                                            <div style="float:left; width: 150px; line-height:38px;"><input type="radio"  class="dep_radio" name="dep" value="No" style="width: 25px;  height: 38px;">No</div>
                                            <div style="clear:both"></div>
                                        </div>


                                    </div>

                                    <div class="col-xs-1">
                                        <h2 style="color:#ed1c24; margin-top:-5px;" class="pull-right"><i class="fa fa-chevron-circle-right dep_glowicon" style="display:none"></i></h2>
                                    </div>

                                    <div class="col-xs-5">
                                        <div class="control-group"><div class="control-group">
                                                <input type="text" class="input-xlarge deposit_input" name="deposit" id="otherdep_glow" value="" style="display:none;" placeholder="Enter the deposit">
                                            </div>
                                        </div>
                                    </div>


                                </div>


                                <div class="row rowgap" id="locationmoveradios" style="display: none;">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Location Move Needed?</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group has_deposit">
                                            <div style="float:left; width: 80px; line-height:38px;"><input type="radio"  class="mov_radio mov_yes" name="mov_radio" value="Yes" style="width: 25px; height: 38px; ">Yes</div>
                                            <div style="float:left; width: 150px; line-height:38px;"><input type="radio"  class="mov_radio mov_no" name="mov_radio" value="No" style="width: 25px;  height: 38px;" checked>No</div>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Sale Pending?</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group is_pending">
                                            <div style="float:left; width: 80px; line-height:38px"><input type="radio"  class="pending_radio pending_yes" name="pending" value="Yes" style="width: 25px; height: 38px; ">Yes</div>
                                            <div style="float:left; width: 150px; line-height:38px;"><input type="radio"  class="pending_radio pending_no" name="pending" value="No" style="width: 25px;  height: 38px;" checked>No</div>
                                        </div>
                                        <div style="background-color: #E9E9E9; margin-top:5px; padding:4px; color: #DF0003; font-weight:bold;"><i class="fa fa-exclamation-triangle"></i> Sale Pending Means The Sale Is Guaranteed or All But Delivered</div>
                                        <div style="clear:both"></div>
                                        <input type="hidden" name="pendingstatus" id="pendingstatus">
                                    </div>
                                </div>



                                <div class="row rowgap" id="hold_time_row">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Hold Length</label>
                                    </div>

                                    <div class="col-xs-9">
                                        <div class="control-group"><div class="control-group" <? if($categoryx == 'Used Not Ready') { ?> style="display:none;" <? } ?>>
                                                <select class="form-control formlabel " id="length" name="length" required <? if($lockholdlength == true) { echo 'readonly="readonly"'; } ?>>
                                                    <? foreach($holddaysavailable as $dv) { ?>
                                                        <option value="<? echo $dv; ?>"  class="radbtns"><? echo $dv; ?> Days</option>
                                                    <? } ?>
                                                </select>
                                            </div>
                                            <? if($categoryx == 'Used Not Ready') { ?>

                                            <? } ?>
                                        </div>
                                    </div>
                                </div>





                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Upload Buyers Order <strong>Must Be .pdf file</strong> <? if($hold_buyers_order_required == true) { ?> <span style="color: red; font-weight: bold;">Required</span> <? } ?></label>
                                    </div>

                                    <div class="col-xs-9">

                                        <input type="file" name="buyersorder" style="height: 30px; padding-bottom: 46px;"  accept="application/pdf" id="buyersorder" <? if($hold_buyers_order_required == true) { echo 'required'; } ?>>

                                    </div>
                                </div>





                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Pay Type</label>
                                    </div>

                                    <div class="col-xs-5">
                                        <div class="control-group"><div class="control-group">
                                                <select class="form-control formlabel " id="paytype" name="paytype" required>
                                                    <option class="visible-sm visible-md visible-lg" value="">Choose One</option>

                                                    <option value="Customer Pay">Customer Pay</option>
                                                    <option value="Financing">Financing</option>
                                                    <option value="Trust Fund">Trust Fund</option>
                                                    <option value="Medicaid Waiver">Medicaid Waiver</option>
                                                    <option value="VA">VA</option>
                                                    <option value="VOC Rehab">VOC Rehab</option>
                                                    <option value="State">State</option>
                                                    <option value="County">County</option>
                                                    <option value="30 Days">-------</option>
                                                    <option value="Other">Other</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-1">
                                        <h2 style="color:#ed1c24; margin-top:-5px;" class="pull-right"><i class="fa fa-chevron-circle-right paytypeicon" style="display:none"></i></h2>
                                    </div>

                                    <div class="col-xs-3">
                                        <div class="control-group"><div class="control-group">

                                                <input type="text" class="input-xlarge" name="otherpaytype" id="otherpaytype" placeholder="Enter Pay Type" style="display:none;" >
                                            </div>
                                        </div>
                                    </div>
                                </div>






                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Lead Source</label>
                                    </div>

                                    <div class="col-xs-5">
                                        <div class="control-group"><div class="control-group">
                                                <select class="form-control formlabel " id="leadsource" name="leadsource" required>
                                                    <option class="visible-sm visible-md visible-lg" value="">Choose One</option>

                                                    <option value="Google Organic">Google Organic</option>
                                                    <option value="PPC">PPC</option>
                                                    <option value="Friend Referral">Friend Referral</option>
                                                    <option value="VA">VA</option>
                                                    <option value="30 Days">-------</option>
                                                    <option value="Other">Other</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-1">
                                        <h2 style="color:#ed1c24; margin-top:-5px;" class="pull-right"><i class="fa fa-chevron-circle-right leadsourceicon" style="display:none"></i></h2>
                                    </div>

                                    <div class="col-xs-3">
                                        <div class="control-group"><div class="control-group">

                                                <input type="text" class="input-xlarge" name="otherleadsource" id="otherleadsource" placeholder="Enter Lead Source" style="display:none;" >
                                            </div>
                                        </div>
                                    </div>
                                </div>








                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Comments</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <textarea class="input-xlarge the-contact-location-comments" name="comments" id="comments" rows="3" placeholder="Add comments about the hold to share with team members."></textarea>
                                        </div>
                                    </div>
                                </div>




                            </div>
                        </div>



                        <div class="row">
                            <div class="col-xs-12" style="margin-top:15px;">
                                <div class="pull-right">
                                    <button type="button" class="btn hidealltools">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-large">NEXT</button>
                                </div>

                            </div>




                        </div>

                    </fieldset>
                </form>








            </div>
        </div>
    </div>









    <div id="removeholdrequest" style="display:none;">

        <div class="row">
            <div class="col-xs-12">
                <h2>Remove Vehicle From Hold</h2>
                <div class="alert alert-warning"><strong>Notice:</strong>  In the comments, please list why you are removing the hold.</div>

                <form action="vehiclemain.php?vehicleid=<? echo $vehicleid; ?>" method="post" id="b-form" >
                    <fieldset>
                        <input type="hidden" id="removethehold" name="removethehold" value="true" />
                        <input type="hidden" id="vehicleid" name="vehicleid" value="<? echo $vehicleid; ?>" />

                        <div class="row">
                            <div class="col-xs-12 col-sm-12">


                                <div class="row rowgap">
                                    <div class="col-xs-3">

                                        <label class="formlabel">Customer Name <i class="fa fa-lock"></i></label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">

                                            <input type="text" class="input-xlarge myx" name="customername" id="customername" value="<? echo $customer; ?>" readonly>

                                        </div>
                                    </div>
                                </div>

                                <div class="row rowgap">
                                    <div class="col-xs-3">

                                        <label class="formlabel">Removed By <i class="fa fa-lock"></i></label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <input type="text" class="input-xlarge" name="holdremovedby" id="holdremovedby" value="<? echo $_SESSION['name']; ?>" readonly required>
                                        </div>
                                    </div>
                                </div>




                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Comments *</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <textarea class="input-xlarge the-contact-location-comments" name="holdremovedcomments" id="holdremovedcomments" required rows="3" placeholder="Tell us why your are taking this vehicle off hold."></textarea>
                                        </div>
                                    </div>
                                </div>




                            </div>
                        </div>



                        <div class="row">
                            <div class="col-xs-12" style="margin-top:15px;">
                                <div class="pull-right">
                                    <button type="button" class="btn hidealltools">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-large">NEXT</button>
                                </div>

                            </div>




                        </div>

                    </fieldset>
                </form>



            </div>
        </div>
    </div>

















    <div id="soldrequest" style="display:none;">

        <div class="row">
            <div class="col-xs-12">
                <h2>Request Vehicle Be Marked As Sold</h2>
                <div class="alert alert-danger"><strong>Notice:</strong>  You should not request the vehicle be marked as sold until the deal is signed, paid and delivered.</div>

                <form action="vehiclemain.php?vehicleid=<? echo $vehicleid; ?>" method="post" id="b-form" >
                    <fieldset>
                        <input type="hidden" id="marksold" name="marksold" value="true" />
                        <input type="hidden" id="vehicleid" name="vehicleid" value="<? echo $vehicleid; ?>" />

                        <div class="row">
                            <div class="col-xs-12 col-sm-12">


                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Customer Name *</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <input type="text" class="input-xlarge myx" name="customername" id="customername" required>
                                        </div>
                                    </div>
                                </div>



                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Salesperson</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <select class="input-xlarge" name="salesmansold" id="salesmansold" required >
                                                <option value="<? echo $_SESSION['name']; ?>" selected><? echo $_SESSION['name']; ?></option>
                                                <option value="">---------------</option>
                                                <?


                                                $loc = mysql_query( "SELECT *
					FROM locations WHERE name !='On Order' AND corporateoffice='false' ORDER BY id ASC");

                                                while ($sss = mysql_fetch_array($loc)) {
                                                    $dealername = $sss['dealername'];
                                                    $locname = $sss['name'];
                                                    $locationidgf = $sss['id'];
                                                    $tollfree = $sss['tollfree'];
                                                    $city = $sss['city'];
                                                    $address = $sss['address'];
                                                    $state = $sss['state'];
                                                    $zip = $sss['zip'];
                                                    $lat = $sss['lat'];
                                                    $lon = $sss['lon'];

                                                    $sqltwo = @mysql_query( "SELECT * FROM contacts WHERE location='$locname' AND role!='None' ORDER BY arrange ASC");

                                                    while ($deone = mysql_fetch_array($sqltwo)) {
                                                        $uxid = $deone['id'];
                                                        $name = $deone['name'];
                                                        $phone = $deone['phone'];
                                                        $cell = $deone['cell'];
                                                        $email = $deone['email'];
                                                        $fax = $deone['fax'];
                                                        $picture = $deone['picture'];
                                                        $numbersold = $deone['numbersold'];
                                                        $title = $deone['title'];
                                                        $order = $deone['order'];
                                                        $details = substr($deone['details'], 0, 80);
                                                        $location = $deone['location'];
                                                        $role = $deone['role'];
                                                        $password = $deone['password'];
                                                        $salesleads = $deone['salesleads'];
                                                        $serviceleads = $deone['serviceleads'];
                                                        $rentalleads = $deone['rentalleads'];
                                                        $uploadpics = $deone['uploadpics'];

                                                        ?>
                                                        <option value="<? echo $name; ?>"><? echo "$locname - $name"; ?></option>
                                                    <? } ?>

                                                    <option value="">---------------</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="row rowgap">
                                    <div class="col-xs-3">
                                        <label class="formlabel">Comments</label>
                                    </div>
                                    <div class="col-xs-9">
                                        <div class="control-group">
                                            <textarea class="input-xlarge the-contact-location-comments" name="soldcomments" id="soldcomments" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>




                            </div>
                        </div>



                        <div class="row">
                            <div class="col-xs-12" style="margin-top:15px;">
                                One you submit your request, it will be sent to the Vehicle Administrator for approval.  You will receive an email once the vehicle has been removed from the web site.  Thank you and GREAT JOB on the sale!
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xs-12" style="margin-top:15px;">
                                <div class="pull-right">
                                    <button type="reset" class="btn">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-large" id="submitsalebtn" onClick="$('#submitsalebtn').attr("disabled", true);">Submit Request For Approval</button>
                                </div>

                            </div>




                        </div>

                    </fieldset>
                </form>


            </div>
        </div>
    </div>


















































    <div id="imageuploads" style="display:none;">

        <div class="row">
            <div class="col-xs-12">
                <h2>Upload Additional Images</h2>
                <p>To load images, simply click the "Select Files" button below.  You will then see a directory of your computer.  You can select multiple files from your computer and load several at once.  Once you images are uploaded, you will see them listed.  Images must be .jpg files and they must be no larger than 5mb.</p>
                <div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>



                <div id="showerrors"></div>

                <table class="table table-striped table-hover" id="uploadtable" style="display:none;">
                    <thead>
                    <tr>
                        <th style="width:20%;">File</th>
                        <th style="width:20%;">Size</th>
                        <th style="width:30%;">Upload Progress</th>
                    </tr>
                    </thead>
                    <tbody id="tbrows">

                    </tbody>

                </table>




                <div id="container">
                    <button id="pickfiles" type="button" class="btn btn-default"><i class="fa fa-search"></i> Select Files</button>
                    <button id="uploadfiles" type="button" class="btn btn-primary" style="display:none;"><i class="fa fa-cloud-upload"></i> Upload Files</button>
                </div>

                <div id="uploadprogress" style="display:none;">
                    <h3>Upload Progress</h3>
                    <div id="overallprogress"></div>
                    <div id="overallpercent"><span class="label label-default">0%</span></div>
                </div>

                <script type="text/javascript">
                    // Custom example logic
                    var totalfiles = 0;
                    var currentpercent = 0;
                    var factor = 0;
                    var uploader = new plupload.Uploader({
                        runtimes : 'html5,flash,silverlight,html4',
                        browse_button : 'pickfiles', // you can pass in id...
                        container: document.getElementById('container'), // ... or DOM Element itself
                        url : '/Express2.0/admin/xml/uploadImages.php',
                        multipart_params: {vehicleid : '<? echo $vehicleid; ?>', systemtype : 'new'},
                        flash_swf_url : 'plupload/js/Moxie.swf',
                        silverlight_xap_url : 'plupload/js/Moxie.xap',

                        filters : {
                            max_file_size : '6mb',
                            mime_types: [
                                {title : "Image files", extensions : "jpg,jpeg"}
                            ]
                        },

                        init: {
                            PostInit: function() {
                                document.getElementById('filelist').innerHTML = '';

                                document.getElementById('uploadfiles').onclick = function() {
                                    $('#container').hide(500);
                                    $('#uploadprogress').show(500);
                                    uploader.start();


                                    return false;
                                };
                            },

                            FilesAdded: function(up, files) {
                                $('#uploadfiles').slideDown(500);
                                $('#uploadtable').slideDown(500);

                                plupload.each(files, function(file) {
                                    totalfiles ++;
                                    document.getElementById('tbrows').innerHTML += '<tr><td>' + totalfiles + ' - ' + file.name + '</td><td>' + plupload.formatSize(file.size) + '</td><td><div id="' + file.id + '"><div>Waiting...<div class="progress  progress-striped"><div class="progress-bar progress-bar-info" style="width: 10%"></div></div></div></div></td></tr>';
                                });
                                currentpercent = 1;
                                factor = Math.round(100 / totalfiles);
                                document.getElementById('overallprogress').innerHTML += '<div class="progress progress-striped active"><div class="progress-bar" style="width: ' + currentpercent + '%"></div></div>';
                            },

                            UploadProgress: function(up, file) {
                                document.getElementById(file.id).getElementsByTagName('div')[0].innerHTML = '<div class="progress progress-striped"><div class="progress-bar progress-bar-success" style="width: ' + file.percent + '%"></div></div>';
                            },
                            FileUploaded: function(up, file) {
                                totalfiles--;
                                currentpercent = Math.round(100 - (factor * totalfiles));
                                document.getElementById('overallprogress').innerHTML = '<div class="progress progress-striped active"><div class="progress-bar" style="width: ' + currentpercent + '%"></div></div>';
                                document.getElementById('overallpercent').innerHTML = '<span class="label label-default">' + currentpercent + '%</span>';
                            },
                            UploadComplete: function(up, file) {

                                $('#sortable').hidetoolsx();
                                $("html, body").animate({ scrollTop: $('#imagessection').offset().top - 80 }, 1000);
                                $('#sortable').load('ajax/images.php?getimages=true&vehicleid=<? echo $vehicleid; ?>');

                            },

                            Error: function(up, err) {
                                document.getElementById('showerrors').innerHTML += "\nError #" + err.code + ": " + err.message;
                            }



                        }

                    });



                    uploader.init();

                </script>




            </div>
        </div>
    </div>









    <div class="spacediv no-line"></div>
    <h2 class="large-on-print">Admin Notes</h2>
    <?

    if($m['admin_notes'] != '') {
        echo $m['admin_notes'];
    } else {
        echo "There are no admin notes available at this time";
    }
    ?>



    <div  class="hidden-print">
        <div class="spacediv"></div>
        <h2 id="imagessection">Pictures</h2>
        <div class="clearfix"></div>
        <p><span class="label label-danger" style="float:left; "><i class="fa fa-thumbs-o-up"></i> NEW</span> &nbsp;&nbsp;&nbsp;You can now drag and drop the images to re-order them.  Simply grab one and drag it to your desired location.  The image that is in the top left corner will be the thumbnail at your site as well as third party sites.</p>



        <div class="row">
            <div id="sortable"></div>

        </div>

    </div>



    <? if($description != '') { ?>
        <div class="spacediv"></div>
        <h2 class="large-on-print">Vehicle Description</h2>
        <? echo $description; ?>
    <? } ?>





    <div class="spacediv"></div>

    <?
    $price_total_admin_total = number_format($m['price_total_admin']);
    $price_total_rebates_total = number_format($m['price_total_rebates']);
    $price_chassis_admin_total = number_format($m['price_chassis_admin']);
    $price_conversion_admin_total = number_format($m['price_conversion_admin']);
    $price_total_public_total = number_format($m['price_total_public']);
    $price_chassis_public_total = number_format($m['price_chassis_public']);
    $price_conversion_public_total = number_format($m['price_conversion_public']);

    $tenpercent = $pricenoformat * .1;
    $pricedown = $m['price_total_public'] - $tenpercent;

    // Calculate 36 Months Payments
    $factor = .0314980;
    $thirtysixmonths = ($pricedown * $factor);
    $thirtysixmonths = number_format(round($thirtysixmonths));

    // Calculate 48 Months Payments
    $factor = .0245775;
    $fortyeightmonths = ($pricedown * $factor);
    $fortyeightmonths = number_format(round($fortyeightmonths));

    // Calculate 60 Months Payments
    $factor = .0205408;
    $sixtymonths = ($pricedown * $factor);
    $sixtymonths = number_format(round($sixtymonths));

    // Calculate 72 Months Payments
    $factor = .0179018;
    $seventytwomonths = ($pricedown * $factor);
    $seventytwomonths = number_format(round($seventytwomonths));


    ?>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
            <h2 class="large-on-print">Pricing<span class="hidden-print"> &amp; Financing</span></h2>
            <div class="baseborder"><span class="pull-right">$<? echo $price_chassis_public_total; ?></span>Chassis:</div>
            <div class="baseborder"><span class="pull-right">$<? echo $price_conversion_public_total; ?></span><? echo $m['conversion']; ?></div>
            <div class="baseborder"><span class="pull-right">-$<? echo $price_total_rebates_total; ?></span>Less Current Rebates</div>
            <div><span class="pull-right"> <strong>$<? echo $price_total_public_total; ?></strong></span><strong>Total:</strong></div>

            <? if($_SESSION['role']=='SuperAdmin' || $_SESSION['role']=='Manager') { ?>
                <div class="admin_only_pricing">
                    <h2 class="large-on-print"><span style="color: #EF0507">Confidential</span> Wholesale/Cost Pricing</h2>
                    <div class="baseborder"><span class="pull-right">$<? echo $price_chassis_admin_total; ?></span>Chassis:</div>
                    <div class="baseborder"><span class="pull-right">$<? echo $price_conversion_admin_total; ?></span><? echo $m['conversion']; ?></div>
                    <?
                    $esql = mysql_query( "SELECT * FROM expenses WHERE vehicleid='$vehicleid' ORDER BY id ASC");
                    while ($efetch = mysql_fetch_array($esql)) {
                        $ename = $efetch['name'];
                        $eamount = $efetch['amount'];
                        ?>
                        <div class="baseborder"><span class="pull-right">$<? echo $eamount; ?></span><? echo $ename; ?></div>
                    <? } ?>
                    <div><span class="pull-right"> <strong>$<? echo $price_total_admin_total; ?></strong></span><strong>Total:</strong></div>
                    <div class="hidden-print">You are seeing this pricing because you are either a Manager or a SuperAdmin.</div>
                </div>
            <? } ?>


        </div>

        <div class="hidden-print">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 col-sm-offset-1">
                <h2 class="large-on-print">Estimated Payments</h2>

                <div class="baseborder"><span class="pull-right"> $<? echo $thirtysixmonths; ?></span>36 Months:</div>
                <div class="baseborder"><span class="pull-right"> $<? echo $fortyeightmonths; ?></span>48 Months:</div>
                <div class="baseborder"><span class="pull-right"> $<? echo $sixtymonths; ?></span>60 Months:</div>
                <div class="baseborder"><span class="pull-right"> $<? echo $seventytwomonths; ?></span>72 Months:</div>

            </div>
            <div class="col-xs-12" style="font-style:italic; font-size:14px; margin:20px 0 0 0;">Financing rates are based on 8.5%APR and 10% down payment.  Actual payment amounts will vary.  This should be used as a rough guidline only.  For exact finaning terms, please contact the financing agency you work with. Awlays verify these financing figures.  Some vehicles and/or customers may not qualify for financing.</div>

        </div>

    </div>



    <div class="hidden-print">
        <div class="spacediv"></div>
        <div class="row hidden-print">
            <div class="col-xs-12">
                <h2>Vehicle Options</h2>


                <ul class="vehicle-options-list"> <?
                    $optionaloptions = json_decode($optionaloptions,true);
                    $standardoptions = json_decode($standardoptions,true);
                    foreach($optionaloptions as $o) {  ?>
                        <li><? echo $o['name']; ?></li>
                    <? }


                    foreach($standardoptions as $o) {  ?>
                        <li><? echo $o['name']; ?></li>
                    <? } ?>
                </ul>


            </div>
        </div>
    </div>



    <div class="hidden-print">
        <div class="spacediv"></div>

        <div class="row">
            <div class="col-xs-12">
                <h2 style="margin-top:30px;" >
                    Conversion: <? echo $m['conversion']; ?></h2>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ">
                    New or Used: <? echo $m['conversion_new/used']; ?>

                </div>






            </div>
        </div>
    </div>





    <div class="hidden-print">
        <div class="spacediv"></div>

        <div class="row">
            <div class="col-xs-12">
                <h2>Downloads</h2>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                        <?
                        $vanfeatures = mysql_query("SELECT id, filename, name, listed
											FROM dealerfileUploads
											WHERE vehicleid='$vehicleid' AND `type`='User'
											ORDER BY name ASC");
                        while ($feat2 = mysql_fetch_array($vanfeatures)) {
                            $id = $feat2['id'];
                            $name = $feat2['name'];
                            $filename = $feat2['filename'];
                            $listed = $feat2['listed'];
                            $listed = date('m/d/y', $listed);

                            ?>
                            <button type="button" class="btn btn-primary" style="width:100%; margin-bottom: 5px; border-radius: 4px; text-align:left; font-size:120%;" onClick="window.open('/dealeruploads/<? echo $filename; ?>');"><i class="fa fa-file-text" style="margin-right:10px; font-size:160%"></i><? echo $name; ?></button>
                        <?  } ?>


                        <button type="button" class="btn btn-primary" style="width:100%; border-radius: 4px; text-align:left; font-size:120%;" onClick="window.open('/vehiclemain.php?vehicleid=<? echo $vehicleid; ?>&adminprint');"><i class="fa fa-file-text" style="margin-right:10px; font-size:160%"></i>Print This Vehicle As Brochure</button>

                    </div>
                </div>
            </div>
        </div>


    </div>






    <div class="hidden-print">
        <?
        $sqlx = mysql_query( "SELECT *
					FROM holds
					WHERE vehicleid='$vehicleid' ORDER BY id DESC");
        $records = mysql_num_rows($sqlx);
        ?>
        <h2 class="large-on-print" style="margin-bottom:-30px; margin-top:90px;"  id="holdhistory">Vehicle Hold History Log - Found <? echo $records; ?> Records</h2>

        <?

        $holdx = $hold;
        $holdstarttimestampx = $holdstarttimestamp;
        $holdexpiresx = $holdexpires;


        while($onex = mysql_fetch_array($sqlx)) {
            $salesman = htmlspecialchars($onex['salesman']);
            $extendeddays = htmlspecialchars($onex['extendeddays']);
            $extendholdnotes = htmlspecialchars($onex['extendholdnotes']);
            $deposit = htmlspecialchars($onex['deposit']);
            $paytype = htmlspecialchars($onex['paytype']);
            $customer = htmlspecialchars($onex['customer']);
            $holdid = htmlspecialchars($onex['holdid']);
            $extention = htmlspecialchars($onex['extention']);
            $salesman = htmlspecialchars($onex['salesman']);
            $holdstart = htmlspecialchars($onex['holdstart']);
            $holdexpires = htmlspecialchars($onex['holdexpires']);
            $holdnotes = $onex['holdnotes'];
            $deposit = htmlspecialchars($onex['deposit']);
            $holdremovedby = htmlspecialchars($onex['holdremovedby']);
            $addedorremoved = htmlspecialchars($onex['addedorremoved']);
            $paytype = htmlspecialchars($onex['paytype']);
            $leadsource = htmlspecialchars($onex['leadsource']);

            $totaldays = round(($holdexpires - $holdstart) / 86400);

            $startdate = date('l F jS', $holdstart);
            $enddate = date('l F jS', $holdexpires);





            ?>



            <? if($extention == 'true') { ?>
                <div class="spacediv"></div>
                <div class="row">
                    <div class="col-sm-12" style="margin-bottom:10px;">
                        <span class="label label-info"><? echo $startdate; ?> - Hold Extension</span>
                    </div>
                    <div class="col-sm-4">
                        <strong>Salesperson:</strong> <? echo $hold; ?><br>
                        <strong>Customer:</strong> <? echo $customer; ?><br>
                        <strong>Deposit:</strong> $<? echo str_replace('$', '', $deposit); ?><br>
                        <strong>Paytype:</strong> <? echo $paytype; ?><br>
                    </div>


                    <div class="col-sm-4">
                        <strong>Hold Start:</strong> <? echo $startdate; ?><br>
                        <strong>Hold Ends:</strong> <? echo $enddate; ?><br>
                        <strong>Total Days:</strong> <? echo $daysleft; ?><br>
                    </div>


                    <div class="col-sm-4">
                        <strong>Original Comments:</strong><br>
                        <?
                        $cshow = false;
                        if($_SESSION['role'] == 'SuperAdmin') {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($_SESSION['name'] == $salesman && $cshow == false) {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($cshow == false ) {
                            echo str_replace('Download Buyers Order','',strip_tags($holdnotes));
                        }
                        ?><br>
                        <strong>Extension Comments:</strong><br>
                        <? echo $extendholdnotes; ?>
                    </div>
                </div>
            <? } ?>




            <? if($extention == 'false'  && $addedorremoved=='added') { ?>
                <div class="spacediv"></div>
                <div class="row">
                    <div class="col-sm-12" style="margin-bottom:10px;">
                        <span class="label label-primary"><? echo $startdate; ?> - Standard Hold</span>
                    </div>
                    <div class="col-sm-4">

                        <strong>Salesperson:</strong> <? echo $hold; ?><br>
                        <strong>Customer:</strong> <? echo $customer; ?><br>
                        <strong>Deposit:</strong> $<? echo str_replace('$', '', $deposit); ?><br>
                        <strong>Paytype:</strong> <? echo $paytype; ?><br>

                    </div>


                    <div class="col-sm-4">
                        <? if($matched != true) { ?>
                            <strong>Hold Start:</strong> <? echo $startdate; ?><br>
                            <strong>Hold Ends:</strong> <? echo $enddate; ?><br>
                            <strong>Days Remaining:</strong> <? echo $daysleft; ?><br>
                        <? } else { ?>
                            <strong>Hold Inactive</strong><br>
                            <strong>Hold Began:</strong> <? echo $startdate; ?><br>
                            <strong>Hold Expired:</strong> <? echo $enddate; ?><br>
                        <? } ?>
                    </div>


                    <div class="col-sm-4">
                        <strong>Comments:</strong><br>
                        <?
                        $cshow = false;
                        if($_SESSION['role'] == 'SuperAdmin') {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($_SESSION['name'] == $salesman && $cshow == false) {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($cshow == false ) {
                            echo str_replace('Download Buyers Order','',strip_tags($holdnotes));
                        }
                        ?>
                    </div>
                </div>
            <? } ?>





            <? if($extention == 'false'  && $addedorremoved=='removed') { ?>
                <div class="spacediv"></div>
                <div class="row">
                    <div class="col-sm-12" style="margin-bottom:10px;">
                        <span class="label label-warning"><? echo $startdate; ?> - Removed Hold</span>
                    </div>
                    <div class="col-sm-4">
                        <strong>Salesperson:</strong> <? echo $hold; ?><br>
                        <strong>Customer:</strong> <? echo $customer; ?><br>
                        <strong>Deposit:</strong> $<? echo str_replace('$', '', $deposit); ?><br>
                        <strong>Paytype:</strong> <? echo $paytype; ?><br>
                    </div>


                    <div class="col-sm-4">
                        <strong>Date Removed:</strong> <? echo $startdate; ?><br>
                        <strong>Removed By:</strong> <? echo $holdremovedby; ?><br>
                        <strong>Hold Expired:</strong> <? echo $enddate; ?><br>
                    </div>


                    <div class="col-sm-4">
                        <strong>Comments At Removal of Hold:</strong><br>
                        <?
                        $cshow = false;
                        if($_SESSION['role'] == 'SuperAdmin') {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($_SESSION['name'] == $salesman && $cshow == false) {
                            echo $holdnotes;
                            $cshow = true;
                        }
                        if($cshow == false ) {
                            echo str_replace('Download Buyers Order','',strip_tags($holdnotes));
                        }
                        ?>
                    </div>
                </div>
            <? } ?>




            <?

            if($holdx == $salesman && $holdstarttimestampx==$holdstart && $holdexpiresx==$holdexpires) {
                $matched = true;
            }

        } ?>
    </div>

    <div style="height:100px;" class="hidden-print"></div>

</div>
</body>
</html>