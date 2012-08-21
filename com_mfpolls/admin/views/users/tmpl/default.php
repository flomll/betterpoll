<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<?php
	JToolBarHelper::title(  JText::_( 'Users - ' ) );
	JToolBarHelper::cancel();
?>

<div style="width: 48%; float:left;">
<form action="index.php?option=com_mfpolls" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>
<div id="tablecell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th  class="title">
				<?php echo JHTML::_('grid.sort',   'Username', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="15%" align="center">
				<?php echo JHTML::_('grid.sort',   'Date/Time', 'm.published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo JHTML::_('grid.sort',   'Option', 'm.voters', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo JHTML::_('grid.sort',   'Device', 'numoptions', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',   'ID', 'm.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php /*echo $this->pagination->getListFooter();*/ ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 		= JRoute::_( 'index.php?option=com_mfpolls&view=poll&task=edit&cid[]='. $row->id );

		$checked 	= JHTML::_('grid.checkedout',   $row, $i );
		$published 	= JHTML::_('grid.published', $row, $i );
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php /*echo $this->pagination->getRowOffset( $i );*/ ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Poll' );?>::<?php echo htmlspecialchars($row->voter); ?>">
				<a href="<?php echo $link  ?>">
					<?php echo htmlspecialchars($row->username); ?></a></span>
			</td>
			<td align="center">
				<?php echo $row->date; ?>
			</td>
			<td align="center">
				<?php echo $row->vote_id; ?>
			</td>
			<td align="center">
				<?php 
					if( $row->device == 1) {
						echo JText::_( 'Mobile' );
					} else { 
						echo JText::_( 'PC/Mac' );
					} 
				?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
			$k = 1 - $k;
		}
		?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="option" value="com_mfpolls" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>

<!-- LEFT SIDE TO DISPLAY THE GRAPH -->
<div style="width: 48%; float:right;">

<table class="pollstableborder" cellspacing="0" cellpadding="0" border="0">
<thead>
	<tr>
		<th colspan="3" class="sectiontableheader">
			<img src="<?php echo $this->baseurl; ?>/components/com_mfpolls/assets/poll.png" align="middle" border="0" width="12" height="14" alt="" />
			<?php echo $this->escape($this->poll->title); ?>
		</th>
	</tr>
</thead>
<tbody>
<?php foreach($this->votes as $vote) : ?>
	<tr class="sectiontableentry<?php echo $vote->odd; ?>">
		<td width="100%" colspan="3">
			<?php echo $vote->text; ?>
		</td>
	</tr>
	<tr class="sectiontableentry<?php echo $vote->odd; ?>">
		<td align="right" width="25">
			<strong><?php echo $this->escape($vote->hits); ?></strong>&nbsp;
		</td>
		<td width="30" >
			<?php echo $this->escape($vote->percent); ?>%
		</td>
		<td width="300" >
			<div class="<?php echo $vote->class; ?>" style="height:<?php echo $vote->barheight; ?>px;width:<?php echo $vote->percent; ?>%"></div>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<br />
<table cellspacing="0" cellpadding="0" border="0">
<tbody>
	<tr>
		<td class="smalldark">
			<?php echo JText::_( 'Number of Voters' ); ?>
		</td>
		<td class="smalldark">
			&nbsp;:&nbsp;
			<?php if(isset($this->votes[0])) echo $this->votes[0]->voters; ?>
		</td>
	</tr>
	<tr>
		<td class="smalldark">
			<?php echo JText::_( 'First Vote' ); ?>
		</td>
		<td class="smalldark">
			&nbsp;:&nbsp;
			<?php echo $this->escape($this->first_vote); ?>
		</td>
	</tr>
	<tr>
		<td class="smalldark">
			<?php echo JText::_( 'Last Vote' ); ?>
		</td>
		<td class="smalldark">
			&nbsp;:&nbsp;
			<?php echo $this->escape($this->last_vote); ?>
		</td>
	</tr>
</tbody>
</table>

</div>