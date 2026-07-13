<?php

return [

    // Your main domain, e.g. "acme.flowdesk.test" has base domain "flowdesk.test".
    'base_domain' => env('TENANCY_BASE_DOMAIN', 'flowdesk.test'),

    // Domains that are NOT a tenant (main site, localhost). Skipped by tenant lookup.
    'central_domains' => [
        env('TENANCY_BASE_DOMAIN', 'flowdesk.test'),
        'localhost',
        '127.0.0.1',
    ],

];
