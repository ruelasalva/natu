<?php

namespace Fuel\Migrations;

class Create_cfdis
{
	public function up()
	{
		\DBUtil::create_table('cfdis', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'code' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('cfdis');
	}
}