<?php
// Check that code was called from WordPress with
// uninstallation constant declared
if (!defined('WP_UNINSTALL_PLUGIN'))
exit;

// Check if options exist and delete them if present
if(get_option('pelepay_business_method') === false) 
{
	delete_option('pelepay_business_method');
}//business account information

if(get_option('pelepay_cancel_url') === false) 
{
	delete_option('pelepay_cancel_url');
}//cancel url

if(get_option('pelepay_success_url') === false) 
{
	delete_option('pelepay_success_url');
}//success url

if(get_option('pelepay_failure_url') === false) 
{
	delete_option('pelepay_failure_url');
}//failure url	

if(get_option('pelepay_button_url') === false) 
{
	delete_option('pelepay_button_url');
}//button url	

if(get_option('pelepay_gateway_url') === false) 
{
	delete_option('pelepay_gateway_url');
}//gateway url

if(get_option('pelepay_payment_number') === false) 
{
	delete_option('pelepay_payment_number');
}//payment number
	
?>