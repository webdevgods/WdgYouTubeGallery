<?php
return array(
    'router' => array(
        'routes' => array(
            'wdg-youtubegallery' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/videos',
                    'defaults' => array(
                        'controller' => 'WdgYouTubeGallery\Controller\Gallery',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'video' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/video[/:slug]',
                            'constraints' => array(
                                'slug' => '[a-zA-Z0-9_-]+'
                            ),
                            'defaults' => array(
                                'controller' => 'WdgYouTubeGallery\Controller\Gallery',
                                'action' => 'video'
                            )
                        ),
                    ),
                )
            ),
            'zfcadmin' => array(
                'child_routes' => array(
                    'wdg-youtubegallery-admin' => array(
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => '/videos',
                            'defaults' => array(
                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                'action'     => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'playlist' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/playlist'
                                ),
                                'child_routes' => array(
                                    'show' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'show'
                                            )
                                        ),
                                        'may_terminate' => true,                                        
                                        'priority' => 100,
                                    ),
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'add'
                                            )
                                        ),
                                        'may_terminate' => true,
                                        'priority' => 1000,
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/delete[/:id]',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'delete'
                                            )
                                        ),
                                        'may_terminate' => true,
                                        'priority' => 1000,
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/edit[/:id]',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'edit'
                                            )
                                        ),
                                        'priority' => 1000,
                                        'may_terminate' => true,
                                    ),
                                    'add-video' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/add-video[/:id]',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'add-video'
                                            )
                                        ),
                                        'priority' => 1000,
                                        'may_terminate' => true,
                                    ),
                                    'remove-video' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/remove-video[/:id]',
                                            'defaults' => array(
                                                'controller' => 'WdgYouTubeGallery\Controller\GalleryAdmin',
                                                'action' => 'remove-video'
                                            )
                                        ),
                                        'priority' => 1000,
                                        'may_terminate' => true,
                                    ),
                                ),
                            ),
                        )
                    )
                )
            )
        )
    )
);