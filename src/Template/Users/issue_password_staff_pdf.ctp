<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

// Clear output buffer
ob_start();
// Load TCPDF
require_once ROOT . DS . 'vendor' . DS . 'tecnickcom' . DS . 'tcpdf' . DS . 'tcpdf.php';

// Create new PDF document
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document protection
$pdf->SetProtection(['modify', 'copy', 'extract', 'assemble'],
    Configure::read('USER_PASSWORD', ''),
    Configure::read('OWNER_PASSWORD', ''), 0, null);

// Disable header and footer
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// Set margins (left, top, right)
$pdf->SetMargins(10, 10, 10);

// Set page orientation to portrait
$pdf->setPageOrientation('P', true, 0);

// Add a page
$pdf->AddPage('P');

// Set line style
$pdf->SetLineStyle(['dash' => 0, 'width' => 1]);

// Move down 50mm
$pdf->Ln(50);

// Configuration variables
$countryAmharic = Configure::read('ApplicationDeployedCountryAmharic', '');
$cityAmharic = Configure::read('ApplicationDeployedCityAmharic', '');
$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish', '');
$cityEnglish = Configure::read('ApplicationDeployedCityEnglish', '');
$pobox = Configure::read('POBOX', '');

// Set document metadata
$pdf->SetCreator(Configure::read('PDF_CREATOR', 'SMiS'));
$pdf->SetAuthor('SMiS, ' . Configure::read('CompanyName', ''));
$title = 'Password Issue Letter Report for ' . h($staffDetails->staff->title->title ?? '') . ' ' .
    h($staffDetails->staff->full_name ?? '') . ' (' .
    h($staffDetails->username ?? '') . ') ' .
    (isset($staffDetails->staff->department->name)
    && !empty($staffDetails->staff->department->name)
        ? h($staffDetails->staff->department->name)
        : (isset($staffDetails->staff->college->name) &&
        !empty($staffDetails->staff->college->name)
            ? h($staffDetails->staff->college->name) : ''));
$pdf->SetTitle($title);
$pdf->SetSubject($title);
$pdf->SetKeywords('Password, Issue, Letter, ' .
    h($staffDetails->staff->full_name  ?? '') . ', ' .
    (isset($staffDetails->staff->department->name) ?
        h($staffDetails->staff->department->name) : '') . ', ' .
    (isset($staffDetails->staff->college->name) ?
        h($staffDetails->staff->college->name) : '') . ', ' .
    h($staffDetails->staff->title->title ?? '') . ', ' .
    h($staffDetails->email ?? '') . ', SMiS');




// Add logo

$logoPath = WWW_ROOT . 'img' . DS .UNIVERSITY_LOGO_HEADER_FOR_TCPDF ;
if (file_exists($logoPath)) {
    try {
        $pdf->Image($logoPath, 5, 5, 25, 25, '', '', 'N',
            true, 300, 'C');
    } catch (\Exception $e) {
        \Cake\Log\Log::error("Failed to add logo: " . $e->getMessage());
    }
}

/*
// Add profile photo if available
if (!empty($staffDetails->staff->attachments)
    && !empty(($staffDetails->staff->attachments[0]['file_dir'])
        && $staffDetails->staff->attachments[0]['group'] === 'img')) {
    $attachmentPath = WWW_ROOT . ($staffDetails->staff->attachments[0]['file_dir'].
            $staffDetails->staff->attachments[0]['file']);
    if (file_exists($attachmentPath) && in_array($staffDetails->staff->attachments[0]['file_type'],
            ['image/jpeg', 'image/png'])) {
        $pdf->Image($attachmentPath, 160, 5, 25, 25, '', '', 'N', true, 300, 'R');
    }
}

// Add profile photo
if (!empty($staffDetails->staff->attachments) && !empty($staffDetails->staff->attachments[0]['file_dir'])
    && in_array($staffDetails->staff->attachments[0]['attachment_group'], ['profile', 'logo', 'background'])) {
    $attachmentPath = WWW_ROOT . $staffDetails->staff->attachments[0]['file_dir'] . $staffDetails->staff->attachments[0]['file'];
    if (in_array($staffDetails->staff->attachments[0]['file_dir'], ['doc', 'img'])) {
        $attachmentPath = WWW_ROOT . 'Uploads/attachments/Staff/' . $staffDetails->staff->id . '/original/' . $staffDetails->staff->attachments[0]['attachment_group'] . '/' . $staffDetails->staff->attachments[0]['file'];
    }
    if (file_exists($attachmentPath) && in_array($staffDetails->staff->attachments[0]['file_type'], ['image/jpeg', 'image/png'])) {
        try {
            $pdf->Image($attachmentPath, 160, 5, 25, 25, '', '', 'N', true, 300, 'R');
        } catch (\Exception $e) {
            \Cake\Log\Log::error("Failed to add profile photo: " . $e->getMessage());
        }
    } else {
        \Cake\Log\Log::error("Profile photo not found or invalid: $attachmentPath");
    }
}
*/

