<?php
/**
 * CalendarFrameSettingSelectRoom::saveCalendarFrameSettingSelectRoom()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <iinfo@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('CalendarFrameSettingFixture', 'Calendars.Test/Fixture');
App::uses('CalendarFrameSettingSelectRoomFixture', 'Calendars.Test/Fixture');

/**
 * CalendarFrameSettingSelectRoom::saveCalendarFrameSettingSelectRoom()のテスト
 *
 * @author AllCreator <iinfo@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Model\CalendarFrameSettingSelectRoom
 */
class CalendarFrameSettingSelectRoomSaveCalendarFrameSettingSelectRoomTest extends NetCommonsSaveTest {

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
	protected $_methodName = 'saveCalendarFrameSettingSelectRoom';

/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		// 登録したい選択ルーム
		$registRooms = array();
		foreach ($data[$this->$model->alias] as $selectRoom) {
			if (empty($selectRoom['room_id'])) {
				continue;
			}
			$registRooms[$selectRoom['room_id']] = $selectRoom['room_id'];
		}

		//テスト実行
		$this->$model->$method($data);

		//チェック用データ取得
		$after = $this->$model->find('all', array(
			'recursive' => -1,
			'conditions' => array('calendar_frame_setting_id' => $data['CalendarFrameSetting']['id']),
		));
		$after = Hash::combine($after, '{n}.CalendarFrameSettingSelectRoom.room_id', '{n}.CalendarFrameSettingSelectRoom.room_id');
		// 確認
		$this->assertEqual($after, $registRooms);
	}

/**
 * Save用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array テストデータ
 */
	public function dataProviderSave() {
		$data['CalendarFrameSetting'] = (new CalendarFrameSettingFixture())->records[0];
		$data['CalendarFrameSetting']['is_select_room'] = '1';

		$data['CalendarFrameSettingSelectRoom'] = array();
		$selectRoomFixture = new CalendarFrameSettingSelectRoomFixture();
		$data['CalendarFrameSettingSelectRoom']['2'] = $selectRoomFixture->records[0];
		$data['CalendarFrameSettingSelectRoom']['2']['room_id'] = '';
		$data['CalendarFrameSettingSelectRoom']['3'] = $selectRoomFixture->records[1];
		$data['CalendarFrameSettingSelectRoom']['4'] = $selectRoomFixture->records[2];
		$data['CalendarFrameSettingSelectRoom']['5'] = $selectRoomFixture->records[3];
		$data['CalendarFrameSettingSelectRoom']['6'] = array(
			'calendar_frame_setting_id' => 1,
			'room_id' => '6'
		);
		$results = array();
		// * 削除の登録処理
		$results[0] = array($data);
		// * 登録処理
		$data['CalendarFrameSettingSelectRoom']['2']['room_id'] = '2';
		$results[1] = array($data);
		$results[1] = Hash::remove($results[1], '2.CalendarFrameSettingSelectRoom.created');
		$results[1] = Hash::remove($results[1], '2.CalendarFrameSettingSelectRoom.created_user');

		return $results;
	}

/**
 * SaveのExceptionError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnExceptionError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Calendars.CalendarFrameSettingSelectRoom', 'save'),
		);
	}

/**
 * SaveのValidationError用DataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド(省略可：デフォルト validates)
 *
 * @return array テストデータ
 */
	public function dataProviderSaveOnValidationError() {
		$data = $this->dataProviderSave()[0][0];

		return array(
			array($data, 'Calendars.CalendarFrameSettingSelectRoom'),
		);
	}

}
