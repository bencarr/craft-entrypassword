# Entry Password

A [Craft CMS 3](https://docs.craftcms.com/v3/) plugin to password-protect individual entries with a simple password completely separate from Craft’s user authentication system. 

## Requirements

This plugin requires Craft 3 or later.

## Installation

Search for `bencarr/craft-entrypassword` on the Plugin Store and click “Install”. Or, install with [Composer](https://getcomposer.org) and activate with the Craft CLI:

```bash
$ composer require bencarr/craft-entrypassword
$ ./craft install/plugin entry-password
```

## Setup

1. **Add a field** — Add a field to one of your entry types using the Entry Password field type. This field allows content authors to manage the password for individual entries.
2. **Update your templates** — Use the [template behaviors](#behaviors) to restrict the display of your entry type’s content in your templates and provide a password form when required. 
3. **Set up a password form** — Set your password form to submit to the [validation action](#validating-passwords) to verify the provided password.

### Field Configuration

When creating an Entry Password field, there are a few options you can configure:

* **Require the password for authenticated users** — Force authenticated Craft admins to provide the password when viewing the entries on the front-end.
* **Set cookie persistence** — Set how long before a user needs to re-authenticate after providing a valid password. Defaults to the browser session. Cookies are always invalidated if the entry’s password is changed.
* **Display the password field in the sidebar** — When editing an entry, display the password field value in the sidebar of the entries form instead of its normal position within a tab in the field layout. The field still appears in its field layout position when editing an entry in a HUD.

## Templating

You have complete control of the experience of submitting the password, and what portions of your template are protected by the password.

Simplified example template:
```twig
{% if entry.requiresPassword %}
    {# ...Your Password Form... #}
{% else %}
    <p>Password-protected template content</p>
{% endif %}
```

### Behaviors

There are a few behaviors available on `Entry` objects to surface plugin logic in your templates.

**entry.requiresPassword** `Boolean`<br>
Whether the visitor needs to provide a password. Returns `false` when the user has already provided the correct password (and has the cookie set from successful validation), or the user is currently logged in as a Craft admin, and the Entry Password field is not set to include authenticated users.

**entry.isPasswordProtected** `Boolean`<br>
Whether the entry has a password set. Returns `false` if the Entry Password field is empty on the entry, or the entry type does not have an Entry Password field in its field layout.

**entry.entryPasswordField** `EntryPasswordField|null`<br>
The Entry Password field object, with its label and settings. Returns `null` for entries without an Entry Password field.

**entry.entryPasswordFieldValue** `string|null`<br>
The raw password value from the Entry Password field. Returns `null` for entries without an Entry Password field, and entries with an empty password value. 

## Validating Passwords

Your password form can be validated using the `entry-password/validate` action. This action will validate the provided password and, if correct, set a cookie to persist the validation. 

The action accepts two parameters:

**password** `String`<br>
The password being submitted for validation.

**entryId** `Int` <small>_Optional_</small><br>
The ID of the entry to validate against. Will attempt to find the entry ID from the request path if no entry ID is provided. 

### Example Twig Form
```twig
{% set error = craft.session.flash('error') %}
<form method="post">
    {{ csrfInput() }}
    {{ actionInput('entry-password/validate') }}

    {% if error %}
        <p>{{ error }}</p>
    {% endif %}

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required/>

    <button type="submit">Submit</button>
</form>
```

### Example AJAX Request
```javascript
fetch('/actions/entry-password/validate', {
    headers: {
      'Accept': 'application/json',
    },
    method: "POST", 
    body: JSON.stringify({
      CRAFT_CSRF_TOKEN: '{token}',
      entryId: 1,
      password: 'test'
    })
})
```

## Security

This plugin should be considered completely unsecure. It is a minimally viable means to obfuscate content, and nothing more. Entry passwords are stored in plain text in the database and are visible in the password field for any content editor who can view the entry edit form. 

## Questions

**Can I add a password to multiple entries at the same time?**<br>
Yes. From an entries index, select the entries you want to update, then select “Set entry password” from the action menu.

**Can I view all my entries that have passwords?**<br>
Probably. As long as your Entry Password field is included in the search index, you can use Craft’s [wildcard field search](https://docs.craftcms.com/v3/searching.html) to find entries with a value in your password field. For example, if your password field’s handle is `publicPassword`, a search in the entries index for `publicPassword:*` would return entries with any value in that field.

**How does the persistence work?**<br>
A cookie. After submitting a valid password for an entry, the validate action sets an entry-specific cookie using the expiration duration set in the field settings. This cookie’s name is `entrypassword_{hash}` and the value is a [masked token](https://www.yiiframework.com/doc/api/2.0/yii-base-security#maskToken()-detail) of the valid password. When checking if an entry’s password is required, the plugin will check for the cookie and revalidate its masked password.

**If I change the entry’s password, will users who had the old password still have access?**<br>
No. Since the cookie is re-validated on each request, and the cookie value is a masked version of the submitted password, it won’t match the new password, so the user will be prompted for a password.
