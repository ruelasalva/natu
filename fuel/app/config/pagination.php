<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.6
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */


return array(

	// the active pagination template
	'active'                      => 'bootstrap-4',

	// default FuelPHP pagination template, compatible with pre-1.4 applications
	'default'                     => array(
		'wrapper'                 => "<div class=\"product-pagination\">\n\t<div class=\"row\">\n\t\t<div class=\"col-xs-12 col-sm-12 text-center\">\n\t\t\t<div class=\"btn-group\">\n\t\t\t\t{pagination}\n\t</div>\n\t\t</div>\n\t\t\t</div>\n</div>\n",

		'first'                   => "{link}",
		'first-marker'            => "&laquo;&laquo;",
		'first-link'              => "\t\t<a class=\"btn btn-default\" href=\"{uri}\">{page}</a>\n",

		'first-inactive'          => "",
		'first-inactive-link'     => "",

		'previous'                => "{link}",
		'previous-marker'         => "Anterior",
		'previous-link'           => "\t\t<a class=\"btn btn-default\" href=\"{uri}\" rel=\"prev\">{page}</a>\n",

		'previous-inactive'       => "{link}",
		'previous-inactive-link'  => "\t\t<a class=\"btn btn-default\" href=\"javascript:return false;\">{page}</a>\n",

		'regular'                 => "{link}",
		'regular-link'            => "\t\t<a class=\"btn btn-default\" href=\"{uri}\">{page}</a>\n",

		'active'                  => "{link}",
		'active-link'             => "\t\t<a class=\"btn btn-primary\" href=\"#\">{page}</a>\n",

		'next'                    => "{link}",
		'next-marker'            => "Siguiente",
		'next-link'               => "\t\t<a class=\"btn btn-default\" href=\"{uri}\" rel=\"next\">{page}</a>\n",

		'next-inactive'           => "{link}",
		'next-inactive-link'      => "\t\t<a class=\"btn btn-default\" href=\"javascript:return false;\">{page}</a>\n",

		'last'                    => "{link}",
		'last-marker'             => "&raquo;&raquo;",
		'last-link'               => "\t\t<a href=\"{uri}\">{page}</a>\n",

		'last-inactive'           => "",
		'last-inactive-link'      => "",
    ),

    // Twitter bootstrap 4.x template
	'bootstrap-4'                 => array(
		'wrapper'                 => "<nav aria-label=\"navigation\">\n\t<ul class=\"pagination\">\n\t\t{pagination}\n\t</ul>\n</nav>",

		'first'                   => "{link}",
		'first-marker'            => "&laquo;&laquo;",
		'first-link'              => "\t\t<a class=\"btn btn-default\" href=\"{uri}\">{page}</a>\n",

		'first-inactive'          => "",
		'first-inactive-link'     => "",

		'previous'                => "{link}",
		'previous-marker'         => "Anterior",
		'previous-link'           => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'previous-inactive'       => "{link}",
		'previous-inactive-link'  => "\t\t<a class=\"btn btn-default\" href=\"javascript:return false;\">{page}</a>\n",

		'regular'                 => "{link}",
		'regular-link'            => "\t\t<a class=\"btn btn-default\" href=\"{uri}\">{page}</a>\n",

		'active'                  => "{link}",
		'active-link'             => "\t\t<a class=\"btn btn-primary\" href=\"#\">{page}</a>\n",

		'next'                    => "{link}",
		'next-marker'             => "Siguiente",
		'next-link'               => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'next-inactive'           => "{link}",
		'next-inactive-link'      => "\t\t<a class=\"btn btn-default\" href=\"javascript:return false;\">{page}</a>\n",

		'last'                    => "{link}",
		'last-marker'             => "&raquo;&raquo;",
		'last-link'               => "\t\t<a href=\"{uri}\">{page}</a>\n",

		'last-inactive'           => "",
		'last-inactive-link'      => "",
	),

	// Twitter bootstrap 2.x template
	'bootstrap'                   => array(
		'wrapper'                 => "<div class=\"pagination\">\n\t<ul>{pagination}\n\t</ul>\n</div>\n",

		'first'                   => "\n\t\t<li>{link}</li>",
		'first-marker'            => "&laquo;&laquo;",
		'first-link'              => "<a href=\"{uri}\">{page}</a>",

		'first-inactive'          => "",
		'first-inactive-link'     => "",

		'previous'                => "\n\t\t<li>{link}</li>",
		'previous-marker'         => "&laquo;",
		'previous-link'           => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",

		'previous-inactive'       => "\n\t\t<li class=\"disabled\">{link}</li>",
		'previous-inactive-link'  => "<a href=\"#\" rel=\"prev\">{page}</a>",

		'regular'                 => "\n\t\t<li>{link}</li>",
		'regular-link'            => "<a href=\"{uri}\">{page}</a>",

		'active'                  => "\n\t\t<li class=\"active\">{link}</li>",
		'active-link'             => "<a href=\"#\">{page}</a>",

		'next'                    => "\n\t\t<li>{link}</li>",
		'next-marker'             => "&raquo;",
		'next-link'               => "<a href=\"{uri}\" rel=\"next\">{page}</a>",

		'next-inactive'           => "\n\t\t<li class=\"disabled\">{link}</li>",
		'next-inactive-link'      => "<a href=\"#\" rel=\"next\">{page}</a>",

		'last'                    => "\n\t\t<li>{link}</li>",
		'last-marker'             => "&raquo;&raquo;",
		'last-link'               => "<a href=\"{uri}\">{page}</a>",

		'last-inactive'           => "",
		'last-inactive-link'      => "",
	),

	// Admin template
	'admin'                       => array(
		'wrapper'                 => "<nav aria-label=\"navigation\">\n\t<ul class=\"pagination justify-content-end mb-0\">\n\t\t{pagination}\n\t</ul>\n</nav>",

		'first'                   => "{link}",
		'first-marker'            => "&laquo;&laquo;",
		'first-link'              => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'first-inactive'          => "{link}",
		'first-inactive-link'     => "\t\t<li class=\"page-item disabled\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'previous'                => "{link}",
		'previous-marker'         => "<i class=\"fas fa-angle-left\"></i><span class=\"sr-only\">Previous</span>",
		'previous-link'           => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'previous-inactive'       => "{link}",
		'previous-inactive-link'  => "\t\t<li class=\"page-item disabled\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'regular'                 => "{link}",
		'regular-link'            => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'active'                  => "{link}",
		'active-link'             => "\t\t<li class=\"page-item active\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'next'                    => "{link}",
		'next-marker'             => "<i class=\"fas fa-angle-right\"></i><span class=\"sr-only\">Next</span>",
		'next-link'               => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'next-inactive'           => "{link}",
		'next-inactive-link'      => "\t\t<li class=\"page-item disabled\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'last'                    => "{link}",
		'last-marker'             => "&raquo;&raquo;",
		'last-link'               => "\t\t<li class=\"page-item\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",

		'last-inactive'           => "{link}",
		'last-inactive-link'      => "\t\t<li class=\"page-item disabled\">\n\t\t\t<a class=\"page-link\" href=\"{uri}\">{page}</a>\n\t\t</li>",
	),

);
