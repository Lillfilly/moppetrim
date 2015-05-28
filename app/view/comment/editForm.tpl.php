<div class='comment-form'>
    <form method=post action="<?=$this->url->create('comment/edit')?>">
	<input type="hidden" name="commentId" value="<?=$commentId?>">
	<input type="hidden" name="page" value="<?=$page?>">
        <fieldset <?php if(!$enabled) : ?>disabled<?php endif; ?>
        <legend>Edit a comment</legend>
        <p><label>Comment:<br/><textarea name='content'><?=$content?></textarea></label></p>
        <p><label>Name:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
        <p><label>Homepage:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
        <p><label>Email:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
        <p class=buttons>
            <input type='submit' name='doSave' value='Spara'/>
            <input type='reset' value='Reset'/>
            <input type='submit' name='doDelete' value='Delete'/>
        </p>
	<output><?=$output?></output>
        </fieldset>
    </form>
</div>
