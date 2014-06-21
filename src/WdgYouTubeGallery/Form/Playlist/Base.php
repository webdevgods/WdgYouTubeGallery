<?php
namespace WdgYouTubeGallery\Form\Playlist;

use WdgZf2\Form\PostFormAbstract;

class Base extends PostFormAbstract
{
    public function __construct()
    {
        parent::__construct();

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
    }
}