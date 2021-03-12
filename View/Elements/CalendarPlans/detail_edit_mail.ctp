<?php
/**
 * 予定編集（メール通知設定部分） template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<?php
	$checkMailStyle = '';
	if (!isset($mailSettingInfo['MailSetting']['is_mail_send']) ||
		$mailSettingInfo['MailSetting']['is_mail_send'] == 0) {
		$checkMailStyle = "style='display: none;'";
	}
?>
<?php
echo $this->NetCommonsForm->hidden('CalendarActionPlan.enable_email', array('value' => false));
echo $this->NetCommonsForm->hidden('CalendarActionPlan.email_send_timing', array('value' => 5));
