<?php

use Illuminate\Database\Capsule\Manager as Capsule;

if(!defined("WHMCS")) die("This file cannot be accessed directly");

function PrivacyPolicy_ClientAreaPage_Hook($vars) 
{
  $SLSETTINGS = [];
  $results = Capsule::table('mod_privacyconfog')->get();
  foreach ($results as $result)
  {
    $setting = $result->setting;
    $value   = $result->value;
    $SLSETTINGS[$setting] = $value;
  }

  $description = $SLSETTINGS["Description"];
  $keywords    = $SLSETTINGS["Keyword"];

  $pagemeta  = ($description)? "<meta name=\"description\" content=\"$description\" />\r" : '';
  $pagemeta .= ($keywords)? "<meta name=\"keywords\" content=\"$keywords\" />\r" : '';

  return $pagemeta;
}

add_hook("ClientAreaHeadOutput",198765,"PrivacyPolicy_ClientAreaPage_Hook");
