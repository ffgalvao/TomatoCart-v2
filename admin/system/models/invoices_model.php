<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * TomatoCart Open Source Shopping Cart Solution
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 (2007)
 * as published by the Free Software Foundation.
 *
 * @package   TomatoCart
 * @author    TomatoCart Dev Team
 * @copyright Copyright (c) 2009 - 2012, TomatoCart. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl.html
 * @link    http://tomatocart.com
 * @since   Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Invoices Model
 *
 * @package   TomatoCart
 * @subpackage  tomatocart
 * @category  template-module-model
 * @author    TomatoCart Dev Team
 * @link    http://tomatocart.com/wiki/
 */
class Invoices_Model extends CI_Model
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
	// ------------------------------------------------------------------------
    
    /**
     * Get the invoices
     *
     * @access public
     * @param $start
     * @param $limit
     * @param $orders_id
     * @param $customers_id
     * @param $status
     * @return array
     */
    public function get_invoices($start, $limit, $orders_id, $customers_name, $status)
    {
    	$this->db
        ->select('o.orders_id, o.customers_ip_address, o.customers_name, o.payment_method, o.date_purchased, o.last_modified, o.currency, o.currency_value, o.invoice_number, o.invoice_date, s.orders_status_name, ot.text as order_total')
        ->from('orders o')
        ->join('orders_total ot', 'o.orders_id = ot.orders_id and ot.class = "total"')
        ->join('orders_status s', 'o.orders_status = s.orders_status_id')
        ->where('o.invoice_number IS NOT NULL')
        ->where('s.language_id', lang_id());
        
    	//search fields
        if (!empty($orders_id))
        {
           $this->db->where('o.orders_id', $orders_id);
        }
        
        if (!empty($customers_name))
        {
            $this->db->like('o.customers_name', $customers_name);
        }
        
        if (!empty($status))
        {
            $this->db->like('s.orders_status_name', $status);
        }
        
        //clone the db object to get the total number of invoices
        $db_total = clone $this->db;
        
        //get the invoices
        $result = $this->db
        ->order_by('o.date_purchased desc, o.last_modified desc, o.invoice_number desc')
        ->limit($limit, $start)
        ->get();
        
        return array('invoices' => $result->result_array(), 'total' => $db_total->count_all_results());
    }
}

/* End of file invoices_model.php */
/* Location: ./system/models/invoices_model.php */