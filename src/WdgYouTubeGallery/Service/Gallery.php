<?php
namespace WdgYouTubeGallery\Service;

use WdgZf2\Service\ServiceAbstract,
    WdgYouTubeGallery\Options\ModuleOptions,
    WdgYouTubeGallery\Options\ModuleOptionsInterface,
    WdgYouTubeGallery\Exception\Service\FormException as FormException;

class Gallery extends ServiceAbstract
{
    /**
     * @var \WdgYouTubeGallery\Options\ModuleOptionsInterface
     */
    protected $options;
    
    /**
     * @return \Zend\Session\Container
     */
    public function getSession()
    {
        $options = $this->getOptions();
        
        return new \Zend\Session\Container($options->getSessionNamespace());
    }
    
    /**
     * @return string
     */
    public function getYouTubeChannelId()
    {
        return $this->getOptions()->getChannelId();
    }
    
    /**
     * @param \Google_Client|null
     * @return \Google_Service_YouTube
     */
    public function getYouTubeOAuthService(\Google_Client $client = null)
    {
        if($client === null)
            $client = $this->getYouTubeOAuthClient();
        
        return new \Google_Service_YouTube($client);
    }
    
    /**
     * @return \WdgYouTubeGallery\Service\Google_Client
     */
    public function getYouTubeApiKeyClient()
    {
        $client = new \Google_Client();
        $client->setDeveloperKey($this->getOptions()->getApiKey());
        
        return $client;
    }
    
    /**
     * @return \Google_Service_YouTube
     */
    public function getYouTubeApiKeyService()
    {
        return new \Google_Service_YouTube($this->getYouTubeApiKeyClient());
    }
    
    /**
     * @return \Google_Client
     * @throws \Exception
     */
    public function getYouTubeOAuthClient()
    {
        $options                = $this->getOptions();
        $client_id              = $options->getClientId();
        $service_account_name   = $options->getClientServiceAccountName();
        $session                = $this->getSession();        
        $key_file_location      = $options->getClientPrivateKeyPath();
        $client                 = new \Google_Client();
        
        $client->setScopes(array(
            'https://www.googleapis.com/auth/youtube'
            ));
        
        if($this->getOptions()->getClientAuthType() === ModuleOptions::AUTHTYPE_PRIVATE )
        {
            if(
                !$client_id
                || !strlen($service_account_name)
                || !strlen($key_file_location)
            ) 
            {
              throw new \Exception("missing needed info");
            }

            if (isset($session->token)) 
            {
                $client->setAccessToken($session->token);
            }

            $key = file_get_contents($key_file_location);
            $cred = new \Google_Auth_AssertionCredentials(
                $service_account_name,
                array('https://www.googleapis.com/auth/youtube'),
                $key
            );

            $client->setAssertionCredentials($cred);
        
            if($client->getAuth()->isAccessTokenExpired()) 
            {
                $client->getAuth()->refreshTokenWithAssertion($cred);
            }
        }
        else
        {
            $client_secret = $options->getClientSecret();
            
            $client->setAccessType("offline");
            $client->setApplicationName("WDGVIDEOGALLERY");
            if(
                !$client_id
                || !strlen($service_account_name)
                || !strlen($client_secret)
            ) 
            {
              throw new \Exception("missing needed info");
            }
            
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            
            //$protocol = $_SERVER['SERVER_PORT'] == '443' || 
                //(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]) ? "https" : "http";
                
            //Temporarily setting to http until I find a better way
            $protocol = "http";
            
            $redirect = filter_var(
                $protocol . '://' . $_SERVER['HTTP_HOST'] . "/admin/videos",
                FILTER_SANITIZE_URL
            );
            
            $client->setRedirectUri($redirect);
            
            if(isset($_GET['code'])) 
            {
                if (strval($session->state) !== strval($_GET['state'])) {
                    throw new \Exception('The session state did not match.');
                }

                $client->authenticate($_GET['code']);
                
                $session->token = $client->getAccessToken();
                
                header('Location: ' . $redirect);
                
                exit;
            }

            if(isset($session->token)) 
            {
                $client->setAccessToken($session->token);
            }
            
            
            if(!$client->getAccessToken())
            {
                $this->requireAuthorization($client);
            }
            elseif($client->isAccessTokenExpired()) 
            {   
                $NewAccessToken = json_decode($client->getAccessToken());
             
                if(!isset($NewAccessToken->refresh_token))
                    $this->requireAuthorization ($client);
                
                $client->refreshToken($NewAccessToken->refresh_token);
            }
        }
        
        $session->token = $client->getAccessToken();

        return $client;
    }
    
