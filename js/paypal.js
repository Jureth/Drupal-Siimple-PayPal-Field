(function (jQuery, settings) {
  Drupal.behaviors.paypal_smart_buttons = {
    attach: function (context) {
      jQuery('.paypal-button', context).each(function (key, element) {
        paypal.Buttons({
          style: JSON.parse(element.getAttribute('data-settings')),
          createOrder: function (data, actions) {
            return actions.order.create({
              purchase_units: [{
                amount: {
                  value: element.getAttribute('data-value'),
                }
              }]
            });
          },
          onApprove: function (data, actions) {
            actions.order.capture().then(function (details) {
              //window.location.reload();
              fetch('/simple_paypal_field/approve', {
                method: 'post',
                headers: new Headers({
                  'Content-type': 'application/json'
                }),
                body: JSON.stringify({details: details})
              })
            });

          },
        }).render(element);
      })
    }
  }
})(jQuery, drupalSettings);
