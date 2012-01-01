
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
        <?php echo form_label('bf_users_id', 'media_bf_users_id'); ?> <span class="required">*</span>
        <input id="media_bf_users_id" type="text" name="media_bf_users_id" maxlength="20" value="<?php echo set_value('media_bf_users_id', isset($media['media_bf_users_id']) ? $media['media_bf_users_id'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('Date', 'media_tanggalupload'); ?> <span class="required">*</span>
			<script>head.ready(function(){$('#media_tanggalupload').datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm:ss'});});</script>
        <input id="media_tanggalupload" type="text" name="media_tanggalupload"  value="<?php echo set_value('media_tanggalupload', isset($media['media_tanggalupload']) ? $media['media_tanggalupload'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('Title', 'media_judul'); ?> <span class="required">*</span>
        <input id="media_judul" type="text" name="media_judul" maxlength="50" value="<?php echo set_value('media_judul', isset($media['media_judul']) ? $media['media_judul'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('Description', 'media_deskripsi'); ?>
        <input id="media_deskripsi" type="text" name="media_deskripsi" maxlength="500" value="<?php echo set_value('media_deskripsi', isset($media['media_deskripsi']) ? $media['media_deskripsi'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('MIME', 'media_mime'); ?> <span class="required">*</span>
        <input id="media_mime" type="text" name="media_mime" maxlength="20" value="<?php echo set_value('media_mime', isset($media['media_mime']) ? $media['media_mime'] : ''); ?>"  />
</div>

<div>
        <?php echo form_label('Media', 'media_media'); ?> <span class="required">*</span>
        <input id="media_media" type="text" name="media_media"  value="<?php echo set_value('media_media', isset($media['media_media']) ? $media['media_media'] : ''); ?>"  />
</div>



	<div class="text-right">
		<br/>
		<input type="submit" name="submit" value="Create media" /> or <?php echo anchor(SITE_AREA .'/settings/media', lang('media_cancel')); ?>
	</div>
	<?php echo form_close(); ?>
