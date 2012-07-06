<?php
/**
* Class tha handles with a DB connection.
*
* Connection Class with the SGBD. All other classes in CMPersistence use this
* class to submit their querys to the database. They try to find a connection
* to the database in a global variable named $_CMAPP[db]. The information
* needed to connect to the Database must be passed throut a CMConfig class
* in the app->database section.
*
* @author Maicon Brauwers <maicon@edu.ufgrs.br>
* @package cmdevel
* @subpackage cmpersistence
*/
class CMDBConnection {

  /**
   * A mysqli object that provides a database connection.
   * @var PEAR_DB
   */
    protected $db;


  /**
   * Flag that indicates if the object is connected to the SGBD
   * @var CMRecBuilder
   */
    private $connected=0;

  /**
   * @var string $sgbd_user Usuario do sgbd
   * @var string $sgbd_passwd Senha do usuario
   * @var string $sgbd_host Hosto do banco de dados
   * @var string $sgbd_dbname Nome do banco de dados
   */
    private $sgbd_user;
    private $sgbd_passwd;
    private $sgbd_host;
    private $sgbd_dbname;
    private $inTransaction = false;

  /**
   * Creates an object that represents the SGBD connection.
   *
   * Creates an connection with the database. If a default parameter with the configuration is
   * passed, the configuration is parsed and the object try to auto-connect with the database. If
   * no default parameter is passed, the user must fill the connection data with setConfiguration() and
   * then call the connect function.
   *
   *  @param CMAPPConfig $config Objeto que contem a configuracao do objeto
   */
    function __construct($config_obj="")
    {
        if(!empty($config_obj)) {
            $this->loadConfigFromXML($config_obj);
            $this->connect();
        }
    }
    
    /**
     * Sets the information to a connection with the database.
     * 
     * @param String $host     The hostname of the SGBD.
     * @param String $user     The username to be used in the connection.
     * @param String $passwd   The password to be used in the connection.
     * @param String $database The dabatase name.
     */
    public function setConfiguration($host, $user, $passwd, $database)
    {
        $this->sgbd_user     = $user;
        $this->sgbd_passwd   = $passwd;
        $this->sgbd_host     = $host;
        $this->sgbd_dbname   = $database;
    }


    public function query($pesq) {
        global $_CMDEVEL;
		
        $result = $this->db->query($pesq);
        $this->insert_id = $this->db->insert_id;
        if(!$this->inTransaction) {
        	$this->db->commit();
        }
        
        if(!$result) {
            $err = $this->db->error;
            $_CMDEVEL['last_querys'][] = $pesq." : FAILURE [$err].";
            Throw new CMDBQueryError($pesq,$err,$this->db->errno);
        }
        else {
            if(is_object($result)) {
				//$r = $result->num_rows;
                $r="";
                $rows = "Returned ".$r." rows";
				
            }
            if(!isset($rows)) $rows="";
            $_CMDEVEL['last_querys'][] = $pesq." : SUCESSS $rows";
        }
        
        
        return $result;
    }

    public function getInsertId() 
    {
		return $this->insert_id;
    }

    
    /**
     * Loads the Configuration of a XML defined by CMConfig.
     *
     * @param XML A SimpleXML describind with the connection data in the format defined by CMConfig.
     * @see CMConfig
     */
    private function loadConfigFromXML($conf)
    {
        
        $this->sgbd_user = $conf->app[0]->database[0]->user;
        $this->sgbd_passwd = $conf->app[0]->database[0]->password;
        $this->sgbd_host  = $conf->app[0]->database[0]->host;
        $this->sgbd_dbname = $conf->app[0]->database[0]->name;

    }

    
  /**
   * Retorna a conexao pear
   */
    public function getDB()
    {
        return $this->db;
    }
    
  /**
   * Estabilish a Connection with the dabatase.
   *
   */
    public function connect()
    {
    //try to stabilish an connection with the database
        $this->db = new mysqli($this->sgbd_host,$this->sgbd_user,$this->sgbd_passwd,$this->sgbd_dbname);

        if(mysqli_connect_errno()) {
            throw new CMDBException("Error connecting to database:".mysqli_connect_error());
        }
        else {
        	$this->db->autocommit(TRUE);
            $this->connected = true;
        }
        $this->db->query("SET NAMES 'utf8'");
    }
    
  /**
   * Inicia uma transacao
   * @access public
   */
    public function beginTransaction() {
        $this->db->autocommit(false);
        $this->inTransaction = true;
    }
    
  /**
   * Commit the current transaction.
   */
    public function commit()
    {
        $this->db->commit();
        $this->db->autocommit(true);
        $this->inTransaction = false;
    }

  /**
   * Rollback the current transaction.
   */
    public function rollback()
    {
        $this->db->rollback();
    }

    
    
    
  /**
   * Escapes a string.
   **/
    public function escapeString($str) {
        return $this->db->real_escape_string($str);
    }
    
    
    public function tableName( $name ) {
         // Skip quoted literals                                                                                                                                             
        if ( $name{0} != '`' ) {
            
                                # Standard quoting                                                                                                                                 
            $name = "`$name`";
        }
        return $name;
    }
    



    /**
     * Query whether a given table exists 
     * 
     * @param String $table The table name.
     **/
    public function tableExists( $table ) {
        $table = $this->tableName( $table );
        try {
            $res = $this->query( "SELECT 1 FROM $table LIMIT 1" );
            return true;
        } catch(CMDBQueryError $e) {
            return false;
        }
    }
    
    
    /**                                                                                                                                                                        
     * Determines whether a field exists in a table                                                                                                                            
     * Usually aborts on failure                                                                                                                                               
     * If errors are explicitly ignored, returns NULL on failure                                                                                                               
     **/
    function fieldExists( $table, $field ) {
        $table = $this->tableName( $table );
        $res = $this->query( 'DESCRIBE '.$table );
        if ( !$res ) {
            return false;
        }

        while ( $row = $this->fetchObject( $res ) ) {
            if ( $row->Field == $field ) {
                $found = true;
                break;
            }
        }
        return $found;
    }

}

?>