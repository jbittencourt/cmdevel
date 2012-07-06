<?php
/**
 * A representantion of an HTML Document.
 *
 * CMHTMLPage is one of the main classes of CMInterface. It's used to create HTML Documents that can be printed
 * to the default output or saved as files. The main advantage of using this aproache instead simply printing
 * the code to the browser is to use objects of the CMHTMLObj class to build custom widgets and UIs.
 *
 * @author Juliano Bittencourt <juliano@edu.ufrgs.br>
 * @access public
 * @version 0.5
 * @package cmdevel
 * @subpackage cminterface
 * @see CMHTMLObj
 */
class CMHTMLPage extends CMHTMLObj
{
    //defines the supported encodings
    const ENCODING_UTF8="utf-8";
    const  ENCODING_LATIN1="ISO-8859-1";

    const HTML_DTD_TYPE_STRICT=0;
    const HTML_DTD_TYPE_TRANSITIONAL=1;
    const XHTML_DTD_TYPE_STRICT=2;

    public $title, $bgcolor, $bgimage, $OnLoad, $refreshRate;
    public $m_left, $m_top, $m_width, $m_height;
    public $script, $JSfiles, $styleFiles;
	public $bodyStyle;
    
    protected $rssFeed;

    private $encoding = self::ENCODING_UTF8;
    private $dtdType  = self::XHTML_DTD_TYPE_STRICT;

    function __construct($id="")
    {
        parent::__construct();      
        $this->id = $id;
        $this->requires("cminterface/widgets/javascript/cmdevel.js",self::MEDIA_JS_WRAPPER);
    }


    
    public function setEncoding($encode)
    {
        $this->encoding = $encode;
    }
    
    public function setID($id)
    {
        $this->id = $id;
    }
    public function sendheader()
    {
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
        header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header ("Content-Type: text/html; charset=".$this->encoding);
    }

    public function setTitle($texto)
    {
        $this->title = $texto;
    }
     
    public function setRefreshRate($time)
    {
        $this->refreshRate = $time;
    }
     
    public function setOnLoad($texto)
    {
        $this->OnLoad = $texto;
    }
     
    public function setOnClose($texto)
    {
        $this->OnClose = $texto;
    }
    
    
    /**
     * Sets the link for the corresponding rss feed of the current page.
     * 
     * @var string $link Link to the RSS Feeds
     * @var string $title Title of the RSS Feeds
     */
    public function setRSSFeed($link, $title="")
    {
		$this->rssFeed = array();
        $this->rssFeed['link'] = $link;
        $this->rssFeed['title'] = $title;
    }

    public function addJSFile($line)
    {
        $this->JSfiles[]= $line;
    }
    public function addStyleFile($sf)
    {
        $this->styleFiles[] = $sf;
    }

    public function addStyle($line)
    {
        $this->style[]="\t$line\n";
    }
     
    public function addClassStyle($class, $line)
    {
        $this->style[] = "\t$class { $line }\n";
    }

    public function setIcon($url)
    {
        $this->favicon = $url;
    }

  /**
   * This static function redirect the current page sending an location header to the browser.
   *
   * @param String $url The url that the browser must open.
   **/
    static function redirect($url)
    {
        Header("Location: $url");
    }


    private function getDoctype()
    {
        switch($this->dtdType) {
            default:
            case self::HTML_DTD_TYPE_TRANSITIONAL:
                $doc = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \n \"http://www.w3.org/TR/html4/loose.dtd\">";
                break;
            case self::HTML_DTD_TYPE_STRICT:
                $doc = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">";
                break;
       	    case self::XHTML_DTD_TYPE_STRICT:
                $doc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                break; 
        }
        
        return $doc;
    }

