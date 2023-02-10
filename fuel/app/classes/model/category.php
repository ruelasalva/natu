<?php

class Model_Category extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"deleted" => array(
			"label" => "Deleted",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'categories';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'products' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Product',
			'key_to'         => 'subcategory_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

	/* Functions */
	public static function get_menu()
	{
		$categories_info = array();

		$categories = Model_Category::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		foreach($categories as $category)
		{
			$categories_info[] = array(
				'slug' => $category->slug,
				'name' => $category->name
			);
		}

		return $categories_info;
    }

	public static function get_opts()
	{
		$category_opts = array();

		$category_opts += array('todo' => 'Todo');

		$categories = Model_Category::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		foreach($categories as $category)
		{
			$category_opts += array($category->slug => $category->name);
		}

		return $category_opts;
	}

}
