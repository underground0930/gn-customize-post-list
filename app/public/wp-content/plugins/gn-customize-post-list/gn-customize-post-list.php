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
 * Text Domain: gcpl
 * Domain Path: /languages
**/

define('GCPL_VERSION', '1.0.0');
define('GCPL_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('GCPL_PLUGIN_NAME', trim(dirname(GCPL_PLUGIN_BASENAME), '/'));
define('GCPL_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
define('GCPL_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));

class Gn_customize_post_list
{
    private $post_types;
    private $gcpl_options;
    private $select_options = array(
      'title' => 'タイトル',
      'content' => '本文',
      'custom_field' => 'カスタムフィールド',
      'taxonomy' => 'タクソノミー',
      'date' => '日時',
    );
    private $default_column = array(
        'cb'    => '<input type="checkbox" />',
    );
    
    public function __construct()
    {
        $this->gcpl_options = get_option(' gcpl_options ');
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
    }


    public function admin_script()
    {
        wp_enqueue_script('gcpl_js', GCPL_PLUGIN_URL . '/admin/js/scripts.js', [ 'wp-element' ], '1.0.0', true );
    }
    public function admin_css()
    {
        wp_enqueue_style('gcpl_css', GCPL_PLUGIN_URL . '/admin/css/style.css');
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
        // $taxonomies = get_taxonomies( array( 'object_type' => array( 'news'), '_builtin' => false  ));
        $this->post_types = array_merge($this->post_types, get_post_types(array('public'  => true, '_builtin' => false ), 'object')); ?>
<div class="gcpl-admin-wrap">
    <h2 class="gcpl-title"><?php echo GCPL_PLUGIN_NAME; ?>
    </h2>
    <p class="gcpl-text"><b>各一覧画面をカスタマイズします</b></p>
    <form class="gcpl-form" method="post" action=''>
        <?php
            wp_nonce_field('nonce-key', 'gcpl-page');
        foreach ($this->post_types as $post_type):
        ?>
        <h4>【<?php echo $post_type->label; ?>】
        </h4>
        <ul>
            <?php for ($i=0; $i<5; $i++):  ?>
            <li style="display:inline-block;">
                <?php echo $i+1; ?>列目 : <select
                    name="<?php echo "gcpl_options[{$post_type->name}][{$i}]" ; ?>">
                    <?php
                            $select_count = 0;
        foreach ($this->select_options as $key => $value):
                        ?>
                    <option
                        value='<?php echo $key; ?>'
                        <?php echo (isset($this->gcpl_options[$post_type->name][$i]) && $this->gcpl_options[$post_type->name][$i] == $key) ? "selected" : ''; ?>><?php echo $value; ?>
                    </option>
                    <?php  endforeach; ?>
                </select>
            </li>
            <?php endfor; ?>
        </ul>
        <hr />
        <?php  endforeach; ?>
        <p>
            <input type='submit' value="save" class="button button-primary button-large">
        </p>
    </form>
</div>
<?php
    }
        
    public function admin_init()
    {
        if (filter_input(INPUT_POST, 'gcpl-page')) {
            if (check_admin_referer('nonce-key', 'gcpl-page')) {
                if ($gcpl_options = filter_input(INPUT_POST, 'gcpl_options', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)) {
                    update_option('gcpl_options', $gcpl_options);
                } else {
                    update_option('gcpl_options', '');
                }
                wp_safe_redirect(menu_page_url('gcpl-page', false));
            }
        }
    }
        
    public function add_column_name($columns)
    { // 一覧に列を追加

        global $post;
        $post_type = get_post_type($post);

        if (isset($this->gcpl_options[$post_type])) {
            $new_array = $this->default_column;
            foreach ($this->gcpl_options[$post_type] as $item) {
                $new_array[$item] = $this->select_options[$item];
            }
            $columns = $new_array;
        }

        return $columns;
    }
    
    public function add_column_value($column_name, $post_id)
    { // 列に値を表示
        global $post;
        $post_type = get_post_type($post);
        switch ($column_name) {
            case 'content':
                $content = strip_tags(get_post_field('post_content', $post_id));
                $content = mb_strlen($content) > 20 ? mb_substr($content, 0, 20, 'UTF-8') . '...' : $content;
                echo $content;
                break;
            
            case 'custom_field':
                echo get_post_field('名前', $post_id);
                // $taxonomy =
        }
        if ($column_name == $post_type) {
            if ($post_type == 'post') {
                $current_term = get_the_terms($post_id, 'category');
                if (isset($current_term)) {
                    foreach ($current_term as $term) {
                        echo "<a href='${admin_url()}edit.php?category_name={$term->slug}&post_type={$post_type}'>{$term->name}!</a>";
                    }
                }
            } else {
                $current_term = get_the_terms($post_id, 'category_' . $post_type);
                if (isset($current_term)) {
                    foreach ($current_term as $term) {
                        echo "<a href='${admin_url()}edit.php?category_news={$term->slug}&post_type={$post_type}'>{$term->name}</a>" . ($term !== end($current_term) ? ',' : '');
                    }
                }
            }
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
                'hide_empty' => 0,
                'name' => $value->name,
                'taxonomy' => $value->name,
                'value_field' => 'slug',
            ));
        }
    }
}

new Gn_customize_post_list();
