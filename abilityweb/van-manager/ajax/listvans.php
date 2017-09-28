<?
include '../../../connect-pdo.php';

require_once '../../js/sendgrid/vendor/autoload.php';
$sendgrid_username  = 'factorypolaris';
$sendgrid_password  = 'J8TpSerA';
include '../../phpfunctions/notifier.php';






////////  UPDATE Customer   ///////////////////////////////
if (isset($_POST['updateCustomer'])) {
    $customerID = $_POST['customerID'];
    if ($_POST['addCustomer'] == "true") {

        $updateClause = "INSERT IGNORE INTO customers SET";
        $whereClause =  "";

    } else {
        $updateClause = "UPDATE customers SET";
        $whereClause = 	"WHERE id='$customerID' LIMIT 1";
    }


    $customerName = strtoupper(str_replace("'", "\'", $_POST['customerName']));
    $address = strtoupper(str_replace("'", "\'", $_POST['address']));
    $city = strtoupper(str_replace("'", "\'", $_POST['city']));
    $state = strtoupper(str_replace("'", "\'", $_POST['state']));
    $zip = str_replace("'", "\'", $_POST['zip']);
    $notes = strtoupper(str_replace("'", "\'", $_POST['notes']));
    $phone1 = str_replace("'", "\'", $_POST['phone1']);
    $phone2 = str_replace("'", "\'", $_POST['phone2']);
    $fax = str_replace("'", "\'", $_POST['fax']);
    $email = str_replace("'", "\'", $_POST['email']);
    $contact = strtoupper(str_replace("'", "\'", $_POST['contact']));

    if($notes == '') {
        $notes = 'Notes:';
    }

    mysql_query( "$updateClause
					name='$customerName',
					address='$address',
					city='$city',
					state='$state',
					zip='$zip',
					notes='$notes',
					phone1='$phone1',
					phone2='$phone2',
					fax='$fax',
					email='$email',
					contact='$contact'
					$whereClause");
    die;
}



////////  UPDATE FavoriteStatus   ///////////////////////////////
if (isset($_POST['modifyFavorite'])) {
    $customerID = $_POST['customerID'];
    $userid = $_POST['userid'];
    $favorite = $_POST['favorite'];

    if($favorite != 'true') {
        mysql_query( "INSERT IGNORE INTO userfavorites SET
					userid='$userid',
					customerid='$customerID'");
    } else {
        mysql_query( "DELETE FROM userfavorites
					WHERE userid='$userid' AND customerid='$customerID' LIMIT 1");
    }
    die;
}









if(isset($_POST['customerforms'])) {
    $customerid = $_POST['customerid'];
    $resff = $mysqli->query("SELECT forms.id,sn,forms.type,customerid,forms.locationid AS locationid,formdate,startdate,expecteddate,year,make,model,door,newused,length,color,interior,forms.notes,vin,miles,total,userid,timestamp,salesleadtext,temp,userage,
vannumber,conversion,vehicleid,progress,layout,layoutString,picompletedate,milesin,milesout,piiib,pioib,fuellevel,preinspectionsign,preinspectionnotes,postinspectionnotes,
chassisinspectioncompletein,chassisinspectioncompleteout,exteriorinspectioncomplete,taxrate, locations.name AS location, contacts.name AS salesman
FROM forms, customers, contacts, locations
WHERE forms.customerid='$customerid' AND
forms.customerid=customers.id AND
forms.userid=contacts.id AND
contacts.locationid=locations.id
ORDER BY forms.id DESC LIMIT 50");
    $userformlist = array();
    while($rowff = $resff->fetch_assoc()) {
        $userformlist[] = $rowff;
    }
    $data =  json_encode($userformlist);
    echo $data;

}























// Vehicle Custom Vars Editing
if(isset($_POST['remove_category'])) {
    $catid = $_POST['catid'];
    $removaltype = $_POST['removaltype'];
    if($catid =='') { die; }

    if($removaltype == 'item') {
        $mysqli->query("DELETE FROM vehicle_vars WHERE id='$catid'");
    }

    if($removaltype == 'category') {
        $resg = $mysqli->query("SELECT * FROM vehicle_vars WHERE id='$catid'");
        $row = $resg->fetch_assoc();
        $id = $row['id'];
        $type = $row['type'];
        if($type == 'category') { die; }
        $mysqli->query("DELETE FROM vehicle_vars WHERE type='$type'");
        echo "Removed Category: $type";
    }


}

if(isset($_POST['remove_single_cat'])) {
    $catid = $_POST['catid'];
    if($catid =='') { die; }

    $mysqli->query("DELETE FROM vehicle_vars WHERE id='$catid' LIMIT 1");

}

if(isset($_POST['add_category'])) {
    $type = $mysqli->real_escape_string($_POST['type']);
    $val = $mysqli->real_escape_string($_POST['value']);
    $category = $mysqli->real_escape_string($_POST['category']);
    $mysqli->query("INSERT IGNORE INTO vehicle_vars SET type='$type', value='', name='$category', showonline='na'");
    echo $mysqli->insert_id;

}

if(isset($_POST['add_item'])) {
    $type = $mysqli->real_escape_string($_POST['type']);
    $val = $mysqli->real_escape_string($_POST['value']);
    $category = $mysqli->real_escape_string($_POST['category']);
    $mysqli->query("INSERT IGNORE INTO vehicle_vars SET type='$type', value='$val', name='$category', showonline='na'");
    echo $mysqli->insert_id;

}











if(isset($_POST['listvans'])) {

    $resff = $mysqli->query("SELECT * FROM vehiclelookup WHERE available!='sold' ORDER BY year ASC LIMIT 1");
    $list = array();
    while($rowff = $resff->fetch_assoc()) {
        $list[] = $rowff;
    }
    $data =  json_encode($list);
    echo $data;

}


if(isset($_POST['remove_vehicle_meta'])) {
    $vehicleid = $_POST['vehicleid'];
    $type = $mysqli->real_escape_string($_POST['type']);
    if($vehicleid!='' && $type!='') {
        $mysqli->query("DELETE FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    }

    if($type == 'location') {
        $mysqli->query("UPDATE vehiclelookup SET location='' WHERE vehicleid='$vehicleid' LIMIT 1");
        $mysqli->query("UPDATE vehiclelookup SET locationid='0' WHERE vehicleid='$vehicleid' LIMIT 1");
    }

    die;
}


if(isset($_POST['update_vehicle_meta'])) {
    $vehicleid = $_POST['vehicleid'];
    $type = $mysqli->real_escape_string($_POST['type']);
    $val = $mysqli->real_escape_string($_POST['value']);
    $val = substr($val, 0, 499);
    $name = $mysqli->real_escape_string($_POST['name']);
    $arrange = $_POST['arrange'];

    if($type == 'miles') {
        $val = numbers_only($val);
    }

    if($type == 'year') {
        $mysqli->query("UPDATE vehiclelookup SET
        year='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'make') {
        $mysqli->query("UPDATE vehiclelookup SET
        make='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'model') {
        $mysqli->query("UPDATE vehiclelookup SET
        model='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }

    if($type == 'vin') {
        $mysqli->query("UPDATE vehiclelookup SET
        vin='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'trim') {
        $mysqli->query("UPDATE vehiclelookup SET
        trim='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'conversion') {
        $mysqli->query("UPDATE vehiclelookup SET
        conversion='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'location') {
        $mysqli->query("UPDATE vehiclelookup SET location='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
        $resg = $mysqli->query("SELECT id FROM locations WHERE name='$val' LIMIT 1");
        $lrow = $resg->fetch_assoc();
        $locationid = $lrow['id'];
        $mysqli->query("UPDATE vehiclelookup SET locationid='$locationid' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'description') {
        $mysqli->query("UPDATE vehiclelookup SET
        description='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'description') {
        $mysqli->query("UPDATE vehiclelookup SET
        description='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'category') {
        $mysqli->query("UPDATE vehiclelookup SET
        category='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }
    if($type == 'newused') {
        $mysqli->query("UPDATE vehiclelookup SET
        category='$val' WHERE newused='$val' LIMIT 1");
    }
    if($type == 'newused_conversion') {
        $mysqli->query("UPDATE vehiclelookup SET
        category='$val' WHERE newused='$val' LIMIT 1");
    }





    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    $total = $checkout->num_rows;

    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='$type', name='$name', value='$val', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$val' WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    }

    // Get the vehicle data for the email alerts
    $g= array();
    $sqltwox = $mysqli->query("SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
    while($met = $sqltwox->fetch_assoc()) {
        $g[$met['type']] = $met['value'];
    }


    // Send a notification for Not Ready -> Other status
    if($type == 'category') {
        $sqlcat = $mysqli->query("SELECT category FROM vehiclelookup WHERE vehicleid='$vehicleid' AND category LIKE '%Not Ready%'");
        $foundnotready = $sqlcat->num_rows;
        if($foundnotready != 0) {
            while($mety = $sqlcat->fetch_assoc()) {
                $oldstatus = $mety['category'];
            }
            if (strpos($val, 'Not Ready') !== FALSE) {
                // Vehicle is not moving from Not Ready to Ready
                echo 'No Match|';
            } else {
                // Vehicle Is now in a Ready status so we are going to send out emails
                echo 'Mail Trying..';
                ob_start();
                ?>

                <div style="padding: 5px; background-color: #EAEAEA; font-weight: bold;">Vehicle Status</div>
                <div style="border-top: solid 1px #dfdfdf; padding: 5px; background-color: #b7ffbe;">
                    <div style="float: right;">Status</div>
                    <? echo $val; ?>&nbsp;
                </div>
                <div style="border-top: solid 1px #dfdfdf; padding: 5px; background-color: #ffb8c9;">
                    <div style="float: right;">Previous Status</div>
                    <? echo $oldstatus; ?>&nbsp;
                </div>
                <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                    <div style="float: right;">Vehicle Link</div>
                    <a href="http://www.<? echo $domainname; ?>/abilityweb/vehiclemain.php?vehicleid=<? echo $vehicleid; ?>">View Vehicle</a>
                </div>
                <?
                $body = ob_get_clean();
                $notifysubject = "Vehicle Moved To Ready Status: ";
                // Function Vars: key, subject, location, mailbody, fromname (optional), fromemail (optional), vehicleid (optional), attachment (optional)
                sendNotification('Not Ready To Ready', $notifysubject, $g['location'], $body ,'', '', $vehicleid, '');
                echo 'Mail Sent..';
            }
        }
        $mysqli->query("UPDATE vehiclelookup SET category='$val' WHERE vehicleid='$vehicleid' LIMIT 1");
    }




    //Send notification for Admin Notes
    if($type == 'admin_notes') {
        if ($val != '') {
            ob_start();
            ?>
            <div style="padding: 5px; background-color: #EAEAEA; font-weight: bold;">Vehicle Status</div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Category</div>
                <? echo $g['category']; ?>&nbsp;
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Status</div>
                <? echo $g['arrival_status']; ?>&nbsp;
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Vehicle Link</div>
                <a href="http://www.<? echo $domainname; ?>/abilityweb/vehiclemain.php?vehicleid=<? echo $vehicleid; ?>">View
                    Vehicle</a>
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <strong>Updated Admin Notes</strong><br>
                <? echo $val; ?>
            </div>
            <?
            $body = ob_get_clean();
            $notifysubject = "Vehicle Admin Notes Updated: ";
            // Function Vars: key, subject, location, mailbody, fromname (optional), fromemail (optional), vehicleid (optional), attachment (optional)
            sendNotification('Admin Notes Updated', $notifysubject, $g['location'], $body, '', '', $vehicleid, '');
        }
    }





    //Send notification for Super Admin Notes
    if($type == 'superadmin_notes') {
        if ($val != '') {
            ob_start();
            ?>
            <div style="padding: 5px; background-color: #EAEAEA; font-weight: bold;">Vehicle Status</div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Category</div>
                <? echo $g['category']; ?>&nbsp;
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Status</div>
                <? echo $g['arrival_status']; ?>&nbsp;
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <div style="float: right;">Vehicle Link</div>
                <a href="http://www.<? echo $domainname; ?>/abilityweb/vehiclemain.php?vehicleid=<? echo $vehicleid; ?>">View
                    Vehicle</a>
            </div>
            <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
                <strong>Updated SuperAdmin Notes</strong><br>
                <? echo $val; ?>
            </div>
            <?
            $body = ob_get_clean();
            $notifysubject = "SuperAdmin Notes Updated: ";
            // Function Vars: key, subject, location, mailbody, fromname (optional), fromemail (optional), vehicleid (optional), attachment (optional)
            sendNotification('Super Admin Notes Updated', $notifysubject, $g['location'], $body, '', '', $vehicleid, '');
        }
    }





// Update the show online status in both the vehicle lookup and meta tables





    $m= array();
    $thesqlmeta = $mysqli->query("SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
    $searchstring = '';
    while($met = $thesqlmeta->fetch_assoc()) {
        $m[$met['type']] = $met['value'];
        $searchstring .= $met['value'] . ' | ';
    }


    $mysqli->query("UPDATE vehiclelookup SET
        searchString='$searchstring'
        WHERE vehicleid='$vehicleid' LIMIT 1");
    echo'made it';


}







if(isset($_POST['update_vehicle_pricing'])) {
    $arrange = '0';
    $vehicleid = $_POST['vehicleid'];
    $conversion_status_public = $mysqli->real_escape_string($_POST['conversion_status_public']);
    $price_chassis_public = $mysqli->real_escape_string(numbers_only($_POST['price_chassis_public']));
    $price_conversion_public = $mysqli->real_escape_string(numbers_only($_POST['price_conversion_public']));
    $price_total_public = $mysqli->real_escape_string(numbers_only($_POST['price_total_public']));
    $price_total_rebates = $mysqli->real_escape_string(numbers_only($_POST['price_total_rebates']));
    $price_chassis_admin = $mysqli->real_escape_string(numbers_only($_POST['price_chassis_admin']));
    $price_conversion_admin = $mysqli->real_escape_string(numbers_only($_POST['price_conversion_admin']));
    $price_total_admin = $mysqli->real_escape_string(numbers_only($_POST['price_total_admin']));
    $expenses = json_decode($_POST['expenses'], true);
    $discounts = json_decode($_POST['discounts'], true);
    echo $expenses[0]['name'];

    $mysqli->query("DELETE FROM expenses WHERE vehicleid='$vehicleid'");
    foreach($expenses as $e) {
        $name = $mysqli->real_escape_string($e['name']);
        $amount = $mysqli->real_escape_string(numbers_only($e['amount']));
        if($name == '') { $name = 'Expense description not provided'; }
        $amount = $mysqli->real_escape_string(numbers_only($e['amount']));
        if($amount!=0) {
            $mysqli->query("INSERT IGNORE into expenses SET vehicleid='$vehicleid', name='$name', amount='$amount', arrange='0'");
        }
    }

    $totaldiscounts = 0;
    $mysqli->query("DELETE FROM discounts WHERE vehicleid='$vehicleid'");
    foreach($discounts as $e) {

        $name = $mysqli->real_escape_string($e['name']);
        if($name == '') { $name = 'Discount'; }
        $amount = $mysqli->real_escape_string(numbers_only($e['amount']));
        if($amount!=0) {
            $totaldiscounts = $totaldiscounts + $amount;
            $mysqli->query("INSERT IGNORE into discounts SET vehicleid='$vehicleid', name='$name', amount='$amount', arrange='0'");
        }

    }
    $baseprice = $price_total_public - $totaldiscounts;


    $mysqli->query("UPDATE vehiclelookup SET
    price='$price_total_public', baseprice='$baseprice' 
    WHERE vehicleid='$vehicleid' LIMIT 1");

    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_total_admin' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_admin', name='price_total_admin', value='$price_total_admin', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_total_admin' WHERE vehicleid='$vehicleid' AND type='price_total_admin' LIMIT 1");
    }


    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_conversion_admin' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_conversion_admin', name='price_conversion_admin', value='$price_conversion_admin', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_conversion_admin' WHERE vehicleid='$vehicleid' AND type='price_conversion_admin' LIMIT 1");
    }


    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_chassis_admin' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_chassis_admin', name='price_chassis_admin', value='$price_chassis_admin', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_chassis_admin' WHERE vehicleid='$vehicleid' AND type='price_chassis_admin' LIMIT 1");
    }



    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_total_rebates' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_rebates', name='price_total_rebates', value='$price_total_rebates', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_total_rebates' WHERE vehicleid='$vehicleid' AND type='price_total_rebates' LIMIT 1");
    }


    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_total_public' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_public', name='price_total_public', value='$price_total_public', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_total_public' WHERE vehicleid='$vehicleid' AND type='price_total_public' LIMIT 1");
    }


    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_conversion_public' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_conversion_public', name='price_conversion_public', value='$price_conversion_public', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_conversion_public' WHERE vehicleid='$vehicleid' AND type='price_conversion_public' LIMIT 1");
    }


    $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='price_chassis_public' LIMIT 1");
    $total = $checkout->num_rows;
    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_chassis_public', name='price_chassis_public', value='$price_chassis_public', arrange='$arrange'");
    } else {
        $mysqli->query("UPDATE vehicle_meta SET value='$price_chassis_public' WHERE vehicleid='$vehicleid' AND type='price_chassis_public' LIMIT 1");
    }

      $checkout = $mysqli->query("SELECT id FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='conversion_status_public' LIMIT 1");
        $total = $checkout->num_rows;
        if($total == 0) {
            $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='conversion_status_public', name='conversion_status_public', value='$conversion_status_public', arrange='$arrange'");
        } else {
            $mysqli->query("UPDATE vehicle_meta SET value='$conversion_status_public' WHERE vehicleid='$vehicleid' AND type='conversion_status_public' LIMIT 1");
        }



    echo 'Updated Pricing...';


}


if(isset($_POST['checkbox_sattus'])) {
    $ischecked = $_POST['ischecked'];
    $vehicleid = $_POST['vehicleid'];

    //update query

}




if(isset($_POST['update_specs'])) {
    $vehicleid = $_POST['vehicleid'];
    $type = $mysqli->real_escape_string($_POST['type']);
    $val = $mysqli->real_escape_string($_POST['value']);


    $checkout = $mysqli->query("SELECT id FROM vehicle_specs WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    $total = $checkout->num_rows;

    if($total == 0) {
        $mysqli->query("INSERT IGNORE into vehicle_specs SET vehicleid='$vehicleid', type='$type', value='$val'");
    } else {
        $mysqli->query("UPDATE vehicle_specs SET value='$val' WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    }

}




if(isset($_POST['update_rebates'])) {
    /*$vehicleid = $_POST['vehicleid'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $rebatetype = $mysqli->real_escape_string($_POST['rebatetype']);
    $rules = $mysqli->real_escape_string($_POST['rules']);
    $rules = substr($rules, 0, 499);
    $rebateid = $mysqli->real_escape_string($_POST['rebateid']);
    $amount = $_POST['amount'];
    $mysqli->query("INSERT IGNORE INTO discounts SET vehicleid='$vehicleid', name='$name', rebatetype='$rebatetype', amount='$amount', rebateid='$rebateid'");
    $affected_rows = $mysqli->affected_rows;
    echo $affected_rows;*/
}

if(isset($_POST['remove_rebates'])) {
    /*$vehicleid = $_POST['vehicleid'];
    $checkout = $mysqli->query("DELETE FROM discounts WHERE vehicleid='$vehicleid'");
    echo '1';*/
}




if(isset($_POST['update_vehiclelookup'])) {
    $vehicleid = $_POST['vehicleid'];
    $type = $mysqli->real_escape_string($_POST['type']);
    $val = $mysqli->real_escape_string($_POST['value']);
    $mysqli->query("UPDATE vehiclelookup SET $type='$val' WHERE vehicleid='$vehicleid' LIMIT 1");



}


function numbers_only($val) {
    $val = str_replace(array(',', '#', '$', '%', '<', '>', '*', '"', "'"), '', $val);
    return $val;
}

if(isset($_POST['big_update'])) {
    $vehicleid = $_POST['vehicleid'];
    $description = $mysqli->real_escape_string($_POST['description']);
    $miles = $mysqli->real_escape_string($_POST['miles']);
    $miles = numbers_only($miles);
    $stock = $mysqli->real_escape_string($_POST['stock']);
    $vin = $mysqli->real_escape_string(strtoupper($_POST['vin']));
    $conversion_description = $mysqli->real_escape_string($_POST['conversion_description']);
    $conversion = $mysqli->real_escape_string($_POST['conversion']);

    $m= array();
    $thesqlmeta = $mysqli->query("SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
    $searchstring = '';
    while($met = $thesqlmeta->fetch_assoc()) {
        $m[$met['type']] = $met['value'];
        $searchstring .= $met['value'] . ' | ';
    }


    $mysqli->query("UPDATE vehiclelookup SET
    description='$description',
    conversion='$conversion',
    conversion_description='$conversion_description',
    miles='$miles',
    searchString='$searchstring',
    vin='$vin',
    stock='$stock'
    WHERE vehicleid='$vehicleid' LIMIT 1");
    $affected_rows = $mysqli->affected_rows;
    echo '1';



}


if(isset($_POST['edit_vehicle'])) {
    $vehicleid = $_POST['vehicleid'];

    $m= array();
    $thesqlmeta = $mysqli->query("SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type!='showonline'");
    $m['conversionid'] = '-1';
    $m['miles'] = '';
    while($met = $thesqlmeta->fetch_assoc()) {
        $m[$met['type']] = $met['value'];
    }

    $m['expenses']= array();
    $c = 0;
    $thesqlmeta = $mysqli->query("SELECT * FROM expenses WHERE vehicleid='$vehicleid' ORDER BY id ASC");
    while($met = $thesqlmeta->fetch_assoc()) {
        $m['expenses'][$c]['name'] = $met['name'];
        $m['expenses'][$c]['amount'] = $met['amount'];
        $c++;
    }

    $m['discounts']= array();
    $c = 0;
    $thesqlmeta = $mysqli->query("SELECT * FROM discounts WHERE vehicleid='$vehicleid' ORDER BY discountid ASC");
    while($met = $thesqlmeta->fetch_assoc()) {
        $m['discounts'][$c]['name'] = $met['name'];
        $m['discounts'][$c]['amount'] = $met['amount'];
        $c++;
    }


    $thesql = $mysqli->query("SELECT * FROM vehiclelookup WHERE vehicleid='$vehicleid' LIMIT 1");
    $deone = $thesql->fetch_assoc();
    $m['description'] = $deone['description'];
    $m['vin'] = $deone['vin'];
    $m['showonline'] = $deone['showonline'];
    $m['show_online'] = $deone['showonline'];
    $m['listdate'] = date('m/d/y', $deone['listdate']);
    if($m['eta']=='') { $m['eta']= date('Y-m-d', $deone['listdate']);}
    $m['conversion_description'] = $deone['conversion_description'];
    $data =  json_encode($m);
    echo $data;

}




if(isset($_POST['get_standard_options'])) {
    $vehicleid = $_POST['vehicleid'];

    $thesql = $mysqli->query("SELECT standard_options FROM vehiclelookup WHERE vehicleid='$vehicleid' LIMIT 1");
    $deone = $thesql->fetch_assoc();
    $m = $deone['standard_options'];
    $data =  json_encode($m);
    echo $data;
}


if(isset($_POST['get_optional_options'])) {
    $vehicleid = $_POST['vehicleid'];
    $thesql = $mysqli->query("SELECT optional_options FROM vehiclelookup WHERE vehicleid='$vehicleid' LIMIT 1");
    $deone = $thesql->fetch_assoc();
    $m = $deone['optional_options'];
    $data =  json_encode($m);
    echo $data;
}

if(isset($_POST['get_specs'])) {
    $vehicleid = $_POST['vehicleid'];
    $type = $_POST['type'];
    $thesql = $mysqli->query("SELECT value FROM vehicle_specs WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
    $deone = $thesql->fetch_assoc();
    $m = $deone['value'];
    $data =  json_encode($m);
    echo $data;
}







if(isset($_POST['deletevan'])) {
    $vehicleid = $_POST['vehicleid'];
    if($vehicleid != '') {
        $mysqli->query("DELETE FROM vehicle_meta WHERE vehicleid='$vehicleid'");
        $mysqli->query("DELETE FROM vehiclelookup WHERE vehicleid='$vehicleid' LIMIT 1");
    }
}




if(isset($_POST['marksold'])) {
    $vehicleid = $_POST['vehicleid'];
    $customername = $mysqli->real_escape_string($_POST['customername']);
    $whosold = $mysqli->real_escape_string($_POST['salesmansold']);
    $salesmanid = $mysqli->real_escape_string($_POST['salesmanid']);
    $soldcomments = $mysqli->real_escape_string($_POST['soldcomments']);
    $locationname = $mysqli->real_escape_string($_POST['locationname']);
    $locationid = $mysqli->real_escape_string($_POST['locationid']);
    $xdate = time();

    $locationidexist = $mysqli->query("SHOW COLUMNS FROM `vehiclelookup` LIKE 'locationid'");
    if($locationidexist->num_rows == 0) {
        $mysqli->query("ALTER TABLE crm.vehiclelookup ADD locationid INT DEFAULT '0'");
    }

    $mysqli->query("UPDATE vehiclelookup SET
    soldsalesman='$whosold',
    soldcomments='$soldcomments',
    solduserid='$salesmanid',
    soldcustomer='$customername',
    location='$locationname',
    locationid='$locationid',
    available='sold',
    soldtimestamp='$xdate'
    WHERE vehicleid='$vehicleid'");

    $mysqli->query("UPDATE vehicle_meta SET
    value='$location',
    WHERE vehicleid='$vehicleid' AND `type`='location'");




    //Send notification for Sold Vehicle Notice
    ob_start();
    ?>
    <div style="padding: 5px; background-color: #EAEAEA; font-weight: bold;">Sale Details</div>
    <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
        <div style="float: right;">Sold By</div>
        <? echo $whosold; ?>&nbsp;
    </div>
    <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
        <div style="float: right;">Location</div>
        <? echo $locationname; ?>&nbsp;
    </div>
    <div style="border-top: solid 1px #dfdfdf; padding: 5px;">
        <div style="float: right;">Customer</div>
        <? echo $customername; ?>&nbsp;
    </div>
    <?
    $body = ob_get_clean();
    $notifysubject = "Vehicle Marked Sold: ";
    // Function Vars: key, subject, location, mailbody, fromname (optional), fromemail (optional), vehicleid (optional), attachment (optional)
    sendNotification('Sold Vehicle', $notifysubject, $g['location'], $body, '', '', $vehicleid, '');

}









if(isset($_GET['vehicle_vars'])) {

    $c = 1;
    $a = 0;
    $list = array();
    $listdate = time();

    $addoredit = $_GET['addoredit'];
    $vehicleid = $_GET['vehicleid'];


    if($addoredit == 'add') {
        $mysqli->query("INSERT IGNORE INTO vehiclelookup SET available='available',
        hold='available',
        standard_options='[]',
        optional_options='[]',
         listdate='$listdate'");
        $vehicleid = $mysqli->insert_id;

        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='show_price_public', name='Show_price_public', value='false', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='show_price_admin', name='Show_price_admin', value='false', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_rebates', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_chassis_admin', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_conversion_admin', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_admin', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_total_public', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_chassis_public', name='Price', value='0', arrange='0'");
        $mysqli->query("INSERT IGNORE into vehicle_meta SET vehicleid='$vehicleid', type='price_conversion_public', name='Price', value='0', arrange='0'");






    } else {

    }


    $resg = $mysqli->query("SELECT * FROM vehicle_vars GROUP BY type");
    while($row = $resg->fetch_assoc()) {
        $type = $row['type'];
        $name = $row['name'];
        $arrange = $row['arrange'];
        $list[$c]['name'] = $row['name'];

        if($addoredit == 'edit') {
            $resgmm = $mysqli->query("SELECT value FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='$type' LIMIT 1");
            $rowm = $resgmm->fetch_assoc();
            $selected_val = $rowm['value'];
        }


        $resffg = $mysqli->query("SELECT * FROM vehicle_vars WHERE type='$type' AND parentid!='0' ORDER BY value ASC");
        $childrows = $resffg->num_rows;

        if($childrows != '0') {
            $list[$c]['haschildren'] = 'true';
        } else {
            $list[$c]['haschildren'] = 'false';
        }



        $s=0;
        $resff = $mysqli->query("SELECT * FROM vehicle_vars WHERE type='$type' AND parentid='0' ORDER BY value ASC");
        while($rowff = $resff->fetch_assoc()) {
            $id = $rowff['id'];
            $parentid = $rowff['parentid'];

            $list[$c][$s] = $rowff;



            if($selected_val == $rowff['value']) {
                $list[$c][$s]['selected'] = 'true';
            } else {
                $list[$c][$s]['selected'] = 'false';
            }

            $resffg = $mysqli->query("SELECT * FROM vehicle_vars WHERE parentid='$id' ORDER BY value ASC");
            $childrows = $resffg->num_rows;

            if($childrows != '0') {
                $list[$c][$s]['haschildren'] = 'true';
            } else {
                $list[$c][$s]['haschildren'] = 'false';
            }



            if($childrows != '0') {

                $resfft = $mysqli->query("SELECT * FROM vehicle_vars WHERE parentid='$id' ORDER BY value ASC");
                $y=0;
                while($rowfft = $resfft->fetch_assoc()) {
                    $list[$c][$s][$y] = $rowfft;

                    $sel_sub = explode(',', $selected_val);
                    if($sel_sub[1] == $rowfft['value']) {
                        $list[$c][$s][$y]['selected'] = 'true';
                    } else {
                        $list[$c][$s][$y]['selected'] = 'false';
                    }

                    $y++;
                }

            }

            $s++;
        }
        $c++;
    }


    if($addoredit == 'edit') {
        $resgmm = $mysqli->query("SELECT value FROM vehicle_meta WHERE vehicleid='$vehicleid' AND type='location' LIMIT 1");
        $rowm = $resgmm->fetch_assoc();
        $selected_val = $rowm['value'];
    }
    $cyear = date('y');
    $list[0]['name'] = 'Location';
    $list[0]['haschildren'] = 'true';
    $list[0]['currentyear'] = $cyear;
    $list[0]['vehicleid'] = $vehicleid;

    $resffw = $mysqli->query("SELECT stockkey, stockval FROM vehiclelookup WHERE stockkey='C' ORDER BY stockval DESC LIMIT 1");
    $rowffw = $resffw->fetch_assoc();
    $numb = $rowffw['stockval'];
    if($numb == '') { $numb = 0; }
    $list[0]['cstock'] = $numb + 1;

    $resffw = $mysqli->query("SELECT stockkey, stockval FROM vehiclelookup WHERE stockkey='N' ORDER BY stockval DESC LIMIT 1");
    $rowffw = $resffw->fetch_assoc();
    $numb = $rowffw['stockval'];
    if($numb == '') { $numb = 0; }
    $list[0]['nstock'] = $numb + 1;

    $resffw = $mysqli->query("SELECT stockkey, stockval FROM vehiclelookup WHERE stockkey='S' ORDER BY stockval DESC LIMIT 1");
    $rowffw = $resffw->fetch_assoc();
    $numb = $rowffw['stockval'];
    if($numb == '') { $numb = 0; }
    $list[0]['sstock'] = $numb + 1;

    $resg = $mysqli->query("SELECT * FROM locations");
    $lnum = 0;
    while($row = $resg->fetch_assoc()) {
        $list[0][$lnum]['arrange'] = '0';
        $list[0][$lnum]['haschildren'] = 'false';
        $list[0][$lnum]['id'] = $row['id'];
        $list[0][$lnum]['isbutton'] = 'false';
        $list[0][$lnum]['locked'] = 'true';
        $list[0][$lnum]['name'] = 'Location';
        $list[0][$lnum]['parentid'] = '0';
        $list[0][$lnum]['showonline'] = 'true';
        $list[0][$lnum]['stockcode'] = $row['stockcode'];
        $list[0][$lnum]['stockcodelength'] = $row['usedvannumber'];
        $list[0][$lnum]['type'] = 'location';
        $list[0][$lnum]['value'] = $row['name'];
        $list[0][$lnum]['zip'] = $row['zip'];

        if($selected_val == $row['name']) {
            $list[0][$lnum]['selected'] = 'true';
        } else {
            $list[0][$lnum]['selected'] = 'false';
        }
        $lnum++;
    }





    $data =  json_encode($list);
    echo '{"items":';
    echo $data;
    echo '}';



}





if(isset($_GET['get_locations'])) {
    $c = 1;
    $a = 0;
    $list = array();
    $resg = $mysqli->query("SELECT * FROM locations");
    $lnum = 0;
    while($row = $resg->fetch_assoc()) {
        $list[0][$lnum] = $row;
        $lnum++;
    }


    $data =  json_encode($list);
    echo '{"locations":';
    echo $data;
    echo '}';

}

if(isset($_GET['get_rebates'])) {
    $vehicleid = $_GET['vehicleid'];
    $addoredit = $_GET['addoredit'];

    $c = 1;
    $a = 0;
    $list = array();
    $resg = $mysqli->query("SELECT * FROM global_discounts");
    $lnum = 0;
    while($row = $resg->fetch_assoc()) {

        $rid = $row['discountid'];
        $list[$lnum]['name'] = $row['name'];
        $list[$lnum]['rebateAmount'] = $row['amount'];
        $list[$lnum]['id'] = $row['discountid'];


        if($addoredit == 'edit'){


            $restg = $mysqli->query("SELECT discountid FROM discounts WHERE vehicleid='$vehicleid' AND rebateid='$rid'");

            $rcount = $restg->num_rows;
            if($rcount > 0) {
                $list[$lnum]['selected'] = 'true';
            } else {
                $list[$lnum]['selected'] = 'false';
            }
        } else {
            $list[$lnum]['selected'] = 'false';
        }

        $lnum++;
    }


    $data =  json_encode($list);
    echo '{"global_rebates":';
    echo $data;
    echo '}';

}





if(isset($_POST['removeimage'])) {
    $x = $_POST['imageid'];

    $thesql = $mysqli->query("SELECT * FROM pictures WHERE id='$x' LIMIT 1");
    $img2 = $thesql->fetch_assoc();
    $large = $img2['large'];
    $thumb = $img2['thumb'];
    unlink("../../Express2.0/imageup/$large");
    unlink("../../Express2.0/imageup/$thumb");
    $mysqli->query("DELETE FROM pictures WHERE id='$x' LIMIT 1");
}



if(isset($_POST['newids'])) {
    $x = $_POST['newids'];
    $y = explode(',',$x,999);
    $i = 0;
    foreach ($y as $value) {
        mysql_query( "UPDATE pictures SET
						arrange='$i'
						WHERE id='$value' LIMIT 1");
        $i++;
    }
}


if(isset($_GET['fetch_crops'])) {
    $id = $_GET['vehicleid'];
    $crop= array();
    $thesqlpic = $mysqli->query("SELECT large FROM pictures WHERE vehicleid='$id' AND cropped='false' ORDER BY arrange ASC");
    while($img2 = $thesqlpic->fetch_assoc()) {
        $crop[] = $img2['large'];
    }


    $jsn = json_encode($crop);
    echo $jsn;
    die;

}




function daysonlinesortfunc($b, $a) {
    if ($a['daysonline'] == $b['daysonline']) {
        return 0;
    }
    return ($a['daysonline'] < $b['daysonline']) ? -1 : 1;
}


if(isset($_GET['get_vehicle_aging'])) {


    $agingarray = array();
    $sqltwow = $mysqli->query( "SELECT * FROM vehiclelookup WHERE available!='sold' AND location!='' AND location IS NOT NULL  ORDER BY hold ASC");
    $voh = $sqltwow->num_rows;



    $c = 0;
    while($one = $sqltwow->fetch_assoc()) {
        $vehicleid = $one['vehicleid'];
        $hold = $one['hold'];
        $salepending = $one['salepending'];

        $m= array();
        $sqltwox = $mysqli->query( "SELECT * FROM vehicle_meta WHERE vehicleid='$vehicleid'");
        while($met = $sqltwox->fetch_assoc()) {
            $m[$met['type']] = $met['value'];
        }
        $agingarray[$c]['year'] = $m['year'];
        $agingarray[$c]['make'] = $m['make'];
        $agingarray[$c]['model'] = $m['model'];
        $agingarray[$c]['stock'] = $m['stock'];
        $agingarray[$c]['location'] = $m['location'];
        $agingarray[$c]['category'] = $m['category'];
        $agingarray[$c]['arrival_status'] = $m['arrival_status'];
        $agingarray[$c]['hold'] = $hold;
        $agingarray[$c]['conversion'] = substr($m['conversion'],0,30);
        $agingarray[$c]['salepending'] = $m['salepending'];
        $agingarray[$c]['condition'] = $m['newused'].'-'.$m['conversion_newused'];

        $agingarray[$c]['holdspstatus'] = 'A';
        $agingarray[$c]['holdicon'] = 'available.svg';
        if($hold != 'available' && $salepending=='false') {
            $agingarray[$c]['holdspstatus'] = 'H';
            $agingarray[$c]['holdicon'] = 'hold.svg';
        }


        if($hold != 'available' && $salepending=='true') {
            $agingarray[$c]['holdspstatus'] = 'P';
            $agingarray[$c]['holdicon'] = 'salepending.svg';
        }


        if($m['eta'] != '') {
            $agingarray[$c]['activeta'] = strtotime($m['eta']);
        } else {
            $agingarray[$c]['activeta'] = $one['listdate'];
        }
        $agingarray[$c]['daysonline'] = round((time() - $agingarray[$c]['activeta']) / 86400,0);
        $agingarray[$c]['agingclass'] = 'vehicleaging zero';
        if($agingarray[$c]['daysonline'] > 30) { $agingarray[$c]['agingclass'] = 'vehicleaging thirty'; }
        if($agingarray[$c]['daysonline'] > 60) { $agingarray[$c]['agingclass'] = 'vehicleaging sixty'; }
        if($agingarray[$c]['daysonline'] > 90) { $agingarray[$c]['agingclass'] = 'vehicleaging ninety'; }
        if($agingarray[$c]['daysonline'] > 120) { $agingarray[$c]['agingclass'] = 'vehicleaging onetwenty'; }
        if($agingarray[$c]['daysonline'] > 365) { $agingarray[$c]['agingclass'] = 'vehicleaging threesixtyfive'; }

        $agingarray[$c]['agingname'] = 'zero';
        if($agingarray[$c]['daysonline'] > 30) { $agingarray[$c]['agingname'] = 'thirty'; }
        if($agingarray[$c]['daysonline'] > 60) { $agingarray[$c]['agingname'] = 'sixty'; }
        if($agingarray[$c]['daysonline'] > 90) { $agingarray[$c]['agingname'] = 'ninety'; }
        if($agingarray[$c]['daysonline'] > 120) { $agingarray[$c]['agingname'] = 'onetwenty'; }
        if($agingarray[$c]['daysonline'] > 365) { $agingarray[$c]['agingname'] = 'threesixtyfive'; }


        if(strlen($agingarray[$c]['daysonline']) == 1){
            $agingarray[$c]['daysonline_five_dig'] = '0000'.$agingarray[$c]['daysonline'];
        }
        if(strlen($agingarray[$c]['daysonline']) == 2){
            $agingarray[$c]['daysonline_five_dig'] = '000'.$agingarray[$c]['daysonline'];
        }
        if(strlen($agingarray[$c]['daysonline']) == 3){
            $agingarray[$c]['daysonline_five_dig'] = '00'.$agingarray[$c]['daysonline'];
        }
        if(strlen($agingarray[$c]['daysonline']) == 4){
            $agingarray[$c]['daysonline_five_dig'] = '0'.$agingarray[$c]['daysonline'];
        }

        if($agingarray[$c]['conversion'] == false) { $agingarray[$c]['conversion'] = ''; }

        $c++;
    }
     uasort($agingarray, 'daysonlinesortfunc');


    $sortedArray = [];
    $c = 0;
    foreach ($agingarray as $xx) {
        $sortedArray[$c] = $xx;
        $c++;
    }
    $data =  json_encode($sortedArray, false, 512);
    echo $data;


    die;


}
























if(isset($_GET['get_inventory'])) {

    $vantype = $_GET['vantype'];
    if($vantype == 'shownonline') { $searchsql = "WHERE available!='sold' AND showonline='true'"; }
    if($vantype == 'allavail') { $searchsql = "WHERE available!='sold'"; }
    if($vantype == 'notonline') { $searchsql = "WHERE available!='sold' AND showonline='false'"; }
    if($vantype == 'hold') { $searchsql = "WHERE available!='sold' AND hold!='available'"; }
    if($vantype == 'sold') { $searchsql = "WHERE available='sold'"; }
    if($vantype == 'coming') { $searchsql = "WHERE available='25555'"; }
    if($searchsql == '') { $searchsql = "WHERE available!='sold' AND showonline='true'"; }






    $thesql = $mysqli->query("SELECT * FROM vehiclelookup $searchsql ORDER BY vehicleid DESC LIMIT 400");
    while($deone = $thesql->fetch_assoc()) {
        $id = $deone['vehicleid'];
        $vin = $deone['vin'];
        $year = $deone['year'];
        $make = $deone['make'];
        $yre = $deone['year'];
        $trim = $deone['trim'];
        $model = $deone['model'];
        $ecolor = $deone['ecolor'];
        $stock = $deone['stock'];
        $conversionlogo = $deone['conversionlogo'];
        $icolor = $deone['icolor'];
        $picone = $deone['one'];
        $thumb = $deone['thumb'];
        $price = $deone['price'];
        $miles = $deone['miles'];
        $hold = $deone['hold'];
        $newused = $deone['newused'];
        $listdate = $deone['listdate'];
        $conversion = $deone['conversion'];
        $nadaprice = $deone['nadaprice'];
        $kbbprice = $deone['kbbprice'];
        $location = $deone['location'];
        $category = $deone['category'];

        $adminnotes = $deone['adminnotes'];
        $youtube = $deone['youtube'];
        $vehicleid = $id;
        $pricef = $price;

        $price = number_format($price);
        $miles = number_format($miles);
        if ($conversionlogo == '') { $conversionlogo = "noConversion.jpg"; }

        // Set The Default Image If There Are None Loaded Yet.
        $thumb = '/img/novan.jpg';

        $thesqlpic = $mysqli->query("SELECT large FROM pictures WHERE vehicleid='$id' ORDER BY arrange ASC LIMIT 1");
        while($img2 = $thesqlpic->fetch_assoc()) {

            $thumb = $img2['large'];
            $thumb = "/Express2.0/imageup/$thumb";
        }


        $m= array();
        $thesqlmeta = $mysqli->query("SELECT * FROM vehicle_meta WHERE vehicleid='$id'");
        $searchstring = '';
        while($met = $thesqlmeta->fetch_assoc()) {
            $m[$met['type']] = $met['value'];
            $searchstring .= $met['value'] . ' | ';
        }


        ?>


        <article class="col-lg-2 col-md-3 col-sm-6 col-xs-12 ima_van maxheight"  data-vehicleid="<? echo $id; ?>"<?
        $thesqlmetad = $mysqli->query("SELECT type, name FROM vehicle_vars GROUP BY type ORDER BY type ASC");
        $showmeonline = 'true';
        while($metd = $thesqlmetad->fetch_assoc()) {

            echo 'data-'.$metd['type'].'="'.$m[$metd['type']].'" ';


        }




        //if (strpos($m['category'], 'Not Ready') === 0) {
        //     $m['category'] = 'Not Ready';
        // }

        ?> data-category="<? echo $m['category']; ?>" data-location="<? echo $m['location']; ?>">

            <div class="vehiclewrap begin_editing_vehicle_btn <? if($hold != 'available') { echo 'holdhighlighter'; } ?>" data-gotovehicleid="<? echo $id; ?>">
                <? if($hold != 'available') { ?>
                    <div class="holdname">HOLD: <? echo $hold; ?></div>
                <? } ?>
                <div class="invimgwrap">

                    <a href="javascript:void(0)">
                        <img  src="<? echo $thumb; ?>" class="img-responsive vanimg"  alt="<? echo "$year $make $model $conversion Wheelchair Van For Sale"; ?>"></a></div>


                <div class="baseborder">Status: <? echo $m['arrival_status']; ?></span></div>
                <div class="baseborder">Category: <? echo $m['category']; ?></span></div>
                <div  title="<? echo $id; ?>" class="invtextwrap vehicledetails">

                    <div class="baseborder"><span><strong><? echo $m['year'] . ' ' . $m['make']; ?></strong></div>


                    <div class="baseborder"><span class="pull-right"> <? echo $m['stock']; ?></span>Stock:</div>
                    <div class="baseborder"><span class="pull-right"> <? echo number_format(str_replace(',','',$m['miles'])); ?></span>Miles:</div>
                    <div class="baseborder"><span class="pull-right"> <? echo $m['location']; ?></span>Location:</div>
                    <div class="baseborder"><span class="pull-right"></span><span style="font-size: 12px"><? echo $m['conversion']; ?></span></div>
                    <div class="baseborder"><span class="pull-right"><? if(number_format($m['price_total_admin']) != 0) echo '$'.number_format($m['price_total_admin']); ?></span>Cost: </div>
                    <div><span class="pull-right"> <strong><? if(number_format($m['price_total_public']) != 0) echo '$'. number_format($m['price_total_public']); ?></strong></span><strong>Retail Total:</strong></div>
                    <div style="display: none;"><? echo strtolower($m['stock']); ?> <? echo $vin; ?>
                        <? if($m['price_total_public'] == '' || $m['price_total_public'] == '0') { echo ' noprice '; }  ?>

                        <? if($m['conversion'] == '') { echo ' noconversion '; }  ?>

                    </div>
                </div>
            </div>
        </article>


    <? }
    //$length = ob_get_length();
    //header('Content-Length: '.$length."\r\n");
    //header('Accept-Ranges: bytes'."\r\n");
    //ob_end_flush();
} ?>


	
