<?php
/**
 * CalendarShareUserEntry Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarAppBehavior', 'Calendars.Model/Behavior');

/**
 * CalendarShareUserEntryBehavior
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Calendars\Model\Behavior
 */
class CalendarShareUserEntryBehavior extends CalendarAppBehavior {

/**
 * Default settings
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2015, NetCommons Project
 */
	protected $_defaults = array(
	);

/**
 * 共有ユーザ情報の登録
 *
 * @param Model &$model 実際のモデル名
 * @param array $shareUsers shareUsers
 * @param int $eventId eventId
 * @return void
 */
	public function insertShareUsers(Model &$model, $shareUsers, $eventId) {
		if (!(isset($this->CalendarEventShareUser))) {
			$model->loadModels(['CalendarEventShareUser' => 'Calendar.CalendarEventShareUser']);
		}

		if (!is_array($shareUsers)) {
			return;
		}

		$func = function ($value) use($eventId) {
			return array(
				'calender_event_id' => $eventId,
				'share_user' => intval($value),
			);
		};

		$shareUserData = array();
		$shareUserData['CalendarEventShareUser'] = array_map($func, $shareUsers);
		$model->CalendarEventShareUser->saveAll($shareUserData['CalendarEventShareUser']);
	}

/**
 * 共有ユーザ情報の登録
 *
 * @param Model &$model 実際のモデル名
 * @param array $shareUsers shareUsers
 * @param int $eventId eventId
 * @return void
 */
	public function updateShareUsers(Model &$model, $shareUsers, $eventId) {
		if (!is_array($shareUsers)) {
			$shareUsers = array();
		}

		if (!(isset($this->CalendarEventShareUser))) {
			$model->loadModels(['CalendarEventShareUser' => 'Calendar.CalendarEventShareUser']);
		}

		$params = array(
			'conditons' => array('CalendarEventShareUser.calender_event_id' => $eventId),
			'recursive' => (-1),
			'order' => array('CalendarEventShareUser.share_user'),
		);
		$oldShareUserDataAry = $model->CalendarEventShareUser->find('all', $params);
		$oldShareUsers = Hash::extract($oldShareUserDataAry, '{n}.CalendarEventShareUser.share_user');

		$shareUsers = sort($shareUsers, SORT_NUMERIC);		//新しい共有ユーザ群
		$oldShareUsers = sort($oldShareUsers, SORT_NUMERIC);	//古い共有ユーザ群

		$insShareUsers = array_diff($oldShareUsers, $shareUsers);	//新しい共有ユーザ群より、追加すべきユーザ群を抽出
		$this->insertShareUsers($model, $insShareUsers, $eventId);

		$delShareUsers = array_diff($shareUsers, $oldShareUsers);	//古い共有ユーザ群より、削除すべきユーザ群を抽出
		$this->deleteShareUsers($model, $delShareUsers, $eventId);

		//新しい共有ユーザ群と、古い共有ユーザ群両方に存在するユーザは、そのままとしておく。
	}

/**
 * 共有ユーザ情報の削除
 *
 * @param Model &$model 実際のモデル名
 * @param array $shareUsers shareUsers
 * @param int $eventId eventId
 * @return void
 * @throws InternalErrorException
 */
	public function deleteShareUsers(Model &$model, $shareUsers, $eventId) {
		if (!(isset($this->CalendarEventShareUser))) {
			$model->loadModels(['CalendarEventShareUser' => 'Calendar.CalendarEventShareUser']);
		}

		$conditions = array(
			'CalendarEventShareUsers.calendar_event_id' => $eventId,
			'CalendarEventShareUsers.share_user' => $shareUsers,	//shareUsersは配列なので IN指定となる。
		);
		if (!$model->CalendarEventShareUser->deleteAll($conditions, false)) {
			//deleteAll失敗
			//throw new InternalErrorException(__d('Calendars', 'delete all error.'));
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
	}
}