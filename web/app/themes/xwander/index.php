<?php
/**
 * Blog template.
 */

$container_class = apply_filters('neve_container_class_filter', 'container', 'blog-archive');

get_header();

$wrapper_classes = [ 'posts-wrapper' ];
if (!neve_is_new_skin()) {
	$wrapper_classes[] = 'row';
}

?>
	<div class="<?php echo esc_attr($container_class); ?> archive-container">
		<div class="row">
			<?php do_action('neve_do_sidebar', 'blog-archive', 'left'); ?>
            <div class="nv-index-posts blog col">
                <?php
                do_action('neve_before_loop');
                do_action('neve_page_header', 'index');
                $tags = get_tags();
                $selected_tag = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
                ?>
                <div class="tags-container">
                    <a href="<?php echo esc_url(home_url( '/blog' )); ?>" class="tag-btn <?php echo $selected_tag == '' ? 'selected' : ''; ?>">All Posts</a>
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(add_query_arg('tag', $tag->slug, home_url( '/blog' ))); ?>" class="tag-btn <?php echo $selected_tag == $tag->slug ? 'selected' : ''; ?>">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php

                do_action('neve_before_posts_loop');

                $tag = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
                if ($tag && $tag != 'all') {
                    $args = array(
                        'post_type' => 'post',
                        'tag' => $tag
                    );
                    $query = new WP_Query( $args );

                    if ($query->have_posts()) {
                        echo '<div class="' . esc_attr(join(' ', $wrapper_classes )) . '">';

                        $pagination_type = get_theme_mod('neve_pagination_type', 'number');

                        while ($query->have_posts()) {
                            $query->the_post();
                            do_action('neve_loop_entry_before');
                            get_template_part('template-parts/content', get_post_type());
                            do_action('neve_loop_entry_after');
                        }

                        if (!is_singular()) {
                            $total_posts = $query->found_posts;
                            $posts_remaining = $total_posts - ($paged * $posts_per_page);
                            echo '<button class="load-more" data-tag="'.$tag.'" data-posts-remaining="'.$posts_remaining.'"><span>Load More</span></button>';
                        }

                        echo '</div>';
                    } else {
                        get_template_part('template-parts/content', 'none');
                    }

                    wp_reset_postdata();
                } else {
                    if (have_posts()) {
                        echo '<div class="' . esc_attr(join( ' ', $wrapper_classes )) . '">';

                        $pagination_type = get_theme_mod('neve_pagination_type', 'number');

                        while (have_posts()) {
                            the_post();
                            do_action('neve_loop_entry_before');
                            get_template_part('template-parts/content', get_post_type());
                            do_action('neve_loop_entry_after');
                        }

                        echo '</div>';

                        if (!is_singular()) {
                            $default_tag = $tag ? $tag : 'all';
                            $total_posts = wp_count_posts()->publish;
                            $displayed_posts = $paged * $posts_per_page;
                            $posts_remaining = $total_posts > $displayed_posts ? $total_posts - $displayed_posts : 0;
                            echo '<button class="load-more" data-tag="'.$default_tag.'" data-posts-remaining="'.$posts_remaining.'"><span>Load More</span></button>';
                        }
                    } else {
                        get_template_part('template-parts/content', 'none');
                    }
                }
                ?>
                <div class="w-100"></div>
                <?php do_action('neve_after_posts_loop'); ?>
            </div>
			<?php do_action('neve_do_sidebar', 'blog-archive', 'right'); ?>
		</div>
	</div>
<?php
get_footer();