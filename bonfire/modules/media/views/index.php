

<style type="text/css">
.gallery-table {
	width: 100%;
}

.gallery-td {
	padding: 10px;
	width: 150px;
}
</style>

<?php
function printpageindex($pageindex, $pagecount) {
	if ($pageindex < 5)
	{
		$start = 0;
	}
	else
	{
		$start = $pageindex - 5;
	}
	if ($pagecount - $pageindex < 5)
	{
		$end = $pagecount;
	}
	else
	{
		$end = $pageindex + 5;
	}

	// 		echo br();
	echo '<div style="text-align: center;">';
	for ($i = $start; $i < $end; $i++) {
		if ($i == $pageindex) {
			echo $i + 1;
		} else {
			echo anchor('/media/index/' . $i, $i + 1);
		}
		echo ' ';
	}
	echo '</div>';
	// 	echo br();
}
?>

<div class="view split-view">

	<!-- media Editor -->
	<div id="content" class="view">
		<div class="scrollable" id="ajax-content">

			<a class="button good ajaxify" href="<?php echo site_url('/media/create')?>"><?php echo lang('media_create_new_button');?>
			</a>

			<h2>Media</h2>
			
			
			
			
				
				<?php 
				if ( isset($pageindex) && isset($pagecount) )
				{
					printpageindex($pageindex, $pagecount);
				}
								
				if (isset($records) && is_array($records) && count($records)) : ?>
	<table class="gallery-table">
		<tbody>
				<?php
							$cells = array();
							$cellid = 0;
							          
							foreach ($records as $record) : $record = (array) $record;
								
								$image_properties = array(
								          'src' => site_url('/media/thumbnail/' . $record['id']),
								          'alt' => 'Fail loading ' . '/media/thumbnail/' . $record['id'],
								          'title' => $record['media_judul'],
								          'rel' => 'lightbox',
								          'width' => '150',
								);
								$img = img($image_properties);
								
								$username = $usernames[$cellid];
								
								$cells[$cellid++] = 
									'<div align="center">'.
									anchor('/media/image/' . $record['id'], $img).
									br().
									'<div style="font-weight: bold">'.
									$record['media_judul'].
									'</div>'.
									br().
									'</div>'.
									$username.
									br().
									$record['media_tanggalupload'].
									br().
									$record['media_deskripsi'].
									br().
									anchor('/media/edit/'. $record['id'], lang('media_edit'), 'class="ajaxify"');
								
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
								<td class="gallery-td">
								<?php echo $cells[$i]; ?>
								</td>
								<?php
							
								if ($i == $cellcount-1 && $col < $maxcol - 1) {
									for ($j = 0; $j < $maxcol - 1 - $col; $j++) {
										?>
										<td class="gallery-td"></td>
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
				?>
		</tbody>
	</table>
				<?php
						endif;
							
				if ( isset($pageindex) && isset($pagecount) )
				{
					printpageindex($pageindex, $pagecount);
				}

				?>
		</div>
		<!-- /ajax-content -->
	</div>
	<!-- /content -->
</div>
