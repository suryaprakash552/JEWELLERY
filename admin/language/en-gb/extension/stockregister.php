<?php
// Heading
$_['heading_title']          = 'Stock Register';

// Text
$_['text_success_add']       = 'Success: Batch has been added successfully!';
$_['text_success_edit']      = 'Success: Batch has been updated successfully!';
$_['text_success_delete']    = 'Success: Batch has been deleted successfully!';
$_['text_success_stock_added'] = 'Success: Stock has been added to inventory!';
$_['text_list']              = 'Batch Stock Register';
$_['text_form']              = 'Batch Form';
$_['text_add']               = 'Add Batch';
$_['text_edit']              = 'Edit Batch';
$_['text_confirm']           = 'Are you sure you want to delete selected batches?';
$_['text_confirm_mark_used'] = 'Are you sure you want to mark selected batches as used and add to inventory?';
$_['text_no_results']        = 'No batches found.';
$_['text_select']            = '--- Please Select ---';
$_['text_all_status']        = 'All Status';
$_['text_batches']           = 'batches';
$_['text_in_stock']          = 'In Stock';
$_['text_used']              = 'Used';
$_['text_in_transit']        = 'In Transit';
$_['text_pending']           = 'Pending';
$_['text_confirmed']         = 'Confirmed';
$_['text_received']          = 'Received';
$_['text_home']              = 'Home';
$_['text_batch_history']     = 'Batch History';
$_['text_view_history']      = 'View batch history for this product';
$_['text_product_details']   = 'Product Details';
$_['text_no_history']        = 'No batch history found for this product.';
$_['text_yes']               = 'Yes';
$_['text_no']                = 'No';
$_['text_mark_as_used']      = 'Mark as Used';
$_['text_select_batches']    = 'Please select at least one batch!';

// Column
$_['column_batch_id']        = 'Batch ID (PO Number)';
$_['column_product']         = 'Product';
$_['column_supplier']        = 'Supplier';
$_['column_received_date']   = 'Received Date';
$_['column_initial_qty']     = 'Quantity';
$_['column_used_qty']        = 'Used Qty';
$_['column_balance_qty']     = 'Balance';
$_['column_location']        = 'Location';
$_['column_status']          = 'Status';
$_['column_action']          = 'Action';
$_['column_price']           = 'Price';
$_['column_model']           = 'Model';
$_['column_sku']             = 'SKU';
$_['column_current_stock']   = 'Current Stock';
$_['column_quantity']        = 'Quantity';
$_['column_notes']           = 'Notes';
$_['column_used']            = 'Used';

// Entry
$_['entry_batch_id']         = 'Batch ID';
$_['entry_product']          = 'Product Name';
$_['entry_supplier']         = 'Supplier Name';
$_['entry_received_date']    = 'Received Date';
$_['entry_delivery_date']    = 'Delivery Date';
$_['entry_qty_received']     = 'Quantity Received';
$_['entry_location']         = 'Storage Location (PL No)';
$_['entry_status']           = 'Status';
$_['entry_notes']            = 'Notes';
$_['entry_date_from']        = 'Date From';
$_['entry_date_to']          = 'Date To';

// Help
$_['help_batch_id']          = 'PO Number from purchase order';
$_['help_qty_received']      = 'Quantity in kg from purchase order';
$_['help_mark_used']         = 'This will add the stock quantity to product inventory and mark the batch as used.';

// Placeholder
$_['placeholder_product']    = 'Search product name';
$_['placeholder_supplier']   = 'Search supplier name';
$_['placeholder_location']   = 'Enter storage location';
$_['placeholder_notes']      = 'Additional notes (optional)';

// Button
$_['button_add']             = 'Add Batch';
$_['button_edit']            = 'Edit';
$_['button_delete']          = 'Delete';
$_['button_save']            = 'Save';
$_['button_cancel']          = 'Cancel';
$_['button_filter']          = 'Filter';
$_['button_clear']           = 'Clear';
$_['button_export']          = 'Export CSV';
$_['button_mark_used']       = 'Mark as Used';
$_['button_view_po']         = 'View Purchase Order';
$_['button_back']            = 'Back';
$_['button_confirm']         = 'Confirm';

// Error
$_['error_warning']          = 'Warning: Please check the form carefully for errors!';
$_['error_permission']       = 'Warning: You do not have permission to modify stock register!';
$_['error_product']          = 'Product name must be between 2 and 128 characters!';
$_['error_supplier']         = 'Supplier name must be between 2 and 128 characters!';
$_['error_qty_received']     = 'Please enter a valid quantity greater than 0!';
$_['error_location']         = 'Location must be at least 2 characters!';
$_['error_order_id']         = 'Order ID is required!';
$_['error_already_used']     = 'Stock has already been added to inventory!';

// Date Format
$_['date_format_short']      = 'd/m/Y';
$_['datepicker']             = 'en-gb';

// Pagination
$_['text_pagination']        = 'Showing %d to %d of %d (%d Pages)';
?>