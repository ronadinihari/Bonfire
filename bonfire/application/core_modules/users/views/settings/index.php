<div class="well shallow-well">
	<span>Username starts with: </span>
</div>

<ul class="tabs">
	<li class="active"><a href="#">All Users</a></li>
	<li><a href="#">Banned</a></li>
	<li><a href="#">Deleted</a></li>
	<li><a href="#">Inactive</a></li>
	<li class="dropdown" data-dropdown="dropdown">
		<a href="#" class="drodown-toggle">By Role</a>
		<ul class="dropdown-menu">
			<li></li>
		</ul>
	</li>
</ul>

<?php echo $this->dataset->table_open(); ?>

<?php if (isset($results) && is_array($results) && count($results)) : ?>
	<?php foreach ($results as $user) : ?>
	<tr>
		<td><?php echo $user->id ?></td>
		<td>
			<a href="">
				<?php echo $user->username; ?>
			</a>
		</td>
		<td><?php echo $user->first_name .' '. $user->last_name ?></td>
		<td>
			<a href="mailto://<?php echo $user->email ?>"><?php echo $user->email ?></a>
		</td>
		<td>
			<?php 
				if ($user->last_login != '0000-00-00 00:00:00')
				{
					echo date('M j, y g:i A', strtotime($user->last_login));
				}
				else
				{
					echo '---';
				}
			?>
		</td>
	</tr>
	<?php endforeach; ?>
<?php else: ?>

<?php endif; ?>

<?php echo $this->dataset->table_close(); ?>
