<?php
namespace WdgYouTubeGallery\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface
{
    const AUTHTYPE_PRIVATE = "private";
    const AUTHTYPE_PUBLIC = "public";
    
    protected $apiKey;
    protected $sessionNamespace;
    protected $channelId;
    protected $clientId;
    protected $clientSecret;
    protected $clientAuthType;
    protected $clientServiceAccountName;
    protected $clientPrivateKeyPath;
    
    public function setApiKey($apiKey) 
    {
        $this->apiKey = $apiKey;
    }
    
    public function setSessionNamespace( $sessionNamespace )
    {
        $this->sessionNamespace = $sessionNamespace;
    }
    
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
    }
    
    public function setClientId( $clientId )
    {
        $this->clientId = $clientId;
    }
    
    public function setClientAuthType( $clientAuthType )
    {
        $this->clientAuthType = $clientAuthType;
    }
    
    public function setClientServiceAccountName( $clientServiceAccountName )
    {
        $this->clientServiceAccountName = $clientServiceAccountName;
    }
    
    public function setClientSecret( $clientSecret )
    {
        $this->clientSecret = $clientSecret;
    }
    
    public function setClientPrivateKeyPath( $clientPrivateKeyPath )
    {
        $this->clientPrivateKeyPath = $clientPrivateKeyPath;
    }
    
    public function getApiKey() 
    {
        return $this->apiKey;
    }
    
    public function getSessionNamespace()
    {
        return $this->sessionNamespace;
    }
    
    public function getChannelId()
    {
        return $this->channelId;
    }
    
    public function getClientId()
    {
        return $this->clientId;
    }
    
    public function getClientAuthType()
    {
        return $this->clientAuthType;
    }
    
    public function getClientServiceAccountName()
    {
        return $this->clientServiceAccountName;
    }    
    
    public function getClientSecret()
    {
        return $this->clientSecret;
    }
    
    public function getClientPrivateKeyPath()
    {
        return $this->clientPrivateKeyPath;
    }
}
