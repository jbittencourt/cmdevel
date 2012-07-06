<?

abstract class CMvfs {

  protected $locator;

  public function __construct($locator) {
    $this->locator = $locator;
  }


  abstract function register();

//   abstract function refresh();
//   abstract function ls();

//   abstract function cd($dir);
//   abstract function pwd();
//   abstract function mkdir($dirname);

//   abstract function upload($file);
//   abstract function touch($filename);

//   abstract function del($filename);


//   abstract function readFile($filename);

//   abstract function saveFile($filename,$data);

}


?>