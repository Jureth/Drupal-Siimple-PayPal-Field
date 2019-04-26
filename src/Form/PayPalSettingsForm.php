<?php

namespace Drupal\simple_paypal_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class PayPalSettingsForm extends ConfigFormBase {

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['simple_paypal_field.settings'];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'simple_paypal_field_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('simple_paypal_field.settings');
    $form['mode'] = [
      '#title' => $this->t('PayPal app mode'),
      '#type' => 'select',
      '#options' => [
        'sandbox' => $this->t('Sandbox'),
        'live' => $this->t('Live'),
      ],
      '#default_value' => $config->get('mode'),
      '#description' => $this->t(
        'Use the same mode as it set in the app settings on the PayPal dev dashboard'
      ),
    ];

    $form['client_id'] = [
      '#title' => $this->t('Client ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('client_id'),
      '#description' => $this->t(
        'Client ID from sandbox/live API credentials of your PayPal application'
      ),
    ];

    $form['secret_id'] = [
      '#title' => $this->t('Secret ID'),
      '#type' => 'textfield',
      '#default_value' => $config->get('secret_id'),
      '#description' => $this->t(
        'Secret ID from sandbox/live API credentials of your PayPal application'
      ),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('simple_paypal_field.settings')
      ->set('mode', $form_state->getValue('mode'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('secret_id', $form_state->getValue('secret_id'))
      ->save();
  }
}
