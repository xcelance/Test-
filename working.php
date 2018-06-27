
<?php
/**
 * @copyright	Copyright (c) 2017 business_data. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php
	// Call the default header layout
	//include ( JPATH_BASE .DS.'/components/com_dobinsonsdistributormap/fork/header_layout.php');
	
?>
<style>
.myClusterClass {
    background-image: url("<?php echo JURI::base()."components/com_dobinsonsdistributormap/fork/assets/images/blank-marker2.png" ?>") !important;
	
}
</style>
<link rel="stylesheet" href="components/com_dobinsonsdistributormap/fork/assets/css/distributormap.css">
<?php
//JHtml::stylesheet(Juri::base() . 'media/com_dobinsonsdistributormap/css/distributormap.css');

$db = JFactory::getDbo();

//$DistributorData = $this->items;
//echo "<pre>";print_r($DistributorData);

$queryDestributer = "SELECT * FROM #__dobinsonsdistributormap_dobinsonsdistributors LEFT JOIN #__dobinsonsdistributormap_locationcountries ON #__dobinsonsdistributormap_dobinsonsdistributors.distributor_country = #__dobinsonsdistributormap_locationcountries.id LEFT JOIN #__dobinsonsdistributormap_locationstates ON #__dobinsonsdistributormap_dobinsonsdistributors.distributor_state = #__dobinsonsdistributormap_locationstates.id LEFT JOIN #__dobinsonsdistributormap_markerimages ON #__dobinsonsdistributormap_dobinsonsdistributors.distributor_maker_type = #__dobinsonsdistributormap_markerimages.id WHERE #__dobinsonsdistributormap_dobinsonsdistributors.published=1 ORDER BY #__dobinsonsdistributormap_dobinsonsdistributors.ordering ASC";
$db->setQuery($queryDestributer);
$getDestributerData = $db->loadObjectList();
//echo"<pre>"; print_r($getDestributerData); echo"</pre>";

for($i=0;$i<count($getDestributerData); $i++)
{
	$data=str_replace("<p>", "~~", str_replace("</p>", "~~~", $getDestributerData[$i]->distributor_opening_times));
	$string = preg_replace('/\s+/', '~', $data);
	
	$getDestributerData[$i]->distributor_opening_times=$string; 
	//unset($getDestributerData[$i]->distributor_opening_times);
}

$queryStore = "SELECT * FROM #__dobinsonsdistributormap_dobinsonsstores LEFT JOIN #__dobinsonsdistributormap_locationcountries ON #__dobinsonsdistributormap_dobinsonsstores.store_country = #__dobinsonsdistributormap_locationcountries.id LEFT JOIN #__dobinsonsdistributormap_locationstates ON #__dobinsonsdistributormap_dobinsonsstores.store_state = #__dobinsonsdistributormap_locationstates.id LEFT JOIN #__dobinsonsdistributormap_markerimages ON #__dobinsonsdistributormap_dobinsonsstores.store_marker_type = #__dobinsonsdistributormap_markerimages.id WHERE #__dobinsonsdistributormap_dobinsonsstores.published=1 ORDER BY #__dobinsonsdistributormap_dobinsonsstores.ordering ASC";
$db->setQuery($queryStore);
$getStoreData = $db->loadObjectList();

for($i=0;$i<count($getStoreData); $i++)
{
	$data=str_replace("<p>", "~~", str_replace("</p>", "~~~", $getStoreData[$i]->store_opening_times));
	$string = preg_replace('/\s+/', '~', $data);
	
	$getStoreData[$i]->store_opening_times=$string; 
	
}


/* echo"<pre>"; print_r($getStoreData); echo"</pre>"; */

$DistributorData = array_merge($getStoreData,$getDestributerData);
// shuffle($DistributorData);

$queryCountry = "SELECT * FROM #__dobinsonsdistributormap_locationcountries order by country_name asc";
$db->setQuery($queryCountry);
$rowsCountry1 = $db->loadObjectList();
//echo "<pre>";print_r($rowsCountry1);

$countriesArr = array();
foreach($rowsCountry1 as $row) {
	$latitude = ($row->north+$row->south)/2 ;
	$longitude = ($row->east+$row->west)/2 ;
	$countriesArr[] = array('name'=>$row->country_name, 'latitude'=>$latitude, 'longitude'=>$longitude, 'id'=>$row->id);
}
 // echo "<pre>";print_r($countriesArr);
