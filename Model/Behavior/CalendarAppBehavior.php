<?php
/**
 * CalendarApp Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
//App::uses('CalendarTime', 'Calendars.Utility');
App::uses('CalendarTime', 'Calendars.Utility');
App::uses('CalendarSupport', 'Calendars.Utility');
App::uses('WorkflowComponent', 'Workflow.Controller/Component');

/**
 * CalendarAppBehavior
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Calendars\Model\Behavior
 */
class CalendarAppBehavior extends ModelBehavior {
	const CALENDAR_PLAN_EDIT_THIS = '0';	//この日の予定のみ変更(削除)
	const CALENDAR_PLAN_EDIT_AFTER = '1';	//この日以降の予定を変更(削除)
	const CALENDAR_PLAN_EDIT_ALL = '2';		//この日を含むすべての予定を変更(削除)

	const CALENDAR_PLUGIN_NAME = 'calendar';
	const TASK_PLUGIN_NAME = 'task';	//ＴＯＤＯプラグインに相当
	const RESERVATION_PLUGIN_NAME = 'reservation';

	const CALENDAR_LINK_UPDATE = 'update';
	const CALENDAR_LINK_CLEAR = 'clear';

	const CALENDAR_INSERT_MODE = 'insert';
	const CALENDAR_UPDATE_MODE = 'update';


	//以下は暫定定義
	const _ON = 1;
	const _OFF = 0;

