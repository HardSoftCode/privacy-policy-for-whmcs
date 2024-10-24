<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Input\Sanitize;

if (!defined("WHMCS")) die("This file cannot be accessed directly");

function PrivacyPolicy_config() 
{
  return [
  "name"        => "Privacy Policy",
  "description" => "The module will give you the ability to create your privacy policy page with multi languages",
  "version"     => "2.3.0",
  "author"      => "<a href=\"http://www.hardsoftcode.com\" target=\"_blank\">HSC</a>",
  "language"    => "english",
  "fields"      => [
  "delete"      => ["FriendlyName" => "Delete Module DB", "Type" => "yesno", "Size" => "25", "Description" => "Tick this box to delete the module database on deactivating"]
  ]];
}

function PrivacyPolicy_activate() 
{
  try 
  {
    if(!Capsule::schema()->hasTable('mod_privacypolicy'))
    {
      Capsule::schema()->create('mod_privacypolicy', function ($table) 
      {
        $table->increments('id');
        $table->text('title');
        $table->text('contents');
        $table->integer('parentid');
        $table->text('language');
        $table->text('status')->nullable();
        $table->integer('orders');
       });
     }

    if(!Capsule::schema()->hasTable('mod_privacyconfog'))
    {
      Capsule::schema()->create('mod_privacyconfog', function ($table) 
      {
        $table->text('setting');
        $table->text('value');
      });
       
      Capsule::table('mod_privacyconfog')->insert(array(
          array('setting' => 'Description', 'value' => ''),
          array('setting' => 'Keyword', 'value' => ''),
      ));
    }
  }
  catch (\Exception $e) 
  {
    return array('status'=>'error','description'=>'Unable to create table: ' .$e->getMessage());
  }
  
  return array('status'=>'success','description'=>'Module activated successfully. Click configuration to configure the module');
}

function PrivacyPolicy_deactivate()
{
  $delete = Capsule::table('tbladdonmodules')->where('module', 'PrivacyPolicy')->where('setting', 'delete')->first();

  if($delete->value)
  {
    try 
    {
      Capsule::schema()->dropIfExists('mod_privacypolicy'); 
      Capsule::schema()->dropIfExists('mod_privacyconfog'); 

    } 
    catch (\Exception $e) 
    {
      return array('status'=>'error','description'=>'Unable to drop table: ' .$e->getMessage());
    }
  
    return array('status'=>'success','description'=>'Module deactivated successfully');
  }
}

function PrivacyPolicy_output($vars) 
{
  global $CONFIG;
  
  $modulelink = $vars['modulelink'];
  $LANG       = $vars['_lang'];

  $SLSETTINGS = array();
  $results = Capsule::table('mod_privacyconfog')->get();
  foreach ($results as $result)
  {
    $setting = $result->setting;
    $value   = $result->value;
    $SLSETTINGS[$setting] = $value;
  }
  
  require(dirname( __FILE__ ).'/includes/pages/home.php');
}

function PrivacyPolicy_clientarea($vars) 
{
  $result = Capsule::table('mod_privacypolicy')->where('language', '')->where('status', NULL)->orderBy('orders', 'ASC')->get();
  foreach ($result as $data)
  {
    $id       = $data->id;
    $title    = $data->title;
    $contents = Sanitize::decode($data->contents);
    $orders   = $data->orders;

    $pdata = Capsule::table('mod_privacypolicy')->where('language', $_SESSION['Language'])->where('parentid', $id)->where('status', NULL)->orderBy('orders', 'ASC')->first();
    
    if($pdata->title)
    {
      $title = $pdata->title;
    }
    
    if($pdata->contents)
    {
      $contents = Sanitize::decode($pdata->contents);
    }
    
	  $privacys[] = ['id' => $id, 'title' => $title, 'contents' => $contents, 'orders' => $orders];	       
  }  
  
  return [
        'pagetitle' => $vars['_lang']['privacypolicy'],
        'breadcrumb' => ['index.php?m=PrivacyPolicy' => $vars['_lang']['privacypolicy']],
        'templatefile' => 'templates/homepage',
        'requirelogin' => false, #true or false
        'vars'         => [
                           'PRLANG'   => $vars['_lang'],
                           'privacys' => $privacys,
                          ],
  ];
}
