<?php
/**
 * @version		$Id: controller.php 15096 2010-02-27 14:16:40Z ian $
 * @package		Joomla
 * @subpackage	Config
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
jimport('joomla.environment.browser');

/**
 * @package		Joomla
 * @subpackage	Config
 */
class MFPollControllerUsers extends JController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		
		$cid	= JRequest::getVar( 'cid', array(), '', 'array' );
		print_r($cid);
	}

	function remove()
	{
		// Check for request forgeries
		echo "DELETE";
		
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), '', 'array' );

		JArrayHelper::toInteger($cid);
		$msg = '';

		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			// Delete entry from user						
// 			$query = 'DELETE FROM #__mfpoll_date'
// 			. ' WHERE poll_id = '.(int) $cid[$i]
// 			;
// 			$db->setQuery( $query );
// 			if ( !$db->query() ) {
// 				echo $db->getErrorMsg() . "\n";
// 			}
// 			
// 			$query = 'UPDATE FROM #__mfpoll_data'
// 			. ' WHERE poll_id = '.(int) $cid[$i]
// 			;
// 			$db->setQuery( $query );
// 			if ( !$db->query() ) {
// 				echo $db->getErrorMsg() . "\n";
// 			}


// 			$poll =& JTable::getInstance('mfpolldate', 'Table');
			
// 			print_r($poll);
// 			if (!$poll->delete( $cid[$i] ))
// 			{
// 				$msg .= $poll->getError();
// 			}
		}
		// TODO: If the last entry is removed you should redirect to default view!!
// 		$this->setRedirect( 'index.php?option=com_mfpolls&view=users', $msg );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$id		= JRequest::getVar( 'id', 0, '', 'int' );
		$db		=& JFactory::getDBO();
		$row	=& JTable::getInstance('mfpoll', 'Table');

		$row->checkin( $id );
		$this->setRedirect( 'index.php?option=com_mfpolls' );
	}
}