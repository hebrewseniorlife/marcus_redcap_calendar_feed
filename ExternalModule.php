<?php
namespace Marcus\CalendarFeed\ExternalModule;

/**
 * ExternalModule  - (required) Abstract implementation of REDCap module
 */
class ExternalModule extends \ExternalModules\AbstractExternalModule {
	public function __construct() {
		parent::__construct();
	}
	
	public function redcap_module_system_enable( $version ) {
		
	}
}