<?php
/**
 * CalendarActionPlan Model
 *
 * @property Block $Block
 * @property Room $Room
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarsAppModel', 'Calendars.Model');
App::uses('CalendarsComponent', 'Calendars.Controller/Component');
App::uses('CalendarSupport', 'Calendars.Utility');

/**
 * Calendar Action Plan Model
 *
 * @author AllCreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\Model
 * @SuppressWarnings(PHPMD)
 */
class CalendarActionPlan extends CalendarsAppModel {

/**
 * use table
 *
 * このモデルはvalidateと
 * insert/update/deletePlan()呼び出しが主目的なのでテーブルを使用しない。
 * @var array
 */
	public $useTable = false;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'NetCommons.Trackable',
		//FUJI'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'Calendars.CalendarValidate',
		'Calendars.CalendarApp',	//baseビヘイビア
		'Calendars.CalendarInsertPlan', //Insert用
		'Calendars.CalendarUpdatePlan', //Update用
		'Calendars.CalendarDeletePlan', //Delete用
		'Calendars.CalendarExposeRoom', //ルーム表示・選択用
		'Calendars.CalendarPlanOption', //予定CRUD画面の各種選択用
		'Calendars.CalendarPlanTimeValidate',	//予定（時間関連）バリデーション専用
		'Calendars.CalendarPlanRruleValidate',	//予定（Rrule関連）バリデーション専用
		'Calendars.CalendarPlanValidate',	//予定バリデーション専用
		////'Calendars.CalendarRruleHandle',	//concatRrule()など
		'Calendars.CalendarPlanGeneration',	//元予定の新世代予定生成関連
		/*
		// 自動でメールキューの登録, 削除。ワークフロー利用時はWorkflow.Workflowより下に記述する
		'Mails.MailQueue' => array(
			'embedTags' => array(
				'X-SUBJECT' => 'CalendarActionPlan.title',
				'X-LOCATION' => 'CalendarActionPlan.location',
				'X-CONTACT' => 'CalendarActionPlan.contact',
				'X-BODY' => 'CalendarActionPlan.description',
				'X-URL' => array(
					'controller' => 'calendar_plans'
				)
			),
			'workflowType' => 'workflow',
		),
		'Mails.MailQueueDelete',
		*/
		'Calendars.CalendarMail',
		'Calendars.CalendarTopics',
		'Calendars.CalendarLink',
	);
	// @codingStandardsIgnoreStart
	// $_schemaはcakePHP2の予約語だが、宣言するとphpcsが警告を出すので抑止する。
	// ただし、$_schemaの直前にIgnoreStartを入れると、今度はphpdocが直前の
	// property説明がないと警告を出す。よって、この位置にIgnoreStartを挿入した。

