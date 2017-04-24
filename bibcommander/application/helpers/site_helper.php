<?php
/**
 * CIMembership
 * 
 * @package		Helpers
 * @author		1stcoder Team
 * @copyright   Copyright (c) 2015 - 2017 1stcoder. (http://www.1stcoder.com)
 * @license		http://opensource.org/licenses/MIT	MIT License
 */


if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
if(!function_exists('bloginfo'))
{
  function bloginfo($key)
  {
   return htmlentities($key);
  }
}

if(!function_exists('get_template'))
{
  function get_template()
  {
	$CI =& get_instance();
	$template_query=$CI->db->select('*')->from('options')->where('option_name', 'site_template')->get()->result();
	$template=$template_query[0]->option_value;
	if(!isset($template) || $template=='') {$template='default'; }
	$theme_url=base_url().'themes/site/'.$template.'/';
	return $theme_url;
  }
}

if(!function_exists('get_template_directory'))
{
  function get_template_directory()
  {
	$CI =& get_instance();
	$template_query=$CI->db->select('*')->from('options')->where('option_name', 'site_template')->get()->result();
	$template=$template_query[0]->option_value;
	if(!isset($template) || $template=='') {$template='default'; }
	$theme_dir='/site/'.$template.'/';
	return $theme_dir;
  }
}

if(!function_exists('get_theme'))
{
  function get_theme()
  {
	$CI =& get_instance();
	$template_query=$CI->db->select('*')->from('options')->where('option_name', 'admin_template')->get()->result();
	$template=$template_query[0]->option_value;
	if(!isset($template) || $template=='') {$template='default'; }
	$theme_url=base_url().'themes/admin/'.$template.'/';
	return $theme_url;
  }
}

if(!function_exists('get_theme_directory'))
{
  function get_theme_directory()
  {
	$CI =& get_instance();
	$template_query=$CI->db->select('*')->from('options')->where('option_name', 'admin_template')->get()->result();
	$template=$template_query[0]->option_value;
	if(!isset($template) || $template=='') {$template='default'; }
	$theme_dir='/admin/'.$template.'/';
	return $theme_dir;
  }
}

if(!function_exists('get_option'))
{
	function get_option( $option, $default='yes' ) {
		 $ci =& get_instance();
		 $ci->config->load('cimembership');
		 $countries = $ci->config->item('country_list');
	}
}

if(!function_exists('config_item'))
{
	function config_item( $option, $default='yes' ) {
		 $ci =& get_instance();
		 $ci->config->load('cimembership');
		 $result = $ci->config->item($option);
		 return $result;
	}
}

//selected country would be retrieved from a database or as post data
function  country_dropdown($name, $id, $class, $selected_country,$top_countries=array(), $all, $selection=NULL, $show_all=TRUE, $attributes)
{
    // You may want to pull this from an array within the helper
	 $countries = config_item('country_list');

    $html = "<select name='{$name}' id='{$id}' class='{$class}' {$attributes}>";
    $selected = NULL;
    if(in_array($selection,$top_countries)){
        $top_selection = $selection;
        $all_selection = NULL;
    }else{
        $top_selection = NULL;
        $all_selection = $selection;
    }
    if(!empty($selected_country)&&$selected_country!='all'&&$selected_country!='select'){
        $html .= "<optgroup label='Selected Country'>";
        if($selected_country === $top_selection){
            $selected = "SELECTED";
        }
        $html .= "<option value='{$selected_country}'{$selected}>{$countries[$selected_country]}</option>";
        $selected = NULL;
        $html .= "</optgroup>";
    }else if($selected_country=='all'){
        $html .= "<optgroup label='Selected Country'>";
        if($selected_country === $top_selection){
            $selected = "SELECTED";
        }
        $html .= "<option value='all'>All</option>";
        $selected = NULL;
        $html .= "</optgroup>";
    }else if($selected_country=='select'){
        $html .= "<optgroup label='Selected Country'>";
        if($selected_country === $top_selection){
            $selected = "SELECTED";
        }
        $html .= "<option value='select'>Select</option>";
        $selected = NULL;
        $html .= "</optgroup>";
    }
    if(!empty($all)&&$all=='all'&&$selected_country!='all'){
        $html .= "<option value='all'>All</option>";
        $selected = NULL;
    }
    if(!empty($all)&&$all=='select'&&$selected_country!='select'){
        $html .= "<option value='select'>Select</option>";
        $selected = NULL;
    }

    if(!empty($top_countries)){
        $html .= "<optgroup label='Top Countries'>";
        foreach($top_countries as $value){
            if(array_key_exists($value, $countries)){
                if($value === $top_selection){
                    $selected = "SELECTED";
                }
            $html .= "<option value='{$value}'{$selected}>{$countries[$value]}</option>";
            $selected = NULL;
            }
        }
        $html .= "</optgroup>";
    }

    if($show_all){
        $html .= "<optgroup label='All Countries'>";
        foreach($countries as $key => $country){
            if($key === $all_selection){
                $selected = "SELECTED";
            }
            $html .= "<option value='{$key}'{$selected}>{$country}</option>";
            $selected = NULL;
        }
        $html .= "</optgroup>";
    }

    $html .= "</select>";
    return $html;
}

?>