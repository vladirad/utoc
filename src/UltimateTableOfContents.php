<?php

namespace Devstetic\Utoc;

use IvoPetkov\HTML5DOMDocument;

class UltimateTableOfContents
{
    use UltimateTableOfContentsSettings;

    public static $plugin_name = 'Ultimate Table of Contents Plugin';
    public static $plugin_domain = 'utoc';
    public static $plugin_version = '1.0.0';
    public $parser;


    public function __construct()
    {
        $this->add_actions();
        $this->add_filters();

        $this->parser = new UltimateTableOfContentsParser();
    }

    public function metabox()
    {
        $screens = $this->get_setting_screen();

        foreach ($screens as $screen) {
            add_meta_box(
                'utoc-box',
                'Table Of Contents',
                [$this, 'html'],
                $screen
            );
        }
    }

    public function save_postadata($post_id)
    {
        if (is_admin())
            foreach (self::$settings as $setting) {
                if (isset($_POST[$setting])) {
                    update_post_meta(
                        $post_id,
                        $setting,
                        $_POST[$setting]
                    );
                }
            }
    }

    public function html($post_obj = null)
    {
        global $post;
        if ($post_obj)
            $post = $post_obj;

        include __DIR__ . '/../template/admin-metabox.php';
    }

    public function add_id_headings($content)
    {
        $dom = new HTML5DOMDocument();

        $dom->loadHTML(
            $content,
            HTML5DOMDocument::ALLOW_DUPLICATE_IDS
        );

        foreach ($dom->querySelectorAll("h1, h2, h3, h4, h5, h6") as $key => $node) {
            $node->setAttribute('id', sanitize_title($node->nodeValue . '-' . substr(md5($key), 0, 6)));
        }

        return $dom->saveHTML();

        // $content = preg_replace_callback('/(\<(h[1-6])(.*?))\>(.*)(<\/h[1-6]>)/i', function ($matches) {
        //     if (!stripos($matches[0], 'id=')) {
        //         $id = ' id="' . sanitize_title($matches[4]) . '"';

        //         $matches[0] = '<' . $matches[2] . $id . $matches[3]  . '>' . $matches[4] . $matches[5];
        //     }

        //     return $matches[0];
        // }, $content);

        // return $content;

        // if (function_exists("parse_blocks")) {
        //     // Fix kadence block heading
        //     return serialize_blocks($this->add_params_to_blocks(parse_blocks($content)));
        // } else {
        //     return $content;
        // }
    }

    // public function add_params_to_blocks($blocks)
    // {
    //     if (is_array($blocks) && count($blocks)) {
    //         foreach ($blocks as $key => &$block) {
    //             $blockName = $block['blockName'] ?? '';

    //             if ($blockName === 'kadence/advancedheading') {
    //                 $html = stripslashes($block['innerHTML']);

    //                 $uniqueID = explode('kt-adv-heading_', $html);
    //                 $uniqueID = explode(' ', $uniqueID[1]);
    //                 $blocks[$key]['attrs']['uniqueID'] = '_' . $uniqueID[0];

    //                 if (substr_count($html, 'id=')) {
    //                     $id = explode('id="', $html);
    //                     $id = explode('"', $id[1] ?? substr(uniqid(), -6));
    //                     $blocks[$key]['attrs']['anchor'] = $id[0];
    //                 } else {
    //                     unset($blocks[$key]['attrs']['anchor']);
    //                 }
    //             }

    //             $blocks[$key]['innerBlocks'] = $this->add_params_to_blocks($block['innerBlocks']);
    //         }
    //     }

    //     return $blocks;
    // }

    public function render_toc($content)
    {
        // Don't add to excerpt
        if (is_front_page() && !is_single())
            return $content;

        $position = $this->get_setting_position();

        $html = $this->get_html();

        $content = str_replace('[btoc]', $html, $content);

        switch ($position) {
            case 'before-content':
                return $html . $content;
            case 'after-content':
                return $content . $html;
            case 'after-first-paragraph':
                $content_new = explode('</p>', $content, 2);
                return $content_new[0] . '</p>' . $html . $content_new[1];

            case 'disabled':
            default:
                return $content;
        }

        return $content;
    }

