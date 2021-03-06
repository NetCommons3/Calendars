<?php
/**
 * Calendar Plan Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Calendar plan Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\View\Helper
 */
class CalendarPlanEditRepeatOptionHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'Form',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
		'NetCommons.Button',
		'NetCommons.TitleIcon',
		'Calendars.CalendarMonthly',
		'Calendars.CalendarCommon',
		'Calendars.CalendarUrl',
	);

/**
 * makeEditRepeatOption
 *
 * @param array $eventSiblings イベント情報
 * @param int $firstSibEventKey イベントKey
 * @param int $firstSibYear デフォルト登録年
 * @param int $firstSibMonth デフォルト登録月
 * @param int $firstSibDay デフォルト登録日
 * @param bool $isRecurrence 繰り返し
 * @return string
 *
 * 速度改善の修正に伴って発生したため抑制
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
	public function makeEditRepeatOption(
		$eventSiblings, $firstSibEventKey, $firstSibYear, $firstSibMonth, $firstSibDay, $isRecurrence) {
		$orgNumOfEvSiblings =
			isset($this->request->data['CalendarActionPlan']['origin_num_of_event_siblings'])
				? $this->request->data['CalendarActionPlan']['origin_num_of_event_siblings']
				: null;
		if (count($eventSiblings) <= 1 && $orgNumOfEvSiblings <= 1) {
			return '';
		}

		$editRrule = 0;
		if (isset($this->request->query['editrrule'])) {
			$editRrule = $this->request->query['editrrule'];
		} elseif (isset($this->request->data['CalendarActionPlan']['edit_rrule'])) {
			$editRrule = $this->request->data['CalendarActionPlan']['edit_rrule'];
		}

		$html = '<div class="form-group" data-calendar-name="RepeatSet">';
		$html .= '<div class="col-xs-12 col-sm-10 col-sm-offset-1">';
		$html .= '<div class="media"><div class="media-left h2">';
		$html .= $this->TitleIcon->titleIcon('/net_commons/img/title_icon/10_070_warning.svg');
		$html .= '</div><div class="media-body">';
		//全選択用に、繰返し先頭eventのeditボタのリンクを生成しておく
		$firstSibEditLink = '';
		//$key = Hash::get($this->_View->viewVars, 'event.CalendarEvent.key');
		if (!empty($firstSibEventKey)) {
			$firstSibEditLink = $this->Button->editLink('', array(
				'controller' => 'calendar_plans',
				'action' => 'edit',
				'key' => $firstSibEventKey,
				'block_id' => '',
				'frame_id' => Current::read('Frame.id'),
				'?' => array(
					'year' => $firstSibYear,
					'month' => $firstSibMonth,
					'day' => $firstSibDay,
					'editrrule' => 2,
				)
			));
			$firstSibEditLink = str_replace('&quot;', '"', $firstSibEditLink);
			$firstSibEditLink = str_replace('&amp;', '&', $firstSibEditLink);
			if (preg_match('/href="([^"]+)"/', $firstSibEditLink, $matches) === 1) {
				$firstSibEditLink = $matches[1];
			}
		}
		$html .= $this->_getMessage($isRecurrence);

		$html .= '</div></div>';

		$html .= '<div class="alert alert-warning">';
		$options = array();
		$options['0'] = __d('calendars', 'only this one');
		if (!$isRecurrence) {
			//「この予定のみ」指定で変更された予定ではないので、1,2も選択肢に加える。
			$options['1'] = __d('calendars', 'all after this one');
			$options['2'] = __d('calendars', 'all');
		}
		$html .= $this->NetCommonsForm->radio('CalendarActionPlan.edit_rrule', $options, array(
			'div' => 'form-inline',
			'value' => $editRrule,
			'ng-model' => 'editRrule',
			'ng-init' => "editRrule = '" . $editRrule . "'",
			'ng-change' => "changeEditRrule('" . $firstSibEditLink . "')",
		));
		if (! $isRecurrence) {
			$html .= '<p class="help-block text-right"><small>';
			$html .= __d('calendars',
				'When you select the [all] will be re-set to the contents is repeated first plan.');
			$html .= '<br />';
			$html .= __d('calendars',
				'If you want to edit without changing the schedule key, select "all after this one" and edit.');
			$html .= '</small></p>';
		}
		$html .= '</div></div></div>';
		return $html;
	}

/**
 * _getMessage
 *
 * @param bool $isRecurrence 繰り返し状況
 * @return string
 */
	protected function _getMessage($isRecurrence) {
		$html = __d('calendars',
			'This plan has been repeatedly set. ' .
			'Select the plan that you want to edit from the following items, ' .
			'Repetation of the plan [only this one] is not displayed.');

		if ($isRecurrence) {
			$html .= __d('calendars', 'Because it was specified in the [only this one], ' .
				'repetation of the plan can not be specified.');
		}
		return $html;
	}
}
