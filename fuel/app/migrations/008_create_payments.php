<?php

namespace Fuel\Migrations;

class Create_payments
{
	public function up()
	{
		\DBUtil::create_table('payments', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'type_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'token' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'total' => array('constraint' => '10,2', 'null' => false, 'type' => 'float'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('payments');
	}
}