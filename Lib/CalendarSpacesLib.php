<?php
/**
 * カレンダーで利用できるスペースに関するクラス
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * カレンダーで利用できるスペースに関するクラス
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Calendars\Lib
 */
class CalendarSpacesLib {

/**
 * カレンダーで利用できるスペースデータを保持
 *
 * @var array
 */
	private static $__spaces = [];

/**
 * カレンダーで利用できるスペースデータを返す
 *
 * @return array
 */
	public static function getSpaces() {
		if (! empty(self::$__spaces)) {
			return self::$__spaces;
		}

		$RoomModel = ClassRegistry::init('Rooms.Room');
		$spaces = $RoomModel->getSpaces();

		$defSpaces = [
			Space::WHOLE_SITE_ID,
			Space::PUBLIC_SPACE_ID,
			Space::PRIVATE_SPACE_ID,
			Space::COMMUNITY_SPACE_ID
		];

		foreach ($spaces as $space) {
			// もとのコードがプライベートとサイト全体のスペース以外を取得していたのでここでも除外する
			$spaceId = $space['Space']['id'];
			if (!in_array($spaceId, $defSpaces, true)) {
				// 定義外のspace_idが入った場合、次へ（rmapのマイポータル等カスタマイズしている際に影響する）
				continue;
			}
			self::$__spaces[$spaceId] = $space;
		}

		return self::$__spaces;
	}

}
