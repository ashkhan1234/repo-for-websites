<link rel="stylesheet" href="../plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="../plupload/js/plupload.full.min.js"></script>
<script type="text/javascript" src="../plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<script>


    // Lets Define Main Variables
    var apiurl = 'https://api.edmunds.com';
    var apikey = 'zf673usxbhbvw23a9u24mrtu';
    var cv = '';
    var cs = '';
    var co = '';
    var writeme = '';
    var count = 1;
    var tsr = 0;
    var addoredit = '';
    var newvin = '';
    var newyear = '';
    var newyearcheck = '';
    var newmake = '';
    var newmodel = '';
    var loadonlymode = false;
    var addsavedonce = false;
    var basicVinDataOnly = false;


    var edmunds_rebates = new Object();
    var global_rebates = new Object();
    var addVanObj = new Object();
    var editObj = new Object();
    var makesAndModels = [];
    var soObj = new Object();
    addVanObj.vars = [];
    addVanObj.vars.category = '';
    $.ajaxSetup({ cache: false });





    // Save Exterior Colors & Manage Scope
    function edit_exterior_scope(edit) {


        $('.color-buttons').click(function() {

            var styleid = $(this).attr('data-ecid');
            addVanObj.ecolorname = $(this).attr('data-name');
            addVanObj.ecolorhex = '#' +  $(this).attr('data-hex');

            update_meta(vehicleid, 'ecolor', addVanObj.ecolorname, 'Exterior Color', '0' );
            update_meta(vehicleid, 'ecolor_hex', addVanObj.ecolorhex, 'e_color_code', '0' );

            $('#exterior_color_input').val($(this).attr('data-name'));
            $('#e_exterior').slideUp(300);
        });

    }




    // Save Interior Colors & Manage Scope
    function edit_interior_scope(edit) {
        $('.icolor-buttons').click(function() {

            var styleid = $(this).attr('data-ecid');
            addVanObj.icolorname = $(this).attr('data-name');
            addVanObj.icolorhex = '#' +  $(this).attr('data-hex');

            update_meta(vehicleid, 'icolor', addVanObj.icolorname, 'Interior Color', '0' );
            update_meta(vehicleid, 'icolor_hex', addVanObj.icolorhex, 'i_color_code', '0' );

            $('#interior_color_input').val($(this).attr('data-name'));
            $('#i_exterior').slideUp(300);
            $('#van_details').show();
            checkWarnEmpty();
        });



    }




    function hide_tabs() {
        $('.tabs').hide();
    }

    function go_to_tab(tabid) {
        $('.tabs').hide();
        $(tabid).show();
    }


    $('body').on('click', '.begin_editing_vehicle_btn', function() {
        $('.begin_editing_vehicle_btn').prop('disabled', true);
        vehicleid = $(this).attr('data-gotovehicleid');
        addoredit = 'edit';
        editObj = {};
        enter_single_vehicle();
        addsavedonce = true;


        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                edit_vehicle: 'true',
                vehicleid: vehicleid

            },
            type: 'POST',
            dataType: 'json',
            success: function(data){
                editObj = data;
                console.log(editObj);
                found_valid_vin();
                addsavedonce = true;
                buildCurrentExpenses();
                buildCurrentDiscounts();
            }
        });

        $.ajax({
            url: 'ajax/listvans.php',
            data: { get_standard_options: 'true', vehicleid: vehicleid },
            type: 'POST',
            dataType: 'json',
            success: function(data){
                addVanObj.standard_options = JSON.parse(data);
                $.ajax({
                    url: 'ajax/listvans.php',
                    data: { get_optional_options: 'true', vehicleid: vehicleid },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        addVanObj.optional_options = JSON.parse(data);


                        addVanObj.specs = [];
                        addVanObj.specs.ac = [];
                        addVanObj.specs.crash = []; //Crash Test Ratings
                        addVanObj.specs.brakes = []; //Brake System
                        addVanObj.specs.mirrors = []; //Mirrors
                        addVanObj.specs.steering_wheel = []; //Steering Wheel
                        addVanObj.specs.interior_trim = []; //Interior Trim
                        addVanObj.specs.child_saftey = []; //Child Safety
                        addVanObj.specs.guages = []; // Instrumentation
                        addVanObj.specs.outlets = []; // Power Outlets
                        addVanObj.specs.shocks = []; // Suspension
                        addVanObj.specs.front_seats = []; // 1st Row Seats
                        addVanObj.specs.exterior_dimensions = []; //Exterior Dimensions
                        addVanObj.specs.specifications = []; //Specifications
                        addVanObj.specs.windows = []; //Windows
                        addVanObj.specs.security = []; //Security
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'ac' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.ac = JSON.parse(data);
                                if(addVanObj.specs.ac){
                                    addVanObj.specs.ac['name'] = 'Air Conditioning';
                                }


                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'brakes' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.brakes = JSON.parse(data);
                                if(addVanObj.specs.brakes){
                                    addVanObj.specs.brakes['name'] = 'Brakes';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'mirrors' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.mirrors = JSON.parse(data);
                                if(addVanObj.specs.mirrors){
                                    addVanObj.specs.mirrors['name'] = 'Mirrors';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'steering_wheel' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.steering_wheel = JSON.parse(data);
                                if(addVanObj.specs.steering_wheel){
                                    addVanObj.specs.steering_wheel['name'] = 'Steering Wheel';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'exterior_lights' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.exterior_lights = JSON.parse(data);
                                if(addVanObj.specs.exterior_lights){
                                    addVanObj.specs.exterior_lights['name'] = 'Steering Wheel';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'interior_trim' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.interior_trim = JSON.parse(data);
                                if(addVanObj.specs.interior_trim){
                                    addVanObj.specs.interior_trim['name'] = 'Interior Trim';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'child_saftey' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.child_saftey = JSON.parse(data);
                                if(addVanObj.specs.child_saftey){
                                    addVanObj.specs.child_saftey['name'] = 'Child Saftey';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'interior_trim' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.interior_trim = JSON.parse(data);
                                if(addVanObj.specs.interior_trim){
                                    addVanObj.specs.interior_trim['name'] = 'Interior Trim';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'guages' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.guages = JSON.parse(data);
                                if(addVanObj.specs.guages){
                                    addVanObj.specs.guages['name'] = 'Guages';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'outlets' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.outlets = JSON.parse(data);
                                if(addVanObj.specs.outlets){
                                    addVanObj.specs.outlets['name'] = 'Outlets';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'shocks' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.shocks = JSON.parse(data);
                                if(addVanObj.specs.shocks){
                                    addVanObj.specs.shocks['name'] = 'Shocks';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'front_seats' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.front_seats = JSON.parse(data);
                                if(addVanObj.specs.front_seats){
                                    addVanObj.specs.front_seats['name'] = 'Front Seats';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'exterior_dimensions' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.exterior_dimensions = JSON.parse(data);
                                if(addVanObj.specs.exterior_dimensions){
                                    addVanObj.specs.exterior_dimensions['name'] = 'Exterior Dimensions';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'specifications' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.specifications = JSON.parse(data);
                                if(addVanObj.specs.specifications){
                                    addVanObj.specs.specifications['name'] = 'Specifications';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'windows' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.windows = JSON.parse(data);
                                if(addVanObj.specs.windows){
                                    addVanObj.specs.windows['name'] = 'Windows';
                                }
                            }
                        });
                        $.ajax({
                            url: 'ajax/listvans.php',
                            data: { get_specs: 'true', vehicleid: vehicleid, type: 'security' },
                            type: 'POST',
                            dataType: 'json',
                            success: function(data){
                                addVanObj.specs.security = JSON.parse(data);
                                if(addVanObj.specs.security){
                                    addVanObj.specs.security['name'] = 'Security';
                                }

                                setTimeout(function(){
                                    load_options_tab(true);
                                }, 2000);
                            }
                        });







                    }
                });
            }
        });




    });






    var sc_height = $(window).height() -220;
    var sc_height_wrapper = $(window).height() -118;
    $('.screen-height').css('min-height', sc_height_wrapper+'px');


    var maxHeight = 0;
    $(".maxheight").each(function(){
        if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
    });
    $(".maxheight").height(maxHeight);




    $('body').on('keyup', '.check_empty_input', function() {
        if($(this).val() != '') {
            $(this).removeClass('empty_input');
            $(this).prev().removeClass('empty_input_label');
        } else {
            $(this).addClass('empty_input');
            $(this).prev().addClass('empty_input_label');
        }
    });




    $('body').on('keyup', '.thousands', function() {
        //$(this).val(thousands($(this).val()));
    });

    $('body').on('click', '#submit_vin_btn', function() {
        newvin = $('#mondal_contents').find('.vin_add_input').val();
        newyearcheck = $('#mondal_contents').find('.year_input_x option:selected').attr('data-year');
        newyear = $('#mondal_contents').find('.year_input_x').val();
        newmake = $('#mondal_contents').find('.make_input_x').val();
        newmodel = $('#mondal_contents').find('.model_input_x').val();
        addoredit = 'add';
        addVanObj.vin = newvin;

        if(newyearcheck == '') {
            $.ajax({
                url: apiurl+'/api/vehicle/v2/vins/'+newvin,
                data: {
                    api_key : apikey,
                    fmt: 'json'
                },
                type: 'GET',
                dataType: "json",
                success: function(data){
                    validate_edmunds(data);
                },
                error : function(){
                    vin_error();
                }
            });
        } else {
            $.ajax({

                url: apiurl+'/api/vehicle/v2/'+newmake+'/'+newmodel+'/'+newyear+'/styles?view=full',
                data: {
                    api_key : apikey,
                    fmt: 'json'
                },
                type: 'GET',
                dataType: "json",
                success: function(data){
                    validate_edmunds(data);
                },
                error : function(){
                    vin_error();
                }
            });
        }

    });




    $('body').on('click', '#add_from_scratch', function() {
        $(this).parent().html('One Moment...');
        newvin = $('#mondal_contents').find('.vin_add_input').val();
        newyear = $('#mondal_contents').find('.year_input_x').val();
        addoredit = 'add';
        addVanObj.vin = newvin;


        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                vehicle_vars: 'true',
                addoredit: addoredit
            },
            type: 'GET',
            dataType: 'json',
            success: function(data){
                vehicle_vars = data;

                addsavedonce = false;
                vehicleid = vehicle_vars.items[0].vehicleid;
                addVanObj.conversionid = '';
                update_meta(vehicleid, 'vin', newvin, 'VIN', '0' );
                update_meta(vehicleid, 'year', newyear, 'Year', '0' );
                update_vehiclelookup(vehicleid, 'vin', newvin);
                update_vehiclelookup(vehicleid, 'year', newyear);


                addoredit = 'edit';
                $.ajax({
                    url: 'ajax/listvans.php',
                    data: {
                        edit_vehicle: 'true',
                        vehicleid: vehicleid

                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        editObj = data;
                        editObj.vin = newvin;
                        editObj.year = newyear;
                        exit_mondal();
                        enter_single_vehicle();
                        found_valid_vin();
                        addsavedonce = false;
                        buildCurrentExpenses();
                        buildCurrentDiscounts();
                    }
                });

            }
        });

    });






    function validate_edmunds(data) {
        if(data.errorType) {
            //alert(data.errorType + '..... ' + data.message);
            //console.log(data);
            vin_error();
        } else {
            cv = data;
            console.log(cv);
            found_valid_vin();
            $('#vin_form').hide();
            iniafterajax();
            exit_mondal();
            addsavedonce = false;
            enter_single_vehicle();
        }
    }



    function vin_error() {
        $('.vin_not_found_alert').show();
    }



    $('body').on('click', '.add_van_manually_btn', function() {
        $('.add_vin_manually_form').show();
        $('.vin_not_found_alert').hide();
        $('.vin_add_input').hide();
    });

    var listofmakes;
    $('body').on('change', '#mondal_contents .year_input_x', function() {
            $.ajax({
                url: apiurl+'/api/vehicle/v2/makes',
                data: {
                    api_key : apikey,
                    fmt: 'json',
                    year: $('#mondal_contents .year_input_x').val(),
                    view: 'basic'
                },
                type: 'GET',
                dataType: "json",
                success: function(data){
                    console.log(data);
                    listofmakes = data;
                    var tmpstring;
                    $.each(data.makes, function( index, value ) {
                        tmpstring += "<option data-index="+index+" data-val="+value.name+" data-niceName="+value.niceName+">"+value.name+"</option>";
                    });
                    $('#mondal_contents .make_input_x').html(tmpstring);
                },
                error : function(){
                    vin_error();
                }
            });
    });

    $('body').on('change', '#mondal_contents .make_input_x', function() {
        var tmpstring;
        var makeIndex = $(this).find('option:selected').attr('data-index');
        console.log(makeIndex);
        $.each(listofmakes.makes[makeIndex].models, function( index, value ) {
            tmpstring += "<option data-index="+index+" data-val="+value.name+" data-niceName="+value.niceName+">"+value.name+"</option>";
        });
        $('.model_input_x').html(tmpstring);
    });




    function checkWarnEmpty() {
        $( ".warn-empty-input" ).each(function() {
            var tagName = $(this).find( ".warn-inspect" ).prop("tagName");

            if(tagName == 'INPUT' || tagName == 'TEXTAREA') {
                if ($(this).find(".warn-inspect").val() == '') {
                    $( this ).addClass('active');
                }else {
                    $( this ).removeClass('active');
                }
            }


            if(tagName == 'DIV') {
                if ($(this).find(".warn-inspect").html() == '') {
                    $( this ).addClass('active');
                }else {
                    $( this ).removeClass('active');
                }
            }

        });


    }



    function found_valid_vin() {
        // load up the vehicle dropdowns and variables
        enter_saving();
        //fetch_rebates();
        addsavedonce = false;
        if(addoredit == 'edit') {
            loadonlymode = true;
        }
        //console.log('Loadonlymode: ' + loadonlymode);

        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                vehicle_vars: 'true',
                addoredit: addoredit,
                vehicleid : vehicleid


            },
            type: 'GET',
            dataType: 'json',
            success: function(data){
                vehicle_vars = data;
                //alert('cstock: '+vehicle_vars.items[0].cstock);
                //alert('nstock: '+vehicle_vars.items[0].nstock);
                //alert('sstock: '+vehicle_vars.items[0].sstock);


                if(addoredit == 'add'){
                    addsavedonce = false;
                    vehicleid = vehicle_vars.items[0].vehicleid;
                    addVanObj.conversionid = '';
                    if(newyearcheck == '') {
                        update_vehiclelookup(vehicleid, 'vin', newvin);
                        update_vehiclelookup(vehicleid, 'year', cv.years[0].year);
                        update_vehiclelookup(vehicleid, 'make', cv.make.name);
                        update_vehiclelookup(vehicleid, 'model', cv.model.name);
                        update_vehiclelookup(vehicleid, 'stockkey', stockkey);
                        update_vehiclelookup(vehicleid, 'stockval', stockval);
                        update_meta(vehicleid, 'vin', newvin, 'VIN', '0' );
                        update_meta(vehicleid, 'year', cv.years[0].year, 'Year', '0' );
                        update_meta(vehicleid, 'make', cv.make.name, 'Make', '0' );
                        update_meta(vehicleid, 'model', cv.model.name, 'Model', '0' );
                    } else {
                        update_vehiclelookup(vehicleid, 'vin', newvin);
                        update_vehiclelookup(vehicleid, 'year', newyear);
                        update_vehiclelookup(vehicleid, 'make', newmake);
                        update_vehiclelookup(vehicleid, 'model', newmodel);
                        update_meta(vehicleid, 'vin', newvin, 'VIN', '0' );
                        update_meta(vehicleid, 'year', newyear, 'Year', '0' );
                        update_meta(vehicleid, 'make', newmake, 'Make', '0' );
                        update_meta(vehicleid, 'model', newmodel, 'Model', '0' );
                    }

                    go_to_tab('#options_tab');
                }

                exit_saving();
                build_sidebar_menus();



                // Add the conversion on edit
                if(addoredit == 'add'){
                    $('#selected_conversion').hide();
                    $('#dynamic_conversion_selector').show();
                } else {
                    if(editObj.conversionid.length != '-1') {
                        $.ajax({
                            url: 'https://www.blvd.com/api/conversions/index.php',
                            data: {
                                fmt: 'json',
                                conversionid: editObj.conversionid
                            },
                            type: 'GET',
                            dataType: "json",
                            success: function(data){
                                $('#selected_conversion').show();
                                $('#dynamic_conversion_selector').hide();
                                $('#selected_conversion_name').html('<h2>'+data[0].modelheading+'</h2>');
                                $('#selected_conversion_description').html(data[0].description);

                            }
                        });
                    } else {
                        $('#dynamic_conversion_selector').hide();
                        $('#selected_conversion').show();
                    }

                }





                fetch_van_images();
                fetch_crops();

                ///Ge Conversions from BLVD
                $.ajax({
                    url: 'https://www.blvd.com/api/conversions/index.php',
                    data: {
                        fmt: 'json'
                    },
                    type: 'GET',
                    dataType: "json",
                    success: function(data){
                        conversion_vars = data;
                        build_conversion_selector();


                    }
                });
            }
        });





        if(addoredit == 'add') {

            if (cv.stylesCount == 0) {
                basicVinDataOnly = true;
            } else {





                if (newyearcheck == '') {
                    console.log('Newyear =null')
                    $('#list_of_trims').html('<h2>Please Select A Trim Level</h2><div class="add-top-line"></div>');
                    console.log('before build trims')
                    $.each(cv.years[0].styles, function () {
                        console.log('IN build trims');
                        writeme = '' +
                            '<div class="row">' +
                            '<div class="col-sm-12 new_van_layout">' +
                            '<div id="trim" class="hidden">' + this.name + '</div>' +

                            '<div class="row">' +
                            '<div class="col-sm-3 container">' +

                            '<button class="form-buttons shiny-buttons gray-btn get_styles_btn" data-trim="' + this.name + '" data-styleid="' + this.id + '" style="margin-right: 15px;">' +
                            '<div class="shine"></div>' +
                            '<div class="shine-btn-text"><i class="icon-checkmark"></i> Select</div>' +
                            '</button></div><div class="col-xs-9 container"><div id="trim" class="match-h2">' + this.name + '</div></div>' +


                            '</div>';

                        $('#list_of_trims').append(writeme);


                    });
                } else {
                    console.log('select a trim');
                    $('#list_of_trims').html('<h2>Please Select A Trim Level</h2><div class="add-top-line"></div>');
                    $.each(cv.styles, function () {

                        writeme = '' +
                            '<div class="row">' +
                            '<div class="col-sm-12 new_van_layout">' +
                            '<div id="trim" class="hidden">' + this.name + '</div>' +

                            '<div class="row">' +
                            '<div class="col-sm-3 container">' +

                            '<button class="form-buttons shiny-buttons gray-btn get_styles_btn" data-trim="' + this.name + '" data-styleid="' + this.id + '" style="margin-right: 15px;">' +
                            '<div class="shine"></div>' +
                            '<div class="shine-btn-text"><i class="icon-checkmark"></i> Select</div>' +
                            '</button></div><div class="col-xs-9 container"><div id="trim" class="match-h2">' + this.name + '</div></div>' +


                            '</div>';

                        $('#list_of_trims').append(writeme);


                    });
                }

        }





        }




        $('#van_details').html('<div id="vehicleImageLine"></div>' +
            '<div class="add-top-line"></div>');

        writeme = ''+
            '<div class="row">' +
            '<div class="col-sm-12 new_van_layout">' +

            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Shown Online</div>' +
            '<div class="thevalue"><div id="show_online">' +
            '<div class="show_to_public" style="display: none;"></div>'+
            '<div class="hide_to_public" style="display: block;"></div>' +
            '</div></div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Date Listed Online</div>' +
            '<div class="thevalue" id="listdate">Today</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label" id="etalabel">Vehicle Aging Start Date</div>' +
            '<input id="eta" class="auto_update_meta date_input_selector" type="text" data-field="eta">' +
            '</div>' +
            '</div>' +


            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Year</div>' +
            '<div class="warn-empty-input">'+
            '<input id="year_field" placeholder="Year" class="auto_update_meta warn-inspect" type="text" data-field="year">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Make</div>' +
            '<div class="warn-empty-input">'+
            '<input id="make_field" placeholder="Make" class="auto_update_meta warn-inspect" type="text" data-field="make">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Model</div>' +
            '<div class="warn-empty-input">'+
            '<input id="model_field" placeholder="Model" class="auto_update_meta warn-inspect" type="text" data-field="model">' +
            '</div>' +
            '</div>' +
            '</div>' +


            '<div class="row crm-form">' +
            '<div class="col-sm-12 container maxheight">' +
            '<div class="label">Conversion</div>' +
            '<div class="warn-empty-input">'+
            '<div id="_conversion" class="thevalue inline warn-inspect"></div><div class="thevalue inline"><button class="editTabJump" data-target="conversion_tab"></button</div>' +
            '</div>' +
            '</div>' +
            '</div>' +


            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">VIN</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="vin_field" data-field="vin" class="auto_update_meta warn-inspect" placeholder="VIN">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label ">Trim Level</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="the_trim_field" data-field="trim" class="auto_update_meta warn-inspect" placeholder="Trim Level">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label"></div>' +
            '<div class="thevalue"></div>' +
            '</div>' +
            '</div>' +
            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label ">Engine</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="engine_field" data-field="engine" class="auto_update_meta warn-inspect" placeholder="Engine">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Highway MPG</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="mpghighway" data-field="mpg_hwy" class="auto_update_meta warn-inspect" placeholder="MPG Hwy">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">City MPG</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="mpgcity" data-field="mpg_city" class="auto_update_meta warn-inspect" placeholder="MPG City">' +
            '</div>' +
            '</div>' +
            '</div>' +


            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Stock</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" class="stock_number auto_update_meta the_stock warn-inspect" id="stock"  data-field="stock" placeholder="Enter Stock">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label ">Miles</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="miles" data-field="miles" class="thousands auto_update_meta warn-inspect" placeholder="Enter Miles">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Public Listed Price</div>' +
            '<div class="thevalue inline" id="sell_for_price">Call For Price</div><button class="editTabJump" data-target="pricing_tab"></button</div>' +
            '</div>' +
            '</div>' +

            '<div id="_colors" style="display: none"></div>' +
            '<div class="row crm-form">' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label">Exterior Color</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="exterior_color_input" data-field="ecolor" class="auto_update_meta warn-inspect" placeholder="Exterior Color">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div class="label ">Interior Color</div>' +
            '<div class="warn-empty-input">'+
            '<input type="text" id="interior_color_input" data-field="icolor" class="auto_update_meta warn-inspect" placeholder="Exterior Color">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-4 container maxheight">' +
            '<div></div>' +
            '<div></div>' +
            '</div>' +
            '</div>' +


            '<div class="row crm-form">' +
            '<div class="col-sm-12 container maxheight">' +
            '<div class="label">Description</div>' +
            '<div class="warn-empty-input">'+
            '<textarea type="text" id="description" data-field="description" class="auto_update_meta warn-inspect" placeholder="Please Enter A Description.  Keep in mind a conversion description will be automatically generated."></textarea>' +
            '</div>' +
            '</div>' +
            '</div>' +






            '</div>' +
            '</div>';

        $('#van_details').append(writeme);



        if(addoredit == 'add'){
            if(newyearcheck == '') {
                $('#year_field').val(cv.years[0].year);
                $('#make_field').val(cv.make.name);
                $('#model_field').val(cv.model.name);
                $('#_conversion').html('');

            } else {
                $('#year_field').val(newyear);
                $('#make_field').val(newmake);
                $('#model_field').val(newmodel);
                $('#_conversion').html('');

            }

        }


        if(addoredit == 'edit'){
            go_to_tab('#general_tab');

            $('#listdate').html(editObj.listdate);
            $('#eta').val(editObj.eta);

            $('#engine_field').val(editObj.engine);
            $('#vin_field').val(editObj.vin);
            $('#mpghighway').val(editObj.mpg_hwy);
            $('#mpgcity').val(editObj.mpg_city);
            $('#stock').val(editObj.stock);
            $('#miles').val(thousands(editObj.miles));
            if(Number(editObj.price_total_public)==0 || editObj.price_total_public=='') {
                $('#sell_for_price').html('Call For Price');
            } else {
                $('#sell_for_price').html('$' + thousands(editObj.price_total_public));
            }
            $('#description').html(editObj.description);
            $('#the_trim_field').val(editObj.trim);
            $('#exterior_color_input').val(editObj.ecolor);
            $('#interior_color_input').val(editObj.icolor);





            $('#year_field').val(editObj.year);
            $('#make_field').val(editObj.make);
            $('#model_field').val(editObj.model);
            $('#_conversion').html(editObj.conversion);







            var showonline = editObj.show_online;

            if(showonline == 'true') {
                $('.show_to_public').show();
                $('.hide_to_public').hide();
            } else {
                $('.show_to_public').hide();
                $('.hide_to_public').show();
            }

            $('#eta').datepicker();


            get_styles_on_edit();

            $('#_colors').show();



            $('.chassis_price_class').val(thousands(editObj.price_chassis_public));
            $('.conversion_price_class').val(thousands(editObj.price_conversion_public));
            $('.a_chassis_price_class').val(thousands(editObj.price_chassis_admin));
            $('.a_conversion_price_class').val(thousands(editObj.price_conversion_admin));
            if(editObj.conversion_status_public == "true"){
                $('.conversion_price_check_class').attr('checked','checked');
            } else {
                $('.conversion_price_check_class').removeAttr('checked');
            }



            $('#show_price_admin').prop('checked', editObj.show_price_admin);

            if(editObj.show_price_public == 'true') {
                $('#show_price_public').attr('checked','checked');
            } else {
                $('#show_price_public').removeAttr('checked');
            }
            if(editObj.show_price_admin == 'true') {
                $('#show_price_admin').attr('checked','checked');
            } else {
                $('#show_price_admin').removeAttr('checked');
            }
            addup();

            $('#youtube_id').val(editObj.youtubeid);
            $('#update_video').trigger('click');

            $('#admin_notes').val(editObj.admin_notes);
            $('#superadmin_notes').val(editObj.superadmin_notes);





            $('#year_field').parent().append('<select multiple class="popupSelectList yearSelectList"></select>');
            $('#make_field').parent().append('<select multiple class="popupSelectList makeSelectList"></select>');
            $('#model_field').parent().append('<select multiple class="popupSelectList modelSelectList"></select>');
            $('#the_trim_field').parent().append('<select multiple class="popupSelectList trimSelectList"></select>');
            $('#engine_field').parent().append('<select multiple class="popupSelectList engineSelectList"></select>');
            $('#exterior_color_input').parent().append('<select multiple class="popupSelectList ecolorSelectList"></select>');
            $('#interior_color_input').parent().append('<select multiple class="popupSelectList icolorSelectList"></select>');


        }






        if(basicVinDataOnly == true) {
            $('#mpghighway').val('');
            $('#mpgcity').val('');
            $('#vin_field').val(newvin);
            $('#list_of_trims').html('<div id="options_limited_data_notice" style="padding: 0 0 20px 0; background-color: rgb(188, 219, 255); color: #fff !important; margin-top: -20px;"><h2 style="font-weight:bold; line-height: 36px;">Limited data provided from the decoded VIN.</h2><p>' +
                'No need to worry, simply ensure you manually enter the following on the General tab.<br>' +
                '<ul style="margin-left: 50px;">' +
                '<li>Trim Level</li>' +
                '<li>Exterior Color</li>' +
                '<li>Exterior Color</li>' +
                '<li>Engine</li>' +
                '</ul></p>' +
                '<p><button style="font-size: 20px; margin-left:5px; margin-top:20px; margin-bottom: 60px;" class="blue-btn" onclick="removeLimitedDataNotice()" style="margin-top: 12px;">Continue Adding Vehicle <i class="fa fa-arrow-right"></i></button>' +
                '<br>What Causes This?<br>' +
                '<ul style="margin-left: 50px;">' +
                '<li>The vehicle is very new and data has not yet been collected but will be soon.</li>' +
                '<li>The vehicle is older and data has not been input and most likely never will be.</li>' +
                '<li>The vehicles manufacturer has not made the data available.</li>' +
                '</ul>' +
                '</p></div>');
        }
        basicVinDataOnly = false;

        checkWarnEmpty();

    }







    var bodyClickListener = function() {
        hideSelectMakeList($(".popupSelectList"))
        $( ".body_bg" ).unbind( "click" );
        $( ".popupSelectList" ).unbind( "change" );

    };

    var selectListChangeHandler = function(input, list) {
        hideSelectMakeList($(".popupSelectList"))
        $( ".popupSelectList" ).unbind( "change" );
        $( ".body_bg" ).unbind( "click" );
        var val = list.val();

        if(val == 'Other - Manually Type One') {
            input.val('');
            input.focus();
        } else {
            input.val(val);
        }
        input.trigger('change');
        checkWarnEmpty();
    };


    var tweenSelectMakeList;

    function showSelectMakeList(targetList) {
        tweenSelectMakeList = TweenMax.to(targetList, .2, {scale:1, ease: Power2.easeOut});
    }
    function hideSelectMakeList(targetList) {
        tweenSelectMakeList = TweenMax.to(targetList, .2, {scale:0, ease: Power2.easeOut});
    }


    function populateManualMakesModelsList(targetList, inputField, obj, keyorval) {
        var o = '';
        if(obj) {
            $.each(obj, function (key, val) {
                if(keyorval == 'val') {
                    var value = val;
                } else { var value = key; }

                if(inputField.val() != '' && inputField.val() == value) {
                    o += '<option class="currentVal" value="'+inputField.val()+'">'+value+'</option>';
                } else {
                    o += '<option>'+value+'</option>';
                }


            });
            o += '<option class="optionManualKey">Other - Manually Type One</option>';
            targetList.html(o);

            showSelectMakeList(targetList);
            targetList.scrollTop(0);

            $( ".body_bg" ).bind( "click", bodyClickListener );
            $( ".popupSelectList" ).bind( "change", function() {
                selectListChangeHandler(inputField, targetList);
            });
        } else {
            inputField.focus();
        }
    }

    $('body').on('click', '#make_field', function() {

        var target = $('.makeSelectList');
        var inputField = $(this);
        populateManualMakesModelsList(target, inputField, makesAndModels, 'key');
    });

    $('body').on('click', '#model_field', function() {

        var target = $('.modelSelectList');
        var inputField = $(this);
        var arr;
        if(makesAndModels[$('#make_field').val()]) {
            arr = makesAndModels[$('#make_field').val()];
        } else {
            arr = [];
        }
        populateManualMakesModelsList(target, inputField, arr, 'key');
    });



    $('body').on('click', '#year_field', function() {

        var target = $('.yearSelectList');
        var inputField = $(this);
        var yearsArray = [];
        var c = 30;
        var yearnow = Number(new Date().getFullYear()) + 1;
        while(c > 0) {
            yearsArray.push(yearnow);
            yearnow--;
            c--;
        }
        populateManualMakesModelsList(target, inputField, yearsArray, 'val');
    });



    $('body').on('click', '#the_trim_field', function() {

        var target = $('.trimSelectList');
        var inputField = $(this);
        var arr;
        if(makesAndModels[$('#make_field').val()][$('#model_field').val()]) {
            arr = makesAndModels[$('#make_field').val()][$('#model_field').val()]['trims'];
        } else {
            arr = [];
        }
        populateManualMakesModelsList(target, inputField, arr, 'val');
    });



    $('body').on('click', '#engine_field', function() {

        var target = $('.engineSelectList');
        var inputField = $(this);
        var arr;
        if(makesAndModels[$('#make_field').val()][$('#model_field').val()]) {
            arr = makesAndModels[$('#make_field').val()][$('#model_field').val()]['engines'];
        } else {
            arr = [];
        }
        populateManualMakesModelsList(target, inputField, arr, 'val');
    });

    $('body').on('click', '#exterior_color_input', function() {

        var target = $('.ecolorSelectList');
        var inputField = $(this);
        var arr;
        if(makesAndModels[$('#make_field').val()][$('#model_field').val()]) {
            arr = makesAndModels[$('#make_field').val()][$('#model_field').val()]['ecolors'];
        } else {
            arr = [];
        }
        populateManualMakesModelsList(target, inputField, arr, 'val');
    });

    $('body').on('click', '#interior_color_input', function() {

        var target = $('.icolorSelectList');
        var inputField = $(this);
        var arr;
        if(makesAndModels[$('#make_field').val()][$('#model_field').val()]) {
            arr = makesAndModels[$('#make_field').val()][$('#model_field').val()]['icolors'];
        } else {
            arr = [];
        }
        populateManualMakesModelsList(target, inputField, arr, 'val');
    });






    $('body').on('click', '.editTabJump', function() {
        var t = $(this).attr('data-target');
        $(".top-tabs-van-edit."+t).trigger("click")
    });







    function getMakesAndModels() {
        $.ajax({
            url: 'https://www.blvd.com/api/makes/index.php',
            data: {
                fmt: 'json',
                getMakesAndModels: true
            },
            type: 'GET',
            dataType: "json",
            success: function(data){
                makesAndModels = data;
            }
        });
    }
    getMakesAndModels();



    $('body').on('keyup', '#general_tab .warn-inspect', function() {
        checkWarnEmpty();
    });



function removeLimitedDataNotice() {
    $('.top-tabs-van-edit.general_tab').trigger('click')
    $('#options_limited_data_notice').hide();
    $('.options_nav_bar').show();
    $('.options_nav_bar').after('<div id="options_limited_data_notice" style="padding: 15px;"><strong>This vehicle has limited data provided from the decoded VIN. </strong><br>' +
        'The decoded VIN does not contain any chassis options.  ' +
        'This is due to one of the following reasons. <div style="margin-bottom: 15px;"><ul>' +
        '<li>The vehicle is very new and data has not yet been collected but will be soon.</li>' +
        '<li>The vehicle is older and data has not been input and most likely never will be.</li>' +
        '<li>The vehicles manufacturer has not made the data available.</li>' +
        '</ul></div></div>');
}









    function thousands(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }


    function fetch_rebates() {
        /*
         setTimeout(function(){
         $.ajax({
         url: 'https://api.edmunds.com/v1/api/incentive/incentiverepository/findincentivesbystyleidandzipcode',
         data: {
         api_key : apikey,
         styleid : addVanObj.styleid,
         zipcode : vehicle_vars.items[0][0].zip,
         fmt: 'json'
         },
         type: 'GET',
         dataType: "json",
         success: function(data){
         console.log('rebates');
         console.log(data);
         edmunds_rebates = data;
         if(data.errorType) {
         alert(data.errorType + '..... ' + data.message);
         }

         var writer = '';
         var found = false;
         var programids = [];
         var b = 0;
         $.each(edmunds_rebates.incentiveHolder, function() {

         found = false;
         if(this.contentType != 'TEXT_ONLY') {

         var sbid = this.subprogramId;


         $.each(programids, function() {
         if(this == sbid) {
         found = true;
         console.log('FOUND')
         }
         });
         programids[b] = this.subprogramId;
         b=b+1;
         if(found == false) {
         writer += '' +
         '<div class="row avail_rebates" style="min-height: 45px; padding-top: 5px;">' +
         '<div class="col-sm-12 new_van_layout">' +
         '<div class="row">' +
         '<div class="col-sm-2">' +
         '<div class="thevalue rebate_value">$'+thousands(this.rebateAmount)+'</div>' +
         '</div>' +
         '<div class="col-sm-9">' +
         '<div class="thevalue rebate_name">'+this.name+'</div>' +
         '<p style="font-size: 11px; color: #747474;">'+this.restrictions+'</p>' +
         '</div>' +
         '<div class="col-sm-1">' +
         '<div class="thevalue text-center">' +
         '<i class="fa fa-plus-circle add_this_rebate add_icon_btn" data-rebateid="'+this.id+'" data-amount="'+this.rebateAmount+'" data-name="'+this.name+'" data-restrictions="'+this.restrictions+'"></i>' +
         '<i id="selected_rebate_remove_btn'+this.id+'" class="fa fa-minus-circle remove_this_rebate remove_icon_btn" style="display: none;" data-rebateid="'+this.id+'" data-amount="'+this.rebateAmount+'" data-name="'+this.name+'"></i>' +
         '</div>' +
         '</div></div></div></div><div class="add-top-line"></div>';
         }
         }

         });
         $('#available_edmunds_rebates').html(writer);

         }
         });
         }, 2000);
         */






        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                get_rebates: 'true',
                addoredit : addoredit,
                vehicleid : vehicleid
            },
            type: 'GET',
            dataType: "json",
            success: function(data){
                //console.log('rebates');
                //console.log(data);
                global_rebates = data;

                var writer = '';
                var found = false;
                var programids = [];
                var b = 0;
                $.each(global_rebates.global_rebates, function() {


                    writer += '' +
                        '<div class="row avail_rebates" style="min-height: 45px; padding-top: 5px;">' +
                        '<div class="col-sm-12">' +
                        '<div class="row">' +
                        '<div class="col-sm-2">' +
                        '<div class="thevalue rebate_value">$'+thousands(this.rebateAmount)+'</div>' +
                        '</div>' +
                        '<div class="col-sm-9">' +
                        '<div class="thevalue rebate_name">'+this.name+'</div>' +
                        '</div>' +
                        '<div class="col-sm-1">' +
                        '<div class="thevalue text-center">' +
                        '<i class="fa fa-plus-circle add_this_rebate add_icon_btn auto_rebate_'+this.selected+'" data-rebateid="'+this.id+'" data-amount="'+this.rebateAmount+'" data-name="'+this.name+'" data-restrictions=""></i>' +
                        '<i id="selected_rebate_remove_btn'+this.id+'" class="fa fa-minus-circle remove_this_rebate remove_icon_btn" style="display: none;" data-rebateid="'+this.id+'" data-amount="'+this.rebateAmount+'" data-name="'+this.name+'"></i>' +
                        '</div>' +
                        '</div></div></div></div><div class="add-top-line"></div>';
                });
                $('#available_global_rebates').html(writer);
                $('.auto_rebate_true').trigger('click');
                skip_save_add_check = false;
            }
        });


    }

    var skip_save_add_check = true;


    $('body').on('click', '.add_this_rebate', function() {
        var rebateid = $(this).attr('data-rebateid');
        var rebatetype = '';
        if(rebateid.length > 2) {
            rebatetype = 'edmunds';
        } else {
            rebatetype = 'global';
        }
        var rebateamount = $(this).attr('data-amount');
        var rebatename = $(this).attr('data-name');
        var restrictions = $(this).attr('data-restrictions');
        $(this).hide();
        $(this).next().show();

        var wr = '<div class="each_rebate" id="rebate_'+rebateid+'" data-rebatetype="'+rebatetype+'" data-rebateid="'+rebateid+'" data-amount="'+rebateamount+'" data-name="'+rebatename+'">' +
            '<div class="row"><div class="col-xs-12 new_van_layout">' +
            '<div class="row">' +
            '<div class="col-sm-4 container_no_label">' +
            '<div style="position: absolute; width: 15px;" class="thevalue">$</div>' +
            '<div class="thevalue rebate_value" style="padding-left: 16px; padding-bottom: 0px;">-'+thousands(rebateamount)+'</div>' +
            '</div>' +
            '<div class="col-sm-7 container_no_label">' +
            '<div class="thevalue rebate_name">'+rebatename+'</div>' +
            '<p class="rebate_rule" style="font-size: 11px; color: #747474; display: none;">'+restrictions+'</p>' +
            '</div>' +
            '<div class="col-sm-1 container_no_label">' +
            '<div class="thevalue text-center"><i data-theid="'+rebateid+'" class="fa fa-minus-circle remove_selected_rebate remove_icon_btn"></i></div>' +
            '</div></div></div></div></div>';
        $('#selected_rebates').append(wr);
        addup();
        if(skip_save_add_check == false) {
            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();

        }

        //save_pricing();
    });

    $('body').on('click', '.remove_this_rebate', function() {
        var rebateid = 'rebate_'+$(this).attr('data-rebateid');
        $('#'+rebateid).remove();
        $(this).hide();
        $(this).prev().show();
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();

        //save_pricing();
    });

    $('body').on('click', '.remove_selected_rebate', function() {
        var theid = $(this).attr('data-theid');
        $('#selected_rebate_remove_btn'+theid).trigger('click');
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();

        //save_pricing();

    });

    $('body').on('click', '.add_rebates_btn', function() {
        $('.rebates_price_screen').slideUp(500);
        $('#add_rebate').show();
        addup();
        //save_pricing();
    });

    $('body').on('click', '.exit_add_rebates_screen', function() {
        $('.rebates_price_screen').show(0);
        $('#add_rebate').slideUp(500);
        addup();
        //save_pricing();


    });

    $('body').on('click', '#change_selected_conversion', function() {
        $('#selected_conversion').slideUp(500);
        $('#dynamic_conversion_selector').slideDown(500);


    });

    $('body').on('click', '.hide_to_public', function() {

        // Move to visible to public
        $('.show_to_public').show();
        $('.hide_to_public').hide();
        update_vehiclelookup(vehicleid, 'showonline', 'true');


    });

    $('body').on('click', '.show_to_public', function() {


        $('.show_to_public').hide();
        $('.hide_to_public').show();
        update_vehiclelookup(vehicleid, 'showonline', 'false');


    });





    function get_styles_on_edit() {
        /*
         var styleid = editObj.styleid;

         $.ajax({
         url: apiurl+'/api/vehicle/v2/styles/'+styleid,
         data: {
         api_key : apikey,
         fmt: 'json',
         view: 'full'
         },
         type: 'GET',
         dataType: "json",
         success: function(data){
         cs = data;



         //Write Exterior Colors
         wtop = '<div id="e_exterior"><h2>Exterior Color</h2><div class="add-top-line"></div><div class="row"><div class="col-sm-12 new_van_layout"><div class="row">';
         wmid = '';
         $.each(cs.colors[1].options, function() {

         if(this.hasOwnProperty('colorChips')){

         wmid += '' +
         '<div class="col-sm-4 container">' +
         '<div id="ecolorname" class="colors-label">'+ this.manufactureOptionName +'</div>' +
         '<div class="thevalue">' +
         '<button class="form-buttons color-buttons" style="background-color: #'+this.colorChips.primary.hex+'" ' +
         'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="'+ this.colorChips.primary.hex +'">' +
         '<div class="shine"></div>' +
         '<div class="shine-btn-text"><i class="fa fa-check-circle-o"></i> Select</div>' +
         '</button>' +
         '</div>' +
         '</div>';
         } else {

         wmid += '' +
         '<div class="col-sm-4 container">' +
         '<div id="ecolorname" class="colors-label">'+ this.manufactureOptionName +'</div>' +
         '<div class="thevalue">' +
         '<button class="form-buttons color-buttons" style="background-color: #EEE" ' +
         'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="EEE">' +
         '<div class="shine"></div>' +
         '<div class="shine-btn-text"><i class="fa fa-check-circle-o"></i> Color Chip N/A</div>' +
         '</button>' +
         '</div>' +
         '</div>';
         }
         });
         wbot = '</div></div></div></div>';


         exteriorwritten = wtop + wmid + wbot;


         wmid = '';
         wtop = '<div id="i_exterior"><h2>Interior Color</h2><div class="add-top-line"></div><div class="row"><div class="col-sm-12 new_van_layout"><div class="row">';
         $.each(cs.colors[0].options, function() {
         wmid += '' +
         '<div class="col-sm-4 container colors">' +
         '<div class="colors-label">'+ this.manufactureOptionName +'</div>' +
         '<div class="thevalue">' +
         '<button class="form-buttons icolor-buttons" style="background-color: #'+this.colorChips.primary.hex+'" ' +
         'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="'+ this.colorChips.primary.hex +'">' +
         '<i class="fa fa-check-circle-o"></i> Select' +
         '</button>' +
         '</div>' +
         '</div>';
         });
         wbot = '</div></div></div></div>';
         exteriorwritten += wtop + wmid + wbot;
         $('#mondal_contents').html(exteriorwritten);
         edit_exterior_scope('changeclickhandler');
         edit_interior_scope('changeclickhandler');
         }
         });

         */
    }





    function iniafterajax(){

        $('.get_styles_btn').click(function() {

            var styleid = $(this).attr('data-styleid');

            addVanObj.styleid = styleid;

            addVanObj.trim = $(this).attr('data-trim');
            update_meta(vehicleid, 'trim', addVanObj.trim, 'Trim Level', '0' );


            $('#the_trim_field').val(addVanObj.trim);



            $('#list_of_trims').hide();


            $.ajax({
                url: apiurl+'/api/vehicle/v2/styles/'+styleid,
                data: {
                    api_key : apikey,
                    fmt: 'json',
                    view: 'full'
                },
                type: 'GET',
                dataType: "json",
                success: function(data){
                    cs = data;
                    //add_color();

                    add_engine();
                    update_meta(vehicleid, 'styleid', styleid, 'styleid', '0' );
                    update_meta(vehicleid, 'mpg_city', cs.MPG.city, 'MPG City', '0' );
                    update_meta(vehicleid, 'mpg_hwy', cs.MPG.highway, 'MPG Highway', '0' );




                }
            });


            ///api/vehicle/v2/styles/200477465/equipment?availability=standard&equipmentType=OTHER&fmt=json&api_key=zf673usxbhbvw23a9u24mrtu
            $.ajax({
                url: apiurl+'/api/vehicle/v2/styles/'+styleid + '/equipment',
                data: {
                    api_key : apikey,
                    fmt: 'json',
                    availability: 'standard',
                    view: 'full'
                },
                type: 'GET',
                dataType: "json",
                success: function(data){
                    //console.log(data);
                    co = data;
                    add_options();


                }
            });






        });

        function add_engine() {
            $('#engine_field').val(cs.engine.size + 'L ' + cs.engine.configuration + cs.engine.cylinder + ' ' + cs.engine.horsepower + 'hp');
            $('#mpghighway').val(cs.MPG.highway)
            $('#mpgcity').val(cs.MPG.city)
            $('#vin_field').val(addVanObj.vin)

            update_meta(vehicleid, 'engine', cs.engine.size + 'L ' + cs.engine.configuration + cs.engine.cylinder + ' ' + cs.engine.horsepower + 'hp', 'Engine', '0' );
        }



        function add_color() {

            ppp = JSON.stringify(addVanObj.specs.ac);
            update_specs(vehicleid, 'ac', ppp);

            ppp = JSON.stringify(addVanObj.specs.brakes);
            update_specs(vehicleid, 'brakes', ppp);

            ppp = JSON.stringify(addVanObj.specs.child_saftey);
            update_specs(vehicleid, 'child_saftey', ppp);

            ppp = JSON.stringify(addVanObj.specs.crash);
            update_specs(vehicleid, 'crash', ppp);

            ppp = JSON.stringify(addVanObj.specs.exterior_dimensions);
            update_specs(vehicleid, 'exterior_dimensions', ppp);

            ppp = JSON.stringify(addVanObj.specs.front_seats);
            update_specs(vehicleid, 'front_seats', ppp);

            ppp = JSON.stringify(addVanObj.specs.guages);
            update_specs(vehicleid, 'guages', ppp);

            ppp = JSON.stringify(addVanObj.specs.interior_trim);
            update_specs(vehicleid, 'interior_trim', ppp);

            ppp = JSON.stringify(addVanObj.specs.mirrors);
            update_specs(vehicleid, 'mirrors', ppp);

            ppp = JSON.stringify(addVanObj.specs.outlets);
            update_specs(vehicleid, 'outlets', ppp);

            ppp = JSON.stringify(addVanObj.specs.security);
            update_specs(vehicleid, 'security', ppp);

            ppp = JSON.stringify(addVanObj.specs.shocks);
            update_specs(vehicleid, 'shocks', ppp);

            ppp = JSON.stringify(addVanObj.specs.specifications);
            update_specs(vehicleid, 'specifications', ppp);

            ppp = JSON.stringify(addVanObj.specs.steering_wheel);
            update_specs(vehicleid, 'steering_wheel', ppp);

            ppp = JSON.stringify(addVanObj.specs.windows);
            update_specs(vehicleid, 'windows', ppp);


            ppp = JSON.stringify(addVanObj.standard_options);
            update_vehiclelookup(vehicleid, 'standard_options', ppp);

            ppp = JSON.stringify(addVanObj.optional_options);
            update_vehiclelookup(vehicleid, 'optional_options', ppp);

            go_to_tab('#general_tab');
            $('#van_details').hide();
            $('#exterior_colors_div').show();
            $('#interior_colors_div').show();




            var wtop = '';
            var wmid = '';
            var wbot = '';



//Write Exterior Colors
            wtop = '<div id="e_exterior"><h2>Exterior Color</h2><div class="add-top-line"></div><div class="row"><div class="col-sm-12 new_van_layout"><div class="row">';
            $.each(cs.colors[1].options, function() {

                if(this.hasOwnProperty('colorChips')){

                    wmid += '' +
                        '<div class="col-sm-4 container">' +
                        '<div id="ecolorname" class="colors-label">'+ this.manufactureOptionName +'</div>' +
                        '<div class="thevalue">' +
                        '<button class="form-buttons color-buttons" style="background-color: #'+this.colorChips.primary.hex+'" ' +
                        'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="'+ this.colorChips.primary.hex +'">' +
                        '<div class="shine"></div>' +
                        '<div class="shine-btn-text"><i class="fa fa-check-circle-o"></i> Select</div>' +
                        '</button>' +
                        '</div>' +
                        '</div>';
                } else {
                    wmid += '' +
                        '<div class="col-sm-4 container">' +
                        '<div id="ecolorname" class="colors-label">'+ this.manufactureOptionName +'</div>' +
                        '<div class="thevalue">' +
                        '<button class="form-buttons color-buttons" style="background-color: #EEE" ' +
                        'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="EEE">' +
                        '<div class="shine"></div>' +
                        '<div class="shine-btn-text"><i class="fa fa-check-circle-o"></i> Color Chip N/A</div>' +
                        '</button>' +
                        '</div>' +
                        '</div>';
                }
            });
            wbot = '</div></div></div></div>';
            $('#exterior_colors_div').html(wtop + wmid + wbot);
            wmid = '';
            $('#_colors').show();
            scroll_to('#exterior_colors_div');
            edit_exterior_scope('firstadd');







// Write Interior
            wtop = '<div id="i_exterior"><h2>Interior Color</h2><div class="add-top-line"></div><div class="row"><div class="col-sm-12 new_van_layout"><div class="row">';
            $.each(cs.colors[0].options, function() {
                wmid += '' +
                    '<div class="col-sm-4 container colors">' +
                    '<div class="colors-label">'+ this.manufactureOptionName +'</div>' +
                    '<div class="thevalue">' +
                    '<button class="form-buttons icolor-buttons" style="background-color: #'+this.colorChips.primary.hex+'" ' +
                    'data-ecid="'+ this.id +'" data-name="'+ this.manufactureOptionName +'" data-hex="'+ this.colorChips.primary.hex +'">' +
                    '<i class="fa fa-check-circle-o"></i> Select' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            });
            wbot = '</div></div></div></div>';
            $('#interior_colors_div').html(wtop + wmid + wbot);


            edit_interior_scope('firstadd');

        }



        function add_options() {




            wmid = '';
            // Write Options
            wtop = '<h2>Chassis Options</h2>';

            var cvar;
            var cn;
            var cd;

            addVanObj.specs = [];
            addVanObj.specs.ac = [];
            addVanObj.specs.crash = []; //Crash Test Ratings
            addVanObj.specs.brakes = []; //Brake System
            addVanObj.specs.mirrors = []; //Mirrors
            addVanObj.specs.steering_wheel = []; //Steering Wheel
            addVanObj.specs.interior_trim = []; //Interior Trim
            addVanObj.specs.child_saftey = []; //Child Safety
            addVanObj.specs.guages = []; // Instrumentation
            addVanObj.specs.outlets = []; // Power Outlets
            addVanObj.specs.shocks = []; // Suspension
            addVanObj.specs.exterior_lights = []; // Suspension
            addVanObj.specs.front_seats = []; // 1st Row Seats
            addVanObj.specs.exterior_dimensions = []; //Exterior Dimensions
            addVanObj.specs.specifications = []; //Specifications
            addVanObj.specs.windows = []; //Windows
            addVanObj.optional_options = []; // Nci Optional Facet -- Rename To: Top Features
            addVanObj.standard_options = []; //Tmvu Feature
            addVanObj.specs.security = []; //Security


            $.each(co.equipment, function() {



                cn = 'Tmvu Feature';
                cd = '';
                cvar = addVanObj.standard_options;
                if(this.name === cn) {
                    cvar.name = 'Top Features';
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.value,
                            value: 'true'
                        });
                    });
                }


                cn = 'Nci Standard Facet';
                cd = '';
                cvar = addVanObj.standard_options;
                if(this.name === cn) {
                    cvar.name = 'Top Features';
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: 'true'
                        });
                    });
                }

                cn = 'Nci Optional Facet';
                cd = '';
                cvar = addVanObj.optional_options;
                if(this.name === cn) {
                    cvar.name = 'Top Features';
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: 'Optional Equipment May Not Be Installed.'
                        });
                    });
                }

                cn = 'Security';
                cd = '';
                cvar = addVanObj.specs.security;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Windows';
                cd = '';
                cvar = addVanObj.specs.windows;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Specifications';
                cd = '';
                cvar = addVanObj.specs.specifications;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Exterior Dimensions';
                cd = '';
                cvar = addVanObj.specs.exterior_dimensions;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = '1st Row Seats';
                cd = '';
                cvar = addVanObj.specs.front_seats;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Suspension';
                cd = '';
                cvar = addVanObj.specs.shocks;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Power Outlets';
                cd = '';
                cvar = addVanObj.specs.outlets;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Instrumentation';
                cd = '';
                cvar = addVanObj.specs.guages;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }


                cn = 'Child Safety';
                cd = '';
                cvar = addVanObj.specs.child_saftey;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Interior Trim';
                cd = '';
                cvar = addVanObj.specs.interior_trim;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Exterior Lights';
                cd = '';
                cvar = addVanObj.specs.exterior_lights;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Steering Wheel';
                cd = '';
                cvar = addVanObj.specs.steering_wheel;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Steering Wheel';
                cd = '';
                cvar = addVanObj.specs.steering_wheel;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Mirrors';
                cd = '';
                cvar = addVanObj.specs.mirrors;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Brake System';
                cd = '';
                cvar = addVanObj.specs.brakes;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Crash Test Ratings';
                cd = '';
                cvar = addVanObj.specs.crash;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cd;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

                cn = 'Air Conditioning';
                cd = '';
                cvar = addVanObj.specs.ac;
                if(this.name === cn) {
                    cvar.name = cn;
                    cvar.description = cn;
                    $.each(this.attributes, function() {
                        cvar.push({
                            name: this.name,
                            value: ucwords(this.value)
                        });
                    });
                }

            });

            wmid = '';
            wtop = '' +
                '<div id="optional_options_wrapper"><h2>Please Select Optional Features</h2>' +
                '<p>We have found and already added <strong>'+addVanObj.standard_options.length +
                '</strong> Options that were listed as standard, However we found ' +
                'the following options that are optional but may be included on this vehicle.  Please take a moment and select them.' +
                '<br><a href="javascript:void(0)" id="skip_all_optional_options">' +
                '<i class="icon-cancel-2"></i> Skip Remaining</a> | <a href="javascript:void(0)" id="add_all_optional_options"><i class="icon-checkmark"></i> Add Remaining</a></p>' +
                '<div class="row"><div class="col-xs-12 new_van_layout"><div id="optional_options_inner_wrapper"><div class="add-top-line"></div>';

            $.each(addVanObj.optional_options, function() {

                wmid += '' +
                    '<div class="row"><div class="col-sm-2 container"  style="height: 62px;">' +
                    '<button class="form-buttons shiny-buttons gray-btn optional_option" data-option="'+ this.name +'" data-type="remove">' +
                    '<div class="shine"></div>' +
                    '<div class="shine-btn-text"><i class="icon-cancel-2"></i> Skip</div>' +
                    '</button></div>' +
                    '<div class="col-sm-2 container"  style="height: 62px;">' +
                    '<button class="form-buttons shiny-buttons blue-btn optional_option" data-option="'+ this.name +'" data-type="add">' +
                    '<div class="shine"></div>' +
                    '<div class="shine-btn-text"><i class="icon-checkmark"></i> Add</div>' +
                    '</button></div>' +
                    '<div class="col-sm-8 container" style="height: 62px;"><span class="thevalue">' + this.name + '</span></div></div>';
            });

            wmid += '';

            wbot = '</div></div></div></div>';
            $('#_options_selector').html(wtop + wmid + wbot);


            $(".vertical_scroll_chris").animate({ scrollTop: $("#optional_options_wrapper").offset().top + -130  }, 800);


            $('.optional_option').click(function() {
                var value = $(this).attr('data-option');
                var type = $(this).attr('data-type');
                if(type == 'remove') {
                    var x = 0;
                    $.each(addVanObj.optional_options, function() {

                        if(this.name == value) {
                            addVanObj.optional_options.splice(x, 1);
                        }
                        x = x + 1;
                    });
                }
                $(this).parent().parent().slideUp(200, function(){
                    $(this).remove()
                });

                if($('#optional_options_inner_wrapper').children().length == 2) {
                    $('#optional_options_wrapper').slideUp(200, function() {
                        $(this).remove();
                        add_color()
                        load_options_tab(true);
                        $(".vertical_scroll_chris").animate({ scrollTop: $("#e_exterior").offset().top + -130  }, 800);
                    })
                }

            });


            $('#skip_all_optional_options').click(function() {
                var x = addVanObj.optional_options.length - 1;
                $.each(addVanObj.optional_options, function() {
                    addVanObj.optional_options.splice(x, 1);
                    x = x - 1;
                });
                add_color();
                load_options_tab( true );
                $('#optional_options_wrapper').slideUp(1200, function() {
                    $(".vertical_scroll_chris").animate({ scrollTop: $("#e_exterior").offset().top + -130  }, 800);
                });
            });
            $('#add_all_optional_options').click(function() {

                add_color();
                load_options_tab( true );
                $('#optional_options_wrapper').slideUp(1200, function() {
                    $(".vertical_scroll_chris").animate({ scrollTop: $("#e_exterior").offset().top + -130  }, 800);
                });
            });








        }


    }







    function build_conversion_selector() {
        var writer = '<div class="inner-pad-vans">' +
            '<h2 id="choose_a_conversion" style="margin-bottom: 40px;">Choose A Conversion Brand</h2>';
        var index = 0;
        var c1 = 0;
        var c2 = 0;
        $.each(conversion_vars.conversions, function() {
            var brandlogo = this.logo;
            var brandname = this.name;
            writer+= '' +
                '<div class="col-xs-6 col-sm-3 col-md-2" style="padding-left:0px; padding-right: 0px;" data-index="'+index+'"><div class="conv_list conv_brand_btn text-center">' +
                '<img src="https://www.blvd.com/uploads/'+this.logo+'" class="img-responsive">' +
                '<div class="data">' +
                '<div class="list-title">'+this.name+'</div>' +

                '</div></div></div>';
            index = index + 1;


            writer+= '' +
                '<div style="display: none;"><div class="row">' +
                '<div class="col-xs-12">' +
                '<img style="max-height: 190px;" src="https://www.blvd.com/uploads/'+this.logo+'" class="img-responsive pull-right">' +
                '<h2 class="list-title"><i class="fa fa-arrow-circle-o-left conv_back"></i><i class="fa fa-arrow-circle-o-left remove_icon_btn back_to_conversion" style="display: none;"></i> '+this.name+'</h2>' +
                '<h3 class="conversion_brand"></h3>' +
                '</div></div>';

            c2 = 0;
            $.each(this, function() {
                if(this.name) {




                    writer+= '' +
                        '<div class="col-xs-6 col-sm-3 col-md-4 select_conversion" data-tube="'+this.youtubeid+'" data-id="'+this.id+'" data-brandindex="'+c1+'" data-conversionindex="'+c2+'"   data-index="'+index+'" data-brandlogo="'+brandlogo+'">' +
                        '<div class="conv_list max_conv_h">' +
                        '<div class="row">' +
                        '<div class="col-xs-4">' +
                        '<img src="https://www.blvd.com/uploads/'+this.logo+'" class="icon img-responsive">' +
                        '</div><div class="col-xs-8">' +
                        '<div class="text-center ">'+this.name+'</div>' +
                        '</div></div></div></div>' +
                        '<div class="conversion_description" data-name="'+this.name+'" style="display: none; margin-top: 30px;">' +
                        '<button class="btn conv_not_selected_btn activate_conversion" data-youtubeid="'+this.youtubeid+'" data-id="'+this.id+'" data-brand="'+brandname+'" data-model="'+this.name+'"><i class="fa fa-chevron-right"></i> Use This Conversion</button>' +
                        '<button class="btn conv_selected_btn deactivate_conversion"  style="display: none;"><i class="fa fa-check-circle-o"></i> Conversion Selected</button>' +
                        '<div class="row">' +
                        '<div class="col-sm-8">' +
                        '<div class="borderwrapper"><h2>Standard Description</h2>'+this.description+'</div>' +
                        '</div>' +
                        '<div class="col-sm-4">' +
                        '<div class="borderwrapper"><div id="video_frame" style="text-align: left;">' +
                        '<h2>Videos</h2><div class="vidd"></div></div></div>' +
                        '<div class="borderwrapper"><h2>Images</h2><img src="https://www.blvd.com/uploads/'+this.logo+'" class="img-responsive"></div>' +
                        '<div class="borderwrapper"><h2>Documents</h2>Brochures, Specs and Feature Documents Coming Soon!</div>' +
                        '' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    c2 = c2 +1;
                }
                c1 = c1 + 1;
            });
            writer+= '</div>';

        });
        writer+= '</div>';
        $('#dynamic_conversion_selector').html(writer);

        if(addoredit == 'edit') {
            $('#conversion_description').val(editObj.conversion_description);

            loadonlymode = false;
            //console.log('Loadonlymode: ' + loadonlymode);
        }


    }

    $('body').on('click', '.activate_conversion', function() {
        $('.conv_not_selected_btn').slideUp(200);
        $('.conv_not_selected_btn').next().slideDown(200);

        var brand = $(this).attr('data-brand');
        var model = $(this).attr('data-model');
        var convid = $(this).attr('data-id');
        $('#_conversion').html( brand + ' ' + model);
        checkWarnEmpty();

        addVanObj.conversionid = $(this).attr('data-id');
        addVanObj.conversion = $(this).attr('data-brand') + ' ' + $(this).attr('data-model');
        update_meta(vehicleid, 'conversion', addVanObj.conversion, 'Conversion', '0' );
        update_meta(vehicleid, 'conversionid', convid, 'Conversion ID', '0' );
        $('#youtube_id').val($(this).attr('data-youtubeid'));
        $('#update_video').trigger('click');


        //  show the new conversion you just selected
        $('#selected_conversion').show();
        $('#dynamic_conversion_selector').hide();
        $.ajax({
            url: 'https://www.blvd.com/api/conversions/index.php',
            data: {
                fmt: 'json',
                conversionid: convid
            },
            type: 'GET',
            dataType: "json",
            success: function(data){

                $('#selected_conversion_name').html('<h2>'+data[0].modelheading+'</h2>');
                $('#selected_conversion_description').html(data[0].description);

            }
        });

    });

    $('body').on('click', '.deactivate_conversion', function() {
        $('.conv_not_selected_btn').slideDown(200);
        $('.conv_not_selected_btn').next().slideUp(200);
        addVanObj.conversionid = '';


    });


    $('body').on('click', '.select_conversion', function() {
        $('.select_conversion').slideUp(100);
        $(this).next().slideDown(300);
        $('.conversion_brand').html($(this).next().attr('data-name'));
        $('.back_to_conversion').show();
        $('.conv_back').hide();
        $('.conversion_brand').html($(this).attr('data-model')).show();

        if(addVanObj.conversionid == $(this).attr('data-id')) {
            $('.conv_not_selected_btn').slideUp(200);
            $('.conv_not_selected_btn').next().slideDown(200);
        } else {
            $('.conv_not_selected_btn').slideDown(200);
            $('.conv_not_selected_btn').next().slideUp(200);
        }


        if($(this).attr('data-tube') != '') {
            vid = '<div class="videoWrapper">' +
                '<iframe src="//www.youtube.com/embed/'+$(this).attr('data-tube')+'?rel=0" allowfullscreen="" height="315" width="100%" frameborder="0">' +
                '</iframe></div>';
        } else {
            vid = '<div class="novideo">Video Not Available At This Time.</div>';
        }
        $(this).next().find('.vidd').html(vid);



    });



    $('body').on('click', '.back_to_conversion', function() {
        $('.select_conversion').slideDown(100);
        $('.conversion_description').slideUp(300);
        $('.back_to_conversion').hide();
        $('.conv_back').show();
        $('.conversion_brand').html('').hide();


    });

    $('body').on('click', '.conv_brand_btn', function() {
        $('#choose_a_conversion').hide();
        $('.conv_brand_btn').hide();
        $(this).parent().next().show();
        max_conv_height();
        $('.back_to_conversion').hide();
        $('.conv_back').show();
    });

    $('body').on('click', '.conv_back', function() {
        $('#choose_a_conversion').show();
        $('.conv_brand_btn').show();
        $(this).parent().parent().parent().parent().hide();
    });



    $('body').on('click', '#update_video', function() {
        var vvvv = $('#youtube_id').val();
        var le = vvvv.length;
        var leless = le - 11;
        var vidid = jQuery.trim(vvvv).substring(leless, le).trim(this);

        $('#youtube_id').val(vidid);
        if(vidid) {
            var u = '"//www.youtube.com/embed/'+vidid+'?rel=0"';
            var wr = '<div class="videoWrapper"><iframe width="100%" height="315" src=';
            wr += u;
            wr += 'frameborder="0" allowfullscreen></iframe></div>';
            $('#video_framev').html(wr);
            addVanObj.youtube = vidid;
        } else {
            $('#video_framev').html('No Video Detected.  Please add a Video ID, then press Preview.');
            addVanObj.youtube = '';
        }
        update_meta(vehicleid, 'youtubeid', addVanObj.youtube, 'youtube', '0' )
    });

    $('body').on('click', '.top-tabs-van-edit', function() {
        $('.top-tabs-van-edit').removeClass('selected');
        $(this).addClass('selected');
        var showtabid = $(this).attr('data-showtabid');
        go_to_tab('#'+showtabid);

    });




    function max_conv_height() {
        var maxHeight = 0;
        $(".max_conv_h").each(function(){
            if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
        });
        $(".max_conv_h").height(maxHeight);
    }

    function fetch_van_images() {
        enter_saving()
        $('#sortable_images').load('../ajax/images.php?getimages=true&vehicleid='+vehicleid, function() {
            $(function() {
                exit_saving();


                if($('.thethumbnails').length != 0) {
                    $('#main_van_image').attr('src',$('.thethumbnails[data-imgnumber="1"]').attr('src'));
                }
                var sortimg = $( "#sortable_images" );
                sortimg.sortable({
                    placeholder: "img_wrap img_wrap_highlighter",
                    cancel: '',
                    connectWith: "#sortable_images",
                    sort: function( event, ui ) {
                    },
                    stop: function( event, ui ) {
                        //cat_button_enabled = true
                    },
                    update: function (event, ui) {
                        var result = $(this).sortable('toArray').toString();
                        //alert(result);
                        // POST to server using $.post or $.ajax

                        $.ajax({
                            data: {'newids' : result},
                            type: 'POST',
                            success: function() {

                                fetch_van_images()
                            },
                            url: '../ajax/images.php'
                        });

                    }
                });
            });
        });


        $('#vehicleImageLine').load('../ajax/images.php?getimagesOnly=true&vehicleid='+vehicleid, function() {

            if($('#vehicleImageLine').children().length == 0) {
                $('#vehicleImageLine').html('<div class="noVanImagesWrapper">You have no images loaded for this van, which makes it 250% less likely to' +
                    'generate a lead or call!<br><a href="javascript: void(0);" class="editTabJump" data-target="images_tab">Upload An Image</div>');
            }
        });


    }


    var crop_data = '';
    var currentCropNumber = 0;
    function fetch_crops() {
        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                fetch_crops : 'true',
                vehicleid: vehicleid
            },
            type: 'GET',
            dataType: "json",
            success: function(data){
                crop_data = data;
                currentCropNumber = 0;
                loop_crop();
            }
        });
    }



    function loop_crop() {
        if(crop_data.length == 0) {
            $('.cropping-main-wrapper').hide();
        } else {
            $('.cropping-main-wrapper').show();
            imageurltocrop = '../../../Express2.0/imageup/'+crop_data[currentCropNumber];
            build_cropping_tool();
        }
        $.each(crop_data, function() {

        })
    }


    $('body').on('click', '.options_nav_btn', function() {
        $('.options_nav_btn').removeClass('nav_tab_active');
        $(this).addClass('nav_tab_active');
        var target = $(this).attr('data-target');
        $('.options_tabs').hide();
        $('#'+target).show();
    });


    $('body').on('click', '.remove_standard_option', function() {
        var rr = $(this).attr('data-index');
        delete addVanObj.standard_options.splice(rr, 1);
        $(this).parent().slideUp(200);
        load_options_tab(false)


        ppp = JSON.stringify(addVanObj.standard_options);
        update_vehiclelookup(vehicleid, 'standard_options', ppp);

    });


    $('body').on('click', '.remove_optional_option', function() {
        var rr = $(this).attr('data-index');
        delete addVanObj.optional_options.splice(rr, 1);
        $(this).parent().slideUp(200);
        load_options_tab(false)

        ppp = JSON.stringify(addVanObj.optional_options);
        update_vehiclelookup(vehicleid, 'optional_options', ppp);


    });

    $('body').on('click', '.expand_specs', function() {

        $(this).parent().next().slideToggle(200);
        $(this).toggleClass('fa-compress add_icon_btn');

        $(this).toggleClass('fa-expand remove_icon_btn');
    });




    function load_options_tab(firstrun) {

        $('.options_nav_bar').show();



        var wr = '<h2>Optional Options</h2><p>NOTICE: Options in this section may or MAY NOT be on this vehicle.  If you are not sure, you should' +
            'removed them to be safe.</p><hr /><div class="row">';
        var x =0;
        $.each(addVanObj.optional_options, function() {


            wr += '<div class="col-xs-12 col-sm-6 col-md-4"><div class="options_inner">' +
                '<i class="fa fa-minus-circle pull-right remove_optional_option remove_icon_btn" data-index="'+x+'"></i>' +
                '<div class="txt">'+this.name+'</div></div></div>';
            x = x + 1;
        });

        if(addVanObj.optional_options.length == 0) {
            wr += '<div class="col-xs-12"><div class="options_inner">' +
                '<div class="txt">There is no optional equipment for this vehicle.</div></div></div>';
        }

        wr += '</div>';
        $('#_optional_list').html(wr);






        var wr = '<h2>Standard Options</h2><p>Options in this section are listed as standard on this model.</p><hr /><div class="row">';
        var x =0;
        $.each(addVanObj.standard_options, function() {
            wr += '<div class="col-xs-12 col-sm-6 col-md-4"><div class="options_inner">' +
                '<i class="fa fa-minus-circle pull-right remove_standard_option remove_icon_btn" data-index="'+x+'"></i>' +
                '<div class="txt">'+this.name+'</div></div></div>';
            x = x + 1;
        });
        if(addVanObj.standard_options.length == 0) {
            wr += '<div class="col-xs-12"><div class="options_inner">' +
                '<div class="txt">There are no standard options for this vehicle.</div></div></div>';
        }
        wr += '</div>';
        $('#_standard_list').html(wr);




        if(firstrun == true) {



            var wr = '<h2>Specifications</h2><div class="row"><hr />';
            var x =0;


            var t1 = '<div style="margin: 5px 15px 2px 15px; background-color: #f5f5f5; padding: 5px; font-weight: bold;">' +
                '<i class="fa fa-compress add_icon_btn expand_specs"></i> ';
            var t2 = '<div class="row" style="margin: 0 15px 0 15px; padding: 4px 0 4px 0; border-bottom: solid 1px #eee;">' +
                '<div class="col-xs-4" style="border-right: solid 1px #eee; border-left: solid 1px #eee;">' +
                '<div class="txt">';
            var t3 = '</div>' +
                '</div>' +
                '<div class="col-xs-8" style="border-right: solid 1px #eee;">' +
                '<div class="txt">';


            if(addVanObj.specs.ac) {
                var which = addVanObj.specs.ac;

                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);
            }

            if(addVanObj.specs.brakes) {
                var which = addVanObj.specs.brakes;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.child_saftey) {

                var which = addVanObj.specs.child_saftey;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.crash) {

                var which = addVanObj.specs.crash;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);
            }



            if(addVanObj.specs.exterior_lights) {

                var which = addVanObj.specs.exterior_lights;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }



            if(addVanObj.specs.exterior_dimensions) {



                var which = addVanObj.specs.exterior_dimensions;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.front_seats) {


                var which = addVanObj.specs.front_seats;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.guages) {

                var which = addVanObj.specs.guages;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.interior_trim) {

                var which = addVanObj.specs.interior_trim;

                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);


            }

            if(addVanObj.specs.mirrors) {

                var which = addVanObj.specs.mirrors;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.outlets) {


                var which = addVanObj.specs.outlets;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.security) {


                var which = addVanObj.specs.security;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.shocks) {


                var which = addVanObj.specs.shocks;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.specifications) {

                var which = addVanObj.specs.specifications;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);

            }

            if(addVanObj.specs.steering_wheel) {

                var which = addVanObj.specs.steering_wheel;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);
            }

            if(addVanObj.specs.windows) {


                var which = addVanObj.specs.windows;
                wr += t1+which.name+'</div><div>';
                $.each(which, function() {
                    wr += t2+this.name+t3+this.value+'</div>' +
                        '</div>' +
                        '</div>';
                    x = x + 1;
                });
                wr += '</div>';
                $('#_specs_list').html(wr);
            }


        } // end if first run ad specs


    }

    $('body').on('click', '#save_pricing_btn', function() {
        save_pricing();
        $('#save_pricing_btn').hide();
        $('#price_not_saved_alert').hide();
        $('#price_save_in_progress').show();


    });


    function save_pricing() {



        $.ajax({
            url: 'ajax/listvans.php',
            data: {
                update_vehicle_pricing: 'true',
                vehicleid : vehicleid,
                price_chassis_public : strip_all_but_numbers(Number(addVanObj.pchassis)),
                price_conversion_public : strip_all_but_numbers(Number(addVanObj.pconv)),
                price_total_public : strip_all_but_numbers(Number(addVanObj.ptotal)),
                price_total_rebates : strip_all_but_numbers(Number(addVanObj.totalrebates)),
                price_chassis_admin : strip_all_but_numbers(Number(addVanObj.a_pchassis)),
                price_conversion_admin : strip_all_but_numbers(Number(addVanObj.a_pconv)),
                price_total_admin : strip_all_but_numbers(Number(addVanObj.atotal)),
                conversion_status_public : addVanObj.checkbox_status,
                expenses : JSON.stringify(editObj.expenses),
                discounts : JSON.stringify(editObj.discounts)
            },
            type: 'POST',
            success: function(data){
                console.log('saved pricing return'+data)
                save_rebates();

            }
        });

        //update_meta(vehicleid, 'price_chassis_public', strip_all_but_numbers(Number(addVanObj.pchassis)), 'Price', '0' );
        //update_meta(vehicleid, 'price_conversion_public', strip_all_but_numbers(Number(addVanObj.pconv)), 'Price', '0' );
        //update_meta(vehicleid, 'price_total_public', strip_all_but_numbers(Number(addVanObj.ptotal)), 'Price', '0' );
        //update_meta(vehicleid, 'price_total_rebates', strip_all_but_numbers(Number(addVanObj.totalrebates)), 'Price', '0' );
        //update_meta(vehicleid, 'price_chassis_admin', strip_all_but_numbers(Number(addVanObj.a_pchassis)), 'Price', '0' );
        //update_meta(vehicleid, 'price_conversion_admin', strip_all_but_numbers(Number(addVanObj.a_pconv)), 'Price', '0' );
        //update_meta(vehicleid, 'price_total_admin', strip_all_but_numbers(Number(addVanObj.atotal)), 'Price', '0' );


    }



    function strip_all_but_numbers(numberse) {
        // alert (numberse)
        var mn = numberse.toString();
        var nnumr = mn.replace(/[^\/\d]/g,'');
        var nnn = Number(nnumr);
        return nnn;
    }

    function logData() {
        console.log('addVanObj')
        console.log(addVanObj)
        console.log('editObj')
        console.log(editObj)
    }



    $('body').on('keyup', '.chassis_price_class', function() {
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();

        //save_pricing();
    });
    $('body').on('keyup', '.conversion_price_class', function() {
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();
        //save_pricing();
    });
    $('body').on('change', '.conversion_price_check_class', function() {
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();
        //save_pricing();
    });
    $('body').on('keyup', '.a_chassis_price_class', function() {
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();
        //save_pricing();
    });

    $('body').on('keyup', '.a_conversion_price_class', function() {
        addup();
        $('#save_pricing_btn').show();
        $('#price_not_saved_alert').show();
        //save_pricing();
    });








    $('body').on('keyup', '.a_expense_price_class', function() {
        var i = $(this).attr('data-index');
        var v = strip_all_but_numbers($(this).val());
        var n = $('.a_expense_name_class[data-index="'+i+'"]').val();
        editObj.expenses[i].amount = v;


            addup();
            $('.a_expense_price_class[data-index="'+i+'"]').focus();
            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();

    });

    $('body').on('keyup', '.a_expense_name_class', function() {
        var i = $(this).attr('data-index');
        var v = $(this).val();
        editObj.expenses[i].name = v;
            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();
    });


    $('body').on('click', '.remove_selected_expense', function() {
        var i = $(this).attr('data-index');
        editObj.expenses.splice(i, 1);
        var container = $(this).parent().parent().parent().parent();
        container.css('background-color', 'red');

        container.slideUp( 200, function() {
            buildCurrentExpenses();
        });

            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();
    });


    $('body').on('click', '.add_expense_btn', function() {
        console.log(' we ran we ran')
        var c = editObj.expenses.length;
        console.log(c);
        editObj.expenses.push({
            amount: 0,
            name: ''
        });
        $('.no-expense-applied-notice').remove();

        var t = '<div class="col-sm-12 new_van_layout just-added-expense">' +
            '<div class="row crm-form">' +
            '<div class="col-sm-4 container_no_label">' +
            '<div style="position: absolute; width: 15px;" class="thevalue">$</div>' +
            '<input id="expense-amount" class="a_expense_price_class" data-index="'+c+'"  style="margin-left: 15px;"  placeholder="Amount" type="text">' +
            '</div>' +
            '<div class="col-sm-7 container_no_label">' +
            '<input id="expense-name" class="a_expense_name_class"  data-index="'+c+'"  placeholder="Description" type="text">' +
            '</div>' +
            '<div class="col-sm-1 container_no_label">' +
            '<div class="thevalue text-center">' +
            '<i data-index="'+c+'" class="fa fa-minus-circle remove_selected_expense remove_icon_btn"></i>' +
            '</div></div>' +
            '</div></div>';
        $('#expenses-container').append(t);

        setTimeout(function() {
            $('.just-added-expense').addClass('glow-expense');
        },100);
        setTimeout(function() {
            $('.just-added-expense').removeClass('glow-expense');
            $('.just-added-expense').removeClass('just-added-expense');
        },4200);


    });

    function buildCurrentExpenses() {
        var t = '';
        var c = 0;

        if(!editObj.expenses || editObj.expenses.length > 0) {
        $.each(editObj.expenses, function() {

            t += '<div class="col-sm-12 new_van_layout">' +
                '<div class="row crm-form">' +
                '<div class="col-sm-4 container_no_label">' +
                '<div style="position: absolute; width: 15px;" class="thevalue">$</div>' +
                '<input id="expense-amount" class="a_expense_price_class" data-index="'+c+'" value="'+thousands(this.amount)+'" style="margin-left: 15px;"  placeholder="Amount" type="text">' +
                '</div>' +
                '<div class="col-sm-7 container_no_label">' +
                '<input id="expense-name" class="a_expense_name_class"  data-index="'+c+'" value="'+this.name+'"   placeholder="Description" type="text">' +
                '</div>' +
                '<div class="col-sm-1 container_no_label">' +
                '<div class="thevalue text-center">' +
                '<i data-index="'+c+'" class="fa fa-minus-circle remove_selected_expense remove_icon_btn"></i>' +
                '</div></div>' +
                '</div></div>';
            c++;
        });
        } else {
            t += '<div class="col-sm-12 new_van_layout no-expense-applied-notice">' +
                '<div class="row crm-form">' +
                '<div class="col-sm-12 container_no_label">' +
                'No expenses applied yet.' +
                '</div>' +
                '</div></div>';
        }

        $('#expenses-container').html(t);
        addup();
    }












    // Single Discounts

    $('body').on('keyup', '.p_discounts_price_class', function() {
        var i = $(this).attr('data-index');
        var v = strip_all_but_numbers($(this).val());
        var n = $('.p_discounts_name_class[data-index="'+i+'"]').val();
        editObj.discounts[i].amount = v;


            addup();
            $('.p_discounts_price_class[data-index="'+i+'"]').focus();
            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();

    });

    $('body').on('keyup', '.p_discounts_name_class', function() {
        var i = $(this).attr('data-index');
        var v = $(this).val();
        editObj.discounts[i].name = v;
            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();
    });


    $('body').on('click', '.remove_selected_discount', function() {
        var i = $(this).attr('data-index');
        editObj.discounts.splice(i, 1);
        var container = $(this).parent().parent().parent().parent();
        container.css('background-color', 'red');

        container.slideUp( 200, function() {
            buildCurrentDiscounts();
        });

            $('#save_pricing_btn').show();
            $('#price_not_saved_alert').show();
    });


    $('body').on('click', '.add_discount_btn', function() {
        var c = editObj.discounts.length;
        editObj.discounts.push({
            amount: 0,
            name: ''
        });
        $('.no-discounts-applied-notice').remove();

        var t = '<div class="col-sm-12 new_van_layout just-added-discount">' +
            '<div class="row crm-form">' +
            '<div class="col-sm-4 container_no_label">' +
            '<div style="position: absolute; width: 15px; margin-left: -5px;" class="thevalue">-$</div>' +
            '<input id="discount-amount" class="p_discounts_price_class" data-index="'+c+'"  style="margin-left: 15px;"  placeholder="Amount" type="text">' +
            '</div>' +
            '<div class="col-sm-7 container_no_label">' +
            '<input id="discount-name" class="p_discounts_name_class"  data-index="'+c+'"  placeholder="Description" type="text">' +
            '</div>' +
            '<div class="col-sm-1 container_no_label">' +
            '<div class="thevalue text-center">' +
            '<i data-index="'+c+'" class="fa fa-minus-circle remove_selected_discount remove_icon_btn"></i>' +
            '</div></div>' +
            '</div></div>';
        $('#discounts-container').append(t);

        setTimeout(function() {
            $('.just-added-discount').addClass('glow-discount');
        },100);
        setTimeout(function() {
            $('.just-added-discount').removeClass('glow-discount');
            $('.just-added-discount').removeClass('just-added-discount');
        },4200);


    });

    function buildCurrentDiscounts() {
        var t = '';
        var c = 0;
        if(!editObj.discounts || editObj.discounts.length > 0) {

            $.each(editObj.discounts, function () {
                t += '<div class="col-sm-12 new_van_layout">' +
                    '<div class="row crm-form">' +
                    '<div class="col-sm-4 container_no_label">' +
                    '<div style="position: absolute; width: 15px; margin-left: -5px;" class="thevalue">-$</div>' +
                    '<input id="discount-amount" class="p_discounts_price_class" data-index="' + c + '" value="' + thousands(this.amount) + '" style="margin-left: 15px;"  placeholder="Amount" type="text">' +
                    '</div>' +
                    '<div class="col-sm-7 container_no_label">' +
                    '<input id="discount-name" class="p_discounts_name_class"  data-index="' + c + '" value="' + this.name + '"   placeholder="Description" type="text">' +
                    '</div>' +
                    '<div class="col-sm-1 container_no_label">' +
                    '<div class="thevalue text-center">' +
                    '<i data-index="' + c + '" class="fa fa-minus-circle remove_selected_discount remove_icon_btn"></i>' +
                    '</div></div>' +
                    '</div></div>';
                c++;
            });
        } else {
            t += '<div class="col-sm-12 new_van_layout no-discounts-applied-notice">' +
                '<div class="row crm-form">' +
                '<div class="col-sm-12 container_no_label">' +
                'No discounts applied yet.' +
                '</div>' +
                '</div></div>';
        }









        $('#discounts-container').html(t);
        addup();
    }

















    function addup() {
        var pchassis = Number(strip_all_but_numbers($('.chassis_price_class').val()));
        var pconv = Number(strip_all_but_numbers($('.conversion_price_class').val()));
        var a_pchassis = Number(strip_all_but_numbers($('.a_chassis_price_class').val()));
        var a_pconv = Number(strip_all_but_numbers($('.a_conversion_price_class').val()));

        var ischecked= $(".conversion_price_check_class").is(':checked');
        addVanObj.checkbox_status = ischecked;

        $('.chassis_price_class').val(thousands(pchassis));
        $('.conversion_price_class').val(thousands(pconv));
        $('.a_chassis_price_class').val(thousands(a_pchassis));
        $('.a_conversion_price_class').val(thousands(a_pconv));

        var totalExpenses = 0;
        $.each($('.a_expense_price_class'), function() {
            var v = strip_all_but_numbers($(this).val());
            totalExpenses = Number(totalExpenses) + Number(strip_all_but_numbers(v));
            $(this).val(thousands(v));
        });

        var totalDiscounts = 0;
        $.each($('.p_discounts_price_class'), function() {
            var v = strip_all_but_numbers($(this).val());
            totalDiscounts = Number(totalDiscounts) + Number(strip_all_but_numbers(v));
            $(this).val(thousands(v));
        });
      /*  var pconvcheck = "";
        $('body').on('click','.conversion_price_check_class',function(){
             var ischecked= $(this).is(':checked');
            addVanObj.pconvcheck = ischecked;
           save_pricing();
        });*/







        //console.log(pchassis + ' - ' + pconv + ' - ' + a_pchassis + ' - ' + a_pconv + ' - ' );

        if(pchassis == '') pchassis = 0;
        addVanObj.pchassis = pchassis;

        if(pconv == '') pconv = 0;
        addVanObj.pconv = pconv;



/*
        if(tsr == '') tsr = 0;
        addVanObj.totalrebates = tsr;*/

        if(a_pchassis == '') a_pchassis = 0;
        addVanObj.a_pchassis = a_pchassis;

        if(a_pconv == '') a_pconv = 0;
        addVanObj.a_pconv = a_pconv;

        var total_public = 0;

/*

        var tsr = 0;
        var rebatesqty = 0;

        $.each($('.each_rebate'), function() {
            var a = $(this).attr('data-amount');
            tsr = Number(tsr) + Number(a);
            rebatesqty = rebatesqty + 1;
        });

        if(rebatesqty == 0) { tsr = 0; }
        $('#total_s_rebates').html('$'+thousands(tsr));

*/




        if(total_public < 1) { total_public = ''; }
        if(total_admin < 1) { total_admin = ''; }
        //if(tsr < 1) { tsr = ''; }

        //var total_public = (pchassis + pconv) - tsr;
        var total_public = (pchassis + pconv) - totalDiscounts;
        var total_public_baseprice = (pchassis + pconv);
        var total_admin_baseprice = (a_pchassis + a_pconv);
        var total_admin = (a_pchassis + a_pconv) + totalExpenses;

        if(total_public == '') { total_public = 0; }
        addVanObj.ptotal = total_public;

        if(tsr == '') { tsr = 0; }
        addVanObj.totalrebates = tsr;

        if(total_admin == '') total_admin = 0;
        addVanObj.atotal = total_admin;



        $('#total_price_public').html(thousands(total_public));
        $('#total_baseprice_public').html(thousands(total_public_baseprice));
        $('#total_baseprice_admin').html(thousands(total_admin_baseprice));
        $('#total_price_admin').html(thousands(total_admin));
        //$('.rebates_field').html('-'+thousands(tsr));
        if(total_public==0 || total_public=='') {
            $('#sell_for_price').html('Call For Price');
        } else {
            $('#sell_for_price').html('$'+thousands(total_public));
        }




        if(totalExpenses > 0) {
            var t = '<div id="totalExpensesLine" class="col-sm-12 new_van_layout priceTotalLine beforeAddons">' +
                '<div class="row crm-form rebate">' +
                '<div class="col-sm-12 container_no_label">' +
                '<div class="totalAmount">' +
                '<div class="thevalue">'+thousands(totalExpenses)+'</div>' +
                '</div>' +
                '<div class="thevalue totalTitle">Total Expenses</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('#totalExpensesLine').remove();
            $('#expenses-container').append(t);
        } else {
            $('#totalExpensesLine').remove();
        }


        if(totalDiscounts > 0) {
            var t = '<div id="totalDiscountsLine" class="col-sm-12 new_van_layout priceTotalLine beforeAddons">' +
                '<div class="row crm-form rebate">' +
                '<div class="col-sm-12 container_no_label">' +
                '<div class="totalAmount">' +
                '<div class="thevalue">'+thousands(totalDiscounts)+'</div>' +
                '</div>' +
                '<div class="thevalue totalTitle">Total Discounts</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            $('#totalDiscountsLine').remove();
            $('#discounts-container').append(t);
        } else {
            $('#totalDiscountsLine').remove();
        }


    }

