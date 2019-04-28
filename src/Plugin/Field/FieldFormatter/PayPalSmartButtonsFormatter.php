<?php

namespace Drupal\simple_paypal_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Formatter of the PayPal field with smart buttons.
 *
 * @FieldFormatter(
 *   id = "paypal_smart_buttons",
 *   label = @Translation("PayPal Smart Buttons"),
 *   field_types = {
 *     "simple_paypal_field",
 *   }
 * )
 */
class PayPalSmartButtonsFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['button_settings'] = [];

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $btn_settings = $this->getSetting('button_settings');
    $form['button_settings'] = [
      '#type' => 'collapsible',
      '#title' => $this->t('Button Settings'),
    ];
    $form['button_settings']['layout'] = [
      '#type' => 'select',
      '#options' => [
        'horizontal' => $this->t('Horizontal'),
        'vertical' => $this->t('Vertical'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $btn_settings['layout'] ?? '',
    ];
    $form['button_settings']['color'] = [
      '#type' => 'select',
      '#title' => $this->t('Color'),
      '#options' => [
        'gold' => $this->t('Gold (Recommended)'),
        'blue' => $this->t('Blue (First Alternative)'),
        'silver' => $this->t('Silver'),
        'white' => $this->t('White'),
        'black' => $this->t('Black'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $btn_settings['color'] ?? '',
    ];
    $form['button_settings']['shape'] = [
      '#type' => 'select',
      '#title' => $this->t('Shape'),
      '#options' => [
        'rect' => $this->t('Rect'),
        'pill' => $this->t('Pill'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $btn_settings['shape'] ?? '',
    ];
    $form['button_settings']['label'] = [
      '#type' => 'select',
      '#title' => $this->t('Label'),
      '#options' => [
        'paypal' => $this->t('Paypal'),
        'checkout' => $this->t('Checkout'),
        'buynow' => $this->t('BuyNow'),
        'pay' => $this->t('Pay'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $btn_settings['label'] ?? '',
    ];
    $form['button_settings']['tagline'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show tagline text'),
      '#return_value' => 1,
      '#default_value' => $btn_settings['tagline'] ?? 1,
      '#description' => $this->t(
        'CAUTION! Can fail the script with vertical layout'
      ),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = array_filter($this->getSetting('button_settings'));
    if (isset($settings['tagline'])) {
      $settings['tagline'] = 'Tagline';
    }
    if (($settings['label'] ?? '') !== 'installment') {
      unset($settings['period']);
    }
    return [implode(', ', $settings)];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $key => $item) {
      if ($item->value) {
        $elements[] = [
          '#theme' => 'paypal_smart_buttons_formatter',
          '#amount' => $items->getSetting('amount'),
          '#field' => $item,
          '#field_key' => $key,
          '#settings' => $this->getSettings(),
          '#attached' => [
            'library' => [
              'simple_paypal_field/paypal_smart_buttons',
            ],
          ],
        ];
      }
      else {
        // Payment processed, so not showing the buttons.
        $elements[] = [
          '#theme' => 'paypal_smart_buttons_formatter_disabled',
          '#item' => $item,
        ];
      }
    }
    return $elements;
  }

}
