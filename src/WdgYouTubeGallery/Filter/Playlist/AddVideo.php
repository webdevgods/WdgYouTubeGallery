<?php
namespace WdgYouTubeGallery\Filter\Playlist;

use Zend\InputFilter\InputFilter;

class AddVideo extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty', 
                    'break_chain_on_failure' => true,  
                    'options' => array(
                        'messages' => array(
                            'isEmpty' => 'Title is required'
                        ),
                    )
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 0,
                        'max' => 200,
                        'messages' => array(
                            'stringLengthTooLong' => 'Title is too long. 200 characters maximum'
                        )
                    ),
                )
            ),
        ));
        
        $this->add(array(
            'name' => 'description',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty', 
                    'break_chain_on_failure' => true,  
                    'options' => array(
                        'messages' => array(
                            'isEmpty' => 'Description is required'
                        ),
                    )
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 0,
                        'max' => 200,
                        'messages' => array(
                            'stringLengthTooLong' => 'Description is too long. 200 characters maximum'
                        )
                    ),
                )
            ),
        ));
        
        $this->add(array(
            'name' => 'video',
            'required' => true,
        ));
        
        $this->add(array(
            'name' => 'playlist_id',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            )
        ));
    }
}

