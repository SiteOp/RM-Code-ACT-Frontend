<?php
/**
 * @version    CVS: 1.1.0
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$user        = JFactory::getUser();
$user_groups = $user->get('groups');
$userId      = $user->get('id');
$adminUser   = $user->authorise('core.admin', 'com_actj');	

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_act');

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_act'))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}


// Count Stars | Average if !empty Comment
$Avg_Stars = 0; 

if (!empty($this->comment)) 
	{	
		$sum = 0;
		
		foreach ($this->comment as $comment) 
		{
			$sum += $comment['myroutegrade'];
			$Avg_Stars += $comment['stars'];
		}
		
		$Calc_Grade = round(($this->item->settergrade * 2 + $sum)  / (count($this->comment) + 2 ), 0) ;
		$Avg_Stars =  round($Avg_Stars / count($this->comment),0) ;
	}
else 
	{
		$Calc_Grade = $this->item->uiaa;
	}

?>


<?php if($adminUser) : ?>Hallo Admin<?php endif; ?>




	<h1>
		<?php if($adminUser) : ?>
			<?php echo $this->item->id; ?> |
		<?php endif; ?>
		
		<?php echo $this->item->name; ?> 
			
		<?php if ($Avg_Stars > 0) : ?>
		| 	Stars <?php echo $Avg_Stars; ?>
		<?php endif; ?>
	</h1>

<div class="row">
	<div class="span5">
		<div class="item_fields">
			<table class="table">
			
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_CALC_GRADE'); ?></th>
					<td><?php echo $Calc_Grade = str_replace
								(array('10', '11', '12', '13', '14', '15', '16','17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36',),	
								 array('3', '3+', '4-', '4', '4+','5-','5','5+','6-','6','6+','7-','7','7+','8-','8','8+','9-','9','9+','10-','10','10+','11-','11','11+','12-',), 
									   $Calc_Grade); ?></td>
				</tr>
				
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_SETTERGRADE'); ?></th>
					<td><?php echo $this->item->uiaa; ?> || <?php echo $this->item->franzoesisch; ?></td>
				</tr>
				
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_COLOR'); ?></th>
					<td><?php echo $this->item->color . $this->item->rgbcode; ?></td> 
				</tr>
				
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_LINE'); ?></th>
					<td><?php echo $this->item->line; ?> / <?php echo $this->item->inorout; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ACT_LINES_SECTOR'); ?></th>
					<td><?php echo $this->item->lineSectorName; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_SETTER'); ?></th>
					<td><?php echo $this->item->setter; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_CREATEDATE'); ?></th>
					<td><?php echo JHtml::_('date', $this->item->createdate, 'd.m.Y'); ?></td>
				</tr>
				
			<?php if (!empty($this->item->info)) :?>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_INFO'); ?></th>
					<td><?php echo nl2br($this->item->info); ?></td>
				</tr>
			<?php endif; ?>
			
			<?php if($adminUser) : ?>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_INFOADMIN'); ?></th>
					<td><?php echo nl2br($this->item->infoadmin); ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_STATE'); ?></th>
					<td>
					<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_ACT_FORM_LBL_ROUTE_MODIFIED'); ?></th>
					<td><span style="color:red"><?php echo JHtml::_('date', $this->item->modified, 'd.m.Y - H.m');  ?> Uhr</span> TODO</td>
				</tr>
			<?php endif; ?>
			</table>
		</div>

		<?php if($canEdit && $this->item->checked_out == 0): ?>
			<a class="btn" href="<?php echo JRoute::_('index.php?option=com_act&task=route.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_ACT_EDIT_ITEM"); ?></a>
		<?php endif; ?>

		<?php if (JFactory::getUser()->authorise('core.delete','com_act.route.'.$this->item->id)) : ?>
		
			<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
				<?php echo JText::_("COM_ACT_DELETE_ITEM"); ?>
			</a>
			
			<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo JText::_('COM_ACT_DELETE_ITEM'); ?></h3>
				</div>
				<div class="modal-body">
					<p><?php echo JText::sprintf('COM_ACT_DELETE_CONFIRM', $this->item->id); ?></p>
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal">Close</button>
					<a href="<?php echo JRoute::_('index.php?option=com_act&task=route.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
						<?php echo JText::_('COM_ACT_DELETE_ITEM'); ?>
					</a>
				</div>
			</div>

		<?php endif; ?>
	</div>
	
	
<?php if (!empty($this->comment)) : ?>
	
	<div class="span7">
	<h2><?php echo JText::_('COM_ACT_COMMENTS'); ?></h2>
		<table class="table">
			  <thead>
				  <tr>
				  <?php if($adminUser) : ?>
					<th><?php echo JText::_('COM_ACT_ID'); ?></th>
				  <?php endif; ?>
					<th><?php echo JText::_('COM_ACT_STARS'); ?></th>
					<th><?php echo JText::_('COM_ACT_COMMENT'); ?></th>
					<th><?php echo JText::_('COM_ACT_GRAD'); ?></th>
					<th><?php echo JText::_('COM_ACT_USERNAME'); ?></th>
				  </tr>
				</thead>
				<tbody>
					<?php foreach ($this->comment as $comment) : ?>
					<tr> 
					<?php if($adminUser) : ?>	
						<td>
                            <a href="<?php echo JRoute::_('index.php?option=com_act&task=commentform.edit&id=' . $comment['c_id'], false, 2); ?>"><?php echo $comment['c_id']; ?></a>
                        </td>
					<?php endif; ?>
						<td><?php echo $comment['stars']; ?></td>
						<td><?php echo $comment['comment']; ?></td>
						<td><?php echo $comment['uiaa']; ?></td>
						<td><?php echo $comment['user_name']; ?></td>
					</tr>
					<?php //print_R($comment);?>
					<?php endforeach; ?>

		</table>
	</div>
<?php endif; ?>
</div><?php // END div row ?>	


  <?php
$user        = JFactory::getUser();
$user_groups = $user->get('groups');
$userId      = $user->get('id');

$app = JFactory::getApplication();
$user = JFactory::getUser(); 
$menu = $app->getMenu();
$routeId = JFactory::getApplication()->input->get('id');
?>  
    
    
    <br ><?php //echo $comment['comment_User_id']; ?></br >
<br ><?php echo $userId ; ?></br >





	<?php 
	//--POST YOUR FORM DATA HERE-->
	$jinput = JFactory::getApplication()->input;
	?>

	<?php


	$stars = isset($_POST["stars"]) ? trim($_POST["stars"]) : ""; // Bewertung
	$kommentar = isset($_POST["kommentar"]) ? trim($_POST["kommentar"]) : ""; // Kommentar
	$review_yn = isset($_POST["review_yn"]) ? trim($_POST["review_yn"]) : ""; // review_yn
    ?>
    

	<?php $Formular = "  
  
	<form id='my_form' action='index.php?option=com_act&view=route&id=853&Itemid=110' method='post'>

	<p>
	 <label> JA:
	<br>
	  <input type='text' name='review_yn' value='" . $review_yn . "' size='35'>
	 </label>
	</p>
    
    <p>
	 <label> Bewertung:
	<br>
	  <input type='text' name='stars' value='" . $stars . "' size='35'>
	 </label>
	</p>

	<p>
	 <label> Kommentar:
	<br>
	  <input type='text' name='kommentar' value='" . $kommentar . "' size='35' maxlength='255' required>
	 </label>
	</p>

	<p>
	 <br>
	 <input type='submit' name='submit' value='Formular absenden'>
	</p>
	</form>
	";

	// Formular abgesendet
	if (isset($_POST["submit"]))
		{

		 // Create and populate an object.
		$comment = new stdClass();
		$comment->id = NULL; 
		$comment->route = $routeId;
		$comment->state = 1;
		$comment->comment = $jinput->post->get('kommentar', 'default_value', 'STRING');
		$comment->review_yn = $jinput->post->get('review_yn', '0', 'INTEGER');
		$comment->stars = $jinput->post->get('stars', '0', 'INTEGER');
		$comment->created_by = $user->id;


		// Insert the object into the user comment table.
		$result = JFactory::getDbo()->insertObject('#__act_comment', $comment,  'id');
        echo '<meta http-equiv="refresh" content="1; URL=https://routes.dav-kletterzentrum-augsburg.de/index.php?option=com_act&view=route&id=853">';
	 
	
		}
		else {

		 // Formular ausgeben
		 echo $Formular;
		}
	?>

