
<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>
<?php // Change the css classes to suit your needs    
if( isset($media) ) {
	$media = (array)$media;
}
$id = isset($media['id']) ? "/".$media['id'] : '';
?>
<?php echo form_open($this->uri->uri_string(), 'class="constrained ajax-form"'); ?>
<div>
        <?php echo form_label('Title', 'media_judul'); ?> <span class="required">*</span>
        <input id="media_judul" type="text" name="media_judul" maxlength="50" value="<?php echo set_value('media_judul', isset($media['media_judul']) ? $media['media_judul'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('Description', 'media_deskripsi'); ?>
	<?php echo form_textarea( array( 'name' => 'media_deskripsi', 'id' => 'media_deskripsi', 'rows' => '5', 'cols' => '80', 'value' => set_value('media_deskripsi', isset($media['media_deskripsi']) ? $media['media_deskripsi'] : '') ) )?>
</div>

<div>
		<?php echo form_label('File', 'media_file'); ?> <span class="required">*</span>
		<input type="file" name="userfile" />
</div>



	<div class="text-right">
		<br/>
		<input type="submit" name="submitcreate" value="Create media" /> or <?php echo anchor(SITE_AREA .'/content/media', lang('media_cancel')); ?>
	</div>
	<?php echo form_close(); ?>
