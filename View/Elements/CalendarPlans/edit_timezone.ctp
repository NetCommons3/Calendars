<?php
/**
 * 予定編集（タイムゾーン選択） template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	$tzTbl = CalendarsComponent::getTzTbl();
	$options = [];
	foreach ($tzTbl as $item) {
		$options[$item[2]] = $item[0];
	}
	echo $this->NetCommonsForm->label('CalendarActionPlan.timezone_offset' . Inflector::camelize('timezone'), __d('calendars', 'Time zone'));
	echo $this->NetCommonsForm->select('CalendarActionPlan.timezone_offset', $options, array(
		'value' => Current::read('User.timezone'),	//valueは初期値
		'class' => 'form-control',
		'empty' => false,
		'required' => true,
	));
