<?php

namespace Fuel\Migrations;

class Create_sales_addresses
{
	public function up()
	{
		\DBUtil::create_table('sales_addresses', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'state_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'last_name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'phone' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'street' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'number' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'internal_number' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'colony' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'zipcode' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'city' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'details' => array('null' => false, 'type' => 'mediumtext'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('sales_addresses');
	}
}
