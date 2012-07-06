<?php

class CMvfsLocal extends CMvfs {


  protected $chroot_dir;
  private $tree;
  private $actualNode;

  public function __construct($locator,$verify=1) {
    parent::__construct($locator);

    if($verify) {
      if(!file_exists($locator)) {
	Throw new CMvfsFileNotFound;
      }

      $this->locator = realpath($this->locator);
    }

     $this->chroot_dir = $locator;


  }


  public function register() {
    mkdir($this->locator);
    if(!file_exists($this->locator)) {
      Throw new CMvfsUnableToRegister;
    }
  }
  

  private function validateDir() {
    $dir = trim($this->locator);
    $dir = realpath($dir);

    $dir.= "/";
         
    $chroot = substr($dir,0,strlen($this->chroot));

    if($chroot!=$this->pathChroot) return false;
    return true;
  }

//   private function listDir($dirname)
   
//     if(empty($dir)) $dir = $this->pathChroot; 
//     if(!($dp = @opendir($dir))) return $lang[upload_denied];
       
//     $retdir = array();
       
//     while($arq = readdir($dp)) {
//       if($arq=='.' || $arq=='..') continue;
//       $fullname = "$dir/$arq";

//       if(is_dir($fullname)) {
// 	$tam  = strlen($this->pathChroot);
// 	$path = substr($fullname,$tam,strlen($fullname)-$tam+1);           
	
// 	$retdir[$arq][nome] = $arq;
// 	$retdir[$arq][rel_nome] = $path;
// 	$retdir[$arq][tipo] = "dir";
// 	$retdir[$arq][filhos] = $this->listadir($fullname);
//       }
//       else {
// 	$retdir[$arq][tipo] = tipoArquivo($fullname);
// 	$retdir[$arq][nome] = $arq;
// 	$retdir[$arq][tamanho] = filesize($fullname);
//       };
          
//     };
       
//     closedir($dp);
       
//     return $retdir;  
//   }
  


// /**
//  * Public functions
//  **/

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