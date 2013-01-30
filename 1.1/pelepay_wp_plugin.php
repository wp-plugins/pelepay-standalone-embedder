<?php
/*
Plugin Name: Pelepay standalone embedder
Plugin URI:
Description: Pelepay standalone embedder
Version: 1.0
Author: EOI - Web Like This!
Author URI: http://eoi.co.il/
License: GPLv2
*/

register_activation_hook( __FILE__,'pelepay_set_default_options' );

add_action('admin_menu','pelepay_settings_menu');
add_action('admin_init','pelepay_admin_init');
add_action('add_meta_boxes','pelepay_register_meta_box');
add_action('save_post','pelepay_save_price_data',10,2);
add_filter('the_content','pelepay_buy_button');
add_shortcode('PELEPAY','pelepay_short_code');
add_shortcode('pelepay','pelepay_short_code');

function pelepay_set_default_options() 
{
	if(get_option('pelepay_business_method') === false) 
	{
		add_option('pelepay_business_method', "Please insert your pelepay email account");
	}//business account information
	
	if(get_option('pelepay_cancel_url') === false) 
	{
		add_option('pelepay_cancel_url', "");
	}//cancel url
	
	if(get_option('pelepay_success_url') === false) 
	{
		add_option('pelepay_success_url', "");
	}//success url
	
	if(get_option('pelepay_failure_url') === false) 
	{
		add_option('pelepay_failure_url', "");
	}//failure url
	
	if(get_option('pelepay_button_url') === false) 
	{
		add_option('pelepay_button_url', "http://www.pelepay.co.il/btn_images/pay_button_12.gif");
	}//button url
	
	if(get_option('pelepay_gateway_url') === false) 
	{
		add_option('pelepay_gateway_url', "https://www.pelepay.co.il/pay/custompaypage.aspx");
	}//gateway url
	
	if(get_option('pelepay_payment_number') === false) 
	{
		add_option('pelepay_payment_number', "1");
	}//payment number
}

function pelepay_settings_menu() 
{
	add_options_page( 'Pelepay Configuration','Pelepay Configuration', 'manage_options','pelepay-settings-menu', 'pelepay_config_page' );
}

function pelepay_config_page() 
{
	// Retrieve plugin configuration options from database
	$pelepay_business_method	=	get_option('pelepay_business_method');
	$pelepay_cancel_url			=	get_option('pelepay_cancel_url');
	$pelepay_success_url		=	get_option('pelepay_success_url');
	$pelepay_failure_url		=	get_option('pelepay_failure_url');
	/*$pelepay_gateway_url		=	get_option('pelepay_gateway_url');*/
	$pelepay_button_url			=	get_option('pelepay_button_url');	
	$pelepay_payment_number		=	get_option('pelepay_payment_number');
	?>

<div id="pelepay-general" class="wrap">
  <h2>Pelepay Settings</h2>
  <form method="post" action="admin-post.php">
    <input type="hidden" name="action" value="save_pelepay_options" />
    <!-- Adding security through hidden referrer field -->
    <?php wp_nonce_field('pelepay'); ?>
    <table width="55%" border="0">
	  <!--<tr>
        <td width="20%" align="right">Gateway URL:</td>
        <td><input type="text" size="55" name="pelepay_gateway_url" value="<?php echo esc_html($pelepay_gateway_url); ?>"/></td>
      </tr>-->
      <tr>
        <td width="26%" align="right">Email:</td>
        <td width="74%"><input type="text" size="55" name="pelepay_business_method" value="<?php echo esc_html($pelepay_business_method); ?>"/></td>
      </tr>
	  <tr>
        <td width="26%" align="right">Payment Number(1-12):</td>
        <td><input type="text" size="55" name="pelepay_payment_number" value="<?php echo esc_html($pelepay_payment_number); ?>"/></td>
      </tr>
      <tr>
        <td align="right">Cancel URL:</td>
        <td><input type="text" size="55" name="pelepay_cancel_url" value="<?php echo esc_html($pelepay_cancel_url); ?>"/></td>
      </tr>
      <tr>
        <td align="right">Success URL:</td>
        <td><input type="text" size="55" name="pelepay_success_url" value="<?php echo esc_html($pelepay_success_url); ?>"/></td>
      </tr>
      <tr>
        <td align="right">Failure URL:</td>
        <td><input type="text" size="55" name="pelepay_failure_url" value="<?php echo esc_html($pelepay_failure_url); ?>"/></td>
      </tr>
      <tr>
        <td align="right" valign="middle">Button URL:</td>
        <td valign="middle">
			<input type="text" size="55" name="pelepay_button_url" value="<?php echo esc_html($pelepay_button_url); ?>"/>&nbsp;
			<img src="<?php echo esc_html($pelepay_button_url); ?>" />
		</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="Submit" class="button-primary"/></td>
      </tr>
    </table>
  </form>
</div>
<?php 
}

