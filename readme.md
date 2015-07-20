# Key Module for Drupal 8

This module provides a global key management service that can be invoked via the services interface.

## Architecture

Key leverages the Drupal 8 Plugin API for Key Types. Key Types define an interface to get key contents. Key Types have
their own configuration forms that store key-type specific settings when creating a Key entity. 

Plugins allow for extensibility for customized needs. This allows other modules to create their own types of keys, the
key type settings, and the logic for retrieving the key value.

## Settings

To manage keys, visit `admin/config/system/key`.

## Use of Services

After configuring the service, the service provides the ability to encrypt and decrypt.

### Get All Keys

`Drupal::service('key_manager')->getKeys();`

### Get Single Key

`Drupal::service('key_manager')->getKey($key_id);`

### Get Key Value

`Drupal::service('key_manager')->getKeyValue($key_id);`


