<?php 
    if (!empty($cost_share_summery)) {
        $sum=0;
        //cost_sharing_sign_date
        echo '<table>';
        echo '<tr><th>Year</th><th>Education Fee</th><th>Accomodation Fee</th>
        <th>Cafeteria Fee</th><th>Medical Fee</th><th>Total</th></tr>';
        foreach ($cost_share_summery as $index=>$value) {
            $total = $value['CostShare']['education_fee']+$value['CostShare']['accomodation_fee']+
            $value['CostShare']['cafeteria_fee']+$value['CostShare']['medical_fee']; 
            
            echo '<tr>';//cafeteria_fee medical_fee
            echo '<td>'.$value['CostShare']['academic_year'].'</td><td>'.$value['CostShare']['education_fee'].
            '</td><td>'.$value['CostShare']['accomodation_fee'].'</td><td>'.$value['CostShare']['cafeteria_fee'].
            '</td><td>'.$value['CostShare']['medical_fee'].'</td><td>'.$total.'</td>';
            echo '</tr>';
            $sum =$sum+$total;
        }
        echo '<tr><td>Grand Total</td><td colspan="5" style="text-align:middle">'.$sum.'</td></tr>';
        echo '</table>';
    } else {
        echo '<div class="info-box info-message"><span></span>There is no 
        cost sharing payment for '.$student_full_name.'.Either it is cost is not maintain or not applicable</div>';
    }
?>
