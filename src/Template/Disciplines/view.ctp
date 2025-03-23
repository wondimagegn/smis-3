<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="disciplines view">

	<div class="smallheading"><?php echo __('View  Discipline Detail'); ?></div>
	<table>
	<tr>
	    <td colspan=2>
	     <?php  echo $this->element('student_basic'); ?>
	    </td>
	</tr>
	    <tr>
	     <td colspan="2">
	        <table>
	           <tr>
	               <td class="fs16" colspan="2">
	                 <?php echo __('Discipline case details.'); 
	                 ?>
	               </td>
	           </tr>
	            <?php
		         
		            echo '<tr>';
		                echo '<td>Discipline Date:</td>';
		                echo '<td>'.$this->Format->short_date($discipline['Discipline']['discipline_taken_date']).'</td>';
		
		               
		            echo '</tr>'; 
		            echo '<tr>';
		                echo '<td> Title:</td>';
		                echo '<td>'.$discipline['Discipline']['title'].'</td>';
		
		               
		            echo '</tr>'; 
		            echo '<tr>';
		                echo '<td> Description:</td>';
		                echo '<td>'.$discipline['Discipline']['description'].'</td>';
		
		               
		            echo '</tr>'; 
		          
		            
		            //
		         
	        ?>
	        </table>
	    
	    </td>
	 
	    </tr>
	
	</table>
	
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
