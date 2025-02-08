<?php ?>

<style>
.bordering {
    border-left: 1px #cccccc solid;
    border-right: 1px #cccccc solid;
}

.bordering2 {
    border-left: 1px #000000 solid;
    border-right: 1px #000000 solid;
    border-top: 1px #000000 solid;
    border-bottom: 1px #000000 solid;
}

.courses_table tr td,
.courses_table tr th {
    padding: 1px
}

tr:hover {
    background-color: #ccc;
    cursor: pointer;

}
</style>
<?php
if (isset($admittedMoreThanOneProgram) && !empty($admittedMoreThanOneProgram)) {
?>
<h5><?php echo $headerLabel; ?></h5>
<?php
  foreach ($admittedMoreThanOneProgram as $dkey => $dvalue) {
  ?>
<h5><?php echo ''; ?></h5>
<table style="width:100%">

    <tr>
        <td class="bordering2">
            S.N<u>o</u> </td>
        <td class="bordering2"> ID </td>
        <td class="bordering2"> Fullname
        </td>
        <td class="bordering2"> Sex
        </td>
        <td class="bordering2">
            Department
        </td>

        <td class="bordering2"> Program
        </td>
        <td class="bordering2">
            ProgramType </td>
    </tr>
    <?php
      $count = 1;
      foreach ($dvalue as $dk) {

        debug($dk);

      ?>
    <tr class='jsView'
        data-animation="fade"
        data-reveal-id="myModal"
        data-reveal-ajax="/students/get_modal_box/<?php echo $dk['Student']['id']; ?>">
        <td class="bordering">
            <?php echo $count; ?> </td>
        <td class="bordering"> <?php

                                  echo $dk['Student']['studentnumber'];
                                  ?>
        </td>
        <td class="bordering">

            <?php
            echo $dk['Student']['full_name'];

            ?>
        </td>
        <td class="bordering">

            <?php
            echo $dk['Student']['gender'];
            ?>
        </td>

        <td class="bordering">

            <?php
            echo $dk['Department']['name'];
            ?>
        </td>

        <td class="bordering">

            <?php
            echo $dk['Program']['name'];
            ?>
        </td>

        <td class="bordering">

            <?php
            echo $dk['ProgramType']['name'];
            ?>
        </td>

    </tr>
    <?php

        $count++;
      } ?>

</table>
<?php
  }
  ?>
<?php
}
?>