# SIMPLE PAYPAL FIELD

## INTRODUCTION
This module provides a PayPal Smart Payment buttons to the site as a entity
field or just a theme hook. The buttons are completely workable and require
only PayPal client id from the site owner.   

## REQUIREMENTS
Drupal core. No external modules or libraries are required.

## INSTALLATION
`composer require drupal/simple_paypal_field` or any other standard way of
installing a drupal module.

## CONFIGURATION
First, you need to fill up the credentials and any other required data for each
payment gateway you wish to use.
After that, add a `Simple PayPal field` field to your entity and set amount of 
money to transfer for each click.

##API

The module dispatches to the Drupal system the 
`\Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents::CREATE_ORDER` event
every time the payment was completed.   
