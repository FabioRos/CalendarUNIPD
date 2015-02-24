<!DOCTYPE html>
<html>
    
<?php include("lib/UnipdCalendar.php");
$obj=new unipdCalendar();
$obj->printHead();
?>

    <body itemscope itemtype="http://schema.org/SoftwareApplication">
        <?php
            $obj->render();
            $obj->printFooter();
        ?>
    </body>
</html>