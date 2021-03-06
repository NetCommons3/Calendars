<?php
/**
 * calendars block permission setting form template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php if (count($roomTree[$spaceId]) > 0): ?>
	<table class="table">
		<?php echo $this->element('Calendars.CalendarBlockRolePermissions/permission_table_header'); ?>
		<tbody>
			<?php foreach ($roomTree[$spaceId] as $roomId => $roomName): ?>
				<tr>
					<td>
						<?php
						$nest = substr_count($roomName, Room::$treeParser);
						echo str_repeat('&nbsp;', $nest * 4) . h($roomName);
						?>
					</td>
					<?php echo $this->CalendarPermission->getPermissionCells($spaceId, $roomBlocks[$spaceId][$roomId]); ?>
					<?php echo $this->CalendarPermission->getUseWorkflowCells($spaceId, $roomBlocks[$spaceId][$roomId]); ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<?php echo __d('calendars', 'It does not exist yet Room.'); ?>
<?php endif;
