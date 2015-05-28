<div class="clearfix"></div>
<h3>Frågor som den här användaren har ställt</h3>
<ul>
<?php foreach($questions as $q) : ?>
    <li><a href="<?=$this->url->create('questions/id/' . $q->id)?>"><?=$q->header?></a></li>
<?php endforeach ?>
</ul>

<h3>Frågor som den här användaren har besvarat</h3>
<ul>
<?php foreach($answers as $a) : ?>
    <li><a href="<?=$this->url->create('questions/id/' . $a->id)?>"><?=$a->header?></a></li>
<?php endforeach ?>
</ul>
<strong>Antal kommentarer skrivna : </strong><?=$comments?>