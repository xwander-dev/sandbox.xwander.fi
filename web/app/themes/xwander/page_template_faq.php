<?php

/* Template Name: FAQ Page */

get_header();

$post_id = get_the_ID();
$faq_data = get_field('faq_categories', $post_id);

?>

<div class="faq-container" itemscope itemtype="https://schema.org/FAQPage">
    <div id="faq_overlay" class="sidebar-overlay"></div>
    <aside class="faq-sidebar">
        <button id="faq_sidebar_close" class="close-sidebar"></button>
        <div class="faq-sidebar-inner">
            <h2><?php echo __('Table of Contents', 'xwander'); ?></h2>
            <ul>
                <?php if (!empty($faq_data)): ?>
                    <?php $question_count = 1; ?>
                    <?php foreach ($faq_data as $category): ?>
                        <li>
                            <strong><?php echo isset($category['category_title']) ? esc_html($category['category_title']) : __('No Title', 'xwander'); ?></strong>
                            <ul>
                                <?php if (!empty($category['questions']) && is_array($category['questions'])): ?>
                                    <?php foreach ($category['questions'] as $question): ?>
                                        <li>
                                            <a href="#answer-<?php echo $question_count; ?>">
                                                <?php echo esc_html($question['question_title'] ?? __('No Title', 'xwander')); ?>
                                            </a>
                                        </li>
                                        <?php $question_count++; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><?php echo __('No questions available', 'xwander'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><?php echo __('No categories available', 'xwander'); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>
    <main class="faq-content">
        <button id="faq_sidebar_toggle" class="toggle-sidebar" title="Toggle Sidebar"></button>
        <?php while (have_posts()): the_post(); ?>
            <div><?php the_content(); ?></div>
        <?php endwhile; ?>

        <?php if (!empty($faq_data)): ?>
            <?php $question_count = 1; ?>
            <?php foreach ($faq_data as $category): ?>
                <section>
                    <h2><?php echo isset($category['category_title']) ? esc_html($category['category_title']) : __('No Title', 'xwander'); ?></h2>
                    <?php if (!empty($category['questions']) && is_array($category['questions'])): ?>
                        <?php foreach ($category['questions'] as $question): ?>
                            <div id="answer-<?php echo $question_count; ?>" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                <h3 itemprop="name"><?php echo esc_html($question['question_title'] ?? __('No Title', 'xwander')); ?></h3>
                                <div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                    <div itemprop="text"><?php echo wp_kses_post($question['question_content'] ?? ''); ?></div>
                                </div>
                            </div>
                            <?php $question_count++; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><?php echo __('No questions available', 'xwander'); ?></p>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="scroll-to-top" id="scrollToTop">
            <a href="#" id="scroll_link" class="scroll-to-top__link" title="Up">
                <i class="button-up"></i>
            </a>
        </div>
    </main>
</div>

<?php get_footer(); ?>

<script type="text/javascript">
    jQuery.fn.debounce = function(fn, delay) {
        var timer = null;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(context, args);
            }, delay);
        };
    };

    jQuery(document).ready(function($) {
        var heroBannerHeight = $('.hero-section').outerHeight();
        var footerHeight = $('.site-footer').outerHeight();
        var activeClass = 'scrolled';
        var bottomClass = 'bottom';
        var isVisible = false;
        var hasBeenClicked = false;
        var lastClickTopOffset = 0;
        var activeScrollClass = 'active';
        var reverseClass = 'reverse';

        function applyStickyState() {
            var currentPosition = $(window).scrollTop();
            var footerOffset = $('.site-footer').offset().top;
            var sidebarHeight = $('.faq-sidebar').outerHeight();

            if (window.innerWidth >= 992) {
                if (currentPosition > heroBannerHeight) {
                    if (currentPosition + sidebarHeight < footerOffset) {
                        $('.faq-sidebar').css({
                            'position': 'fixed',
                            'top': '0',
                            'padding-bottom': '0'
                        }).show();
                        $('.faq-sidebar-inner').removeClass(bottomClass);
                    } else {
                        $('.faq-sidebar').css({
                            'position': 'fixed',
                            'top': '0',
                            'padding-bottom': footerHeight + 'px'
                        }).show();
                        $('.faq-sidebar-inner').addClass(bottomClass);
                    }
                } else {
                    $('.faq-sidebar').css({
                        'position': 'relative',
                        'top': 'auto',
                        'padding-bottom': '0'
                    }).show();
                    $('.faq-sidebar-inner').removeClass(bottomClass);
                }

                if (currentPosition >= heroBannerHeight) {
                    $(document.body).addClass(activeClass);
                } else {
                    $(document.body).removeClass(activeClass);
                }
            } else {
                $('.faq-sidebar-inner').removeClass(bottomClass);
            }
        }

        function handleResize() {
            heroBannerHeight = $('.hero-section').outerHeight();
            footerHeight = $('.site-footer').outerHeight();
            applyStickyState();
        }

        function toggleSidebar() {
            $('.faq-sidebar').toggleClass('open');
            $('#faq_overlay').toggleClass('active');
        }

        function closeSidebar() {
            $('.faq-sidebar').removeClass('open');
            $('#faq_overlay').removeClass('active');
        }

        function scrollFunction() {
            var topOffset = $(window).scrollTop();

            if (topOffset > 0) {
                if (!isVisible) {
                    $('#scroll_link').addClass(activeScrollClass);
                    isVisible = true;
                }

                if ($('#scroll_link').hasClass(reverseClass)) {
                    $('#scroll_link').attr('title', 'Up');
                    $('#scroll_link').removeClass(reverseClass);
                }
            } else {
                if (isVisible && !hasBeenClicked) {
                    $('#scroll_link').removeClass(activeScrollClass);
                    isVisible = false;
                }

                if (!$('#scroll_link').hasClass(reverseClass)) {
                    $('#scroll_link').attr('title', 'Down');
                    $('#scroll_link').addClass(reverseClass);
                }
            }
        }

        $('#faq_sidebar_toggle').on('click', toggleSidebar);
        $('#faq_sidebar_close, #faq_overlay').on('click', closeSidebar);
        $('#scroll_link').click(function() {
            var topOffset = $(window).scrollTop(),
                targetOffset = 0;

            if (!topOffset && lastClickTopOffset) {
                targetOffset = lastClickTopOffset;
            }

            hasBeenClicked = true;
            lastClickTopOffset = topOffset;
            $('html, body').animate({scrollTop: targetOffset}, 100);

            return false;
        });

        $(window).on('scroll', $.fn.debounce(applyStickyState, 15));
        $(window).on('resize', $.fn.debounce(handleResize, 50));
        $(window).on('scroll', $.fn.debounce(scrollFunction, 50));

        applyStickyState();
    });
</script>
