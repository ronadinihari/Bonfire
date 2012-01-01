
<div class="view split-view">
	
	<!-- media List -->
	<div class="view">
	
	<?php if (isset($records) && is_array($records) && count($records)) : ?>
		<div class="scrollable">
			<div class="list-view" id="role-list">
				<?php foreach ($records as $record) : ?>
					<?php $record = (array)$record;?>
					<div class="list-item" data-id="<?php echo $record['id']; ?>">
						<p>
							<b><?php echo (empty($record['media_name']) ? $record['id'] : $record['media_name']); ?></b><br/>
							<span class="small"><?php echo (empty($record['media_description']) ? lang('media_edit_text') : $record['media_description']);  ?></span>
						</p>
					</div>
				<?php endforeach; ?>
			</div>	<!-- /list-view -->
		</div>
	
	<?php else: ?>
	
	<div class="notification attention">
		<p><?php echo lang('media_no_records'); ?> <?php echo anchor(SITE_AREA .'/reports/media/create', lang('media_create_new'), array("class" => "ajaxify")) ?></p>
	</div>
	
	<?php endif; ?>
	</div>
	<!-- media Editor -->
	<div id="content" class="view">
		<div class="scrollable" id="ajax-content">
				
			<div class="box create rounded">
				<a class="button good ajaxify" href="<?php echo site_url(SITE_AREA .'/reports/media/create')?>"><?php echo lang('media_create_new_button');?></a>

				<h3><?php echo lang('media_create_new');?></h3>

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
		<th>Media</th><th><?php echo lang('media_actions'); ?></th>
		</thead>
		<tbody>
<?php
foreach ($records as $record) : ?>
<?php $record = (array)$record;?>
			<tr>
<?php
	foreach($record as $field => $value)
	{
		if($field != "id") {
?>
				<td><?php echo ($field == 'deleted') ? (($value > 0) ? lang('media_true') : lang('media_false')) : $value; ?></td>

<?php
		}
	}
?>
				<td><?php echo anchor(SITE_AREA .'/reports/media/edit/'. $record['id'], lang('media_edit'), 'class="ajaxify"'); ?></td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
				<?php endif; ?>
				
		</div>	<!-- /ajax-content -->
	</div>	<!-- /content -->
</div>
