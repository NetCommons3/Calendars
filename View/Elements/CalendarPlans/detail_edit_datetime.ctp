<?php
/**
 * 予定編集（日時指定部分） template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php
	echo $this->CalendarEditDatetime->makeEditDatetimeHiddens(
		array('detail_start_datetime', 'detail_end_datetime')
	);
?>
<?php /* 要注意
 ここの「日指定」「日時指定」のdatetimeのinputは、日時が先で日が後の並びにしなくてはならない
 なんとなれば、ng-initの操作によるパラメータ設定が日時を先にしないとデフォルトの設定にならないから
*/ ?>
<?php /* 期間・時間の指定のチェックボックスがONで期間指定の場合の「開始」部分 */ ?>
<?php //<!--表示条件１START--> ?>
<div ng-show="<?php echo $useTime; ?>" class="col-xs-12 col-sm-5" ng-cloak>
	<?php echo $this->CalendarEditDatetime->makeEditDatetimeHtml(
		$vars,
		'datetime',
		__d('calendars', 'From'),
		'detail_start_datetime',
		'detailStartDatetime',
		'changeDetailStartDatetime'
	); ?>
</div><?php //<!--ng-show 表示条件１END--> ?>

<?php /* 期間・時間の指定のチェックボックスがOFFで終日指定の場合の部分 */ ?>
<?php //<!--表示条件２START--> ?>
<div ng-show="<?php echo '!' . $useTime; ?>" class="col-xs-12 col-sm-5">
	<?php echo $this->CalendarEditDatetime->makeEditDatetimeHtml(
	$vars,
	'date',
	__d('calendars', 'All day'),
	'detail_start_datetime',
	'detailStartDate',
	'changeDetailStartDate'
	); ?>
</div><?php //<!--ng-show 表示条件２END--> ?>


<?php /* 期間・時間の指定のチェックボックスがONで期間指定の場合の「-」部分 */ ?>
<div ng-show="<?php echo $useTime; ?>" class="col-xs-6 col-sm-1 text-center">
	<div style="line-height:4.5em;" class="hidden-xs">
		<?php echo __d('calendars', '-'); ?>
	</div>
</div>


<?php /* 期間・時間の指定のチェックボックスがONで期間指定の場合の「終了」部分 */ ?>
<div ng-show="<?php echo $useTime; ?>" class="col-xs-12 col-sm-5">

	<br class="visible-xs-block">

	<?php echo $this->CalendarEditDatetime->makeEditDatetimeHtml(
		$vars,
		'datetime',
		__d('calendars', 'To'),
		'detail_end_datetime',
		'detailEndDatetime',
		'changeDetailEndDatetime'
	); ?>

	<?php /* 期間・時間の指定のチェックボックスがOFFで終日指定の場合の「終了」部分 */ ?>
	<div ng-hide="1" ng-cloak>
		<?php echo $this->CalendarEditDatetime->makeEditDatetimeHtml(
			$vars,
			'date',
			false,
			'detail_end_datetime',
			'detailEndDate',
			'changeDetailEndDate'
		); ?>
	</div><?php //<!-- ng-hide --> ?>
</div>
<div class='col-xs-12'>
	<?php echo $this->NetCommonsForm->error('CalendarActionPlan.detail_start_datetime'); ?>
</div>
