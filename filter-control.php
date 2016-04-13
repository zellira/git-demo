<?php
function filter_init(){
//Important, data filter
    global $wp_query;
    $dateY = "";
    $dateM = "";
    $dateD = "";
    if(is_archive()){
        $dateY = $wp_query->query_vars['year'];
        $dateM = $wp_query->query_vars['monthnum'];
        $dateD = $wp_query->query_vars['day'];
    }
    if ( is_single() || is_tag()) {
        $cats =  get_the_category();
        $cat = $cats[0];
    } else {
        $cat = get_category( get_query_var( 'cat' ) );
    }
    $cat_slug = "";
    if(empty($cat->errors)){
        $cat_slug = $cat->slug;
    }

    $published_posts = wp_count_posts()->publish;
    $posts_per_page = get_option('posts_per_page');
    $page_number_max = ceil($published_posts / $posts_per_page);

    echo '<div id="filter-init" data-number-page="'.$page_number_max.'" data-year="'.$dateY.'" data-day="'.$dateD.'" data-month="'.$dateM.'" data-cat-slug="'.$cat_slug.'"></div>';
}
function filter_sort()
{
    ?>
    <div class="sort pull-left filter-sort-by dropdown">
        <button class="btn dropdownMenu1 dropdown-toggle" type="button"  data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="true">
            <?php _e('Sort by', 'zellira') ?>
            <span class="caret"></span>
        </button>
        <ul class="sort-by sortclick animated fadeIn dropdown-menu">
            <li>
                <a data-filter="sort-by" class="sort-by active" data-post-type="date"> <?php _e('Date', 'zellira') ?></a>
            </li>
            <li>
                <a data-filter="sort-by" data-post-type="title"><?php _e('Titles', 'zellira') ?></a>
            </li>

        </ul>
    </div>
    <?php
}
function filter_order()
{
    ?>
    <div class="sort pull-left  filter-order ">
        <ul class="order-by animated fadeIn">
            <li>
                <a data-filter="order-by" class="fa fa-sort-amount-asc"
                   data-post-type="asc"><span><?php _e('Asc', 'zellira') ?></span></a>
            </li>
            <li>
                <a data-filter="order-by" class="fa fa-sort-amount-desc order-by active"
                   data-post-type="desc"><span><?php _e('Desc', 'zellira') ?></span></a>
            </li>
        </ul>
    </div>
    <?php
}
function filter_sticky()
{
    ?>
    <div class="sort pull-left  filter-sticky">
        <ul class="order-by sticky animated fadeIn">
            <li>
                <a data-filter="sticky" class="fa fa-star-o"
                   data-post-type="sticky_posts"><span><?php _e('Feature', 'zellira') ?></span></a>
            </li>
            <li>
                <a data-filter="sticky" class="fa fa-star order-by not_sticky active"
                   data-post-type=""><span><?php _e('Feature', 'zellira') ?></span></a>
            </li>
        </ul>
    </div>
    <?php
}
function filter_type()
{
    $tag = "";
    if (is_tag()) {
        $tag = get_queried_object();
        $tag = $tag->slug;
    }
    $author = "";
    if (is_author()) {
        $author = get_the_author_meta('ID');
    }
    ?>
    <div data-tag="<?php echo $tag ?>" data-author="<?php echo $author ?>"
         class="page-header-data pull-left sort filter-type dropdown">
        <button class="btn dropdown-toggle dropdownMenu1" type="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="true">
            <?php _e('Filter by', 'zellira') ?>

            <span class="caret"></span>
        </button>
        <ul class="format dropdown-menu animated fadeIn" >
            <li style="display:none">
                <a class="post active" data-filter="format" data-post-type="post"></a>
            </li>
            <li>
                <a class="post" data-filter="format" data-post-type="standard">
                    <i class="dashicons dashicons-format-standard"></i><?php _e('Standard', 'zellira') ?>
                </a>
            </li>
            <?php
            $terms = get_terms("post_format");
            $count = count($terms);
            if ($count > 0) {
                foreach ($terms as $term) {
                    echo '<li>';
                    $res = str_replace('post-format-', '', $term->slug);
                    if ($term->count > 0) {
                        if ($res == "link") {
                            $icon_link = 'link-filter-icon';
                        } else
                            $icon_link = '';
                        echo '<a data-filter="format" data-post-type="' . $res . '" class="post-format-archive-link ' . $res . '" ><i class="dashicons dashicons-format-' . $res . ' ' . $icon_link . '"></i>' . $term->name . '</a>';
                    }
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </div>
    <?php
}

function filter_control()
{
    filter_init();
    ?>
    <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 background-color navbar-fixed-top"
         id="filter-control">

        <div>
            <?php if (is_home() || is_tag() || is_author() || is_category() || is_archive() || is_search()) {
                filter_type();
                filter_sort();
                filter_order();
                filter_sticky();
                if (is_search()) {
                    ?>
                    <div class="search-title col-xs-12 col-md-8 ">
                        <?php if (have_posts()) :
                            ?>
                            <h1 class="pull-right page-header-search"
                                data-search="<?php echo get_search_query() ?>"><?php printf(__('Search Results for: %s', 'zellira'), get_search_query()); ?></h1>
                            <?php
                        endif;
                        get_search_form(); ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class=" pull-right search-filter col-xs-4 col-md-4 ">
                        <?php get_search_form(); ?>
                    </div>
                    <?php
                }
                ?>
            <?php } else {
                ?>
                <header class="col-md-12 entry-header">
                    <?php
                    the_title('<h1 class="entry-title">', '</h1>');
                    ?>
                    <div class=" pull-right btn-single col-xs-12 col-md-5 ">

                        <div id="comment-filter">
                            <button class="btn">
                        <span class="comment pull-right">
                             <i class="fa fa-comments-o"></i>
                             <span class="disqus-comment-count">
                                 <?php if (get_comments_number()) {
                                     printf(_n('One Comment', '%1$s Comments', get_comments_number(), 'zellira'),
                                         number_format_i18n(get_comments_number()));
                                 } else {
                                     echo "0 " . __('Comment', "zellira");
                                 } ?></span>
                         </span>
                            </button>
                        </div>
                        <div id="single-share">
                            <?php share_social(); ?>
                        </div>
                        <div id="search">
                            <?php get_search_form(); ?>
                        </div>
                    </div>
                </header><!-- .entry-header -->
            <?php } ?>
        </div>
        <?php if (is_home() || is_tag() || is_author() || is_category() || is_archive() || is_search()) { ?>
            <div id="filter-selected" class="ccolor_c">
                <p id="format" data-sort="post"><i class="fa fa-remove"></i><span></span></p>

                <p id="sort-by" data-sort="date"><i class="fa fa-remove"></i><span></span></p>

                <p id="order-by" data-sort="desc"><i class="fa fa-remove "></i><span></span></p>
            </div>
        <?php } ?>
    </div>

<?php }