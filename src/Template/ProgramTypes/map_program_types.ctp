<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
           
<div class="programTypes form">
<?php echo $this->Form->create('ProgramType');?>
<div style="padding;20px;"></div>
	<fieldset>
		<legend class="smallheading"><?php echo __('Map Program Type'); ?></legend>
	<?php
		echo $this->Form->input('program_type_id',array('id'=>'program_type_id','label'=>'Program Types','error'=>false,'empty'=>'--select program type--'));
		
		
		
	?>
	<div id="program_type_equivalency">
	    
	</div>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Map'),
'class'=>'tiny radius button bg-blue'));?>
</div>   
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->


<script type="text/javascript">
  $(document).ready(function() { 
    $('#program_type_id').change(function () { 
       
         $('#program_type_equivalency').load('/program_types/get_program_types/'+$(this).val());

    });
  });
</script>
