<?php
/**
 * Calendar Legend Calendar Helper
 *
 * @author Allcreator Co., Ltd. <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('AppHelper', 'View/Helper');
App::uses('Space', 'Rooms.Model');

/**
 * Calendar Legend Calendar Helper
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
 * @param array $vars カレンダー情報
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
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getPublicLegend($vars) {
		if (! $this->_isDisplayLegend($vars, Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID))) {
			return '';
		}
		$html = $this->_getLegend($vars, Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID));
		return $html;
	}
/**
 * _getPrivateLegend
 *
 * プライベート 凡例取得
 *
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getPrivateLegend($vars) {
		$html = '';
		if (! $this->_isDisplayLegend($vars, Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID))) {
			return '';
		}
		if (in_array(Space::PRIVATE_SPACE_ID, $vars['roomSpaceMaps'])) {
			////$html = $this->_getLegend($vars, Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID));

			//Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID)では、各ユーザ毎のprivateRoomIdにはならない。
			//myselfに、各ユーザ毎のprivateRoomIdをセットするように変えたので
			//見直した。
			//
			$html = $this->_getLegend($vars, $vars['myself']);
		}
		return $html;
	}
/**
 * _getGroupRoomLegend
 *
 * グループルーム 凡例取得
 *
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getGroupRoomLegend($vars) {
		$html = '';
		// 空間的にはグループスペースでルームIDは「全会員」ではないものがあるか
		$communitySpaceId = Space::COMMUNITY_SPACE_ID;
		foreach ($vars['roomSpaceMaps'] as $roomId => $spaceId) {
			if ($spaceId == $communitySpaceId && $roomId != Space::getRoomIdRoot($communitySpaceId)) {
				if ($this->_isDisplayLegend($vars, $roomId)) {
					$html = $this->_getLegend($vars, $roomId, __d('calendars', 'Room'));
					break;
				}
			}
		}
		return $html;
	}
/**
 * _getMemberLegend
 *
 * 全会員 凡例取得
 *
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getMemberLegend($vars) {
		$html = '';
		if (! $this->_isDisplayLegend($vars, Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID))) {
			return '';
		}
		if (in_array(Space::COMMUNITY_SPACE_ID, $vars['roomSpaceMaps'])) {
			$html = $this->_getLegend(
				$vars, Space::getRoomIdRoot(Space::COMMUNITY_SPACE_ID), __d('calendars', 'All the members')
			);
		}
		return $html;
	}
/**
 * _getDoShareLegend
 *
 * 共有した予定 凡例取得
 *
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getDoShareLegend($vars) {
		$html = '';
		if (! $this->_isDisplayLegend($vars, Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID))) {
			return '';
		}
		// 共有した予定を持てるかどうかはプライベートを持っているかです
		if (in_array(Space::PRIVATE_SPACE_ID, $vars['roomSpaceMaps'])) {
			$html = $this->_getLegend(
				$vars,
				null,
				__d('calendars', 'Shared plan'),
				'calendar-plan-mark-private',
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
 * @param array $vars カレンダー情報
 * @return string 凡例HTML
 */
	protected function _getDoneShareLegend($vars) {
		$html = '';
		if (! $this->_isDisplayLegend($vars, Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID))) {
			return '';
		}
		$userId = Current::read('User.id');
		if (empty($userId)) {
			return $html;
		}
		$html = $this->_getLegend(
			$vars,
			null,
			__d('calendars', 'Schedule of fellow'),
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
	protected function _getLegend($vars, $roomId, $spaceName = '', $calendarPlanMark = '',
		$icon = '') {
		if ($calendarPlanMark == '') {
			$calendarPlanMark = $this->CalendarCommon->getPlanMarkClassName($vars, null, $roomId);
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

/**
 * _isDisplayLegend
 *
 * 凡例を出してよいか
 * （表示形式設定で首絞めされていたら表示しなくてよい
 *
 * @param array $vars カレンダー情報
 * @param int $id 表示対象ルームID
 * @return bool
 */
	protected function _isDisplayLegend($vars, $id) {
		if (! $vars['CalendarFrameSetting']['is_select_room']) {
			return true;
		}
		if ($id == Space::getRoomIdRoot(Space::PRIVATE_SPACE_ID)) {
			if (! isset($vars['exposeRoomOptions'][$vars['myself']])) {
				return false;
			}
		} elseif ($id == Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID)) {
			$roomIds = array_keys($vars['exposeRoomOptions']);
			foreach ($roomIds as $roomId) {
				if ($vars['spaceNameOfRooms'][$roomId] == 'public') {
					return true;
				}
			}
			return false;
		} else {
			// 表示対象ルームIDの中にあるか
			if (! isset($vars['exposeRoomOptions'][$id])) {
				return false;
			}
		}
		return true;
	}
}
