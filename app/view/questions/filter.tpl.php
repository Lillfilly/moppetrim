<h2><?=$tag?></h2>
<ul>
<?php 
    foreach($questions as $question):
?>
<div class="question">
    <a href="<?=$this->url->create('questions/id/' . $question->id)?>"><h3><?=$question->header?></h3></a>
    <hr />
    <p>
	Frågan ställd av: <a href="<?=$this->url->create('users/id/' . $question->userId)?>"><?= $question->userId ?></a>
    </p>
    <p>
	<strong>Tags:</strong>
	<?php foreach($question->tags as $tag) : ?>
	    <?php require (__DIR__ . '/tag.tpl.php'); ?>
	<?php endforeach; ?>
    </p>
</div>
<?php endforeach; ?>
</ul>