    function __toString()
    {
        global $_CMAPP,  $_CMDEVEL;

        $buffer = array();

        echo  $this->getDoctype();
        echo  "\n<html ";
        if($this->dtdType == self::XHTML_DTD_TYPE_STRICT) echo 'xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br"';
        
        echo ">\n";

        $temp_body = parent::__toString();
    //Head
        
        
        echo "<head>\n";
        if(!empty($this->refreshRate)){
            echo  "<meta http-equiv=\"refresh\" content=".$this->refreshRate." />\n";
        }

        echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=$this->encoding\" />\n";
  
        if(!empty($this->title)) {
            echo "<title>".$this->title."</title>\n";
        }
        
        $req = CMHTMLObj::getPageRequires();

        if(!empty($req)) {
            foreach($req as $item) {
                $type = "";
                switch($item['type']){
                    case CMHTMLObj::MEDIA_CSS_WRAPPER:
                        $this->styleFiles[] = $_CMAPP['media_url']."/mediawrapper.php?type=css&amp;frm_file=".urlencode($item['file']);
                        break;
                    case CMHTMLObj::MEDIA_JS_WRAPPER :
                        $this->JSfiles[] = $_CMAPP['media_url']."/mediawrapper.php?type=js&amp;frm_file=".urlencode($item['file']);
                        break;
					case CMHTMLObj::MEDIA_JS:
						$item['file'] = $_CMAPP['js_url']."/".$item['file'];
                    case CMHTMLObj::MEDIA_CUSTOM_JS:
                        $this->JSfiles[] = $item['file'];
                        break;
                    case CMHTMLObj::MEDIA_CSS:
						 $item['file'] = $_CMAPP['css_url']."/".$item['file'];
					case CMHTMLObj::MEDIA_CUSTOM_CSS:
                        $this->styleFiles[] = $item['file'];
                        break;
                }
            }
        }
        if(!empty($this->styleFiles)) {
            foreach($this->styleFiles as $item) {
                echo "<link rel=\"stylesheet\"  type=\"text/css\" href=\"$item\" />\n";
            };
        };

        if(!empty($this->JSfiles)) {
            foreach($this->JSfiles as $item) {
                echo "\t<script type=\"text/javascript\" charset=\"utf-8\" src=\"$item\"></script>\n";
            };
        }

        
        if(!empty($this->style)) {
            echo "<style type=\"text/css\">\n";
            reset($this->style);
            foreach($this->style as $item){
                echo "$item";
            };
            echo "</style>\n";
        }
         
        

        if(!empty($this->favicon)) {
            echo "<link rel=\"icon\" href=\"$this->favicon\" type=\"image/ico\"/>\n";
            echo "<link rel=\"SHORTCUT ICON\" href=\"$this->favicon\"/>\n";
        }
        
        if(is_array($this->rssFeed)) {
			
            echo '<link rel="alternate" title="';
            if (empty($this->rssFeed['title']))
            	echo $this->title;
            else
            	echo $this->rssFeed['title'];
            echo '" href="'.$this->rssFeed['link'].'" type="application/rss+xml"/>'."\n";
        }
        
        echo "</head>\n";


        if(!empty($this->frameset)) {
            $this->printFrames();
        };
        if(!empty($this->id)) {
            $id = "id=\"$this->id\"";
        } else $id = "";

        echo "<body $id ";

        if(!empty($_CMDEVEL['page']['preloadimages'])) {
            $preload = "CM_preloadImages(";
            foreach($_CMDEVEL['page']['preloadimages'] as $img) {
                $preload.="'$img',";
            }

            $preload[strlen($preload)-1] = ")";
            $preload.=";";
            $this->OnLoad.=$preload;
        }


        if(!empty($this->OnLoad)) {
            echo " onLoad=\"$this->OnLoad\" ";
        }
        if(!empty($this->OnClose)) {
            echo " onUnLoad=\"$this->OnClose\"";
        }

		
        echo 'class="' . $this->bodyStyle .'"';

        echo ">\n";

        if(!empty($_CMDEVEL['page']['pag_begin'])) {
            foreach($_CMDEVEL['page']['pag_begin'] as $item) {
                if(is_string($item)) {
                    echo $item;
                } else {
                    if($item instanceof CMHTMLObj) {
                        echo$item->__toString();
                    };
                };
            }
        }


        echo $temp_body;

        if(!empty($_CMDEVEL['page']['pag_end'])) {
            foreach($_CMDEVEL['page']['pag_end'] as $item) {
                if(is_string($item)) {
                    echo $item;
                } else {
                    if($item instanceof CMHTMLObj) {
                        echo $item->__toString();
                    };
                };
                flush();
            }
        }

        if(!empty($_CMDEVEL['page']['preloadimages'])) {
            $preload = "CM_preloadImages(";
            foreach($_CMDEVEL['page']['preloadimages'] as $img) {
                $preload.="'$img',";
            }

        }    

        echo "\n</body>\n";
        echo "</html>";
        return "";
    }
}