$queryCountry = "SELECT * FROM 	qvp13_dobinsonsdistributormap_markerimages";
$db->setQuery($queryCountry);
$rowsCountry = $db->loadObjectList();
//echo"<pre>";print_r($rowsCountry);die; 
$storeImages = array();

//$distributonIcon = 'https://dobinsonsprings.com/images/icons/distributor-marker.png';
foreach($rowsCountry as $row) { 
	$storeImages[$row->id] = $row->marker_image;		
}


//echo"<pre>";print_r($storeImages);die;
$dataArr1 = json_encode($DistributorData);

$dataArr = str_replace("'", "\'", $dataArr1);

$storeImages = json_encode($storeImages);

$countryarr=array();
//echo"<pre>";  print_r($DistributorData); echo"</pre>";
foreach( $DistributorData as $disdata)
{		
	if(!in_array($disdata->country_name, $countryarr)) {
		$countryarr[$disdata->country_name] = $disdata->country_name;		
	}
	//array_push($countryarr,$countryId);
}

?>
<div class="col-md-12 col-lg-12 col-sm-12">
	<div id="showMsg"></div>
	<div class="col-lg-8 col-md-12 col-sm-12 mapContainer">
		<div class="row mapTopRow" >
			<div class="col-lg-6 col-md-6 col-sm-6 mapTopRowLeft">
				<p class="header-title">AUSTRALIAN CUSTOMERS</p>
				<div class="input-group col-lg-12 col-md-12 col-sm-12 input-div">
                    <input class="col-lg-10 col-md-10 col-sm-12 col-xs-12" class="form-control" placeholder="Post code" name="address" id="address">
			    	<button class="btn btn-default go-btn col-lg-2 col-md-2 col-sm-12 col-xs-12" onclick="geoCodeLocation()" type="button">Go!</button>
				</div>
			</div>
			<?php //print_r(array_unique($countryarr) ); ?>
			<div class="col-lg-6 col-md-6 col-sm-6 mapTopRowRight">
				<p class="header-title color-white">INTERNATIONAL CUSTOMERS</p>
				<div class="btn-group input-group col-lg-12 col-md-12 col-xs-12 dropdown" >
					<button type="button" class="btn-normal col-lg-10 col-md-10 col-sm-12 col-xs-11" id="selectedCountryBtn">Select Country</button>
					<button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle toggle-btn"><i class="fa fa-angle-down"></i></button>
					<ul class="dropdown-menu menu-dropdown-ul">
						<?php
						
							/* foreach($DistributorData as $country) {
								$countryName = $country->_country_name;
								$countryId = $country->country;
								echo ' <li><a href="javascript:void(0)" data-value="'.$countryId.'">'.$countryName.'</a></li>';
							} */
							 foreach($countryarr as $key => $c_name)
							{
										echo ' <li><a href="javascript:void(0)" data-value="'.$key.'">'.$c_name.'</a></li>';
							} 
							
							
						?>
					</ul>
				</div>
			</div>
		</div>
		<div id="map-canvas" ></div>
	</div>
	<div class="col-lg-3 col-md-12 col-sm-12 sidebarContainer">
		<div id="rightSideContainer">
			<div class="white-background-row">
				<div class="sidebar-header row">
					<div class="col-lg-12 col-md-12 col-sm-12 textFindStoreContainer" onClick="sharelocation()"><button onClick="sharelocation()" class="find-btn"><i class="fa fa-search search-icon" ></i>FIND YOUR NEAREST STORE</button><button class="refresh-btn" onClick="window.location.reload()" >Refresh</button></div>
				</div>
				<div id="businessList" ></div>
			</div>
			<div class="div-logo">
    		</div>
			<?php //echo"<pre>"; print_r($rowsCountry); echo"</pre>"; ?>
    			<div class="box-company-logo">
				<?php 
					$i=0;
					foreach($rowsCountry as $row) { ?>
					<?php if($i=="0") {?>
							<div class="logo-with-name"><span class="map-legend-logo"><img class="dobinson-logo" src="<?php echo JURI::base(); ?>components/com_dobinsonsdistributormap/files/markerimages_marker_image/<?php  echo $row->marker_image; ?>" /></span><span class="map-legend-text">Dobinsons<br />Stores</span></div>
					<?php  } ?>
					
					<?php if($i=="1") {?>
							<div class="logo-with-name"><span class="map-legend-logo"><img class="dobinson-logo" src="<?php echo JURI::base(); ?>components/com_dobinsonsdistributormap/files/markerimages_marker_image/<?php  echo $row->marker_image; ?>" /></span><span class="map-legend-text">Dobinsons<br />Distributors</span></div>
							
							
					<?php  } ?>
				<?php 
					$i++;
				} ?>
    				
    				
    			</div>
		</div>

	</div>
	
