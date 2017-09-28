<? include 'Connect.php';

$search = $_GET['search'];
$var1 = str_replace(' ','+',$_GET['var1']);
$var1 = str_replace('-','_',$_GET['var1']);
$timeback7days = (time() - 604800);


$filters = explode('+',$var1);

$fl = array();
$activefilters = array();
$makearray = array();
$arc = 0;
$makearrc = 0;
$kw = '';
$metasql = '';

function encodeURL($var) {

    $var = strtolower(str_replace(' ','_',$var));

    return $var;
}
function decodeURL($var) {
    $var = ucwords(str_replace('_',' ',$var));
    $var = str_replace(array('Braunability'),'<img src="//www.dealerexpress.net/sharedimages/braunability-icon.png">',$var);

    return $var;
}



$sql = $mysqli->query( "SELECT vehicle_meta.type as type, vehicle_meta.value as value, count(vehiclelookup.vehicleid) as count
                        FROM vehicle_meta, vehiclelookup
                        WHERE (vehiclelookup.available!='sold' OR vehiclelookup.available='sold' AND soldtimestamp>'$timeback7days')
                        AND vehiclelookup.showonline='true'
                        AND vehiclelookup.vehicleid=vehicle_meta.vehicleid
                        AND vehicle_meta.value!=''
                        AND vehicle_meta.type IN('ada_compliant','category','show_price_public','body','price_total_public','make','location','certified','arrival_status','chassis_warranty','conversion_warranty','conversion','newused','brand','conversion_newused', 'dvd_entertainment','entry_type','conversion','navigation','on_specials_page')
                        GROUP BY vehicle_meta.type, vehicle_meta.value ORDER BY vehicle_meta.type, vehicle_meta.value");
while($xx = $sql->fetch_assoc()) {
    $fl[$arc]['name'] = encodeURL($xx['value']);
    $fl[$arc]['count'] = $xx['count'];
    $fl[$arc]['type'] = $xx['type'];
    $fl[$arc]['active'] = 'false';

    if($xx['type'] == 'make') {
        $makearray[$makearrc] = encodeURL($xx['value']);
        $makearrc++;
    }


    $arc++;
}


$idstring = '';







// New
if($var1 == 'new') {
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE type='newused' AND value='new'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $kw = 'New';
    $metasql = " AND vehicleid IN($idstring) ";
}
// Specials
if($var1 == 'specials') {
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE type='on_specials_page' AND value='Yes'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $kw = 'On Sale!';
    $metasql = " AND vehicleid IN($idstring) ";
}


// Used
if($var1 == 'used') {
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE type='newused' AND value='used'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $kw = 'Used';
    $metasql = " AND vehicleid IN($idstring) ";
}
// Pre-Owned
if($var1 == 'pre_owned_van_new_conversion') {

    $kw = 'Pre-Owned';

}


// ADA
if($var1 == 'ada_compliant') {
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE type='ada_compliant' AND value='Yes'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $kw = 'ADA Compliant';
    $metasql = " AND vehicleid IN($idstring) ";
}



// Makes
$mkey = array_search($var1, $makearray);
if($mkey !== false) {
    $selectedmake = $makearray[$mkey];
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE type='make' AND value='$selectedmake'");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $kw = ucwords($selectedmake);
    $metasql = " AND vehicleid IN($idstring) ";
}


if(isset($_GET['search'])) {
    //$searchsql = "AND searchstring LIKE '%$search%'";
    $searchsql = '';
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehicle_meta
				WHERE value LIKE '%$search%'");

    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    $searchsql = " AND vehicleid IN($idstring) ";
    $kw = '';

}



foreach($filters as $fil) {
    $formatfilter = decodeURL($fil);
    $key = array_search($fil, array_column($fl, 'name'));
    if(array_search($fil, array_column($fl, 'name'))!== false) {
        $fl[$key]['active'] = 'true';
        $fl[$key]['price_total_public'] = 'true';
        $activefilters[$fil] = 'active';
    }
    $key = array_search($fil, array_column($fl, 'name'));
    if(array_search($fil, array_column($fl, 'type'))!== false) {
        $fl[$key]['active'] = 'true';
        $activefilters[$fil] = 'active';
    }
}



// All - No Var Set
if(($var1 == '' || $var1=='pre_owned_van_new_conversion') && !isset($_GET['search'])) {
    $sql = $mysqli->query( "SELECT vehicleid
				FROM vehiclelookup
				WHERE available!='sold' AND showonline='true' $searchsql
OR soldtimestamp>'$timeback7days' AND showonline='true' $searchsql");
    while ($deone2 = $sql->fetch_assoc()) {
        $vehicleid = $deone2['vehicleid'];
        $idstring .= $vehicleid . ', ';
    }
    $idstring = substr($idstring, 0,-2);
    if($var1!='pre_owned_van_new_conversion') {
        $kw = '';
    }

    $metasql = " AND vehicleid IN($idstring) ";
}










$activevehiclesarray = explode(', ',$idstring);



$sqltwow = $mysqli->query( "SELECT vehicleid, hold, location, available
FROM vehiclelookup
WHERE available!='sold' AND showonline='true'
OR soldtimestamp>'$timeback7days' AND showonline='true'
ORDER BY year DESC");
$totalvans = $sqltwow->num_rows;


?>
<!doctype html>
<html>
<head>
    <title><? if($kw!=''){ echo $kw.' '; } ?> Wheelchair Vans For Sale in <? echo $states; ?> | <? echo $dealername; ?></title>
    <meta name="description" content="Browse <? echo $totalvans; ?> <? if($kw!=''){ echo $kw.' '; } ?>wheelchair vans for sale in <? echo $states; ?>. Find <? if($kw!=''){ echo $kw.' '; } ?>lowered floor wheelchair vans and handicap vans at great prices in <? echo $states; ?> at <? echo $dealername; ?>">
    <? require_once 'head-tags.php'; ?>
    <link rel="stylesheet" href="/css/vans-for-sale.css">
</head>
<body>
<? include 'header.php'; ?>



    <div class="row">
        <div class="col-xs-12 col-md-9">
            <h1 style="border:none;"><? if($kw!=''){ echo $kw.' '; } ?> Wheelchair Vans For Sale in <? echo $states; ?></h1>
            <div class="metro">
                <nav class="breadcrumbs mini">
                    <ul>
                        <li>
                            <a href="/"><i class="fa fa-home"></i></a>
                        </li>
                        <li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
                            <a href="<? echo $results_dot_php; ?>" itemprop="url" title="Wheelchair Vans For Sale">
                                <span itemprop="title">Wheelchair Vans For Sale</span>
                            </a>
                        </li>
                        <? if($kw!=''){ ?>
                            <li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb">
                                <a href="<? echo $results_dot_php; ?>/<? echo $var1; ?>" itemprop="url" title="<? echo $kw; ?>">
                                    <span itemprop="title"><? echo $kw; ?></span>
                                </a>
                            </li>
                        <? } ?>
                    </ul>
                </nav>
            </div>


            <p class="hidden-xs"><? echo $dealername; ?> is the leading source for <? if($kw!=''){ echo strtolower($kw).' '; } ?> wheelchair van
                sale<? if($totalvans>1) echo 's'; ?> in <? echo $states; ?>. We service the entire state of <? echo $states; ?> for quality wheelchair vans for sale and
                offer delivery of your handicap van anywhere in <? echo $states; ?>. We currently have <? echo $totalvans; ?> <? if($kw!=''){ echo strtolower($kw).' '; } ?>
                wheelchair van<? if($totalvans>1) echo 's'; ?> available.
            </p>

        </div>
        <div class="hidden-xs hidden-sm col-md-3" style="position:relative;">
            <div class="top-right-apply-btn">
                <a class="big-apply-btn giant" href="javascript:apply_online()">
                    <i class="fa fa-chevron-right"></i>
                    <div class="subtext">Need A Loan? We Can Help!</div>
                    Apply Online</a>
            </div>
        </div>
    </div>

    <div class="tiles">
        <div class="row">
            <div class="block-xxs-14" style="margin-bottom: -10px; position:relative;">

                <div class="vfs-tab-wrapper" style="border-bottom: solid 3px #000;">

                    <!--<a class="vehicletabs btnbar <? if($activefilters['new']!='active' && $activefilters['used']!='active') { echo 'active'; } ?>" href="<? echo $results_dot_php; ?>">ALL</a>
                        <a class="vehicletabs btnbar <? if($activefilters['new']=='active') { echo 'active'; } ?>" href="<? echo $results_dot_php; ?>/new">New</a>
                        <a class="vehicletabs btnbar <? if($activefilters['used']=='active') { echo 'active'; } ?>" href="<? echo $results_dot_php; ?>/used">Used</a>-->

                </div>
                <?


                /*
                $key = array_search('dodge', array_column($fl, 'name'));

                foreach($fl as $make) {
                    echo $make['name'].'-'.$make['count'].'-'.$make['active'].'<br>';
                }

                echo '<pre>';
                print_r($activevehiclesarray);
                echo '</pre>';

                */
                ?>
            </div>


            <div class="block-xxs-14 block-sm-5 block-md-4 block-lg-3 match-column-height hidden-xs">


                <div class="browse-wrapper" style="display: block;">

                    <div class="vfs-count">
                        <div class="currentvancount pull-left"><? echo $totalvans; ?></div>
                        <div class="pull-left currentvancounttxt">Wheelchair Vans<br>For Sale</div>
                        <div style="clear: both"></div>
                        <div class="totalfilterswrapper"><div class="totalfilters pull-left">0</div>
                            <div class="pull-left">Active Filters<br><a href="javascript:clearAllFilters();" style="background-color: #fff; padding: 1px 4px; margin-top: 2px; display: block;">Clear Filters <i class="fa fa-times-circle"></i></a></div>
                            <div style="clear: both"></div>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading active search hidecount">Search Inventory</div>
                        <div class="browse-items" data-type="search">
                            <input class="vehicle-search" value="<? if(isset($_GET['search'])) { echo $search; } ?>" type="text" placeholder="Stock, VIN or Keyword">
                            <button class="vehicle-search-btn"><i class="fa fa-search"></i></button>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading active hidecount">Vehicle Condition</div>
                        <div class="browse-items" data-type="newused">

                            <?
                            $i = 0;
                            foreach($fl as $m) {   ?>
                                <? if($m['type']=='newused' && $m['name']=='new') { ?>
                                    <li class="bic <? if($var1=='new') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } ?>

                                <? if($m['type']=='newused' && $m['name']=='used') { ?>
                                    <li class="bic <? if($var1=='used') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } ?>
                                <? $i++; } ?>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading active hidecount">Conversion Condition</div>
                        <div class="browse-items" data-type="conversion_newused">
                            <? $i = 0;
                            foreach($fl as $m) {   ?>
                                <? if($m['type']=='conversion_newused' && $m['name']=='new') { ?>
                                    <li class="bic <? if($var1=='jkfldsjfls') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } ?>

                                <? if($m['type']=='conversion_newused' && $m['name']=='used') { ?>
                                    <li class="bic <? if($var1=='jfkldsajflksda') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } ?>
                                <? $i++; } ?>
                        </div>
                    </div>

       


                    <div class="browse-category">
                        <div class="browse-heading hidecount">Meets ADA Compliance</div>
                        <div class="browse-items" data-type="ada_compliant" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='ada_compliant' && $m['name']=='yes') { ?>
                                    <li class="bic <? if($activefilters[$m['type']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['type']); ?></li>
                                <? } } ?>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading hidecount">On Special</div>
                        <div class="browse-items" data-type="on_specials_page" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='on_specials_page' && $m['name']=='yes') { ?>
                                    <li class="bic <? if($activefilters[$m['type']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>">On Special</li>
                                <? } } ?>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading hidecount">Make</div>
                        <div class="browse-items" data-type="make" style="display: none">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='make') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>






                    <div class="browse-category">
                        <div class="browse-heading hidecount">Conversion</div>
                        <div class="browse-items softcheck" data-type="conversion" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='conversion') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading hidecount">Wheelchair Entry</div>
                        <div class="browse-items" data-type="entry_type" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='entry_type') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>


                    <?
                    /*
                     *
                     *
                     *
                     *
                     *
                    ?>
                    <div class="browse-category">
                        <div class="browse-heading hidecount">Location</div>
                        <div class="browse-items" data-type="location" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='location') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>


                                        <div class="browse-category">
                        <div class="browse-heading hidecount">Vehicle Type</div>
                        <div class="browse-items softcheck" data-type="body" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='body') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>



                                        <div class="browse-category">
                        <div class="browse-heading hidecount">Conversion Brand</div>
                        <div class="browse-items softcheck" data-type="brand" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='brand') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>

                    <div class="browse-category">
                        <div class="browse-heading">Price Range</div>
                        <div class="browse-items" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='show_price_public' && $m['name']=='true') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>



                    <div class="browse-category">
                        <div class="browse-heading hidecount">Meets ADA Compliance</div>
                        <div class="browse-items" data-type="ada_compliant" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='ada_compliant' && $m['name']=='yes') { ?>
                                    <li class="bic <? if($activefilters[$m['type']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['type']); ?></li>
                                <? } } ?>
                        </div>
                    </div>



                    <div class="browse-category">
                        <div class="browse-heading hidecount">Status</div>
                        <div class="browse-items" data-type="arrival_status" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='arrival_status') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>


    <div class="browse-category">
                        <div class="browse-heading hidecount">DVD Entertainment</div>
                        <div class="browse-items softcheck" data-type="dvd_entertainment" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='dvd_entertainment' && $m['name']=='yes') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['type']); ?></li>
                                <? } } ?>
                        </div>
                    </div>



                    <div class="browse-category">
                        <div class="browse-heading hidecount">Conversion Warranty</div>
                        <div class="browse-items softcheck" data-type="conversion_warranty" style="display: none;">
                            <?
                            foreach($fl as $m) {
                                if($m['type']=='conversion_warranty') { ?>
                                    <li class="bic <? if($activefilters[$m['name']]=='active') { echo 'active'; } ?>" data-count="<? echo $m['count']; ?>" data-defaultcount="<? echo $m['count']; ?>" data-type="<? echo $m['type']; ?>" data-value="<? echo $m['name']; ?>"><? echo decodeURL($m['name']); ?></li>
                                <? } } ?>
                        </div>
                    </div>


                    */
                    ?>






                    <div style="margin-top: 30px;"></div>
                </div>




                <div class="vfs-sidebar-wrapper stick-it hidden-xs">
                    <!-- Ask Question Form -->
                    <div class="hideprint">
                        <div class="ask-question-wrapper"><i class="fa fa-question-circle pull-right" style="font-size: 32px;"></i> Contact Dealer</div>
                        <div id="sidebarForm" class="sidebarfeature">
                            <div class="sub-text">We are standing by to assist you quickly via email.</div>
                            <?
                            $leadtype = 'General';
                            include 'ask-question-form.php'; ?>
                        </div>
                    </div>
                    <!-- End ask Question Form -->
                </div>


            </div>


            <div class="block-xxs-14 block-sm-9 block-md-10 block-lg-11 match-column-height">
                <div class="sidebarfeature main-panel main-panel-right">
                    <?

                    $lowprice = 0;
                    $highprice = 0;
                    $vac = 0;
                    $vehiclesarray = array();



                    while ($deone = $sqltwow->fetch_assoc()) {
                        $id = $deone['vehicleid'];
                        $hold = $deone['hold'];
                        $location = $deone['location'];
                        $available = $deone['available'];
                        $vehiclesarray[$vac]['vehicleid'] = $id;

                        $vehiclevisible = false;
                        $vkey = array_search($id, $activevehiclesarray);

                        if($vkey !== false) {
                            $vehiclevisible = true;
                        }

                        /*newcode*/
                        $totaldiscounts = 0;
                        $sqltwoxdis = $mysqli->query( "SELECT * FROM discounts WHERE vehicleid='$id'");
                        while ($ddd = $sqltwoxdis->fetch_assoc()) {
                            $totaldiscounts = $totaldiscounts + $ddd['amount'];
                        }

                        /*newcode*/

                        $m= array();
                        $sqltwox = $mysqli->query( "SELECT * FROM vehicle_meta WHERE vehicleid='$id'");
                        while ($met = $sqltwox->fetch_assoc()) {
                            $m[$met['type']] = $met['value'];
                            $vehiclesarray[$vac][$met['type']] = encodeURL($met['value']);

                            if($met['type'] == 'price_total_public') {
                                if(is_numeric($met['value']) && $met['value'] < $lowprice) {
                                    $lowprice = $met['value'];
                                }
                                if(is_numeric($met['value']) && $met['value'] > $highprice) {
                                    $highprice = $met['value'];
                                }
                            }

                        }



                        $vac++;

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
                        <div class="block-xxs-14 block-xs-7 block-sm-7 block-md-7 block-lg-third single-vehicle" data-vehicleid="<? echo $id; ?>" <? if($vehiclevisible == false) { echo 'id="hide-res-block"'; } ?>>
                            <div class="vehicleresultsmainx" style="position:relative;">
                                <div class="year-float"><? echo $m['year']; ?></div>
                                <? if($available=='sold') { ?>
                                    <div class="sold-float">Sold</div>
                                <? } else { ?>
                                    <? if($hold!='available') { ?>
                                        <div class="hold-float">Hold</div>
                                    <? } ?>
                                <? } ?>

                                <a href="/wheelchair-vehicle-for-sale/<? echo base64url_encode($id); ?>">
                                    <div class="vehiclelist-image-wrapper">
                                        <img src="<? echo $thumb; ?>" class="img-responsive">
                                    </div>
                                    <div class="vehiclelist-details">
                                        <div class="make-model full-row no-border">
                                            <div class="condition-90 chassis <? echo $m['newused']; ?>"><? echo $m['newused']; ?></div>
                                            <? echo $m['make']; ?> <? echo $m['model']; ?>
                                        </div>
                                        <div class="conversion full-row no-border">
                                            <div class="condition-90 conv <? echo $m['conversion_newused']; ?>"> <? echo $m['conversion_newused']; ?></div>
                                            <?
                                            if (strpos($m['conversion'], 'BraunAbility') !== false) {
                                                echo '<img src="//www.dealerexpress.net/sharedimages/braunability-icon.png" style="width: 14px; margin-top: -2px;"> ';
                                            }
                                            echo $m['conversion'];

                                            ?>
                                        </div>

                                        <div class="full-row">
                                            <div class="half-row">Trim:</div>
                                            <div class="half-row"><? echo $m['trim']; ?></div>
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
                                        <!--<div class="full-row">
                                            <div class="half-row">Price:</div>
                                            <div class="half-row"><strong>
                                                    <?/* if($m['show_price_public'] == 'true') {
                                                        $m['show_price_public'] = str_replace(',','',$m['show_price_public']);
                                                        */?>
                                                        $<?/* echo number_format($m['price_total_public']); */?>
                                                    <?/* } else { */?>
                                                        Call For Price
                                                    <?/* } */?>
                                                </strong>
                                            </div>
                                        </div>-->

                                        <div class="full-row" style="overflow: visible;">
                                            <div class="half-row">Price:</div>
                                            <div class="half-row"><strong>
                                                    <? if($m['show_price_public'] == 'true') {
                                                        $m['show_price_public'] = str_replace(',','',$m['show_price_public']); ?>
                                                        $<? echo number_format($m['price_total_public']); ?>
                                                        <? if($totaldiscounts != 0) { ?>
                                                            <div class="total-discounts-resutls">
                                                            <div class="total-discounts-resutls-inner">$<? echo number_format($totaldiscounts); ?><div class="total-discounts-sub-txt">In Savings</div>
                                                                <div class="discount-shadow"></div></div></div><? } } else { ?>Call For Price<? } ?></strong>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        </div>

                        <? $c++;  } ?>
                    <div style="clear:both; height: 15px;"></div>

                </div>


            </div>

        </div>
    </div>


