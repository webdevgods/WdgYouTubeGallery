<?php
use WdgYouTubeGallery\Form;

return array(
    'factories' => array(
        'wdgyoutubegallery_playlist_add_form' => function(\Zend\Form\FormElementManager $sm){
            $form = new Form\Playlist\Add();
            
            $form->setInputFilter(new \WdgYouTubeGallery\Filter\Playlist\Add());

            return $form;
        },
        'wdgyoutubegallery_playlist_edit_form' => function(\Zend\Form\FormElementManager $sm){
            $form = new Form\Playlist\Edit();
            
            $form->setInputFilter(new \WdgYouTubeGallery\Filter\Playlist\Edit());

            return $form;
        },
        'wdgyoutubegallery_playlist_add_video_form' => function(\Zend\Form\FormElementManager $sm){
            $form = new Form\Playlist\AddVideo();
            
            $form->setInputFilter(new \WdgYouTubeGallery\Filter\Playlist\AddVideo());

            return $form;
        },
    )
);