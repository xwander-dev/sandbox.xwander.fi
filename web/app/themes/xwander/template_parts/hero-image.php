<?php 

global $post;


if( get_field('hero_image',$post->ID) ) 
{
	$image_id = get_field('hero_image' );
} 
else 
{
	$image_id = get_field('default_hero_image', 'option');
}

if( get_field('hero_image_mobile',$post->ID) ) 
{
	$mobile_id = get_field('hero_image_mobile',$post->ID);
}
else
{
	$mobile_id = $image_id;
}

$bokun_manager = Bokun_Data_Manager::get_instance();
$bokun_title = $bokun_manager->get_title();
$bokun_excerpt = $bokun_manager->get_excerpt();

$hero_title = !empty($bokun_title) ? $bokun_title : (get_field("hero_title") ?: get_the_title($post->ID));
$hero_excerpt = !empty($bokun_excerpt) ? $bokun_excerpt : (get_field("hero_description") ?: null);

$hero_video_enabled = false;
$hero_video_url = get_field('hero_video_url');

if ($hero_video_url && get_field('hero_enable_video')) {
    $hero_video_enabled = false;
    //$hero_video_enabled = true;
    if (!parse_url($hero_video_url, PHP_URL_QUERY)) {
        $hero_video_url .= '?wm=1';
    }
}

$is_mobile = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $_SERVER['HTTP_USER_AGENT']);
$videoCookie = isset($_COOKIE['canLoadVideo']) ? filter_var($_COOKIE['canLoadVideo'], FILTER_VALIDATE_BOOLEAN) : false;
$video_check = $hero_video_enabled && $videoCookie;
?>

<script type="text/javascript">
    var heroVideoUrl = <?php echo json_encode($hero_video_enabled ? $hero_video_url : null, JSON_UNESCAPED_SLASHES); ?>;
    var isMobile = <?php echo json_encode($is_mobile); ?>;
</script>

<?php if (!is_singular('post')) : ?>
    <div class="hero-section<?= $video_check ? ' has-video' : '' ?>">
        <?php if (is_home()) : ?>
            <h1><?php echo __('Blog', 'theme'); ?></h1>
        <?php else : ?>
            <div class="wm-image-container hero-image-container">
                <div id="video_container">
                    <?php if ($video_check): ?>
                        <div class="hero-video-wrapper" style="padding: 56.25% 0 0 0; position: relative;">
                            <iframe id="hero_video"
                                    src="<?php echo $hero_video_url; ?>&autoplay=1&loop=1&title=0&byline=0&portrait=0&background=1"
                                    style="position:absolute; top:0; left:0; width:100%; height:100%; display:none;" frameborder="0"
                                    allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                            <script src="https://player.vimeo.com/api/player.js"></script>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="video_placeholder" class="hero-image-wrapper">
                    <div aria-hidden="true" style="width:100%;padding-bottom:66.66666666666667%"></div>
                    <picture>
                        <source 
                            media="(max-width: 420px)"
                            srcset="<?php echo wp_get_attachment_image_url( $mobile_id, 'hero-mobile') ?> 1x, <?php echo wp_get_attachment_image_url( $mobile_id, 'hero-mobile-2x') ?> 2x">

                        <source 
                            media="(max-width: 1024px)"
                            srcset="<?php echo wp_get_attachment_image_url( $image_id, 'large') ?> 1x">	

                        <source 
                            media="(max-width: 1440px)" 
                            srcset="<?php echo wp_get_attachment_image_url( $image_id, 'hero') ?> 1x, <?php echo wp_get_attachment_image_url( $image_id, 'hero-2x') ?> 2x">

                        <img 
                            src="<?php echo wp_get_attachment_image_url( $image_id, 'full') ?>"
                            alt="<?php get_post_meta( $image_id, '_wp_attachment_image_alt', true) ?>"
                            class="wm-image-cover hero-image-cover">
                    </picture>
                </div>
            </div>
            <div class="hero-top-overlay"></div>
            <div class="hero-content-container">
                <div class="hero-content">
                    <div class="home-banner-text">
                        <h1 class="has-text-align-center has-text-color">
                            <?php if($hero_title) echo $hero_title; ?>
                        </h1>

                        <?php if($hero_excerpt): ?>
                            <h2 class="home-banner-subhead has-text-align-center has-text-color"><?php echo $hero_excerpt; ?></h2>
                        <?php endif; ?>		            
                        <?php if (is_singular( 'tour' )) include_once('hero-meta.php'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>	
    </div>
<?php endif; ?>	