</div>

<script>
 console.log(<?php echo $dataArr; ?>);
var dataArr = JSON.parse('<?php echo $dataArr; ?>');
var storeImages = JSON.parse('<?php echo $storeImages; ?>');
// console.log(storeImages);

var map, bounds, infoBubble, userlocation, geocoder;
var markersArr = [];
var markersNewArr = [];
var currentSearchFor = ''; //Australia or Countries.
var markerClusterer = null;

function initMap(){
	geocoder = new google.maps.Geocoder();
	bounds = new google.maps.LatLngBounds();
	infoBubble = new InfoBubble({
			shadowStyle: 0,
			maxWidth : 600,
			minWidth : 150,
			borderWidth: 5,
			borderColor:'#ADD8E6',
			arrowSize: 15,
        });
	var myOptions = {
		zoom: 4,
		center: new google.maps.LatLng(-25.363, 131.044),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);	
	setAllStores();
	
}

function geoCodeLocation(){
	var address = jQuery('#address').val();
	if(address == ''){
		jQuery('div#showMsg').show().html('<div class="alert alert-danger"><strong>Please insert any Postcode or Suburb !!</strong></div>').fadeOut(5000);
		jQuery('#geoCodeLocation').focus();
		return false;
	}
	
	address += '';
	
	
	var result = "";
	geocoder.geocode( { 'address': address, 'region': 'au' }, function(results, status) {
		 if (status == google.maps.GeocoderStatus.OK) {
			var lat = results[0].geometry.location.lat();
			var lng = results[0].geometry.location.lng();
			
			if(userlocation){
				userlocation.setMap(null);
			}
			
			var myLatLng = new google.maps.LatLng(lat, lng);
			userlocation = new google.maps.Marker({ 
				position: myLatLng,
				map: map,
				title: 'Mylocation'
			}); 
			currentSearchFor = 'au';
			setAllStores(lat, lng);
		 } else {
			alert('Sorry, Google map api : Unable to find address.');
		 }
	});
}

function sharelocation() {
	console.log(navigator);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    }
}

function showPosition(position) {
	var lat = position.coords.latitude;
	var lng = position.coords.longitude;
	
	if(userlocation){
		userlocation.setMap(null);
	}
	
	var myLatLng = new google.maps.LatLng(lat, lng);
	userlocation = new google.maps.Marker({ 
		position: myLatLng,
		map: map,
		title: 'Mylocation'
	}); 
	currentSearchFor = 'shareLocation';
	 setAllStores(lat, lng);
}

