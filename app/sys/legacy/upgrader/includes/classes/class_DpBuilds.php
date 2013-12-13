<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * A class that wraps up DeskPRO build number workings.
 *
 * DeskPRO builds consist of four numbers put together as one:
 * (major)(minor)(maintenance)(build)
 *
 * The minor, maintenance and build numbers are always two digits
 * with a leading zero if required. This is to ensure there are enough
 * numbers to keep the New > Old relationship.
 *
 * Examples:
 *     3.0.0 Beta 1: (3)(00)(00)(01)
 *     3.0.0 Beta 2: (3)(00)(00)(02)
 *     3.0.0 "Gold": (3)(00)(00)(06) - there are six builds from beta to gold
 *
 *     3.0.1: (3)(00)(01)(01) - There is always at least one build
 *     3.0.1.1: (3)(00)(01)(02) - If there was such a version, it'd just be another build
 *
 *     3.1.0 Beta 1: (3)(01)(00)(01)
 *
 */
class DpBuilds {

	var $deskpro_versions = array();
	var $deskpro_versions_names = array();
	var $upgrade_paths = array();
	var $legacy_versions = array();





	function DpBuilds($loadfile = false) {

		if (!$loadfile) {
			$loadfile = INC . 'versions.php';
		}

		require($loadfile);

		$this->deskpro_versions = $deskpro_versions;
		$this->deskpro_versions_names = $deskpro_versions_names;
		$this->upgrade_paths = $upgrade_paths;
		$this->legacy_versions = $legacy_versions;
	}





	/**
	 * Get the version for a build.
	 *
	 * If build does not exist, an empty string is returned.
	 *
	 * @param integer $build
	 * @return string
	 */
	function getVersion($build) {

		if (!isset($this->deskpro_versions[$build])) {
			return '';
		}

		return $this->deskpro_versions[$build];
	}





	/**
	 * Get the version name for a build.
	 *
	 * If build does not exist, an empty string is returned.
	 *
	 * @param integer $build
	 * @return string
	 */
	function getVersionName($build) {

		if (!isset($this->deskpro_versions_names[$build])) {
			return '';
		}

		return $this->deskpro_versions_names[$build];
	}





	/**
	 * Get the next version in the upgrade path.
	 *
	 * If there is no next build, 0 is returned.
	 *
	 * @param integer $build
	 * @return integer
	 */
	function getNextBuild($build) {

		if (!isset($this->upgrade_paths[$build])) {
			return 0;
		}

		return $this->upgrade_paths[$build];
	}





	/**
	 * Convert an old build number to the correct build.
	 *
	 * If the legacy build couldn't be mapped to a new build,
	 * 0 is returned.
	 *
	 * @param integer $legacy_build
	 * @return integer
	 */
	function convertLegacyBuild($legacy_build) {

		if (!isset($this->legacy_versions[$legacy_build])) {
			return 0;
		}

		return $this->legacy_versions[$legacy_build];
	}





	/**
	 * Check if a build number is valid.
	 *
	 * @param integer $build
	 * @return boolean
	 */
	function isBuild($build) {
		return isset($this->deskpro_versions[$build]);
	}





	/**
	 * Return which build is newest.
	 *
	 * If builds are the same, 0 is returned.
	 *
	 * @param integer $build1
	 * @param integer $build2
	 * @return integer
	 */
	function newestBuild($build1, $build2) {
		if ($build1 > $build2) {
			return $build1;
		} elseif ($build1 < $build2) {
			return $build2;
		} else {
			return 0;
		}
	}





	/**
	 * Return which build is oldest.
	 *
	 * If builds are the same, 0 is returned.
	 *
	 * @param integer $build1
	 * @param integer $build2
	 * @access static
	 * @return integer
	 */
	function oldestBuild($build1, $build2) {
		if ($build1 < $build2) {
			return $build1;
		} elseif ($build1 > $build2) {
			return $build2;
		} else {
			return 0;
		}
	}





	/**
	 * Convert an input if it is a legacy number, otherwise leave it alone. If it's
	 * an unrecognized version then return $default.
	 *
	 * @param integer $in
	 * @param integer $default
	 * @return integer
	 */
	function convertIfLegacy($in, $default = 0) {

		$build = $in;

		if (!$this->isBuild($in)) {
			$build = $this->convertLegacyBuild($in);

			if (!$build) {
				$build = $default;
			}
		}

		return $build;
	}
}

?>