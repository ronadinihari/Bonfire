<div class="box create rounded">

	<a class="button good" href="<?php echo site_url(SITE_AREA .'/developer/media/create'); ?>">
		<?php echo lang('media_create_new_button'); ?>
	</a>

	<h3><?php echo lang('media_create_new'); ?></h3>

	<p><?php echo lang('media_edit_text'); ?></p>

</div>

<br />

<?php if (isset($records) && is_array($records) && count($records)) : ?>
				
	<h2>media</h2>
	<table>
		<thead>
		
			
		<th>bf_users_id</th>
		<th>Date</th>
		<th>Title</th>
		<th>Description</th>
		<th>MIME</th>
		<th>Media</th>
		<th>Thumbnail</th>
		
			<th><?php echo lang('media_actions'); ?></th>
		</thead>
		<tbody>
		
		<?php foreach ($records as $record) : ?>
			<?php $record = (array)$record;?>
			<tr>
			<?php foreach($record as $field => $value) : ?>
				
				<?php if ($field != 'id') : ?>
					<td><?php echo ($field == 'deleted') ? (($value > 0) ? lang('media_true') : lang('media_false')) : $value; ?></td>
				<?php endif; ?>
				
			<?php endforeach; ?>

				<td>
					<?php echo anchor(SITE_AREA .'/developer/media/edit/'. $record[$primary_key_field], lang('media_edit'), '') ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>