<?php include 'header-map.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/themes/dark.css">
<style type="text/css">
    .panel-body .table th {
    text-align: center;
    vertical-align: top;
}
.table_report { border: none; }
.table_report td { font-weight: normal; }
.booking_records tr td { vertical-align:middle !important; }
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
<section id="middle">
    <header id="page-header">
        <h1><?php echo $title; ?></h1> 
    </header>
    <div id="content" class="padding-20">
        <div id="panel-1" class="panel panel-default">
             
            <div class="panel-body">
                <?php if($this->session->flashdata('err_msg')) { ?>
                    <div class="alert alert-danger noborder text-center weight-400 nomargin noradius">
                        <?php echo $this->session->flashdata('err_msg'); ?>
                    </div>
                <?php } ?>
                <?php if($this->session->flashdata('suc_msg')) { ?>
                    <div class="alert alert-success noborder text-center weight-400 nomargin noradius">
                        <?php echo $this->session->flashdata('suc_msg'); ?>
                    </div>
                <?php }?>  
                <form action="<?php echo base_url('target_report/map'); ?>" class="" method="post" id="addbooking" style="margin-bottom: 0px;">
                    <div class="row" style="display: none;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="rate">View</label> 
                                <input class="view_reoprt" type="radio" id="" name="view_reoprt"  value="makers" <?php echo ((isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='makers') || !isset($_POST['view_reoprt'])) ? 'checked' : ''; ?> />Makers
                                <input class="view_reoprt" type="radio" id="" name="view_reoprt"  value="secondarymakers" <?php echo (isset($_POST['view_reoprt']) && $_POST['view_reoprt']=='secondarymakers') ? 'checked' : ''; ?> /> Secondary Makers
                            </div>
                        </div>  
                    </div>
                    <div class="row" style="margin-bottom: 0px;"> 
                        <div class="col-md-3" >                           
                            <div class="form-group">
                                <label for="name">Employee </label> 
                                <select class="form-control" id="employee" name="employee" disabled>
                                    <?php if($logged_role!=1 || (isset($_POST['view_reoprt']) && $_POST['view_reoprt'] =='secondarymakers')) { ?>
                                    <option value="">Select  Employee</option>
                                    <?php } ?>
                                    <?php if($users) { 
                                        foreach ($users as $key => $value) { ?>
                                        <option value="<?php echo $value['id']; ?>" <?php if(isset($_POST['employee'])) { if($_POST['employee']==$value['id']) { echo "selected"; } } ?>><?php echo $value['name'].' - '.$value['username']; ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="txt-danger"><?php echo form_error('employee'); ?></span>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Year</label> 
                                <select class="form-control" id="year" name="year" disabled>
                                    <option value="">Select Year</option>
                                    <?php   $current_year = date('Y');
                                            $start_year = 2022; 
                                            for ($i=$start_year; $i <=$current_year ; $i++) { ?>
                                               <option value="<?php echo $i; ?>" <?php if((isset($_POST['year']) && $_POST['year']==$i) || (!isset($_POST['year']) && $current_year==$i)) {  echo "selected"; }  ?> ><?php echo $i; ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('year'); ?></span>
                            </div> 
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Month</label> 
                                <select class="form-control" id="month" name="month"disabled >
                                    <option value="">Select Month</option>
                                    <?php   $month = 1;
                                            $current_month = date('m');
                                            for ($i=$month; $i <=12 ; $i++) { ?>
                                               <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>" <?php if((isset($_POST['month']) && $_POST['month']==$i) || (!isset($_POST['month']) && $current_month==$i)) {  echo "selected"; }  ?>><?php echo date("F",mktime(0, 0, 0, $i, 10)); ?></option>
                                            <?php } ?> 
                                </select>
                                <span class="txt-danger"><?php echo form_error('month'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">State</label> 
                                <select class="form-control" id="state" name="state" disabled>
                                    <option value="">Select State</option>
                                    <?php if($states) {
                                        foreach ($states as $key => $value) {  ?>
                                            <option value="<?php echo $value['name']; ?>" <?php if(isset($_POST['state'])) { if($_POST['state']==$value['name']) { echo "selected"; } } ?>><?php echo $value['name']; ?></option>
                                    <?php } } ?>
                                </select>
                                <span class="txt-danger"><?php echo form_error('state'); ?></span>
                            </div>
                        </div>
                    </div>    
                    <div class="row" style="display:none;">                               
                        <div class="col-md-4">
                            <div class="form-group"> 
                                <label class="btn-block"></label>
                                <button type="submit" class="btn btn-default booking_submit" value="Search">Search History</button>  
                            </div>                                  
                        </div> 
                        <div class="col-md-4">
                        </div>                             
                    </div>
                </form> 
                <div class="table-responsive"> 
                    <div id="map" style="height:600px"></div>
                    <div id="trackInfo"></div>
                </div>                  
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.2.3/flatpickr.js"></script>
<script type="text/javascript">
$("#booking_date_from,#booking_date_to").flatpickr({  
    dateFormat: "d-m-Y",
}); 
</script>
<script src='<?php echo base_url(); ?>assets/select2/dist/js/select2.min.js' type='text/javascript'></script>
<link href='<?php echo base_url(); ?>assets/select2/dist/css/select2.min.css' rel='stylesheet' type='text/css'>
<script>
$(document).ready(function(){    
    
    $("#employee,#state").select2();
    $(document).on('change', '.view_reoprt', function(){   
        var view_reoprt= $('input[name="view_reoprt"]:checked').val(); 
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>target_report/getusers/',
            data: { 'view_reoprt': view_reoprt},
            success: function(msg){ 
                $("#employee").html(msg);
            }
        }); 
    });
});
</script>

    
  

<script type="text/javascript">
    var locations = []; 
    var current_date_show = '<?php echo $day_data; ?>';

    var current_latitude = '<?php echo $latitude; ?>';
    var current_longitude = '<?php echo $longitude; ?>';

    var locations =<?php echo json_encode($locations);?>;  
    
    //alert(JSON.stringify(locations_poly)); 
    function initMap() {
        var zoom = 16;
        if(current_date_show=='')
            var zoom = 14;
            var current_latitude = '<?php echo $latitude; ?>';
            if(current_latitude=='')
                current_latitude = parseFloat(locations[0][0].latitude);

            var current_longitude = '<?php echo $longitude; ?>';
            if(current_longitude=='')
                current_longitude = parseFloat(locations[0][0].longitude);

          var map = new google.maps.Map(document.getElementById('map'), {
              zoom: zoom,
              center: {lat:  parseFloat(current_latitude), lng:  parseFloat(current_longitude)},
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });
          var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
          var triangleCoords = [];
          var distnacecordinates = 0; 
           var distance = 0;
          for(var i=0; i< locations.length; i++){ 
            var AryForLine = [];
            var visit_count = 0;
            var color = Math.floor(Math.random()*16777215).toString(16);
            var prev_distance = 0;
                for(var j=0; j< locations[i].length; j++){               
                    AryForLine.push(new google.maps.LatLng(locations[i][j].latitude, locations[i][j].longitude));
                    AryForLine.push();
                    //alert(AryForLine);


                    triangleCoords.push({lat: parseFloat(parseFloat(locations[i][j].latitude).toFixed(5)), lng: parseFloat(parseFloat(locations[i][j].longitude).toFixed(5))});
                    var point = new google.maps.LatLng(
                            parseFloat(locations[i][j].latitude).toFixed(5),
                            parseFloat(locations[i][j].longitude).toFixed(5));
                    prev_distance = distance;
                    if(j<=(locations[i].length-2)) {
                        distance = calcDistance(point, new google.maps.LatLng(parseFloat(locations[i][j+1].latitude).toFixed(5),parseFloat(locations[i][j+1].longitude).toFixed(5)));
                    }
                    distnacecordinates = (parseFloat(distnacecordinates)+parseFloat(prev_distance));
                    
                    var address = locations[i][j].address;
                    var label_img = "http://maps.google.com/mapfiles/ms/icons/grey.png";
                    if(address.search("###")>=0)
                    { 
                        var marker = new google.maps.Marker({
                             map: map,
                             position: point,
                             /*icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                strokeColor: "#"+color,
                                scale: 10
                            },*/
                             //label:  j+"",
                            //icon: {url:label_img},
                             label: {text: visit_count+"", color: "#fff"},
                             title : locations[i][j].address + " ("+distnacecordinates+" KM) "+locations[i][j].username
                        });
                        visit_count++;
                    } 
                    else
                    {
                       var marker = new google.maps.Marker({
                             map: map,
                             position: point,
                             /*icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                strokeColor: "#"+color,
                                scale: 10
                            },*/
                             //label:  j+"",
                             icon: {url:label_img},
                             //label: {text: j+"", color: "#fff"},
                             title : locations[i][j].address + " ("+distnacecordinates+" KM) "+locations[i][j].username
                        }); 
                    } 
                   
                        
                     
                } 


                const lineSymbol = {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 8,
                    strokeColor: "#393",
                  };
                  // Create the polyline and add the symbol to it via the 'icons' property.
                  const line = new google.maps.Polyline({
                    path:AryForLine,
                    icons: [
                      {
                        icon: lineSymbol,
                        offset: "100%",
                      },
                    ],
                    map: map,
                  });

                  animateCircle(line);
          }
           
 
            


          console.log(triangleCoords); 
          parent.document.getElementById("trackInfo").innerHTML =  "&nbsp;&nbsp;&nbsp;Total Distance : "+distnacecordinates.toFixed(2) +" KM"; 
         /*  try {
              var bermudaTriangle = new google.maps.Polyline({
                  path: triangleCoords,
                  strokeColor: '#FF0000',
                 strokeOpacity: 0.8,
                 strokeWeight: 2,
                 geodesic: true,
                 // fillColor: '#FF0000',
                 // fillOpacity: 0.35 
                });
                bermudaTriangle.setMap(map);
            }
            catch(err) {
                 console.log(err.message);
            }
            var markerCluster = new MarkerClusterer(map, markers,
                    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'}); */
        }
    
        function calcDistance(p1, p2){return(google.maps.geometry.spherical.computeDistanceBetween(p1, p2)/1000).toFixed(2);}


        function animateCircle(line) {
  let count = 0;

  window.setInterval(() => {
    count = (count + 1) % 200;

    const icons = line.get("icons");

    icons[0].offset = count / 2 + "%";
    line.set("icons", icons);
  }, 60);
}

    </script>
    <!-- office@myxgen.com -->
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARcza2iCjWdXXORgjOT_ol7raY5DJ5wr4&callback=initMap&sensor=false&libraries=geometry">
    </script>