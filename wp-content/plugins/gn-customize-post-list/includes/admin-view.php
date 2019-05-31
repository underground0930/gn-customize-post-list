<?php
    if (! defined('ABSPATH')) {
        exit;
    } // Exit if accessed directly

    $this->post_types = array_merge($this->post_types, get_post_types(array('public'  => true, '_builtin' => false ), 'object'));
    $post_types_array = array();

    foreach ($this->post_types as $post_type) {
        array_push($post_types_array, array(
            'name' => $post_type->name,
            'label' => $post_type->label,
        ));
    }
    $default_option = array(
        'key' => 'title',
        'label' => '',
        'value' => ''
    );
    $default_options = array(
        $default_option,
        array(
            'key' => 'date',
            'label' => '',
            'value' => ''
        )
    );
    $selects = array(
        array( 'key' => 'title', 'label' => 'title' ),
        array( 'key' => 'content', 'label' => 'content' ),
        array( 'key' => 'custom_field_text', 'label' => 'custom field text' ),
        array( 'key' => 'custom_field_img', 'label' => 'custom field img' ),
        array( 'key' => 'taxonomy', 'label' => 'taxonomy' ),
        array( 'key' => 'date', 'label' => 'date' ),
        array( 'key' => 'author', 'label' => 'author' ),
        array( 'key' => 'comments', 'label' => 'comments' )
    );
?>

<div class="gncpl-admin-wrap">
  <script>
    <?php
        echo 'var gncpl_admin_default_option =' . json_encode($default_option) . ';' . "\n";
        echo 'var gncpl_admin_default_options =' . json_encode($default_options) . ';' . "\n";
        echo 'var gncpl_admin_selects =' . json_encode($selects) . ';' . "\n";
        echo 'var gncpl_admin_ajax_url  = "' . admin_url('admin-ajax.php', __FILE__) . '";' . "\n";
        echo 'var gncpl_admin_post_types = '. json_encode($post_types_array) . ';' . "\n";
        echo 'var gncpl_admin_options = ' . json_encode(get_option('gncpl_options'))  . ';' . "\n";
        echo 'var gncpl_admin_security = "' . wp_create_nonce('gncpl_nonce') . '";' . "\n";
    ?>
  </script>
  <h2 class="gncpl-admin-title"><?php echo GNCPL_PLUGIN_NAME; ?>
  </h2>
  <div id="gncpl-admin-app"></div>
</div>