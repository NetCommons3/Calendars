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

/**
 * CalendarFrameSettingSelectRoom Model
 *
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Model
 */
class CalendarFrameSettingSelectRoom extends CalendarsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'NetCommons.Trackable',
		'Workflow.WorkflowComment',
		'Workflow.Workflow',
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
		'CalendarFrameSetting' => array(
			'className' => 'Calendars.CalendarFrameSetting',
			'foreignKey' => 'calendar_frame_setting_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Room' => array(
			'className' => 'Rooms.Room',
			'foreignKey' => 'room_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
			'calendar_frame_setting_id' => array(
				'rule1' => array(
					'rule' => array('numeric'),
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
 * @param int $settingId frame setting id
 * @return array select Rooms
 */
	public function getSelectRooms($settingId) {
		$this->Room = ClassRegistry::init('Rooms.Room', true);
		$selectRoom = $this->Room->find('all', array(
			'fields' => array(
				'Room.id',
				'CalendarFrameSettingSelectRoom.room_id',
				'CalendarFrameSettingSelectRoom.calendar_frame_setting_id'
			),
			'recursive' => -1,
			'joins' => array(
				array('table' => 'calendar_frame_setting_select_rooms',
					'alias' => 'CalendarFrameSettingSelectRoom',
					'type' => 'LEFT',
					'conditions' => array(
						'Room.id = CalendarFrameSettingSelectRoom.room_id',
						'calendar_frame_setting_id' => $settingId,
					)
				)
			),
			'order' => array('Room.id ASC')
		));
		// 全会員のレコードは絶対抜けるので付け足す
		$allMembers = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => CalendarsComponent::CALENDAR_ALLMEMBERS,
				'calendar_frame_setting_id' => $settingId,
			)
		));
		if ($allMembers) {
			$allMembersArray = array(CalendarsComponent::CALENDAR_ALLMEMBERS => CalendarsComponent::CALENDAR_ALLMEMBERS);
		} else {
			$allMembersArray = array(CalendarsComponent::CALENDAR_ALLMEMBERS => '');
		}
		$selectRoom = Hash::merge($allMembersArray, Hash::combine($selectRoom, '{n}.Room.id', '{n}.CalendarFrameSettingSelectRoom.room_id'));
		return $selectRoom;
	}

/**
 * validateFrameSettingSelectRoom
 *
 * @param array $data validate data
 * @return bool
 */
	public function validateCalendarFrameSettingSelectRoom($data) {
		foreach ($data['CalendarFrameSettingSelectRoom'] as $selectRoom) {
			if ($selectRoom['room_id'] == '') {
				continue;
			}
			$this->create();
			$this->set($selectRoom);
			if (! $this->validates()) {
				return false;
			}
		}
		return true;
	}

/**
 * saveFrameSettingSelectRoom
 *
 * @param array $data save data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveCalendarFrameSettingSelectRoom($data) {
		$settingId = $data['CalendarFrameSetting']['id'];
		// 全部消して
		$this->deleteAll(array(
			'calendar_frame_setting_id' => $settingId
		), false);
		// 入れ直し
		$ret = array();
		foreach ($data['CalendarFrameSettingSelectRoom'] as $selectRoom) {
			$this->create();
			$this->set($selectRoom);
			$ret[] = $this->save($selectRoom, false);
		}
		return $ret;
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @throws InternalErrorException
 */
}