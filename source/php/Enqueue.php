<?php

namespace CustomShortLinks;

class Enqueue
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function enqueueScripts()
    {
        if (!$this->shouldEnqueue()) {
            return;
        }

        wp_dequeue_script('autosave');
        wp_enqueue_script('custom-short-links', CUSTOMSHORTLINKS_URL . '/dist/'
        .\CustomShortLinks\Helper\CacheBust::name('js/custom-short-links.js'),
        array(), '1.0.0');
        wp_localize_script('custom-short-links', 'CustomShortLinksVars', array(
            'home_url' => home_url(),
            'shortlink' => __('Shortlink', 'custom-short-links')
        ));
    }

    public function shouldEnqueue()
    {
        $screen = get_current_screen();

        if ($screen->post_type == 'custom-short-link' && ($screen->action == 'add' || (isset($_GET['action']) && $_GET['action'] == 'edit'))) {
            return true;
        }

        return false;
    }
}
