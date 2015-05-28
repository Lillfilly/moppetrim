<?php
    if(isset($user)){
	$link = '<a href="'.$this->url->create('users/logout').'">Logga ut</a>';
    }else{
	$link = '<a href="'.$this->url->create('users/login').'">Logga in/Skapa konto</a>';
    }
?>

<?=$link?>