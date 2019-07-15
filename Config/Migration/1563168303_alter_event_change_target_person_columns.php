<?php
class AlterEventChangeTargetPersonColumns extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_event_change_target_person_columns';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'calendar_events' => array(
					'target_person_grade' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '対象者（テキスト）', 'charset' => 'utf8', 'after' => 'location'),
					'target_person_class_room' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'target_person_grade'),
				),
			),
			'drop_field' => array(
				'calendar_events' => array('target_person'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'calendar_events' => array('target_person_grade', 'target_person_class_room'),
			),
			'create_field' => array(
				'calendar_events' => array(
					'target_person' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '対象者（テキスト）', 'charset' => 'utf8'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
