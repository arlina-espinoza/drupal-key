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

Key includes two key types:

*   **Authentication:** A generic key type to use for a password or API key that does not belong to any other defined key type. This is the default.
*   **AES Encryption:** Can be used for encrypting and decrypting data with the Advanced Encryption Standard (AES) cipher. This key type has a field for the user to select a key size, from the three sizes supported by AES. The key size is validated based on the selection.

Key types are native Drupal 8 plugins (CTools plugins in Key for Drupal 7), so new types can easily be defined.

## Key providers

A key provider is the means by which the key value is stored and/or provided when needed. Key providers are responsible for the following:

*   **Getting Value:** The key provider retrieves the key value.
*   **Value Input:** A key provider can indicate that it can accept a key value for setting.
*   **Setting Value:** If a key value is submitted, the key provider sets it.
*   **Obscuring Value:** A key provider can perform alterations to the key value in order to obscure it when editing.
*   **Deleting Value:** If a key is deleted or the key provider is changed, the provider can delete the key value.

Key includes two key providers:

*   **Configuration:** Stores the key in Drupal configuration settings. The key value can be set, edited, and viewed through the administrative interface, making it useful during site development. However, for better security on production websites, keys should not be stored in configuration. Keys using the Configuration provider are not obscured when editing, making it even more important that this provider not be used in a production environment.
*   **File:** Stores the key in a file, which can be anywhere in the file system, as long as it's readable by the user that runs the web server. Storing the key in a file outside of the web root is generally more secure than storing it in the database.

Both the Configuration and File provider plugins support storing the key with Base64 encoding.

Key providers are native Drupal 8 plugins (CTools plugins in Key for Drupal 7), so new providers can easily be defined.

## Key input

When adding or editing a key, if the selected key provider accepts a key value, a key input is automatically selected, as defined by the key type. Key inputs are responsible for the following:

*   **Value Input:** The key input defines the field (or fields) used to enter the key value.
*   **Processing Value:** The key input processes the submitted value to prepare it, before it can be set by the key provider. The key value is also processed when a key is being edited, in order to prepare it for presentation to the user.

Key includes three key inputs:

*   **None:** This input is used by default when the selected key provider does not accept a key value. The File key provider uses this input.
*   **Text Field:** This input provides a basic text field for submitting a key value. The Configuration key provider uses this input.
*   **Textarea Field:** This input is the same as the text field input, except it uses a textarea HTML element, so it's useful for longer keys, such as SSH keys.

The Text Field and Textarea Field input plugins support the submission of keys that are Base64-encoded.

Key inputs are native Drupal 8 plugins (CTools plugins in Key for Drupal 7), so new inputs can easily be defined.

## Adding a Key Select element to a form

Key adds a Form API element called key_select that behaves like a select element, but that is populated with available keys as options. This allows module developers to easily add a key field to a settings form, like this (in Drupal 7, use `t()`, instead of `$this->t()`):

`$form['secret_key'] = [  
'#type' => 'key_select',  
'#title' => $this->t('Secret key'),  
];`

There are a couple of additional properties that can be used:

*   `#key_filters` An array of filters to apply to the list of keys. Currently, filtering is quite basic, though it will be improved. You can filter on key type and/or key provider. Examples:
    *   `#key_filters = ['type' => 'mailchimp']` This would only display MailChimp keys.
    *   `#key_filters = ['provider' => 'file']` This would only display keys that use the File key provider.
    *   `#key_filters = ['type' => 'mailchimp', 'provider' => 'file']` This would only display MailChimp keys that use the File key provider.
*   <div>`#key_description` This is a boolean value that determines if information about keys is added to the element's description. It is TRUE by default and it prepends the description with the following text (with a link to the add key form):</div>

    <div>

    > Choose an available key. If the desired key is not listed, create a new key.

    </div>

    <div>This can be disabled by setting #key_description to FALSE.</div>

## Using a Key in Drupal 8

Modules can retrieve information about keys or a specific key value by making a call to the Key Manager service. It is best practice to [inject the service](https://www.drupal.org/node/2133171) into your own service, [form](https://www.drupal.org/node/2203931), or [controller](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!DependencyInjection!ContainerInjectionInterface.php/interface/ContainerInjectionInterface/8). The following examples assume the use of the `\Drupal` object for brevity, but the examples can be extrapolated to fit the use case of your module.

### Get all key entities

`Drupal::service('key.repository')->getKeys()`

### Get a single key entity

`Drupal::service('key.repository')->getKey($key_id)`

### Get a key value

`Drupal::service('key.repository')->getKey($key_id)->getKeyValue()`

## Using a Key in Drupal 7

Modules can retrieve information about keys or a specific key value.

### Get all key configurations

`key_get_configs()`

### Get a single key configuration

`key_get_config($key_id)`

### Get a key value

`key_get_key_value($key_id)`