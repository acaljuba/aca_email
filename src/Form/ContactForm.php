<?php
	namespace Drupal\aca_email\Form;
	
	use Drupal\Core\Form\FormStateInterface;
	use Drupal\Core\Form\FormBase;
	use Drupal\Core\Mail\MailManagerInterface;
	use Symfony\Component\DependencyInjection\ContainerInterface;
	use Drupal\Core\Language\LanguageManagerInterface;
	use Symfony\Component\HttpFoundation\Response;
	
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
		
		/**
		 * The language manager.
		 * 
		 * @var \Drupal\Core\Mail\MailManagerInterface
		 */
		protected $languageManager;

		/**
		 * Public constructor.
		 * 
		 * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
		 *   The mail manager.
		 * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
		 *   The language manager.
		 */
		public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager) {
			$this->mailManager = $mail_manager;
			$this->languageManager = $language_manager;
		}
		
		/**
		 * {@inheritdoc}
		 */
		public static function create(ContainerInterface $container) {
			return new static(
				$container->get('plugin.manager.mail'),
				$container->get('language_manager')
			);
		}

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
			
			$form[location] = array(
				'#type' => 'label',
				'#title' => 'Location'
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
			
			// Validates email address. Also, check on DNS servers if receiving E-mail exists (not sure does it works).
			// if (!valid_email_address($form_state->getValue('email'))) {
			if (!\Drupal::service('email.validator')->isValid($form_state->getValue('email', TRUE, TRUE))) {
				$form_state->setErrorByName('email', $this->t('That e-mail address is not valid.'));
			}
		}

		/**
		 * Submits the form.
		 * 
		 * {@inheritdoc}
		 */
		public function submitForm(array &$form, FormStateInterface $form_state) {
			$values = $form_state->getValues();
			
			// Specify module name so the hook_mail() method can be invoked.
			$module = 'aca_email';
			
			// Template key is needed so we can configure in Mail System which formatter and sender are used for this module.
			// If key is not specified, then module uses default formatter and sender (if any exists).
			// Also, this requires MailSystem and Development modules to be installed.
			$key = 'contact_message';
			
			// Receiving E-mail address.
			$to = $values['email'];
			
			// Sending E-mail address.
			$from = \Drupal::config('system.site')->get('mail');
			
			// Additional information, see hook_mail().
			$params = $values;
			
			// Get default language code.
			$language_code = $this->languageManager->getDefaultLanguage()->getId();
			
			// Not sure if needed.
			// $render = ['#theme' => 'item_list'];
			// \Drupal::service('renderer')->render($render);
			
			// $result = $this->mailManager->mail($module, $key, $to, $language_code, $params, $from, TRUE);
			
			// Send E-mail using Drupal service, this calls Mail Manager.
			$result = \Drupal::service('plugin.manager.mail')->mail($module, $key, $to, $language_code, $params, $from, TRUE);
			if ($result['result'] == TRUE) {
				drupal_set_message($this->t('Your message has been sent.'));
			}
			else {
				drupal_set_message($this->t('There was a problem sending your message and it was not sent.'), 'error');
			}
			
			// $render = ['#theme' => 'item_list'];
			// \Drupal::service('renderer')->render($render);
		    // 
			// $module = 'mailsystem_test';
			// $key = 'theme_test';
			// $to = 'acaljuba@gmail.com';
			// $langcode = \Drupal::languageManager()->getDefaultLanguage()->getId();
			// \Drupal::service('plugin.manager.mail')->mail($module, $key, $to, $langcode);
			// return new Response('', 204);
		}
	}
?>