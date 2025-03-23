Dear <?php if(isset($firstname)){
            if(!empty($titleofperson)){
                echo ucfirst($titleofperson)." ".ucfirst($firstname);
            }else{
                echo ucfirst($firstname);
            }          
        }else {
            echo "";
        }?>, <br/>
        
      
<p>
   <em><?php echo $message;?></em>
</p>

<footer>
To find more information or get in touch, visit our website at 
<a href="http://www.merebprojects.com" target="_blank">
www.merebprojects.com
</a>
</footer>

