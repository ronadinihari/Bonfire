<?php 
	Assets::add_css(array(
			'css/bootstrap.css'
		));
	Assets::add_js(array(
			'jquery-1.5.min.js',
			'bootstrap-dropdown.js'
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
    
    <?php echo Assets::css(); ?>
    
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
		<div class="container-fluid">
			<a class="brand" href="<?php echo site_url(); ?>" target="_blank"><?php echo config_item('site.title') ?></a>
			<?php echo Contexts::render_menu('text'); ?>
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
		</div>
		</div>
	</div>
	
	<div id="nav-bar">
		<?php if (isset($toolbar_title)) : ?>
			<h1><?php echo $toolbar_title ?></h1>
		<?php endif; ?>
		
		<?php Template::block('sub_nav', ''); ?>
	</div>

	<div class="container-fluid">
		<?php echo Template::yield(); ?>
	</div>
	
	<div id="debug"><!-- Stores the Profiler Results --></div>
	
	<script>
		head.js(<?php echo Assets::external_js(null, true) ?>);
		head.js(<?php echo Assets::module_js(true) ?>);
	</script>
	<?php echo Assets::inline_js(); ?>
</body>
</html>