# Key Module for Drupal

Key provides the ability to manage keys, which can be employed by other modules. It gives site administrators the ability to define how and where keys are stored, which allows the option of a high level of security and allows sites to meet regulatory or compliance requirements.

Examples of the types of keys that could be managed with Key are:

*   An API key for connecting to an external service, such as PayPal, MailChimp, Authorize.net, UPS, an SMTP mail server, or Amazon Web Services
*   A key used for encrypting data

## Managing keys

Key provides an administration page where users with the "administer keys" permission can add, edit, and delete keys.

## Using keys

Creating a key will have no effect unless another module makes use of it. That integration would typically present itself to the end user in the form of a select field that lists available keys and allows the user to choose one. This could appear, for instance, on the integrating module's configuration page.

## Key types

A key type can be selected for a key in order to indicate the purpose of the key. Key types are responsible for the following:

*   **Filtering:** A list of keys, in a select field for instance, can be filtered to only include keys of a certain type. (Example: a module that performs encryption could limit a list to only include keys flagged with the "Encryption" key type.)
*   **Validation:** A key type can provide validation on form submission to insure that the key value meets certain requirements. (Example: a module that performs encryption could validate that a key value is the proper length for the encryption algorithm being used.)
*   **Generation:** A key type can provide the ability to generate a key of that type. (Example: a module that performs encryption could generate a random key of the required length.)
*   **Value Input:** A key type can indicate which input method should be used for submitting a key value, if the selected Key Provider accepts a key value.

Key includes one key type:

*   **Basic:** Can be used when no other key type is applicable. This is the default.

Key types are native Drupal 8 plugins, so new types can easily be defined. Key for Drupal 7 does not yet support key types.

## Key providers

A key provider is the means by which the key value is stored and/or provided when needed. Key providers are responsible for the following:

*   **Getting Value:** The key provider retrieves the key value.
*   **Value Input:** A key provider can indicate that it can accept a key value for setting.
*   **Setting Value:** If a key value is submitted, the key provider sets it.
*   **Obscuring Value:** A key provider can perform alterations to the key value in order to obscure it when editing.
*   **Deleting Value:** If a key is deleted or the key provider is changed, the provider can delete the key value.

Key includes two key providers:

*   **Configuration:** Stores the key in Drupal configuration settings. The key value can be set, edited, and viewed through the administrative interface, making it useful during site development. However, for better security on production websites, keys should not be stored in configuration.
*   **File:** Stores the key in a file, which can be anywhere in the file system, as long as it's readable by the user that runs the web server. Storing the key in a file outside of the web root is generally more secure than storing it in the database.

Key providers are native Drupal 8 plugins (CTools plugins in Key for Drupal 7), so new providers can easily be defined.

## Key input

When adding or editing a key, if the selected key provider accepts a key value, a key input is automatically selected, as defined by the key type. Key inputs are responsible for the following:

*   **Value Input:** The key input defines the field (or fields) used to enter the key value.
*   **Processing Value:** The key input processes the submitted value to prepare it, before it can be set by the key provider. The key value is also processed when a key is being edited, in order to prepare it for presentation to the user.

Key includes two key inputs:

*   **None:** This input is used by default when the selected key provider does not accept a key value. The File key provider

*   **Text Field:** This input provides a basic text field for submitting a key value. The Configuration key provider uses this input.

Key inputs are native Drupal 8 plugins, so new inputs can easily be defined. Key for Drupal 7 does not yet support key inputs.

## Using a Key

Modules can retrieve information about keys or a specific key value by making a call to the Key Manager service. It is best practice to [inject the service](https://www.drupal.org/node/2133171) into your own service, [form](https://www.drupal.org/node/2203931), or [controller](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!DependencyInjection!ContainerInjectionInterface.php/interface/ContainerInjectionInterface/8). The following examples assume the use of the `\Drupal` object for brevity, but the examples can be extrapolated to fit the use case of your module.

### Get all key entities

`Drupal::service('key.repository')->getKeys();`

### Get a single key entity

`Drupal::service('key.repository')->getKey($key_id);`

### Get a key value

`Drupal::service('key.repository')->;getKey($key_id)->getKeyValue();`
