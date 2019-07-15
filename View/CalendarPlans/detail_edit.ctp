<?php
/**
 * 予定登録 template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
echo $this->element('Calendars.scripts');

$planRoomId = isset($this->request->data['CalendarActionPlan']['plan_room_id'])
	? $this->request->data['CalendarActionPlan']['plan_room_id']
	: array_keys($exposeRoomOptions);
$jsParameters = array(
	'frameId' => Current::read('Frame.id'),
	'myRoomId' => $myself,
	'canUseGroup' => Current::permission('group_creatable'),
	'planRoomId' => $planRoomId
);
?>

<article ng-controller='CalendarsDetailEdit' class='block-setting-body'
	ng-init="initialize(<?php echo h(json_encode($jsParameters)); ?>)">

	<?php /* 画面見出し */ ?>
	<?php echo $this->element('Calendars.CalendarPlans/detail_edit_heading'); ?>

	<div class='panel panel-default'>
		<?php /* $this->NetCommonsForm->create(); */ ?>
		<?php echo $this->element('Calendars.CalendarPlans/edit_form_create'); ?>
		<?php echo $this->element('Calendars.CalendarPlans/required_hiddens'); ?>
		<?php
			echo $this->element('Calendars.CalendarPlans/detail_edit_hiddens', array(
				'event' => $event, 'eventSiblings' => $eventSiblings, 'firstSib' => $firstSib,
			));
		?>
		<div class='panel-body'>
			<?php $this->NetCommonsForm->unlockField('CalendarActionPlan.edit_rrule'); ?>

			<?php
				//変数の初期化を先頭に集める
				$editRrule = true;

				$firstSibYear = $firstSibMonth = $firstSibDay = $firstSibEventId = $firstSibEventKey = 0;
				if (!empty($this->request->data['CalendarActionPlan']['first_sib_event_id']) &&
					!empty($this->request->data['CalendarActionPlan']['first_sib_event_key']) &&
					!empty($this->request->data['CalendarActionPlan']['first_sib_year']) &&
					!empty($this->request->data['CalendarActionPlan']['first_sib_month']) &&
					!empty($this->request->data['CalendarActionPlan']['first_sib_day'])) {
					$firstSibEventId = $this->request->data['CalendarActionPlan']['first_sib_event_id'];
					$firstSibEventKey = $this->request->data['CalendarActionPlan']['first_sib_event_key'];
					$firstSibYear = $this->request->data['CalendarActionPlan']['first_sib_year'];
					$firstSibMonth = $this->request->data['CalendarActionPlan']['first_sib_month'];
					$firstSibDay = $this->request->data['CalendarActionPlan']['first_sib_day'];
				} else {
					if (!empty($firstSib)) {
						$firstSibEventId = $firstSib['CalendarActionPlan']['first_sib_event_id'];
						$firstSibEventKey = $firstSib['CalendarActionPlan']['first_sib_event_key'];
						$firstSibYear = $firstSib['CalendarActionPlan']['first_sib_year'];
						$firstSibMonth = $firstSib['CalendarActionPlan']['first_sib_month'];
						$firstSibDay = $firstSib['CalendarActionPlan']['first_sib_day'];
					}
				}

				$originEventId = 0;
				if (!empty($event)) {
					$originEventId = $event['CalendarEvent']['id'];
				} else {
					if (!empty($this->request->data['CalendarActionPlan']['origin_event_id'])) {
						$originEventId = $this->request->data['CalendarActionPlan']['origin_event_id'];
					}
				}

				$isRecurrence = false;
				if ((!empty($event) && !empty($event['CalendarEvent']['recurrence_event_id'])) ||
					!empty($this->request->data['CalendarActionPlan']['origin_event_recurrence'])) {
					$isRecurrence = true;
				}

				$useTime = 'useTime[' . $frameId . ']';
			?>

			<?php /* 繰り返しパターンの場合の繰り返し編集オプション */
			echo $this->CalendarPlanEditRepeatOption->makeEditRepeatOption(
				$eventSiblings,
				$firstSibEventKey, $firstSibYear, $firstSibMonth, $firstSibDay, $isRecurrence);
			?>

			<?php /* タイトル入力 */ ?>
			<div class='form-group' data-calendar-name='inputTitle'>
				<div class='col-xs-12'>
					<?php echo $this->element('Calendars.CalendarPlans/edit_title'); ?>
				</div>
			</div><!-- end form-group-->

			<?php /* 期日指定ラベル＋期間・時間指定のチェックボックス */ ?>
			<div class="form-group" data-calendar-name="checkTime">
				<div class='form-inline col-xs-12'>
					<?php
					echo $this->NetCommonsForm->label('', __d('calendars', 'Setting the date'), array(
						'required' => true));
					?>
					&nbsp;
					<?php
					echo $this->NetCommonsForm->checkbox('CalendarActionPlan.enable_time', array(
						'label' => __d('calendars', 'Setting the time'),
						'class' => 'calendar-specify-a-time_' . $frameId,
						'div' => false,
						'ng-model' => $useTime,
						'ng-change' => 'toggleEnableTime(' . $frameId . ')',
						'ng-false-value' => 'false',
						'ng-true-value' => 'true',
						'ng-init' => (($this->request->data['CalendarActionPlan']['enable_time']) ? ($useTime . ' = true') : ($useTime . ' = false')),
						));
					?>
				</div>
 			</div><!-- end form-group-->

			<?php /* 期日入力（終日／開始、終了）*/ ?>
			<div class='form-group' data-calendar-name='inputStartEndDateTime'>
				<?php echo $this->element('Calendars.CalendarPlans/detail_edit_datetime', array('useTime' => $useTime)); ?>
			</div><!-- form-group name="inputStartEndDateTime"おわり -->

			<?php /* 繰り返し設定 （この予定のみ変更のときは出さない）*/ ?>
			<div class="form-group" data-calendar-name="inputRruleInfo" ng-hide="editRrule==0">
				<div class="col-xs-12">
					<?php echo $this->element('Calendars.CalendarPlans/detail_edit_repeat_items', array('useTime' => $useTime)); ?>
				</div>
			</div><!-- end form-group-->

			<?php /* 場所 */ ?>
			<div class="form-group" data-calendar-name="inputLocation" ng-cloak>
				<div class="col-xs-12">
					<?php echo $this->NetCommonsForm->input('CalendarActionPlan.location', array(
						'type' => 'text',
						'label' => __d('calendars', 'Location'),
						'div' => false,
					)); ?>
				</div>
			</div>

			<?php // 対象者 ?>
			<div class="form-group">
				<div class="col-xs-12">
					<?php echo $this->NetCommonsForm->label(null, __d('calendars', 'Target Person'));?>
					<div>
						<?php echo $this->NetCommonsForm->input('CalendarActionPlan.target_person_grade', [
							'label' => false,
							'placeholder' => __d('calendars', 'Grade'),
							'div' => [
								'class' => 'col-xs-6'
							]
						]);?>
						<?php echo $this->NetCommonsForm->input('CalendarActionPlan.target_person_class_room', [
							'label' => false,
							'placeholder' => __d('calendars', 'Class room'),
							'div' => [
								'class' => 'col-xs-6'
							]
						]);?>
					</div>
				</div>
			</div>
			<?php // 持ち物?>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('CalendarActionPlan.belongings', [
					'label' => __d('calendars', 'Belongings'),
					'div' => [
						'class' => 'col-xs-12'
					]
				]);?>
			</div>

			<?php /* 予定の対象空間選択 */ ?>
			<div class="form-group" data-calendar-name="selectRoomForOpen">
				<div class="col-xs-12">
					<?php
						echo $this->CalendarExposeTarget->makeSelectExposeTargetHtml($event, $frameId, $vars, $exposeRoomOptions, $myself);
					?>
					<?php echo $this->NetCommonsForm->error('CalendarActionPlan.plan_room_id'); ?>
				</div>
			</div><!-- end form-group-->

			<?php /* 予定の共有設定 */ ?>
			<div class="form-group calendar-plan-share_<?php echo $frameId; ?> " data-calendar-name="planShare"
                 ng-cloak ng-show="data.canUseGroup == true && data.planRoomId == <?php echo $myself; ?>">
				<div class="col-xs-12 col-sm-10 col-sm-offset-2">
					<?php echo $this->element('Calendars.CalendarPlans/edit_plan_share', array('shareUsers', $shareUsers)); ?>
				</div><!-- col-sm-10おわり -->
			</div><!-- form-groupおわり-->

			<?php /* メール通知設定 */ ?>
			<?php echo $this->element('Calendars.CalendarPlans/detail_edit_mail'); ?>

			<br />

			<?php /* その他詳細設定 */ ?>
			<div class="form-group">
				<div class="col-xs-12">
					<?php echo $this->element('Calendars.CalendarPlans/detail_edit_etc_details'); ?>
				</div>
			</div>

			<?php /* コメント入力 */ ?>
			<hr />
			<div data-calendar-name="inputCommentArea">
				<div class="col-xs-12">
					<?php echo $this->Workflow->inputComment('CalendarEvent.status'); ?>
				</div><!-- col-xs-12おわり -->
			</div><!-- inputCommentAreaおわり -->

		</div><!-- panel-bodyを閉じる -->

		<div class="panel-footer text-center">
			<?php echo $this->CalendarPlan->makeEditButtonHtml('CalendarActionPlan.status', $vars, $event); ?>
		</div><!--panel-footerの閉じるタグ-->
		<?php echo $this->NetCommonsForm->end(); ?>

		<?php if (isset($event['CalendarEvent']) && ($this->request->params['action'] === 'edit' && $this->CalendarWorkflow->canDelete($event))) : ?>
			<div class="panel-footer text-right">
				<?php
				echo $this->element('Calendars.CalendarPlans/delete_form', array(
					'frameId' => $frameId,
					'event' => $event,
					'capForView' => $capForView,
					'eventSiblings' => $eventSiblings,
					'firstSib' => $firstSib,
					'firstSibYear' => $firstSibYear,
					'firstSibMonth' => $firstSibMonth,
					'firstSibDay' => $firstSibDay,
					'firstSibEventId' => $firstSibEventId,
					'originEventId' => $originEventId,
					'isRecurrence' => $isRecurrence,
				));
				?>
			</div>
		<?php endif; ?>

	</div><!--end panel-->

	<?php /* コメント一覧 */
		echo $this->Workflow->comments();
	?>

</article>
