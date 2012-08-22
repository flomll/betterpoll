<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgMFPollMFPollSystem extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatibility we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgMFPollMFPollSystem( &$subject, $config )
 {
    parent::__construct( $subject, $config );
 }
/**
 * Plugin method with the same name as the event will be called automatically.
 */
 public function onVote()
 {
    $app = &JFactory::getApplication();
    $db =& JFactory::getDBO();
   	$user =& JFactory::getUser();

    $args = func_get_args();
    if(count($args) != 2) {
    	JError::raise(2, 500, JText::_( 'ERROR: argument missmatch.' ) , '', false);
    	$errors ++;
		return false;
    }
    $pollid = (int)$args[0];
    $optionid = (int)$args[1];
    
    // Enter if the user not voted
    if(!$this->voted($pollid, $user->id))
    {   
		// Update the hits on the option
		$query = "UPDATE ".
			" #__mfpoll_data ".
			" SET ".
			" #__mfpoll_data.hits=#__mfpoll_data.hits+1 ".
			" WHERE #__mfpoll_data.id = ".(int)$optionid." AND #__mfpoll_data.pollid = ".(int)$pollid." ;";

		$db->setQuery($query);
		$db->query();
		
		// Error handling for DB
		if( $db->getErrorNum() ){
			JError::raise(2, 500, $db->getErrorMsg(), '', false); $errors ++;
			return false;
		}
	
		// Update the number of hits on the poll self.
		unset($query);
		$query = "UPDATE ".
			" #__mfpolls ".
			" SET ".
			" #__mfpolls.voters=#__mfpolls.voters+1 ".
			" WHERE #__mfpolls.id = ".(int)$pollid." ;";
			
		$db->setQuery($query);
		$db->query();
		
		// Error handling for DB
		if( $db->getErrorNum() ){
			return $db->getErrorMsg();
		}

		// Update the number of hits on the poll self.
		unset($query);
		$query = "INSERT INTO".
			" #__mfpoll_date".
			" (date, poll_id, vote_id, user_id)".
			" VALUES ".
			" (now(),".(int)$pollid.",".(int)$optionid.",".$user->id.")";
	    
		$db->setQuery($query);
		$db->query();
		
		// Error handling for DB
		if( $db->getErrorNum() ){
			return $db->getErrorMsg();
		}
	    // FIXME: Joomla API has problems with multible UPDATE command submit by one 
	    // $db->query(). Workaround is we split have to execute the method $db->query()
	    // for two times to update the tables.
	    return true;
	}	
        // Plugin code goes here.
        // You can access parameters via $this->params.
    return false;
 }
 
 public function onLoadPolls()
 {
 	return true;
 }
 
 /**
  * 
  */
 private
 function voted($pollid, $userid)
 {
	$db =& JFactory::getDBO();

	// Has the user already voted?
	$query = 'SELECT '.
		' id '.
		' FROM #__mfpoll_date AS d '.
		' WHERE '.
		' d.poll_id = '.(int)$pollid .' AND d.user_id = '.(int)$userid;
	$db->setQuery($query);
	$rows = $db->loadObject();
	
	if($rows->id != null || $rows->id != 0)
	{
		return true;
	}
	return false;
 }
}
?>