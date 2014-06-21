<?php
namespace WdgYouTubeGallery\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Gallery extends AbstractHelper
{
    protected $playlists;
    
    /**
     * @param array $playlists
     */
    public function __construct(array $playlists) 
    {
        $this->playlists = $playlists;
    }

    /**
     * __invoke
     *
     * @access public
     * @param \ZfcUser\Entity\UserInterface $user
     * @throws \ZfcUser\Exception\DomainException
     * @return String
     */
    public function __invoke()
    {
        $playlists_array = array(
            "playlists" => array()
        );
        
        foreach($this->playlists as $playlist)
        {
            $playlist_object = (object) array(
                "title" => $playlist->getTitle(),
                "videos" => array()
            );
            
            /* @var $Video \FileBank\File */
            foreach ($playlist->getVideos() as $video)
            {
                $url = $this->getView()->getFileById($video->getId())->getUrl();

                $playlist_object->videos[] = (object) array(
                    "caption" => $video->getName(), 
                    "src" => $url, 
                    "th" => $url
                );
            };
            
            $playlists_array["playlists"][] = $playlist_object;
        }

        ?>
        <div class="entry-content">
            <!-- The HTML -->
            <div id="plusgallery"
                 data-video-path="/plusgallery/videos/plusgallery"
                 data-credit="false"
                 data-type="local"
                 data-video-data='<?php echo json_encode($playlists_array);?>'
                 data-object-path="test"
                 ><!-- +Gallery http://www.plusgallery.net/ -->
            </div>
        </div>

        <!-- Load jQuery ahead of this -->
        <script src="/wdg-video-gallery/plusgallery/js/plusgallery.js"></script>
        <link rel="stylesheet" href="/wdg-video-gallery/plusgallery/css/plusgallery.css">
        <script>
            $(function() {
                //DOM loaded
                $('#plusgallery').plusGallery();
            });
        </script>
        <?php
    }
    
    private function _arrayToObject($d) 
    {
        if (is_array($d)) 
        {
            return (object) array_map( array( $this, __METHOD__ ), $d );
        }
        else 
        {
            return $d;
        }
    }
}