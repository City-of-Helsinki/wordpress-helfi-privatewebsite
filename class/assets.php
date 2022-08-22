<?php
namespace CityOfHelsinki\WordPress\PrivateWebsite;

class Assets {
	public $minified;

	public function init() {
		$this->minified = (defined('WP_DEBUG') && true === WP_DEBUG) ? '' : 'min';

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'adminScripts' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'adminStyles' ), 1 );
		}
		else {
			add_action( 'wp_enqueue_scripts', array( $this, 'publicScripts' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'publicStyles' ), 2 );
		}

	}

	public function implodeParts( array $parts, string $separator ) {
		return implode(
			$separator,
			array_filter( $parts )
		);
	}

	public function dir( string $base, string $separator, array $parts ) {
		return $base . $separator . $this->implodeParts($parts, '/') . $separator;
	}

	public function assetFile( array $parts ) {
		return $this->implodeParts($parts, '.');
	}

	public function assetPath( string $directory, string $name, string $minified, string $extension ) {
		return $this->dir(
			PLUGIN_PATH . 'assets',
			DIRECTORY_SEPARATOR,
			array($directory, $extension)
			) . $this->assetFile(array(
				$name,
				$minified,
				$extension
			));
	}

	public function assetUrl( string $directory, string $name, string $minified, string $extension ) {
		return $this->dir(
			PLUGIN_URL . 'assets',
			'/',
			array($directory, $extension)
			) . $this->assetFile(array(
				$name,
				$minified,
				$extension
			));
	}

	public function assetVersion( string $path ) {
		return (defined('WP_DEBUG') && true === WP_DEBUG) ? filemtime( $path ) : PLUGIN_VERSION;
	}

	public function adminScripts( string $hook ) {
		wp_enqueue_script(
			'privatewebsite-wp-admin-scripts',
			$this->assetUrl('admin', 'scripts', $this->minified, 'js'),
			apply_filters( 'privatewebsite_admin_scripts_dependencies', array() ),
			$this->assetVersion( $this->assetPath('admin', 'scripts', $this->minified, 'js') ),
			true
		);
	}

	public function adminStyles( string $hook ) {
		wp_enqueue_style(
			'privatewebsite-wp-admin-styles',
			$this->assetUrl('admin', 'styles', $this->minified, 'css'),
			apply_filters( 'privatewebsite_admin_styles_dependencies', array() ),
			$this->assetVersion( $this->assetPath('admin', 'styles', $this->minified, 'css') ),
			'all'
		);
	}

	public function publicScripts() {
		wp_enqueue_script(
			'privatewebsite-wp-scripts',
			$this->assetUrl('public', 'scripts', $this->minified, 'js'),
			apply_filters( 'privatewebsite_scripts_dependencies', array() ),
			$this->assetVersion( $this->assetPath('public', 'scripts', $this->minified, 'js') ),
			true
		);
	}

	public function publicStyles() {
		wp_enqueue_style(
			'privatewebsite-wp-styles',
			$this->assetUrl('public', 'styles', $this->minified, 'css'),
			apply_filters( 'privatewebsite_styles_dependencies', array( 'wp-block-library' ) ),
			$this->assetVersion( $this->assetPath('public', 'styles', $this->minified, 'css') ),
			'all'
		);
	}

}
$assets = new Assets();
$assets->init();
