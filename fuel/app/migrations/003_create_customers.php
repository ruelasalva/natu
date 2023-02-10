<?php

namespace Fuel\Migrations;

class Create_customers
{
	public function up()
	{
		\DBUtil::create_table('customers', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'user_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'type_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'last_name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'phone' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('customers');
	}
}