    public function requireAuthorization(\Google_Client $client)
    {
        $session = $this->getSession();
        $state = mt_rand();
                
        $client->setState($state);

        $session->state = $state;

        $authUrl = $client->createAuthUrl();

        echo '<h3>Authorization Required</h3>';
        echo '<p>You need to <a href="'.$authUrl.'">authorize access</a> before proceeding.<p>';
        exit;
    }
    
    /**
     * @return \Zend\Form\Form 
     */
    public function getAddPlaylistForm()
    {
        return $this->getServiceManager()->get('FormElementManager')->get('wdgyoutubegallery_playlist_add_form');
    }
    
    /**
     * @param int $id
     * @return \Zend\Form\Form 
     */
    public function getEditPlaylistForm($id)
    {
        $playlist = $this->getPlaylistById($id);
        
        /* @var $form \Zend\Form\Form */
        $form = $this->getServiceManager()->get('FormElementManager')->get('wdgyoutubegallery_playlist_edit_form');

        $form->get("id")->setValue($playlist->getId());
        $form->get("title")->setValue($playlist->getSnippet()->getTitle());
        $form->get("description")->setValue($playlist->getSnippet()->getDescription());

        return $form;
    }
    
    /**
     * @param string $id
     * @return Form
     */
    public function getPlaylistAddVideoForm($id)
    {
        $playlist = $this->getPlaylistById($id);
        
        /* @var $form \Zend\Form\Form */
        $form = $this->getServiceManager()->get('FormElementManager')->get('wdgyoutubegallery_playlist_add_video_form');
        
        $form->get("playlist_id")->setValue($playlist->getId());
        
        return $form;
    }  
    
    /**
     * @return \Google_Service_YouTube_PlaylistListResponse
     */
    public function getAllPlaylists()
    {
        $youtube = $this->getYouTubeApiKeyService();
        
        /* @var $playlistsResource \Google_Service_YouTube_Playlists_Resource */
        $playlistsResource = $youtube->playlists;
        
        
        /* @var $playlists \Google_Service_YouTube_PlaylistListResponse */
        $playlists = $playlistsResource->listPlaylists("snippet", array(
            "channelId" => $this->getOptions()->getChannelId()
        ));
        
        return $playlists;
    }
    
    /**
     * @param string $id
     * @return \Google_Service_YouTube_Playlist
     * @throws \Exception
     */
    public function getPlaylistById($id)
    {
        $youtube = $this->getYouTubeOAuthService();
        
        /* @var $playlistsResource \Google_Service_YouTube_Playlists_Resource */
        $playlistsResource = $youtube->playlists;

        /* @var $playlists \Google_Service_YouTube_PlaylistListResponse */
        $playlistsResponse = $playlistsResource->listPlaylists('id, snippet', array(
          'id' => $id
        ));

        if(!$playlistsResponse->valid())
            throw new \Exception('Could not get playlist');
        
        /* @var $playlist \Google_Service_YouTube_Playlist */
        return $playlistsResponse->current();
    }
    
    /**
     * @param array $array
     * @return string Inserted playlist id
     * @throws FormException
     */
    public function addPlaylistByArray(array $array)
    {
        $form   = $this->getAddPlaylistForm();

        $form->setData($array);

        if(!$form->isValid())throw new FormException("Form values are invalid");

        $data   = $form->getInputFilter()->getValues();
        
        $youtube = $this->getYouTubeOAuthService();
        
        $playlistSnippet = new \Google_Service_YouTube_PlaylistSnippet();
        
        $playlistSnippet->setChannelId($this->getYouTubeChannelId());
        $playlistSnippet->setTitle($data["title"]);
        $playlistSnippet->setDescription($data["description"]);
        
        $playlistStatus = new \Google_Service_YouTube_PlaylistStatus();
        
        $playlistStatus->setPrivacyStatus("public");
        
        $playlist = new \Google_Service_YouTube_Playlist();
        
        $playlist->setSnippet($playlistSnippet);
        $playlist->setStatus($playlistStatus);
        
        /* @var $playlists \Google_Service_YouTube_Playlists_Resource */
        $playlists = $youtube->playlists;

        $playlistInserted = $playlists->insert("snippet,status", $playlist);

        return $playlistInserted->getId();
    }
    
    /**
     * @param array $array
     * @return string Playlist id
     * @throws FormException
     */
    public function editPlaylistByArray($array)
    {
        $form   = $this->getEditPlaylistForm($array["id"]);

        $form->setData($array);

        if(!$form->isValid())throw new FormException("Form values are invalid");

        $data = $form->getInputFilter()->getValues();

        $playlist = $this->getPlaylistById($data["id"]);

        $playlistSnippet = $playlist->getSnippet();
        
        $playlistSnippet->setTitle($data["title"]);
        $playlistSnippet->setDescription($data["description"]);
        
        $playlist->setSnippet($playlistSnippet);
        
        $youtube = $this->getYouTubeOAuthService();
        
        /* @var $playlists \Google_Service_YouTube_Playlists_Resource */
        $playlists = $youtube->playlists;

        $playlistUpdated = $playlists->update("snippet", $playlist);

        return $playlistUpdated->getId();
    }
    