/**
 * use _schema
 *
 * @var array
 */
	public $_schema = array (
		// @codingStandardsIgnoreEnd

		// 入力カラムの定義、データ型とdefault値、必要ならlength値
		//繰返し編集の指定(0/1/2). このフィールドは渡ってこない時もあるので
		//ViewにてunlockField指定しておくこと。
		'edit_rrule' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		//カレンダー元eventId
		'origin_event_id' => array(
			'type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false),
		//カレンダー元eventKey
		'origin_event_key' => array(
			'type' => 'string', 'default' => ''),
		//カレンダー元eventRecurrence
		'origin_event_recurrence' => array(
			'type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false),
		//カレンダー元eventException
		'origin_event_exception' => array(
			'type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false),

		//カレンダー元rruleId
		'origin_rrule_id' => array(
			'type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false),
		//カレンダー元rruleKey
		'origin_rrule_key' => array(
			'type' => 'string', 'default' => ''),
		//カレンダー元rruleを共有する兄弟eventの数
		'origin_num_of_event_siblings' => array(
			'type' => 'integer', 'null' => false, 'default' => 0, 'unsigned' => false),

		// 全変更選択時、繰返し先頭eventのeditボタンを擬似クリックする方式用の項目
		// editLink()を呼ぶときの必要パラメータ
		'first_sib_year' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'first_sib_month' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'first_sib_day' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'first_sib_event_id' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		/*
		// -- 以下のcapForViewOf1stSibによるデータすり替え方式用の項目(first_sib_cap_xxx)は、--
		// -- 全変更選択時、繰返し先頭eventのeditボタンを擬似クリックする方式にかえたので、削除. --

		//先頭兄弟（繰返しの先頭）capForView(表示用CalendarActionPlan)の情報
		'first_sib_cap_enable_time' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'first_sib_cap_easy_start_date' => array('type' => 'string', 'default' => ''),	//YYYY-MM-DD
		'first_sib_cap_easy_hour_minute_from' => array('type' => 'string', 'default' => ''), //hh:mm
		'first_sib_cap_easy_hour_minute_to' => array(
			'type' => 'string', 'default' => ''),	//hh:mm
		'first_sib_cap_detail_start_datetime' => array(
			'type' => 'string', 'default' => ''),	//YYYY-MM-DD or YYYY-MM-DD hh:mm
		'first_sib_cap_detail_end_datetime' => array(
			'type' => 'string', 'default' => ''), //YYYY-MM-DD or YYYY-MM-DD hh:mm
		'first_sib_cap_timezone_offset' => array('type' => 'string', 'default' => ''),
		*/

		//タイトル
		'title' => array('type' => 'string', 'default' => ''),

		//タイトルアイコン
		//注）タイトルアイコンは、CalendarActionPlanモデルを指定することで、以下の形式で渡ってくる。
		//<input id="PlanTitleIcon" class="ng-scope" type="hidden" value="/net_commons/img/title_icon/10_040_left.svg" name="data[CalendarActionPlan][title_icon]">
		'title_icon' => array('type' => 'string', 'default' => ''),

		//時間の指定(1/0)
		'enable_time' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		////完全なる開始日付時刻と終了日付時刻(hidden)
		////'full_start_datetime' => array('type' => 'string', 'default' => ''),	//hidden
		////'full_end_datetime' => array('type' => 'string', 'default' => ''),	//hidden

		//簡易編集の日付時刻エリア
		'easy_start_date' => array('type' => 'string', 'default' => ''),	//YYYY-MM-DD
		'easy_hour_minute_from' => array('type' => 'string', 'default' => ''), //hh:mm
		'easy_hour_minute_to' => array('type' => 'string', 'default' => ''),	//hh:mm
		//詳細編集の日付時刻エリア
		'detail_start_datetime' => array(
			'type' => 'string', 'default' => ''),	//YYYY-MM-DD or YYYY-MM-DD hh:mm
		'detail_end_datetime' => array(
			'type' => 'string', 'default' => ''), //YYYY-MM-DD or YYYY-MM-DD hh:mm

		//公開対象
		'plan_room_id' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		//注）共有するユーザ群は、CalendarActionPlanモデルではなく、GroupsUserモデルの配列として以下形式で渡ってくる。
		//<input type="hidden" value="2" name="data[GroupsUser][0][user_id]">
		//<input type="hidden" value="3" name="data[GroupsUser][1][user_id]">

		//タイムゾーン
		'timezone_offset' => array('type' => 'string', 'default' => ''),

		//詳細フラグ(1/0) (hidden. 画面表示時点で、detail(or easy)かはわかるので値を指定しておく。
		'is_detail' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		//場所
		'location' => array('type' => 'string', 'default' => ''),
		//連絡先
		'contact' => array('type' => 'string', 'default' => ''),
		//内容(wysiwyg)
		'description' => array('type' => 'string', 'default' => ''),

		//予定を繰り返す(1/0)
		'is_repeat' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		//繰返し周期 DAILY, WEEKLY, MONTHLY, YEARLY
		'repeat_freq' => array('type' => 'string', 'default' => ''),

		//繰返し間隔 rrule_interval[DAILY], rrule_interval[WEEKLY], rrule_interval[MONTHLY], rrule_interval[YEARLY]
		// rrule_interval[DAILY] inList => array(1, 2, 3, 4, 5, 6)  //n日ごと
		// rrule_interval[WEEKLY] inList => array(1, 2, 3, 4, 5) //n週ごと
		// rrule_interval[MONTHLY] inList => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11) //nヶ月ごと
		// rrule_interval[YEARLY] inList => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12) //n年ごと
		'rrule_interval' => array('type' => 'string', 'default' => ''),

		//週単位or月単位 rrule_byday[WEEKLY], rrule_byday[MONTHLY], rrule_byday[YEARLY]
		// rrule_byday[WEEKLY] inList => array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA')
		// rrule_byday[MONTHLY] inList => array('', '1SU', '1MO', '1TU', ... , '4FR, '4SA', '-1SU', '-2SU', ..., '-1SA')
		// rrule_byday[YEARLY] inList => array('', '1SU', '1MO', '1TU', ... , '4FR, '4SA', '-1SU', '-2SU', ..., '-1SA')
		'rrule_byday' => array('type' => 'string', 'default' => ''),

		//月単位 rrule_bymonthday[MONTHLY]
		// rrule_bymonthday[MONTHLY] inList => array('', 1, 2, ..., 31 );
		'rrule_bymonthday' => array('type' => 'string', 'default' => ''),

		//年単位 rrule_bymonth[YEARLY]
		// rrule_bymonth[YEARLY] inList => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12) //n月
		'rrule_bymonth' => array('type' => 'string', 'default' => ''),

		//繰返しの終了指定
		// rrule_term inList('COUNT', 'UNTIL')
		'rrule_term' => array('type' => 'string', 'default' => ''),

		//繰返し回数
		'rrule_count' => array('type' => 'string', 'default' => ''),

		//繰返し終了日
		'rrule_until' => array('type' => 'string', 'default' => ''),

		//メールで通知(1/0)
		'enable_email' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		//メール通知タイミング
		'email_send_timing' => array(
			'type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),

		//承認ステータス
		//statusは カレンダー独自stauts取得関数getStatusで取ってくるので、ここからは外す。
		//'status' => array('type' => 'integer', 'null' => false, 'unsigned' => false),

	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->loadModels([
			'Frame' => 'Frames.Frame',
			'Calendar' => 'Calendars.Calendar',
		]);
	}

/**
 * _doMergeRruleValidate
 *
 * 繰返し関連バリデーションのマージ
 *
 * @param bool $isDetailEdit 詳細画面かどうか true=詳細(detail)画面, false=簡易(easy)画面
 * @return void
 */
	protected function _doMergeRruleValidate($isDetailEdit) {
		$this->validate = array_merge($this->validate, array(
			'edit_rrule' => array(
				'rule1' => array(
					'rule' => array('inList', array(0, 1, 2)),
					'required' => false,
					'message' => __d('calendars', 'Invalid input. (change of repetition)'),
				),
			),
			'is_repeat' => array(
				'rule1' => array(
					'rule' => array('inList', array(0, 1)),
					'required' => false,
					'message' => __d('calendars', 'Invalid input. (repetition)'),
				),
			),
			'repeat_freq' => array(
				'rule1' => array(
					'rule' => array('checkRrule'),
					'required' => false,
					'message' => CalendarsComponent::CALENDAR_RRULE_ERROR_HAPPEND,
				),
			),
		));
	}

/**
 * _doMergeDatetimeValidate
 *
 * 日付時刻関連バリデーションのマージ
 *
 * @param bool $isDetailEdit 詳細画面かどうか true=詳細(detail)画面, false=簡易(easy)画面
 * @return void
 */
	protected function _doMergeDatetimeValidate($isDetailEdit) {
		$this->validate = array_merge($this->validate, array(
			'enable_time' => array(
				'rule1' => array(
					'rule' => array('inList', array(0, 1)),
					'required' => false,
					'message' => __d('calendars', 'Invalid input. (time)'),
				),
			),
			'easy_start_date' => array(
				'rule1' => array(
					'rule' => array('date', 'ymd'),	//YYYY-MM-DD
					'required' => !$isDetailEdit,
					'allowEmpty' => $isDetailEdit,
					'message' => __d('calendars', 'Invalid input. (year/month/day)'),
				),
			),
			'easy_hour_minute_from' => array(
				'rule1' => array(
					'rule' => array('datetime'), //YYYY-MM-DD hh:mm
					'required' => false,
					'allowEmpty' => true,
					'message' => __d('calendars', 'Invalid input. (start time)(easy edit mode)'),
				),
				'rule2' => array(
					'rule' => array('checkReverseStartEndTime', 'easy'), //YYYY-MM-DD hh:mm
					'message' => __d('calendars', 'Invalid input. (start time and end time)(easy edit mode)'),
				),
			),
			'easy_hour_minute_to' => array(
				'rule1' => array(
					'rule' => array('datetime'), //YYYY-MM-DD hh:mm
					'required' => false,
					'allowEmpty' => true,
					'message' => __d('calendars', 'Invalid input. (end time)'),
				),
			),
			'detail_start_datetime' => array(
				'rule1' => array(
					'rule' => array('customDatetime', 'detail'), //YYYY-MM-DD or YYYY-MM-DD hh:mm
					'message' => __d('calendars', 'Invalid input. (start time)'),
				),
				'rule2' => array(
					'rule' => array('checkReverseStartEndDateTime', 'detail'),
					'message' => __d('calendars', 'Invalid input. (start day (time) and end day (time))'),
				),
			),
			'detail_end_datetime' => array(
				'rule1' => array(
					'rule' => array('customDatetime', 'detail'), //YYYY-MM-DD or YYYY-MM-DD hh:mm
					'message' => __d('calendars', 'Invalid input. (end date)'),
				),
			),
		));
	}

/**
 * _doMergeTitleValidate
 *
 * タイトル関連バリデーションのマージ
 *
 * @param bool $isDetailEdit 詳細画面かどうか true=詳細(detail)画面, false=簡易(easy)画面
 * @return void
 */
	protected function _doMergeTitleValidate($isDetailEdit) {
		$this->validate = array_merge($this->validate, array(
			'title' => array(
				'rule1' => array(
					'rule' => array('notBlank'),
					'required' => true,
					'message' => __d('calendars', 'Invalid input. (plan title)'),
				),
				'rule2' => array(
					'rule' => array('maxLength', CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
					'message' => sprintf(__d('calendars',
						'%d character limited. (plan title)'), CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
				),
			),
			'title_icon' => array(
				'rule2' => array(
					'rule' => array('maxLength', CalendarsComponent::CALENDAR_VALIDATOR_GENERAL_VCHAR_LEN),
					'required' => false,
					'allowEmpty' => true,
					'message' => sprintf(__d('calendars',
						'%d character limited. (title icon)'),
						CalendarsComponent::CALENDAR_VALIDATOR_GENERAL_VCHAR_LEN),
				),
			),
		));
	}

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
		//CakeLog::debug("request_data[" . print_r($this->data, true) . "]");
		$isDetailEdit = (isset($this->data['CalendarActionPlan']['is_detail']) &&
			$this->data['CalendarActionPlan']['is_detail']) ? true : false;
		//$this->_doMergeDisplayParamValidate($isDetailEdit);	//画面パラメータ関連validation
		$this->_doMergeTitleValidate($isDetailEdit);	//タイトル関連validation
		$this->_doMergeDatetimeValidate($isDetailEdit);	//日付時刻関連validation
		$this->validate = array_merge($this->validate, array(	//コンテンツ関連validation
			'plan_room_id' => array(
				'rule1' => array(
					'rule' => array('allowedRoomId'),
					'required' => true,
					'allowEmpty' => false,
					'message' => __d('calendars', 'Invalid input. (authority)'),
				),
				'rule2' => array(
					'rule' => array('checkShareUser'),
					'message' => __d('calendars', 'You can not set sharers.'),
				),
			),
			'timezone_offset' => array(
				'rule1' => array(
					'rule' => array('allowedTimezoneOffset'),
					'required' => false,
					'message' => __d('calendars', 'Invalid input. (timezone)'),
				),
			),
			'is_detail' => array(
				'rule1' => array(
					'rule' => array('inList', array(0, 1)),
					'required' => false,
					'message' => __d('calendars', 'Invalid input. (detail)'),
				),
			),
			'location' => array(
				'rule1' => array(
					'rule' => array('maxLength', CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
					'required' => false,
					'message' => sprintf(__d('calendars',
						'%d character limited. (location)'), CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
				),
			),
			'contact' => array(
				'rule1' => array(
					'rule' => array('maxLength', CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
					'required' => false,
					'message' => sprintf(__d('calendars', '%d character limited. (contact)'),
						CalendarsComponent::CALENDAR_VALIDATOR_TITLE_LEN),
				),
			),
			'description' => array(
				'rule1' => array(
					'rule' => array('maxLength', CalendarsComponent::CALENDAR_VALIDATOR_TEXTAREA_LEN),
					'required' => false,
					//'message' => sprintf(__d('calendars', '連絡先は最大 %d 文字です。'),
					'message' => sprintf(__d('calendars', '%d character limited. (detail)'),
						CalendarsComponent::CALENDAR_VALIDATOR_TEXTAREA_LEN),
				),
			),
			//statusの値は カレンダー独自status取得関数getStatusで取ってくるので省略
		));
		$this->_doMergeRruleValidate($isDetailEdit);	//繰返し関連validation

		return parent::beforeValidate($options);
	}

/**
 * saveCalendarPlan
 *
 * 予定データ登録
 *
 * @param array $data POSTされたデータ
 * @param string $procMode procMode
 * @param bool $isOriginRepeat isOriginRepeat
 * @param bool $isTimeMod isTimeMod
 * @param bool $isRepeatMod isRepeatMod
 * @param int $createdUserWhenUpd createdUserWhenUpd
 * @param bool $isMyPrivateRoom isMyPrivateRoom
 * @return bool 成功時true, 失敗時false
 * @throws InternalErrorException
 */
	public function saveCalendarPlan($data, $procMode,
		$isOriginRepeat, $isTimeMod, $isRepeatMod, $createdUserWhenUpd, $isMyPrivateRoom) {
		// 設定画面を表示する前にこのルームのブロックがあるか確認
		// 万が一、まだ存在しない場合には作成しておく
		// ここで作成されるのはカレンダーが配置されているルームのブロックであって
		// カレンダー予定対象のルームのブロックではないことに注意
		// 予定対象ルームのブロックはこの下の処理でCalendarEventが作成されるときに作成されます。
		$data = $this->Calendar->prepareBlockWithoutFrame($data);

		$this->begin();
		$eventId = 0;
		$this->aditionalData = $data['WorkflowComment'];

		try {
			//備忘）
			//選択したTZを考慮したUTCへの変換は、この
			//convertToPlanParamFormat()の中でcallしている、
			//_setAndMergeDateTime()がさらにcallしている、
			//_setAndMergeDateTimeDetail()で行っています。
			//
			$planParam = $this->convertToPlanParamFormat($data);

			//call元の_calendarPost()の最初でgetStatus($data)の結果が
			//$data['CalendarActionPlan']['status']に代入されているので
			//ここは、その値を引っ張ってくるだけに直す。
			////$status = $this->getStatus($data);
			$status = $data['CalendarActionPlan']['status'];

			//if ($status === false) { getStatus内でInternalErrorExceptionしている
			//	CakeLog::error("save_Nより、statusが決定できませんでした。data[" .
			//		serialize($data) . "]");
			//	throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			//}
			if ($procMode === CalendarsComponent::PLAN_ADD) {
				//新規追加処理
				//CakeLog::debug("DBG: PLAN_ADD case.");

				//$this->insertPlan($planParam);
				$eventId = $this->insertPlan($planParam, $isMyPrivateRoom);
			} else {	//PLAN_EDIT
				//変更処理
				//CakeLog::debug("DBG: PLAN_MODIFY case.");

				//現予定を元に、新世代予定を作成する
				//1. statusは、cal用新statusである。
				//2. createdUserWhenUpdは、変更後の公開ルームidが「元予定生成者の＊ルーム」から「編集者・承認者
				//(＝ログイン者）のプライベート」に変化していた場合、created_userを元予定生成者から編集者・承認者
				//(＝ログイン者）に変更する例外処理用。
				//3. isMyPrivateRoomは、変更後の公開ルームidが「編集者・承認者（＝ログイン者）のプライベート」以外の場合、
				//仲間の予定はプライベートの時のみ許される子情報なので、これらはcopy対象から外す（stripする）例外処理用。
				//
				$newPlan = $this->makeNewGenPlan($data, $status, $createdUserWhenUpd, $isMyPrivateRoom);

				$editRrule = $this->getEditRruleForUpdate($data);

				$isInfoArray = array($isOriginRepeat, $isTimeMod, $isRepeatMod, $isMyPrivateRoom);
				$eventId = $this->updatePlan($planParam, $newPlan, $status, $isInfoArray, $editRrule,
					$createdUserWhenUpd);
			}

			if ($this->isOverMaxRruleIndex) {
				CakeLog::info("save(CalendarPlanの内部でカレンダーのrruleIndex回数超過が" .
				"発生している。強制rollbackし、画面にINDEXオーバーであることを" .
				"出す流れに乗せ、例外は投げないようにする。");
				$this->rollback();
				return false;
			}

			// taskや施設予約登録とのLinkで登録された予定は、planParamの
			// modelとcontent_keyに
			// 値が入っており、その時は承認メール、公開通知メール送信、および
			// 新着通知はしてはいけない。(NC2と同様の仕様)
			//
			if (empty($planParam['model']) && empty($planParam['content_key'])) {
				// 承認メール、公開通知メールの送信
				$this->sendWorkflowAndNoticeMail($eventId, $isMyPrivateRoom);

				$this->saveCalendarTopics($eventId);

				$this->_enqueueEmail($data);
			}

			$this->commit();

		} catch (Exception $ex) {

			$this->rollback($ex);

			return false;
		}
		return $eventId;
	}

/**
 * convertToPlanParamFormat
 *
 * 予定データ登録
 *
 * @param array $data POSTされたデータ
 * @return mixed 成功時$planParamデータ
 * @throws InternalErrorException
 */
	public function convertToPlanParamFormat($data) {
		$planParam = array();
		try {
			$model = ClassRegistry::init('Calendars.Calendar');
			if (!($calendar = $model->findByBlockKey($data['Block']['key']))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$planParam['calendar_id'] = isset($calendar[$model->alias]['id'])
				? $calendar[$model->alias]['id']
				: null;

			////statusは、上流の_calendarPost()直後でカレンダー独自status取得・代入
			////が実行され、$data['CalendarAtionPlan']['status']にセットされているので
			////単純なcopyの方に移動させた。
			////$planParam['status'] = $this->getStatus($data);
			$planParam['language_id'] = Current::read('Language.id');
			$planParam['room_id'] = $data[$this->alias]['plan_room_id'];
			$planParam['timezone_offset'] = $this->_getTimeZoneOffsetNum(
				$data[$this->alias]['timezone_offset']);
			$planParam = $this->_setAndMergeDateTime($planParam, $data);
			$planParam = $this->_setAndMergeRrule($planParam, $data);

			$shareUsers = [];
			if (isset($data['GroupsUser'])) {
				foreach ($data['GroupsUser'] as $groupUser) {
					$shareUsers[] = $groupUser['user_id'];
				}
			}
			$myUserId = Current::read('User.id');
			$newShareUsers = array();
			foreach ($shareUsers as $user) {
				if ($user == $myUserId) {
					CakeLog::info('予定を共有する人に自分自身 user_id[' .
						$user . ']がいます。自分自身は除外します。');
					continue;
				}
				$newShareUsers[] = $user;
			}
			$planParam['share_users'] = $newShareUsers;

			// tasksや施設予約登録のLinkで登録された予定の場合、
			// planParamのmodelとcontent_keyに値を記録し、CalendarEventContentに繋げる。
			//
			if (! empty($data['CalendarActionPlanForLink']['model']) &&
				! empty($data['CalendarActionPlanForLink']['content_key'])) {
				$planParam['model'] = $data['CalendarActionPlanForLink']['model'];
				$planParam['content_key'] = $data['CalendarActionPlanForLink']['content_key'];
			}

			//単純なcopyでＯＫな項目群
			$fields = array(
				'title', 'title_icon',		//FIXME: insert/update側に追加実装しないといけない項目
				'location', 'contact', 'description',
				'enable_email', 'email_send_timing', 'status',
			);
			foreach ($fields as $field) {
				$planParam[$field] = $data[$this->alias][$field];
			}

			//他の機構で渡さないといけないデータはここでセットすること
			//

			//カレンダー用のワークフロー
			//なお、$planParam['model']にLink元の他pluginモデル名が有るときは、
			//カレンダー用ではなく、そのpluginモデル用のWorkFlowコメントであり、
			//カレンダーのWFコメントとして間違ってセットしないように注意。
			//
			$planParam[CalendarsComponent::ADDITIONAL] = array();
			if (isset($data['WorkflowComment']) && empty($planParam['model'])) {
				//ワークフローコメントをセットする。
				$planParam[CalendarsComponent::ADDITIONAL]['WorkflowComment'] = $data['WorkflowComment'];
				//ワークフローコメントがsave時Block.keyも一緒に必要としてるので、セットする。
				$planParam[CalendarsComponent::ADDITIONAL]['Block'] = array();
				$planParam[CalendarsComponent::ADDITIONAL]['Block']['key'] = Current::read('Block.key');
			}
		} catch(Exception $ex) {
			//パラメータ変換のどこかでエラーが発生
			CakeLog::error($ex->getMessage());
			throw($ex);	//再throw
		}

		return $planParam;
	}

/**
 * getStatus
 *
 * WorkflowStatus値の取り出し
 *
 * @param array $data POSTされたデータ
 * @return string 成功時 $status, 失敗時 例外をthrowする。
 * @throws InternalErrorException
 */
	public function getStatus($data) {
		$keys = array_keys($data);
		foreach ($keys as $key) {
			if (preg_match('/^save_(\d+)$/', $key, $matches) === 1) {
				//return $matches[1];

				////////////////////////////////////////
				//カレンダーでのstatus取得の考え方)
				//save_NのNをまず取り出し、下記ルールを適用。空間によってはstatusを切り替える。
				//
				//status が、STATUS_PUBLISHED（1）承認済＝発行済 又は STATUS_APPROVAL_WAITING （2）承認待ち の時
				//＝＞status値は、現ユーザが指定空間でcontent publish権限あるならPUBLISHED、権限ないならAPPROVED
				//status が、STATUS_IN_DRAFT（3） 一時保存 又は STATUS_DISAPPROVED（4）差し戻し の時
				//＝＞status値は、そのまま使う。

				$status = $matches[1];
				$roomId = $data['CalendarActionPlan']['plan_room_id'];
				$checkStatus = array(
					WorkflowComponent::STATUS_PUBLISHED, WorkflowComponent::STATUS_APPROVAL_WAITING
				);
				if (in_array($status, $checkStatus)) {
					$status = WorkflowComponent::STATUS_APPROVAL_WAITING;
					if (CalendarPermissiveRooms::isPublishable($roomId)) {
						$status = WorkflowComponent::STATUS_PUBLISHED;
					}
				}
				return $status;
			}
		}
		//マッチするものが無い場合例外throw
		throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
	}

/**
 * _getTimeZoneOffsetNum
 *
 * timezoneID文字列(ex.Asia/Tokyo)からタイムゾーン数値(ex.-12.0,- 12.0)に変換
 *
 * @param string $timezoneOffset タイムゾーンオフセット文字列
 * @return float 成功時 対応するタイムゾーンオフセット数値, 失敗時 例外をthrowする。
 * @throws InternalErrorException
 */
	protected function _getTimeZoneOffsetNum($timezoneOffset) {
		//$tzTblの形式 '_TZ_GMTP9' => array("(GMT+9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk", 9.0, "Asia/Tokyo"),
		$tzTbl = CalendarsComponent::getTzTbl();
		foreach ($tzTbl as $tzData) {
			if ($tzData[2] === $timezoneOffset) {
				return $tzData[1];
			}
		}
		//マッチするものが無い場合例外throw
		throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
	}

/**
 * enqueueEmail
 *
 * メール通知がonの場合、通知時刻等を指定したデータをMailキューに登録する。
 *
 * @param array $data POSTされたデータ
 * @return void 失敗時 例外をthrowする.
 * @throws InternalErrorException
 */
	protected function _enqueueEmail($data) {
		//if ($data[$this->alias]['enable_email']) {
		//	$[email_send_timing] => 60
		//	FIXME: email_send_timingの値をつかって、Mailキューに登録する。
		//}
	}

/**
 * proofreadValidationErrors
 *
 * validationErrors配列の内、対象項目とmessageを動的に校正する。(主にrruleの複合validate対応)
 *
 * @param Model &$model モデル
 * @return array
 */
	public function proofreadValidationErrors(&$model) {
		$msg = isset($model->validationErrors['repeat_freq'][0])
			? $model->validationErrors['repeat_freq'][0]
			: null;
		if ($msg === CalendarsComponent::CALENDAR_RRULE_ERROR_HAPPEND) {
			unset($model->validationErrors['repeat_freq']);
			//CakeLog::debug("DBG: proofread count[" . count($model->calendarProofreadValidationErrors) . "]");
			if (count($model->calendarProofreadValidationErrors) > 0) {
				$model->validationErrors = array_merge($model->validationErrors,
					$model->calendarProofreadValidationErrors);
			}
		}
	}

/**
 * getProcModeOriginRepeatAndModType
 *
 * 追加・変更、元データ繰返し有無、及び時間・繰返し系変更タイプの判断処理
 *
 * @param array $data $this->request->data配列が渡される
 * @param array $originEvent 変更元のevent関連データ
 * @param string $completedRrule すでに完成しているrrule文字列（''以外の時に有効）
 * @return array 処理モード、元データ繰返し有無、時間系変更有無、繰返し系変更有無を配列で返す。
 */
	public function getProcModeOriginRepeatAndModType($data, $originEvent, $completedRrule = '') {
		$cap = $data['CalendarActionPlan'];

		////////////////////////////////
		//追加処理か変更処理かの判断
		//
		$procMode = CalendarsComponent::PLAN_ADD;
		if (!empty($cap['origin_event_id'])) {
			$procMode = CalendarsComponent::PLAN_EDIT;
		}

		////////////////////////////////
		//元データが繰返しタイプかどうかの判断
		$isOriginRepeat = false;
		if (isset($cap['origin_num_of_event_siblings']) &&
			$cap['origin_num_of_event_siblings'] > 1) {
			$isOriginRepeat = true;
		}

		////////////////////////////////
		//変更内容が、時間系の変更を含むかどうかの判断
		//（Googleカレンダーの考え方の導入）
		//
		$timeModCnt = 0;
		if (!empty($originEvent)) {
			//１）タイムゾーンの比較
			$this->__compareTz($cap, $originEvent, $timeModCnt);

			//２）日付時刻の比較
			//入力されたユーザ日付（時刻）を、選択TZを考慮し、サーバ系日付時刻に直してから比較する。
			$this->__compareDatetime($cap, $originEvent, $timeModCnt);
		}
		$isTimeMod = false;
		if ($timeModCnt) {	//1箇所以上変化があればtrueにする。
			$isTimeMod = true;
		}

		////////////////////////////////
		//変更内容が、繰返し系の変更を含むかどうかの判断
		//（Googleカレンダーの考え方の導入）
		//
		$repeatModCnt = 0;
		if (!empty($originEvent)) {
			//１）繰返しの比較
			$cru = new CalendarRruleUtil();

			//POSTされたデータよりrrule配列を生成する。
			$workParam = array();
			$workParam = $this->_setAndMergeRrule($workParam, $data, $completedRrule);
			$capRrule = $cru->parseRrule($workParam['rrule']);

			//eventの親rruleモデルよりrruleを取り出し配列化する。
			$originRrule = $cru->parseRrule($originEvent['CalendarRrule']['rrule']);

			//CakeLog::debug("DBG: capRrule[" . serialize($capRrule) .
			//	"] VS originRrule[" . serialize($originRrule) . "]");
			$diff1 = $this->__arrayRecursiveDiff($capRrule, $originRrule);
			$diff2 = $this->__arrayRecursiveDiff($originRrule, $capRrule);
			if (empty($diff1) && empty($diff2)) {
				//a集合=>b集合の差集合、b集合=>a集合の差集合、ともに空なので
				//集合要素に差はない、と判断する。
			} else {
				//差がみつかったので、繰返しに変更あり。
				//CakeLog::debug("DBG: 差がみつかったので、繰返しに変更あり。");
				++$repeatModCnt;
				//CakeLog::debug("DBG 繰返しに変化あり! capRrule[" . serialize($capRrule) .
				//"] VS originRrule[" . serialize($originRrule) . "]");
			}
		}
		$isRepeatMod = false;
		if ($repeatModCnt) {	//1箇所以上変化があればtrueにする。
			$isRepeatMod = true;
		}

		return array($procMode, $isOriginRepeat, $isTimeMod, $isRepeatMod);
	}

/**
 * __compareTz
 *
 * タイムゾーンの比較
 *
 * @param array $cap $data['CalendarActionPlan']情報
 * @param array $originEvent 元イベント関連情報
 * @param int &$timeRepeatModCnt 変更数。タイムゾーンが変更されいていたら１加算する。
 * @return void
 */
	private function __compareTz($cap, $originEvent, &$timeRepeatModCnt) {
		$tzTbl = CalendarsComponent::getTzTbl();
		$originTzId = '';
		foreach ($tzTbl as $tzInfo) {
			//dobule と stringで、型が違うので == で比較すること
			if ($tzInfo[CalendarsComponent::CALENDAR_TIMEZONE_OFFSET_VAL] ==
			$originEvent['CalendarEvent']['timezone_offset']) {
				$originTzId = $tzInfo[CalendarsComponent::CALENDAR_TIMEZONE_ID];
				break;
			}
		}
		if ($originTzId != $cap['timezone_offset']) {
			//選択したＴＺが変更されている。
			//CakeLog::debug("DBG: 選択したＴＺが変更されている。");
			++$timeRepeatModCnt;
			//CakeLog::debug("DBG: TZに変更あり！ originTzId=[" . $originTzId .
			//	"] VS cap[timezone_offset]=[" . $cap['timezone_offset'] . "]");
		}
	}

/**
 * __compareDatetime
 *
 * 日付時刻の比較
 * 入力されたユーザ日付（時刻）を、選択TZを考慮し、サーバ系日付時刻に直してから比較する。
 *
 * @param array $cap $data['CalendarActionPlan']情報
 * @param array $originEvent 元イベント関連情報
 * @param int &$timeRepeatModCnt 変更数。日付時刻情報が変更されいていたら１加算する。
 * @return void
 */
	private function __compareDatetime($cap, $originEvent, &$timeRepeatModCnt) {
		if ($cap['enable_time']) {
			//開始ー終了. "YYYY-MM-DD hh:mm" - "YYYY-MM-DD hh:mm"
			//
			//FIXME:  YYYY-MM-DD hh:mm のはずだが、手入力の時も問題ないか要確認。
			$nctm = new NetCommonsTime();

			$serverStartDatetime = $nctm->toServerDatetime($cap['detail_start_datetime'] . ':00',
				$cap['timezone_offset']);
			$startDate = CalendarTime::stripDashColonAndSp(substr($serverStartDatetime, 0, 10));
			$startTime = CalendarTime::stripDashColonAndSp(substr($serverStartDatetime, 11, 8));
			$capDtstart = $startDate . $startTime;

			$serverEndDatetime = $nctm->toServerDatetime(
				$cap['detail_end_datetime'] . ':00', $cap['timezone_offset']);
			$endDate = CalendarTime::stripDashColonAndSp(substr($serverEndDatetime, 0, 10));
			$endTime = CalendarTime::stripDashColonAndSp(substr($serverEndDatetime, 11, 8));
			$capDtend = $endDate . $endTime;
		} else {
			//終日指定
			//CalendarsAppMode.phpの_setAndMergeDateTimeEasy()の終日タイプと同様処理をする。
			//
			//FIXME:  YYYY-MM-DDのはずだが、手入力の時も問題ないか要確認.
			$ymd = substr($cap['detail_start_datetime'], 0, 10);	//YYYY-MM-DD
			list($serverStartDateZero, $serverNextDateZero) =
				(new CalendarTime())->convUserDate2SvrFromToDateTime(
					$ymd, $cap['timezone_offset']);
			$startDate = CalendarTime::stripDashColonAndSp(substr($serverStartDateZero, 0, 10));
			$startTime = CalendarTime::stripDashColonAndSp(substr($serverStartDateZero, 11, 8));
			$capDtstart = $startDate . $startTime;

			$endDate = CalendarTime::stripDashColonAndSp(substr($serverNextDateZero, 0, 10));
			$endTime = CalendarTime::stripDashColonAndSp(substr($serverNextDateZero, 11, 8));
			$capDtend = $endDate . $endTime;
		}
		if ($capDtstart == $originEvent['CalendarEvent']['dtstart'] &&
			$capDtend == $originEvent['CalendarEvent']['dtend']) {
			//サーバ日付時間はすべて一致。
		} else {
			//サーバ日付時刻に変更あり。
			//CakeLog::debug("DBG: サーバ日付時刻に変更あり。");
			++$timeRepeatModCnt;
			/*
			CakeLog::debug("DBG: dtstar,dtendに変更あり！ POSTオリジナル enable_time[" .
				$cap['enable_time'] . "] detail_start_datetime[" . $cap['detail_start_datetime'] .
				"] detail_end_datetime[" . $cap['detail_end_datetime'] .
				"] timezone_offset[" . $cap['timezone_offset'] . "]  => サーバ系 capDtstart[" .
				$capDtstart . "] capDtend[" . $capDtend . "] VS origin dtstart[" .
				$originEvent['CalendarEvent']['dtstart'] . "] dtend[" .
				$originEvent['CalendarEvent']['dtend'] . "]");
			*/
		}
	}

/**
 * __arrayRecursiveDiff
 *
 * ２配列の集合の比較
 *
 * @param array $aArray1 配列１
 * @param array $aArray2 配列２
 * @return array 配列１の内、配列２にふくまれてない要素を配列で返す。
 */
	private function __arrayRecursiveDiff($aArray1, $aArray2) {
		$aReturn = array();
		foreach ($aArray1 as $mKey => $mValue) {
			if (array_key_exists($mKey, $aArray2)) {
				if (is_array($mValue)) {
					$aRecursiveDiffResult = $this->__arrayRecursiveDiff($mValue, $aArray2[$mKey]);
					if (count($aRecursiveDiffResult)) {
						$aReturn[$mKey] = $aRecursiveDiffResult;
					}
				} else {
					if ($mValue != $aArray2[$mKey]) {
						$aReturn[$mKey] = $mValue;
					}
				}
			} else {
				$aReturn[$mKey] = $mValue;
			}
		}
		return $aReturn;
	}
}
