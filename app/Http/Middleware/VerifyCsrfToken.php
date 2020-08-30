<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'ckupload',
        'private-list',
        'is_pay',
        'connect-phone',
        'verify-phone',
        'entry-google-form',
        'send-simi',
        'send-message-automation',
        'send-image-url-simi',
        'send-image-url',
        'send-message-wassenger-automation',
        'send-message-queue-system',
        
        //wp callback
        'send-message-queue-system-wp-activtemplate',
        'send-message-queue-system-wp-celebfans',
        'send-message-queue-system-wp-activflash',
        'send-message-queue-system-wp-digimaru',
        'send-message-queue-system-wp-ms',
        'send-message-queue-system-wp-michaelsugiharto',
    ];
}
