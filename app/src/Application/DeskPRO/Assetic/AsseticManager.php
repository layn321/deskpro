<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage DeskPRO
 */

namespace Application\DeskPRO\Assetic;

use Orb\Util\Arrays;
use Orb\Util\Strings;

class AsseticManager
{
	/**
	 * @var string
	 */
	protected $static_path;

	/**
	 * The directory under static that we'll write built assets to.
	 * This is also the directory we'll try to fetch assets from with the asset helper
	 * for serving files.
	 *
	 * @var string
	 */
	protected $build_subdir;

	/**
	 * The path where files are written to
	 * @var string
	 */
	protected $write_path;

	/**
	 * @var \Application\DeskPRO\Templating\Asset\UrlPackage
	 */
	protected $asset_helper;

	/**
	 * @var \Assetic\AssetManager
	 */
	protected $asset_manager;

	/**
	 * @var \Assetic\FilterManager
	 */
	protected $filter_manager;

	/**
	 * Config (usually config.assets.php) that holds info about assets
	 * @var array
	 */
	protected $asset_config = null;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	protected $options;

	/**
	 * @var bool
	 */
	protected $debug = false;

	/**
	 * True to detect when a file is updated when fetching its public path
	 * to automatically update it.
	 *
	 * @var bool
	 */
	protected $auto_update = false;

	/**
	 * Keeps track of which assets use others
	 * @var array
	 */
	protected $dep_map = array();

	public function __construct(array $asset_config, $static_path, $build_subdir, $debug = false)
	{
		$this->debug = $debug;

		$this->static_path  = $static_path;
		$this->build_subdir = $build_subdir;
		$this->write_path   = $static_path . '/' . $this->build_subdir;

		$this->asset_manager = new \Assetic\AssetManager();
		$this->filter_manager = new \Assetic\FilterManager();

		$options = array();
		if (isset($asset_config['OPTIONS'])) {
			$options = $asset_config['OPTIONS'];
			unset($asset_config['OPTIONS']);
		}

		$options = new \Orb\Util\OptionsArray($options);
		$this->options = $options;

		$this->asset_config = $asset_config;

		foreach ($asset_config as $name => $info) {
			if (isset($info['references'])) {
				foreach ($info['references'] as $sub_name) {
					if (!isset($this->dep_map[$sub_name])) {
						$this->dep_map[$sub_name] = array();
					}

					$this->dep_map[$sub_name][] = $name;
				}
			}
		}
	}


	/**
	 * @param string $name
	 * @return \Assetic\Factory\AssetCollection
	 */
	public function getBuildAsset($name)
	{
		$info = $this->getBundleConfig($name);
		$this->getAssetBundle($name);

		$factory = new \Assetic\Factory\AssetFactory($this->write_path);
		$factory->setDebug($this->debug);
		$factory->setAssetManager($this->asset_manager);

		$asset = $factory->createAsset(array('@' . $name));

		return $asset;
	}


