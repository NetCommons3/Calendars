<?php
/**
 * 実績の追加（ブログとの連携）ボタン表示ヘルパ
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Class CalendarRelatedBlogHelper
 */
class CalendarRelatedBlogHelper extends AppHelper {

/**
 * @var array Helpers
 */
	public $helpers = [
		'NetCommons.LinkButton'
	];

/**
 * addAchievementButton
 *
 * @param array $event Event
 * @return void
 */
	public function addAchievementButton(array $event) {
		$blogBlockId = $this->__findRelatedBlogBlockId();

		$roomId = $event['CalendarEvent']['room_id'];
		$canEdit = CalendarPermissiveRooms::isEditable($roomId);
		$canCreate = CalendarPermissiveRooms::isCreatable($roomId);
		// 表示ルームにおける自分の権限がeditable以上なら無条件に編集可能
		// creatable のとき=自分が作ったデータならOK
		if ($canEdit ||
			($canCreate && $event['CalendarEvent']['created_user'] == Current::read('User.id'))) {
			if ($blogBlockId !== null) {
				// 実績の追加ボタン
				return $this->LinkButton->add(__d('calendars', 'Add Achievement'), array(
					'plugin' => 'blogs',
					'controller' => 'blog_entries_edit',
					'action' => 'add',
					'block_id' => $blogBlockId,
					'?' => [
						'page_id' => Current::read('Page.id'),
						'event_key' => $event['CalendarEvent']['key']
					]
				));
			}
		}
	}

/**
 * __findRelatedBlogBlockId
 *
 * @return int|null
 */
	private function __findRelatedBlogBlockId() {
		$blogModel = ClassRegistry::init('Blogs.Blog');

		Configure::load('Calendars.related_blog');

		$blogKey = Configure::read('Calendars.relatedBlog.key');
		/** @var Blog $blogModel */
		$blogBlockId = null;
		if ($blogKey) {
			$blogBlockId = $blogModel->findBlockIdByKey($blogKey);
		}
		return $blogBlockId;
	}
}