<?php
namespace WdgYouTubeGallery\Form\Playlist;

use WdgZf2\Form\PostFormAbstract;

class AddVideo extends PostFormAbstract
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => 'Description',
            ),
        ));
        
        $this->add(array(
            'type' => 'file',
            'name' => 'video',
            'options' => array(
                'label' => 'Video',
            ),
        ));
        
        $this->add(array(
            'type' => 'hidden',
            'name' => 'playlist_id'
        ));
    }
}