</script>


<div class="top-tabs-van-edit-wrapper">
    <div class="top-tabs-van-edit general_tab selected" data-showtabid="general_tab">
        General
    </div>
    <div class="top-tabs-van-edit images_tab"  data-showtabid="images_tab">
        Images
    </div>
    <div class="top-tabs-van-edit conversion_tab" data-showtabid="conversion_tab">
        Conv.
    </div>
    <div class="top-tabs-van-edit pricing_tab" data-showtabid="pricing_tab">
        Pricing
    </div>
    <div class="top-tabs-van-edit options_tab" data-showtabid="options_tab">
        Options
    </div>
    <div class="top-tabs-van-edit video_tab" data-showtabid="video_tab">
        Video
    </div>
    <div class="top-tabs-van-edit docs_tab" data-showtabid="docs_tab">
        Docs
    </div>
    <div class="top-tabs-van-edit admin_tab" data-showtabid="admin_tab">
        Admin
    </div>
</div>


<div style="clear: left"></div>
<div class="vin_start content-wrapper full_height">
    <div class="vertical_scroll_chris">

        <div id="general_tab" class="tabs">
            <div id="van_details" class="van_section"></div>
            <div id="exterior_colors_div" class="van_section"></div>
            <div id="interior_colors_div" class="van_section"></div>
            <? include 'marksoldform.php'; ?>
        </div>

        <div id="images_tab" class="tabs" style="display: none;"><? include 'tab_images.php'; ?></div>

        <div id="conversion_tab" class="tabs">

            <div id="selected_conversion">
                <div class="inner-pad-vans">

                    <div class="row">
                        <div class="col-lg-8">
                            <div id="selected_conversion_name"></div>

                        </div>
                        <div class="col-lg-4">
                            <button id="change_selected_conversion" class="form-buttons shiny-buttons red-btn" style="margin-top: 5px; z-index: 1;">
                                <div class="shine"></div>
                                <div class="shine-btn-text"><i class="fa fa-pencil"></i> Change Conversion</div>
                            </button>
                        </div>

                        <div class="col-lg-12">
                            <div id="selected_conversion_description"></div>
                        </div>
                        <div class="col-xs-12">
                            <div class="note-about-conversions">
                                <i class="fa fa-exclamation-triangle"></i>
                                If you would like to add additional details about the conversion, you should put the details in the "Description" field on the <a href="javascript: $('.general_tab').trigger('click'); void(0);">General tab</a>.  Add details such as hand controls, power tiedowns or modifications to the conversion performed after the original sale.  We do it this way to accomodate third party feeds as they do not accept multiple conversion descriptions.
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="dynamic_conversion_selector"></div>
        </div>

        <div id="pricing_tab" class="tabs">

            <div class="van_section rebates_price_screen">
                <div id="price_not_saved_alert"><i class="fa fa-exclamation-triangle"></i> Don't forget to save changes to pricing using the "Save Pricing" button on the right.</div>
                <button id="save_pricing_btn" class="btn">Save Pricing <i class="fa fa-cloud-upload"></i></button>
                <div id="price_save_in_progress"><i class="fa fa-spinner fa-spin"></i> PLEASE HOLD...  Price Save In Progress.  Thank You.</div>





                <div class="retailPricingWrapper">
                    <h2 class="pricingH2 retailH2">Retail Pricing</h2>
                    <div class="add-top-line"></div>
                    <div class="row">
                        <div class="col-sm-12 new_van_layout">
                            <div class="row">
                                <div class="col-sm-4 container_no_label text-left">
                                    <input type="checkbox" id="show_price_public" data-field="show_price_public"  class="auto_update_meta on-off-checkbox">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Show Public Pricing Online? (Checked For Yes)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 new_van_layout">
                            <div class="row crm-form">
                                <div class="col-sm-4 container_no_label">
                                    <div style="position: absolute; width: 15px;" class="thevalue">$</div>
                                    <input id="chassis_price" class="chassis_price_class priceField" placeholder="Chassis Base Price" type="text">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Chassis Base Price</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 new_van_layout">
                            <div class="row crm-form">
                                <div class="col-sm-4 container_no_label">
                                    <div style="position: absolute; width: 15px;" class="thevalue">$</div>
                                    <input id="conversion_price" class="conversion_price_class priceField"  placeholder="Conversion Base Price" type="text">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Conversion Price</div>
                                </div>
                            </div>
                            <div class="row crm-form ">
                                <div class="col-sm-4 container_no_label">
                                </div>
                            <div class="col-sm-8 container_no_label convcheck_class">
                                <div class="thevalue">
                                    <input name="conv-check" id="convcheck" class="input-control conversion_price_check_class"   type="checkbox" checked="" value="" style="width: 30px; height: 25px; vertical-align: bottom;" >
                                    <span class="label_convcheck">Non-Converted Vehicle: Prices Starting At</span>
                                </div>
                            </div>
                            </div>
                        </div>



                        <div class="col-sm-12 new_van_layout priceTotalLine beforeAddons">
                            <div class="row crm-form rebate">
                                <div class="col-sm-12 container_no_label">
                                    <div class="totalAmount">
                                        <div id="total_baseprice_public" class="thevalue"></div>
                                    </div>
                                    <div class="thevalue totalTitle">Sub Total (Before Discounts)</div>
                                </div>
                            </div>
                        </div>

                        <div class="van_section rebates_price_screen col-xs-12">
                            <button class="blue-btn pull-right add_discount_btn"><i  class="fa fa-plus-circle"></i> Add Discount</button>
                            <h2 class="priceLineSubHeading">Discounts</h2>
                            <div class="add-top-line"></div>
                            <!--<div id="selected_rebates"></div>-->
                        </div>
                        <div id="discounts-container"></div>


                        <!--<div class="col-sm-12 new_van_layout">
                            <div class="row crm-form">
                                <div class="col-sm-4 container_no_label">
                                    <div style="position: absolute; width: 15px;" class="thevalue">$</div>
                                    <div class="thevalue rebates_field" style="padding-left: 16px;"></div></div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Less Current Discounts</div>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-sm-12 new_van_layout priceTotalLine">
                            <div class="row crm-form rebate">
                                <div class="col-sm-12 container_no_label">
                                    <div class="totalAmount">
                                        <div id="total_price_public" class="thevalue"></div>
                                    </div>
                                    <div class="thevalue totalTitle">Total After Discounts:</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>












            <div class="van_section rebates_price_screen">
                <div class="costPricingWrapper">
                    <h2 class="pricingH2 costH2">Our Costs</h2>
                    <div class="add-top-line"></div>
                    <div class="row">
                        <div class="col-sm-12 new_van_layout">
                            <div class="row">
                                <div class="col-sm-4 container_no_label text-left">
                                    <input type="checkbox" id="show_price_admin" data-field="show_price_admin"  class="auto_update_meta on-off-checkbox">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Show To Managers?</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 new_van_layout">
                            <div class="row crm-form">
                                <div class="col-sm-4 container_no_label">
                                    <div style="position: absolute; width: 15px;" class="thevalue">$</div>
                                    <input id="a_chassis_price" class="a_chassis_price_class priceField"  placeholder="Chassis" type="text">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Chassis Base Price</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 new_van_layout">
                            <div class="row crm-form">
                                <div class="col-sm-4 container_no_label">
                                    <div style="position: absolute; width: 15px;" class="thevalue">$</div>
                                    <input id="a_conversion_price" class="a_conversion_price_class priceField"  placeholder="Conversion" type="text">
                                </div>
                                <div class="col-sm-8 container_no_label">
                                    <div class="thevalue">Conversion Price</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 new_van_layout priceTotalLine beforeAddons">
                            <div class="row crm-form rebate">
                                <div class="col-sm-12 container_no_label">
                                    <div class="totalAmount">
                                        <div id="total_baseprice_admin" class="thevalue">--</div>
                                    </div>
                                    <div class="thevalue totalTitle">Sub Total (Before Expenses)</div>
                                </div>
                            </div>
                        </div>

                        <div class="van_section rebates_price_screen col-xs-12">
                            <button class="blue-btn pull-right add_expense_btn"><i  class="fa fa-plus-circle"></i> Add Expense</button>
                            <h2  class="priceLineSubHeading">Expenses</h2>
                            <div class="add-top-line"></div>
                        </div>

                        <div id="expenses-container"></div>



                        <div class="col-sm-12 new_van_layout priceTotalLine costLine">
                            <div class="row crm-form rebate">
                                <div class="col-sm-12 container_no_label">
                                    <div class="totalAmount">
                                        <div id="total_price_admin" class="thevalue" ></div>
                                    </div>
                                    <div class="thevalue totalTitle">Total:</div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div style="height: 60px;"></div>
            </div>







            <div class="van_section" id="add_rebate" style="display: none;">
                <h2 style="margin-bottom: 20px;">
                    <div class="pull-right" style="margin-right: 17px;">
                        <i  class="fa fa-check add_icon_btn exit_add_rebates_screen"></i></div>
                    Add Discounts To Vehicle</h2>
                <h2 style="margin-bottom: 40px; margin-top: -15px;">Total: <span id="total_s_rebates">$0</span></h2>
                <div style="border-left: solid 3px #c2002b; margin-left: 15px;">
                    <h3 style="border-left: solid 5px #c2002b; padding-left: 7px;"> Discounts Specific To This VIN. Collected From Edmunds.com</h3>
                    <div class="add-top-line"></div>
                    <div id="available_edmunds_rebates"></div>
                </div>

                <div style="height: 25px;"></div>

                <div style="border-left: solid 3px #5774ff; margin-left: 15px;">
                    <h3 style="border-left: solid 5px #5774ff; padding-left: 7px;"> Available Global Custom Discounts</h3>
                    <div class="add-top-line"></div>
                    <div id="available_global_rebates"></div>
                </div>


            </div>





        </div>

        <div id="options_tab" class="tabs">
            <div id="list_of_trims" class="van_section"></div>
            <div id="_options_selector" class="van_section"></div>



            <div class="van_section new_van_layout">
                <nav class="navigation-bar white border options_nav_bar" style="margin: 15px; display: none;">
                    <div class="navigation-bar-content">
                        <a href="javascript:void(0)" class="element options_nav_btn nav_tab_active" data-target="_standard_list"><i class="fa fa-list"></i> Standard Options</a>
                        <span class="element-divider"></span>
                        <a href="javascript:void(0)" class="element options_nav_btn " data-target="_optional_list"><i class="fa fa-list"></i> Optional Equipment</a>
                        <span class="element-divider"></span>

                        <a href="javascript:void(0)" class="element options_nav_btn" data-target="_specs_list"><i class="fa fa-list"></i> Specs</a>
                        <span class="element-divider"></span>

                    </div>
                </nav>

                <div id="_standard_list" class="van_section options_tabs"></div>
                <div id="_optional_list" class="van_section options_tabs" style="display: none;"></div>
                <div id="_specs_list" class="van_section options_tabs" style="display: none;"></div>

            </div>
        </div>






        <div id="video_tab" class="tabs">
            <div class="van_section new_van_layout">
                <h2>Vehicle Video</h2>
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-6 col-lg-4">
                        <label class="admin">YouTube Video ID/URL</label>
                        <div class="input-control text" data-role="input-control">
                            <input placeholder="11 Digit YouTube Video ID" id="youtube_id" name="youtube_id" maxlength="50"  type="text">
                            <button type="button" class="btn-clear" tabindex="-1"></button>
                        </div>
                        <a class="btn blue-btn pull-right" id="update_video" href="javascript:void(0)" style="margin-top: 5px;">
                            <div><i class="fa fa-refresh"></i> Update/Preview</div>
                        </a>

                    </div>
                    <div class="col-xs-12 col-sm-5 col-md-6 col-lg-8">
                        <div id="video_framev" style="text-align: center;">
                            No Video Found For This Vehicle.
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <h2 style="margin-top: 60px;">Learn</h2>
                        <div class="well">
                            <h3>Examples:</h3>
                            <p>EQF_OoXnpYQ<br>
                                https://www.youtube.com/watch?v=EQF_OoXnpYQ</p>
                            <h3>About:</h3>
                            <p>We need the 11 digits after the "v=" in the YouTube URL or the entire URL that ends with the VideoID.  It's easy as
                                go to the video on YouTube, then copy and paste the URL into the box above.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>






        <div id="docs_tab" class="tabs">
            <div class="van_section new_van_layout" style="text-align: left">
                <h2>Documents</h2>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div style="font-size: 40px;">Upload New</div>

                        <div style="margin-bottom: 15px; border: solid 1px rgba(0,0,0,.2); padding: 8px; ">
                            <label class="admin">Name/Title of your new Document</label>
                            <div class=" text" data-role="input-control">
                                <input placeholder="Name/Title" id="doc_name" name="doc_name" maxlength="50" style="width: 100%; border: solid 1px rgba(0,0,0,.2);" type="text">
                                <div class="text-left"><input type="checkbox" class="showtocheckbosx on-off-checkbox inline" name="document_show_to" name="document_show_to" value="SuperAdmin"><label class="on-off-checkbox-label">Show To Super Admins</label></div>
                                <div class="text-left"><input type="checkbox" class="showtocheckbosx on-off-checkbox inline" name="document_show_to" name="document_show_to" value="User"><label class="on-off-checkbox-label">Show To Users/Salespeople</label></div>
                                <div class="text-left"><input type="checkbox" class="showtocheckbosx on-off-checkbox inline" name="document_show_to" name="document_show_to" value="Everyone"><label class="on-off-checkbox-label">Show To Public</label></div>
                            </div>
                            <button id="selectdoc" class="red-btn">Select File</button>
                            <button id="uploaddoc" class="blue-btn" >Upload</button>

                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <div style="font-size: 40px;">Current Documents</div>
                        <div id="delete_confirm"></div>
                        <div class="loading_docs" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fa fa-spinner fa-spin"></i> Loading.. One Moment Please
                            </div>
                        </div>
                        <div id="current_docs"></div>


                    </div>


                </div>


                <script type="text/javascript">


                    $('body').on('click', '.docs_tab', function() {
                        get_docs();
                    });


                    $('body').on('click', '.delete_doc', function() {

                        var docid = $(this).attr('data-id');

                        $('#delete_confirm').load('ajax/uploadvandocs.php?deletedoc=true&docid='+docid, function() {
                            get_docs();
                        });
                    });


                    function get_docs() {
                        $('.loading_docs').show();
                        $('#current_docs').html('');
                        $('#current_docs').load('ajax/uploadvandocs.php?getdocs=true&vehicleid='+vehicleid, function() {
                            $('.loading_docs').hide();
                            $(function() {


                            });
                        });
                    }




                    $('body').on('keyup', '#doc_name', function() {
                        uploaded_doc_name = $(this).val();
                    });

                    $('body').on('click', '.showtocheckbosx', function() {

                        checkeddocumenttypes = '';
                        $.each($('.showtocheckbosx'), function() {

                            if($(this).is(':checked')) {
                                checkeddocumenttypes += $(this).val() + '|';
                            }
                        });
                    });


                    var uploaded_doc_name = '';
                    var checkeddocumenttypes = '';

                    var uploaderdocs = new plupload.Uploader({
                        runtimes : 'html5',
                        browse_button : 'selectdoc', // you can pass in id...
                        max_file_count : 1,
                        url : 'ajax/uploadvandocs.php',
                        flash_swf_url : '../plupload/js/Moxie.swf',
                        silverlight_xap_url : '../plupload/js/Moxie.xap',
                        cnumber : 1,

                        filters : {
                            max_file_size : '8mb',
                            mime_types: [
                                {title : "Document files", extensions : "pdf,doc,docx,xls,xlsx"}
                            ]
                        },
                        init: {
                            PostInit: function() {
                                //document.getElementById('filelist').innerHTML = '';

                                document.getElementById('uploaddoc').onclick = function() {
                                    uploaderdocs.start();
                                    return false;
                                };
                            },

                            FilesAdded: function(up, files) {
                                plupload.each(files, function(file) {
                                    //document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                                });
                                //$('#uploaddoc').show();
                                //$('#selectdoc').hide();
                            },

                            UploadProgress: function(up, file) {
                                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                            },
                            UploadComplete: function(up, files) {
                                // Called when all files are either uploaded or failed
                                $('#uploaddoc').show();
                                $('#selectdoc').show();
                                $('#doc_name').val('');
                                get_docs();

                            },

                            Error: function(up, err) {
                                //document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                            }
                        }
                    });

                    uploaderdocs.init();

                    uploaderdocs.bind('BeforeUpload', function(up, file) {

                        up.settings.multipart_params = {
                            "vehicleid": vehicleid,
                            dealerdoc : 'true',
                            humanfilename: uploaded_doc_name,
                            showfor : checkeddocumenttypes

                        };
                        $('#uploaddoc').hide();
                        $('#selectdoc').hide();

                    });






                </script>




            </div>


        </div>

        <div id="admin_tab" class="tabs">
            <div class="van_section new_van_layout">
                <h2>Admin Tools and Notes</h2>
                <div class="row crm-form">
                    <div class="col-sm-12 container">
                        <div class="label">User and Manager Notes (Limit 500 Characters)</div><textarea type="text" id="admin_notes"  data-field="admin_notes" maxlength="499"  class="auto_update_meta" placeholder="Add notes that will appear to users and managers."></textarea>
                    </div>
                </div>
                <div class="row crm-form">
                    <div class="col-sm-12 container">
                        <div class="label">Super Admin Notes (Limit 500 Characters)</div><textarea type="text" id="superadmin_notes"  data-field="superadmin_notes" maxlength="499" class="auto_update_meta" placeholder="Add notes that will only appear to SuperAdmins."></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">

                </div>
            </div>

        </div>


    </div>



</div>

<div id="out_of_bounds" class="out-of-bounds">
    <div id="vin_form" style="display: none">
        <div class="row">
            <div class="col-sm-12">
                <h2>Add A Vehicle</h2>
                <p>To begin, please enter a valid VIN number</p>
                <input type="text" class="vin_add_input"  maxlength="17" placeholder="Type VIN Here">
                <div class="row vin_not_found_alert" style="display: none">
                    <div class="col-xs-12 col-sm-12">
                        <div class="add_van_alert">The VIN you entered was either not found or not correct.  Please verify the VIN above and re-submit or <a href="#" class="add_van_manually_btn" >Add The Vehicle Manually</a> if the VIN is verified to be accurate.</div>
                    </div>
                </div>
                <div class="row add_vin_manually_form" style="display: none;">
                    <div class="col-xs-12 col-sm-4">
                        <label class="add_van_input_label">Year</label>
                        <select class="add_van_input year_input_x" id="year_input">
                            <option data-year="" value="">Select A Year</option>
                            <?
                            $c=40;
                            $ty = (date('Y') + 1);
                            while($c > 0) {
                                echo "<option>$ty</option>";
                                $ty--;
                                $c--;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <label class="add_van_input_label">Make</label>
                        <select class="add_van_input make_input_x" id="make_input"></select>
                    </div>
                    <div class="col-xs-12 col-sm-4">

                        <label class="add_van_input_label">Model</label>
                        <select class="add_van_input model_input_x" id="model_input"></select>
                    </div>
                    <div class="col-xs-12 col-sm-12" style="margin-top: 10px;">
                        <div class="add_van_alert">Still Can't Find The Vehicle?  <a href="javascript: void(0)" id="add_from_scratch">Add From Scratch</a></div>.
                    </div>
                </div>
                <button id="submit_vin_btn" style="display: none"></button>
            </div>

        </div>
    </div>







</div>








