<?


    // Enforce the www. protocol within the public side of the site.
    if (strpos($_SERVER['HTTP_HOST'], 'www.') === false) {
        $redirect = "http://www.".$domainname.$_SERVER['REQUEST_URI'];
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $redirect");
    }
?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5NBQ6JW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->



<div class="global"><? // This tag gets closed in the footer ?>
    <div class="very-top-header">
        <div class="cornerbg"></div>
    </div>
    <div class="nav-wrapper-bg"></div>

    <header id="headercontainer">
    
  

        <!--   CONTENT AREA OF THE HEADER INCLUDING LOGO, SOCIAL, PHONE, AND CENTER IMG.  ---->
        <div class="container fully-transparent header-height" >
            <div class="header-box-color">
                <div id="logo">
                    <a href="/"><img src="/img/logo.png"  alt="<? echo $dealername; ?> Logo" class="img img-responsive"></a>
                </div>
                <div class="company-name"><? echo $dealername; ?></div>
                <div class="company-slogan">Wheelchair Vans, Vehicle Lifts, Rehab Wheelchairs, Mobility Driving Controls & Much More. </div>
                
                
                <a class="mobilemenubutton hide-print" href="javascript:void(0);"><img src="/img/mobile-menu-btn.png"></a>
                <!--<img class="header-van hidden-xs hidden-sm hidden-md" src="/img/wheelchair-van-top.png" alt="Wheelchair Van sitting in <? echo $states; ?>">-->
                <div class="header-locations-wrapper">
                    <div class="location-single">
                        <div class="socialmediaicons">
                            <a href="https://www.facebook.com/Jay-Hatfield-Mobility-Lees-Summit-MO-1448301695222710/"><img src="/img/icons/facebook.png" alt="Join us on Facebook<br>Read Real Customer Reviews"/></a>
                            <a href="https://plus.google.com/116407668370809498351"><img src="/img/icons/google-plus.png" alt="Read and Write Reviews<br>On Our Google Plus Page."/></a>
                        </div>
                        <div class="cityname">Lee's Summit, MO</div>
                        <div class="location-buttons">
                            <a href="/locations/columbus-ks" class="locationphone"><i class="fa fa-phone"></i> 1-816-600-5124</a>
                        </div>
                    </div>
                    <div class="location-single">
                        <div class="socialmediaicons">
                            <a href="https://www.facebook.com/Jay-Hatfield-Mobility-129227767120959/"><img src="/img/icons/facebook.png" alt="Join us on Facebook<br>Read Real Customer Reviews"/></a>
                            <a href="https://plus.google.com/118368947781883482951"><img src="/img/icons/google-plus.png" alt="Read and Write Reviews<br>On Our Google Plus Page."/></a>
                        </div>
                        <div class="cityname">Columbus, KS</div>
                        <div class="location-buttons">
                            <a href="/locations/lees-summit-mo" class="locationphone"><i class="fa fa-phone"></i> 1-800-545-4227</a>
                        </div>
                    </div>
                    <div class="location-single">
                        <div class="socialmediaicons">
                            <a href="https://www.facebook.com/Jay-Hatfield-Mobility-Salina-KS-668217376720647/"><img src="/img/icons/facebook.png" alt="Join us on Facebook<br>Read Real Customer Reviews"/></a>
                            <a href="https://plus.google.com/108434540325571508300"><img src="/img/icons/google-plus.png" alt="Read and Write Reviews<br>On Our Google Plus Page."/></a>
                        </div>
                        <div class="cityname">Salina, KS</div>
                        <div class="location-buttons">
                            <a href="/locations/salina-ks" class="locationphone"><i class="fa fa-phone"></i> 1-785-452-9888</a>
                        </div>
                    </div>
                    <div class="location-single">
                        <div class="socialmediaicons">
                            <a href="https://www.facebook.com/Jay-Hatfield-Mobility-Wichita-KS-1729706413711336/"><img src="/img/icons/facebook.png" alt="Join us on Facebook<br>Read Real Customer Reviews"/></a>
                            <a href="https://plus.google.com/100842526172447242844"><img src="/img/icons/google-plus.png" alt="Read and Write Reviews<br>On Our Google Plus Page."/></a>
                        </div>
                        <div class="cityname">Wichita, KS</div>
                        <div class="location-buttons">
                            <a href="/locations/wichita-ks" class="locationphone"><i class="fa fa-phone"></i>1-866-885-2593

</a>
                        </div>
                    </div>
                    
                </div>
                <? //if($fullpath != '') { ?>
                <div class="socialiconstop hidden-xs hidden-sm" style="z-index: 9999;">
