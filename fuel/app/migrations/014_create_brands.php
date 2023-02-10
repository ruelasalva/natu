<?php

namespace Fuel\Migrations;

class Create_brands
{
	public function up()
	{
		\DBUtil::create_table('brands', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'slug' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'image' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'deleted' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('brands');
	}
}