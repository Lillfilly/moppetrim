<div class="question">
    <p>
	<?=$question->question?>
    </p>
    <hr />
    <p>
	Frågan ställd av: <a href="<?=$this->url->create('users/id/' . $question->userId)?>"><?= $question->name ?></a>
    </p>
    <ul>
	<li><strong>Svar:</strong> <?=$question->answers?></li>
	<li><strong>Rank:</strong> <?=$question->rank?></li>
    </ul>

    <?php if($allowVotes):?>
    <a href="<?=$this->url->create('questions') . '?vote=true&type=q&question&id=' . $question->id?>">Upprösta</a>
    <a href="<?=$this->url->create('questions') . '?vote=false&type=q&question&id=' . $question->id?>">Nedrösta</a>
    <?php endif;?>
    <p>
	<strong>Taggar:</strong>
	<?php foreach($question->tags as $tag) : ?>
	    <?php require (__DIR__ . '/tag.tpl.php'); ?>
	<?php endforeach; ?>
    </p>
</div>