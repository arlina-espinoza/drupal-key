<?php

/**
 * @file
 * Plugin definition for the CyberSource SOAP API - CC payment method.
 */

$plugin = array(
  'title' => t('CyberSource SOAP API - CC'),
  'type' => 'commerce_payment',
  'key_fields' => array('transaction_key'),
);