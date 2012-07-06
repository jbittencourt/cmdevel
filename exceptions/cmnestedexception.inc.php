<?

/**
 * Implements an exception that is throwed when other exception occurs.
 * 
 * @package cmdevel
 * @subpackage exceptions
 * 
 * @author Juliano Maicon Brauwer <maicon@edu.ufrgs.br>
 **/
 

class CMNestedException extends CMException 
{
	private $rootCause;
	private $thrower;
	
	public function setRootCause(Exception $e, $thrower)
	{
		$this->rootCause = $e;
		$this->thrower   = $thrower;
	}
	
}

?>