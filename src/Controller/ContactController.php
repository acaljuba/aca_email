<?php
	namespace Drupal\aca_email\Controller;
	
	use Drupal\Core\Controller\ControllerBase;
	use Symfony\Component\HttpFoundation\Response;
	
	/**
	 * Controller for sending E-mail messages.
	 * 
	 * This is where our contact page is defined.
	 * 
	 * @ingroup aca_email
	 */
	class ContactController extends ControllerBase {
		
		/**
		 * A page for sending E-mail messages.
		 * 
		 * @return form
		 * 	A render array containing our 'Send E-mail' page content.
		 */
		public function contact() {
			// First we build the form using the appropriate template form located at Drupal\aca_email\Form.
			$form = \Drupal::formBuilder()->getForm('Drupal\aca_email\Form\ContactForm');
			
			// While sending the array, we are calling the Drupal service to render the form.
			return array(
				'#type' => 'form',
				'#markup' => \Drupal::service("renderer")->render($form)
			);
		}
	}
?>