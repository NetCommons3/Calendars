<?php
/**
 * CalendarFrameSettingSelectRoom::getSelectRooms()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <iinfo@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('WorkflowGetTest', 'Workflow.TestSuite');

/**
 * CalendarFrameSettingSelectRoom::getSelectRooms()のテスト
 *
 * @author AllCreator <iinfo@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Model\CalendarFrameSettingSelectRoom
 */
class CalendarFrameSettingSelectRoomGetSelectRoomsTest extends WorkflowGetTest {

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
	protected $_modelName = 'CalendarFrameSettingSelectRoom';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getSelectRooms';

/**
 * getSelectRooms()のテスト
 *
 * @param int $settingId FrameSettingレコードのID
 * @param mix $expect 期待値
 * @dataProvider dataProviderGet
 * @return void
 */
	public function testGetSelectRooms($settingId, $expect) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		if ($expect == array()) {
			//$this->_mockForReturn('Caledars.CalendarsAppModel', 'getReadableRoomIds', 9999, 1);
			//$this->_mockForReturn('CalendarFrameSettingSelectRoom', 'getReadableRoomIds', array(22), 1);
			$mock = $this->getMockForModel('Calendars.CalendarFrameSettingSelectRoom', array('getReadableRoomIds'));
			$this->$model = $mock;
			$mock->expects($this->once())
				->method('getReadableRoomIds')
				->will($this->returnValue(77));
		}

		//テスト実施
		$result = $this->$model->$methodName($settingId);

		//チェック
		$this->assertEqual($result, $expect);
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
		$rooms = array(
			'2' => array(
				'calendar_frame_setting_id' => 1,
				'room_id' => '2',
			),
			//3 => 2,
			//4 => 3,
			'5' => array(
				'calendar_frame_setting_id' => 1,
				'room_id' => '5',
			),
			'6' => array(
				'calendar_frame_setting_id' => null,
				'room_id' => null,
			)
		);
		$errRooms = array(
			'2' => array(
				'calendar_frame_setting_id' => null,
				'room_id' => null,
			),
			//3 => null,
			//4 => null,
			'5' => array(
				'calendar_frame_setting_id' => null,
				'room_id' => null,
			),
			'6' => array(
				'calendar_frame_setting_id' => null,
				'room_id' => null,
			)
		);
		return array(
			array(1, $rooms),
			array(100, $errRooms),
			array(null, $errRooms),
			array(9999, array()), //取得なし
		);
	}

}