    public function add_admin_styles()
    {
        wp_register_style('utoc-admin', plugin_dir_url(__DIR__)  . 'assets/admin.min.css', false, self::$plugin_version);
        wp_enqueue_style('utoc-admin');

        wp_enqueue_script('utoc-admin', plugin_dir_url(__DIR__)  . 'assets/utoc-admin.js', ["jquery"], self::$plugin_version, true);
        wp_localize_script('utoc-admin', 'utoc_admin', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function add_theme()
    {
        $style = $this->get_setting_style();

        switch ($style) {
            case 'default':
            case 'default-sticky':
                wp_register_style('utoc-theme', plugin_dir_url(__DIR__) . 'assets/themes/default.min.css', false, self::$plugin_version);
                wp_enqueue_style('utoc-theme');
                break;
        }


        if ($custom_css = $this->get_setting_css()) {
            wp_register_style('utoc-no-style-handle', false);
            wp_enqueue_style('utoc-no-style-handle');
            wp_add_inline_style('utoc-no-style-handle', $custom_css);
        }


        wp_enqueue_script('utoc', plugin_dir_url(__DIR__)  . 'assets/utoc.js', ["jquery"], self::$plugin_version, true);
    }

    public function add_settings()
    {
        add_menu_page(
            self::$plugin_name,
            'UTOC Settings',
            'manage_options',
            'utoc',
            [$this, 'html_settings'],
        );


        // if (isset($_POST['utoc-save-and-clear'])) {
        //     $this->clear_utoc_post_settings();
        // }
    }

    public function html_settings()
    {
        include __DIR__ . '/../template/admin-settings.php';
    }

    public function settings_init()
    {
        foreach (self::$settings as $setting) {
            register_setting('utoc', $setting);
        }
    }

    public function add_actions()
    {
        add_action('add_meta_boxes', [$this, 'metabox']);
        add_action('admin_menu', [$this, 'add_settings']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_enqueue_scripts', [$this, 'add_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'add_theme']);
        add_action('wp_enqueue_scripts', [$this, 'add_theme']);
        add_action('save_post', [$this, 'save_postadata']);

        add_action('wp_ajax_nopriv_get_utoc_html', [$this, 'ajax_html']);
        add_action('wp_ajax_get_utoc_html', [$this, 'ajax_html']);

        add_action('wp_ajax_nopriv_get_utoc_metabox_html', [$this, 'ajax_html_metabox']);
        add_action('wp_ajax_get_utoc_metabox_html', [$this, 'ajax_html_metabox']);
    }

    function the_post_filter($post_object)
    {
        $post_object->post_content = $this->add_id_headings($post_object->post_content);
    }

    public function add_filters()
    {
        // add_filter('content_save_pre', [$this, 'add_id_headings'], 99, 1);
        add_filter('the_content', [$this, 'add_id_headings'], 30, 1);
        // add_filter('the_post', [$this, 'the_post_filter'], 10, 1);

        $this->add_render_filter();
    }

    public function ajax_html()
    {
        echo $this->get_html($_POST['post_id']);
        die();
    }

    public function ajax_html_metabox()
    {
        $this->html(get_post($_POST['post_id']));
        die();
    }

    private function add_render_filter()
    {
        if (!is_admin())
            add_filter('the_content', [$this, 'render_toc'], 35);
    }

    private function remove_render_filter()
    {
        remove_filter('the_content', [$this, 'render_toc'], 35);
    }

    private function get_html($post_id = null)
    {
        global $post;

        if ($post_id) {
            $post = get_post($post_id);
        }

        $this->remove_render_filter();
        $post_content = apply_filters('the_content', $post->post_content);
        $this->add_render_filter();

        $this->parser->parse($post_content);

        return $this->parser->get_html();
    }
}
