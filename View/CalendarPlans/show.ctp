<?php
?>
<?php echo $this->element('Calendars.scripts'); ?>

<article ng-controller="CalendarsDetailEdit" class="block-setting-body">


<article class="block-setting-body">

<div class='col-xs-12 col-sm-10 col-sm-offset-1 h3'>カレンダー</div>

<!-- <div class="panel panel-default"> -->

<!-- <form class="form-horizontal"> --><!-- これで<div class-"form-group row"のrowを省略できる -->

<!-- <div class="panel-body"> -->



<div name="dispTitle">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">

	<label><?php echo __d('calendars', '件名'); ?></label>
	<div class="clearfix"></div>

	<i><img style='width:1.8em; height:1.3em;' src='/calendars/img/svg/icon-weather3.svg' /></i>
	<span>湯島天神に参拝</span>
	<div class="clearfix"></div>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->


<div name="dispRoomForOpen">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '公開対象'); ?></label>
	<div class="clearfix"></div>
	<span class='calendar-plan-mark calendar-plan-mark-private'></span><span><?php echo __d('calendars', '自分自身'); ?></span>
<div class="clearfix"></div>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->

<div name="sharePersons">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '予定を共有する人'); ?></label>
<div class="clearfix"></div>
	<span class='calendar-share-person'>武蔵坊弁慶</span>
	<span class='calendar-share-person'>山田太郎</span>
	<span class='calendar-share-person'>鈴木イチロー</span>
	<span class='calendar-share-person'>浅田まお</span>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->


<div name="showPlace">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '場所'); ?></label>
	<div class="clearfix"></div>
	<span>東京都文京区</span>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->


<div name="showPlace">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '連絡先'); ?></label>
	<div class="clearfix"></div>
	<span>橋本携帯電話</span>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->


<div name="description">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '詳細'); ?></label>
	<div class="clearfix"></div>
<!-- ここにwysiwyigの内容がきます -->
	<div>
	ここに<span class="h2">詳細</span>がはいります。ここに<span style="color:red">詳細</span>がはいります。
	<br/>
	ここに<span class="h3">詳細</span>がはいります。ここに<span style="color:blue">詳細</span>がはいります。
	<br/>
	ここに<span class="h4">詳細</span>がはいります。ここに<span style="color:green">詳細</span>がはいります。
	</div>

<!-- ここにwysiwyigの内容がきます -->
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->

<div name="repeat">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '繰返し'); ?></label>
	<div class="clearfix"></div>
	<div><span style="margin-right: 1em">毎週</span><span>月曜日,火曜日</span></div>
	<div>2016年2月29日まで</div>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->

<div name="writer">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '記入者'); ?></label>
	<div class="clearfix"></div>
	<span>橋本俊秀</span>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->

<div name="updateDate">
<div class="col-xs-12 col-sm-10 col-sm-offset-1 calendar-eachplan-box">
	<label><?php echo __d('calendars', '更新日'); ?></label>
	<div class="clearfix"></div>
	<span>2016-02-07 15:25:30</span>
</div><!-- col-sm-10おわり -->
</div><!-- おわり-->

<!-- </div> --><!-- panel-bodyを閉じる -->

<!-- <div class="panel-footer text-center"> -->
<div class="text-center">
<button name="cancel" onclick="location.href = '/faqs/faq_blocks/index/5?frame_id=11'" ng-click="sending=true" ng-disabled="sending" class="btn btn-default btn-workflow " type="button">
	<span class="glyphicon glyphicon-remove"></span>
	<?php echo __d('calendars', 'キャンセル'); ?>
</button>
<?php
	echo "<button type='button' ng-click=\"showRepeatConfirm('" . $frameId . "','" . $isRepeat . "','edit')\" class='btn btn-primary btn-workflow' title='" . __d('calendars', '編集') . "'>";
?>
    <span class="glyphicon glyphicon-edit"> </span>
</button>

<!-- <hr style="margin-top:0.2em; margin-bottom:0.2em" /> -->
<hr />

<div class="col-xs-12 text-right">
<?php
	echo "<button type='button' ng-click=\"showRepeatConfirm('" . $frameId . "','" . $isRepeat . "','delete')\" class='btn btn-danger btn-workflow' name='delete' title='" . __d('calendars', '削除') . "'>";
?>
    <span class="glyphicon glyphicon-trash"> </span>
</button>
<!-- </div> --><!--panel-footerの閉じるタグ-->
</div>

<!-- </form> --><!--formを閉じる-->

<!-- </div> --><!--panelを閉じる-->

</article>

<script type='text/javascript'>
var mock= {};

mock.elms = document.getElementsByClassName('calendar-specify-a-time_<?php echo $frameId ?>');
mock.fnc =  function(evt) {
	var target = evt.target;

    if ( target.tagName != 'INPUT' ) {
        for( ; ; ) {
            target = target.parentNode;
            if (target.nodeType != Node.ELEMENT_NODE) {
                continue;
            }

            if( target.tagName == 'INPUT') {
                break;
            }

            if( target.tagName == 'BODY') {
                return;
            }
        }
    }

	var elm = (document.getElementsByClassName('calendar-starttime-endtime_<?php echo $frameId; ?>'))[0];

	if (target.checked) {
		//開始時間と終了時間の指定あり
		elm.style.display = 'block';

	} else {
		//開始時間と終了時間の指定なし
		elm.style.display = 'none';
	}

};
for(var i=0; i < mock.elms.length; ++i) {
    mock.elms[i].addEventListener('change', mock.fnc );
}

mock.elms2 = document.getElementsByClassName('calendar-send-a-mail_<?php echo $frameId ?>');
mock.fnc2 =  function(evt) {
	var target = evt.target;

    if ( target.tagName != 'INPUT' ) {
        for( ; ; ) {
            target = target.parentNode;
            if (target.nodeType != Node.ELEMENT_NODE) {
                continue;
            }

            if( target.tagName == 'INPUT') {
                break;
            }

            if( target.tagName == 'BODY') {
                return;
            }
        }
    }

	var elm = (document.getElementsByClassName('calendar-mail-notice_<?php echo $frameId; ?>'))[0];
	if (target.checked) {
		//開始時間と終了時間の指定あり
		elm.style.display = 'block';

	} else {
		//開始時間と終了時間の指定なし
		elm.style.display = 'none';
	}

};
for(var i=0; i < mock.elms2.length; ++i) {
    mock.elms2[i].addEventListener('change', mock.fnc2 );
}





</script>
