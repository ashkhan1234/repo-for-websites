<? include 'Connect.php';
$vehicleid = $_GET['vehicleid'];
if(!is_numeric($vehicleid)) {
    $vehicleid = base64url_decode($vehicleid);
}
$vehicleid = substr(stripslashes(preg_replace('/\D/', '', $vehicleid)),0, 5);
if(!is_numeric($vehicleid)) {
    header("HTTP/1.0 404 Not Found");
    echo 'Page Not Found';
    die;
}


$target_tab = $_GET['tab'];


$sql = $mysqli->query( "SELECT vehiclelookup.hold, vehiclelookup.description, vehiclelookup.standard_options, vehiclelookup.optional_options, vehiclelookup.hitcounter,
                    locations.id as locationid, locations.url as locationslug, locations.phone, locations.tollfree
					FROM vehiclelookup, locations
					WHERE vehicleid='$vehicleid' AND showonline='true' AND vehiclelookup.location=locations.name");
$totalfound = $sql->num_rows;
if($totalfound == 0) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $results_dot_php");
}
$one = $sql->fetch_assoc();
$hold = htmlspecialchars($one['hold']);
$description = $one['description'];
$standardoptions = $one['standard_options'];
$optionaloptions = $one['optional_options'];
$hitcounter = $one['hitcounter'];
$phone = $one['phone'];
$tollfree = $one['tollfree'];
if($tollfree == '') {
    $phone = $phone;
} else {
    $phone = $tollfree;
}


$locationid = $one['locationid'];
$locationslug = $one['locationslug'];




