<?php

namespace Fuel\Migrations;

class Create_sales
{
	public function up()
	{
		\DBUtil::create_table('sales', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'customer_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'payment_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'address_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'total' => array('constraint' => '10,2', 'null' => false, 'type' => 'float'),
			'transaction' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'bill' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'status' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'sale_date' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('sales');
	}
}
