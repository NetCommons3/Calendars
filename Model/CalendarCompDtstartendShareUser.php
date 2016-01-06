<?php
/**
 * CalendarCompDtstartendShareUser Model
 *
 * @property CalendarCompDtstartend $CalendarCompDtstartend
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarsAppModel', 'Calendars.Model');

/**
 * CalendarCompDtStartendShareUser Model
 *
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Model
 */
class CalendarCompDtstartendShareUser extends CalendarsAppModel {

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
		'Calendars.CalendarApp',	//base$B%S%X%$%S%"(B
		'Calendars.CalendarInsertPlan', //Insert$BMQ(B
		'Calendars.CalendarUpdatePlan', //Update$BMQ(B
		'Calendars.CalendarDeletePlan', //Delete$BMQ(B
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CalendarCompDtstardend' => array(
			'className' => 'Calendars.CalendarCompDtstartend',
			'foreignKey' => 'calendar_comp_dtstartend_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
			'calender_comp_dtstartend_id' => array(
				'rule1' => array(
					'rule' => array('numeric'),
					'required' => true,
					'message' => __d('net_commons', 'Invaid request'),
				),
			),
			'share_user' => array(
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
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @throws InternalErrorException
 */
}
