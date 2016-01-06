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
 * CalendarFrameSetting Model
 *
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Model
 */
class CalendarFrameSetting extends CalendarsAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',	//key,origin_id $B$"$C$?$iF0:n$7!"$J$/$F$bL532$J%S%X%$%S%"(B

		'NetCommons.Trackable',	// TBL$B$,(B Trackable$B9`L\%;%C%H(B(created_uer$B!\(Bmodified_user)$B$r$b$C$F$$$?$i!"(BTrackable($B?M$NDI@W2DG=!K$H$_$J$5$l$k!#(B
								// Trackable$B$H$_$J$5$l$?$?$i!"(Bcreated_user$B$KBP1~$9$k(Busername,handle(TrackableCreator)$B$,!"(B
								// modified_user$B$KBP1~$9$k(Busername,hanldle(TrackableUpdator)$B$,!"(B
								// belongTo$B$G<+F0DI2C$5$l!"<hF@%G!<%?$K$/$C$D$$$F$/$k!#(B
								// $B$J$*!"(Bcreated_user, modified_user$B$,$J$/$F$bL532$J%S%X%$%S%"$G$"$k!#(B

		'Workflow.Workflow',	// TBL$B$K(B $B>5G'9`L\%;%C%H(B(status + is_active + is_latest + language_id + (origin_id|key) )$B$,$"$l$P!">5G'(BTBL$B$H$_$J$5$l$k!#(B
								// $B>5G'(BTBL$B$N(BINSERT$B$N;~$@$1F/$/!#(BUPDATE$B$N;~$OF/$+$J$$!#(B
								// status===STATUS_PUBLISHED$B!J8x3+!K$N;~$@$1(BINSERT$B%G!<%?$N(Bis_active$B$,(Btrue$B$K$J$j!"(B
								//	key,$B8@8l$,0lCW$9$k$=$NB>$N%G!<%?$O(Bis_active$B$,(Bfalse$B$K$5$l$k!#(B
								// is_latest$B$O(B(status$B$K4X78$J$/(B)INSERT$B%G!<%?$N(Bis_latest$B$,(Btrue$B$K$J$j!"(B
								//	key,$B8@8l$,0lCW$9$k$=$NB>$N%G!<%?$O(Bis_latest$B$,(Bfalse$B$K$5$l$k!#(B
								//
								// $B$J$*!">5G'9`L\%;%C%H$,$J$/$F$bL532$J%S%X%$%S%"$G$"$k!#(B

		'Workflow.WorkflowComment', // $model->data['WorkflowComment'] $B$,$"$l$PF/$/$7!"$J$/$F$bL532$J%S%X%$%S%"!#(B
								// $model->data['WorkflowComment'] $B$,$"$l$P!"$3$N(BTBL$B$K(Bstatus$B$,$"$k$3$H!J$J$1$l$P!"(Bstatus=NULL$B$GFM$C9~$_$^$9!K(B

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
			'className' => 'CalendarFrameSettingSelectRoom',
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
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @throws InternalErrorException
 */
}
