<?php

add_action('wp_ajax_quads_get_tags', 'quads_ajax_get_tags');

/**
 * Get tags by ajax search
 */
function quads_ajax_get_tags() {
    
    if (empty($_POST['data']))
        wp_die(0);
    
    $q = $_POST['data']['q'];
    
    $tags = get_tags(array('search' => $q));
    $new_tags = array();

    foreach ($tags as $key => $value) {
        $new_tags[$key][$value->slug] = $value->name;
    }
    
    $new_tags = quads_flatten($new_tags);

    echo json_encode(array('q' => $q, 'results' => $new_tags));
    wp_die();
}