	const ROOM_ZERO = 0;

/**
 * calendarWdayArray
 *
 * @var array
 */
	public static $calendarWdayArray = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');

/**
 * edit_rrrule_list
 *
 * @var array
 */
	public static $editRrules = array(
		self::CALENDAR_PLAN_EDIT_THIS,
		self::CALENDAR_PLAN_EDIT_AFTER,
		self::CALENDAR_PLAN_EDIT_ALL
	);

/**
 * 登録処理
 *
 * @param Model &$model 実際のモデル名
 * @param array $planParams planParams
 * @param array $rruleData rruleData
 * @param array $eventData eventデータ(CalendarEventのモデルデータ)
 * @param string $startTime startTime 開始日付時刻文字列
 * @param string $endTime endTime 開始日付時刻文字列
 * @return array $rEventData
 * @throws InternalErrorException
 */
	public function insert(Model &$model, $planParams, $rruleData, $eventData, $startTime, $endTime) {
		$this->loadEventAndRruleModels($model);
		$params = array(
			'conditions' => array('CalendarsRrule.id' => $eventData['CalendarEvent']['calendar_rrule_id']),
			'recursive' => (-1),
			'fields' => array('CalendarsRrule.*'),
			'callbacks' => false
		);
		if (empty($this->rruleData)) {
			//CalendarRruleのデータがないので初回アクセス
			//
			$rruleData = $model->CalendarRrule->find('first', $params);
			if (!is_array($rruleData) || !isset($rruleData['CalendarRrule'])) {
				$this->validationErrors = Hash::merge($this->validationErrors, $model->CalendarRrule->validationErrors);
				//throw new InternalErrorException(__d('Calendars', 'insert find error.'));
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->rruleData = $rruleData;	//CalendarRruleのデータを記録し、２度目以降に備える。
		}

		$insertStartTime = CalendarTime::timezoneDate($startTime, 1, 'YmdHis');
		$insertEndTime = CalendarTime::timezoneDate($endTime, 1, 'YmdHis');

		$rEventData = $this->setReventData($eventData, $insertStartTime, $insertEndTime); //eventDataをもとにrEventDataをつくり、モデルにセット

		$model->CalendarEvent->set($rEventData);

		if (!$model->CalendarEvent->validates()) {	//rEventDataをチェック
			$this->validationErrors = Hash::merge($this->validationErrors, $model->CalendarEvent->validationErrors);
			//throw new InternalErrorException(__d('Calendars', 'rEvent data check error.'));
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		if (!$model->CalendarEvent->save($rEventData, false)) { //保存のみ
			$this->validationErrors = Hash::merge($this->validationErrors, $model->CalendarEvent->validationErrors);
			//throw new InternalErrorException(__d('Calendars', 'rEvent data save error.'));
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}

		$rEventData['CalendarEvent']['id'] = $model->CalendarEvent->id; //採番されたidをeventDataにセ>

		if ($rEventData['CalendarEventContent']['linked_model'] !== '') { //関連コンテンツの登録
			if (!(isset($this->CalendarEventContent))) {
				$model->loadModels(['CalendarEventContent' => 'Calendar.CalendarEventContent']);
			}
			$this->CalendarEventContent->saveLinkedData($rEventData);
		}

		return $rEventData;
	}

/**
 * rEventDataへのデータ設定
 *
 * @param array $eventData 元になるeventData配列
 * @param string $insertStartTime insertStartTime 登録用開始日付時刻文字列
 * @param string $insertEndTime insertEndTime 登録用終了日付時刻文字列
 * @return array 実際に登録する$rEventData配列を返す
 */
	public function setReventData($eventData, $insertStartTime, $insertEndTime) {
		$rEventData = $eventData;

		$rEventData['CalendarEvent']['id'] = null;		//新規登録用にidにnullセット

		$rEventData['CalendarEvent']['start_date'] = substr($insertStartTime, 0, 8);
		$rEventData['CalendarEvent']['start_time'] = substr($insertStartTime, 8);
		$rEventData['CalendarEvent']['dtstart'] = $insertStartTime;
		$rEventData['CalendarEvent']['end_date'] = substr($insertEndTime, 0, 8);
		$rEventData['CalendarEvent']['end_time'] = substr($insertEndTime, 8);
		$rEventData['CalendarEvent']['dtend'] = $insertEndTime;

		if (isset($eventData['CalendarEvent']['created_user'])) {
			$rEventData['CalendarEvent']['created_user'] = $eventData['CalendarEvent']['created_user'];
		}

		if (isset($eventData['CalendarEvent']['created'])) {
			$rEventData['CalendarEvent']['created'] = $eventData['CalendarEvent']['created'];
		}

		if (isset($eventData['CalendarEvent']['modified_user'])) {
			$rEventData['CalendarEvent']['modified_user'] = $eventData['CalendarEvent']['modified_user'];
		}

		if (isset($eventData['CalendarEvent']['modified'])) {
			$rEventData['CalendarEvent']['modified'] = $eventData['CalendarEvent']['modified'];
		}

		return $rEventData;
	}

/**
 * startDate,startTime,endDate,endTime生成
 *
 * @param string $sTime sTime文字列(年月日時分秒）
 * @param string $eTime eTime文字列(年月日時分秒）
 * @param string &$startDate 生成したstartDate文字列
 * @param string &$startTime 生成したstartTime文字列
 * @param string &$endDate 生成したendDate伯父列
 * @param string &$endTime 生成したendTime文字列
 * @return void
 */
	public function setStartDateTiemAndEndDateTime($sTime, $eTime, &$startDate, &$startTime, &$endDate, &$endTime) {
		$startTimestamp = mktime(0, 0, 0, substr($sTime, 4, 2), substr($sTime, 6, 2), substr($sTime, 0, 4));
		$endTimestamp = mktime(0, 0, 0, substr($eTime, 4, 2), substr($eTime, 6, 2), substr($eTime, 0, 4));

		$diffNum = ($endTimestamp - $startTimestamp) / 86400;

		$timestamp = mktime(substr($sTime, 8, 2), substr($sTime, 10, 2), substr($sTime, 12, 2),
							substr($byday, 4, 2), substr($byday, 6, 2), substr($byday, 0, 4));
		$startDate = date('Ymd', $timestamp);
		$startTime = date('His', $timestamp);

		$timestamp = mktime(substr($eTime, 8, 2), substr($eTime, 10, 2), substr($eTime, 12, 2),
							substr($byday, 4, 2), substr($byday, 6, 2) + $diffNum, substr($byday, 0, 4));
		$endDate = date('Ymd', $timestamp);
		$endTime = date('His', $timestamp);
	}

/**
 * RruleDataへのデータ設定
 *
 * @param array $planParams 予定パラメータ
 * @param array &$rruleData rruleデータ
 * @param string $mode mode insert時:self::CALENDAR_INSERT_MODE(デフォルト値) update時:self::CALENDAR_UPDATE_MODE
 * @return void
 */
	public function setRruleData($planParams, &$rruleData, $mode = self::CALENDAR_INSERT_MODE) {
		$params = array(
			'location' => '',
			'contact' => '',
			'description' => '',
			'rrule' => '',
			'room_id' => 1, //Current::read('Room.id'),		//ATODE
			'icalendar_uid' => CalendarSupprt::generateIcalUid($planParams['start_date'], $planParams['start_time']),
			'icalendar_comp_name' => self::CALENDAR_PLUGIN_NAME,
			'status' => WorkflowComponent::STATUS_IN_DRAFT,
			//'language_id' => 1, //Current::read('Language.id'),	//ATODE
		);

		foreach ($planParams as $key => $val) {
			if (isset($params[$key])) {
				$params[$key] = $val;
			}
		}

		//レコード $rrule_data  の初期化と'CalendarRrule'キーセットはおわっているので省略
		//$rruleData = array();
		//$rruleData['CalendarRrule'] = array();

		//rruleDataに詰める。
		//$rruleData['CalendarRrule']['id'] = null;		//create()の後なので、不要。
		$rruleData['CalendarRrule']['block_id'] = 1; //ATODE  Current::read('Block.id');	//Block.idを取得
		//keyは、Workflowが自動セット
		$rruleData['CalendarRrule']['name'] = '';		//名前はデフォルトなし
		////$rruleData['CalendarRrule']['location'] = $params['location'];
		////$rruleData['CalendarRrule']['contact'] = $params['contact'];
		////$rruleData['CalendarRrule']['description'] = $params['description'];
		$rruleData['CalendarRrule']['rrule'] = $params['rrule'];
		if ($mode === self::CALENDAR_INSERT_MODE) {
			$rruleData['CalendarRrule']['icalendar_comp_name'] = $params['icalendar_comp_name'];
		}
		$rruleData['CalendarRrule']['room_id'] = $params['room_id'];
		$rruleData['CalendarRrule']['status'] = $params['status'];
		////$rruleData['CalendarRrule']['language_id'] = $params['language_id'];
	}

/**
 * setPlanParams2Params
 *
 * planParamsからparamsへの設定
 *
 * @param array &$planParams 予定パラメータ
 * @param array &$params paramsパラメータ
 * @return void
 */
	public function setPlanParams2Params(&$planParams, &$params) {
		$keys = array(
			'title',
			'title_icon',
			'location',
			'contact',
			'description',
			'is_allday',
			'timezone_offset',
			//'link_plugin',
			//'link_key',
			//'link_plugin_controller_action_name',
			'linked_model',
			'linked_content_key',
		);
		foreach ($keys as $key) {
			if (isset($planParams[$key])) {
				$params[$key] = $planParams[$key];
			}
		}
	}

/**
 * eventDataへのデータ設定
 *
 * @param array $planParams 予定パラメータ
 * @param array $rruleData rruleDataパラメータ
 * @param array &$eventData eventデータ
 * @return void
 */
	public function setEventData($planParams, $rruleData, &$eventData) {
		//初期化
		$params = array(
			'calendar_rrule_id' => $rruleData['CalendarRrule']['id'],	//外部キーをセット
			'room_id' => $rruleData['CalendarRrule']['room_id'],
			'language_id' => $rruleData['CalendarRrule']['language_id'],
			'target_user' => CakeSession::read('Calendars.target_user'),	//カレンダーの対象ユーザをSessionから取り出しセット
			'title' => '',
			'title_icon' => '',
			'location' => '',
			'contact' => '',
			'description' => '',
			'is_allday' => self::_OFF,
			'start_date' => $planParams['start_date'],
			'start_time' => $planParams['start_time'],
			'dtstart' => $planParams['start_date'] . $planParams['start_time'],
			'end_date' => $planParams['end_date'],
			'end_time' => $planParams['end_time'],
			'dtend' => $planParams['end_date'] . $planParams['end_time'],
			'timezone_offset' => CakeSession::read('Calendars.timezone_offset'),
			//'link_plugin' => '',
			//'link_key' => '',
			//'link_plugin_controller_action_name' => ''
			'linked_model' => '',
			'linked_content_key' => '',
		);

		setPlanParams2Params($planParams, $params);

		//レコード $event_data  の初期化と'CalendarEvent'キーセットはおわっているので省略
		//$eventData = array();
		//$eventData['CalendarEvent'] = array();

		//eventを詰める。
		//$eventData['CalendarEvent']['id'] = null;		//create()の後なので、不要。
		$eventData['CalendarEvent']['calendar_rrule_id'] = $params['calendar_rrule_id'];
		$eventData['CalendarEvent']['room_id'] = $params['room_id'];
		$eventData['CalendarEvent']['language_id'] = $params['language_id'];
		$eventData['CalendarEvent']['target_user'] = $params['target_user'];
		$eventData['CalendarEvent']['title'] = $params['title'];
		$eventData['CalendarEvent']['title_icon'] = $params['title_icon'];
		$eventData['CalendarEvent']['is_allday'] = $params['is_allday'];
		$eventData['CalendarEvent']['start_date'] = $params['start_date'];
		$eventData['CalendarEvent']['start_time'] = $params['start_time'];
		$eventData['CalendarEvent']['dtstart'] = $params['dtstart'];
		$eventData['CalendarEvent']['end_date'] = $params['end_date'];
		$eventData['CalendarEvent']['end_time'] = $params['end_time'];
		$eventData['CalendarEvent']['dtend'] = $params['dtend'];
		$eventData['CalendarEvent']['timezone_offset'] = $params['timezone_offset'];

		//$eventData['CalendarEvent']['link_plugin'] = $params['link_plugin'];
		//$eventData['CalendarEvent']['link_key'] = $params['link_key'];
		//$eventData['CalendarEvent']['link_plugin_controller_action_name'] = $params['link_plugin_controller_action_name'];
		//保存するモデルをここで替える
		$eventData['CalendarEventContent']['linked_model'] = $params['linked_model'];
		$eventData['CalendarEventContent']['linked_content_key'] = $params['linked_content_key'];
	}

/**
 * eventとrruleの両モデルをロードする。
 *
 * @param Model &$model モデル
 * @return void
 */
	public function loadEventAndRruleModels(Model &$model) {
		if (!isset($model->CalendarEvent)) {
			$model->loadModels([
				'CalendarEvent' => 'Calendars.CalendarEvent'
			]);
		}
		if (!isset($model->CalendarRrule)) {
			$model->loadModels([
				'CalendarRrule' => 'Calendars.CalendarRrule'
			]);
		}
	}
}