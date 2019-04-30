<?php

namespace Drupal\simple_paypal_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'boolean_checkbox' widget.
 *
 * @FieldWidget(
 *   id = "paypal_smart_buttons",
 *   label = @Translation("PayPal smart buttons"),
 *   field_types = {
 *     "simple_paypal_field",
 *     "auto_off_paypal_field"
 *   },
 *   multiple_values = FALSE
 * )
 */
class PayPalSmartButtonsWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];
    $element['value'] = $element;
    if ($item->value) {
      $element['value'] += [
        '#theme' => 'paypal_smart_buttons_formatter',
        '#amount' => $items->getSetting('amount'),
        '#field' => $item,
        '#field_key' => $delta,
        '#settings' => ['button_settings' => $this->getSettings()],
        '#attached' => [
          'library' => [
            'simple_paypal_field/paypal_smart_buttons',
          ],
        ],
      ];
    }
    else {
      // @todo permissions check here.
      if ($this->currentUser->hasPermission(
        'simple_paypal_field.administer'
      )) {
        $element['value'] += [
          '#type' => 'checkbox',
          '#default_value' => !empty($items[0]->value),
          '#title_display' => 'after',
          '#title' => $this->t('Enable'),
        ];
      }
      else {
        // Payment processed, so not showing the buttons.
        $element['value'] += [
          '#theme' => 'paypal_smart_buttons_formatter_disabled',
          '#item' => $item,
        ];
      }
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['layout'] = [
      '#type' => 'select',
      '#options' => [
        'horizontal' => $this->t('Horizontal'),
        'vertical' => $this->t('Vertical'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $this->getSetting('layout') ?? '',
    ];
    $form['color'] = [
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
      '#default_value' => $this->getSetting('color') ?? '',
    ];
    $form['shape'] = [
      '#type' => 'select',
      '#title' => $this->t('Shape'),
      '#options' => [
        'rect' => $this->t('Rect'),
        'pill' => $this->t('Pill'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $this->getSetting('shape') ?? '',
    ];
    $form['label'] = [
      '#type' => 'select',
      '#title' => $this->t('Label'),
      '#options' => [
        'paypal' => $this->t('Paypal'),
        'checkout' => $this->t('Checkout'),
        'buynow' => $this->t('BuyNow'),
        'pay' => $this->t('Pay'),
        '' => $this->t('Not Set'),
      ],
      '#default_value' => $this->getSetting('label') ?? '',
    ];
    $form['tagline'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show tagline text'),
      '#return_value' => 1,
      '#default_value' => $this->getSetting('tagline') ?? 0,
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
    $settings = array_filter($this->getSettings());
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
  public static function defaultSettings() {
    $settings = parent::defaultSettings();
    $settings['layout'] = '';
    $settings['color'] = '';
    $settings['shape'] = '';
    $settings['label'] = '';
    $settings['tagline'] = 0;

    return $settings;

  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, AccountProxyInterface $user) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $third_party_settings
    );
    $this->currentUser = $user;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('current_user')
    );
  }

}
