<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test Elevator</title>
    </head>
    <body>
        <?php
        require_once 'elevator_class.php';
        $elevator = new my_elevator;

        $elevator->current_floor = 1;
        $elevator->request_floor = array(5, 2, 6);
        $elevator->maintenance(array(1, 3));

        $elevator->call_evelator();
        ?>
    </body>
</html>
