<?php if($writing) : ?>
<div class='comment-form'>
    <form method=post>
        <!--<input type=hidden name="redirect" value="<?=$this->url->create($redirect)?>">-->
	<input type='hidden' name='page' value="<?=$page?>">
        <fieldset>
        <legend>Lämna en kommentar</legend>
        <p><label>Comment:<br/><textarea name='content'><?=$content?></textarea></label></p>
        <p><label>Name:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
        <p><label>Homepage:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
        <p><label>Email:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
        <p class=buttons>
            <input type='submit' name='doCreate' value='Lägg till kommentar' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
            <input type='reset' value='Reset'/>
            <input type='submit' name='doRemoveAll' value='Ta bort alla' onClick="this.form.action = '<?=$this->url->create('comment/remove-all')?>'"/>
        </p>
        <output><?=$output?></output>
        </fieldset>
    </form>
</div>
<?php else :?>
    <form method="post" id="form-write">
	<!--<input type='hidden' name='redirect' value="<?=$this->url->create('comment')?>">-->
	<input type='hidden' name='page' value="<?=$page?>">
	<input type='submit' name='doWrite' value='Skriv kommentar'>
    </form>
<?php endif;