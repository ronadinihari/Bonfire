<div class="box create rounded">

	<h3>media</h3>

</div>

<br />

<?php if (isset($records) && is_array($records) && count($records)) : ?>
				
	<table>
		<thead>
		
			
		<th>bf_users_id</th>
		<th>Date</th>
		<th>Title</th>
		<th>Description</th>
		<th>MIME</th>
		<th>Media</th>
		<th>Thumbnail</th>
		
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

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>