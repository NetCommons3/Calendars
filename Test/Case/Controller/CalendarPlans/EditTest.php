<?php
/**
 * CalendarPlansController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarPlansController', 'Calendars.Controller');
App::uses('WorkflowControllerEditTest', 'Workflow.TestSuite');
App::uses('CalendarsComponent', 'Calendars.Controller/Component');	//constを使うため

/**
 * CalendarPlansController Test Case
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Controller\CalendarPlansController
 */
class CalendarPlansControllerEditTest extends WorkflowControllerEditTest {

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
		'plugin.holidays.holiday',
		'plugin.holidays.holiday_rrule',
		'plugin.calendars.roles_room4test', //add
		'plugin.calendars.roles_rooms_user4test', //add
		'plugin.user_attributes.user_attribute_layout',
		'plugin.calendars.room4test',
	);

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'calendars';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'calendar_plans';

/**
 * テストDataの取得
 *
 * @param string $originEventId オリジナルEventId
 * @return array
 */
	private function __getData($originEventId = '0') {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		if ($originEventId == 1) {
			$originEventKey = 'calendarplan1';
			$originalRruleId = 1;
		} elseif ($originEventId == 2) {
			$originEventKey = 'calendarplan2';
			$originalRruleId = 2;
		} elseif ($originEventId == 4) {
			$originEventKey = 'calendarplan4';
			$originalRruleId = 4;
		} elseif ($originEventId == 6) {
			$originEventKey = 'calendarplan6';
			$originalRruleId = 6;
		}
		$data = array(
			'save_' . WorkflowComponent::STATUS_PUBLISHED => null,
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
			),
			'CalendarActionPlan' => array(
				'origin_event_id' => $originEventId,
				'origin_event_key' => $originEventKey,
				'origin_event_recurrence' => '0',
				'origin_event_exception' => '0',
				'origin_rrule_id' => $originalRruleId,
				'origin_rrule_key' => $originEventKey,
				'origin_num_of_event_siblings' => '0',
				'is_repeat' => '0',
				'first_sib_event_id' => '0',
				'is_recurrence' => '0',
				'edit_rrule' => '0',
				'first_sib_event_id' => 0,
				'first_sib_year' => '2016',
				'first_sib_month' => '9',
				'first_sib_day' => '4',
				'easy_start_date' => '',
				'easy_hour_minute_from' => '',
				'easy_hour_minute_to' => '',
				'is_detail' => 1,
				'title_icon' => '',
				'title' => 'add',
				'enable_time' => '0',
				'detail_start_datetime' => '2016-09-04',
				'detail_end_datetime' => '2016-09-04',
				'is_repeat' => 0,
				'repeat_freq' => 'DAILY',
				'rrule_interval' => array(
					'DAILY' => '1',
					'WEEKLY' => '1',
					'MONTHLY' => '1',
					'YEARLY' => '1',
					),
				'rrule_byday' => array(
					'WEEKLY' => array(
						'0' => 'SU',
					),
					'MONTHLY' => '',
					'YEARLY' => '',
				),
				'rrule_byday' => array(
					'WEEKLY' => array(
						'0' => 'SU'
					),
					'MONTHLY' => 0,
					'YEARLY' => 0,
				),
				'rrule_bymonthday' => array(
					'MONTHLY' => '',
						'rrule_bymonth' => array(
							'YEARLY' => array(
								'0' => 9,
							),
					),
				),
				'rrule_term' => 'COUNT',
				'rrule_count' => '3',
				'rrule_until' => '2016-09-04',
				'plan_room_id' => '1',
				'enable_email' => '',
				'email_send_timing' => '5',
				'location' => '',
				'contact' => '',
				'description' => '',
				'timezone_offset' => 'Asia/Tokyo',
			),
			'WorkflowComment' => array(
				'comment' => 'WorkflowComment save test'
			),
		);
		return $data;
	}

