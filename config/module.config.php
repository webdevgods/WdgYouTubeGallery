<?php
return array(
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../public',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        'WdgYouTubeGallery' => 'application/layout/layout',
    ),
    'navigation' => array(
        'admin' => array(
            'wdgyoutubegallery' => array(
                'label' => 'Videos',
                'route' => 'zfcadmin/wdg-youtubegallery-admin'
            ),
        ),
    ),
    'wdgyoutubegallery' => array(
        // Public api key. Used for public info and front end
        'apiKey' => '',
        
        // Session namespace for auth token
        'sessionNamespace' => 'wdgyoutube',
        
        // Channel id
        'channelId' => '',
        
        // Flag for which auth type to use. "private" key or "public" key 
        'clientAuthType' => 'public',

        // OAUTH - Client ID
        'clientId' => '',

        // OAUTH - Email address 
        'clientServiceAccountName' => '',

        // If using private key file then put OAUTH local key file real path here
        'clientPrivateKeyPath' => '',

        // Otherwise use OAUTH - Client Secret
        'clientSecret' => ''
    )
);
