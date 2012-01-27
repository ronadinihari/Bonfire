
								
<style type="text/css">
.gallery-td { 
	padding:10px;
}
</style>

	<!-- media Editor -->
		<div class="scrollable">

			<div class="box create rounded">
				<a
					href="<?php echo site_url(SITE_AREA .'/content/media/create')?>"><?php echo lang('media_create_new_button');?>
				</a>

				<h3>
					
					
					
					
					
				<?php echo lang('media_create_new');?></h3>
			</div>
			<br />
			
			
			
			
			
			
			
			
			
			
			
			
				<?php if (isset($records) && is_array($records) && count($records)) : ?>
				
					<h2>Media</h2>
	<table>
		<tbody>
				<?php
							$cells = array();
							$cellid = 0;
							          
							foreach ($records as $record) : $record = (array) $record;
								
								$image_properties = array(
								          'src' => site_url(SITE_AREA . '/content/media/thumbnail/' . $record['id']),
								          'alt' => 'Fail loading ' . SITE_AREA . '/content/media/thumbnail/' . $record['id'],
								          'title' => $record['media_judul'],
								          'rel' => 'lightbox',
								);
								$img = img($image_properties);
								
								$cells[$cellid++] = 
									'<div align="center">'.
									anchor(SITE_AREA . '/content/media/image/' . $record['id'], $img).
									br().
									'<div style="font-weight: bold">'.
									$record['media_judul'].
									'</div>'.
									br().
									'</div>'.
									$record['media_tanggalupload'].
									br().
									$record['media_deskripsi'].
									br().
									anchor(SITE_AREA .'/content/media/edit/'. $record['id'], lang('media_edit'));
								
							endforeach;
							
							$cellcount = $cellid;
							$col = 0;
							$maxcol = 4;
								
							for ($i = 0; $i < $cellcount; $i++)
							{
							
								if ($col == 0) {
									?>
									<tr>
									<?php
								}
								?>
								<td width="100" class="gallery-td">
								<?php echo $cells[$i]; ?>
								</td>
								<?php
							
								if ($i == $cellcount-1 && $col < $maxcol - 1) {
									for ($j = 0; $j < $maxcol - 1 - $col; $j++) {
										?>
										<td width="100" class="gallery-td"></td>
										<?php
									}
								} elseif ($col == $maxcol - 1) {
									?>
									</tr>
									<?php
									
									$col = 0;
								} else {
									$col++;
								}
							}
						endif;
				?>
		</tbody>
	</table>
		</div>
		<!-- /ajax-content -->
