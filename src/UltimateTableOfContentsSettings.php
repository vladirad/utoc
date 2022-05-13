<?php

namespace Devstetic\Utoc;

trait UltimateTableOfContentsSettings
{
    public static $settings = [
        'utoc_position',
        'utoc_level',
        'utoc_style',
        'utoc_title',
        'utoc_screen',
        'utoc_text',
        'utoc_visible',
        'utoc_css',
        'utoc_chevron_level'
    ];

    public static $levels = [
        1, 2, 3, 4, 5, 6
    ];

    public static $chevron_levels = [
        0, 1, 2, 3, 4, 5
    ];

    public static $styles = [
        'none',
        'default',
        'default-sticky',
    ];

    // public function clear_utoc_post_settings()
    // {
    //     // TODO: Leave visibility and edited text
    //     global $wpdb;
    //     $table = $wpdb->prefix . 'postmeta';

    //     $wpdb->delete($table, array('meta_key' => 'utoc_position'));
    //     $wpdb->delete($table, array('meta_key' => 'utoc_level'));
    //     $wpdb->delete($table, array('meta_key' => 'utoc_style'));
    // }

    public function get_setting($setting, $default = null)
    {
        global $post;

        if (!in_array($setting, self::$settings))
            return null;


        $post_meta = null;

        if ($post)
            $post_meta = get_post_meta($post->ID, $setting, true);

        return $post_meta ?: get_option($setting, $default);
    }

    public function get_setting_position()
    {
        return $this->get_setting('utoc_position') ?: 'disabled';
    }

    public function get_setting_level()
    {
        return $this->get_setting('utoc_level', self::$levels);
    }

    public function get_setting_chevron_level()
    {
        return $this->get_setting('utoc_level', self::$chevron_levels);
    }

    public function get_setting_style()
    {
        return $this->get_setting('utoc_style') ?: 'default';
    }

    public function get_setting_title()
    {
        return $this->get_setting('utoc_title') ?: 'default';
    }

    public function get_setting_screen()
    {
        return $this->get_setting('utoc_screen') ?: ['post'];
    }

    public function get_setting_css()
    {
        return $this->get_setting('utoc_css') ?: '';
    }
}
