<?php
namespace WdgYouTubeGallery\Options;

interface ModuleOptionsInterface
{
    public function setApiKey($apiKey);
    
    public function setSessionNamespace($sessionNamespace);
    
    public function setChannelId($channelId);
    
    public function setClientId($clientId);
    
    public function setClientAuthType($clientAuthType);
    
    public function setClientServiceAccountName($clientServiceAccountName);
    
    public function setClientSecret($clientSecret);
    
    public function setClientPrivateKeyPath($clientPrivateKeyPath);
    
    public function getApiKey();
    
    public function getSessionNamespace();
    
    public function getChannelId();
    
    public function getClientId();
    
    public function getClientAuthType();
    
    public function getClientServiceAccountName();
    
    public function getClientSecret();
    
    public function getClientPrivateKeyPath();
}
