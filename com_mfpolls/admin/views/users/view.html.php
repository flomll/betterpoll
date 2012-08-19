<?php
/**
* @version		$Id: view.html.php 19343 2010-11-03 18:12:02Z ian $
* @package		Joomla
* @subpackage	Config
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Poll component
 *
 * @static
 * @package		Joomla
 * @subpackage	Poll
 * @since 1.0
 */
class MFPollViewUsers extends JView
{
/*	function display( $tpl = null )
	{
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'm.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',		'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( "$option.filter_state",		'filter_state',		'',		'word' );
		$search				= $mainframe->getUserStateFromRequest( "$option.search",			'search',			'',		'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'm.published = 1';
			}
			else if ($filter_state == 'U' )
			{
				$where[] = 'm.published = 0';
			}
		}
		if ($search)
		{
			$where[] = 'LOWER(m.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		// sanitize $filter_order
		if (!in_array($filter_order, array('m.title', 'm.published', 'a.ordering', 'catname', 'm.voters', 'numoptions', 'm.lag', 'm.id'))) {
			$filter_order = 'm.id';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
			$filter_order_Dir = '';
		}

		$orderby 	= ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query = 'SELECT COUNT(m.id)'
		. ' FROM #__mfpolls AS m'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT m.*, u.name AS editor, COUNT(d.id) AS numoptions'
		. ' FROM #__mfpolls AS m'
		. ' LEFT JOIN #__users AS u ON u.id = m.checked_out'
		. ' LEFT JOIN #__mfpoll_data AS d ON d.pollid = m.id AND d.text <> ""'
		. $where
		. ' GROUP BY m.id'
		. $orderby
		;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}*/
	
	function display( $tpl = null )
	{
		global $mainframe, $option;

		$db					=& JFactory::getDBO();
		$pollid = JRequest::getVar('cid');
		$pollid = (int)$pollid[0];
		
		if($pollid < 0)
		{
			echo 'Wrong pollid';
			return;
		}
		
		// TODO: Calculate the number of rows for pageination 
// 		$query = 'SELECT COUNT(m.id)'
// 		. ' FROM #__mfpolls AS m'
// 		. $where
// 		;
// 		$db->setQuery( $query );
// 		$total = $db->loadResult();
// 
// 		jimport('joomla.html.pagination');
// 		$pagination = new JPagination( $total, $limitstart, $limit );
		
		// TODO: Load the user data to display.
		$query = 'SELECT d.*, u.name AS voter, u.email AS email, u.username AS username'
		. ' FROM #__mfpoll_date AS d'
		. ' LEFT JOIN #__users AS u ON u.id = d.user_id'
 		. ' WHERE d.poll_id = '.$pollid
		;
		
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();
		
// 		print_r($rows);

		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		
// --------------------------------------------------------------------------------------
		$first_vote = '';
		$last_vote 	= '';
		$votes		= '';

		// Check if there is a poll corresponding to id and if poll is published
		if ($pollid > 0)
		{
			$query = 'SELECT MIN( date ) AS mindate, MAX( date ) AS maxdate'
				. ' FROM #__mfpoll_date'
				. ' WHERE poll_id = '. (int) $pollid;
			$db->setQuery( $query );
			$dates = $db->loadObject();

			if (isset( $dates->mindate )) {
				$first_vote = JHTML::_('date',  $dates->mindate, JText::_('DATE_FORMAT_LC2') );
				$last_vote 	= JHTML::_('date',  $dates->maxdate, JText::_('DATE_FORMAT_LC2') );
			}

			$query = 'SELECT a.id, a.text, a.hits, b.voters '
				. ' FROM #__mfpoll_data AS a'
				. ' INNER JOIN #__mfpolls AS b ON b.id = a.pollid'
				. ' WHERE a.pollid = '. (int) $pollid
				. ' AND a.text <> ""'
				. ' ORDER BY a.hits DESC';
			$db->setQuery( $query );
			$votes = $db->loadObjectList();
		} else {
			$votes = array();
		}

		// list of polls for dropdown selection
// 		$query = 'SELECT id, title, alias'
// 			. ' FROM #__mfpolls'
// 			. ' WHERE published = 1'
// 			. ' ORDER BY id'
// 		;
// 		$db->setQuery( $query );
// 		$pList = $db->loadObjectList();
// 
// 		foreach ($pList as $k=>$p)
// 		{
// 			$pList[$k]->url = JRoute::_('index.php?option=com_mfpoll&id='.$p->id.':'.$p->alias);
// 		}

// 		array_unshift( $pList, JHTML::_('select.option',  '', JText::_( 'Select Poll from the list' ), 'url', 'title' ));

		// dropdown output
// 		$lists = array();

// 		$lists['polls'] = JHTML::_('select.genericlist',   $pList, 'id',
// 			'class="inputbox" size="1" style="width:200px" onchange="if (this.options[selectedIndex].value != \'\') {document.location.href=this.options[selectedIndex].value}"',
//  			'url', 'title',
//  			JRoute::_('index.php?option=com_mfpoll&id='.$pollid.':'.$poll->alias)
//  			);


		$graphwidth = 200;
		$barheight 	= 4;
		$maxcolors 	= 5;
		$barcolor 	= 0;
		$tabcnt 	= 0;
		$colorx 	= 0;

		$maxval		= isset($votes[0]) ? $votes[0]->hits : 0;
		$sumval		= isset($votes[0]) ? $votes[0]->voters : 0;

		$k = 0;
		for ($i = 0; $i < count( $votes ); $i++)
		{
			$vote =& $votes[$i];

			if ($maxval > 0 && $sumval > 0)
			{
				$vote->width	= ceil( $vote->hits * $graphwidth / $maxval );
				$vote->percent = round( 100 * $vote->hits / $sumval, 1 );
			}
			else
			{
				$vote->width	= 0;
				$vote->percent	= 0;
			}

			$vote->class = '';
			if ($barcolor == 0)
			{
				if ($colorx < $maxcolors) {
					$colorx = ++$colorx;
				} else {
					$colorx = 1;
				}
				$vote->class = "polls_color_".$colorx;
			} else {
				$vote->class = "polls_color_".$barcolor;
			}

			$vote->barheight = $barheight;

			$vote->odd		= $k;
			$vote->count	= $i;
			$k = 1 - $k;
		}

		$this->assign('first_vote',	$first_vote);
		$this->assign('last_vote',	$last_vote);

// 		$this->assignRef('lists',	$lists);
// 		$this->assignRef('params',	$params);
// 		$this->assignRef('poll',	$poll);
		$this->assignRef('votes',	$votes);	
// --------------------------------------------------------------------------------------		

// 		$this->assignRef('user',		JFactory::getUser());
// 		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
// 		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
	}
}
