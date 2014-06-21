<?php
namespace WdgYouTubeGallery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class GalleryController extends AbstractActionController
{
    /**
     * @var \WdgYouTubeGallery\Service\Gallery
     */
    public $galleryService;
    
    public function indexAction()
    {
        $service = $this->getGalleryService();
        $search = $service->getYouTubeApiKeyService();
        
        return new ViewModel(array('search' => $search, 'channelId' => $service->getYouTubeChannelId()));
    }
    
    /**
     * getGalleryService
     *
     * @return \WdgYouTubeGallery\Service\Gallery
     */
    public function getGalleryService()
    {
        if (null === $this->galleryService)
        {
            $this->galleryService = $this->getServiceLocator()->get('wdgyoutubegallery_service_gallery');
        }
        return $this->galleryService;
    }
}