<?php

	$weeklyLink = NetCommonsUrl::actionUrl(array(
		'controller' => 'calendars',
		'action' => 'index',
		'style' => 'weekly',
		//'tab' => 'timeline',
		'year' => sprintf("%04d", $vars['year']),
		'month' => sprintf("%02d", $vars['month']),
		'day' => $vars['day'],
		'frame_id' => Current::read('Frame.id'),
	));

	$lmonthlyLink = NetCommonsUrl::actionUrl(array(
		'controller' => 'calendars',
		'action' => 'index',
		'style' => 'largemonthly',
		//'tab' => 'list',
		'year' => sprintf("%04d", $vars['year']),
		'month' => sprintf("%02d", $vars['month']),
		'day' => $vars['day'],
		'frame_id' => Current::read('Frame.id'),
	));

	$dailyLink = NetCommonsUrl::actionUrl(array(
		'controller' => 'calendars',
		'action' => 'index',
		'style' => 'daily',
		'tab' => 'list',
		'year' => sprintf("%04d", $vars['year']),
		'month' => sprintf("%02d", $vars['month']),
		'day' => $vars['day'],
		'frame_id' => Current::read('Frame.id'),
	));

?>

<ul role='tablist' class='nav nav-tabs calendar-date-move-tablist'>

<?php if ($active === 'lmonthly'): ?>
	<li class='active'>
		<a href='#' onclidk='return false;'>
  <?php else: ?>
	<li>
		<a href="<?php echo $lmonthlyLink; ?>">
<?php endif; ?>

<?php echo __d('calendars', '月'); ?></a>
</li>

<?php if ($active === 'weekly'): ?>
	<li class='active'>
		<a href='#' onclidk='return false;'>
  <?php else: ?>
	<li>
		<a href="<?php echo $weeklyLink; ?>">
<?php endif; ?>

<?php echo __d('calendars', '週'); ?></a>
</li>

<?php if ($active === 'daily'): ?> 
	<li class='active'>
	<a href='#' onclick='return false;'>
 <?php else: ?>
		<li>
		<a href="<?php echo $dailyLink; ?>">
<?php endif; ?>

<?php echo __d('calendars', '日'); ?></a>
</li>
</ul>
<br>