	/**
	 * Write the bundle file to the filesystem
	 *
	 * @param $name
	 * @return void
	 */
	public function writeBuildFile($name)
	{
		$info = $this->getBundleConfig($name);
		$asset = $this->getBuildAsset($name);

		$file = $this->write_path . '/' . $info['out'];
		$dir = dirname($file);

		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		if (!is_dir($dir)) {
			throw \RuntimeException("Bad asset write path `$dir`");
		}

		$content = $asset->dump();

		if (strpos($info['out'], '.css') !== false) {
			// Sprite refs are per file
			$sprite_id = preg_replace('#[^a-zA-Z0-9_\-]#', '', str_replace('.css', '', $info['out']));
			$content = preg_replace('#sprite-ref: ([A-Za-z0-9_\-]+)#', 'sprite-ref: '. $sprite_id .'_$1', $content);
			$content = preg_replace('#sprite: ([A-Za-z0-9_\-]+)#', 'sprite: '. $sprite_id .'_$1', $content);
			$content = preg_replace('#sprite-image: url\(\'?(.*)/(.*?)\.png\'?\)#', 'sprite-image: url($1/'. $sprite_id .'_$2.png)', $content);
		}

		if (isset($info['post_filters'])) {
			$bundle_asset = $this->getAssetBundle($name);
			$first = Arrays::getFirstItem($bundle_asset->all());

			$ext = Strings::getExtension($file);
			$hash = substr(sha1(time().rand(11111, 99999)), 0, 7);
			$new_file = dirname($file) . '/' . $hash . '.' . $ext;

			file_put_contents($new_file, $content);

			$filters = array();
			foreach ($info['post_filters'] as $f) {
				$filters[] = $this->getFilter($f);
			}
			$new_asset = new \Assetic\Asset\FileAsset($new_file, $filters);

			$factory = new \Assetic\Factory\AssetFactory($this->write_path);
			$factory->setDebug($this->debug);
			$am = new \Assetic\AssetManager();
			$am->set('tmp', $new_asset);
			$factory->setAssetManager($am);
			$post_asset = $factory->createAsset('@tmp');

			$content = $post_asset->dump();

			unset($filters, $new_asset, $factory, $am, $post_asset);
			unlink($new_file);
		}

		if (strpos($file, '.css') !== false) {
			$content = str_replace('/sprite-ui-h.png', '/sprite-ui-h.png?_'.time(), $content);
			$content = str_replace('/sprite-ui-v.png', '/sprite-ui-v.png?_'.time(), $content);
		}

		file_put_contents($file,  $content);

		// Also need to update any that use this
		if (isset($this->dep_map[$name])) {
			foreach ($this->dep_map[$name] as $parent_name) {
				//$this->writeBuildFile($parent_name);
			}
		}
	}


	/**
	 * Write a build file only if its stale
	 *
	 * @param $name
	 * @return void
	 */
	public function writeBuildFileIfStale($name)
	{
		if ($this->isBuildStale($name)) {
			$this->writeBuildFile($name);
		}
	}


	/**
	 * Templting helper used with fetching URLs
	 *
	 * @param $asset_helper
	 * @return void
	 */
	public function setAssetHelper($asset_helper)
	{
		$this->asset_helper = $asset_helper;
	}


	/**
	 * Get the public path to an asset build file
	 *
	 * @param $name
	 * @return string
	 */
	public function getUrl($name)
	{
		if ($this->auto_update && $this->isBuildStale($name)) {
			$this->writeBuildFile($name);
		}

		$info = $this->getBundleConfig($name);
		return $this->asset_helper->getUrl($this->build_subdir . '/' . $info['out']);
	}


	/**
	 * Get an array of paths to all the raw files in a bundle
	 *
	 * @param $name
	 * @return string[]
	 */
	public function getRawUrls($name)
	{
		$info = $this->getBundleConfig($name);

		if (!$info) {
			throw new \InvalidArgumentException("Unknown bundle config `$name`");
		}

		$urls = array();

		if (isset($info['references'])) {
			foreach ($info['references'] as $r) {
				$r_urls = $this->getRawUrls($r);
				if ($r_urls) {
					$urls = array_merge($urls, $r_urls);
				}
			}
		}

		if (isset($info['files'])) {
			foreach ($info['files'] as $f) {
				$urls[] = $this->asset_helper->getUrl($f);
			}
		}

		return $urls;
	}


	/**
	 * Check to see if a build file is out of date.
	 *
	 * @param $name
	 * @return bool
	 */
	public function isBuildStale($name)
	{
		$info = $this->getBundleConfig($name);
		$build_file = $this->write_path . '/' . $info['out'];

		if (!file_exists($build_file)) {
			return true;
		}

		$asset = $this->getAssetBundle($name);

		$build_time  = filemtime($build_file);
		$bundle_time = $asset->getLastModified();

		return ($build_time < $bundle_time);
	}


	/**
	 * Check if a build exists
	 *
	 * @param $name
	 * @return bool
	 */
	public function isBuildExist($name)
	{
		$info = $this->getBundleConfig($name);
		$build_file = $this->write_path . '/' . $info['out'];

		return file_exists($build_file);
	}


