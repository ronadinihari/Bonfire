<?php if (validation_errors()) : ?>
<div class="notification error">
	<p><?php echo validation_errors(); ?></p>
</div>
<?php endif; ?>

<?php echo form_open($this->uri->uri_string()); ?>

	<fieldset>
		<div class="clearfix">
			<label for="title"><?php echo lang('bf_site_name') ?></label>
			<div class="input">
				<input type="text" name="title" class="span6" value="<?php echo isset($settings['site.title']) ? $settings['site.title'] : set_value('site.title') ?>" />
			</div>
		</div>
		
		<div class="clearfix">
			<label for="system_email"><?php echo lang('bf_site_email') ?></label>
			<div class="input">
				<input type="text" name="system_email" class="span4" value="<?php echo isset($settings['site.system_email']) ? $settings['site.system_email'] : set_value('site.system_email') ?>" />
				<p class="help-inline"><?php echo lang('bf_site_email_help') ?></p>
			</div>
		</div>
		
		<div class="clearfix">
			<label for="status"><?php echo lang('bf_site_status') ?></label>
			<div class="input">
				<select name="status">
					<option value="1" <?php echo isset($settings) && $settings['site.status'] == 1 ? 'selected="selected"' : set_select('site.status', '1') ?>><?php echo lang('bf_online') ?></option>
					<option value="0" <?php echo isset($settings) && $settings['site.status'] == 0 ? 'selected="selected"' : set_select('site.status', '1') ?>><?php echo lang('bf_offline') ?></option>
				</select>
			</div>
		</div>
		
		<div class="clearfix">
			<label for="list_limit"><?php echo lang('bf_top_number') ?></label>
			<div class="input">
				<input type="text" name="list_limit" value="<?php echo isset($settings['site.list_limit']) ? $settings['site.list_limit'] : set_value('site.list_limit') ?>" class="span1" />
				<p class="help-inline"><?php echo lang('bf_top_number_help') ?></p>
			</div>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><?php echo lang('bf_security') ?></legend>
		
		<div class="clearfix">
			<div class="input">
				<div class="inputs-list">
					<label>
						<input type="checkbox" name="allow_register" id="allow_register" value="1" <?php echo config_item('auth.allow_register') == 1 ? 'checked="checked"' : set_checkbox('auth.allow_register', 1); ?> />
						<span><?php echo lang('bf_allow_register') ?></span>
					</label>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<label for="login_type"><?php echo lang('bf_login_type') ?></label>
			<div class="input">
				<select name="login_type">
					<option value="email" <?php echo config_item('auth.login_type') == 'email' ? 'selected="selected"' : ''; ?>><?php echo lang('bf_login_type_email') ?></option>
					<option value="username" <?php echo config_item('auth.login_type') == 'username' ? 'selected="selected"' : ''; ?>><?php echo lang('bf_login_type_username') ?></option>
					<option value="both" <?php echo config_item('auth.login_type') == 'both' ? 'selected="selected"' : ''; ?>><?php echo lang('bf_login_type_both') ?></option>
				</select>
			</div>
		</div>
		
		<div class="clearfix">
			<label><?php echo lang('bf_use_usernames') ?></label>
			<div class="input">
				<ul class="inputs-list">
					<li>
						<label style="display: inline" class="text-left">
							<input type="radio" name="use_usernames" id="use_usernames" value="1" <?php echo config_item('auth.use_usernames') == 1 ? 'checked="checked"' : set_radio('auth.use_usernames', 1); ?> />
							<span><?php echo lang('bf_username') ?></span>
						</label>
					</li>
					<li>
						<label style="display: inline" class="text-left">
							<input type="radio" name="use_usernames" id="use_usernames" value="0" <?php echo config_item('auth.use_usernames') == 0 ? 'checked="checked"' : set_radio('auth.use_usernames', 0); ?> />
							<span><?php echo lang('bf_email') ?></span>
						</label>
					</li>
					<li>
						<label style="display: inline" class="text-left">
							<input type="checkbox" name="use_own_names" id="use_own_names" value="1" <?php echo config_item('auth.use_own_names') == 1 ? 'checked="checked"' : set_checkbox('auth.use_own_names', 2); ?> />
							<span><?php echo lang('bf_use_own_name') ?></span>
						</label>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="input">
				<div class="inputs-list">
					<label for="allow_remember">
						<input type="checkbox" name="allow_remember" id="allow_remember" value="1" <?php echo config_item('auth.allow_remember') == 1 ? 'checked="checked"' : set_checkbox('auth.allow_remember', 1); ?> />
						<span><?php echo lang('bf_allow_remember') ?></span>
					</label>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<label for="remember_length"><?php echo lang('bf_remember_time') ?></label>
			<div class="input">
				<select name="remember_length" id="remember_length">
					<option value="604800"  <?php echo config_item('auth.remember_length') == '604800' ?  'selected="selected"' : '' ?>>1 <?php echo lang('bf_week') ?></option>
					<option value="1209600" <?php echo config_item('auth.remember_length') == '1209600' ? 'selected="selected"' : '' ?>>2 <?php echo lang('bf_weeks') ?></option>
					<option value="1814400" <?php echo config_item('auth.remember_length') == '1814400' ? 'selected="selected"' : '' ?>>3 <?php echo lang('bf_weeks') ?></option>
					<option value="2592000" <?php echo config_item('auth.remember_length') == '2592000' ? 'selected="selected"' : '' ?>>30 <?php echo lang('bf_days') ?></option>
				</select>
			</div>
		</div>
	
	</fieldset>
	
	<?php if ($this->auth->has_permission('Site.Developer.View')) : ?>
	<!-- Developer Settings -->
	<fieldset>
		<legend>Developer</legend>
		
		<div class="clearfix">
			<div class="input">
				<ul class="inputs-list">
					<li>
						<label for="show_profiler">
							<input type="checkbox" name="show_profiler" value="1" <?php echo config_item('site.show_profiler') == 1 ? 'checked="checked"' : set_checkbox('auth.use_extended_profile', 1); ?> />
							<span><?php echo lang('bf_show_profiler') ?></span>
						</label>
					</li>
					<li>
						<label for="do_check">
							<input type="checkbox" name="do_check" value="1" <?php echo config_item('updates.do_check') == 1 ? 'checked="checked"' : set_checkbox('updates.do_check', 1); ?> />
							<span><?php echo lang('bf_do_check') ?></span>
							<p class="help-inline"><?php echo lang('bf_do_check_edge') ?></p>
						</label>
					</li>
					<li>
						<label for="bleeding_edge">
							<input type="checkbox" name="bleeding_edge" value="1" <?php echo config_item('updates.bleeding_edge') == 1 ? 'checked="checked"' : set_checkbox('updates.bleeding_edge', 1); ?> />
							<span><?php echo lang('bf_update_show_edge') ?></span>
							<p class="help-inline"><?php echo lang('bf_update_info_edge') ?></p>
						</label>
					</li>
				</ul>
			</div>
		</div>
		
	</fieldset>
	<?php endif; ?>
	
	<div class="actions">
		<input type="submit" name="submit" class="btn primary" value="<?php echo lang('bf_action_save') .' '. lang('bf_context_settings') ?>" />
	</div>

<?php echo form_close(); ?>