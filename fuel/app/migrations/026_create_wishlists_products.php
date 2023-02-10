<?php

namespace Fuel\Migrations;

class Create_wishlists_products
{
	public function up()
	{
		\DBUtil::create_table('wishlists_products', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'wishlist_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'product_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('wishlists_products');
	}
}