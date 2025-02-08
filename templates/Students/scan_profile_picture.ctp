
<?php 

echo $this->Form->create('Student');

?>
<?php 
//echo $this->Form->create('Student', array('default' => false,'id'=>''));

?>

<div class="box">
     <div class="box-body pad-forty">
       <div class="row">
		   	 <div class="large-12 columns">
		   	 	   <h4><?php echo __('Synchronize Profile Pictures With Files Dropped under webroot/media/transfer/img'); ?></h4>		
		   	 </div>
			  <div class="large-6 columns">
                        
                         <?php 
                        echo $this->Form->submit('Start',array('name'=>'Synchronize','class'=>'tiny radius button bg-blue'));



                    ?>
   

</div>

			  </div> <!-- end of columns 6 -->
			 
	   </div> <!-- end of row -->
   </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php    
echo $this->Form->end();
?>
