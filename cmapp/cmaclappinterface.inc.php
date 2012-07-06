<?

interface CMACLAppInterface {

  /**
   * Return the ACO of this tool
   *
   * This function should return the ACO of the tool
   * that intends to Implement an Access Custom List
   *
   * @return object CMACO An CMACO 
   **/
  public function getACO();

  /**
   * Return a list of the privileges implemented by the tool.
   **/
  public function listPrivileges();


  /**
   * Return a list of the human readable names of the privileges
   **/
  public function listPrivilegesMessages();


}

?>