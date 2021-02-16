<?php
/**
 * パブリックで未承認の予定を管理者が承認するとログインしていないユーザには表示されない migration
 *
 * @author AllCreator <rika.fujiwara@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * パブリックで未承認の予定を管理者が承認するとログインしていないユーザには表示されない migration
 *
 * @package NetCommons\Calendars\Config\Migration
 */
class BugfixSettingIsActive extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'bugfix_setting_is_active';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'up') {
			/** @var \CalendarEvent $CalendarEvent */
			$CalendarEvent = $this->generateModel('CalendarEvent');

			$update = [
				'CalendarEvent.is_active' => '0',
			];
			$conditions = [
				'CalendarEvent.status' => '1',
				'CalendarEvent.is_active' => '1',
				'CalendarEvent.is_latest' => '0',
			];
			$CalendarEvent->updateAll($update, $conditions);

			$update = [
				'CalendarEvent.is_active' => '1',
			];
			$conditions = [
				'CalendarEvent.status' => '1',
				'CalendarEvent.is_active' => '0',
				'CalendarEvent.is_latest' => '1',
			];
			$CalendarEvent->updateAll($update, $conditions);
		}

		return true;
	}
}