$printURL = "/printvehicle.php?vehicleid=$vehicleid";
// Counter Work
$hitcounter ++;
$mysqli->query("UPDATE vehiclelookup SET
			hitcounter='$hitcounter'
			WHERE vehicleid='$vehicleid'");
//End Counter Work



$m= array();
$sqltwox = $mysqli->query( "SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
while ($met = $sqltwox->fetch_assoc()) {
    $m[$met['type']] = $met['value'];
}
/*new code*/
$discounts= array();
$totaldiscounts = 0;
$sqltwoxdis = $mysqli->query( "SELECT * FROM discounts WHERE vehicleid='$vehicleid'");
while ($ddd = $sqltwoxdis->fetch_assoc()) {
    if($ddd['amount'] != 0) {
        if ($ddd['name'] == '') {
            $ddd['name'] = $dealername . ' Discount';
        }
        $discounts[] = $ddd;
        $totaldiscounts = $totaldiscounts + $ddd['amount'];}}
        /*new code*/

$optionaloptions = json_decode($optionaloptions,true);
$standardoptions = json_decode($standardoptions,true);

// Get the URL to the current page.
$fullpath = strtok($_SERVER["REQUEST_URI"],'?');
$fullpath = substr($fullpath, 1);


// Check and see if certain options are present.
$featuredOptions = array();

if($m['leather']=='Yes') { array_push($featuredOptions,'Leather Seating'); }
if($m['navigation']=='Yes') { array_push($featuredOptions,'In-Dash Navigation'); }
if($m['sunroof']=='Yes') { array_push($featuredOptions,'Sunroof'); }
if($m['dvd_entertainment']=='Yes') { array_push($featuredOptions,'Mid-Row DVD Player'); }

foreach($standardoptions as $o) {
    if (strpos($o['name'], 'Auxiliary Audio') !== false) {
        array_push($featuredOptions,'Auxiliary Audio Input');
    }
    if (strpos($o['name'], 'Alloy Wheels') !== false) {
        array_push($featuredOptions,$o['name']);
    }
    if (strpos($o['name'], 'MP3') !== false) {
        array_push($featuredOptions,'MP3 Playback');
    }
}

$featuredOptions = array_unique($featuredOptions);









?>
<!doctype html>
<html>
<head>
    <title><? echo $m['year']; ?> <? echo $m['make']; ?> <? echo $m['model']; ?> | Stock: <? echo $m['stock']; ?> | Wheelchair Van For Sale | <? echo $dealername; ?></title>
    <meta name="description" content="Top Quality <? echo $m['make']; ?> Wheelchair Van For Sale | <? echo $m['year']; ?> <? echo $m['make']; ?> <? echo $m['model']; ?> | Stock: <? echo $m['stock']; ?>">
    <? require_once 'head-tags.php'; ?>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="/css/vans-for-sale.css">
</head>
<body>
<? include 'header.php'; ?>
<div class="row">
    <div class="col-xs-12">
        <div class="price-top hidden-xs hidden-sm">
            <button id="my-button" type="button" class="btn-default van-for-sale">
                <i class="fa fa-arrow-down"></i>
            <div>Click For Price</div>
            </button>
            <?/* if($m['show_price_public'] == 'true') {
                $m['show_price_public'] = str_replace(',','',$m['show_price_public']);
                echo '$'.number_format($m['price_total_public']); */?><!--
            <?/* } else { */?>
                Call For Price
            --><?/* } */?>
            <br><a href="/wheelchair-van-financing" style="font-size: 16px;">Learn About Financing Options</a>
        </div>
        <div class="page-top-floats backwrap">
            <button type="button" class="btn-default van-for-sale" onClick="goBack();">
                <i class="fa fa-arrow-left"></i>
                <div>Back</div>
            </button>
        </div>
        <div class="page-top-floats hidden-xs">
            <h1><? echo $m['year']; ?> <? echo $m['make']; ?> <? echo $m['model']; ?></h1>
            <div class="conversion"><? echo $m['conversion']; ?></div>
        </div>


    </div>
</div>

<?
$img = $mysqli->query( "SELECT large, super, thumb FROM pictures WHERE vehicleid='$vehicleid' ORDER BY arrange ASC");
$totalpics = $img->num_rows;
if($totalpics !=0){
    ?>

    <div class="row">
        <div class="col-sm-12">
            <?
            $imgarray = array();
            $imgc = 0;
            $imgarray[0]['thumb'] = '/img/novan.jpg';
            $imgarray[0]['large'] = '/img/novan.jpg';
            $imgarray[0]['super'] = '/img/novan.jpg';
            $imgarray[0]['superwidth'] = 640;
            $imgarray[0]['superheight'] = 390;

            while($img2 = $img->fetch_assoc()) {
                $imgthumb = "/Express2.0/imageup/".$img2['thumb'];
                $imglarge = "/Express2.0/imageup/".$img2['large'];
                $imgsuper = "/Express2.0/imageup/".$img2['super'];

                if ($imgsuper != '/Express2.0/imageup/') {
                    $imgsuper = $imgsuper;
                    $hassuperimage = 'true';
                } else {
                    $imgsuper = $imglarge;
                    $hassuperimage = 'false';
                }

                if ($imgthumb == '/Express2.0/imageup/') {
                    $imgthumb = '/img/novan.jpg';
                }
                if ($imglarge == '/Express2.0/imageup/') {
                    $imglarge = '/img/novan.jpg';
                }
                if ($imgsuper == '/Express2.0/imageup/') {
                    $imgsuper = '/img/novan.jpg';
                }
                $imgserverpath = str_replace('/Express2.0','Express2.0',$imgsuper);
                $imgserverpath = str_replace('/img/novan','img/novan',$imgserverpath);

                list($superwidth, $superheight) = getimagesize($imgserverpath);

                $imgarray[$imgc]['thumb'] = $imgthumb;
                $imgarray[$imgc]['large'] = $imglarge;
                $imgarray[$imgc]['super'] = $imgsuper;
                $imgarray[$imgc]['superwidth'] = $superwidth;
                $imgarray[$imgc]['superheight'] = $superheight;
                $imgc++;
            }
            ?>
            <script>
                $(window).load(function() {

                    $('.img-loader-spinner').hide();

                    //Pre-Load the large images after initial download complete
                    var mobile_thumbs = '';
                    var desktop_thumbs = '';
                    <? $ii=0; foreach($imgarray as $i) {

                    if($hassuperimage == 'true') {
                        if($ii == 2 || $ii == 5 || $ii == 8) { $widthclass = '14'; } else { $widthclass = '7'; }
                        } else {
                        if($ii == 2 || $ii == 5 || $ii == 8) { $widthclass = 'third'; } else { $widthclass = 'third'; }
                    }



                    ?>
                    desktop_thumbs += '<div class="block-xxs-<? echo $widthclass; ?> vertical-thumb">' +
                        '<div class="slide thumb <? if($ii == 0) { echo 'active'; } ?>" data-imgurl="<? echo $i['super']; ?>" data-number="<? echo $ii; ?>" data-super="<? echo $i['super']; ?>">' +
                        '<img src="<? echo $i['large']; ?>" class="img-responsive">' +
                        '</div></div>';

                    mobile_thumbs += '<div class="slide thumb"  data-imgurl="<? echo $i['super']; ?>" data-number="<? echo $ii; ?>" data-super="<? echo $i['super']; ?>">' +
                        '<img src="<? echo $i['large']; ?>" class="img-responsive">' +
                        '</div>';
                    <? $ii++; } ?>


                    $('#mobile-thumbs').html(mobile_thumbs);
                    $('#desktop-thumbs').html(desktop_thumbs);

                    var imgSlider = $('.img-thumbs').bxSlider({
                        slideWidth: 160,
                        pager: false,
                        minSlides: 1,
                        speed: 400,
                        maxSlides: 20,
                        slideMargin: 10,
                        auto: false,
                        autoHover: true,
                        moveSlides: 2,
                        controls: false
                    });

                    $('#move-left').click(function(){
                        imgSlider.goToPrevSlide();
                        return false;
                    });

                    $('#move-right').click(function(){
                        imgSlider.goToNextSlide();
                        return false;
                    });

                    $('.thumb').click(function(e){
                        e.preventDefault();
                        var fff = $(this).attr('data-imgurl');
                        $('#main_image').attr("src", fff);
                        $('#main_image').parent().attr("href", fff);
                        $('.thumb.slide').removeClass('active');
                        $(this).addClass('active');
                        $('.img-loader-spinner').show();
                        $('#main_image').attr("data-number", $(this).attr('data-number'));
                        currentimage = $(this).attr('data-number');
                        $('.main-img-container').imagesLoaded().done( function( instance ) {
                            $('.img-loader-spinner').hide();
                        });
                    });


                    // Download the large images.
                    $('.img-thumbs').imagesLoaded().done( function( instance ) {
                        //Pre-Load the large images after initial download complete
                        var w = '';
                        <? $ii=0; foreach($imgarray as $i) { ?>
                        w += '<img src="<? echo $i['super']; ?>">';
                        <? $ii++; } ?>
                        totalimages = (<? echo $ii; ?> - 1);
                        $('.img-cache-holder').html(w);
                    });




                    var totalimages;
                    var currentimage = 0;
                    var imgarray = <? echo json_encode($imgarray, true); ?>;

                    $('#main_image').click(function(e) {
                        e.preventDefault();
                        currentimage = $(this).attr('data-number');
                        superImageJump();
                    });

                    function superImageJump() {
                        if (imgarray[currentimage]['super'] && imgarray[currentimage]['super'] != '') {
                            $('#superimage-inner > img').attr('src', imgarray[currentimage]['super']);
                            sizeUpSuperImage();
                            $('#superimage-wrapper').fadeIn().addClass('supersizevisible');
                        }
                    }



                    $('#superimgageright').click(function(event){
                        currentimage++;
                        if(currentimage > totalimages) { currentimage = 0; }
                        superImageJump();
                        event.stopPropagation();
                    });


                    $('#superimgageleft').click(function(event){
                        currentimage--;
                        if(currentimage < 0) { currentimage = totalimages; }
                        superImageJump();
                        event.stopPropagation();
                    });

                    $('#superimgageclose').click(function(event){
                        $('#superimage-wrapper').removeClass('supersizevisible').fadeOut();
                        event.stopPropagation();
                    });
                    $('#supermainimg').click(function(event){
                        $('#superimgageright').trigger('click');
                        event.stopPropagation();
                    });
                    $('#superimage-wrapper').click(function(event){
                        $('#superimage-wrapper').removeClass('supersizevisible').fadeOut();
                        event.stopPropagation();
                    });

                    function sizeUpSuperImage() {
                        var sw = $(window).width();
                        var sh = $(window).height();
                        var iw = imgarray[currentimage]['superwidth'];
                        var ih = imgarray[currentimage]['superheight'];
                        var widhtratio = ih / iw;
                        var heightratio = iw / ih;

                        var usepercent
                        if(sw > 1) { usepercent = 1; }
                        if(sw > 800) { usepercent = .9; }
                        if(sw > 1200) { usepercent = .9; }
                        var maxwidth = Math.round(sw * usepercent);
                        var maxheight = Math.round(sh * usepercent);

                        var targetImage = $('#superimage-inner > img');

                        targetImage.css('width', maxwidth+'px');
                        targetImage.css('height', (maxwidth * widhtratio)+'px');

                        if(targetImage.outerHeight() > maxheight) {
                            targetImage.css('height', maxheight+'px');
                            targetImage.css('width', (maxheight * heightratio)+'px');
                        }

                        var marginTop = Math.round((sh - targetImage.outerHeight()) / 2);

                        var superimageinner = $('#superimage-inner');
                        superimageinner.width(targetImage.outerWidth());
                        superimageinner.height(targetImage.outerHeight());
                        superimageinner.css('margin-top', marginTop+'px');


                    }

                    function mainImageJump() {
                        if (imgarray[currentimage]['super'] && imgarray[currentimage]['super'] != '') {
                            $('#main_image').attr('src', imgarray[currentimage]['super']);
                            $('#main_image').attr('data-number', currentimage);
                        }
                    }
                    $('#normalimgageright').click(function(event){
                        currentimage++;
                        if(currentimage > totalimages) { currentimage = 0; }
                        mainImageJump();
                        event.stopPropagation();
                    });
                    $('#normalimgageleft').click(function(event){
                        currentimage--;
                        if(currentimage < 0) { currentimage = totalimages; }
                        mainImageJump();
                        event.stopPropagation();
                    });


                    window.onresize = function() {
                        if($('#superimage-wrapper').hasClass('supersizevisible')) {
                            sizeUpSuperImage();
                        }
                    }




                });
            </script>
            <div class="img-cache-holder" style="display: none;"></div>

            <div class="tiles">
                <div class="row">
                    <div class="block-xxs-14 no-padding">
                        <div class="block-xxs-14 <? if($hassuperimage == 'true') { echo 'block-md-10'; } else { echo 'block-md-7'; } ?> main-img-container">
                            <i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom img-loader-spinner"></i>
                            <div id="normalimgageright"><img src="//www.dealerexpress.net/sharedimages/supergalleryright.png"></div>
                            <div id="normalimgageleft"><img src="//www.dealerexpress.net/sharedimages/supergalleryleft.png"></div>
                            <a href="javascript:void(0)">
                                <img id="main_image" src="<? echo $imgarray[0]['super']; ?>" class="img-responsive" data-number="0">
                            </a>
                        </div>
                        <div class="<? if($hassuperimage == 'true') { echo 'block-xxs-4'; } else { echo 'block-xxs-7'; } ?> hidden-xs hidden-sm no-padding">
                            <div class="block-xxs-14 no-padding" style="padding-right: 5px !important;" >
                                <div class="thumb-container <? if($hassuperimage != 'true') { echo 'no-super-image'; } ?>">
                                    <i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom thumb-loader-spinner" style="display: none"></i>
                                    <div id="desktop-thumbs" class="inside">

                                    </div></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 10px;"></div>

            <div class="img-thumbs-wrapper hidden-md hidden-lg" >
                <div id="move-right" class="vfs-arrow main-bg-1 main-bg-1-hover">
                    <i class="fa fa-chevron-right"></i>
                    <div>Slide Images</div>
                </div>
                <div id="mobile-thumbs" class="img-thumbs vans-preview-imgs-pink">

                </div></div>


        </div>
    </div>
<? } else { ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="sidebarfeature" style="margin-top: 15px;">
                Check back soon for images.
            </div>
        </div>
    </div>
<? } ?>



<div style="height: 10px;"></div>


<div id="page-container" class="tiles">
    <div class="row">
        <div class="block-xxs-14 no-padding">
            <div class="block-xxs-14" style="margin-bottom: -10px; position:relative;">
                <div class="vfs-tab-wrapper hidden-xs" style="border-bottom: solid 3px #2D2D2D;">
                    <a href="general" id="general" class="vehicletabs btnbar jumpbtn active">General</a>
                    <a href="price" id="price" class="vehicletabs price-btn btnbar jumpbtn ">Price</a>
                    <? if($m['conversionid'] != '') { ?>
                        <a href="conversion" id="conversion" class="vehicletabs btnbar jumpbtn">Conversion</a>
                    <? } ?>
                    <a href="options" id="options" class="vehicletabs btnbar jumpbtn">Options</a>

                    <? if($m['youtubeid'] != '') { ?>
                        <a href="video" id="video"  class="vehicletabs btnbar jumpbtn">Video</a>
                    <? } ?>

                </div>


                <div class="buttons-tools">
                    <div class="row">
                        <div class="block-xxs-14 block-sm-7">
                            <button type="button" class="btn-default van-for-sale" onClick="goBack();">
                                <i class="fa fa-arrow-left"></i>
                                <div>Back</div>
                            </button>
                        </div>
                        <div class="block-xxs-14 block-sm-7">
                            <button type="button" class="btn-default van-for-sale" onClick="window.open('/printvehicle.php?vehicleid=<? echo $vehicleid; ?>');">
                                <i class="fa fa-print"></i>
                                <div>Print</div>
                            </button>
                        </div>
                        <!--<div class="block-xxs-14 block-sm-third">
                                        <button type="button" class="btn-default van-for-sale" onClick="window.open('/printvehicle.php?vehicleid=<? echo $vehicleid; ?>');">
                                            <i class="fa fa-envelope"></i>
                                            <div>Email</div>
                                        </button>
                                    </div>-->
                    </div>
                </div>

            </div>
            <div id="vansection" class="block-xxs-14 block-sm-9 block-md-10 match-column-height">
                <div class="tiles">
                    <div class="row">
                        <div class="block-xxs-14 no-padding">
                            <div id="vansection" class="block-xxs-14">
                                <div class="sidebarfeature main-panel" style="margin-top: -5px; border-top: none;">

                                    <img id="main_image_print" src="<? echo $imgurl1; ?>">


                                    <div id="_general" class="tab_container">
                                        <h2>General Information</h2>

                                        <div class="row">
                                            <div class="block-xxs-14 no-padding">


                                                <? if($hold != 'available') { ?>
                                                    <div class="block-xxs-14 block-md-7">
                                                        <div class="notice-bar hold"><i class="fa fa-exclamation-triangle"></i> Vehicle on Hold</div>
                                                    </div>
                                                <? } ?>


                                                <? if($m['certified'] == 'Yes') { ?>
                                                    <div class="block-xxs-14  block-md-7">
                                                        <div class="notice-bar certified"><i class="fa fa-check"></i> Certified Pre-Owned</div>
                                                    </div>
                                                <? } ?>
                                                <div style="clear: left"></div>


                                                <div class="block-xxs-14 block-md-7">
                                                    <div class="rowAll rowA"><div class="rowTitle">Year:</div><span class="rowFloat"><p><? echo $m['year']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">Model:</div><span class="rowFloat"><p><? echo $m['model']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">Conversion:</div><span class="rowFloat"><p><? echo $m['conversion']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">VIN:</div><span class="rowFloat"><p><? echo $m['vin']; ?></p></span></div>
                                                    <div class="rowAll rowB"><div class="rowTitle">Condition:</div><span class="rowFloat"><p><? echo $m['newused']; ?>/<? echo $m['conversion_newused']; ?></p></span></div>

                                                    <div class="rowAll rowB"><div class="rowTitle">Exterior:</div><span class="rowFloat"><p><? echo $m['ecolor']; ?></p></span></div>
                                                    <div class="rowAll rowB"><div class="rowTitle">Hwy MPG:</div><span class="rowFloat"><p><? echo $m['mpg_hwy']; ?> MPG Highway Est.</p></span></div>
                                                    <div class="rowAll rowA nobottomborder"><div class="rowTitle">Location:</div><span class="rowFloat"><p><a href="/locations">Contact Us</a></p></span></div>
                                                </div>
                                                <div class="block-xxs-14 block-md-7">
                                                    <div class="rowAll rowA"><div class="rowTitle">Make:</div><span class="rowFloat"><p><? echo $m['make']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">Trim:</div><span class="rowFloat"><p><? echo $m['trim']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">Miles:</div><span class="rowFloat"><p><? if($m['miles']!='' ) { echo number_format($m['miles']); } ?></p></span></div>
                                                    <div class="rowAll rowB"><div class="rowTitle">Stock:</div><span class="rowFloat"><p><? echo $m['stock']; ?></p></span></div>
                                                    <div class="rowAll rowB"><div class="rowTitle">Engine:</div><span class="rowFloat"><p><? echo $m['engine']; ?></p></span></div>
                                                    <div class="rowAll rowA"><div class="rowTitle">Interior:</div><span class="rowFloat"><p><? echo $m['icolor']; ?></p></span></div>
                                                    <div class="rowAll rowB"><div class="rowTitle">City MPG:</div><span class="rowFloat"><p><? echo $m['mpg_city']; ?> MPG City Est.</p></span></div>
                                                    <div class="rowAll rowA nobottomborder"><div class="rowTitle">Status:</div><span class="rowFloat">
                                                                    <p>
                                                                        <?
                                                                        if($hold!='available') {
                                                                            echo '<i class="fa fa-exclamation-triangle"></i> Vehicle on Hold';
                                                                        } else {
                                                                            echo $m['arrival_status']; if($m['arrival_status'] == 'On Order') { echo ' - ETA: '.$m['eta']; }
                                                                        }
                                                                        ?>
                                                                    </p></span></div>
                                                </div>


                                                <div class="block-xxs-14 block-md-7">
                                                    <div class="vehicle-infobox">
                                                        <div class="upper">Chassis Warranty:</div>
                                                        <div class="lower"><? echo $m['chassis_warranty']; ?></div>
                                                    </div>
                                                </div>
                                                <div class="block-xxs-14 block-md-7">
                                                    <div class="vehicle-infobox">
                                                        <div class="upper">Conversion Warranty</div>
                                                        <div class="lower"><? echo $m['conversion_warranty']; ?></div>
                                                    </div>
                                                </div>
                                                <? if($description != '') { ?>
                                                    <div class="block-xxs-14">
                                                        <h2>Vehicle Description</h2>
                                                        <p><? echo $description; ?></p>
                                                    </div>
                                                <? } ?>


                                                <?
                                                if(count($featuredOptions)!=0) {
                                                    echo '<div class="block-xxs-14" style="padding-bottom: 0;"><h2>Noted Vehicle Options</h2></div>';
                                                    foreach($featuredOptions as $feature) { ?>
                                                        <div class="block-xxs-14 block-sm-7 block-lg-third downloads">
                                                            <i class="fa fa-check-circle"></i> <? echo $feature; ?>
                                                        </div>
                                                    <? } ?>
                                                    <div class="block-xxs-14 block-sm-7 block-lg-third downloads">
                                                        <a href="options" id="options" class="jumpbtn"><i class="fa fa-plus-circle"></i> <? echo count($standardoptions); ?> Additional Options</a>
                                                    </div>
                                                <? } ?>

                                                <?
                                                $vanfeatures = $mysqli->query("SELECT id, filename, name, listed
                                                                                        FROM dealerfileuploads
                                                                                        WHERE vehicleid='$vehicleid' AND `type`='Everyone'
                                                                                        ORDER BY name ASC");
                                                ?>
                                                <div class="hidden-xs">
                                                    <div class="block-xxs-14" style="padding-bottom: 0;">
                                                        <h2>Downloads</h2>
                                                    </div>
                                                    <?
                                                    while ($feat2 = $vanfeatures->fetch_assoc()) {
                                                        $id = $feat2['id'];
                                                        $name = $feat2['name'];
                                                        $filename = $feat2['filename'];
                                                        $listed = $feat2['listed'];
                                                        $listed = date('m/d/y', $listed);
                                                        ?>
                                                        <div class="block-xxs-14 block-sm-7 block-lg-third downloads">
                                                            <li onclick="window.open('/dealeruploads/<? echo $filename; ?>');">
                                                                <? echo $name; ?>
                                                            </li>
                                                        </div>
                                                    <? } ?>
                                                    <div class="block-xxs-14 block-sm-7 block-lg-third downloads">
                                                        <li onclick="window.open('/printvehicle.php?vehicleid=<? echo $vehicleid; ?>');">
                                                            Print This Vehicle As Brochure
                                                        </li>
                                                    </div>
                                                </div>



                                            </div>
                                        </div>
                                    </div>









                                    <div id="_price" class="tab_container ini-hide">
                                        <h2>Price</h2>
                                        <div class="row">
                                            <div class="block-xxs-14 no-padding">
                                                <div class="block-xxs-14 block-sm-14 block-md-7">
                                                  <!--  <?/* if($m['show_price_public'] == 'true') {
                                                        $m['show_price_public'] = str_replace(',','',$m['show_price_public']);
                                                        */?>
                                                        <p style="font-size: 60px; margin-top: 10px; margin-bottom: 20px;">$<?/* echo number_format($m['price_total_public']); */?></p>
                                                    <?/* } else { */?>
                                                        <p style="font-size: 25px; margin-top: 10px; margin-bottom: 20px; line-height: 32px">Call For Price<br><strong><?/* echo $phone; */?></strong></p>
                                                    --><?/* } */?>

                                                    <? if($m['show_price_public'] == 'true') {
                                                        $m['show_price_public'] = str_replace(',','',$m['show_price_public']); ?>
                                                        <? if ($m['price_chassis_public'] ==0 || $m['price_conversion_public']== 0){
                                                            if(count($discounts)!=0) { ?>

                                                        <? }}else{ ?>
                                                        <table class="table " style="margin-bottom: 0;">
                                                            <tbody><tr>
                                                                <td><span class="chassis-price">$<? echo number_format(($m['price_chassis_public'] )); ?></span></td>
                                                                <td>Chassis Base Price</td></tr>
                                                           <? if($m['conversion_status_public'] == "true"){ ?>
                                                            <tr>
                                                                <td><span class="chassis-price">$<? echo number_format(($m['price_conversion_public'])); ?></span></td>
                                                                <td>Conversion Prices Starting At</td>
                                                            </tr>
                                                            <? }else{?>
                                                               <tr>
                                                                   <td><span class="chassis-price">$<? echo number_format(($m['price_conversion_public'])); ?></span></td>
                                                                   <td>Conversion Price</td>
                                                               </tr>

                                                           <?} ?>
                                                            </tbody></table>
                                                            <? }?>
                                                        <? if(count($discounts)!=0) { ?>
                                                            <table class="table vehicleDiscountTable" style="margin-bottom: 0;">
                                                                <tbody><tr>
                                                                    <td><span class="price-before-discounts">$<? echo number_format(($m['price_total_public'] + $totaldiscounts)); ?></span></td>
                                                                    <td>Price Before Discounts</td></tr>
                                                                <? foreach ($discounts as $dis) { ?>
                                                                    <tr><td>-$<? echo number_format($dis['amount']); ?></td>
                                                                        <td><? echo $dis['name']; ?></td></tr>
                                                                <? } ?>
                                                                <tr></tr>
                                                               </tbody></table>
                                                        <? } ?>
                                                        <? if($m['price_total_public']!='0') {
                                                            $price = $m['price_total_public'];
                                                            $tenpercent = $price * .1;
                                                            $pricedown = $price - $tenpercent;
                                                            $factor = .012290758346019;
                                                            $tenyrmonthlypayment = number_format($pricedown * $factor);
                                                            ?>
                                                            <table><tr><td colspan="2"><span class="price-total-sellfor">$<? echo number_format($m['price_total_public']); ?></span> <span id="afterAllDiscountsText">After All Discounts</span></td>
                                                            </tr></table>
                                                            <?
                                                            echo '<div class="payments-as-low-as">Payments As Low As<br><span class="payment">$'.number_format($tenyrmonthlypayment,0)."</span>/mo</div>";
                                                        }  else {
                                                        $price = $m['price_total_public'];
                                                        $tenpercent = $price * .1;
                                                        $pricedown = $price - $tenpercent;
                                                        $factor = .012290758346019;
                                                        $tenyrmonthlypayment = number_format($pricedown * $factor);
                                                        if($m['price_total_public']!='0') { ?>
                                                            <table>
                                                                <tr>
                                                                    <td colspan="2"><span
                                                                                class="price-total-sellfor">$<? echo number_format($m['price_total_public']); ?></span>
                                                                        <span id="afterAllDiscountsText">After All Discounts</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <?
                                                            echo '<div class="payments-as-low-as">Payments As Low As<br><span class="payment">$' . number_format($tenyrmonthlypayment, 0) . "</span>/mo</div>";
                                                        } } }else { ?>
                                                        <p style="font-size: 25px; margin-top: 10px; margin-bottom: 20px; line-height: 32px">Call For Price<br><strong><? echo $phone; ?></strong></p><? }?>



                                                </div>
                                                <div class="block-xxs-14 block-sm-14 block-md-7">
                                                    <p>After All Rebates. Price and rebates posted
                                                        online are subject to change without notice or any guarantee.  Please call or stop in to confirm exact pricing.
                                                        Tax & License not included.</p>
                                                </div>


                                                <? if($m['show_price_public'] == 'true') { ?>
                                                    <div class="hidden-xs">
                                                        <div class="block-xxs-14">
                                                            <h2>Wheelchair Van Loans</h2>
                                                        </div>
                                                        <div class="block-xxs-14 block-sm-14 block-md-7">
                                                            <div class="top-right-apply-btn-vehiclemain" style="margin-bottom: 20px; margin-top: 0;">
                                                                <a class="big-apply-btn giant" href="javascript:apply_online()">
                                                                    <i class="fa fa-chevron-right"></i>
                                                                    <div class="subtext">Need A Loan? We Can Help!</div>
                                                                    Apply Online</a>
                                                            </div>
                                                            <p>We offer many financing options from multiple lenders. Competitive rates, flexible terms and a
                                                                simple application process make owning your next vehicle easier than ever.</p>

                                                            <div style="padding-left: 20px; margin-top: 20px;">
                                                                <li><a href="/wheelchair-van-financing">Learn More About Financing</a></li>
                                                                <li><a href="javascript: apply_online();">Apply Online Now</a></li>
                                                                <li><a href="/sell-your-van">Get Your Trade-In Value</a></li>
                                                            </div>



                                                            <?

                                                            $price = $m['price_total_public'];
                                                            $tenpercent = $price * .1;
                                                            $pricedown = $price - $tenpercent;

                                                            // Calculate 36 Months Payments
                                                            $factor = .0314980;
                                                            $thirtysixmonths = number_format($pricedown * $factor);

                                                            // Calculate 48 Months Payments
                                                            $factor = .0245775;
                                                            $fortyeightmonths = number_format($pricedown * $factor);

                                                            // Calculate 60 Months Payments
                                                            $factor = .0205408;
                                                            $sixtymonths = number_format($pricedown * $factor);

                                                            // Calculate 72 Months Payments
                                                            $factor = .0179018;
                                                            $seventytwomonths = number_format($pricedown * $factor);

                                                            ?>
                                                        </div>
                                                        <div class="block-xxs-14 block-sm-14 block-md-7">

                                                            <table class="table table-striped" style="margin-bottom: 0;">
                                                                <tr>
                                                                    <th>Per Month</th>
                                                                    <th>APR</th>
                                                                    <th>Length</th>
                                                                </tr>
                                                                <tr>
                                                                    <td>$<? echo $thirtysixmonths; ?>/mo</td>
                                                                    <td>8.33%</td>
                                                                    <td>36 Months</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>$<? echo $fortyeightmonths; ?>/mo</td>
                                                                    <td>8.33%</td>
                                                                    <td>48 Months</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>$<? echo $sixtymonths; ?>/mo</td>
                                                                    <td>8.33%</td>
                                                                    <td>60 Months</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>$<? echo $seventytwomonths; ?>/mo</td>
                                                                    <td>8.33%</td>
                                                                    <td>72 Months</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3"></td>
                                                                </tr>
                                                            </table>

                                                        </div>
                                                        <div class="block-xxs-14">
                                                            <div class="small" style="color: #535353;">Estimated payments calculated based on 10% down payment
                                                                with an APR of 8.33%. Financing rates will vary based on your credit score. Better rates
                                                                are likely available for well qualified buyers.</div>
                                                        </div>
                                                    </div>
                                                <? } ?>

                                                <div class="hidden-xs">
                                                    <div class="block-xxs-14">
                                                        <h2>Loan Payment Calculator</h2>
                                                    </div>
                                                    <div class="block-xxs-14 block-md-7">

                                                        <p>Use our payment calculator to get an estimate on how much your wheelchair van loan will
                                                            cost per month.  The payment amount you see will vary as it does not factor tax,
                                                            license and other fees.  Please give us a call for exact financing terms.</p>

                                                    </div>
                                                    <div class="block-xxs-14 block-md-7">
                                                        <div  style="background-color: #fff; padding: 10px; border: solid 1px rgba(0,0,0,.1)">
                                                            <form  name=calc method=POST id="rental-form" >
                                                                <fieldset>


                                                                    <div class="row">
                                                                        <div class="block-xxs-14 no-padding">
                                                                            <div class="block-xxs-4">
                                                                                <label class="formlabel">Amount *</label>
                                                                            </div>
                                                                            <div class="block-xxs-10">
                                                                                <div class="control-group">
                                                                                    <input type="text" class="input-xlarge myx numbersOnly" name="loan" id="loan">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>







                                                                    <div class="row">
                                                                        <div class="block-xxs-14 no-padding">
                                                                            <div class="block-xxs-4">
                                                                                <label class="formlabel">Years *</label>
                                                                            </div>
                                                                            <div class="block-xxs-10">
                                                                                <div class="control-group">
                                                                                    <select class="input-xlarge" name="months" id="months" required>
                                                                                        <option value="36">3 Years</option>
                                                                                        <option value="x">----</option>
                                                                                        <option value="12">1 Year</option>
                                                                                        <option value="36">3 Years</option>
                                                                                        <option value="60">5 Years</option>
                                                                                        <option value="72">6 Years</option>
                                                                                        <option value="120">10 Years</option>

                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class="row">
                                                                        <div class="block-xxs-14 no-padding">
                                                                            <div class="block-xxs-4">
                                                                                <label class="formlabel">Rate *</label>
                                                                            </div>
                                                                            <div class="block-xxs-10">
                                                                                <div class="control-group">
                                                                                    <select class="input-xlarge" name="rate" id="rate" required>
                                                                                        <option value="8.33">8.33%</option>
                                                                                        <option value="x">----</option>
                                                                                        <option value=".000000000000001">~0%</option>
                                                                                        <option value="1.9">1.9%</option>
                                                                                        <option value="2.9">2.9%</option>
                                                                                        <option value="3.9">3.9%</option>
                                                                                        <option value="4.9">4.9%</option>
                                                                                        <option value="5.9">5.9%</option>
                                                                                        <option value="6.9">6.9%</option>
                                                                                        <option value="7.9">7.9%</option>
                                                                                        <option value="8.33">8.33%</option>
                                                                                        <option value="8.9">8.9%</option>
                                                                                        <option value="9.9">9.9%</option>
                                                                                        <option value="10.9">10.9%</option>

                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    <div class="row">
                                                                        <div class="block-xxs-14 no-padding">
                                                                            <div class="block-xxs-4">
                                                                                <label class="formlabel">Monthly<br>Payment</label>
                                                                            </div>
                                                                            <div class="block-xxs-10">
                                                                                <div class="control-group">
                                                                                    <input type="text" class="input-xlarge" style="font-weight:bold; font-size:26px;" disabled name="pay" id="pay">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="block-xxs-14 no-padding">
                                                                            <div class="block-xxs-14">
                                                                                <button type="button" class="btn btn-primary btn-large pull-right" onClick="showpay()">Calculate</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>






                                                                </fieldset>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="block-xxs-14">
                                                    <h2>Trade In Your Current Vehicle</h2>
                                                </div>


                                                <div class="block-xxs-14 block-sm-14">
                                                    <p>We accept vehicle trades of all kinds, including trades on non-mobility vehicles.
                                                        Whether your current vehicle is a standard vehicle or wheelchair accessible, we
                                                        will take it in on trade.
                                                    </p>
                                                    <div style="padding-left: 20px; margin-top: 20px;">
                                                        <li><a href="/sell-your-van">Get Your Trade-In Value</a></li>
                                                    </div>
                                                </div>








                                            </div>



                                        </div>
                                    </div>





                                    <div id="_options" class="tab_container ini-hide">
                                        <h2>Vehicle Options</h2>
                                        <div class="row">
                                            <div class="block-xxs-14 no-padding">
                                                <div class="block-xxs-14">
                                                    <div>
                                                        <ul class="vehicle-options-list"> <?
                                                            $oc = 0;
                                                            foreach($optionaloptions as $o) {?>
                                                                <li class="block-xxs-14 block-md-7"><? echo $o['name']; ?></li>
                                                                <?  $oc++; }
                                                            foreach($standardoptions as $o) { ?>
                                                                <li class="block-xxs-14 block-md-7"><? echo $o['name']; ?></li>
                                                                <? $oc++; } ?>

                                                        </ul>
                                                        <? if($oc == 0) { ?>
                                                            <p>We are not able to determine options for this vehicle at this time.  Please contact us
                                                                for a complete list of options on this vehicle.</p>
                                                        <? } ?>
                                                    </div>
                                                </div>

                                                <div class="block-xxs-14">
                                                    <hr>
                                                    <div class="small">We do our best to ensure vehicle information including options are accurate, however
                                                        there are times when vehicle information and options may not be accurate.  Always verify the vehicle
                                                        options, year, make, model, miles, trim, price and conversion prior to purchase. We reserve the right to
                                                        change this page without notice.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>





                                    <? if($m['conversionid'] != '') { ?>
                                        <div id="_conversion" class="tab_container ini-hide">
                                            <?
                                            $conid = $m['conversionid'];
                                            $d = file_get_contents("http://www.blvd.com/api/conversions?conversionid=$conid");
                                            $conjson = json_decode($d,true);
                                            ?>
                                            <? if($conjson[0]['banner'] != 'http://www.blvd.com/uploads/') { ?>
                                                <img src="<? echo $conjson[0]['banner']; ?>" class="conversion-banner">
                                            <? } ?>
                                            <h2><? echo $conjson[0]['brand'] . ' ' . $conjson[0]['model']; ?> Options</h2>

                                            <div class="row">
                                                <div class="block-xxs-14 no-padding">
                                                    <div class="block-xxs-14">

                                                        <? if($m['conversionid'] != '0') {

                                                            ?>



                                                            <div class="conversion_body">
                                                                <img src="http://www.blvd.com/uploads/<? echo $conjson[0]['logo']; ?>" class="conversion-logo pull-right">
                                                                <div class="custom_content"><? echo $m['conversion_description']; ?></div>
                                                                <? echo $conjson[0]['description']; ?>
                                                            </div>
                                                        <? } ?>
                                                        <?


                                                        foreach ( $conjson[0]['specs_categories'] as $k=>$spec) {
                                                            echo '<h2>'.str_replace('_',' ',$k).'</h2>';
                                                            foreach ( $spec as $spec_cat) {
                                                                $c_key = $spec_cat['key'];
                                                                $c_val = $spec_cat['value'];
                                                                if($c_key == 'Product Link') {
                                                                    $c_val=strtolower($c_val);
                                                                    $blvd_slug = $conjson[0]['slug'];
                                                                    $c_val = "<a href=\"$blvd_slug\" target=\"_blank\" rel=\"nofollow\">Learn More</a>";
                                                                }
                                                                if($c_key != 'Warranty') { ?>
                                                                    <div class="rowAll rowA"><div class="rowTitle conversion"><? echo $c_key; ?></div><div class="rowFloat conversion"><p><? echo $c_val; ?></p></div></div>
                                                                <? } }

                                                        }

                                                        ?>

                                                    </div>

                                                    <div class="block-xxs-14">
                                                        <hr>
                                                        <div class="small">We do our best to ensure vehicle information including options are accurate, however
                                                            there are times when vehicle information and options may not be accurate.  Always verify the vehicle
                                                            options, year, make, model, miles, trim, price and conversion prior to purchase. We reserve the right to
                                                            change this page without notice.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <? } ?>





                                    <? if ($m['youtubeid'] != '') { ?>
                                        <div id="_video" class="tab_container ini-hide hideprint">

                                            <div class="videoWrapper">
                                                <iframe width="560" height="349" src="//www.youtube.com/embed/<? echo $m['youtubeid']; ?>?rel=0" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    <? } ?>










                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-xxs-14 block-sm-5 block-md-4 match-column-height">

                <div class="top-right-apply-btn-vehiclemain">
                    <a class="big-apply-btn giant" href="javascript:apply_online()">
                        <i class="fa fa-chevron-right"></i>
                        <div class="subtext">Need A Loan? We Can Help!</div>
                        Apply Online</a>
                </div>


                <div class="vfs-sidebar-wrapper stick-it">

                    <!-- Ask Question Form -->
                    <div class="hideprint">
                        <div class="ask-question-wrapper"><i class="fa fa-question-circle pull-right" style="font-size: 32px;"></i> Contact Dealer</div>
                        <div id="sidebarForm" class="sidebarfeature">
                            <div class="sub-text">We are standing by to assist you quickly via email.</div>
                            <?
                            $leadtype = 'Vehicle';
                            $force_form_location_id = $locationid;
                            include 'ask-question-form.php'; ?>
                        </div>
                    </div>
                    <!-- End ask Question Form -->
                </div>


            </div>
            <div class="block-xxs-14">
                <div class="small" style="margin-bottom: 20px;">The Information on this page is deemed accurate but is not guaranteed.
                    We reserve the right to change this page without notice.
                    Always verify pricing, options, equipment, conversion and other details about the vehicle with a sales representative
                    before making a purchase. Wheelchair Van Conversion data provided by the <a href="http://www.blvd.com" target="_blank">BLVD.com API</a>.
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        function ini() {
            var screenW = $('body').outerWidth();
            if(screenW > 767) {
                $('.ini-hide').hide();
            }
        }
        ini();

        $( ".do_print_btn" ).click(function() {
            window.print();
        });

        $( "#sharebtn" ).click(function() {
            $('#share').modal('show');
        });

        $('.numbersOnly').keyup(function () {
            if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
                this.value = this.value.replace(/[^0-9\.]/g, '');
            }
        });

        $('.jumpbtn').click(function(e) {
            e.preventDefault();
            var t = $(this).attr('id');
            $('.vehicletabs').removeClass('active');
            $("#"+t).addClass('active');
            $(".tab_container").hide();
            $("#_"+t).show().addClass('active');
            match_column_heights();
        });



    });

    $(window).load(function() {
        match_column_heights();

        var screenW = $('body').outerWidth();
        if(screenW > 767) {
            $(".stick-it").stick_in_parent({offset_top:10});
        }
    });

    function match_column_heights() {
        var screenW = $('body').outerWidth();
        if(screenW > 767) {
            var maxHeightb = 0;
            $(".match-column-height").height('');
            $(".match-column-height").each(function () {
                if ($(this).height() > maxHeightb) {
                    maxHeightb = $(this).height();
                }
            });
            $(".match-column-height").height(maxHeightb);
        }
    }





    //Generate the string for Get Price into comments box
    var vanyear = '<? echo $m['year']; ?>';
    var vanmake = '<? echo $m['make']; ?>';
    var vanmodel = '<? echo $m['model']; ?>';
    var vanstock = '<? echo $m['stock']; ?>';
    var vanconversion = '<? echo $m['conversion']; ?>';
    var vanstring = ' ' + vanyear + ' ' + vanmake + ' ' + vanmodel + ' - Stock:' + vanstock + ' - Conversion:' + vanconversion;

    function requestprice() {
        $('#modalContactTitle').html( '<span class="glyphicon glyphicon-usd"></span> Vehicle Price Request Form');
        $('#modalContactParagraph').html( '<span class="superior-blue"><strong>We are unable to display pricing on New vehicles due to the complexity of factory, conversion and mobility rebates.</strong></span>  However we would love to get you a current price for any vehicle you see.  Please provide a little information so we know who to send this to.');
        $('.the-contact-location-comments').html( 'Would you please provide current pricing information on this vehicle.' + vanstring);
        $('#contact-location').modal('show');
    }

    function goBack() {
        window.history.back();
    }


    // Payment Calculator
    Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {            var n = this, decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces, decSeparator = decSeparator == undefined ? "." : decSeparator, thouSeparator = thouSeparator == undefined ? "," : thouSeparator, sign = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "", j = (j = i.length) > 3 ? j % 3 : 0;return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");};function showpay() {if ((document.calc.loan.value == null || document.calc.loan.value.length == 0) || (document.calc.months.value == null || document.calc.months.value.length == 0) || (document.calc.rate.value == null || document.calc.rate.value.length == 0)) { document.calc.pay.value = "Incomplete data";} else {var princ = document.calc.loan.value;var term  = document.calc.months.value;var intr   = document.calc.rate.value / 1200;var calc = princ * intr / (1 - (Math.pow(1/(1 + intr), term)));var formattedMoney = '$' + calc.formatMoney(2,',','.');document.calc.pay.value = formattedMoney;}}

    $("#my-button").click(function(e) {
        e.preventDefault();
        $('.vehicletabs').removeClass('active');
        $(".price-btn").addClass('active');
        $(".tab_container").hide();
        $("#_price").show().addClass('active');
        match_column_heights();
        $('html, body').animate({
            scrollTop: $(".price-btn").offset().top
        }, 800);

    });


</script>
<? include 'footer.php'; ?>
<div id="superimage-wrapper">
    <div id="superimage-inner">
        <div id="superimgageleft" class="superimagearrow"><img src="//www.dealerexpress.net/sharedimages/supergalleryleft.png"></div>
        <div id="superimgageright" class="superimagearrow"><img src="//www.dealerexpress.net/sharedimages/supergalleryright.png"></div>
        <div id="superimgageclose"><img src="//www.dealerexpress.net/sharedimages/supergalleryclose.png"></div>
        <img src="" id="supermainimg">
    </div>
</div>

</body>
</html>