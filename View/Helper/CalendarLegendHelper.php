<?php
/**
 * Calendar Trun Calendar Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
/**
 * Calendar Turn Calendar Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @package NetCommons\Calendars\View\Helper
 */
class CalendarLegendHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Calendars.CalendarCommon',
		'Calendars.CalendarDaily',
	);

/**
 * getCalendarLegend
 *
 * 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	public function getCalendarLegend($vars) {
		$html = '<div class="panel panel-default calendar-room-legend">';
		$html .= '<div class="panel-body calendar-room-legend">';
		$html .= '<ul class="dl-horizontal list-inline">';

		$html .= $this->_getPublicLegend($vars);
		$html .= $this->_getPrivateLegend($vars);
		$html .= $this->_getGroupRoomLegend($vars);
		$html .= $this->_getMemberLegend($vars);
		$html .= $this->_getDoShareLegend($vars);
		$html .= $this->_getDoneShareLegend($vars);

		$html .= '</ul></div></div>';
		return $html;
	}
/**
 * _getPublicLegend
 *
 * パブリック 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getPublicLegend($vars) {
		$html = $this->_getLegend($vars, Room::PUBLIC_PARENT_ID);
		return $html;
	}
/**
 * _getPrivateLegend
 *
 * プライベート 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getPrivateLegend($vars) {
		$html = '';
		if (in_array(Space::PRIVATE_SPACE_ID, $vars['roomSpaceMaps'])) {
			$html = $this->_getLegend($vars, Room::PRIVATE_PARENT_ID);
		}
		return $html;
	}
/**
 * _getGroupRoomLegend
 *
 * グループルーム 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getGroupRoomLegend($vars) {
		$html = '';
		// 空間的にはグループスペースでルームIDは「全会員」ではないものがあるか
		foreach ($vars['roomSpaceMaps'] as $roomId => $spaceId) {
			if ($spaceId == Space::ROOM_SPACE_ID && $roomId != Room::ROOM_PARENT_ID) {
				$html = $this->_getLegend($vars, $roomId, __d('calendars', 'ルーム'));
				break;
			}
		}
		return $html;
	}
/**
 * _getMemberLegend
 *
 * 全会員 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getMemberLegend($vars) {
		$html = '';
		if (in_array(Space::ROOM_SPACE_ID, $vars['roomSpaceMaps'])) {
			$html = $this->_getLegend($vars, Room::ROOM_PARENT_ID, __d('calendars', '全会員'));
		}
		return $html;
	}
/**
 * _getDoShareLegend
 *
 * 共有した予定 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getDoShareLegend($vars) {
		$html = '';
		// 共有した予定を持てるかどうかはプライベートを持っているかです
		if (in_array(Space::PRIVATE_SPACE_ID, $vars['roomSpaceMaps'])) {
			$html = $this->_getLegend(
				$vars,
				null,
				__d('calendars', '共有した予定'),
				'calendar-plan-mark-share',
				'share'
			);
		}
		return $html;
	}
/**
 * _getDoneShareLegend
 *
 * 仲間の予定 凡例取得
 *
 * @param array $vars カレンンダー情報
 * @return string 凡例HTML
 */
	protected function _getDoneShareLegend($vars) {
		$html = '';
		if (empty(Current::read('User.id'))) {
			return $html;
		}
		$html = $this->_getLegend(
			$vars,
			null,
			__d('calendars', '仲間の予定'),
			'calendar-plan-mark-share'
		);
		return $html;
	}
/**
 * _getLegend
 *
 * 凡例要素取得
 *
 * @param array $vars カレンダー情報
 * @param array $roomId ルームID
 * @param string $spaceName 表示用名称
 * @param string $calendarPlanMark カラーマーククラス名
 * @param string $icon 表示アイコン
 * @return string 凡例HTML
 */
	protected function _getLegend($vars, $roomId, $spaceName = '', $calendarPlanMark = '', $icon = '') {
		if ($calendarPlanMark == '') {
			$calendarPlanMark = $this->CalendarCommon->getPlanMarkClassName($vars, $roomId);
		}
		if ($spaceName == '') {
			$spaceName = $this->CalendarDaily->getSpaceName($vars, $roomId, Current::read('Language.id'));
		}
		$html = '<li><div class="calendar-plan-mark ' . $calendarPlanMark . '">';
		if ($icon != '') {
			$html .= '<span class="glyphicon glyphicon-' . $icon . '" aria-hidden="true"></span>';
		}
		$html .= $spaceName;
		$html .= '</div></li>';
		return $html;
	}
}