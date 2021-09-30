<?php
/**
 * GetCalendarDailyUrlTest.php
 *
 * @author Japan Science and Technology Agency
 * @author National Institute of Informatics
 * @link http://researchmap.jp researchmap Project
 * @link http://www.netcommons.org NetCommons Project
 * @license http://researchmap.jp/public/terms-of-service/ researchmap license
 * @copyright Copyright 2017, researchmap Project
 */

App::uses('CalendarUrlHelper', 'Calendars.View/Helper');
App::uses('View', 'View');
App::uses('NetCommonsUrl', 'NetCommons.Utility');

class CalendarUrlHelperGetCalendarDailyUrlTest extends CakeTestCase {
	public function testTest(){
		$calendarUrl = new CalendarUrlHelper(new View());
		Current::write('Frame.id', '10');
		$calendarUrl->beforeRender('viewFile');
		$url = $calendarUrl->getCalendarDailyUrl(2000, 3, 10);

		$expected = '/calendars/calendars/index?frame_id=10&style=daily&tab=list&year=2000&month=3&day=10';
		self::assertSame($expected, $url);
	}
}