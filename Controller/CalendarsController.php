<?php
/**
 * Calendars Controller
 *
 * @property PaginatorComponent $Paginator
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CalendarsAppController', 'Calendars.Controller');
App::uses('NetCommonsTime', 'NetCommons.Utility');
App::uses('CalendarTime', 'Calendars.Utility');

/**
 * CalendarsController
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Calendars\Controller
 */
class CalendarsController extends CalendarsAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Calendars.CalendarRrule',
		'Calendars.CalendarEvent',
		'Calendars.CalendarFrameSetting',
		'Calendars.Calendar',
		'Calendars.CalendarEventShareUser',
		'Calendars.CalendarFrameSettingSelectRoom',
		'Holidays.Holiday',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'NetCommons.Permission' => array(
			//アクセスの権限
			'allow' => array(
				//indexとviewは祖先基底クラスNetCommonsAppControllerで許可済なので、あえて書かない。
				//予定のCRUDはCalendarsPlancontrollerが担当。このcontrollerは表示系conroller.とする。
			),
		),
		'Paginator',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		//'Workflow.Workflow',
		//'NetCommons.Date',
		//'NetCommons.DisplayNumber',
		//'NetCommons.Button',
		'Calendars.CalendarMonthly',
	);

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (! Current::read('Block.id')) {
			//Block.idが無い時は、基底クラスNetCommonsAppControllerのemptyRenderアクションを実行(=autoRenderを止める)
			//した後、falseを返して、filterを失敗させる。結果として、エラー詳細が表示されない真っ白い画面表示となる。
			//
			$this->setAction('emptyRender');
			return false;
		}
	}

/**
 * index
 *
 * @return void
 */
	public function index() {
		$ctpName = '';
		$vars = array();
		if (isset($this->request->params['named']) && isset($this->request->params['named']['style'])) {
			$style = $this->request->params['named']['style'];
		} else {
			//style未指定の場合、CalendarFrameSettingモデルのdisplay_type情報から表示するctpを決める。
			$this->setCalendarCommonCurrent($vars);
			$displayType = Current::read('CalendarFrameSetting.display_type');
			if ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_SMALL_MONTHLY) {
				$style = 'smallmonthly';
			} elseif ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_LARGE_MONTHLY) {
				$style = 'largemonthly';
			} elseif ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_WEEKLY) {
				$style = 'weekly';
			} elseif ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_DAILY) {
				$style = 'daily';
			} elseif ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_TSCHEDULE) {
				$style = 'schedule';
				$this->request->params['named']['sort'] = 'time';	//見なしnamedパラメータセット
			} elseif ($displayType == CalendarsComponent::CALENDAR_DISP_TYPE_MSCHEDULE) {
				$style = 'schedule';
				$this->request->params['named']['sort'] = 'member';	//みなしnamedパラメータセット
			} else {	//月縮小とみなす
				$style = 'smallmonthly';
			}
		}

		$ctpName = $this->getCtpAndVars($style, $vars);

		$frameId = Current::read('Frame.id');
		$languageId = Current::read('Language.id');
		$this->set(compact('frameId', 'languageId', 'vars'));
		$this->render($ctpName);
	}

/**
 * getMonthlyVars
 *
 * 月カレンダー用変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars 月（縮小用）データ
 */
	public function getMonthlyVars($vars) {
		$this->setCalendarCommonVars($vars);
		return $vars;
	}

/**
 * getWeeklyVars
 *
 * 週単位変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars 週単位データ
 */
	public function getWeeklyVars($vars) {
		$this->setCalendarCommonVars($vars);
		return $vars;
	}

/**
 * getDailyListVars
 *
 * 日単位（一覧）用変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars 日単位（一覧）データ
 */
	public function getDailyListVars($vars) {
		$this->setCalendarCommonVars($vars);
		$vars['tab'] = 'list';
		return $vars;
	}

/**
 * getDailyTimelineVars
 *
 * 日単位（タイムライン）用変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars 日単位（タイムライン）データ
 */
	public function getDailyTimelineVars($vars) {
		$this->setCalendarCommonVars($vars);
		$vars['tab'] = 'timeline';
		return $vars;
	}

/**
 * getMemberScheduleVars
 *
 * スケジュール（会員順）用変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars スケジュール（会員順）データ
 */
	public function getMemberScheduleVars($vars) {
		$this->setCalendarCommonVars($vars);
		$vars['sort'] = 'member';
		return $vars;
	}

/**
 * getTimeScheduleVars
 *
 * スケジュール（時間順）用変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars スケジュール（時間順）データ
 */
	public function getTimeScheduleVars($vars) {
		$this->setCalendarCommonVars($vars);
		$vars['sort'] = 'time';
		return $vars;
	}

/**
 * getDailyVars
 *
 * 日次カレンダー変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars 日次カレンダー変数
 */
	public function getDailyVars($vars) {
		if (isset($this->request->params['named']['tab']) && $this->request->params['named']['tab'] === 'timeline') {
			$vars = $this->getDailyTimelineVars($vars);
		} else {
			$vars = $this->getDailyListVars($vars);
		}
		return $vars;
	}

/**
 * getScheduleVars
 *
 * スケジュール変数取得
 *
 * @param array $vars カレンンダー情報
 * @return array $vars スケジュール変数
 */
	public function getScheduleVars($vars) {
		if (isset($this->request->params['named']['sort']) && $this->request->params['named']['sort'] === 'member') {
			$vars = $this->getMemberScheduleVars($vars);
		} else {
			$vars = $this->getTimeScheduleVars($vars);
		}
		return $vars;
	}

/**
 * getCtpAndVars
 *
 * ctpおよびvars取得
 *
 * @param string $style スタイル
 * @param array &$vars カレンダー共通変数
 * @return string ctpNameを格納したstring
 */
	public function getCtpAndVars($style, &$vars) {
		$ctpName = '';
		switch ($style) {
			case 'smallmonthly':
				$ctpName = 'smonthly';
				$vars = $this->getMonthlyVars($vars);	//月カレンダー情報は、拡大・縮小共通
				$vars['style'] = 'smallmonthly';
				break;
			case 'largemonthly':
				$ctpName = 'lmonthly';
				$vars = $this->getMonthlyVars($vars);	//月カレンダー情報は、拡大・縮小共通
				$vars['style'] = 'largemonthly';
				break;
			case 'weekly':
				$ctpName = 'weekly';
				$vars = $this->getWeeklyVars($vars);
				$vars['style'] = 'weekly';
				break;
			case 'daily':
				$ctpName = 'daily';
				$vars = $this->getDailyVars($vars);
				$vars['style'] = 'daily';
				break;
			case 'schedule':
				$ctpName = 'schedule';
				$vars = $this->getScheduleVars($vars);
				$vars['style'] = 'schedule';
				break;
			default:
				//不明時は月（縮小）
				$ctpName = 'smonthly';
				$vars = $this->getMonthlyVars($vars);
				$vars['style'] = 'smallmonthly';
		}

		return $ctpName;
	}
}