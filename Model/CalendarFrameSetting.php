<?php
/**
 * CalendarFrameSettingSelectRoom Model
 *
 * @property Room $Room
 * @property CalendarFrameSetting $CalendarFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarsAppModel', 'Calendars.Model');
App::uses('CalendarsComponent', 'Calendars.Controller/Component');	//constを使うため

/**
 * CalendarFrameSetting Model
 *
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Model
 */
class CalendarFrameSetting extends CalendarsAppModel {

/**
 * Display type value
 *
 * @var string
 */
	const DISPLAY_TYPE_SMALL_MONTHLY_VALUE	= '0',
			DISPLAY_TYPE_LARGE_MONTHLY_VALUE	= '1',
			DISPLAY_TYPE_WEEKLY_VALUE = '2',
			DISPLAY_TYPE_DAILY_VALUE = '3',
			DISPLAY_TYPE_SCHEDULE_TIME_VALUE = '4',
			DISPLAY_TYPE_SCHEDULE_MEMBER_VALUE = '5';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',	//key,origin_id あったら動作し、なくても無害なビヘイビア

		'NetCommons.Trackable',	// TBLが Trackable項目セット(created_user＋modified_user)をもっていたらTrackable(人の追跡可能）とみなされる。
								// Trackableとみなされたたら、created_userに対応するusername,handle(TrackableCreator)が、
								// modified_userに対応するusername,hanldle(TrackableUpdator)が、
								// belongToで自動追加され、取得データにくっついてくる。
								// なお、created_user, modified_userがなくても無害なビヘイビアである。

		'Workflow.Workflow',	// TBLに 承認項目セット(status + is_active + is_latest + language_id + (origin_id|key) )があれば、承認TBLとみなされる。
								// 承認TBLのINSERTの時だけ働く。UPDATEの時は働かない。
								// status===STATUS_PUBLISHED（公開）の時だけINSERTデータのis_activeがtrueになり、
								//	key,言語が一致するその他のデータはis_activeがfalseにされる。
								// is_latestは(statusに関係なく)INSERTデータのis_latestがtrueになり、
								//	key,言語が一致するその他のデータはis_latestがfalseにされる。
								//
								// なお、承認項目セットがなくても無害なビヘイビアである。

		'Workflow.WorkflowComment', // $model->data['WorkflowComment'] があれば働くし、なくても無害なビヘイビア。
								// $model->data['WorkflowComment'] があれば、このTBLにstatusがあること（なければ、status=NULLで突っ込みます）

		'Calendars.CalendarValidate',
		'Calendars.CalendarApp',	//baseビヘイビア
		'Calendars.CalendarInsertPlan', //Insert用
		'Calendars.CalendarUpdatePlan', //Update用
		'Calendars.CalendarDeletePlan', //Delete用
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Room' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CalendarFrameSettingSelectRoom' => array(
			'className' => 'Calendars.CalendarFrameSettingSelectRoom',
			'foreignKey' => 'calendar_frame_setting_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('id' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'display_type' => array(
				'rule1' => array(
					'rule' => array('numeric'),
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'start_pos' => array(
				'rule1' => array(
					'rule' => array('numeric'),
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'display_count' => array(
				'rule1' => array(
					'rule' => array('numeric'),
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'is_myroom' => array(
				'rule1' => array(
					'rule' => 'boolean',
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'is_select_room' => array(
				'rule1' => array(
					'rule' => 'boolean',
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'room_id' => array(
				'rule1' => array(
					'rule' => array('numeric'),
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * getSelectRooms
 *
 * @param int $settingId calendar frame setting id
 * @return array select Rooms
 */
	public function getSelectRooms($settingId = null) {
		if ($settingId === null) {
			$setting = $this->find('first', array(
				'conditions' => array(
					'frame_key' => Current::read('Frame.key'),
				)
			));
			$settingId = $setting['CalendarFrameSetting']['id'];
		}
		$this->CalendarFrameSettingSelectRoom = ClassRegistry::init('Calendars.CalendarFrameSettingSelectRoom', true);
		$selectRooms = $this->CalendarFrameSettingSelectRoom->getSelectRooms($settingId);
		return $selectRooms;
	}

/**
 * saveFrameSetting
 *
 * @param array $data save data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveFrameSetting($data) {
		//トランザクションBegin
		$this->begin();
		try {
			// フレーム設定のバリデート
			$this->set($data);
			if (! $this->validates()) {
				CakeLog::error(serialize($this->validationErrors));

				$this->rollback();
				return false;
			}

			//フレームの登録
			if (! ($data = $this->save($data, false))) {	//バリデートは前で終わっているので第二引数=false
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			if ($data['CalendarFrameSetting']['is_select_room']) {
				//ルーム指定あり処理.
				$this->CalendarFrameSettingSelectRoom = ClassRegistry::init('Calendars.CalendarFrameSettingSelectRoom');
				if (! $this->CalendarFrameSettingSelectRoom->validateCalendarFrameSettingSelectRoom($data)) {
					CakeLog::error(serialize($this->CalendarFrameSettingSelectRoom->validationErrors));

					$this->rollback();
					return false;
				}
				if (! $this->CalendarFrameSettingSelectRoom->saveCalendarFrameSettingSelectRoom($data)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			$this->commit();
		} catch (Exception $ex) {
			CakeLog::error($ex);

			$this->rollback();
			throw $ex;
		}
		return $data;
	}

/**
 * setDefaultValue
 *
 * @param array &$data save data
 * @return void
 * @throws InternalErrorException
 */
	public function setDefaultValue(&$data) {
		$data[$this->alias]['display_type'] = CalendarsComponent::CALENDAR_DISP_TYPE_SMALL_MONTHLY;
		//start_pos、is_myroom、is_select_roomはtableの初期値をつかう。
		$data[$this->alias]['display_count'] = CalendarsComponent::CALENDAR_STANDARD_DISPLAY_DAY_COUNT;

		//frame_key,room_idは明示的に設定されることを想定し、setDefaultではなにもしない。
		$data[$this->alias]['timeline_base_time'] = CalendarsComponent::CALENDAR_TIMELINE_DEFAULT_BASE_TIME;
	}

}