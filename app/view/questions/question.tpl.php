<div class="question">
    <a href="<?=$this->url->create('questions/id/' . $question->id)?>"><h3><?=$question->header?></h3></a>
    <hr />
    <p>
	Frågan ställd av: <a href="<?=$this->url->create('users/id/' . $question->userId)?>"><?= $question->name ?></a>
    </p>
    <ul>
	<li><strong>Svar:</strong> <?=$question->answers?></li>
	<li><strong>Rank:</strong> <?=$question->rank?></li>
    </ul>
    <?php if($question->allowVotes):?>
    <a href="<?=$this->url->create('questions') . '?vote=true&type=q&id=' . $question->id?>">Upprösta</a>
    <a href="<?=$this->url->create('questions') . '?vote=false&type=q&id=' . $question->id?>">Nedrösta</a>
    <?php endif;?>
    <p>
	<strong>Tags:</strong>
	<?php foreach($question->tags as $tag) : ?>
	    <?php require (__DIR__ . '/tag.tpl.php'); ?>
	<?php endforeach; ?>
    </p>
</div>