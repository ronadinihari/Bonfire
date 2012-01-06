<h2>Android Media Upload - Web Access</h2>

<p>Please <?php echo anchor('/login', 'login'); ?> to enter.</p>
<?php  
	// acessing our userdata cookie
	$cookie = unserialize($this->input->cookie($this->config->item('sess_cookie_name')));
	$logged_in = isset ($cookie['logged_in']);
	unset ($cookie);
		
	if ($logged_in) : ?>

	<div class="notification attention" style="text-align: center">
		<?php echo anchor(SITE_AREA, 'Dive into Bonfire\'s Springboard'); ?>
	</div>

<?php endif;?>