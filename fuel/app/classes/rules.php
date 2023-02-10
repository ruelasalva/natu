<?php

class Rules
{
	public static function _validation_alphanumeric_puntuation($val)
	{
		return (preg_match('/^[a-zA-Z0-9\.]*$/', $val)) ? true : false;
	}

	public static function _validation_alphabetic($val)
	{
		return (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]*$/', $val)) ? true : false;
	}

	public static function _validation_alphabetic_spaces($val)
	{
		return (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]*$/', $val)) ? true : false;
	}

	public static function _validation_alphabetic_spaces_puntuation($val)
	{
		return (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ \.]*$/', $val)) ? true : false;
	}

	public static function _validation_alphanumeric($val)
	{
		return (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9]*$/', $val)) ? true : false;
	}

	public static function _validation_alphanumeric_spaces($val)
	{
		return (preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]*$/', $val)) ? true : false;
	}

	public static function _validation_date($val)
	{
		return (preg_match('/^([\d]{2})([\/][\d]{2})([\/][\d]{4})$/', $val)) ? true : false;
	}

	public static function _validation_isodate($val)
	{
		return (preg_match('/^(?:[1-9][0-9]*)?[0-9]{4}-(1[0-2]|0[1-9])-(3[01]|0[1-9]|[12][0-9])T(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])?$/', $val)) ? true : false;
	}

	public static function _validation_hours($val)
	{
		return (preg_match('/^([\d]{2})([\:][0-5][0-9])$/', $val)) ? true : false;
	}

	public static function _validation_float($val)
	{
		return (preg_match('/^\d+(\.\d{1,3})?$/', $val)) ? true : false;
	}

	public static function _validation_full_float($val)
	{
		return (preg_match('/^[0-9]+([\.][0-9]*)?$/', $val)) ? true : false;
	}

	public static function _validation_credit_cards($val)
	{
		return (preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|622((12[6-9]|1[3-9][0-9])|([2-8][0-9][0-9])|(9(([0-1][0-9])|(2[0-5]))))[0-9]{10}|64[4-9][0-9]{13}|65[0-9]{14}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})*$/', $val)) ? true : false;
	}
}
