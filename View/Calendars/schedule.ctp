<?php
/**
 * calendars schedule view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->element('Calendars.scripts');

$camelizeData = NetCommonsAppController::camelizeKeyRecursive(array(
'isCollapsed' => $vars['isCollapsed'],
));
?>
<article
		ng-controller="CalendarSchedule"
		ng-init="initialize(<?php echo h(json_encode($camelizeData, JSON_FORCE_OBJECT)); ?>)">

<?php
	if ($vars['sort'] === 'member') {
		echo $this->element('Calendars.Calendars/schedule_member', array('frameId' => $frameId, 'languageId' => $languageId, 'vars' => $vars));
	} else {
		echo $this->element('Calendars.Calendars/schedule_time', array('frameId' => $frameId, 'languageId' => $languageId, 'vars' => $vars));
	}
?>
</article>