/**
 * editアクションのGETテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGet() {
		$data = $this->__getData(1);
		$results = array();

		//ログインなし
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['CalendarActionPlan']['origin_event_key']),
			'assert' => null, 'exception' => 'ForbiddenException'
		);
		return $results;
	}

/**
 * editアクションのGETテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByCreatable() {
		$data = $this->__getData(1);
		$results = array();

		//作成権限のみ
		//--他人の記事
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan1'),
			'assert' => null,
			//'exception' => 'BadRequestException',
			'exception' => 'ForbiddenException',
		);
		$results[1] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan4'),
			'assert' => null,
			//'exception' => 'BadRequestException',
			'exception' => 'ForbiddenException',
		);
		//--自分の記事
		$results[2] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan2'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		$results[3] = Hash::merge($results[2], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		));
		$results[4] = Hash::merge($results[2], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		));
		$results[5] = Hash::merge($results[2], array(
			//'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => '_method', 'value' => 'DELETE'),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		));
		//フレームID指定なしテスト
		$results[6] = Hash::merge($results[2], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'calendarplan2'),
			'assert' => array('method' => 'assertNotEmpty'),
		));
		return $results;
	}

/**
 * editアクションのGETテスト(編集権限、公開権限なし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByEditable() {
		$data = $this->__getData(4);
		$results = array();

		//編集権限あり
		//--コンテンツあり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan4'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'delete', 'linkExist' => false, 'url' => array()),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			//'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_APPROVED, 'value' => null),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_PUBLISHED, 'value' => null),
		)));
		//--コンテンツなし
		$results[count($results)] = array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null, 'key' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'emptyRender'),
			'exception' => 'ForbiddenException', 'return' => 'viewFile'
		);

		return $results;
	}

/**
 * editアクションのGETテスト(公開権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditGetByPublishable() {
		$data = $this->__getData(6);
		$results = array();

		//--コンテンツあり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan6'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'delete', 'linkExist' => false, 'url' => array()),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Frame][id]', 'value' => $data['Frame']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'input', 'name' => 'data[Block][id]', 'value' => $data['Block']['id']),
		)));
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_IN_DRAFT, 'value' => null),
		)));
		array_push($results, Hash::merge($results[$base], array(
			//'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_APPROVED, 'value' => null),
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'save_' . WorkflowComponent::STATUS_PUBLISHED, 'value' => null),
		)));
		array_push($results, Hash::merge($results[0], array(
			'assert' => array('method' => 'assertInput', 'type' => 'button', 'name' => 'delete', 'value' => null),
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'calendarplan6'),
			'assert' => array('method' => 'assertNotEmpty'),
		)));

		return $results;
	}

/**
 * editアクションのPOSTテスト
 *
 * @param array $data POSTデータ
 * @param string $role ロール
 * @param array $urlOptions URLオプション
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderEditPost
 * @return void
 */
	public function testEditPost($data, $role, $urlOptions, $exception = null, $return = 'view') {
		//ログイン
		if (isset($role)) {
			TestAuthGeneral::login($this, $role);
		}

		//テスト実施
		$this->_testPostAction(
			'post', $data, Hash::merge(array('action' => 'edit'), $urlOptions), $exception, $return
		);

		//正常の場合、リダイレクト
		if (! $exception) {
			$header = $this->controller->response->header();
			$this->assertNotEmpty($header['Location']);
		}

		//ログアウト
		if (isset($role)) {
			TestAuthGeneral::logout($this);
		}
	}

/**
 * editアクションのPOSTテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - role: ロール
 *  - urlOptions: URLオプション
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderEditPost() {
		$data = $this->__getData(1);
		$data2 = $this->__getData(2);
		$data4 = $this->__getData(4);

		return array(
			//ログインなし
			array(
				'data' => $data, 'role' => null,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['CalendarActionPlan']['key']),
				'exception' => 'ForbiddenException',
			),
			//作成権限のみ
			//--他人の記事
			array(
				'data' => $data, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan1'),
				'exception' => 'ForbiddenException',
			),
			//--自分の記事
			array(
				'data' => $data2, 'role' => Role::ROOM_ROLE_KEY_GENERAL_USER,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan2'),
			),
			//編集権限あり
			//--コンテンツあり
			array(
				'data' => $data4, 'role' => Role::ROOM_ROLE_KEY_EDITOR,
				'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan4'),
			),
			//フレームID指定なしテスト
			array(
				'data' => $data2, 'role' => Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
				'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id'], 'key' => 'calendarplan2'),
			), //pending CalendarPlansController.php 304行目のルート($frameIdが0)は通らない？
		);
	}

/**
 * editアクションのValidationErrorテスト用DataProvider
 *
 * ### 戻り値
 *  - data: 登録データ
 *  - urlOptions: URLオプション
 *  - validationError: バリデーションエラー
 *
 * @return array
 */
	public function dataProviderEditValidationError() {
		$data = $this->__getData(2);
		$result = array(
			'data' => $data,
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => 'calendarplan2'),
		);

		return array(
			Hash::merge($result, array(
				'validationError' => array(
					'field' => 'CalendarActionPlan.title',
					'value' => '',
					'message' => __d('calendars', 'Invalid input. (plan title)'),
				)
			)),
		);
	}

/**
 * editアクションのValidateionErrorテスト
 *
 * @param array $data POSTデータ
 * @param array $urlOptions URLオプション
 * @param string|null $validationError ValidationError
 * @dataProvider dataProviderEditValidationError
 * @return void
 */
	public function testEditValidationError($data, $urlOptions, $validationError = null) {
		//ログイン
		TestAuthGeneral::login($this);

		//テスト実施
		$this->_testActionOnValidationError(
			'post', $data, Hash::merge(array('action' => 'edit'), $urlOptions), $validationError
		);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

}