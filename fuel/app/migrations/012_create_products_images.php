<?php

namespace Fuel\Migrations;

class Create_products_images
{
	public function up()
	{
		\DBUtil::create_table('products_images', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'product_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'image' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'order' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('products_images');
	}
}