    public function deletePlaylist($id)
    {
        $youtube = $this->getYouTubeOAuthService();
        $playlist = $this->getPlaylistById($id);
        
        /* @var $playlists \Google_Service_YouTube_Playlists_Resource */
        $playlists = $youtube->playlists;

        $playlists->delete($playlist->getId());
    }
    
    public function deletePlaylistVideo($id)
    {
        $youtube = $this->getYouTubeOAuthService();
        
        /* @var $videos \Google_Service_YouTube_PlayListItems_Resource */
        $videos = $youtube->playlistItems;

        $videos->delete($id);
    }
    
    /**
     * @param array $data
     * @return \FileBank\Entity\File
     * @throws FormException
     */
    public function addPlaylistVideoByArray($data)
    {
        $playlist   = $this->getPlaylistById($data["playlist_id"]);
        $form       = $this->getPlaylistAddVideoForm($data["playlist_id"]);
        $client     = $this->getYouTubeOAuthClient();
        $youtube    = $this->getYouTubeOAuthService($client);

        //Set these manually
        $form->setData($data);

        if(!$form->isValid())
        {
            throw new FormException("Form values are invalid");
        }
        
        // REPLACE this value with the path to the file you are uploading.
        $videoPath = $data["video"]["tmp_name"];

        // Create a snippet with title, description, tags and category ID
        // Create an asset resource and set its snippet metadata and type.
        // This example sets the video's title, description, keyword tags, and
        // video category.
        $snippet = new \Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($data["title"]);
        $snippet->setDescription($data["description"]);

        //todo:set tags
//            $snippet->setTags(array("tag1", "tag2"));

        // Numeric video category. See
        // https://developers.google.com/youtube/v3/docs/videoCategories/list 
        $snippet->setCategoryId("22");

        // Set the video's status to "public". Valid statuses are "public",
        // "private" and "unlisted".
        $status = new \Google_Service_YouTube_VideoStatus();

        $status->privacyStatus = "public";

        // Associate the snippet and status objects with a new video resource.
        $video = new \Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // Specify the size of each chunk of data, in bytes. Set a higher value for
        // reliable connection as fewer chunks lead to faster uploads. Set a lower
        // value for better recovery on less reliable connections.
        $chunkSizeBytes = 1 * 1024 * 1024;

        // Setting the defer flag to true tells the client to return a request which can be called
        // with ->execute(); instead of making the API call immediately.
        $client->setDefer(true);

        // Create a request for the API's videos.insert method to create and upload the video.
        /* @var $insertRequest \Google_Service_YouTube_Videos_Resource */
        $insertRequest = $youtube->videos->insert("status,snippet", $video);

        // Create a MediaFileUpload object for resumable uploads.
        $media = new \Google_Http_MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($videoPath));

        // Read the media file and upload it chunk by chunk.
        $upload_status = false;
        $handle = fopen($videoPath, "rb");

        while (!$upload_status && !feof($handle)) {
          $chunk = fread($handle, $chunkSizeBytes);
          $upload_status = $media->nextChunk($chunk);
        }

        fclose($handle);

        // If you want to make other calls after the file upload, set setDefer back to false
        $client->setDefer(false);

        $resource_id = new \Google_Service_YouTube_ResourceId();            
        $resource_id->setVideoId($upload_status["id"]);
        $resource_id->setKind("youtube#video");

        $playlistItemSnippet = new \Google_Service_YouTube_PlaylistItemSnippet();
        $playlistItemSnippet->setPlaylistId($playlist->getId());
        $playlistItemSnippet->setResourceId($resource_id);            

        $playlistItem = new \Google_Service_YouTube_PlaylistItem();
        $playlistItem->setSnippet($playlistItemSnippet);

        /* @var $playlistItemsResource \Google_Service_YouTube_PlaylistItems_Resource */
        $playlistItemsResource = $youtube->playlistItems;            
        $playlistItemsResource->insert("snippet", $playlistItem);
        
        return $upload_status["id"];
    }
    
    /**
     * @param \WdgYouTubeGallery\Service\ModuleOptionsInterface $options
     */
    public function setOptions( ModuleOptionsInterface $options )
    {
        $this->options = $options;
    }

    /**
     * @return \WdgYouTubeGallery\Options\ModuleOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('wdgyoutubegallery_module_options'));
        }
        
        return $this->options;
    }
}
