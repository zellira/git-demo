<?php
add_action('wp_ajax_zellira_filter', 'zellira_filter');           // for logged in user
add_action('wp_ajax_nopriv_zellira_filter', 'zellira_filter');

function zellira_filter(){
    $typeFilter     =   !empty($_POST['filter_type']) ? $_POST['filter_type'] : null;
    $order          =   !empty($_POST['order'])? $_POST['order'] : 'DESC';
    $sticky         =   !empty($_POST['sticky_post'])? $_POST['sticky_post'] : null;
    $argSticky      =   null;
    $orderby        =   !empty($_POST['orderby']) ? $_POST['orderby'] : 'date';
    $post_format    =   !empty($_POST['post_format']) ? $_POST['post_format'] : null;
    $author         =   !empty($_POST['author']) ? $_POST['author'] : null;
    $tag            =   !empty($_POST['tag']) ? $_POST['tag'] : null;
    $argTag         =   null;
    $day            =   !empty($_POST['day']) ? $_POST['day'] : null;
    $month          =   !empty($_POST['month']) ? $_POST['month'] : null;
    $year           =   !empty($_POST['year']) ? $_POST['year'] : null;
    $cat            =   !empty($_POST['cat'])? $_POST['cat'] : null;
    $search         =   !empty($_POST['search_query']) ? $_POST['search_query'] : null;
    $post_status = !empty($_POST['post_status']) ? $_POST['post_status'] : 'publish';
    $posts_per_page =   null;
    $paged          =   null;
    $loop_paged     =   null;
    if($typeFilter == 'scroll') {
        $posts_per_page =   get_option('posts_per_page');
        $paged          =   !empty($_POST['page_no'])? $_POST['page_no'] : null;
        $loop_paged = (get_query_var('page')) ? get_query_var('page') : $paged;
    }
    if(!empty($tag)){
        $argTag = array(
            'taxonomy' => 'post_tag',
            'field' => 'id',
            'terms' => $tag,
        );
    }

    if(!empty($sticky)){
        $sticky = get_option("sticky_posts");
    }
    $args = array(
        's' =>  $search,
        'order'   => $order,
        'orderby' => $orderby,
        'post_type' => 'post',
        'category_name' => $cat,
        'post_status' => $post_status,
        'author' => $author,
        'date_query' => array(
            array(
                'year'  => $year,
                'month' => $month,
                'day'   => $day,
            )
        ),
        'paged'  => $loop_paged,
        'posts_per_page' => $posts_per_page,
        'relation' => 'AND',
        'post__in'   => $sticky,
    );
    if($post_format !== null){
        if($post_format == "standard"){
            $type = array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video');
            $argPost =   array(
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'terms' => $type,
                'operator' => 'NOT IN'
            );
        }else{

            $type = array('post-format-'.$post_format);
            $argPost =   array(
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'terms' => $type
            );
        }
        $args['tax_query'] = array(
            'relation' => 'AND',
            $argPost,
            $argTag,
        );
    }else{
        $args['tax_query'] = array(
                $argTag
        );
    }
    $ajax_query =  new WP_Query( $args );


    if ( $ajax_query->have_posts() ) : while ( $ajax_query->have_posts() ) : $ajax_query->the_post();
        get_template_part( 'content', get_post_format() );
    endwhile;
    endif;
    die();
}

