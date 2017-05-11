<?php

/**
 * @package Bubbles
 * @version 1.0
 */

/**
 * Plugin Name: Bubbles
 * Author: Cardinal Media
 * Version: 0.1
 */

class Bubbles {

  function __construct(){
    add_action('admin_init', array($this, 'register_theme_settings'));
    add_action('admin_menu', array($this, 'register_options_page'));
    add_action('rest_api_init', array($this, 'register_endpoint'));
    $this->register_shortcode();
  }

  function register_theme_settings(){
    register_setting('bubbles', 'mailchimp_api_key');
    register_setting('bubbles', 'mailchimp_datacenter');
  }

  function register_options_page(){
    add_options_page('Bubbles', 'Bubbles', 'administrator', 'bubbles', array($this, 'options_page_html'));
  }

  function options_page_html(){
    ?>
    <div class="wrap">
      <h2><?php _e('Settings', 'bubbles'); ?></h2>
      <form method="post" action="options.php">
        <?php settings_fields('bubbles'); ?>
        <?php do_settings_sections('bubbles'); ?>
        <div>
          <label for="mailchimp_api_key"><?php _e('MailChimp API Key'); ?></label>
          <input name="mailchimp_api_key" id="mailchimp_api_key" type="password" value="<?php echo get_option('mailchimp_api_key'); ?>">
        </div>
        <div>
          <label for="mailchimp_datacenter"><?php _e('MailChimp Datacenter'); ?></label>
          <input name="mailchimp_datacenter" id="mailchimp_datacenter" type="text" value="<?php echo get_option('mailchimp_datacenter'); ?>">
        </div>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  function register_endpoint(){
    register_rest_route('bubbles/v1', '/submit', array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => array($this, 'form_submit_callback')
    ));
  }

  function form_submit_callback(WP_REST_Request $req){
    $json = $req->get_json_params();

    $list_id       = $json['list_id'];
    $email_address = $json['email_address'];
    $datacenter    = get_option('mailchimp_datacenter');

    if(!isset($list_id)){
      return new WP_Error('no_list_id', 'Please provide a list ID');
    }

    if(!isset($email_address)){
      return new WP_Error('no_email_address', 'Please provide an email address.');
    }

    if(!isset($datacenter) || !strlen($datacenter)){
      return new WP_Error('no_datacenter', 'Specify a datacenter in plugin settings.');
    }

    $body = array(
      'email_address' => $email_address,
      'status' => 'subscribed'
    );

    $submit = wp_remote_post("https://$datacenter.api.mailchimp.com/3.0/lists/$list_id/members", array(
      'headers' => array(
        'Authorization' => 'Basic ' . get_option('mailchimp_api_key'),
        'Content-Type' => 'application/json; charset=utf-8'
      ),
      'body' => json_encode($body)
    ));

    $response = new WP_REST_Response($submit);

    $response->set_status($submit['response']['code']);

    return $response;
  }

  function register_shortcode(){
    // Add shortcode
    add_shortcode('mailchimp_signup', array($this, 'shortcode_markup'));

    // Register JS for shortcode
    add_action('wp_enqueue_scripts', array($this, 'register_script'));
  }

  function register_script(){
    wp_register_script('axios/js', plugins_url('/bower_components/axios/dist/axios.min.js', __FILE__), array(), '1.0.0', true);
    wp_register_script('bubbles/js', plugins_url('/dist/bubbles.min.js', __FILE__), array('axios/js'), '1.0.0', true);
  }

  function shortcode_markup($atts){
    wp_enqueue_script('axios/js');
    wp_enqueue_script('bubbles/js');

    $attributes = shortcode_atts(array(
      'form_class'   => 'form-inline',
      'input_class'  => 'form-control',
      'list_id'      => '',
      'placeholder'  => __('Enter email address', 'bubbles'),
      'submit_class' => 'btn',
      'submit_text'  => __('Sign up', 'bubbles')
    ), $atts);

    return "
    <div id=\"mailchimp_confirmation\" class=\"mailchimp-confirmation\"></div>
    <form id=\"mailchimp_signup\" class=\"".$attributes['form_class']."\" data-list=\"".$attributes['list_id']."\">
      <input
        name=\"email\"
        class=\"".$attributes['input_class']."\"
        type=\"email\"
        placeholder=\"".$attributes['placeholder']."\"
      >
      <input
        class=\"".$attributes['submit_class']."\"
        type=\"submit\"
        value=\"".$attributes['submit_text']."\"
      >
    </form>
    ";
  }

}

$bubbles = new Bubbles();
