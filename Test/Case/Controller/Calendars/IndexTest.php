<?php
/**
 * CalendarsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarsController', 'Calendars.Controller');
App::uses('WorkflowControllerIndexTest', 'Workflow.TestSuite');
App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('CalendarsComponent', 'Calendars.Controller/Component');
App::uses('CalendarFrameSetting', 'Calendars.Model');

/**
 * CalendarsController Test Case
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Calendars\Test\Case\Controller\CalendarsController
 */
class CalendarsControllerIndexTest extends NetCommonsControllerTestCase {

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
	protected $_controller = 'calendars';

/**
 * テストDataの取得
 *
 * @return array
 */
	private function __getData() {
		$frameId = '6';
		$blockId = '2';
		$blockKey = 'block_1';

		$data = array(
			'Frame' => array(
				'id' => $frameId
			),
			'Block' => array(
				'id' => $blockId,
				'key' => $blockKey,
				'language_id' => '2',
				'room_id' => '1',
				'plugin_key' => $this->plugin,
			),
		);

		return $data;
	}

/**
 * indexアクションのテスト
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string $style スタイル
 * @param string $startPos 開始位置（昨日/今日）
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndex
 * @return void
 */
	public function testIndex($urlOptions, $assert, $style = '', $startPos = '', $exception = null, $return = 'view') {
		//スタイル Fixture書き換え
		//Current::$current['CalendarFrameSetting']['display_type'] = $style;
		$data['CalendarFrameSetting'] = (new CalendarFrameSettingFixture())->records[0];
		$data['CalendarFrameSetting']['display_type'] = $style;

		if ($startPos == CalendarsComponent::CALENDAR_START_POS_WEEKLY_YESTERDAY) {
			$data['CalendarFrameSetting']['start_pos'] = CalendarsComponent::CALENDAR_START_POS_WEEKLY_YESTERDAY;
		} elseif ($startPos == CalendarsComponent::CALENDAR_START_POS_WEEKLY_TODAY) {
			$data['CalendarFrameSetting']['start_pos'] = CalendarsComponent::CALENDAR_START_POS_WEEKLY_TODAY;
		}

		$this->controller->CalendarFrameSetting->save($data);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
		), $urlOptions);
		$this->_testGetAction($url, $assert, $exception, $return);
	}

/**
 * indexアクションのテスト(ログインなし)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndex() {
		//$data = $this->__getData();
		$results = array();

		//ログインなし（月縮小）
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//ログインなし（月拡大）
		$results[1] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertNotEmpty'),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_LARGE_MONTHLY,
		);
		//ログインなし（週表示）
		$results[2] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertNotEmpty'),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_WEEKLY,
		);
		//ログインなし（日表示）(タイムライン)
		$results[3] = array(
			'urlOptions' => array('frame_id' => '6', '?' => array('tab' => 'timeline')),
			'assert' => array('method' => 'assertNotEmpty'),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_DAILY,
		);
		//ログインなし（スケジュール（時間順）表示）(今日から表示)
		$results[4] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertNotContains', 'expected' => __d('calendars', 'yesterday')),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_TSCHEDULE,
		);
		//ログインなし（スケジュール（時間順）表示）（昨日から表示）
		$results[5] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('calendars', 'yesterday')),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_TSCHEDULE,
			'startPos' => CalendarsComponent::CALENDAR_START_POS_WEEKLY_YESTERDAY,
		);
		//ログインなし（スケジュール（会員順）表示）(今日から表示)
		$results[6] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertNotContains', 'expected' => __d('calendars', 'yesterday')),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_MSCHEDULE,
			'startPos' => CalendarsComponent::CALENDAR_START_POS_WEEKLY_TODAY,
		);
		//ログインなし（スケジュール（会員順）表示）（昨日から表示）
		$results[7] = array(
			'urlOptions' => array('frame_id' => '6'),
			'assert' => array('method' => 'assertContains', 'expected' => __d('calendars', 'yesterday')),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_MSCHEDULE,
			'startPos' => CalendarsComponent::CALENDAR_START_POS_WEEKLY_YESTERDAY,
		);

		//  pending 120行目の最後のelse「月縮小とみなす」のルートは通せない？？
		//チェック
		//--追加ボタンチェック(なし)
		$results[8] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2'),
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => false, 'url' => array()),
		);

		return $results;
	}

/**
 * indexアクションのテスト(編集権限あり)
 *
 * @param array $urlOptions URLオプション
 * @param array $assert テストの期待値
 * @param string $style スタイル
 * @param string|null $exception Exception
 * @param string $return testActionの実行後の結果
 * @dataProvider dataProviderIndexByEditable
 * @return void
 */
	public function testIndexByEditable($urlOptions, $assert, $style = '', $exception = null, $return = 'view') {
		//ログイン
		TestAuthGeneral::login($this, Role::ROOM_ROLE_KEY_EDITOR);

		//スタイル Fixture書き換え
		$data['CalendarFrameSetting'] = (new CalendarFrameSettingFixture())->records[0];
		$data['CalendarFrameSetting']['display_type'] = $style;
		$this->controller->CalendarFrameSetting->save($data);

		//テスト実施
		$url = Hash::merge(array(
			'plugin' => $this->plugin,
			'controller' => $this->_controller,
			'action' => 'index',
		), $urlOptions);

		$this->_testGetAction($url, $assert, $exception, $return);

		//ログアウト
		TestAuthGeneral::logout($this);
	}

/**
 * indexアクションのテスト(編集権限あり)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByEditable() {
		$results = array();

		//編集権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => '6', 'block_id' => '2'),
			'assert' => array('method' => 'assertNotEmpty'),
			'style' => CalendarsComponent::CALENDAR_DISP_TYPE_DAILY,
		);
		//チェック
		//--追加ボタンチェック 日表示
		array_push($results, Hash::merge($results[$base], array(
			//'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array('controller' => 'calendar_plans')),
			'assert' => array('method' => 'assertContains', 'expected' => '/calendars/calendar_plans/add?'),
		)));
		//フレームあり（ブロックなし）
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => '14', 'block_id' => null),
			'assert' => array('method' => 'assertEquals', 'expected' => 'index'),
			'exception' => null, 'return' => 'viewFile'
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => '2'),
			'assert' => array('method' => 'assertContains', 'expected' => 'index'),
		)));

		return $results;
	}

/**
 * indexアクションのテスト(作成権限のみ)用DataProvider
 *
 * ### 戻り値
 *  - urlOptions: URLオプション
 *  - assert: テストの期待値
 *  - exception: Exception
 *  - return: testActionの実行後の結果
 *
 * @return array
 */
	public function dataProviderIndexByCreatable() {
		$data = $this->__getData();
		$results = array();

		//作成権限あり
		$base = 0;
		$results[0] = array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		);
		//チェック
		//--追加ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'assert' => array('method' => 'assertActionLink', 'action' => 'add', 'linkExist' => true, 'url' => array()),
		)));
		//--編集ボタンチェック
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => $data['Frame']['id'], 'block_id' => $data['Block']['id'], 'key' => $data['FaqQuestion']['key']),
			'assert' => array('method' => 'assertActionLink', 'action' => 'edit', 'linkExist' => true, 'url' => array()),
		)));
		//フレームID指定なしテスト
		array_push($results, Hash::merge($results[$base], array(
			'urlOptions' => array('frame_id' => null, 'block_id' => $data['Block']['id']),
			'assert' => array('method' => 'assertNotEmpty'),
		)));

		return $results;
	}

}