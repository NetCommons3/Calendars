<?php
/**
 * CalendarActionPlan::validate()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsValidateTest', 'NetCommons.TestSuite');
//App::uses('CalendarFrameSettingFixture', 'Calendars.Test/Fixture');
//App::uses('CalendarFrameSettingSelectRoomFixture', 'Calendars.Test/Fixture');
App::uses('CalendarsComponent', 'Calendars.Controller/Component'); //constを使うため

/**
 * CalendarActionPlan::validate()のテスト
 *
 * @author AllCreator <info@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Model\CalendarActionPlan
 */
class CalendarActionPlanValidateTest extends NetCommonsValidateTest {

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
	protected $_modelName = 'CalendarActionPlan';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'validates';

/**
 * テストDataの取得
 *
 * @param string $key key
 * @return array
 */
	private function __getData($key = 'key_1') {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'Frame' => array(
				'id' => $frameId,
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
			),
			'CalendarActionPlan' => array(
				'origin_event_id' => 0,
				'origin_event_key' => '',
				'origin_event_recurrence' => 0,
				'origin_event_exception' => 0,
				'origin_rrule_id' => 0,
				'origin_rrule_key' => '',
				'origin_num_of_event_siblings' => 0,
				'first_sib_event_id' => 0,
				'first_sib_year' => 2016,
				'first_sib_month' => 7,
				'first_sib_day' => 28,
				'easy_start_date' => '',
				'easy_hour_minute_from' => '',
				'easy_hour_minute_to' => '',
				'is_detail' => 1,
				'title_icon' => '',
				'title' => 'test3',
				'enable_time' => 0,
				'detail_start_datetime' => '2016-07-28',
				'detail_end_datetime' => '2016-07-28',
				'is_repeat' => 0,
				'repeat_freq' => 'DAILY',
				'rrule_interval' => array(
					'DAILY' => 1,
					'WEEKLY' => 1,
					'MONTHLY' => 1,
					'YEARLY' => 1,
				),
				'rrule_byday' => array(
					'WEEKLY' => array(
						'0' => 'TH',
					),
					'MONTHLY' => '',
					'YEARLY' => '',
				),
				'rrule_bymonthday' => array(
					'MONTHLY' => '',
				),
				'rrule_bymonth' => array(
					'YEARLY' => array(
						'0' => 7,
					),
				),
				'rrule_term' => 'COUNT',
				'rrule_count' => 3,
				'rrule_until' => '2016-07-28',
				'plan_room_id' => '2',
				'enable_email' => 0,
				'email_send_timing]' => 5,
				'location' => '',
				'contact' => '',
				'description' => '',
				'timezone_offset' => 'Asia/Tokyo',
			),
			'CalendarActionPlanForDisp' => array(
				'detail_start_datetime' => '2016-07-28 11:00',
				'detail_end_datetime' => '2016-07-28',
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);

		return $data;
	}

/**
 * Validatesのテスト
 *
 * @param array $data 登録データ
 * @param string $field フィールド名
 * @param string $value セットする値
 * @param string $message エラーメッセージ
 * @param array $overwrite 上書きするデータ
 * @dataProvider dataProviderValidationError
 * @return void
 */
	public function testValidationError($data, $field, $value, $message, $overwrite = array()) {
		$model = $this->_modelName;

		$testCurrentData = array(
			'Frame' => array(
				'key' => 'frame_3',
				'room_id' => '2',
				'language_id' => 2,
				'plugin_key' => 'calendars',
				),
			'Language' => array(
				'id' => 2
				),
			);
		Current::$current = Hash::merge(Current::$current, $testCurrentData);
		// カレンダー権限設定情報確保
		$testRoomInfos = array(
			'roomInfos' => array(
				'1' => array(
					'role_key' => 'room_administrator',
					'use_workflow' => '',
					'content_publishable_value' => 1,
					'content_editable_value' => 1,
					'content_creatable_value' => 1,
				),
			),
		);
		CalendarPermissiveRooms::$roomPermRoles = Hash::merge(CalendarPermissiveRooms::$roomPermRoles, $testRoomInfos);
		if (is_null($value)) {
			unset($data[$model][$field]);
		} else {
			$data[$model][$field] = $value;
		}
		$data = Hash::merge($data, $overwrite);

		//validate処理実行
		$this->$model->set($data);
		$result = $this->$model->validates();
		$this->assertFalse($result);

		if ($message) {
			$this->assertEquals($this->$model->validationErrors[$field][0], $message);
		}
	}

/**
 * ValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - field フィールド名
 *  - value セットする値
 *  - message エラーメッセージ
 *  - overwrite 上書きするデータ(省略可)
 *
 * @return array テストデータ
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function dataProviderValidationError() {
		$data = $this->__getData();
		$data2 = $data;
		$data2['CalendarActionPlan']['detail_start_datetime'] = '2016-07-28';
		$data2['CalendarActionPlan']['detail_start_datetime'] = '2016-07-28';
		$data3 = $data;
		$data3['CalendarActionPlan']['is_repeat'] = true;
		$data4 = $data;
		$data4['CalendarActionPlan']['detail_start_datetime'] = '2016-07-28 12:12';
		$data4['CalendarActionPlan']['detail_start_datetime'] = '2016-07-28 22:22';
		$data5 = $data;
		$data5['CalendarActionPlan']['is_detail'] = false;
		$data5['CalendarActionPlan']['enable_time'] = true;
		$data5['CalendarActionPlan']['easy_start_date'] = '2017-01-01';
		$data5['CalendarActionPlan']['easy_hour_minute_to'] = '2017-02-02 22:22:00';
		$data5['CalendarActionPlan']['easy_hour_minute_from'] = '2017-02-02 22:23:12';
		$data6 = $data5;
		$data6['CalendarActionPlan']['is_detail'] = true;
		$data7 = $data5;
		$data7['CalendarActionPlan']['enable_time'] = 0;
		$data8 = $data5;
		$data8['CalendarActionPlan']['easy_hour_minute_to'] = '2017-02-02 22:23:20';
		$data8['CalendarActionPlan']['easy_hour_minute_from'] = '2017-02-02 22:23:12';
		$data9 = $data5;
		$data9['CalendarActionPlan']['easy_hour_minute_to'] = '2017-02-02 22:22';
		$data9['CalendarActionPlan']['easy_hour_minute_from'] = '2017-02-02 22:23';
		$data10 = $data5;
		$data10['CalendarActionPlan']['easy_hour_minute_to'] = '2017-01-01 12:22:20';
		$data10['CalendarActionPlan']['easy_hour_minute_from'] = '2017-01-01 12:22:12';
		//週単位
		$data11 = $data;
		$data11['CalendarActionPlan']['is_repeat'] = true;
		$data11['CalendarActionPlan']['repeat_freq'] = 'WEEKLY';
		//月単位
		$data12 = $data;
		$data12['CalendarActionPlan']['is_repeat'] = true;
		$data12['CalendarActionPlan']['rrule_interval']['MONTHLY'] = 1;
		$data12['CalendarActionPlan']['repeat_freq'] = 'MONTHLY';
		$data12['CalendarActionPlan']['rrule_byday']['MONTHLY'] = 1;
		//年単位
		$data13 = $data;
		$data13['CalendarActionPlan']['is_repeat'] = true;
		$data13['CalendarActionPlan']['repeat_freq'] = 'YEARLY';
		$text101 = 'a123456789a123456789a123456789a123456789a123456789a123456789a123456789a123456789a123456789a123456789a';
		$text60000 = ''; //詳細文字制限60000字確認用
		for ($i = 0; $i <= 595; $i++) {
			$text60000 .= $text101;
		}
		return array(
			//beforeValidate
			array('data' => $data, 'field' => 'plan_room_id', 'value' => 'aaa',
				'message' => __d('calendars', 'Invalid input. (authority)')),
			array('data' => $data, 'field' => 'timezone_offset', 'value' => 'aaa',
				'message' => __d('calendars', 'Invalid input. (timezone)')),
			array('data' => $data, 'field' => 'is_detail', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (detail)')),
			array('data' => $data, 'field' => 'location', 'value' => $text101,
				'message' => sprintf(__d('calendars',
						'%d character limited. (location)'), CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN)),
			array('data' => $data, 'field' => 'contact', 'value' => $text101,
				'message' => sprintf(__d('calendars', '%d character limited. (contact)'),
						CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN)),
			array('data' => $data, 'field' => 'description', 'value' => $text60000,
				'message' => sprintf(__d('calendars', '%d character limited. (detail)'),
						CalendarsComponent::CALENDAR_VALIDATOR_TEXTAREA_LEN)),
			//タイトル関連(_doMergeTitleValidat)
			array('data' => $data, 'field' => 'title', 'value' => '',
				'message' => __d('calendars', 'Invalid input. (plan title)')),
			array('data' => $data, 'field' => 'title', 'value' => $text101,
				'message' => sprintf(__d('calendars',
						'%d character limited. (plan title)'), CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN)),
			array('data' => $data, 'field' => 'title_icon', 'value' => $text101 . $text101 . $text101,
				'message' => sprintf(__d('calendars',
						'%d character limited. (title icon)'),
						CalendarsComponent::CALENDAR_VALIDATOR_GENERAL_VCHAR_LEN)),
			//日付時刻関連バリデーション
			array('data' => $data4, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			//---(easy指定)-- pending 本ルートは不要
			array('data' => $data5, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			array('data' => $data6, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			array('data' => $data7, 'field' => 'easy_start_date', 'value' => '0',
				'message' => __d('calendars', 'Invalid input. (year/month/day)')),
			array('data' => $data8, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			array('data' => $data9, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			array('data' => $data10, 'field' => 'enable_time', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (time)')),
			//--(easy end)
			//array('data' => $data, 'field' => 'easy_start_date', 'value' => 'a', //pending不要？
			//	'message' => __d('calendars', 'Invalid input. (time)')),
			//array('data' => $data, 'field' => 'easy_hour_minute_from', 'value' => 'a', //pending不要？
			//	'message' => __d('calendars', 'Invalid input. (start time)(easy edit mode)')),
			//array('data' => $data, 'field' => 'easy_hour_minute_from', 'value' => 'a', //pending不要？
			//	'message' => __d('calendars', 'Invalid input. (start time and end time)(easy edit mode)')),
			//array('data' => $data, 'field' => 'easy_hour_minute_to', 'value' => 'a', //pending不要？
			//	'message' => __d('calendars', 'Invalid input. (end time)')),
			array('data' => $data, 'field' => 'detail_start_datetime', 'value' => '',
				'message' => __d('calendars', 'Invalid input. (start time)')),
			array('data' => $data2, 'field' => 'detail_start_datetime', 'value' => '2016-07-29',
				'message' => __d('calendars', 'Invalid input. (start day (time) and end day (time))')),
			array('data' => $data, 'field' => 'detail_end_datetime', 'value' => 'll',
				'message' => __d('calendars', 'Invalid input. (end date)')),
			//繰返し関連
			array('data' => $data, 'field' => 'edit_rrule', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (change of repetition)')),
			array('data' => $data, 'field' => 'is_repeat', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (repetition)')),
			array('data' => $data11, 'field' => 'is_repeat', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (repetition)')),
			array('data' => $data12, 'field' => 'is_repeat', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (repetition)')),
			//月単位 pending　エラーになる
			//↑Indirect modification of overloaded property CalendarActionPlan::$calendarProofreadValidationErrors has no effect
			//var/www/app/app/Plugin/Calendars/Model/Behavior/CalendarValidateAppBehavior.php:178
			array('data' => $data13, 'field' => 'is_repeat', 'value' => 'a',
				'message' => __d('calendars', 'Invalid input. (repetition)')),
			//array('data' => $data3, 'field' => 'rrule_count', 'value' => '',
			//	'message' => __d('calendars', 'Invalid input. (repetition)')),
			//↑pending errorになる　Indirect modification of overloaded property CalendarActionPlan::$calendarProofreadValidationErrors has no effect
			//var/www/app/app/Plugin/Calendars/Model/Behavior/CalendarPlanRruleValidateBehavior.php:42
			//var/www/app/app/Plugin/Calendars/Model/Behavior/CalendarPlanRruleValidateBehavior.php:102
			//array('data' => $data3, 'field' => 'repeat_freq', 'value' => 'a',
			//	'message' => CalendarsComponent::CALENDAR_RRULE_ERROR_HAPPEND), //pending1 ここは__d定義ではないですがよいでしょうか
			//↑pending errorになる⇒Indirect modification of overloaded property CalendarActionPlan::$calendarProofreadValidationErrors has no effect
			///var/www/app/app/Plugin/Calendars/Model/Behavior/CalendarValidateAppBehavior.php:56
		);
	}

}