if (!empty($staffDetails->staffs[0]['attachments'][0])) {
    $attachment = $staffDetails->staffs[0]['attachments'][0];
    if($attachment->isLegacy){
        if ($attachment->isLegacyImageDirectory) {
            $pdf->Image(
                $attachment->getLegacyUrlForCake2(),
                160,
                5,
                25,
                25,
                '',
                '',
                'N',
                true,
                300,
                'R'
            );
        } else {
            $pdf->writeHTML(
                '<a href="' . $attachment->getLegacyUrlForCake2() .
                '">Download Attachment</a>',
                true,
                false,
                true,
                false,
                ''
            );
        }

    } else {
        if ($attachment->is_image) {
            $pdf->Image(
                $attachment->getUrl(),
                160,
                5,
                25,
                25,
                '',
                '',
                'N',
                true,
                300,
                'R'
            );
        } else {
            $pdf->writeHTML(
                '<a href="' . $attachment->getUrl() .
                '">Download Attachment (' . $attachment->file_type . ', ' . $attachment->file_size . ' bytes)</a>',
                true,
                false,
                true,
                false,
                ''
            );
        }
    }
}
// Add font
$pdf->AddFont('freeserif', '', 'freeserif.php');


// Header content
$pdf->SetFont('freeserif', 'B', 14);
$pdf->MultiCell(92, 7, h($university->name ?? ''), 0, 'C', false,
    0, 1, 10);
$pdf->SetFont('freeserif', 'B', 13);
$pdf->MultiCell(92, 7, h($staffDetails->staff->college->name ?? ''), 0,
    'C', false, 0, 1, 17);
if (!empty($staffDetails->staff->department->id)) {
    $pdf->MultiCell(92, 7, h($staffDetails->staff->department->type ?? '') .
        ' of ' . h($staffDetails->staff->department->name ?? ''), 0, 'C',
        false, 0, 1, 22);
} else {
    $pdf->MultiCell(92, 7, '', 0, 'C', false, 0, 1, 22);
}

$pdf->SetFont('freeserif', '', 18);
$pdf->MultiCell(85, 7, strtoupper(h($university->amharic_name ?? '')), 0,
    'C', false, 0, 120, 10);
$pdf->SetFont('freeserif', '', 16);
$pdf->MultiCell(85, 7, h($staffDetails->staff->college->amharic_name ?? ''),
    0, 'C', false, 0, 120, 17);
if (!empty($staffDetails->staff->department->id)) {
    $pdf->MultiCell(85, 7, 'የ' . h($staffDetails->staff->department->amharic_name ?? '') .
        ' ' . h($staffDetails->staff->department->type_amharic ?? ''), 0, 'C', false, 0, 120, 22);
} else {
    $pdf->MultiCell(85, 7, '', 0, 'C', false, 0, 120, 22);
}

