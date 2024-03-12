<?php

namespace CustomShortLinks;

class App
{
    public function __construct()
    {
        new \CustomShortLinks\Shortlinks();
        new \CustomShortLinks\Enqueue();

        add_action('init', array($this, 'init'));
        add_filter('acf/settings/load_json', array($this, 'jsonLoadPath'));
        add_filter('acf/fields/post_object/query', array($this, 'removeFromAcfPostQuery'), 99, 3);
    }

    public function init()
    {
        if (!file_exists(WP_CONTENT_DIR . '/mu-plugins/AcfImportCleaner.php') && !class_exists('\\AcfImportCleaner\\AcfImportCleaner')) {
            require_once CUSTOMSHORTLINKS_PATH . 'source/php/Helper/AcfImportCleaner.php';
        }
    }

    public function jsonLoadPath($paths)
    {
        $paths[] = CUSTOMSHORTLINKS_PATH . 'acf-exports';
        return $paths;
    }

    /**
     * Removes the 'custom-short-link' post type from the ACF post query.
     *
     * @param array $args The arguments for the post query.
     * @param string $field The ACF field name.
     * @param int $id The post ID.
     * @return array The modified arguments for the post query.
     */
    public function removeFromAcfPostQuery($args, $field, $id) 
    {
        $key = array_search('custom-short-link', $args['post_type'] ?? []);

        if ($key !== false) {
            unset($args['post_type'][$key]);
        }

        return $args;
    }
}
