$.subscribe('list-view/list-item/click', function(id) {
	$('#content').load('<?php echo site_url('/media/edit') ?>/'+ id);
});