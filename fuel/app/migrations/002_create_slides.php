<?php

namespace Fuel\Migrations;

class Create_slides
{
	public function up()
	{
		\DBUtil::create_table('slides', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'image' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'url' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'order' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('slides');
	}
}
