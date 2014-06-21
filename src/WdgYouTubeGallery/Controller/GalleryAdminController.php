<?php
namespace WdgYouTubeGallery\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use WdgYouTubeGallery\Options\ModuleOptionsInterface as ModuleOptions;

class GalleryAdminController extends AbstractActionController
{
    protected $options;
    
    protected $galleryService;
    
    public function indexAction()
    {
        $service = $this->getGalleryService();
        $youtube = $service->getYouTubeOAuthService();
        
        return new ViewModel(array(
            "youtube" => $youtube,
            "channelId" => $service->getYouTubeChannelId()
        ));
    }
    
    public function showAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $service = $this->getGalleryService();
        
        return new ViewModel(array(
            "youtube" => $service->getYouTubeOAuthService(),
            "playlistId" => $id
        ));
    }
    
    public function addAction()
    {
        $service    = $this->getGalleryService();        
        $form       = $service->getAddPlaylistForm();
        $request    = $this->getRequest();
        
        if($request->isPost())
        {
            $post = $request->getPost();
            
            try 
            {
                $playlist_id = $service->addPlaylistByArray($post->toArray());
                
                $this->flashMessenger()->addSuccessMessage("Added Playlist");

                return $this->redirect()->toRoute("zfcadmin/wdg-youtubegallery-admin/playlist/show", array("id" => $playlist_id));
            }
            catch (\WdgYouTubeGallery\Exception\Service\FormException $exc)
            {
                $this->flashMessenger()->addErrorMessage($exc->getMessage());
            }
            catch (\Exception $exc)
            {
                $this->flashMessenger()->addErrorMessage("Could not add playlist: ".$exc->getMessage());
            }
            
            $form->setData($post)->isValid();   
        }
        
        return new ViewModel(array('form' => $form));
    }
    
    public function editAction()
    {
        $service    = $this->getGalleryService();
        $form       = $service->getEditPlaylistForm($this->getEvent()->getRouteMatch()->getParam("id"));
        $request    = $this->getRequest();
        
        if($request->isPost())
        {
            $post = $request->getPost();
            
            try 
            {
                $playlist_id = $service->EditPlaylistByArray($post->toArray());
                
                $this->flashMessenger()->addSuccessMessage("Edited Playlist");

                return $this->redirect()->toRoute("zfcadmin/wdg-youtubegallery-admin/playlist/show", array("id" => $playlist_id));
            }
            catch (\WdgYouTubeGallery\Exception\Service\FormException $exc)
            {
                $this->flashMessenger()->addErrorMessage($exc->getMessage());
            }
            catch (\Exception $exc)
            {
                $this->flashMessenger()->addErrorMessage("Could not edit playlist: ".$exc->getMessage());
            }
            
            $form->setData($post)->isValid();   
        }
        
        return new ViewModel(array("form" => $form));
    }
    
    public function deleteAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam("id");
        
        try 
        {
            $this->getGalleryService()->deletePlaylist($id);
            
            $this->flashMessenger()->addSuccessMessage("Playlist Deleted");
        } 
        catch(\Exception $exc) 
        {
            $this->flashMessenger()->addErrorMessage($exc->getMessage());
        }
        
        return $this->redirect()->toRoute("zfcadmin/wdg-youtubegallery-admin");
    }
    
    public function addVideoAction()
    {
        ini_set('max_execution_time', 1000);
        
        $id         = $this->params()->fromRoute('id', "");
        $service    = $this->getGalleryService();
        $request    = $this->getRequest();
        
        $form = $service->getPlaylistAddVideoForm($id);
        
        if($request->isPost())
        {
            $post = $request->getPost();
            
            try 
            {
                $data = array_merge_recursive(
                    $post->toArray(),          
                    $this->getRequest()->getFiles()->toArray()
                );
                
                $service->addPlaylistVideoByArray($data);
                
                $this->flashMessenger()->addSuccessMessage("Added Video");

                return $this->redirect()->toRoute("zfcadmin/wdg-youtubegallery-admin/playlist/show", array("id" => $id));
            }
            catch (\WdgStore\Exception\Service\Store\FormException $exc)
            {
                $this->flashMessenger()->addErrorMessage($exc->getMessage());
            }
            catch (\Exception $exc)
            {
                $this->flashMessenger()->addErrorMessage("Could not add video: ".$exc->getMessage());
            }
            
            $form->setData($data)->isValid();   
        }
        
        return new ViewModel(array("form" => $form));
    }
    
    public function removeVideoAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam("id");

        try 
        {
            $this->getGalleryService()->deletePlaylistVideo($id);
            
            $this->flashMessenger()->addSuccessMessage("Video Deleted");
        } 
        catch(\Exception $exc) 
        {
            $this->flashMessenger()->addErrorMessage($exc->getMessage());
        }
        
        return $this->redirect()->toRoute("zfcadmin/wdg-youtubegallery-admin");
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
    
    /**
     * @param \WdgYouTubeGallery\Options\ModuleOptionsInterface
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        
        return $this;
    }
    
    /**
     * @return \WdgYouTubeGallery\Options\ModuleOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) 
        {
            $this->setOptions($this->getServiceLocator()->get('wdgyoutubegallery_module_options'));
        }
        
        return $this->options;
    }
}