
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
        <?php echo form_label('', ''); ?>
        <?php
			$image_properties = array(
			          'src' => site_url(SITE_AREA . '/content/media/thumbnail/' . $media['id']),
			          'alt' => 'Fail loading ' . SITE_AREA . '/content/media/thumbnail/' . $media['id'],
			          'title' => $media['media_judul'],
			          'rel' => 'lightbox',
			);
        	$img = img($image_properties);
        	$anchor = anchor(SITE_AREA . '/content/media/image/' . $media['id'], $img);
        	echo $anchor;
        ?>
</div>

<div>
        <?php echo form_label('Thumbnail', 'media_thumbnail'); ?> <span class="required">*</span>
		<input type="file" name="userfile" />
</div>



	<div class="text-right">
		<br/>
		<input type="submit" name="submitedit" value="Edit media" /> or <?php echo anchor(SITE_AREA .'/content/media', lang('media_cancel')); ?>
	</div>
	<?php echo form_close(); ?>

	<div class="box delete rounded">
		<a class="button" id="delete-me" href="<?php echo site_url(SITE_AREA .'/content/media/delete/'. $id); ?>" onclick="return confirm('<?php echo lang('media_delete_confirm'); ?>')"><?php echo lang('media_delete_record'); ?></a>
		
		<h3><?php echo lang('media_delete_record'); ?></h3>
		
		<p><?php echo lang('media_edit_text'); ?></p>
	</div>
