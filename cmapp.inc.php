<?
/**
 * @package cmdevel
 * @subpackage cmapp
 **/

include("cmapp/exceptions/cmappexception.inc.php");

$_CMDEVEL["class_register"]['CMEnvSession'] = 'cmapp/cmenvsession.inc.php';
$_CMDEVEL["class_register"]['CMUser'] = 'cmapp/cmuser.inc.php';
$_CMDEVEL["class_register"]['CMConfig'] = 'cmapp/cmappconfig.inc.php';
$_CMDEVEL["class_register"]['CMEnvironment'] = 'cmapp/cmenvironment.inc.php';

//groups manegment
$_CMDEVEL["class_register"]['CMGroupMember'] = 'cmapp/cmgroupmember.inc.php';
$_CMDEVEL["class_register"]['CMGroupMemberJoin'] = 'cmapp/cmgroupmemberjoin.inc.php';
$_CMDEVEL["class_register"]['CMGroup'] = 'cmapp/cmgroup.inc.php';


//aco manegment
$_CMDEVEL["class_register"]['CMACO'] = 'cmapp/cmaco.inc.php';
$_CMDEVEL["class_register"]['CMACLGroup'] = 'cmapp/cmaclgroup.inc.php';
$_CMDEVEL["class_register"]['CMACLUser'] = 'cmapp/cmacluser.inc.php';
$_CMDEVEL["class_register"]['CMACLWorld'] = 'cmapp/cmaclworld.inc.php';
$_CMDEVEL["class_register"]['CMACLAppInterface'] = 'cmapp/cmaclappinterface.inc.php';


?>