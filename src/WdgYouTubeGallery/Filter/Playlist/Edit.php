<?php
namespace WdgYouTubeGallery\Filter\Playlist;

class Edit extends Base
{
    public function __construct()
    {        
        parent::__construct();
        
        $this->add(array(
            'name' => 'id',
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        ));
    }
}

