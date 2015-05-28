<h2><?=$title?></h2>
<hr />
<?php foreach($users as $user){
	require(__DIR__ . '/user.tpl.php');
}
?>
<div class="clearfix"></div>