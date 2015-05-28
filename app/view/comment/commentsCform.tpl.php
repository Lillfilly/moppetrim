<?php if (is_array($comments)) : ?>
<section class='comments'>
<?php foreach ($comments as $id => $comment) : ?>

<div class='comment'>
    <header>
	<form method="post" action="<?=$this->url->create('comment/edit')?>" id="form-<?=$id?>">
	    <input type="hidden" name="page" value="<?=$page?>">
	    <input type="hidden" name="commentId" value="<?=$id?>">
	    <strong>
		Kommentar <a href="#" onClick="document.getElementById('form-<?=$id?>').submit();return false;">#<?=$id?></a>
	    </strong>
	</form>
	<span class='author'>
	    Skriven av: 
	    <?php if(filter_var($comment['mail'], FILTER_VALIDATE_EMAIL)) : ?>
		<a href="mailto:<?=$comment['mail']?>?Subject=Din%20Kommentar" target="_top"><?=$comment['name']?></a>
	    <?php else: ?>
		<?=$comment['name']?>
	    <?php endif; ?>
	</span>
    </header>
    <hr>
    <main class="content">
	<?=$comment['content']?>
    </main>
    <footer class='byline'>
	<?php if(filter_var($comment['web'], FILTER_VALIDATE_URL)) : ?>
	    Gillade du kommentaren ifrån <?=$comment['name']?>? <a href="<?=$comment['web']?>">Besök hens webbsida</a>
	<?php else: ?>
	    <?=$comment['name']?> har inte angett sin webbplats
	<?php endif; ?>
    </footer>
</div>

<?php endforeach; ?>
</section>
<?php endif; ?>
