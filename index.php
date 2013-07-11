<?php
/*
Plugin Name: Contact Form Registe Extension
Plugin URI: https://github.com/TEDxTaipei/Contact-Form-Register-Extension
Description: Let Contact From 7 can use for event register, and add send check mail for user and receive inbond mail when user reply mail.
Author: TEDxTaipei
Author URI: http://tedxtaipei.com/
Text Domain: tedxtaipei-cfre
Domain Path: /lang
Version: 0.1.0
*/

require "core/RegisterExtension.php";

$tedxtaipei_CFRE = \TEDxTaipei\RegisterExtension\RegisterExtension::getInstance();
$tedxtaipei_CFRE->bootstrap();
