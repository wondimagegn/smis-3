 <?php 
echo $this->Html->image('amulogo.png',['fullBase'=>true]);
?>
<p>

<?php echo $message;?>
</p>

<p>

Please do not reply to this email but use the provided information for tracking your application, as email replies are not monitored.

</p>
<footer>
To find more information or get in touch, visit our website
<?php
  echo Router::url("/", true);
?>
</footer>

