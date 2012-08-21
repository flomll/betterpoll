<?php
/**
* @version		$Id: poll.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Polls
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
* @package		Joomla
* @subpackage	Polls
*/
class MFPollModelPoll extends JModel
{
	/**
	 * Add vote
	 * @param int The id of the poll
	 * @param int The id of the option selected
	 *
	 * \todo Set the user ID to the poll entry
	 */
	function vote( $poll_id, $option_id )
	{
		// FIXME:
		$user =& JFactory::getUser();
		if($user->guest){
			JError::raiseNotice( 100, 'Sorry, this vote is not allowed for unregistered user!' );
// 			JFactory::getApplication()->enqueueMessage( 'Sorry, this vote is not allowed for unregisterd user!' );
			return; // do not execute the rest of the function.
		}
				
		// Get browser informations
// 		$browser = &JBrowser::getInstance();
// 		echo $browser->getPlatform()." - ";
// 		echo $browser->getBrowser()."-";
// 		echo $browser->getMajor().".";
// 		echo $browser->getMinor();
	
		$ismobile = true;
		$findme = 'mobile';
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (($pos = strpos($useragent, $findme)) === false)
		{
			$ismobile = false;
		} 
		else
		{
			$findmes = array('iphone', 'ipad', 'android');
			$isfound = 'others';
			foreach($findmes as $findme)
			{
				if (($pos = strpos($useragent, $findme)) !== false)
				{
					$isfound = true;
				}
			}
			
			if(strcmp($isfound, 'others'))
			{
				
			}
		}

		
		$db = $this->getDBO();
		$poll_id	= (int) $poll_id;
		$option_id	= (int) $option_id;

		$query = 'UPDATE #__mfpoll_data'
			. ' SET hits = hits + 1'
			. ' WHERE pollid = ' . (int) $poll_id
			. ' AND id = ' . (int) $option_id
			;
		$db->setQuery( $query );
		$db->query();

		$query = 'UPDATE #__mfpolls'
			. ' SET voters = voters + 1'
			. ' WHERE id = ' . (int) $poll_id
			;
		$db->setQuery( $query );
		$db->query();

		$date =& JFactory::getDate();

		$query = 'INSERT INTO #__mfpoll_date'
			. ' SET date = ' . $db->Quote($date->toMySQL())
			. ', vote_id = ' . (int) $option_id
			. ', poll_id = ' . (int) $poll_id
			. ', user_id = ' . (int) $user->id
			. ', device = ' . (int) $ismobile
		;
		$db->setQuery( $query );
		$db->query();
	}
}
