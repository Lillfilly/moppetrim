<div class="comment">
    <p>
	<?=$comment->comment?>
    </p>
    <hr />
    <p>
	Kommentar skriven av: <a href="<?=$this->url->create('users/id/' . $comment->userId)?>"><?= $comment->name ?></a>
	<br/>
	<strong>Rank: </strong><?=$comment->rank?>
    </p>
    <p>
	<?php if($comment->allowVotes):?>
	    <a href="<?=$this->url->create('questions') . '?vote=true&type=c&id=' . $comment->id . '&question=' . $questionId?>">Upprösta</a>
	    <a href="<?=$this->url->create('questions') . '?vote=false&type=c&id=' . $comment->id . '&question=' . $questionId?>">Nedrösta</a>
	<?php endif;?>
    </p>
</div>