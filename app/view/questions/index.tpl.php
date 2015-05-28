<h2><?=$pageHeader?></h2>
<?php if(isset($createLink)){echo $createLink;}?>
<ul>
<?php 
    foreach($questions as $question){
	require(__DIR__ . '/question.tpl.php');
    }
?>
</ul>