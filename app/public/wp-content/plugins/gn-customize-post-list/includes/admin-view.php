<div class="gncpl-admin-wrap">
    <script>
        <?php
            $this->post_types = array_merge($this->post_types, get_post_types(array('public'  => true, '_builtin' => false ), 'object'));
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
            echo 'var security = "' . wp_create_nonce('gncpl_nonce') . '";'; 
        ?>
    </script>
    <h2 class="gncpl-admin-title"><?php echo GNCPL_PLUGIN_NAME; ?></h2>
    <div id="gncpl-admin-app"></div>
</div>