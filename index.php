<? include 'Connect.php'; ?>
<!doctype html>
<html>
<head>
    <title>Wheelchair Vans and Handicap Van Sales <? echo $states; ?> | <? echo $dealername; ?></title>
    <meta name="description" content="Wheelchair vans and <? echo $dealername; ?> can simplify you handicap driving needs!  We service <? echo $states; ?> with quality wheelchair vans, scooter lifts, wheelchair van rentals and more. We offer sales, service and rentals of all top handicap van brands.">
    <? require_once 'head-tags.php'; ?>
    <link rel="stylesheet" href="/css/vans-for-sale.css">
</head>
<body>
<? include 'header.php'; ?>
    <?
    // Gather banner images into array.
    $sql = $mysqli->query( "SELECT name, url, image FROM banners ORDER BY arrange ASC LIMIT 10");
    $icounter = 0;
    $bannerarray = array();
    while ($one = $sql->fetch_assoc()) {
        $name = htmlspecialchars($one['name']);
        $url = htmlspecialchars($one['url']);
        $image = htmlspecialchars($one['image']);
        if($url == 'http://') $url= $results_dot_php;
        $url= str_replace('http://', '', $url);
        $url= str_replace('www.', '', $url);
        $url= str_replace($domainname, '', $url);
        $url = ltrim($url, "/");
        $bannerarray[$icounter]['image'] = $image;
        $bannerarray[$icounter]['name'] = $name;
        $bannerarray[$icounter]['url'] = $url;
        $icounter++;
    }
    ?>
<div class="home-page-wrapper">






<div class="banner-tiles" style="overflow: hidden">

                <div class="banner-slider">
                    <div class="bannersloading"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                    <div id="image-list" >

                        <? if (count($bannerarray) > 1) {

                        foreach($bannerarray as $i) {
                            ?>
                            <div class="single-image" >
                                <img src="/Express2.0/imageup/<? echo $i['image']; ?>" alt="<? echo $i['name']; ?>" class="img-responsive banner-wrap-img"/>
                                <div class="banner-title-bar">
                                    <a href="<? echo $i['url']; ?>">Learn More <i class="fa fa-chevron-right"></i></a>
                                    <span class="banner-title"><? echo $i['name']; ?></span>
                                </div>
                            </div>
                        <? } ?>
                        <div class="banner-nav-btn left"><i class="fa fa-chevron-left"></i></div>
                        <div class="banner-nav-btn right"><i class="fa fa-chevron-right"></i></div>
                        <div class="banner-nav-btn expand expand-banner-btn" style="display: none;"><i class="fa fa-expand"></i></div>
                        <? } else { ?>
                            <? if (count($bannerarray) == 1) { ?>
                                <a href="<? echo $bannerarray[0]['url']; ?>"><img src="/Express2.0/imageup/<? echo $bannerarray[0]['image']; ?>" class="img-responsive" alt="<? echo $bannerarray[0]['name']; ?> <? echo $states; ?>" /></a>
                                <div class="banner-title-bar">
                                    <a href="<? echo $bannerarray[0]['url']; ?>">Learn More <i class="fa fa-chevron-right"></i></a>
                                    <span class="banner-title"><? echo $bannerarray[0]['name']; ?></span>
                                </div>
                         <? } ?>
                        <? }?>
                    </div>




                </div>



    <div style="clear: both"></div>
