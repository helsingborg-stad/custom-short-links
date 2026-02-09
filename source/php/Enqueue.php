<?php

declare(strict_types=1);


namespace CustomShortLinks;

use WpUtilService\Features\Enqueue\EnqueueManager;

class Enqueue
{
    public function __construct(
        private EnqueueManager $wpEnqueue,
    ) {
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function enqueueScripts()
    {
        if (!$this->shouldEnqueue()) {
            return;
        }
        wp_dequeue_script('autosave');
        $this->wpEnqueue
            ->add('js/custom-short-links.js', [], '1.0.0')
            ->with()
            ->translation('CustomShortLinksVars', array(
                'home_url' => home_url(),
                'shortlink' => __('Shortlink', 'custom-short-links'),
            ));
    }

    public function shouldEnqueue()
    {
        $screen = get_current_screen();

        if ($screen->post_type == 'custom-short-link' && ($screen->action == 'add' || isset($_GET['action']) && $_GET['action'] == 'edit')) {
            return true;
        }

        return false;
    }
}
