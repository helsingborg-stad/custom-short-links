<?php

namespace CustomShortLinks;

class Shortlinks
{
    public function __construct()
    {
        add_action('init', array($this, 'registerPostType'));
        add_action('wp', array($this, 'handleRedirect'));

        add_filter('enter_title_here', array($this, 'titlePlaceholder'));
    }

    /**
     * Register shortlinks posttype
     * @return void
     */
    public function registerPostType()
    {
        $nameSingular = 'Shortlink';
        $namePlural = 'Shortlinks';
        $description = 'Create shortlinks to your posts or pages';

        $labels = array(
            'name'               => _x($nameSingular, 'post type general name', 'custom-short-links'),
            'singular_name'      => _x($nameSingular, 'post type singular name', 'custom-short-links'),
            'menu_name'          => _x($namePlural, 'admin menu', 'custom-short-links'),
            'name_admin_bar'     => _x($nameSingular, 'add new on admin bar', 'custom-short-links'),
            'add_new'            => _x('Add New', 'add new button', 'custom-short-links'),
            'add_new_item'       => sprintf(__('Add new %s', 'custom-short-links'), $nameSingular),
            'new_item'           => sprintf(__('New %s', 'custom-short-links'), $nameSingular),
            'edit_item'          => sprintf(__('Edit %s', 'custom-short-links'), $nameSingular),
            'view_item'          => sprintf(__('View %s', 'custom-short-links'), $nameSingular),
            'all_items'          => sprintf(__('All %s', 'custom-short-links'), $namePlural),
            'search_items'       => sprintf(__('Search %s', 'custom-short-links'), $namePlural),
            'parent_item_colon'  => sprintf(__('Parent %s', 'custom-short-links'), $namePlural),
            'not_found'          => sprintf(__('No %s', 'custom-short-links'), $namePlural),
            'not_found_in_trash' => sprintf(__('No %s in trash', 'custom-short-links'), $namePlural)
        );

        $args = array(
            'labels'               => $labels,
            'description'          => __($description, 'custom-short-links'),
            'public'               => true,
            'publicly_queriable'   => true,
            'show_ui'              => true,
            'show_in_nav_menus'    => false,
            'show_in_menu'         => true,
            'has_archive'          => false,
            'rewrite'              => array(
                'slug'       => '/',
                'with_front' => false
            ),
            'hierarchical'         => false,
            'menu_position'        => 100,
            'exclude_from_search'  => true,
            'menu_icon'            => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNDguMTU1IDQ4LjE1NSI+PHBhdGggZD0iTTM2Ljg1IDI0LjY1Nmw5LjUwNC05LjUwM0wzNi44NSA1LjY1SDI0LjA4di00LjZjMC0uNTc3LS40NzMtMS4wNS0xLjA1LTEuMDVIMTkuMjRjLS41NzggMC0xLjA1LjQ3My0xLjA1IDEuMDV2NC42SDIuM2EuNS41IDAgMCAwLS41LjV2MTguMDA3YS41LjUgMCAwIDAgLjUuNWgxNS44OXYzLjczaC01Ljg4bC00Ljc1MiA0Ljc1IDQuNzUyIDQuNzUzaDUuODh2OS4yMTRjMCAuNTc3LjQ3MiAxLjA1MiAxLjA1IDEuMDUyaDMuNzg1Yy41NzggMCAxLjA1LS40NzUgMS4wNS0xLjA1MlYzNy44OWg3LjI2YS41LjUgMCAwIDAgLjUtLjV2LTguNTA0YS41LjUgMCAwIDAtLjUtLjVoLTcuMjZ2LTMuNzNoMTIuNzc0eiIgZmlsbD0iIzk5OSIvPjwvc3ZnPg==',
            'supports'             => array('title')
        );

        register_post_type('custom-short-link', $args);
    }

    public function handleRedirect()
    {

    }

    /**
     * Changes the title input placeholder in WP Admin
     * @param  string $placeholder Original placeholder
     * @return string              New placeholder
     */
    public function titlePlaceholder($placeholder)
    {
        $screen = get_current_screen();

        if ($screen->post_type == 'custom-short-link') {
            $placeholder = __('Enter shortlink');
        }

        return $placeholder;
    }
}
