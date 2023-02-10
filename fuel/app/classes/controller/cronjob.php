<?php

/**
 * CONTROLADOR CRONJOB
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Cronjob extends Controller
{
	/**
	 * CLEAN SALES
	 *
	 * ELIMINA LOS PEDIDOS NO CONCRETADOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_clean_sales()
	{
        # SE INICIALIZAN LAS VARIABLES
		$data = array();
		$time = time() - 345600;

		# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
		$sales = Model_Sale::query()
        ->where('status', 2)
		->where('updated_at', '<=', $time)
		->order_by('id', 'desc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($sales))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sales as $sale)
			{
				# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
				$one_sale = Model_Sale::query()
				->where('id', $sale->id)
				->get_one();

				# SE ESTEBLECE LA NUEVA INFORMACION
				$one_sale->status = 3;

				# SI SE OBTIENE INFORMACION
				if(!empty($one_sale))
				{
					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$sale_products = Model_Sales_Product::query()
					->where('sale_id', $sale->id)
					->get();

					# SI SE OBTIENE INFORMACION
					if(!empty($sale_products))
					{
						# SE RECORRE ELEMENTO POR ELEMENTO
						foreach($sale_products as $sale_product)
						{
							# SE ELIMINA EL REGISTRO
							$sale_product->save();
						}
					}

					# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
					$sale_tax_datam = Model_Sales_Tax_Datum::query()
					->where('sale_id', $sale->id)
					->get_one();

					# SI SE OBTIENE INFORMACION
					if(!empty($sale_tax_datam))
					{
						# SE ELIMINA EL REGISTRO
						$sale_tax_datam->save();
					}

					# SE ELIMINA EL REGISTRO
					$one_sale->save();
				}
			}
		}
	}
}
