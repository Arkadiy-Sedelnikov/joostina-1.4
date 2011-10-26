<div align="center" class="boss_innermenu">
</div>
<h1 class="contentheading">
<?php $this->displayCatTitle(); ?>
</h1>
<div class="boss_pathway">
<?php $this->displayPathway(); ?>
</div>
<div class="boss_subcats">
<?php $this->displaySubcats(); ?>
</div>
<div class="boss_description">
<?php $this->displayCatDescription(); ?>
</div>
<div align="right"><h2><?php $this->displayWriteLink(); ?></h2></div>
<br />
<div align="left">
	<?php $this->displayPagesCounter(); ?>
</div>
<div align="center">
    <?php $this->displayOrderOption(); ?>
</div>
<br/>
<table class="table_of_items" width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr class="tr-second">
       
        <th><?php echo BOSS_FORM_MESSAGE_TITLE; ?></th>   
        
        <?php  foreach($this->fieldsgroup as $fieldsgroup){ ?>
        <th><?php echo $fieldsgroup[0]->title; ?></th>
        <?php } ?>
        
   </tr>        
    <?php $this->displayContents(); ?>
</table>
<p align="center">
<?php echo $this->displayPagesLinks(); ?>
</p>