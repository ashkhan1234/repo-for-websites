<div id="all-the-links-for-mobile">
    <div class="block-xxs-14 block-sm-third block-md-20p">
        <div class="flinkheading"><i class="fa fa-plus"></i> Vans For Sale</div>
        <a href="<? echo $results_dot_php; ?>">View All Inventory</a>
        <a href="<? echo $results_dot_php; ?>/new">New Vans For Sale</a>
        <a href="<? echo $results_dot_php; ?>/used">Used Vans For Sale</a>
        <a href="/wheelchair-van-financing">Financing Options</a>
        <a href="/sell-your-van">Sell Your Van</a>
    </div>
    <div class="block-xxs-14 block-sm-third block-md-20p">
        <div class="flinkheading"><i class="fa fa-plus"></i> Mobility Vehicle Products</div>
        <a href="/vehicle-mobility/wheelchair-van-conversions">Vehicle Conversions</a>
        <a href="/vehicle-mobility/hand-controls">Hand Controls</a> 
        <a href="/for-sale">Used Equipment</a>
        <a href="/vehicle-mobility/vehicle-lifts">Scooter Lifts</a>
        <a href="/vehicle-mobility">View All Products</a>
    </div>
    <div class="block-xxs-14 block-sm-third block-md-20p">
        <div class="flinkheading"><i class="fa fa-plus"></i> Mobility Products</div>
        <a href="/rehab-equipment">Rehab Equipment</a>
        <a href="/power-chairs-and-scooters">Power Chairs & Scooters</a>
        <a href="/durable-medical-equipment/lift-chairs">Lift Chairs</a>
        <a href="/bruno-stair-lifts">Stair Lifts</a>
        <a href="/mobility-products">View All Products</a>
    </div>
    <div class="block-xxs-14 block-sm-third block-md-20p add-top-margin-sm">
        <div class="flinkheading"><i class="fa fa-plus"></i> Vehicle Services</div>
        <a href="/wheelchair-van-service">Wheelchair Van Service</a>
        <a href="/driver-evaluation">Driver Evaluations</a>
        <a href="/veterans">Veteran Services</a>
        <a href="/wheelchair-van-rental">Wheelchair Van Rental</a>
    </div>
    <div class="block-xxs-14 block-sm-third block-md-20p add-top-margin-sm">
        <div class="flinkheading"><i class="fa fa-plus"></i> About Us</div>
        <a href="<? echo $contact_page_url; ?>">Contact Us</a>
        <a href="/about-us">About Us</a>
        <a href="/local">Local Cities</a>
        <a href="/join-newsletter">Join Email Newsletter</a>
        <a href="/employment-opportunities">Employment Opportunities</a>
    </div>
</div>

<div class="ignore-in-mobile-menu">
<div class="block-xxs-14" style="height: 20px;" ></div>
<div class="block-xxs-14 block-md-14"><div class="fline"></div></div>
    <div class="block-xxs-14"><div class="bigtext" style="padding-left: 5px;">Our Locations</div></div>
<?
    $locvv = $mysqli->query( "SELECT * FROM locations");
    while ($ssso = $locvv->fetch_assoc()) {
    $locationido= $ssso['id'];
    $tollfreeo = $ssso['tollfree'];
    $cityo = $ssso['city'];
    $addresso = $ssso['address'];
    $stateo = $ssso['state'];
    $zipo = $ssso['zip'];
    $lato = $ssso['lat'];
    $lono = $ssso['lon'];
    $nameo = $ssso['name'];
    $dphoneo = $ssso['phone'];
    $locationimageo = $ssso['locationimage'];
    $locationurlo = $ssso['url'];

    ?>
    <div class="block-xxs-14 block-sm-quarter">
        <div class="footer-location-wrapper">
            <div class="bigtext" style="padding-left: 5px;"><? echo $cityo; ?>, <? echo $stateo; ?></div>
            <div class="mediumtext" style="padding-left: 5px;"><? echo $addresso; ?><br><? echo $cityo; ?>, <? echo $stateo; ?> <? echo $zipo; ?></div>
            <a href="<? echo $locationurlo; ?>" class="noicon">Contact <i class="fa fa-caret-right"></i></a>
            <a href="tes:<? echo $dphoneo; ?>" class="noicon"><i class="fa fa-phone-square"></i> <? echo $dphoneo; ?></a>
        </div>
    </div>
    <? } ?>
    <div class="block-xxs-14" style="height: 20px;" ></div>
    <div class="block-xxs-14 block-md-14"><div class="fline"></div></div>
    <div class="block-xxs-14  block-sm-14 text-right inline-links">
        <a class="noicon" href="/privacy-policy"><i class="fa fa-lock"></i> Privacy Policy</a>
        <a class="noicon" href="<? echo $contact_page_url; ?>"><i class="fa fa-question-circle-o"></i> Contact Us</a>
        <div class="admin-link-btn-wrapper"><a class="noicon" href="/abilityweb/crm"><i class="fa fa-lock"></i> User Login</a></div>
        <div class="powered-by-wrapper">Website Powered By: <a class="noicon" href="https://www.dealerexpress.net" target="_blank">Dealer Express</a> - Data By: <a class="noicon" href="https://www.blvd.com" target="_blank">BLVD.com</a></div>
    </div>
</div>