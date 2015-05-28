<?php
    extract($user->getProperties());
?>

<a href="<?= $this->url->create('users/id/' . $id)?>">
<div class="user">
    <figure>
	<img src="<?= get_gravatar($email)?>">
    </figure>
    <p>
    <strong><?=$name?><br/></strong>
    </p>
    <p>
	Rykte: <?=$reputation?><br/>
	Röster: <?=$votes?>
    </p>
</div>
</a>