<? include 'footer.php'; ?>

<script>

    var meta = <? echo json_encode($fl, 99999); ?>; 
    var vehicles = <? echo json_encode($vehiclesarray, 99999); ?>;
    var currentfilters = {};
    var populateCheckboxes = [];

    $( document ).ready(function() {

        $(".browse-items").each(function() {
            var type = $(this).attr('data-type');
            currentfilters[type] = [];
            $(this).children('li').each(function () {
                var value = $(this).attr('data-value');
                var i = $(this).index();
                if($(this).hasClass('active')) { var checked = true; } else { var checked = false; }
                currentfilters[type][i] = {};
                currentfilters[type][i]['value'] = value;
                currentfilters[type][i]['checked'] = checked;
            });
        });

        <? if(isset($_GET['search'])) { ?>
            $('.totalfilterswrapper').slideDown();
            $('.totalfilters').html('1');
        <? } ?>


        $(".bic").each(function() {
            if($(this).hasClass('active')) {
                $(this).parent().slideDown(500);
                $(this).parent().prev().addClass('active');
            }
        });

        <? if($var1 != '') { ?>
        localStorage.setItem('filters','[]');
        <? } ?>

        <? if($var1 == 'pre_owned_van_new_conversion') { ?>
        $('.bic[data-type="newused"][data-value="used"]').trigger('click');
        $('.bic[data-type="conversion_newused"][data-value="new"]').trigger('click');
        <? } ?>



        populateCheckBoxes();
        evaluateChecks();

    });

    function populateCheckBoxes() {
        if(!localStorage.getItem('filters')) {
            localStorage.setItem('filters','[]');
        }
        populateCheckboxes = JSON.parse(localStorage.getItem('filters'));
        $.each(populateCheckboxes, function (i, obj) {
            var type = obj.type;
            var value = obj.value;
            var target = $('.bic[data-type="'+type+'"][data-value="'+value+'"]');
            var i = target.index();
            currentfilters[type][i]['checked'] = true;
            target.addClass('active');
            evaluateChecks();
            target.parent().show();
            target.parent().prev().addClass('active');
        });

    }
    function dePopulateCheckBoxes() {
        populateCheckboxes = JSON.parse(localStorage.getItem('filters'));
        $.each(populateCheckboxes, function (i, obj) {
            var type = obj.type;
            var value = obj.value;
            var target = $('.bic[data-type="'+type+'"][data-value="'+value+'"]');
            var i = target.index();
            currentfilters[type][i]['checked'] = false;
            target.removeClass('active');

            target.parent().show();
            target.parent().prev().removeClass('active');
            localStorage.setItem('filters','[]')

        });
        evaluateChecks();
    }


    function clearAllFilters() {
        $('.vehicle-search').val('');
        dePopulateCheckBoxes();
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '';
            window.history.pushState({path:newurl},'',newurl);
        }
    }


    function getKey(obj, prop, val) {
        for (var key in obj) {
            if (obj[key].hasOwnProperty(prop) && obj[key][prop] === val) {
                return key;
            }
        }
    }



    function evaluateChecks() {
        $('.single-vehicle').hide().removeClass('active');
        $(".bic").show();
        $(".browse-category").show();

        localStorage.setItem('test1','this is a test');



        var matchedall;
        var foundonecheckbox = false;
        var removefiltercats = {};

        $.each(currentfilters, function (property) {
            var type = property;

            if(getKey(currentfilters[type], 'checked', true)) {
                foundonecheckbox = true;
                return;
            }

        });

        if(foundonecheckbox == true) {


            $.each(vehicles, function (i, obj) {
                var vehicleid = this.vehicleid;
                $.each(currentfilters, function (property) {
                    var type = property;
                    $.each(this, function () {
                        if (this.checked == true) {
                            if (obj[type] == this.value) {
                                $('.single-vehicle[data-vehicleid="' + vehicleid + '"]').show().addClass('active');
                            }
                        }

                    });

                });

            });


            $(".single-vehicle.active").each(function () {
                var currentobj = $(this);
                var vehicleid = $(this).attr('data-vehicleid');
                var matchedone = false;
                var vkey = getKey(vehicles, 'vehicleid', vehicleid)

                $.each(currentfilters, function (property) {
                    var type = property;
                    var hascheck = false;
                    matchedone = false;
                    $.each(this, function () {
                        if (this.checked == true) {
                            hascheck = true;
                            if (vehicles[vkey][type] == this.value) {
                                matchedone = true;
                            }
                        }
                    });
                    if (hascheck == true) {
                        if (matchedone == false) {
                            $('.single-vehicle[data-vehicleid="' + vehicleid + '"]').hide().removeClass('active');
                        }
                    } else {
                        // Hide browse options where no vehicles are available
                        $.each(this, function () {
                            removefiltercats[type] = 'remove';
                        });
                    }


                });

            });


            populateCheckboxes = [];
            $(".bic").each(function() {
                var type = $(this).attr('data-type');
                var value = $(this).attr('data-value');
                var matchedone = false;
                var count = 0;
                if($(this).hasClass('active')) {
                    populateCheckboxes.push({'type' : type, 'value' : value});

                }

                $(".single-vehicle.active").each(function () {
                    var currentobj = $(this);
                    var vehicleid = $(this).attr('data-vehicleid');

                    var vkey = getKey(vehicles, 'vehicleid', vehicleid);
                    if(value == vehicles[vkey][type]) {
                        matchedone = true;
                        count++;

                    }
                });
                $(this).attr('data-count',count);
                if(matchedone == false) {
                    $(this).hide();

                }

                if($(this).parent().find(".bic.active").length != 0) {
                    $(this).show();
                    $(this).attr('data-count',$(this).attr('data-defaultcount'));
                }

            });



        } else {
            // there was no checkboxes selected
            $('.single-vehicle').show().addClass('active');
            $(".bic").each(function() {
                $(this).attr('data-count',$(this).attr('data-defaultcount'));
            });
            populateCheckboxes = [];
        }



        // Cleanup after evertying runs
        setTimeout(function() {
            match_column_heights();
            $(document.body).trigger("sticky_kit:recalc");
        },220);

        localStorage.setItem( 'filters',JSON.stringify(populateCheckboxes));

        var totalfilters = 0;
        $(".browse-items").each(function() {
            var coun = $(this).find('.bic.active').length;
            totalfilters = Number(totalfilters) + Number(coun);
            if(coun == 0) {
                $(this).prev().attr('data-selected','').addClass('hidecount')
            } else {
                $(this).prev().attr('data-selected',coun).removeClass('hidecount');
            }
        });


        $('.currentvancount').html($('.main-panel-right').find('.single-vehicle.active').length);

        $('.totalfilters').html(totalfilters);
        if(totalfilters != 0) {
            $('.totalfilterswrapper').slideDown();
        } else {
            $('.totalfilterswrapper').slideUp();
        }






    }



    $('.browse-heading').click(function() {
        if($(this).hasClass('active')) {
            $(this).next().slideUp(200);
        } else {
            $(this).next().slideDown(200);
        }
        $(this).toggleClass('active');
        setTimeout(function() {
            match_column_heights();
            $(document.body).trigger("sticky_kit:recalc");
        },220)
    });

    $('.bic').click(function() {

        var type = $(this).attr('data-type');
        var value = $(this).attr('data-value');
        var i = $(this).index();

        if($(this).hasClass('active')) {
            currentfilters[type][i]['checked'] = false;
        } else {
            currentfilters[type][i]['checked'] = true;
        }
        $(this).toggleClass('active');

        evaluateChecks();

    });

    $('.vehicle-search').keyup(function(e) {
        if (e.which == 13) {
            $('.vehicle-search-btn').trigger('click');
        }
    });

    $('.vehicle-search-btn').click(function() {
        var string = $('.vehicle-search').val();
        if(string == '') {
            clearAllFilters();
            $('.vehicle-search').val('');
        } else {
            clearAllFilters();
            window.location.href = "//<? echo $_SERVER['HTTP_HOST'];?>/wheelchair-vans-for-sale?search="+string;
        }

    });



    $( document ).ready(function() {
        TweenMax.set($('.total-discounts-resutls'), {perspective:'600px',scale:0.5,marginTop:'-60px',right:'-8px' });
        TweenMax.set($('.total-discounts-resutls-inner'), {rotationX:-3,rotationY:40,rotationZ:-5, borderWidth:'4px',fontSize:'52px',lineHeight:'' });
        TweenMax.set($('.total-discounts-sub-txt'), {fontSize:'28px',marginTop:'-18px' });
        TweenMax.set($('.discount-shadow'), {display:'block' });
        TweenMax.to($('.total-discounts-resutls-inner'), 1, {rotationY:400, repeat:-1, repeatDelay:3, ease: Back.easeOut.config(1.4)});
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js"></script>

</body>
</html>