// Address
$pdf->SetFont('freeserif', '', 12);
$pdf->MultiCell(30, 7, 'P.O.Box: ' . h($pobox), 0, 'C', false, 0, 34, 35);
if (!empty($staffDetails->staff->department->id) &&
    !empty($staffDetails->staff->department->phone)) {
    $pdf->MultiCell(100, 7, 'Tel: ' . h($staffDetails->staff->department->phone),
        0, 'L', false, 0, 146, 35);
} elseif (empty($staffDetails->staff->department->id) &&
    !empty($staffDetails->staff->college->phone)) {
    $pdf->MultiCell(100, 7, 'Tel: ' . h($staffDetails->staff->college->phone),
        0, 'L', false, 0, 146, 35);
}

// Line separator
$pdf->Line(2, 43, 207, 43);
$pdf->SetFont('freeserif', '', 14);
$pdf->MultiCell(157, 7, h($cityAmharic . '፡ ' . $countryAmharic), 0, 'C', false, 0, 27, 31);
$pdf->SetFont('freeserif', 'B', 12);
$pdf->MultiCell(157, 7, h($cityEnglish . ', ' . $countryEnglish), 0, 'C', false, 0, 27, 36);

// Title
$pdf->SetFont('freeserif', 'B', 14);
$pdf->MultiCell(157, 7, 'Password Issue Letter', 0, 'C', false, 0, 27,
    46);
$pdf->Ln(15);

// Letter content
$welcomeFirstTime = !empty($staffDetails['User']['last_login'])
    ? '<td colspan="2">This is a password reset letter, please login to SMiS <b>(' . h(Configure::read('PORTAL_URL_HTTPS', '')) . ')</b>, using the account details below: </td>'
    : '<td colspan="2">Welcome to ' . h($university->name ?? '') . '!
It is exciting world of knowledge. All academic related transactions are handled by SMiS.
In order to access the portal <b>(' . h(Configure::read('PORTAL_URL_HTTPS', '')) . ')</b>,
use the account details below: </td>';

$staffCopyHtml = '
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
    <tr><td colspan="2">Dear ' . (isset($staffDetails->staff->title->title) ?
        h($staffDetails->staff->title->title) . ' ' : '') . h($staffDetails->staff->full_name ?? '') . ',</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>' . $welcomeFirstTime . '</tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td style="width:15%">&nbsp;</td>
        <td style="width:85%"><span style="padding-left: 5px;">Username: &nbsp;&nbsp;' . h($staffDetails->username ?? '') . '</span></td>
    </tr>
    <tr>
        <td style="width:15%">&nbsp;</td>
        <td style="width:85%"><span style="padding-left: 5px;">Temporary Password: &nbsp;&nbsp;' .
    h($password) . '</span></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><span style="padding-left: 5px;">Recovery Email: &nbsp;&nbsp;' . h($staffDetails->email ?? '') . '</span></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td colspan="2" style="width:100%">
            <u style="font-weight: bold;">Important Notes:</u>
            <p>
                <ol>
                    <li>For first time login, you will be forced to change the above temporary password to your own chosen password.</li>
                    <li>Make sure you provide a strong password when you are presented with the password change page and always remember your password.</li>
                    <li>You are advised to keep your password secure and secret, particularly if using a shared computer.</li>
                    <li>You can use the above registered email <b>' .
    h($staffDetails->email ?? '') . '</b> on Forgot password link: <b>' .
    h(Configure::read('BASE_URL_HTTPS', '')) . 'users/forget</b>
                    to recover your username and password if forgotten.</li>
                </ol>
            </p>
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>
<br><br>';

$pdf->SetFont('freeserif', '', 12);
$pdf->writeHTML($staffCopyHtml);

// Reset pointer to the last page
$pdf->lastPage();


// Clear output buffer
ob_clean();


// Output the PDF
try {
    $filename = 'Password_Issue_Letter_' . str_replace(' ', '_', $staffDetails->staff->full_name ?? '') . '_(' . ($staffDetails->username ?? '') . ')_' . date('Y-m-d') . '.pdf';
    $pdf->Output($filename, 'I');
} catch (\Exception $e) {
    \Cake\Log\Log::error("Failed to output PDF: " . $e->getMessage());
    throw new \Exception("Failed to output PDF.");
}

// Ensure no output after PDF
exit;