function pelepay_admin_init()
{
	add_action('admin_post_save_pelepay_options','process_pelepay_options');
}

function process_pelepay_options() 
{
	
	// Check that user has proper security level
	if(!current_user_can('manage_options'))
	wp_die('Not allowed! Need admin access');
	// Check that nonce field created in configuration form
	// is present
	check_admin_referer('pelepay');
	
	$pelepay_business_method=	sanitize_text_field($_POST[pelepay_business_method]);
	$pelepay_cancel_url		=	sanitize_text_field($_POST[pelepay_cancel_url]);
	$pelepay_success_url	=	sanitize_text_field($_POST[pelepay_success_url]);
	$pelepay_failure_url	=	sanitize_text_field($_POST[pelepay_failure_url]);
	/*$pelepay_gateway_url	=	sanitize_text_field($_POST[pelepay_gateway_url]);*/
	$pelepay_button_url		=	sanitize_text_field($_POST[pelepay_button_url]);	
	$pelepay_payment_number	=	sanitize_text_field($_POST[pelepay_payment_number]);
	
	
	
	// Store updated options array to database
	update_option('pelepay_business_method', $pelepay_business_method);
	update_option('pelepay_cancel_url', $pelepay_cancel_url);
	update_option('pelepay_success_url', $pelepay_success_url);
	update_option('pelepay_failure_url', $pelepay_failure_url);
	/*update_option('pelepay_gateway_url', $pelepay_gateway_url);*/
	update_option('pelepay_button_url', $pelepay_button_url);	
	update_option('pelepay_payment_number', $pelepay_payment_number);
	
	// Redirect the page to the configuration form that was
	// processed
	wp_redirect( add_query_arg( 'page',	'pelepay-settings-menu',admin_url('options-general.php') ) );
	exit;
}

function pelepay_register_meta_box() 
{
	add_meta_box('pelepay_price_meta_box','Pelepay Options','pelepay_price_meta_box','post','normal');
	add_meta_box('pelepay_price_meta_box','Pelepay Options','pelepay_price_meta_box','page','normal');	
}

function pelepay_price_meta_box($post)
{
	// Retrieve current price &  name based on post ID
	$post_pelepay_price = esc_html(get_post_meta($post->ID,'post_pelepay_price',true));
	$post_pelepay_name = esc_html(get_post_meta($post->ID,'post_pelepay_name',true));
?>
<!-- Display fields to enter pelepay price & name -->
<table>
  <tr>
    <td style="width: 100px">Price</td>
    <td><input type="text" size="10" name="post_pelepay_price" value="<?php echo $post_pelepay_price; ?>" />
    </td>
  </tr>
  <tr>
    <td style="width: 100px">Description</td>
    <td><input type="text" size="70" name="post_pelepay_name" value="<?php echo $post_pelepay_name; ?>" />
    </td>
  </tr>
</table>
<?php 
}

function pelepay_save_price_data($post_id=false,$post=false) 
{
	// Check post type for posts or pages
	if($post->post_type == 'post' || $post->post_type == 'page') 
	{
		// Store data in post meta table if present in post data		
		update_post_meta($post_id,'post_pelepay_price',$_POST['post_pelepay_price']);		
		update_post_meta($post_id,'post_pelepay_name',$_POST['post_pelepay_name']);
	}
}

