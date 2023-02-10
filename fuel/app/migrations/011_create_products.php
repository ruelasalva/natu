<?php

namespace Fuel\Migrations;

class Create_products
{
	public function up()
	{
		\DBUtil::create_table('products', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'category_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'brand_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'slug' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'code' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'image' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'description' => array('null' => false, 'type' => 'mediumtext'),
			'original_price' => array('constraint' => '10,2', 'null' => false, 'type' => 'float'),
			'available' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'status' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'status_index' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'deleted' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('products');
	}
}
