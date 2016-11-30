<?php

/**
 * @file
 * Contains \Drupal\aca_email\GeocodeClassInterface.
 */

namespace Drupal\aca_email;

/**
 * Provides an interface defining an Geocode Class.
 */
interface GeocodeClassInterface {
	/**
	 * This method returns geographic location of given address.
	 * 
	 * @param string address.
	 * 
	 * @return array
	 */
	public function geocode($address);
}
?>