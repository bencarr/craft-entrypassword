<?php
return [
    'Entry Password' => 'Entry Password',
    'Entry Password plugin loaded' => 'Entry Password plugin loaded',

    // Settings Fields
    'requiredForAuthenticatedUsers.label' => 'Require Password for Authenticated Users',
    'requiredForAuthenticatedUsers.instructions' => 'Require authenticated Craft admins also enter the password to view the entry template.',
    'cookieExpiration.label' => 'Cookie Expiration',
    'cookieExpiration.instructions' => 'How long should the validation cookie last?',
    'displaysInSidebar.label' => 'Move Password Field to Sidebar',
    'displaysInSidebar.instructions' => 'Show the password field in the entry sidebar, rather than its field layout position. The field will still display in its field layout position in the HUD editor.',

    // Persistence Options
    'persistenceOptions.' . (0) => 'Browser Session',
    'persistenceOptions.' . (60 * 60 * 24) => '1 Day',
    'persistenceOptions.' . (60 * 60 * 24 * 7) => '1 Week',
    'persistenceOptions.' . (60 * 60 * 24 * 30) => '1 Month',
    'persistenceOptions.' . (60 * 60 * 24 * 365) => '1 Year',
    'persistenceOptions.' . (60 * 60 * 24 * 365 * 2) => '2 Years',

    // Bulk Action
    'actions.set.trigger' => 'Set entry password',
    'actions.set.prompt' => 'Enter the new password',
    'actions.set.empty' => 'No entries selected',
    'actions.set.success' => 'Password set on {count}.',
    'actions.set.error' => 'Could not update {count}.',
];
