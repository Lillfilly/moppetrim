<div class="answer <?php if($answer->accepted == 'TRUE'){echo 'accepted';}?>">
    <?php if($showAccept):?>
	<a href="?accept=<?=$answer->id?>">Acceptera svar</a>
    <?php endif;?>
    <p>
	<?=$answer->answer?>
    </p>
    <hr />
    <p>
	Svaret skrivet av: <a href="<?=$this->url->create('users/id/' . $answer->userId)?>"><?= $answer->name ?></a>
	<br/>
	<strong>Rank: </strong><?=$answer->rank?>
    </p>
    <p>
	<?php if($answer->allowVotes):?>
	    <a href="<?=$this->url->create('questions') . '?vote=true&type=a&id=' . $answer->id . '&question=' . $questionId?>">Upprösta</a>
	    <a href="<?=$this->url->create('questions') . '?vote=false&type=a&id=' . $answer->id. '&question=' . $questionId?>">Nedrösta</a>
	<?php endif;?>
    </p>
    <hr/>
    <h4>Kommentarer</h4>
    <?php
	if($this->session->get('user') != NULL){
	    echo '<p><a href="'.$this->url->create('questions/comment/' . $answer->id).'">Kommentera detta svaret</a></p>';
	}

	foreach($comments as $comment){
	    require(__DIR__ . '/comment.tpl.php');
	}
    ?>
</div>