function setAllStores(userlat, userlng){

	if(dataArr.length > 0){ 
		if(infoBubble){
			infoBubble.setMap(null);
		}
		if (markerClusterer) {
          markerClusterer.clearMarkers();
        }
		jQuery('#businessList').html('');
		clearmapMarkers(); 
		bounds  = new google.maps.LatLngBounds();


		for(var i in dataArr){
			//console.log(dataArr[i]);
			if(dataArr[i].hasOwnProperty('distributor_name')){
				var id = dataArr[i].id;
				var name = dataArr[i].distributor_name ;
				var email_address = dataArr[i].distributor_email_address ;
				var phone_number = dataArr[i].distributor_phone_number ;
				var open_time = dataArr[i].open_time ;
				var street_address = dataArr[i].distributor_street_address;
				var latitude = parseFloat(dataArr[i].distributor_latitude) ;
				var longitude = parseFloat(dataArr[i].distributor_longitude) ;
				var country = dataArr[i].country_name;
				var website = dataArr[i].distributor_website;
				var marker_type = dataArr[i].distributor_maker_type;
				var city = dataArr[i].city != null ? dataArr[i].city : '';
				var _state_name = dataArr[i].state_name != null ? dataArr[i].state_name : '';
				var post_code = dataArr[i].distributor_post_code != null ? dataArr[i].distributor_post_code : ''; 
			} else {
				var id = dataArr[i].id;
				var name = dataArr[i].store_name ;
				var email_address = dataArr[i].store_email_address ;
				var phone_number = dataArr[i].store_phone_number ;
				var open_time = dataArr[i].open_time ;
				var street_address = dataArr[i].store_street_address;
				var latitude = parseFloat(dataArr[i].store_latitude) ;
				var longitude = parseFloat(dataArr[i].store_longitude) ;
				var country = dataArr[i].country_name;
				var website = dataArr[i].store_website;
				var marker_type = dataArr[i].store_maker_type;
				var city = dataArr[i].city != null ? dataArr[i].city : '';
				var _state_name = dataArr[i].state_name != null ? dataArr[i].state_name : '';
				var post_code = dataArr[i].store_post_code != null ? dataArr[i].store_post_code : ''; 

			}
			
			if(isNaN(latitude)){
				continue;
			}

			var myLatLng = new google.maps.LatLng(latitude, longitude);
			
			/**********************For Search Start*****************************/
			var distance = '';
			if(currentSearchFor == 'au' || currentSearchFor == 'shareLocation'){
				if(userlat != undefined &&  userlng != undefined){
					var userLatLng = new google.maps.LatLng(userlat, userlng);
					bounds.extend(userLatLng);
					distance = calcDistance(userLatLng, myLatLng); 
					if(distance > 10){
					}
				}
			}
			if(currentSearchFor == 'countries'){
				var getCountrySelected = jQuery('button#selectedCountryBtn').val();
				if(getCountrySelected != country) {
					continue;
				}
			}
			/**********************For Search End*****************************/
			bounds.extend(myLatLng);
			var image = {
			url: '<?php echo JUri::base(); ?>components/com_dobinsonsdistributormap/files/markerimages_marker_image/'+dataArr[i].marker_image,
				scaledSize: new google.maps.Size(38, 45)
			};
			
			var marker = new google.maps.Marker({ 
				position: myLatLng,
				map: map,
				icon: image,
				businessid : id,
				name: name,
				street_address: street_address,
				email_address: email_address,
				open_time: open_time,
				latitude: latitude,
				longitude: longitude,
				phone_number: phone_number,
				distance: distance,
				country: country,
				website: website,
				marker_type: marker_type,
				city: city, _state_name: _state_name, post_code: post_code
			}); 

			markersArr.push(marker);
			if(marker_type == '2') {
				markersNewArr.push(marker);
			}
			google.maps.event.addListener(marker, 'click', function () {
				var markerContent = '<div class="popup-heading">';
				markerContent += '<span><b>'+this.name+'</b></span>';
                markerContent += '</div>';
				markerContent += '<table class="table table-stripped">';
				markerContent += '<tr><td><strong>Address:</strong></td><td>'+this.street_address+'</td></tr>';
				markerContent += '<tr><td><strong>Phone:</strong></td><td>'+this.phone_number+'</td></tr>';
				markerContent += '<tr><td><strong>Email:</strong></td><td><a href="mailto:'+this.email_address+'">'+this.email_address+'</a></td></tr>';
				markerContent += '<tr><td><strong>Website:</strong></td><td><a href="'+chkHttps(this.website)+'">'+this.website+'</a></td></tr>';
				//markerContent += '<tr><td><strong>Open Time:</strong></td><td>'+this.open_time+'</td></tr>';
				markerContent += '</table>';
				
				infoBubble.setContent(markerContent);
				infoBubble.open(map, this);
            });
		}

		if(markersArr.length > 0){
			setSidebarDetails();
			map.fitBounds(bounds);
			markerClusterer = new MarkerClusterer(map, markersNewArr, {
			imagePath: '<?php echo JURI::base()."components/com_dobinsonsdistributormap/fork/assets/images/blank-marker2.png" ?>'
			});
			setTimeout(function(){
				if(userlat != undefined &&  userlng != undefined){
					var userLatLng = new google.maps.LatLng(userlat, userlng);
					map.setCenter(userLatLng);
					map.setZoom(13);
				}
			}, 600)
		 	
		} else{
			jQuery('#businessList').html('<div class="alert alert-danger" style="font-weight: bold;text-align: center;" >No Result Found.</div>');
		}
	}
}

function chkHttps(geturl){
	var val = geturl;
	if (val && !val.match(/^http([s]?):\/\/.*/)) {
		return 'https://' + val;
	}
	return val;
}

