<?php

namespace Fuel\Migrations;

class Create_transfer_data
{
	public function up()
	{
		\DBUtil::create_table('transfer_data', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'info' => array('null' => false, 'type' => 'mediumtext'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('transfer_data');
	}
}
