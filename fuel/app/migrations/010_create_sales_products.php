<?php

namespace Fuel\Migrations;

class Create_sales_products
{
	public function up()
	{
		\DBUtil::create_table('sales_products', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'sale_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'product_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'quantity' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'price' => array('constraint' => '10,2', 'null' => false, 'type' => 'float'),
			'total' => array('constraint' => '10,2', 'null' => false, 'type' => 'float'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('sales_products');
	}
}
