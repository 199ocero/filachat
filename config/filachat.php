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
    | Show menu item
    |--------------------------------------------------------------------------
    |
    | This option controls whether this plugin registers a menu item in the
    | sidebar. If disabled, you can manually register a navigation item in a
    | different part of the panel.
    |
    */
    'show_in_menu' => true,

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
    | User Searchable Columns
    |--------------------------------------------------------------------------
    |
    | This option specifies the searchable columns for the user model. This is used
    | to search for users in the chat.
    |
    */
    'user_searchable_columns' => [
        'name',
        'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Chat List Display Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column to be displayed when selecting the user
    | in the chat list.
    |
    */
    'user_chat_list_display_column' => 'name',

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
    | Agent Searchable Columns
    |--------------------------------------------------------------------------
    |
    | This option specifies the searchable columns for the agent model. This is used
    | to search for agents in the chat.
    |
    */
    'agent_searchable_columns' => [
        'name',
        'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | Agent Chat List Display Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column to be displayed when selecting the agent
    | in the chat list.
    |
    */
    'agent_chat_list_display_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Sender Name Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column name for the sender's name. You can
    | customize this if your user model uses a different column name. This also
    | use to search for users in the chat.
    |
    */
    'sender_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Receiver Name Column
    |--------------------------------------------------------------------------
    |
    | This option specifies the column name for the receiver's name. You can
    | customize this if your user model uses a different column name. This also
    | use to search for users in the chat.
    |
    */
    'receiver_name_column' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Upload Files
    |--------------------------------------------------------------------------
    |
    | This option specifies the mime types and the type of disk to be used.
    |
    */
    'disk' => 'public',
    // this configuration is only use if the disk is S3
    's3' => [
        'directory' => 'attachments',
        'visibility' => 'public',
    ],
    // these are the mime types that are allowed and you can remove if you want
    'mime_types' => [
        // audio
        'audio/m4a',
        'audio/wav',
        'audio/mpeg',
        'audio/ogg',
        'audio/aac',
        'audio/flac',
        'audio/midi',

        // images
        'image/png',
        'image/jpeg',
        'image/jpg',
        'image/gif',

        // videos
        'video/mp4',
        'video/avi',
        'video/quicktime',
        'video/webm',
        'video/x-matroska',
        'video/x-flv',
        'video/mpeg',

        // documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/csv',
        'text/plain',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ],
    /*
    | If you want to change the maximum file size above 12mb you need to publish
    | livewire config file and change the value for rules. Example below is from livewire config file.
    | 'rules' => null, // Example: ['file', 'mimes:png,jpg']  | Default: ['required', 'file', 'max:12288'] (12MB)
    */
    'max_file_size' => 12288, // default livewire 12MB converted to kilobytes
    'min_file_size' => 1,
    // this option here is for number of files to be uploaded
    'max_files' => 10,
    'min_files' => 0,

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
    | Navigation Label
    |--------------------------------------------------------------------------
    |
    | This option specifies the navigation label used in the sidebar.
    */
    'navigation_label' => 'FilaChat',

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    |
    | This option specifies the user number of unread message badge in the sidebar.
    */
    'navigation_display_unread_messages_count' => false,

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
    | Navigation Sort
    |--------------------------------------------------------------------------
    |
    | This option specifies the navigation sort used in the chat navigation. You can
    | customize this if you have a different sort order in your application.
    |
    */

    'navigation_sort' => 1,

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
    'timezone' => env('APP_TIMEZONE', 'UTC'),
];