</div>









  <!-- <div class="" style="position: relative; width: 100%; overflow: hidden;">

   <div class="tiles fully-transparent"  style="position: relative; z-index: 5; background-color: transparent; margin-left: 0px; margin-right: 0px;">
        <div class="row" style="margin: 0px !important; padding: 0px !important; background-color: transparent;">
            <div class="block-xxs-14 block-lg-14 no-padding">
                <div class="block-sm-14 block-md-14 block-lg-14 block-sm-14  no-padding">
                    <div class="block-lg-14 block-md-14 block-sm-14 no-padding"><div class="tile-banner-full-height gray noborder rel" style="overflow: hidden;">



                            <div class="banner-slider">
                                <div class="bannersloading"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                                <div id="image-list">


                                    <?/*
                                    foreach($bannerarray as $i) {
                                    */?>
                                        <div class="single-image">
                                            <img src="/Express2.0/imageup/<?/* echo $i['image']; */?>" alt="<?/* echo $i['name']; */?>" class="img-responsive banner-wrap-img"/>
                                            <div class="banner-title-bar">
                                                <a href="<?/* echo $i['url']; */?>">Learn More <i class="fa fa-chevron-right"></i></a>
                                                <span class="banner-title"><?/* echo $i['name']; */?></span>
                                            </div>
                                        </div>
                                    <?/* } */?>


                                </div>

                                <div class="banner-nav-btn left"><i class="fa fa-chevron-left"></i></div>
                                <div class="banner-nav-btn right"><i class="fa fa-chevron-right"></i></div>
                                <div class="banner-nav-btn expand expand-banner-btn" style="display: none;"><i class="fa fa-expand"></i></div>



                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear: both"></div>

            </div>
        </div>
    </div>
   </div>-->


        
      


        
 <!--
 <img src="/img/videos/vid-overlay.png" style="position: absolute; width: 100%; top: 0; left: 0; right: 0; bottom: 0; z-index:999 ">
        <img src="/img/product-special.jpg" class="img-responsive"> 

        <div style="position: absolute;  text-align: center; font-size: 16px; margin-left: auto; margin-right: auto; margin-top: 50px; color: #000; opacity: 1; background-color: #fff;">Example Video... Hatfield's Custom Video Is Almost Complete.</div>
        <video id="home-main-vid" src="/img/videos/braunability-vmi-wheelchair-vans.mp4" poster="/img/videos/braunability-vmi-home-video-poster.jpg" autoplay loop style="width: 100%;"></video>
        -->
    <!--</div>

