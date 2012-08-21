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
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db		=& JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), '', 'array' );
		$pollid = (int)JRequest::getvar( 'pollid');

		JArrayHelper::toInteger($cid);
		$msg = '';
		
		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			$row =& JTable::getInstance('mfpolldate', 'Table');
			// load the item's data so we'll know with what item were dealing with
			if (!$row->load($cid[$i])) {
    			$msg .= $row->getError();
			}
			
			// FIXME: Check if $pollid == $row->poll_id

			// Decrement vote row - number of 'hits'
			$query = 'UPDATE #__mfpoll_data AS d'
			. ' SET d.hits=d.hits-1'
			. ' WHERE d.pollid = '.(int) $row->poll_id.' AND d.id='.$row->vote_id
			;
			$db->setQuery( $query );
			if ( !$db->query() ) {
				$msg .= $db->getErrorMsg();
			}
			
			// Decrement poll row - number of 'voters'
			unset($query);
			$query = 'UPDATE #__mfpolls AS p'
			. ' SET p.voters=p.voters-1'
			. ' WHERE p.id = '.(int) $row->poll_id
			;
			$db->setQuery( $query );
			if ( !$db->query() ) {
				$msg .= $db->getErrorMsg();
			}

			if (!$row->delete( $cid[$i] ))
			{
				$msg .= $row->getError();
			}
		}
		
		// Get number of voters to get right redirection
		unset($query);
		$query = 'SELECT COUNT(d.id) AS voters '
		. ' FROM #__mfpoll_date AS d'
		. ' WHERE d.poll_id = '.(int) $pollid
		;
		$db->setQuery( $query );
		$count = $db->loadResult();
		
		if($count <= 0) 
		{
			$this->setRedirect( 'index.php?option=com_mfpolls', $msg );
		}else {
			$this->setRedirect( 'index.php?option=com_mfpolls&view=users&cid[]='.$pollid, $msg );
		}
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