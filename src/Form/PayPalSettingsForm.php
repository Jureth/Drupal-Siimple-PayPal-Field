<?php

namespace Drupal\simple_paypal_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * PayPal settings form.
 */
class PayPalSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['simple_paypal_field.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_paypal_field_settings_form';
  }

  /**
   * {@inheritdoc}
   */
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
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('simple_paypal_field.settings')
      ->set('paypal.mode', $form_state->getValue('mode'))
      ->set('paypal.client_id', $form_state->getValue('client_id'))
      ->set('paypal.secret_id', $form_state->getValue('secret_id'))
      ->save();
  }

}
