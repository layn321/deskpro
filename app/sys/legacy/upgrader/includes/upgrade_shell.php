<?php
	
class upgrade_base_v3 {

	// called from main script so need to keep
	function header() {

	}

	function start($message) {

		if (defined('UPGRADE_DEBUG')) {
			echo strip_tags($message);
			@ob_flush();
			flush();
		}
	}

	function yes() {
		echo " - YES\n";
		@ob_flush();
		flush();
	}

	function runStep($step) {

		$steps = count($this->pages);

		// ren all the steps
		for ($i = 1; $i <= $steps; $i++) {

			if (defined('UPGRADE_DEBUG')) {
				echo "\n## " . $this->version . " Upgrade : Step $i ##\n\n";
				flush();
			}
			
			$variable = 'step' . $i;
			$this->$variable(1);

		}

		set_deskpro_version($this->version, $this->version_number);

	}
	
	// just carrying on running the step until it is finished
	function redoStep($step, $page) {

		if (defined('UPGRADE_DEBUG')) {
			echo "\n## " . $this->version . " Upgrade : Step $step : Page $page ##\n\n";
			@ob_flush();
			flush();
		}

		$variable = 'step' . $step;
		$this->$variable($page);
	}	
}

class upgrade_base_v2 {
	
	// called from main script so need to keep
	function header() {

	}

	function start($message) {

		if (defined('UPGRADE_DEBUG')) {
			echo strip_tags($message);
			@ob_flush();
			flush();
		}

	}

	function yes() {
		echo " - YES\n";
		@ob_flush();
		flush();
	}

	function runStep($step) {

		$steps = count($this->pages);

		// ren all the steps
		for ($i = 1; $i <= $steps; $i++) {
			
			if (defined('UPGRADE_DEBUG')) {
				echo "\n## v2-v3 Upgrade : Step $i ##\n\n";
				@ob_flush();
				flush();
			}
			
			$variable = 'step' . $i;
			$this->$variable();

		}
	}
	
	// just carrying on running the step until it is finished
	function redoStep($step) {
		
		if (defined('UPGRADE_DEBUG')) {
			echo "\n## v2-v3 Upgrade : Step $step ##\n\n";
			@ob_flush();
			flush();
		}
		
		$variable = 'step' . $step;
		$this->$variable($page);
	}	
}

class upgrade_base_v1 {
	
	// called from main script so need to keep
	function header() {

	}

	function start($message) {

		if (defined('UPGRADE_DEBUG')) {
			echo strip_tags($message);
			@ob_flush();
			flush();
		}

	}

	function yes() {
		echo " - YES\n";
		@ob_flush();
		flush();
	}

	function runStep($step) {

		$steps = count($this->pages);

		// ren all the steps
		for ($i = 1; $i <= $steps; $i++) {
			
			if (defined('UPGRADE_DEBUG')) {
				echo "\n## v1-v2 Upgrade : Step $i ##\n\n";
				@ob_flush();
				flush();
			}
			
			$variable = 'step' . $i;
			$this->$variable();

		}
	}
	
	// just carrying on running the step until it is finished
	function redoStep($step) {
		
		if (defined('UPGRADE_DEBUG')) {
			echo "\n## v1-v2 Upgrade : Step $step##\n\n";
			@ob_flush();
			flush();
		}
		
		$variable = 'step' . $step;
		$this->$variable($page);
	}	
}

/**
* Class to create the install/upgrade header
*
* @access	Public
*
* @return	 string
*/
class Header {

	var $title;

	function setTitle($title) {
		$this->title = $title;
	}

	function build() {

	}

	function footer() {

	}

	function link($word, $page, $js = '') {

	}

	function buildconfig($step = 1, $override='') {

	}
}

?>