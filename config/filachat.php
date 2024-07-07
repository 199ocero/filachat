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
];
