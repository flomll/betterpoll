<?php
/**
* @version		$Id: controller.php 14401 2010-01-26 14:10:00Z louis $
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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Static class to hold controller functions for the Poll component
 *
 * @static
 * @package		Joomla
 * @subpackage	Poll
 * @since		1.5
 */
class MFPollController extends JController
{
	/**
	 * Method to show the search view
	 *
	 * @access	public
	 * @since	1.5
	 */
	function display()
	{
		JRequest::setVar('view','poll'); // force it to be the polls view
		parent::display();
	}

	/**
 	 * Add a vote to an option
 	 */
	function vote()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// FIXME:
		$user =& JFactory::getUser();
		if($user->guest){
			JError::raiseNotice( 100, 'Sorry, this vote is not allowed for unregistered user!' );
			return;
		}

		$db			=& JFactory::getDBO();
		$poll_id	= JRequest::getVar( 'id', 0, '', 'int' );
		$option_id	= JRequest::getVar( 'voteid', 0, 'post', 'int' );

		$poll =& JTable::getInstance('mfpoll','Table');
		if (!$poll->load( $poll_id ) || $poll->published != 1) {
			JError::raiseWarning( 404, JText::_('ALERTNOTAUTH') );
			return;
		}

		// Has the user already voted?
		$query = 'SELECT id FROM #__mfpoll_date AS d WHERE d.poll_id = '.$poll_id .' AND d.user_id = '.$user->id;
		$db->setQuery($query);
		$rows = $db->loadObject();
		
		$voted = false;
		if($rows->id != null || $rows->id != 0)
		{
			$voted = true;
		}

		if ($voted || !$option_id )
		{
			if($voted) {
				echo $msg = JText::_('You already voted for this poll today!');
			}

			if(!$option_id){
				$msg = JText::_('WARNSELECT');
			}
		}
		else
		{
			require_once(JPATH_COMPONENT.DS.'models'.DS.'mfpoll.php');
			$model = new MFPollModelPoll();
			$model->vote( $poll_id, $option_id );

			$msg = JText::_( 'Thanks for your vote!' );
		}

		// set Itemid id for links
		$menu = &JSite::getMenu();
		$items	= $menu->getItems('link', 'index.php?option=com_mfpolls&view=poll');

		$itemid = isset($items[0]) ? '&Itemid='.$items[0]->id : '';

		$this->setRedirect( JRoute::_('index.php?option=com_mfpolls&id='. $poll_id.':'.$poll->alias.$itemid, false), $msg );
	}
}
?>