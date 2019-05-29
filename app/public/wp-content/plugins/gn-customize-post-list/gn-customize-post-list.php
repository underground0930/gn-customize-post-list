<?php
/**
 *
 * @link  http://example.com
 * @since 1.0.0
 * @package GN Customize Post List
 *
 * Plugin Name: GN Customize Post List
 * Plugin URI: https://htmlgo.site/
 * Description: このプラグインは、記事一覧をカスタマイズ出来るようにします。
 * Version: 1.0.0
 * Author: go.nishiduka
 * Author URI: https://htmlgo.site/
 * Text Domain: gncpl
 * Domain Path: /languages
**/

define('GNCPL_VERSION', '1.0.0');
define('GNCPL_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('GNCPL_PLUGIN_NAME', trim(dirname(GNCPL_PLUGIN_BASENAME), '/'));
define('GNCPL_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('GNCPL_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));

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
            )
        );
         
        add_action('admin_menu', array($this, 'add_admin_menu'));

        add_filter('manage_posts_columns', array($this, 'add_column_name'));
        add_action('manage_posts_custom_column', array($this, 'add_column_value'), 10, 2);
        add_action('restrict_manage_posts', array($this, 'add_custom_taxonomies_term_filter'));

        add_action('admin_print_styles', array( $this, 'admin_css'));
        add_action('admin_print_scripts', array( $this, 'admin_script'));

        add_action('admin_init', array($this, 'admin_init'));
        
        // プラグイン有効化された時の処理
        register_activation_hook(__FILE__, array($this, 'activationHook'));
        // プラグイン無効化された時の処理
        register_deactivation_hook(__FILE__, array($this, 'deactivationHook'));
        
        add_action('wp_ajax_update_gncpl_options', array($this, 'ajax_update_callback'));
    }

    public function ajax_update_callback()
    {
        $error = new WP_Error();
        $options = $_POST['gncpl_options'];

        if (isset($options) && check_ajax_referer('gncpl_nonce', 'security')) {

            foreach($options as $option){
                foreach($option as $option_child){

                    switch ($option_child['key']) {
                        case 'custom_field':
                            break;
                        case 'taxonomy':
                            break;
                        case 'title':
                            break;
                        case 'date':
                            break;
                        case 'content':
                            break;
                        default:
                            // 不正な値 エラー
                            break;
                    }
                    
                }                
            }


            update_option('gncpl_options', $options);
            $error->add('200', 'success');


        } else {
            $error->add('100', 'security error');
        }
        echo $error->get_error_message();
        exit;
    }
    
    public function admin_script()
    {
        wp_enqueue_script('gncpl_js', GNCPL_PLUGIN_URL . '/admin/js/scripts.js', [ 'wp-element' ], '1.0.0', true);
    }
    public function admin_css()
    {
        // wp_enqueue_style('gncpl_css', GNCPL_PLUGIN_URL . '/admin/css/style.css');
    }

    public function add_admin_menu()
    {
        add_options_page(
            __('GN Customize Post List', 'gn-customize-post-list'),
            __('GN Customize Post List', 'gn-customize-post-list'),
            'administrator',
            'gn-customize-post-list',
            array($this,'custom_admin'),
            '',
            2
        );
    }
    
    public function custom_admin()
    {
        $this->post_types = array_merge($this->post_types, get_post_types(array('public'  => true, '_builtin' => false ), 'object')); ?>
<div class="gncpl-admin-wrap">
    <script>
        <?php
        $post_types_array = array();
        
        foreach ($this->post_types as $post_type) {
            array_push($post_types_array, array(
                'name' => $post_type->name,
                'label' => $post_type->label,
            ));
        }
                    
        echo 'var admin_ajax_url  = "' . admin_url('admin-ajax.php', __FILE__) . '";';
        echo 'var gncpl_admin_post_types = '. json_encode($post_types_array) . ';';
        echo 'var gncpl_admin_options = ' . json_encode(get_option('gncpl_options'))  . ';';
        echo 'var security = "' . wp_create_nonce('gncpl_nonce') . '";'; ?>
    </script>

    <h2 class="gncpl-admin-title"><?php echo GNCPL_PLUGIN_NAME; ?>
    </h2>
    <?php wp_nonce_field('nonce-key', 'gncpl-page'); ?>
    <div id="gncpl-admin-app"></div>
</div>
<?php
    }
        
    public function admin_init()
    {
    }
        
    public function add_column_name($columns)
    { // 一覧に列を追加

        global $post;
        $post_type = get_post_type($post);

        if (isset($this->gncpl_options[$post_type])) {
            $new_array = $this->default_column;
            $count = 0;
            foreach ($this->gncpl_options[$post_type] as $item) {
                $name = $item['key'];
                switch ($name) {
                    case 'custom_field':
                        $new_array['custom_field_val_'. $item['value']] = esc_html( $item['label'] );
                        break;
                    case 'taxonomy':
                        $new_array['taxonomy_val_'. $item['value']] = esc_html( $item['label'] );
                        break;
                    case 'title':
                        $new_array['title'] = 'タイトル';
                        break;
                    case 'date':
                        $new_array['date'] = '日時';
                        break;
                    case 'content':
                        $new_array['content'] = '本文';
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
    { // 列に値を表示
        global $post;
        
        $result_name = $column_name;
        $result_val = '';
        
        if (strpos($result_name, 'custom_field_val_') !== false) {
            $result_val = substr($result_name, mb_strlen('custom_field_val_'));
            $result_name = 'custom_field';
        } elseif (strpos($result_name, 'taxonomy_val_') !== false) {
            $result_val = substr($result_name, mb_strlen('taxonomy_val_'));
            $result_name = 'taxonomy';
        }

        $post_type = get_post_type($post);
        switch ($result_name) {
            case 'content':
                $content = strip_tags(get_post_field('post_content', $post_id));
                $content = mb_strlen($content) > 30 ? mb_substr($content, 0, 20, 'UTF-8') . '...' : $content;
                echo $content;
                break;
            
            case 'custom_field':
                echo get_post_field($result_val, $post_id);
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