<!--                    <a href="https://www.facebook.com/Jay-Hatfield-Mobility-129227767120959/" class="header-text-trigger" target="_blank"><img src="/img/icons/facebook.png" alt="Join us on Facebook<br>Read Real Customer Reviews"/></a>-->
<!--                    <a href="https://plus.google.com/b/118368947781883482951/118368947781883482951/posts" target="_blank" class="header-text-trigger" ><img src="/img/icons/google-plus.png" alt="Read and Write Reviews<br>On Our Google Plus Page."/></a>-->
                   <a href="" target="_blank" class="header-text-trigger" ><img src="/img/icons/nmeda.png" alt=""/></a>
                    <a href="" target="_blank" class="header-text-trigger" ><img src="/img/icons/qap.png" alt=""/></a>

                    <? /*
 <a href="http://www.blvd.com"class="header-text-trigger" target="_blank"><img src="//www.blvd.com/img/blvd-logo-mobile.png" alt="5 Star Rating At BLVD.com<br>The Online Mobility Resource"/></a>
 */
 ?>
                </div>
    <? // } ?>
                <div style="clear: both"></div>
                <div id="header-animation" class="active hidden-xs hidden-sm"></div>
                <div style="clear: both"></div>
            </div>

            <div class="nav-wrapper">

                <!--   MAIN NAVIGATION BUTTONS - ALSO TRIGGERS FOR DROPDOWNS SEEN BELOW ---->
                <nav class="navbar navbar-default navbar-static-top tm_navbar clearfix"  role="navigation">
                    <ul class="nav sf-menu clearfix">
                        <li><a class="<? if($firstbreadcrumbslug == '') { echo 'active'; } ?>" href="/" title="<? echo $dealername; ?> Home Page"><i class="fa fa-home"></i></a></li>
                        <li><a class="<? if($firstbreadcrumbslug == str_replace('/','',$results_dot_php) && $second == 'new' && $second != 'specials') { echo 'active'; } ?>" href="/wheelchair-vans-for-sale-kansas-missouri/new" title="New Wheelchair Vans For Sale">New Vans</a></li>
                        <!--<li><a class="<?/* if($firstbreadcrumbslug == str_replace('/','',$results_dot_php) && $second != 'specials') { echo 'active'; } */?>" href="/wheelchair-vans-for-sale-kansas-missouri/used" title="Pre-Owned Wheelchair Vans For Sale">Pre-Owned Vans</a></li>-->
                        <li><a class="<? if($firstbreadcrumbslug == str_replace('/','',$results_dot_php) && $second == 'used' && $second != 'specials') { echo 'active'; } ?>" href="/wheelchair-vans-for-sale-kansas-missouri/used" title="Pre-Owned Wheelchair Vans For Sale">Pre-Owned Vans</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'sell-your-van') { echo 'active'; } ?>" href="/sell-your-van" title="We Buy Wheelchair Vans">We Buy Vans</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'wheelchair-van-service') { echo 'active'; } ?>" href="/wheelchair-van-service" title="Wheelchair Van Loans">Service</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'mobility-products' && $second == '') { echo 'active'; } ?>" href="/power-chairs-and-scooters" title="Power Chairs and Scooters">Chairs/Scooters</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'mobility-products' && $second == 'lift-chairs') { echo 'active'; } ?>" href="/durable-medical-equipment/lift-chairs" title="Used stair lifts, scooter lifts and more.">Lifts & More</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'about-us') { echo 'active'; } ?>" href="/about-us" title="Get Your Wheelchair Van Service Completed">About Us</a></li>
                        <li><a href="javascript:void('0');" class="" id="open-large-nav-dropdown" title="Open the full menu"><i class="fa fa-plus"></i> More</a></li>
                        <li><a class="<? if($firstbreadcrumbslug == 'locations') { echo 'active'; } ?>" href="<? echo $contact_page_url; ?>" title="Contact One Of Our Our Wheelchair Van Dealer Locations">Contact</a></li>
                    </ul>
                </nav>
                <div class="large-nav-dropdown">
                    <div class="inside-large-nav-dropdown">
                        <div class="tiles large-menu">
                            <div class="row">
                                <div class="block-xxs-14">
                                    <div class="headingwrapper">
                                        <div class="block-xxs-7 bigtext" ><i class="fa fa-th-large"></i> <? echo $dealername; ?> Full Menu</div>
                                        <div class="block-xxs-7 bigtext text-right" ><a id="close-large-nav-dropdown" href="javascript:void('0');" class="close-btn noicon"><i class="fa fa-times-circle"></i> Close</a></div>
                                    </div>
                                </div>

                            <? include 'link-menu.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </header>
    
  
    <? if($fullpath != '') { // if we are not on the homepage insert the site container.  Closed in the footer. ?>
    <div class="mid-content-wrapper container"><? } ?>