<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Template Definitions
    |--------------------------------------------------------------------------
    |
    | Define all notification templates here. Each key is a unique slug.
    | The seeder and notification:sync command read this file to populate
    | the database. Once an admin edits a template in the UI, their changes
    | are preserved — sync only creates new templates, never overwrites.
    |
    | Structure:
    |   'slug' => [
    |       'name'        => 'Human-readable name',
    |       'description' => 'What triggers this notification',
    |       'channels'    => ['email', 'in_app', 'sms', 'web_push', 'mobile_push'],
    |       'variables'   => ['var_name' => 'Description for admin UI'],
    |       'defaults'    => [
    |           'email_subject' => 'Subject with {{var_name}} placeholders',
    |           'email_body'    => '<p>HTML body with {{var_name}}</p>',
    |           'sms_body'      => 'Plain text with {{var_name}}',
    |           'in_app_title'  => 'Title with {{var_name}}',
    |           'in_app_body'   => 'Body with {{var_name}}',
    |           'push_title'    => 'Push title with {{var_name}}',
    |           'push_body'     => 'Push body with {{var_name}}',
    |       ],
    |   ],
    |
    | Global variables (always available, no need to define):
    |   {{site_name}}, {{site_url}}, {{current_year}}
    |
    */

    'welcome' => [
        'name' => 'Welcome',
        'description' => 'Sent to new users after registration',
        'channels' => ['email', 'in_app'],
        'variables' => [
            'user_name' => 'The registered user\'s name',
            'login_url' => 'URL to the login page',
        ],
        'defaults' => [
            'email_subject' => 'Welcome to {{site_name}}, {{user_name}}!',
            'email_body' => '<p>Hello {{user_name}},</p><p>Welcome to {{site_name}}! Your account has been created successfully.</p><p>Click the button below to get started.</p>',
            'in_app_title' => 'Welcome, {{user_name}}!',
            'in_app_body' => 'Your account has been created successfully. Start exploring the platform.',
        ],
    ],

    'password-changed' => [
        'name' => 'Password Changed',
        'description' => 'Sent when a user changes their password',
        'channels' => ['email'],
        'variables' => [
            'user_name' => 'The user\'s name',
            'changed_at' => 'Date and time of the change',
        ],
        'defaults' => [
            'email_subject' => 'Your password has been changed',
            'email_body' => '<p>Hi {{user_name}},</p><p>Your password was successfully changed on {{changed_at}}.</p><p>If you did not make this change, please contact support immediately.</p>',
        ],
    ],

];
