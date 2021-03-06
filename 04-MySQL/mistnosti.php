<?php

require_once ("include/db_connect.php");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Výpis místností</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body><div class="container"><?php

$pdo = DB::connect();
$stmt = $pdo->query('SELECT * FROM room');

if ($stmt->rowCount() == 0){
    echo "Databáze neobsahuje žádná data";
} else {
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Název</th><th>Číslo</th><th>Telefon</th></tr></thead>";

    echo "<tbody>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='mistnost.php?room_id={$row->room_id}'>{$row->name}</a></td>";
        echo "<td>{$row->no}</td>";
        echo "<td>" . ($row->phone ?: "&mdash;") . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";

    echo "</table>";
}
    ?></div></body>
</html>

