<div class="row">

	<?php if (has_permission('Activities.Own.View')): ?>
	<div class="column size1of4 media-box">
		<a href="<?php echo site_url(SITE_AREA .'/reports/activities/activity_user') ?>">
			<img src="<?php echo Template::theme_url('images/activity-user.png') ?>" />
		</a>

		<p><b><?php echo lang('activity_own'); ?></b><br/>
		<span><?php echo lang('activity_own_description'); ?></span>
		</p>
	</div>
	<?php endif; ?>

	<?php if (has_permission('Activities.User.View')): ?>
	<div class="column size1of4  media-box">
		<a href="<?php echo site_url(SITE_AREA .'/reports/activities/activity_user') ?>">
			<img src="<?php echo Template::theme_url('images/customers.png') ?>" />
		</a>

		<p><b><?php echo lang('activity_users'); ?></b><br/>
		<span><?php echo lang('activity_users_description'); ?></span>
		</p>
	</div>
	<?php endif; ?>

	<?php if (has_permission('Activities.Module.View')): ?>
	<div class="column size1of4  media-box">
		<a href="<?php echo site_url(SITE_AREA .'/reports/activities/activity_module') ?>">
			<img src="<?php echo Template::theme_url('images/product.png') ?>" />
		</a>

		<p><b><?php echo lang('activity_modules'); ?></b><br/>
		<span><?php echo lang('activity_module_description'); ?></span>
		</p>
	</div>
	<?php endif; ?>

	<?php if (has_permission('Activities.Date.View')): ?>
	<div class="column size1of4 media-box">
		<a href="<?php echo site_url(SITE_AREA .'/reports/activities/activity_date') ?>">
			<img src="<?php echo Template::theme_url('images/calendar.png') ?>" />
		</a>

		<p><b><?php echo lang('activity_date'); ?></b><br/>
		<span><?php echo lang('activity_date_description'); ?></span>
		</p>
	</div>
	<?php endif; ?>

</div>


<div class="row">
	<!-- Active Modules -->
	<div class="column size1of2">
		<h3><?php echo lang('activity_top_modules'); ?></h3>
		<?php if (isset($top_modules) && is_array($top_modules) && count($top_modules)) : ?>

			<ol>
			<?php foreach ($top_modules as $top_module) : ?>
				<li><strong><?php echo $top_module->module; ?></strong> : <em><?php echo $top_module->activity_count; ?></em> <?php echo lang('activity_logged'); ?></li>
			<?php endforeach; ?>
			</ol>

		<?php else : ?>
			<?php echo lang('activity_no_top_modules'); ?>
		<?php endif; ?>
	</div>

	<!-- Active Users -->
	<div class="column size2of2 last-column">
		<h3><?php echo lang('activity_top_users'); ?></h3>
		<?php if (isset($top_users) && is_array($top_users) && count($top_users)) : ?>

			<ol>
			<?php foreach ($top_users as $top_user) : ?>
				<li><strong><?php echo ($top_user->username == '' ? 'Not found':$top_user->username); ?></strong> : <em><?php echo $top_user->activity_count; ?></em>  <?php echo lang('activity_logged'); ?></li>
			<?php endforeach; ?>
			</ol>

		<?php else : ?>
			<?php echo lang('activity_no_top_users'); ?>
		<?php endif; ?>
	</div>
</div>



<?php if (has_permission('Activities.Own.Delete')): ?>
<div class="box delete rounded">
	<a class="btn danger" id="delete-activity_own"><?php echo lang('activity_own_delete'); ?></a>
	<?php echo lang('activity_delete_own_note'); ?>
	<select id="activity_own_select">
		<option value="<?php echo $this->auth->user_id(); ?>"><?php echo $this->auth->username(); ?></option>))
	</select>
</div>
<?php endif; ?>

<?php if (has_permission('Activities.User.Delete')): ?>
<div class="box delete rounded">
	<a class="btn danger" id="delete-activity_user"><?php echo lang('activity_user_delete'); ?></a>
	<?php echo lang('activity_delete_user_note'); ?>
	<select id="activity_user_select">
		<option value="all"><?php echo lang('activity_all_users'); ?></option>
	<?php foreach ($users as $au) : ?>
		<option value="<?php echo $au->id; ?>"><?php echo $au->username; ?></option>
	<?php endforeach; ?>
	</select>
</div>
<?php endif; ?>

<?php if (has_permission('Activities.Module.Delete')): ?>
<div class="box delete rounded">
	<a class="btn danger" id="delete-activity_module"><?php echo lang('activity_module_delete'); ?></a>
	<?php echo lang('activity_delete_module_note'); ?>
	<select id="activity_module_select">
		<option value="all"><?php echo lang('activity_all_modules'); ?></option>
		<option value="core"><?php echo lang('activity_core'); ?></option>
	<?php foreach ($modules as $mod) : ?>
		<option value="<?php echo $mod; ?>"><?php echo $mod; ?></option>
	<?php endforeach; ?>
	</select>
</div>
<?php endif; ?>

<?php if (has_permission('Activities.Date.Delete')): ?>
<div class="box delete rounded">
	<a class="btn danger" id="delete-activity_date"><?php echo lang('activity_date_delete'); ?></a>
	<?php echo lang('activity_delete_date_note'); ?>
	<select id="activity_date_select">
		<option value="all"><?php echo lang('activity_all_dates'); ?></option>
	<?php foreach ($activities as $activity) : ?>
		<option value="<?php echo $activity->activity_id; ?>"><?php echo $activity->created_on; ?></option>
	<?php endforeach; ?>
	</select>
</div>
<?php endif; ?>
