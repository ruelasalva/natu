<?php

namespace Fuel\Migrations;

class Create_customers_tax_data
{
	public function up()
	{
		\DBUtil::create_table('customers_tax_data', array(
			'id' => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'customer_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'payment_method_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'cfdi_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'sat_tax_regime_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'state_id' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'business_name' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'rfc' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'street' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'number' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'internal_number' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'colony' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'zipcode' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'city' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'csf' => array('constraint' => 255, 'null' => false, 'type' => 'varchar'),
			'default' => array('constraint' => 11, 'null' => false, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'null' => true, 'type' => 'int', 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('customers_tax_data');
	}
}
