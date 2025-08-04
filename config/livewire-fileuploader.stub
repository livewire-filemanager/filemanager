<?php
return [
    /*
    |--------------------------------------------------------------------------
    | ACL Feature
    |--------------------------------------------------------------------------
    |
    | If set to true, file access will be restricted to the users who created the files.
    |
    */
    'acl_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the API endpoints for file management operations.
    |
    */
    'api' => [
        'enabled' => true,
        'prefix' => 'filemanager/v1',
        'middleware' => ['api', 'auth:sanctum'],
        'rate_limit' => '100,1',
        'max_file_size' => 10240,
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'],
        'chunk_size' => 1048576,
    ],

    /*
    |--------------------------------------------------------------------------
    | Folder Configuration
    |--------------------------------------------------------------------------
    |
    | Configure folder creation and management settings.
    |
    */
    'folders' => [
        'max_depth' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Callbacks
    |--------------------------------------------------------------------------
    |
    | Custom callbacks for extending functionality.
    |
    */
    'callbacks' => [
        'before_upload' => null,
        'after_upload' => null,
        'access_check' => null,
    ],
];