function setSidebarDetails(){ 
	if(markersArr.length > 0) {
		var html = '';
		for(i in markersArr){ 
			if(isNaN(i)){
				continue;
			} 
			html += '<div data-distance="'+markersArr[i].distance+'" class="businessDetailsContainer row">';
			html += 	'<div class="map-list-heading">'+markersArr[i].name+'</div>';
			if(markersArr[i].distance != '')
			html +=		'<div class="map-list-distance">Distance: '+markersArr[i].distance+' klms</div>';
			html += 	'<div class="map-list-street_address">'+markersArr[i].street_address+'</div>';
			if(markersArr[i].city != '' || markersArr[i]._state_name != '' || markersArr[i].post_code != ''){
				html += 	'<div class="map-list-city">'+markersArr[i].city+' '+markersArr[i]._state_name+' '+ markersArr[i].post_code +'</div>';
			}
			html += 	'<div class="map-list-phone-number">Phone: '+markersArr[i].phone_number+'</div>';
			html +=		'<div class="map-list-email">Email: <a href="mailto:'+markersArr[i].email_address+'">'+markersArr[i].email_address+'</a></div>';
			html +=		'<div class="map-list-website">Website: <a target="_blank" href="'+chkHttps(markersArr[i].website)+'">'+markersArr[i].website+'</a></div>';
			var tmpC = i+1;
			//html +=		'<div class="openTimeHeading" onclick="showOpenTiming(this, '+tmpC+')" style="cursor:pointer;font-size: 14px;font-weight: 500;background-color:rgb(251,221,18);">&nbsp;<i class="fa fa-caret-right" style="font-size:17px;" ></i>&nbsp;&nbsp;Opening Times</div>';
			html += 	'<div class="openTimeContainer" style="display:none;font-size: 14px;font-weight: 500;">'+markersArr[i].open_time+'</div>';
			html +=	'</div>';	
		}
		jQuery('#businessList').html(html);	
		
		resetOrderByDistance();
	}
}

var clickedHeader = '';
function showOpenTiming(element , count){
	if(clickedHeader != '' && clickedHeader == count){
		var getDisplayProp = jQuery(element).next('div.openTimeContainer').css('display');
		if(getDisplayProp == 'none') {
			jQuery(element).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
			jQuery(element).next('div.openTimeContainer').slideDown();
		}else{
			jQuery(element).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
			jQuery(element).next('div.openTimeContainer').hide();
		}
		return false;
	}
	clickedHeader = count;
	
	//jQuery('div.openTimeHeading i').removeClass('fa-caret-down').addClass('fa-caret-right');
	//jQuery('div.openTimeContainer').hide();
	
	var getDisplayProp = jQuery(element).next('div.openTimeContainer').css('display');
	if(getDisplayProp == 'none') {
		jQuery(element).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
		jQuery(element).next('div.openTimeContainer').slideDown();
	}
}

function calcDistance(p1, p2) {
  return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
}

function clearmapMarkers(){ 
	if(markersArr.length > 0 ){
		for(var i in markersArr){
			if(isNaN(i)){
				continue;
			}
			markersArr[i].setMap(null);
		}
		markersArr = [];
		markersNewArr = [];
	}
}
 
function resetOrderByDistance(){
	setTimeout(function(){
		//if(currentSearchFor == 'au'){
			var list = jQuery('div#businessList');
			var listItems = list.find('div.businessDetailsContainer').sort(function(a,b){ return jQuery(a).attr('data-distance') - jQuery(b).attr('data-distance'); });
			list.find('div.businessDetailsContainer').remove();
			list.append(listItems);
		//}
	}, 100);
}

jQuery(document).ready(function(){
    jQuery(".mapTopRowRight .dropdown-menu li a").click(function(){ 
        currentSearchFor = 'countries'
        jQuery(this).parents(".dropdown").find('.btn-normal').eq(0).text(jQuery(this).text() );
        jQuery(this).parents(".dropdown").find('.btn-normal').eq(0).val(jQuery(this).data('value'));
        
        setTimeout(function(){
            setAllStores();
        }, 100)
    });
})
</script>
<script src="https://cdn.rawgit.com/googlemaps/v3-utility-library/master/infobubble/src/infobubble.js"></script>
<script src="https://dobinsonsprings.com/images/clusters/src/markerclusterer2.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDL5Ae9Mv4lqPyQ1wD3NUhHkpmuX85DFo4&libraries=geometry&callback=initMap"
    async defer></script>
	
