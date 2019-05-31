<?php
/**
 *
 * @link  http://example.com
 * @since 1.0.0
 * @package GN Customize Post List
 *
 * Plugin Name: GN Customize Post List
 * Plugin URI: https://github.com/underground0930/gn-customize-post-list
 * Description: You can customize the display items of the article list.
 * Version: 1.0.0
 * Author: go.nishiduka
 * Author URI: https://htmlgo.site/
 * Text Domain: gn-customize-post-list
**/

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

define('GNCPL_VERSION', '1.0.0');
define('GNCPL_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('GNCPL_PLUGIN_NAME', trim(dirname(GNCPL_PLUGIN_BASENAME), '/'));
define('GNCPL_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('GNCPL_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));

define('INPUT_MAX_LENGTH', 30);

class Gn_customize_post_list
{
    private $post_types;
    private $gncpl_options;
    private $default_column = array(
        'cb'    => '<input type="checkbox" />',
    );

    public function __construct()
    {
        $this->gncpl_options = get_option(' gncpl_options ');
        $this->post_types = array(
            'post' => (object) array(
                'name' => 'post',
                'label' => 'POST',
            ),
            'page' => (object) array(
                'name' => 'page',
                'label' => 'PAGE',
            ),
        );

        add_action('admin_menu', array($this, 'add_admin_menu'));

        add_filter('manage_pages_columns', array($this, 'add_column_name'));
        add_action('manage_pages_custom_column', array($this, 'add_column_value'), 10, 2);

        add_filter('manage_posts_columns', array($this, 'add_column_name'));
        add_action('manage_posts_custom_column', array($this, 'add_column_value'), 10, 2);

        add_action('restrict_manage_posts', array($this, 'add_custom_taxonomies_term_filter'));

        add_action('admin_print_scripts-settings_page_'. GNCPL_PLUGIN_NAME, array( $this, 'admin_script'));

        register_activation_hook(__FILE__, array($this, 'activationHook')); // plugin　active
        register_deactivation_hook(__FILE__, array($this, 'deactivationHook')); // plugin　inactive

        add_action('wp_ajax_update_gncpl_options', array($this, 'ajax_update_callback')); // wp_ajax callback
    }


    public function check_length($text, $len)
    {
        $text_length = mb_strlen(trim($text));
        return ($text_length > $len || $text_length === 0) ? true : false;
    }

    public function ajax_update_callback()
    {
        $options = $_POST['gncpl_options'];
        $error = new WP_Error();
        $error_flag = false;
        $error_arr = array();
        $error_texts = array(
            'over' => '「label」 and 「slug」 length is ( 0 < length < ' . INPUT_MAX_LENGTH . ')' ,
            'duplicate' => 'don\'t duplicate'
        );


        if (isset($options) && check_ajax_referer('gncpl_nonce', 'security')) {
            foreach ($options as $key => $option) {
                $i = 0;
                $duplicate_arr = array();
                foreach ($option as $option_child) {
                    switch ($option_child['key']) {

                        case 'custom_field_img':
                        case 'custom_field_text':
                        case 'taxonomy':

                            array_push($duplicate_arr, $option_child['key'] . '_' . $option_child['value']);

                            if ($this->check_length($option_child['label'], INPUT_MAX_LENGTH) || $this->check_length($option_child['value'], INPUT_MAX_LENGTH)) {
                                $error_arr[$key][$i] = $error_texts['over'];
                                $error_flag = true;
                            } elseif (array_count_values($duplicate_arr)[ $option_child['key'] . '_' . $option_child['value']] > 1) {
                                $error_arr[$key][$i] = $error_texts['duplicate'];
                                $error_flag = true;
                            } else {
                                $option_child = array(
                                    'key' => $option_child['key'],
                                    'label' => $option_child['label'],
                                    'value' => $option_child['value'],
                                );
                            }
                            break;

                        case 'title':
                        case 'date':
                        case 'content':
                        case 'author':
                        case 'comments':

                            array_push($duplicate_arr, $option_child['key']);

                            if (array_count_values($duplicate_arr)[$option_child['key']] > 1) {
                                $error_arr[$key][$i] = $error_texts['duplicate'];
                                $error_flag = true;
                            } else {
                                $option_child = array(
                                    'key' => $option_child['key'],
                                    'label' => "",
                                    'value' => "",
                                );
                            }

                            break;

                        default:
                            unset($option_child);
                            break;
                    }
                    $i++;
                }
            }

            if (!$error_flag) { // success
                update_option('gncpl_options', $options);
                $error->add('200', '');
            } else { // error
                $error->add('101', json_encode($error_arr));
            }
        } else { // security error
            $error->add('100', 'security');
        }
        echo $error->get_error_message();
        exit;
    }

