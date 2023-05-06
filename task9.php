<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    body {
      display: flex;
      justify-content: center;
      flex-direction: column;
    }

    form {
      margin: 120px auto;
      width: 300px;
      display: flex;
      flex-direction: column;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin: 20px 0;
    }

    th,
    td {
      text-align: left;
      padding: 8px;
    }

    th {
      background-color: #ccc;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <h1><a href="index.php"><-- To main</a></h1>
  <form method="get">
    <label for="type">Тип підрозділу:</label>
    <select name="type" id="type">
      <option value="chastina">Частина</option>
      <option value="brigada">Бригада</option>
      <option value="army">Армія</option>
    </select>
    <br>
    <label for="name">ID підрозділу:</label>
    <input type="text" name="id" id="id">
    <br>
    <label for="eq">Тип озброєння:</label>
    <select name="eq" id="eq">
      <option value="">Всі</option>
      <option value="БПЛА">БПЛА</option>
      <option value="Вогнепальна зброя">Вогнепальна зброя</option>
      <option value="Артилерія">Артилерія </option>
      <option value="Ракетні комплекси">Ракетні комплекси</option>
      <option value="Бронежилети">Бронежилети</option>
      <option value="Підривні пристрої">Підривні пристрої</option>
    </select>
    <br>
    <input type="submit" name="submit" value="Відправити">
  </form>

  <?php
  // Отримання даних з форми
  require_once 'connection.php';
  $link = mysqli_connect($host, $user, $password, $database)
    or die("Помилка" . mysqli_error($link));

  if (isset($_GET['submit'])) {


    // Отримання даних з форми
    $type = htmlentities(mysqli_real_escape_string($link, $_GET['type']));
    $id = htmlentities(mysqli_real_escape_string($link, $_GET['id']));
    $eq = htmlentities(mysqli_real_escape_string($link, $_GET['eq']));

    if(empty($eq)){
      $condition = "";
    }
    else{
      $condition = "AND et.name = '$eq'";
    }

    if ($type == 'army') {
      $sql = "SELECT et.name, COALESCE(SUM(e.number), 0) as total_number
      FROM army a
      JOIN brigada b ON a.army_id = b.army_id
      JOIN chastina c ON b.brigada_id = c.brigada_id
      JOIN equipment e ON c.chastina_id = e.chastina_id
      JOIN equipment_types et ON e.type = et.equipment_types_id
      WHERE a.army_id = $id $condition
      GROUP BY et.name;";

    } elseif ($type == 'brigada') {
      $sql = "SELECT et.name, COALESCE(SUM(e.number), 0) as total_number
      FROM brigada b
      JOIN chastina c ON b.brigada_id = c.brigada_id
      JOIN equipment e ON c.chastina_id = e.chastina_id
      JOIN equipment_types et ON e.type = et.equipment_types_id
      WHERE b.brigada_id = $id $condition
      GROUP BY et.name;";

    } elseif ($type == 'chastina') {
      $sql = "SELECT et.name, COALESCE(SUM(e.number), 0) as total_number
      FROM chastina c
      JOIN equipment e ON c.chastina_id = e.chastina_id
      JOIN equipment_types et ON e.type = et.equipment_types_id
      WHERE c.chastina_id = $id $condition
      GROUP BY et.name;";
    }

    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>Тип озброєння</th><th>Кількість у підрозділі</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 2; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
      }
      echo "</table>";
      mysqli_free_result($result);
    } else {
      echo "<h3>No results found.</h3>";
    }

    // закриваємо з'єднання з базою даних
    mysqli_close($link);
  }

  ?>
</body>

</html>