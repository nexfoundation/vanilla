<?php
if (!defined('APPLICATION')) exit();
$Methods = $this->data('Methods', []);
$CssClass = ' CenterEntryMethod';
?>
<h1><?php echo t("Apply for Membership") ?></h1>
<?php
echo '<div class="Entry'.$CssClass.'">';
if (count($Methods) > 0) {
    echo '<div class="Methods">';
    foreach ($Methods as $Key => $Method) {
        $CssClass = 'Method Method_'.$Key;
        echo '<div class="'.$CssClass.'">',
        $Method['SignInHtml'],
        '</div>';
    }
    echo '</div>';
}
echo '</div>';
?>