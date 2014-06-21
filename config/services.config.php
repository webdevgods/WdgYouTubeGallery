<?php
namespace WdgYouTubeGallery;

return array(
    'invokables' => array(
        'wdgyoutubegallery_service_gallery' => 'WdgYouTubeGallery\Service\Gallery'
    ),
    'factories' => array(
        'wdgyoutubegallery_module_options' => function ($sm) {
            $config = $sm->get('Config');
            return new Options\ModuleOptions(isset($config['wdgyoutubegallery']) ? $config['wdgyoutubegallery'] : array());
        }
    )
);