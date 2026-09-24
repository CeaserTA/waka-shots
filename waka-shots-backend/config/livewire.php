<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire caps temporary uploads at 12MB by default, which silently blocks
    | the larger portfolio images regardless of the Filament `maxSize()` rule.
    | Raised to 25MB (25600KB) to match the portfolio image upload limit.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:25600'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],

];
