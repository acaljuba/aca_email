<?php
	namespace Drupal\aca_email\Form;
	
	use Drupal\Core\Form\FormStateInterface;
	use Drupal\Core\Form\FormBase;
	use Drupal\Core\Mail\MailManagerInterface;
	
	/** 
	 * E-mail contact form example, still in Development.
	 * 
	 * @ingroup aca_email
	 */
	class ContactForm extends FormBase {
		/**
		 * The mail manager.
		 * 
		 * @var \Drupal\Core\Mail\MailManagerInterface
		 */
		protected $mailManager;

		// 
		// Public constructor
		// 
		// public function __construct(MailManagerInterface $mail_manager) {
		// 	$this->mailManager = $mail_manager;
		// }

		/**
		 * Retrieves the form id.
		 * 
		 * {@inheritdoc}
		 */
		public function getFormId() {
			return 'aca_email_form';
		}

		/**
		 * Builds the form.
		 * 
		 * {@inheritdoc}
		 */
		public function buildForm(array $form, FormStateInterface $form_state) {
			$form['intro'] = array(
				'#markup' => t('Form for sending message to an e-mail address!'),
			);
			
			// First name.
			$form['first_name'] = array(
				'#type' => 'textfield',
				'#title' => t('First name'),
				'#required' => TRUE,
			);
			
			// Last name.
			$form['last_name'] = array(
				'#type' => 'textfield',
				'#title' => t('Last name'),
				'#required' => TRUE,
			);
			
			// E-mail address.
			$form['email'] = array(
				'#type' => 'textfield',
				'#title' => t('Email'),
				'#required' => TRUE,
			);
			
			// Phone.
			$form['phone'] = array(
				// '#type' => 'tel',
				'#type' => 'number',
				'#title' => $this->t('Phone'),
			);
			
			// Address.
			$form['address'] = array(
				'#type' => 'textfield',
				'#title' => t('Address'),
			);
			
			// Message.
			$form['message'] = array(
				'#type' => 'textarea',
				'#title' => t('Message'),
				// '#required' => TRUE,
			);
			
			// Submit button.
			$form['submit'] = array(
				'#type' => 'submit',
				'#value' => t('Submit'),
			);
			
			return $form;
		}

		/**
		 * Validates the form.
		 * 
		 * {@inheritdoc}
		 */
		public function validateForm(array &$form, FormStateInterface $form_state) {
			// Validates first and last name.
			if (!ctype_alpha($form_state->getValue('first_name'))) {
				$form_state->setErrorByName('first_name', $this->t('First name must contain only letters.'));
			}
			if (!ctype_alpha($form_state->getValue('last_name'))) {
				$form_state->setErrorByName('last_name', $this->t('Last name must contain only letters.'));
			}
			
			// Validates phone number.
			$phone = $form_state->getValue('phone');
			if (!is_numeric($phone)) {
				$form_state->setErrorByName('phone', $this->t('Phone number must be numeric.'));
			}
			if (strlen($phone) < 5) {
				$form_state->setErrorByName('phone', $this->t('The phone number must be at least 5 numbers long.'));
			}
			
			// Validates email address.
			if (!valid_email_address($form_state->getValue('email'))) {
				$form_state->setErrorByName('email', t('That e-mail address is not valid.'));
			}
		}

		/**
		 * Submits the form.
		 * 
		 * {@inheritdoc}
		 */
		public function submitForm(array &$form, FormStateInterface $form_state) {
			// Does nothing, for now
			$values = $form_state->getValues();
			foreach ($values as $key => $value) {
				$label = isset($form[$key]['#title']) ? $form[$key]['#title'] : $key;
				
				if ($value && $label) {
					$display_value = is_array($value) ? preg_replace('/[\n\r\s]+/', ' ', print_r($value, 1)) : $value;
					$message = $this->t('Value for %title: %value', array('%title' => $label, '%value' => $display_value));
					drupal_set_message($message);
				}
			}
			
			drupal_set_message(t('Your message has been sent.'));
		}
	}
?>