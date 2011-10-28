<?php 
	Assets::add_js(array(
			'jquery-1.6.4.min.js',
		), 
		'external',
		true
	);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title><?php echo isset($toolbar_title) ? $toolbar_title .' : ' : ''; ?> <?php echo config_item('site.title') ?></title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php echo Assets::css(null, 'screen', true); ?>
    
    <script src="<?php echo base_url() .'assets/js/head.min.js' ?>"></script>
	<script>
	head.feature("placeholder", function() {
		var inputElem = document.createElement('input');
		return new Boolean('placeholder' in inputElem);
	});
	</script>
</head>
<body>

	<noscript>
		<p>Javascript is required to use Bonfire's admin.</p>
	</noscript>
	
	<div id="message">
		<?php echo Template::message(); ?>
	</div>

	<div class="topbar" id="topbar" data-dropdown="dropdown">
		<div class="topbar-inner">
			<div class="container">
				<ul class="nav secondary-nav">
					<li class="dropdown">
						<a href="<?php echo site_url(SITE_AREA .'/settings/users/edit/'. $this->auth->user_id()) ?>" id="tb_email" class="dropdown-toggle" title="<?php echo lang('bf_user_settings') ?>">
							<?php echo config_item('auth.use_usernames') ? (config_item('auth.use_own_names') ? $this->auth->user_name() : $this->auth->username()) : $this->auth->email() ?>
						</a>
						
						<ul class="dropdown-menu">
							<li>
								<a href="<?php echo site_url('logout'); ?>">Logout</a>
							</li>
						</ul>
					</li>
				</ul>
				<?php echo Contexts::render_menu('both'); ?>
			</div><!-- /container -->
			<div style="clearfix"></div>
		</div><!-- /topbar-inner -->
		
	</div><!-- /topbar -->
	
	<div id="nav-bar">
		<div class="container">
			<?php if (isset($toolbar_title)) : ?>
				<h1><?php echo $toolbar_title ?></h1>
			<?php endif; ?>
		
			<?php Template::block('sub_nav', ''); ?>
		</div>
	</div>

	<div class="container">
		<?php echo Template::yield(); ?>
	</div>
	
	<footer>
		<div class="container">
			<p>Page rendered in {elapsed_time} seconds. {memory_usage} memory used.<br/>
			Built with <a href="http://cibonfire.com" target="_blank">Bonfire</a></p>
		</div>
	</footer>

	<div id="debug"><!-- Stores the Profiler Results --></div>
	
	<script>
		head.js(<?php echo Assets::external_js(null, true) ?>);
		head.js(<?php echo Assets::module_js(true) ?>);
	</script>
	<?php echo Assets::inline_js(); ?>
</body>
</html>