function pelepay_display_price_button($post_pelepay_price='',$post_pelepay_button_name='') 
{
	$post_id = get_the_ID();
		
	// Retrieve current source name and address based on post ID
	if($post_pelepay_price == '')
	{
		$post_pelepay_price = get_post_meta($post_id,'post_pelepay_price',true);
	}//fetch the post/page price if call not from the short code
	if($post_pelepay_button_name == '')
	{
		$post_pelepay_button_name	=	get_the_title($post_id);
	}//fetch the post/page title if call not from short code
	
	$permalink = get_permalink($post_id); 
	// Output information to browser
	if(!empty($post_pelepay_price))
	{
		$pelepay_business_method	=	get_option('pelepay_business_method');
		
		$pelepay_cancel_url			=	get_option('pelepay_cancel_url');
		$pelepay_success_url		=	get_option('pelepay_success_url');
		$pelepay_failure_url		=	get_option('pelepay_failure_url');
		
		if($pelepay_cancel_url == '')
		{
			$pelepay_cancel_url	=	$permalink;
		}
		if($pelepay_failure_url == '')
		{
			$pelepay_failure_url	=	$permalink;
		}
		if($pelepay_success_url == '')
		{
			$pelepay_success_url	=	$permalink;
		}
		
		
		
		$pelepay_button_url			=	get_option('pelepay_button_url');
		$pelepay_gateway_url		=	get_option('pelepay_gateway_url');
		$pelepay_payment_number		=	get_option('pelepay_payment_number');
	
	
		//$buy_now_botton = '<img src="'.$pelepay_button_url.'" alt="Buy Now">';
		$buy_now_botton = '<form name="pelepayform" action="'.$pelepay_gateway_url.'" method="post">
							  <INPUT TYPE="hidden" value="'.$pelepay_business_method.'" NAME="business">
							  <INPUT TYPE="hidden" value="'.$post_pelepay_price.'" NAME="amount">
							  <INPUT TYPE="hidden" value="'.$post_id.'" NAME="orderid">
							  <INPUT TYPE="hidden" value="'.$post_pelepay_button_name.'" NAME="description">
							  
							  <INPUT TYPE="hidden" value="'.$pelepay_cancel_url.'" NAME="cancel_return">
							  <INPUT TYPE="hidden" value="'.$pelepay_failure_url.'" NAME="fail_return">
							  <INPUT TYPE="hidden" value="'.$pelepay_success_url.'" NAME="success_return">
							  
							  <input type="image" src="'.$pelepay_button_url.'" name="submit" alt="Make payments with pelepay">
							</form>';

		$before_link = '<div class="PostSource">';
		$price_text = '<strong>&#1502;&#1495;&#1497;&#1512;:</strong>';
		$after_link = '</div>';		
		
		return $pelepay_content= $before_link.$price_text.$post_pelepay_price.' &#x20aa; '.$buy_now_botton.$after_link;		
	}
}

function pelepay_buy_button($content)
{	
	//code block if any Response from pelepay
	if(isset($_GET['Response']))
	{
		$error_icon_url = plugins_url( 'error.png', __FILE__ );
		
		$style_err	=	'style="padding:2px 4px;margin:0px;border:solid 1px #FBD3C6;background:#FDE4E1;color:#CB4721;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;text-align:left;"';
		$style_suc	=	'style="padding:2px 4px;margin:0px;border:solid 1px #C0F0B9;background:#D5FFC6;color:#48A41C;font-family:Arial, Helvetica, sans-serif; font-size:14px;font-weight:bold;text-align:left;"';
		switch($_GET['Response']) 
		{
			case '000': 
				$msg = '<div '.$style_suc.'><img src="'.$error_icon_url.'"> עסקה מאושרת</div><br />';
				break;
			case '003': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> התקשר לחברת האשראי</div><br />';
				break;
			case '004': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> סירוב של חברת האשראי</div><br />';
				break;
			case '033': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> הכרטיס אינו תקין</div><br />';
				break;
			case '001': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> כרטיס אשראי חסום</div><br />';
				break;
			case '002': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> כרטיס אשראי גנוב</div><br />';
				break;
			case '039': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> ספרת הביקורת של הכרטיס אינה תקינה</div><br />';
				break;
			case '101': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> לא מכבדים דיינרס</div><br />';
				break;
			case '061': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> לא הוזן מספר כרטיס אשראי</div><br />';
				break;
			case '157': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> כרטיס אשראי תייר</div><br />';
				break;
			case '133': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> כרטיס אשראי תייר</div><br />';
				break;
			case '036': 
				$msg = '<div '.$style_err.'><img src="'.$error_icon_url.'"> פג תוקף הכרטיס</div><br />';
				break;							
		}			
	}	
	$post_id = get_the_ID();
	$post_pelepay_button_name = get_post_meta($post_id,'post_pelepay_name',true);
	return $msg.$content.pelepay_display_price_button('',$post_pelepay_button_name);
}

function pelepay_short_code($atts) 
{
	extract(shortcode_atts(array('price'=>'','name'=>''),$atts));
	/*$pelepay_button_url	= get_option('pelepay_button_url');
	$buy_now_botton = '<img src="'.$pelepay_button_url.'" alt="Buy Now">';	
	$price_text = '<strong>Price:</strong>';		
	return $pelepay_content= $price_text.$price.' &#x20aa; '.$buy_now_botton;*/
	return $pelepay_content = pelepay_display_price_button($price,$name);
}

?>
