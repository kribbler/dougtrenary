<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
	<![endif]-->
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
	
	<link rel="stylesheet" type='text/css' media='all' href="<?php echo get_stylesheet_directory_uri(); ?>/style-woocommerce.css" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<header id="masthead" class="site-header" role="banner">
			<!-- 
			<a class="home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
				<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
			</a>
 			-->
 			<div id="header_links">
 				<a target="_blank" href="http://www.facebook.com/pages/Doug-Trenarys-Fast-Track-Inc/111627808876369?sk=info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/facebook.png" title="Facebook" alt="Facebook" /></a>
 				<a target="_blank" href="http://www.youtube.com/user/DougTrenary?ob=0&feature=results_main"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/you-tube1.png" title="Youtube" alt="Youtube" /></a>
 				<a href="<?php echo get_site_url()?>/checkout/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cart.png" title="Your Shopping Cart" alt="Your Shopping Cart" /></a>
				<?php if( !is_user_logged_in() ):?>
					<a id="dtu_login" href="<?php echo site_url();?>/login/">DTU Member Login</a>
				<?php else: ?>
					<a id="dtu_login" href="<?php echo site_url();?>/membership/">DTU Members Area</a>
				<?php endif; ?>
 			</div>
 			
 			<?php if ( is_active_sidebar( 'sidebar-3' ) ) :?>
				<div id="header_widget_3">
					<?php dynamic_sidebar( 'sidebar-3' ); ?>
				</div>
 			<?php endif; ?>
			<div id="navbar" class="navbar">
				<nav id="site-navigation" class="navigation main-navigation" role="navigation">
					<h3 class="menu-toggle"><?php _e( 'Menu', 'twentythirteen' ); ?></h3>
					<a class="screen-reader-text skip-link" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentythirteen' ); ?>"><?php _e( 'Skip to content', 'twentythirteen' ); ?></a>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
					<?php get_search_form(); ?>
				</nav><!-- #site-navigation -->
			</div><!-- #navbar -->
		</header><!-- #masthead -->
<?php if (is_front_page()) {?>
	<div id="scrolling_marquee">
		<?php
		newannouncement( $group = "GROUP1" ); //this adds the scrolling marque
		?>
	</div>
<?php } ?>
		<div id="main" class="site-main____">
