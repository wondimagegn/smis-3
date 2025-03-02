<div style="margin:0" class="row summary-border-top">
    <?php
    if (isset($rank) && !empty($rank)) { ?>
        <div class="large-12 columns">
            <div class="summary-nest">
                <p class="text-black"> Your Stand! </p>
            </div>
        </div>
        <div class="large-12 columns">
            <?php 
            foreach ($rank as $acsem => $v) { ?>
                <div class="row">
                    <div class="large-12 columns">
                        <h6 class="text-black" style="text-align:center"><?= $acsem; ?> </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="large-12 columns">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td class="center">By</td>
                                <td class="center">Section</td>
                                <td class="center">Batch</td>
                                <td class="center">College</td>
                            </tr>
                            <?php 
                            if (isset($v['sgpa']) && !empty($v['sgpa'])) { ?>
                                <tr>
                                    <td class="center">SGPA</td>
                                    <td class="center"><?= $v['sgpa']['StudentRank']['section_rank']; ?></td>
                                    <td class="center"><?= $v['sgpa']['StudentRank']['batch_rank']; ?></td>
                                    <td class="center"><?= $v['sgpa']['StudentRank']['college_rank']; ?></td>
                                </tr>
                                <?php 
                            }
                            if (isset($v['cgpa']) && !empty($v['cgpa'])) { ?>
                                <tr>
                                    <td class="center">CGPA</td>
                                    <td class="center"><?= $v['cgpa']['StudentRank']['section_rank']; ?></td>
                                    <td class="center"><?= $v['cgpa']['StudentRank']['batch_rank']; ?></td>
                                    <td class="center"><?= $v['cgpa']['StudentRank']['college_rank']; ?></td>
                                </tr>
                                <?php
                            } ?>
                        </table>
                    </div>
                </div>
                <?php 
            } ?>
        </div>
        <?php 
    } ?>
</div>