	/**
	 * Gets an asset bundle, initializing it if needed
	 *
	 * @param string $name
	 * @return \Assetic\Asset\AssetCollection
	 */
	public function getAssetBundle($name)
	{
		if ($this->asset_manager->has($name)) {
			return $this->asset_manager->get($name);
		}

		$info = $this->getBundleConfig($name);
		$filters = array();
		if (isset($info['filters'])) {
			foreach ($info['filters']  as $f) {
				$filters[] = $this->getFilter($f, isset($info['filter_options'][$f]) ? $info['filter_options'][$f] : array());
			}
		}

		$coll = new \Assetic\Asset\AssetCollection(array(), $filters);
		if (isset($info['files'])) {
			foreach ($info['files'] as $f) {
				$path = $this->static_path . '/' . $f;
				$coll->add(new \Assetic\Asset\FileAsset($path));
			}
		}

		if (isset($info['references'])) {
			foreach ($info['references'] as $r) {
				$ref_info = $this->getAssetBundle($r);


				$coll->add(new \Assetic\Asset\AssetReference($this->asset_manager, $r));
			}
		}

		$this->asset_manager->set($name, $coll);
		return $coll;
	}


	/**
	 * Get all asset bundles
	 *
	 * @return array
	 */
	public function getAllAssetBundles()
	{
		$bundles = array();
		foreach ($this->asset_config as $name) {
			$bundles[$name] = $this->getAssetBundle($name);
		}

		return $bundles;
	}


	/**
	 * Get an array of all defined asset names
	 *
	 * @return array
	 */
	public function getAllBundleNames()
	{
		return array_keys($this->asset_config);
	}


	/**
	 * Get bundle configuration
	 *
	 * @param string $name
	 * @return array
	 */
	public function getBundleConfig($name)
	{
		return isset($this->asset_config[$name]) ? $this->asset_config[$name] : null;
	}


	/**
	 * Get a filter or initialize it if its not created yet.
	 *
	 * @param string $name
	 * @return void
	 */
	public function getFilter($name, array $options = array())
	{
		// Trim off ? which means not to use it in debug
		if ($name[0] == '?') {
			$name = substr($name, 1);
		}

		$filter = null;

		switch ($name) {
			case 'yui_simple':
				$filter = new \Assetic\Filter\Yui\JsCompressorFilter(
					$this->options->get('yui_compressor'),
					$this->options->get('java_path')
				);
				$filter->setDisableOptimizations(true);
				$filter->setNomunge(true);
				$filter->setLineBreak(100);
				break;
			case 'yui':
				$filter = new \Assetic\Filter\Yui\JsCompressorFilter(
					$this->options->get('yui_compressor'),
					$this->options->get('java_path')
				);
				$filter->setLineBreak(100);
				break;
			case 'image_gradients':
				$filter = new \Application\DeskPRO\Assetic\Filter\CssGradientImage(array(

				));
				break;
			case 'css':
				$filter = new \Assetic\Filter\CssMinFilter();
				$filter->setFilters(array(
					"ImportImports"                 => false,
					"RemoveComments"                => true,
					"RemoveEmptyRulesets"           => true,
					"RemoveEmptyAtBlocks"           => true,
					"ConvertLevel3AtKeyframes"      => false,
					"ConvertLevel3Properties"       => false,
					"Variables"                     => false,
					"RemoveLastDelarationSemiColon" => true
				));
				$filter->setPlugins(array(
					"Variables"                     => false,
					"ConvertFontWeight"             => false,
					"ConvertHslColors"              => false,
					"ConvertRgbColors"              => false,
					"ConvertNamedColors"            => true,
					"CompressColorValues"           => false,
					"CompressUnitValues"            => true,
					"CompressExpressionValues"      => true
				));
				break;
			case 'css_path':
				$filter = new \Assetic\Filter\CssRewriteFilter();
				break;
			case 'null':
				$filter = new \Orb\Assetic\Filter\Null();
				break;
			case 'smartsprites':
				$filter = new \Orb\Assetic\Filter\SmartSprites(
					$this->options->get('smartsprites'),
					$options
				);
				break;
			case 'less':
				$filter = new \Orb\Assetic\Filter\Lessc(
					$this->options->get('less')
				);
				break;
			default:
				throw new \InvalidArgumentException("Invalid filter `$name`");
		}

		return $filter;
	}
}
