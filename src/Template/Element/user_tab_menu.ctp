<?php
$tabs = array (
    'basic_fields' => 'Basic information',
    'add_address' => 'Address & Contact',
    'education_background' => 'Education Background'
);

// Build a javascript string, items separated with commas.

$all_tabs = "";
$sep = "";

foreach ($tabs as $key => $tag) {
    $all_tabs.= $sep.$key;
    $sep = ",";
}

echo "<!--  TAB MENU --> \n";

if (isset($current_tab) ) {
    $tabId = "toggleOf".ucwords($current_tab);
    $textId = "id=\"$tabId\"";
} else {
    $tabId = "";
    $textId = null;
}
echo "<div class=\"TabToggles\" $textId>";

foreach ($tabs as $key => $tag) {
    $class = null;
    if (isset($current_tab) && $key == $current_tab) {
        $class = " class=\"selected\"";
    }
    $newTabId = "toggleOf".ucwords($key);
    echo "\n<a href=\"javascript:switchDiv('$key', '$newTabId', '$all_tabs');\"". $class.">".  __($tag)."</a>";
}

echo "\n</div>\n";
echo "<!--  END OF TAB MENU --> \n";