-->
    <div class="hidden-xs hidden-sm hidden-md hidden-lg">
        <? if (count($bannerarray) != 0) { ?>
            <div class="bx-wrapper-wrapper" style="border: none;">
                <ul class="bxslider" >
                    <? foreach($bannerarray as $pic) {  ?>
                        <li><a href="<? echo $pic['url']; ?>" title="<? echo $pic['name']; ?> <? echo $states; ?>">
                                <img src="/Express2.0/imageup/<? echo $pic['image']; ?>" /></a></li>
                    <? } ?>
                </ul>
            </div>
        <? } ?>
    </div>





    <div class="full-width main-bg-2" style=" position:relative; margin-top:0px;">
     <div style="background-color: #fff; height: 8px; position: absolute; top: 10px; left: 0; right: 0;"></div>
        <div style="background-color: #fff; height: 8px; position: absolute; bottom: 10px; left: 0; right: 0;"></div>
        <div class="container fully-transparent" style="padding-top: 79px; padding-bottom: 50px; background-color: transparent">
            <div class="div-img hidden-xs hidden-sm">
            <img src="/img/lady-in-wheelchair-smile.png" class="smile-lady hidden-sm">
            </div>
            <div class="tiles">
                <div class="row">
                    <div class="block-xxs-14 no-padding">
                        <div class="block-xxs-14 block-md-8">
                            <h1 class="home-main-h1">Wheelchair Van Sales & Service of <? echo $states; ?></h1>
                            <p>Stay Mobile with Automotive Lifts, Amerivan Conversions, and Power Wheelchairs in our Kansas City, Wichita, Columbus and Salina locations. Illness, injury, and age all have a dramatic impact on your ability to move. However, this doesn't mean you have to let life slow you down.</p>
                        </div>
                        <div class="block-xxs-14 block-md-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







    <div class="full-width" style="background-color:#fff; margin-bottom: 0px;">
        <div class="container fully-transparent" style="padding-top: 40px; padding-bottom: 40px;">
            <div class="tiles">
                <div class="row">
                    <div class="block-xxs-14 no-padding">
                        <div class="block-xxs-14">
                            <h2 class="home-h2">Mobility Products For Vehicles</h2>
                        </div>
                        <div class="negative-margin">
                        <?
                        $res = $mysqli->query("SELECT * FROM post WHERE parentid='1128' ORDER BY arrange ASC");
                        $trows = $res->num_rows;
                        while ($row = $res->fetch_assoc()) {
                        $bpostid = $row["id"];
                        $bname = $row["name"];
                        $bslug = $row["slug"];
                        $bheading = $row["heading"];
                        $bparentid = $row["parentid"];
                        $blogo = $row["logo"];
                        ?>
                        <div class="block-xxs-7 block-sm-2  block-md-2 block-lg-2 text-center">
                            <a href="/<? echo $bslug; ?>">
                                <div class="tbox match-column-height-vans">
                                    <img src="/uploads/<? echo $blogo; ?>" class="img-responsive bdr" >
                                    <div class="under-image"><? echo $bname; ?></div>
                                </div>
                            </a>
                        </div>
                        <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="full-width main-bg-2" style="position:relative; background-color: #1c3f95">
        <div style="background-color: #fff; height: 8px; position: absolute; bottom: 10px; left: 0; right: 0;"></div>
        <div style="background-color: #fff; height: 8px; position: absolute; top: 10px; left: 0; right: 0;"></div>
        <div class="container tiles fully-transparent" style="position: relative; z-index: 5;  padding: 70px 0 90px 0;">
            <div class="row" style="margin: 0px !important; padding: 0px !important; background-color: transparent;">
                <div class="block-xxs-14 block-md-2 text-center" style=""></div>
                <div class="block-xxs-14 block-md-10 text-center" style="">
                    <h2 style="font-size: 23px !important; line-height: 35px;" class="home-h2 xl-h2 text-white">Jay Hatfield Mobility is the largest supplier of wheelchair accessible vans and power wheelchairs in the four state area. Whether you need minor assistance walking, or you want vehicle customizations for increased independence, you can count on us. Our team will always supply you with quality products.</h2>

                </div>
                <div class="block-xxs-14 block-md-2 text-center" style=""></div>

            </div>
        </div>
    </div>


    <div class="full-width" style="background-color:#fff; margin-bottom: 0px;">
        <div class="container fully-transparent" style="padding-top: 50px; padding-bottom: 80px;">
            <div class="tiles">
                <div class="row">
                    <div class="block-xxs-14 no-padding">
                        <div class="block-xxs-14">
                            <h2 class="home-h2">Durable Medical Equipment</h2>
                        </div>
                        <div class="negative-margin">
                            <?
                            $res = $mysqli->query("SELECT * FROM post WHERE parentid='62' ORDER BY arrange ASC");
                            $trows = $res->num_rows;
                            while ($row = $res->fetch_assoc()) {
                                $bpostid = $row["id"];
                                $bname = $row["name"];
                                $bslug = $row["slug"];
                                $bheading = $row["heading"];
                                $bparentid = $row["parentid"];
                                $blogo = $row["logo"];
                                ?>
                                <div class="block-xxs-7 block-sm-2  block-md-2 block-lg-20p text-center">
                                    <a href="/<? echo $bslug; ?>">
                                        <div class="tbox match-column-height-vans">
                                            <img src="/uploads/<? echo $blogo; ?>" class="img-responsive bdr" >
                                            <div class="under-image"><? echo $bname; ?></div>
                                        </div>
                                    </a>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






<div class="full-width main-bg-2" style=" position:relative;  padding-top: 30px; padding-bottom: 30px;">
    <div style="background-color: #fff; height: 8px; position: absolute; bottom: 10px; left: 0; right: 0;"></div>
    <div style="background-color: #fff; height: 8px; position: absolute; top: 10px; left: 0; right: 0;"></div>
    <div class="tiles">
        <h2 class="home-h2 xl-h2 text-white text-center">4 Locations For Mobility</h2>
    </div>
    <div id="map-canvas" ></div>
</div>



<div class="full-width full-width-white" style="background-color: #fff">
    <div class="container fully-transparent" style="padding-top: 60px; padding-bottom: 90px;">
        <div class="tiles">
            <div class="row">
                <div class="block-xxs-14 no-padding">
                    <div class="block-xxs-14 block-md-9 text-left">
                        <?
                        $sqltwow = $mysqli->query( "SELECT vehicleid
                                                    FROM vehiclelookup
                                                    WHERE available!='sold' AND showonline='true'");
                        $totalvans = $sqltwow->num_rows;
                        ?>
                        <h2 class="home-h2">Current Vehicles On Special</h2>
                        <p><? echo $totalvans; ?> Wheelchair Vans In-Stock Right Now</p>


                    </div>
                    <div class="block-xxs-14 block-md-5 text-left">
                        <div class="float-links text-right">
                            <a href="<? echo $results_dot_php; ?>">View All <i class="fa fa-chevron-circle-right"></i></a>
                            <a href="<? echo $results_dot_php; ?>/new">New <i class="fa fa-chevron-circle-right"></i></a>
                            <a href="<? echo $results_dot_php; ?>/used">Used <i class="fa fa-chevron-circle-right"></i></a>
                        </div>
                    </div>
                    <div style="clear: both;"></div>



                    <?

                    $resffg = $mysqli->query("SELECT * FROM vehicle_meta WHERE type='on_specials_page' AND value='Yes'");
                    $pcount = $resffg->num_rows;

                    $p='';
                    while($rowffg = $resffg->fetch_assoc()) {
                        $p .= $rowffg['vehicleid'].',';
                    }
                    $post_ids = substr($p,0,-1);
                    if($pcount !=0) {
                        $specialsql = " AND vehicleid IN($post_ids)";
                    } else {
                        $specialsql = "";
                    }







                    $sqltwow = $mysqli->query("SELECT * FROM vehiclelookup WHERE available!='sold' AND showonline='true' $specialsql ORDER BY RAND() LIMIT 4");






                    while ($deone = $sqltwow->fetch_assoc()) {
                        $id = $deone['vehicleid'];
                        $hold = $deone['hold'];
                        $location = $deone['location'];
                        $available = $deone['available'];

                        $m= array();
                        $sqltwox = $mysqli->query( "SELECT * FROM vehicle_meta WHERE vehicleid='$id'");
                        while ($met = $sqltwox->fetch_assoc()) {
                            $m[$met['type']] = $met['value'];
                        }

                        // Set The Default Image If There Are None Loaded Yet.

                        $img = $mysqli->query( "SELECT large FROM pictures WHERE vehicleid='$id' ORDER BY arrange ASC LIMIT 1");
                        $img2 = $img->fetch_assoc();
                        $thumb = $img2['large'];
                        $thumb = "/Express2.0/imageup/$thumb";
                        if($thumb=='/Express2.0/imageup/') { $thumb = '/img/novan.jpg'; }


                        if($m['newused'] == '') { $m['newused'] = 'Call'; }
                        if($m['conversion_newused'] == '') { $m['conversion_newused'] = 'Call'; }

                        $c= 0;

                        ?>
                        <div class="block-xxs-14 block-xs-7 block-sm-7 block-md-third block-lg-quarter">
                            <div class="vehicleresultsmainx" style="position:relative;">
                                <div class="year-float"><? echo $m['year']; ?></div>
                                <? if($available=='sold') { ?><div class="sold-float">Sold</div><? } ?>
                                <a href="/wheelchair-vehicle-for-sale/<? echo base64url_encode($id); ?>">
                                    <div class="vehiclelist-image-wrapper">
                                        <img src="<? echo $thumb; ?>" alt="<? echo $m['year']; ?> <? echo $m['make']; ?> <? echo $m['model']; ?> <? echo $m['conversion']; ?>wheelchair van for sale" class="img-responsive">
                                    </div>
                                    <div class="vehiclelist-details">
                                        <div class="make-model full-row no-border">
                                            <div class="condition-90 chassis <? echo $m['newused']; ?>"><? echo $m['newused']; ?></div>
                                            <? echo $m['make']; ?> <? echo $m['model']; ?>
                                        </div>
                                        <div class="conversion full-row no-border">
                                            <div class="condition-90 conv <? echo $m['conversion_newused']; ?>"> <? echo $m['conversion_newused']; ?></div>
                                            <? echo $m['conversion']; ?>
                                        </div>

                                        <div class="full-row">
                                            <div class="half-row">Trim:</div>
                                            <div class="half-row"><? echo $m['trim']; ?></div>
                                        </div>
                                        <div class="full-row">
                                            <div class="half-row">Location:</div>
                                            <div class="half-row"><? echo $location; ?></div>
                                        </div>
                                        <div class="full-row">
                                            <div class="half-row">Miles:</div>
                                            <div class="half-row"><? if($m['miles']!='' ) { echo number_format($m['miles']); } ?></div>
                                        </div>
                                        <div class="full-row">
                                            <div class="half-row">Stock:</div>
                                            <div class="half-row"><? if($m['stock']!='' ) { echo '#'.$m['stock']; } ?></div>
                                        </div>
                                        <div class="full-row">
                                            <div class="half-row">Status:</div>
                                            <div class="half-row">
                                                <?
                                                if($available=='sold') {
                                                    echo '<span style="color: #a90736;"><i class="fa fa-exclamation-triangle"></i> SOLD</span> ';
                                                } else {
                                                    if($hold!='available') {
                                                        echo '<span style="color: #a90736;"><i class="fa fa-exclamation-triangle"></i> Vehicle on Hold</span> ';
                                                    } else {
                                                        echo $m['arrival_status']; if($m['arrival_status'] == 'On Order') { echo ' - ETA: '.$m['eta']; }
                                                    }
                                                }

                                                ?>
                                            </div>
                                        </div>
                                        <div class="full-row">
                                            <div class="half-row">Price:</div>
                                            <div class="half-row"><strong>
                                                    <? if($m['show_price_public'] == 'true') {
                                                        $m['show_price_public'] = str_replace(',','',$m['show_price_public']);
                                                        ?>
                                                        $<? echo number_format($m['price_total_public']); ?>
                                                    <? } else { ?>
                                                        Call For Price
                                                    <? } ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <? $c++;  } ?>



                </div>
            </div>
        </div>
    </div>
</div>

















    <div class="full-width main-bg-2" style="position:relative;">
        <div style="background-color: #fff; height: 8px; position: absolute; bottom: 10px; left: 0; right: 0;"></div>
        <div style="background-color: #fff; height: 8px; position: absolute; top: 10px; left: 0; right: 0;"></div>
    <div class="container tiles fully-transparent"  style="position: relative; z-index: 5; background-color: transparent; padding-top: 50px; padding-bottom: 80px;">
        <div class="row" style="margin: 0px !important; padding: 0px !important; background-color: transparent;">
            <div class="block-xxs-14  no-padding">
                <h2 class="home-h2 text-center">Kansas & Missouri's Finest & Most Affordable Wheelchair Vans</h2>
                <div class="block-sm-14 block-md-14 block-lg-14 block-sm-14  no-padding hidden-xs hidden-sm hidden-md hidden-lg ">
                    <div class="block-lg-14 block-md-14 block-sm-14 no-padding"><div class="tile-banner-full-height gray noborder rel" style="overflow: hidden;">
                            <div class="banner-slider">
                                <div class="bannersloading"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                                <div id="image-list">


                                    <?
                                    foreach($bannerarray as $i) {
                                    ?>
                                        <div class="single-image">
                                            <img src="/Express2.0/imageup/<? echo $i['image']; ?>" alt="<? echo $i['name']; ?>" class="img-responsive banner-wrap-img"/>
                                            <div class="banner-title-bar">
                                                <a href="<? echo $i['url']; ?>">Learn More <i class="fa fa-chevron-right"></i></a>
                                                <span class="banner-title"><? echo $i['name']; ?></span>
                                            </div>
                                        </div>
                                    <? } ?>


                                </div>

                                <div class="banner-nav-btn left"><i class="fa fa-chevron-left"></i></div>
                                <div class="banner-nav-btn right"><i class="fa fa-chevron-right"></i></div>
                                <div class="banner-nav-btn expand expand-banner-btn" style="display: none;"><i class="fa fa-expand"></i></div>



                            </div>
                        </div>
                    </div>
                </div>
                <div style="clear: both"></div>
                <p class="text-center" style="margin-top: 30px; !important;" >At Jay Hatfield Mobility, we go above and beyond your typical walkers and canes (though we have those, too). We want to give you complete freedom. Once you choose the equipment you need, our Administrative Assistants and Mobility Specialists will help you take care of the paperwork. When you're ready, we'll also provide free delivery and equipment setup. Come to Us for Convenient, Quality Service</p>


            </div>
        </div>
    </div>
        </div>















    <div class="full-width" style="background-color:#fff; margin-bottom: 0px;">
        <div class="container fully-transparent" style="padding-top: 0px; padding-bottom: 60px; background-color: #fff">
            <div class="tiles">
                <div class="row">

                    <div class="block-xxs-14 no-padding">



                    </div>

                </div>
            </div>
        </div>
    </div>






</div>

<script>
    $(document).ready(function() {

        $('.bxslider').bxSlider({
            mode: 'horizontal',
            speed: 500,
            pause: 6000,
            controls: true,
            pager: false,
            auto: true
        });


        $(".single-image").first().addClass("zooming");

        var n=1;
        var total_slides;
        setInterval(function(){

            $("#image-list").length;
            $("#image-list").each(function(){
                total_slides= $(this).children('.single-image').length;
                $(".single-image").removeClass("zooming");
                $(".single-image").eq(n).addClass("zooming");
                    n++;
                if(n==total_slides){
                    n=0;
                }
            })
        },10000);

        //Banner Slider
        $('.banner-nav-btn.right').click(function() {
            var total= $("#image-list").children(".single-image").length;
            //  console.log("next "+total+" "+n);
            if(n < total-1){
                n++;
            }else{
                n = 0;
            }
            $(".single-image").removeClass("zooming");
            $(".single-image").eq(n).addClass("zooming");
        });
        $('.banner-nav-btn.left').click(function() {
            var total= $("#image-list").children(".single-image").length;
            if(n > 0){
                n--;
            }else{
                n = total-1;
            }
            $(".single-image").removeClass("zooming");
            $(".single-image").eq(n).addClass("zooming");


        });

/*
             //Banner Slider
             $('#image-list').children().first().addClass('zooming');
             var bannerimg = 1;
             $('.banner-nav-btn.right').click(function() {
             nextBanner();
             clearInterval(bannertimer);
             });
             $('.banner-nav-btn.left').click(function() {
             prevBanner();
             clearInterval(bannertimer);
             });
            function nextBanner() {
                    var total = $('#image-list').children().length;
                    if(bannerimg < total) {
                        bannerimg = bannerimg + 1;
                    } else {
                        bannerimg = 1;
                    }
                    var t = $('.zooming');
                    t.removeClass('zooming');
                    $('.single-image:nth-child(' + bannerimg + ')').addClass('zooming');
            }

            function prevBanner() {

                var total = $('#image-list').children().length;
                if(bannerimg > 1) {
                    bannerimg = bannerimg - 1;
                } else {
                    bannerimg = total;
                }
                var t = $('.zooming');
                t.removeClass('zooming');
                $('.single-image:nth-child(' + bannerimg + ')').addClass('zooming');
            }

*/
            <? // Auto progress the banner slides ?>
            var bannertimer = '';
            function addBannerTimer() {
                bannertimer = setInterval(function() {
                   // nextBanner();
                },7000);
            }
            addBannerTimer();



            <? // Enlarge and shink the banner ?>
            $('.expand-banner-btn').click(function() {
                clearInterval(bannertimer);
                var src = $('.single-image:nth-child(' + bannerimg + ')').find('img').prop('src');
                var title = $('.single-image:nth-child(' + bannerimg + ')').find('.banner-title').html();
                console.log(title);
                $('#enlarged-banner-image').prop('src',src);
                $('#enlarge-title').html(title);
                $('#enlarge-banner-container').show();
                $('#main-tile-content').hide();

            });
            $('.close-enlarged-image').click(function() {
                $('#enlarge-banner-container').hide();
                $('#main-tile-content').show();
                addBannerTimer();
            });


        });






        var v = document.getElementById( "home-main-vid" );
        var $homevideo = Popcorn("#home-main-vid");
        var mainIsPlaying = false;
        $('#home-main-vid').click(function() {
            if(mainIsPlaying == false) {
                $homevideo.play();
                mainIsPlaying = true;
            } else {
                $homevideo.pause();
                mainIsPlaying = false;
            }
        });



    </script>

<? include 'footer.php'; ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSghRQ5P0ZFHrd5JTrAh3xfw6F02cTlyw"></script>
<script type="text/javascript" src="/js/mapping/map-style.js"></script>
<script type="text/javascript" src="/js/mapping/map-gen.js"></script>
</body>
</html>