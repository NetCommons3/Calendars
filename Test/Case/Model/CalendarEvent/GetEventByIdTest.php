<?php
/**
 * CalendarEvent::getEventByIdTest()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowGetTest', 'Workflow.TestSuite');
App::uses('CalendarEventFixture', 'Calendars.Test/Fixture');

/**
 * CalendarEvent::getEventById()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Model\CalendarEvent
 */
class CalendarEventGetEventByIdTest extends WorkflowGetTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.calendars.block_setting_for_calendar',
		'plugin.calendars.calendar',
		'plugin.calendars.calendar_event',
		'plugin.calendars.calendar_event_content',
		'plugin.calendars.calendar_event_share_user',
		'plugin.calendars.calendar_frame_setting',
		'plugin.calendars.calendar_frame_setting_select_room',
		'plugin.calendars.calendar_rrule',
		'plugin.workflow.workflow_comment',
		'plugin.rooms.rooms_language4test',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'calendars';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'CalendarEvent';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getEventById';

/**
 * getEventById()のテスト
 *
 * @param int $id CalendarEventレコードのID
 * @param int $userId ユーザーID
 * @param mix $expect 期待値
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetEventById($id, $userId, $expect) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		$testCurrentData = array(
			'Frame' => array(
				'key' => 'frame_3',
				'room_id' => '2',
				'language_id' => 2,
				'plugin_key' => 'calendars',
				),
			'Language' => array(
				'id' => 1,
				),
			'Room' => array(
				'id' => '2',
				),
			'User' => array(
				'id' => $userId,
				),
			'Permission' => array(
				),
			);
		Current::$current = Hash::merge(Current::$current, $testCurrentData);

		// カレンダー権限設定情報確保
		$testRoomInfos = array(
			'roomInfos' => array(
				'2' => array(
					'role_key' => 'room_administrator',
					'use_workflow' => '',
					'content_publishable_value' => 0,
					'content_editable_value' => 0,
					'content_creatable_value' => 1,
				),
			),
		);
		CalendarPermissiveRooms::$roomPermRoles = Hash::merge(CalendarPermissiveRooms::$roomPermRoles, $testRoomInfos);

		//テスト実施
		$result = $this->$model->$methodName($id);
		//チェック
		if ($result == array()) {
			$this->assertEqual($result, $expect);
		} else {
			$expect['is_origin'] = true;
			$expect['is_translation'] = false;
			$expect['is_original_copy'] = false;
			$this->assertEqual($result['CalendarEvent'], $expect);
		}
	}

/**
 * GetのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return void
 */
	public function dataProviderGet() {
		$expectNotExist = array();
		$expectExist = (new CalendarEventFixture())->records[0];
		$expectExist2 = Hash::merge($expectExist,
			array('pseudo_friend_share_plan' => true, 'is_share' => false));
		$expectExist3 = Hash::merge($expectExist,
			array('pseudo_friend_share_plan' => false, 'is_share' => false));
		$expectExist4 = Hash::merge($expectExist,
			array('pseudo_friend_share_plan' => false, 'is_share' => true));

		return array(
			array(999, 1, $expectNotExist), //存在しない
			array(0, 1, $expectNotExist), //存在しない
			array(1, 1, $expectExist2), //存在する(userId = 1)
			array(1, 0, $expectExist3), //存在する(userId = 0)
			array(1, 2, $expectExist4), //存在する(userId = 0)

		);
	}

}