    public function admin_script()
    {
        wp_enqueue_script('gncpl_js', GNCPL_PLUGIN_URL . '/admin/js/scripts.js', array(), '1.0.0', true);
    }

    public function add_admin_menu()
    {
        $result = add_options_page(
            'GN Customize Post List',
            'GN Customize Post List',
            'administrator',
            'gn-customize-post-list',
            array($this,'custom_admin'),
            '',
            2
        );
    }

    public function custom_admin()
    {
        include_once('includes/admin-view.php');
    }

    public function add_column_name($columns)
    {
        global $post;
        $post_type = get_post_type($post);
        $post_type = $post_type ? $post_type : 'page';

        if (isset($this->gncpl_options[$post_type])) {
            $new_array = $this->default_column;
            $count = 0;
            foreach ($this->gncpl_options[$post_type] as $item) {
                $name = $item['key'];
                switch ($name) {
                    case 'custom_field_img':
                    case 'custom_field_text':
                    case 'taxonomy':
                        $new_array[ $name . '_val_'. $item['value']] = esc_html($item['label']);
                        break;
                    case 'title':
                        $new_array['title'] = 'title';
                        break;
                    case 'date':
                        $new_array['date'] = 'date';
                        break;
                    case 'content':
                        $new_array['content'] = 'content';
                        break;
                    case 'comments':
                        $new_array['comments'] ='comments';
                        break;
                    case 'author':
                        $new_array['author'] = 'author';
                        break;
                    default:
                        break;
                }
            }
            $columns = $new_array;
        }

        return $columns;
    }


    public function add_column_value($column_name, $post_id)
    {
        global $post;

        $result_name = $column_name;
        $result_val = '';

        if (strpos($result_name, 'custom_field_text_val_') !== false) {
            $result_val = substr($result_name, mb_strlen('custom_field_text_val_'));
            $result_name = 'custom_field_text';
        } elseif (strpos($result_name, 'custom_field_img_val_') !== false) {
            $result_val = substr($result_name, mb_strlen('custom_field_img_val_'));
            $result_name = 'custom_field_img';
        } elseif (strpos($result_name, 'taxonomy_val_') !== false) {
            $result_val = substr($result_name, mb_strlen('taxonomy_val_'));
            $result_name = 'taxonomy';
        }

        $post_type = get_post_type($post);
        switch ($result_name) {
            case 'content':
                $val = strip_tags(get_post_field('post_'. $result_name, $post_id));
                $val = mb_strlen($val) > 50 ? mb_substr($val, 0, 50, 'UTF-8') . '...' : $val;
                echo $val;
                break;

            case 'custom_field_text':
                echo esc_html(get_post_field($result_val, $post_id));
                break;

            case 'custom_field_img':
                echo "<img width='50' src='". wp_get_attachment_image_src(get_post_field($result_val, $post_id))[0]  ."' alt=''>" ;
                break;

            case 'taxonomy':
                $current_term = get_the_terms($post_id, $result_val);
                if ($current_term) {
                    foreach ($current_term as $term) {
                        if ($post_type === 'post' && $result_val === 'category') {
                            echo "<a href='${admin_url()}edit.php?category_name={$term->slug}'>{$term->name}</a>" . ($term !== end($current_term) ? ', ' : '');
                        } elseif ($post_type === 'post' && $result_val === 'post_tag') {
                            echo "<a href='${admin_url()}edit.php?tag={$term->slug}'>{$term->name}</a>" . ($term !== end($current_term) ? ', ' : '');
                        } else {
                            echo "<a href='${admin_url()}edit.php?{$result_val}={$term->slug}&post_type={$post_type}'>{$term->name}</a>" . ($term !== end($current_term) ? ', ' : '');
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    /**
     * 管理画面の投稿一覧にカスタムフィールドの絞り込み選択機能を追加します。
     */

    public function add_custom_taxonomies_term_filter()
    {
        global $post_type;
        $taxonomies = get_taxonomies(array( 'object_type' => array( $post_type ), '_builtin' => false ), 'object');

        foreach ($taxonomies as $value) {
            wp_dropdown_categories(array(
                'show_option_all' => $value->label,
                'orderby' => 'name',
                'selected' => get_query_var($value->name),
                'name' => $value->name,
                'taxonomy' => $value->name,
                'value_field' => 'slug',
                'show_count' => true,
                'hide_if_empty' => true
            ));
        }
    }
    public function activationHook()
    {
        if (!get_option('gncpl_options')) {
            update_option('gncpl_options', array());
        }
    }
    public function deactivationHook()
    {
        delete_option('gncpl_options');
    }
}

new Gn_customize_post_list();
