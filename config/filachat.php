<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Roles
    |--------------------------------------------------------------------------
    |
    | This option controls whether roles (user, agent) are used in the chat
    | system. If disabled, all users can chat with each other without role
    | constraints.
    |
    */
    'enable_roles' => true,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | This option specifies the user model used in the chat system. You can
    | customize this if you have a different user model in your application.
    |
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Agent Model
    |--------------------------------------------------------------------------
    |
    | This option specifies the agent model used in the chat system. You can
    | customize this if you have a different agent model in your application.
    |
    */
    'agent_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Sender Name Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column name for the sender's name. You can
    | customize this if your user model uses a different column name.
    |
    */
    'sender_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Receiver Name Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column name for the receiver's name. You can
    | customize this if your user model uses a different column name.
    |
    */
    'receiver_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Route Slug
    |--------------------------------------------------------------------------
    |
    | This option specifies the route slug used in the chat system. You can
    | customize this if you have a different route slug in your application.
    |
    */
    'slug' => 'filachat',

    /*
    |--------------------------------------------------------------------------
    | Navigation Icon
    |--------------------------------------------------------------------------
    |
    | This option specifies the navigation icon used in the chat navigation. You can
    | customize this if you have a different icon in your application.
    |
    */
    'navigation_icon' => 'heroicon-o-chat-bubble-bottom-center',

    /*
    |--------------------------------------------------------------------------
    | Max Content Width
    |--------------------------------------------------------------------------
    |
    | This option specifies the maximum width of the chat page. You can
    | customize this if you have a different width in your application. You can use
    | all enum values from \Filament\Support\Enums\MaxWidth.
    |
    */
    'max_content_width' => \Filament\Support\Enums\MaxWidth::Full,

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | This option specifies the timezone used in the chat system. You can
    | customize this if you have a different timezone in your application. Please
    | see supported timezones here: https://www.php.net/manual/en/timezones.php
    |
    */
    'timezone' => 'UTC',
];
