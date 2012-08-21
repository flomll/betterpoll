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

/**
* @package		Joomla
* @subpackage	MFPolls
*/
class TableMFPollDate extends JTable
{
	/** @var int Primary key */
	var $id			= 0;
	/** @var string */
	var $date		= '';
	/** @var string */
	var $vote_id	= 0;
	/** @var string */
	var $poll_id	= 0;
	/** @var time */
	var $user_id	= 0;
	/** @var boolean */
	var $device		= 0;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db )
	{
		parent::__construct( '#__mfpoll_date', 'id', $db );
	}

	/**
	 * Binds an array to the object
	 * @param 	array	Named array
	 * @param 	string	Space separated list of fields not to bind
	 * @return	boolean
	 */
	function bind( $array, $ignore='' )
	{
		$result = parent::bind( $array );
		// cast properties
		$this->id	= (int) $this->id;

		return $result;
	}

	/**
	 * Overloaded check function
	 *
	 * @access public
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
// 	function check()
// 	{
// 		// check for valid name
// 		if (trim( $this->title ) == '')
// 		{
// 			$this->setError(JText::_( 'Your Poll must contain a title.' ));
// 			return false;
// 		}
// 		// check for valid lag
// 		$this->lag = intval( $this->lag );
// 		if ($this->lag == 0) {
// 			$this->setError(JText::_( 'Your Poll must have a non-zero lag time.' ));
// 			return false;
// 		}
// 
// 		if(empty($this->alias)) {
// 			$this->alias = $this->title;
// 		}
// 		$this->alias = JFilterOutput::stringURLSafe($this->alias);
// 		if(trim(str_replace('-','',$this->alias)) == '') {
// 			$datenow =& JFactory::getDate();
// 			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
// 		}
// 
// 		return true;
// 	}

	// overloaded delete function
	function delete( $oid=null )
	{
		$k = $this->_tbl_key;
		if ( $oid ) {
			$this->$k = intval( $oid );
		}

		if ( parent::delete( $oid ))
		{
// 			$query = 'DELETE FROM #__mfpoll_data'
// 			. ' WHERE pollid = '.(int) $this->$k
// 			;
// 			$this->_db->setQuery( $query );
// 			if ( !$this->_db->query() ) {
// 				$this->_error .= $this->_db->getErrorMsg() . "\n";
// 			}

			$query = 'DELETE FROM #__mfpoll_date'
			. ' WHERE poll_id = '.(int) $this->$k
			;
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() ) {
				$this->_error .= $this->_db->getErrorMsg() . "\n";
			}

// 			$query = 'DELETE from #__mfpoll_menu'
// 			. ' WHERE pollid = '.(int) $this->$k
// 			;
// 			$this->_db->setQuery( $query );
// 			if ( !$this->_db->query() ) {
// 				$this->_error .= $this->_db->getErrorMsg() . "\n";
// 			}

			return true;
		}

		return false;
	}
}
