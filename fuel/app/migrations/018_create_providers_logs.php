<?php

namespace Fuel\Migrations;

class Create_providers_logs
{
	public function up()
	{
		\DBUtil::create_table('providers_logs', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'provider' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'type' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'transaction' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'response' => array('null' => false, 'type' => 'mediumtext'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('providers_logs');
	}
}
