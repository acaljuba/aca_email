<?php

/**
 * @file
 * Contains \Drupal\aca_email\GeocodeClass.
 */

namespace Drupal\aca_email;

use Drupal\aca_email\GeocodeClassInterface;

/**
 * Defines an class for getting geocode location.
 */
class GeocodeClass implements GeocodeClassInterface {
	/**
	 * GeocodeClass main function.
	 */
	function geocode($address){
		// url encode the address
		$address = urlencode($address);
		
		// google map geocode api url
		$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
	
		// get the json response
		$resp_json = file_get_contents($url);
		
		// decode the json
		$resp = json_decode($resp_json, true);
	
		// response status will be 'OK', if able to geocode given address 
		if($resp['status']=='OK'){
	
			// get the important data
			$lati = $resp['results'][0]['geometry']['location']['lat'];
			$longi = $resp['results'][0]['geometry']['location']['lng'];
			$formatted_address = $resp['results'][0]['formatted_address'];
			
			// verify if data is complete
			if($lati && $longi && $formatted_address){
			
				// put the data in the array
				$data_arr = array();            
				
				array_push(
					$data_arr, 
						$lati, 
						$longi, 
						$formatted_address
					);
				
				return $data_arr;
				
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
}
?>