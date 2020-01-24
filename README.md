# Entry Password

A [Craft CMS 3.x](https://docs.craftcms.com/v3/) plugin to password-protect individual entries with a simple password completely separate from Craft’s user authentication system. 

## Requirements

This plugin requires Craft 3 or later.

## Installation

Search for `bencarr/entry-password` on the Plugin Store and click “Install”. Or, install with Composer and activate with the Craft CLI:

```bash
$ composer require bencarr/entry-password
$ ./craft install/plugin entry-password
```

## How it Works

* **Custom Field Type** — Add an Entry Password field to one of your entry types to allow content authors to set a password on entries. 
* **Your Templating Logic** — Restrict the display of entry content in your templates and provide a password form when required.
* **Validation Action** — Submit your form to the validation action to verify the provided password.
* **Cookie** — After submitting a valid password, the validation action sets a cookie that persists that validated password.

## Configuration Options

When configuring an Entry Password field, you can:

* **Require Password for Authenticated Users**<br>
Optionally require authenticated Craft admins to also submit the password for entries when viewing the entries on the front-end.
* **Set Cookie Expiration Duration**<br>
Set how long a valid password cookie should persist. Defaults to the browser session. Cookies are always invalidated if the entry’s password changes.
* **Display Field in Sidebar**<br>
When editing an entry, display the password field value in the sidebar of the entries form instead of its normal position within a tab in the field layout. The field still appears in its field layout position when editing an entry in a HUD.

## Templating

You’re completely in control of the experience of submitting the password, and what portions of your template are protected by the password.

A simplified example of an entry template:
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
Whether the visitor needs to provide a password. Will return `false` when the user has already provided the correct password (and has the cookie set on successful validation), or the user is currently logged in as a Craft admin and the Entry Password field is not set to include authenticated users.

**entry.isPasswordProtected** `Boolean`<br>
Whether the entry has a password set. Will return `false` if the Entry Password field is empty on the entry, or the entry type does not have an Entry Password field in its field layout.

**entry.entryPasswordField** `EntryPasswordField|null`<br>
The Entry Password field object, with its label and settings. Will return `null` for entries without an Entry Password field.

**entry.entryPasswordFieldValue** `string|null`<br>
The raw password value from the Entry Password field. Will return `null` for entries without an Entry Password field, and entries with a blank password value. 

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

This plugin should be considered completely unsecure. It is a minimally viable means to obfuscate content, and nothing more. Entry passwords are stored in plain text in the database and are visible in the password field for any content editor able to view the edit form of an entry. 
