<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Elevator</title>
    </head>
    <body>
        <?php
        require_once 'elevator.php';
        $elevator = new elevator;
                
        print_r(class_implements(new elevator()));
        ?>
    </body>
</html>
