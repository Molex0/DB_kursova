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
    tr:nth-child(odd) td:first-child{
      background-color: #ccc;
    }
    h4{
      margin: 0;
    }
  </style>
</head>

<body>
  <h1><a href="index.php"><-- To main</a></h1>
  <form method="get">
    <label for="name">ID солдата:</label>
    <input type="text" name="id" id="id">
    <input type="submit" name="submit" value="Відправити">
  </form>

  <?php
  // Отримання даних з форми
  require_once 'connection.php';
  $link = mysqli_connect($host, $user, $password, $database)
    or die("Помилка" . mysqli_error($link));

  if (isset($_GET['submit'])) {
    // Отримання даних з форми
    $id = htmlentities(mysqli_real_escape_string($link, $_GET['id']));


    $sql =
      "SELECT g.name AS soldier_name, a.name AS army_name, b.name AS brigada_name, c.name AS chastina_name, d.name AS rota_name, e.name AS vzvod_name, f.name AS department_name, n.name AS commander_name_a, m.name AS commander_name_b ,l.name AS commander_name_c,k.name AS commander_name_r ,j.name AS commander_name_v,h.name AS commander_name_d
    FROM soldier AS g
    INNER JOIN department AS f ON g.department_id = f.department_id
    INNER JOIN vzvod AS e ON f.vzvod_id = e.vzvod_id
    INNER JOIN rota AS d ON e.rota_id = d.rota_id
    INNER JOIN chastina AS c ON d.chastina_id = c.chastina_id
    INNER JOIN brigada AS b ON c.brigada_id = b.brigada_id
    INNER JOIN army AS a ON b.army_id = a.army_id
    LEFT JOIN soldier AS h ON f.commander_id = h.soldier_id
    LEFT JOIN soldier AS j ON e.commander_id = j.soldier_id
    LEFT JOIN soldier AS k ON d.commander_id = k.soldier_id
    LEFT JOIN soldier AS l ON c.commander_id = l.soldier_id
    LEFT JOIN soldier AS m ON b.commander_id = m.soldier_id
    LEFT JOIN soldier AS n ON a.commander_id = n.soldier_id
    WHERE g.soldier_id = $id;
    ";

    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ПІБ</th><th>Армія</th><th>Бригада</th><th>Частина</th><th>Рота</th><th>Взвод</th><th>Відділення</th>";

      $row = mysqli_fetch_row($result);
      echo "<tr>";
      for ($j = 0; $j < 7; ++$j) echo "<td>$row[$j]</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><h4>Командир</h4></td>";
      for ($j = 7; $j < 13; ++$j) echo "<td>$row[$j]</td>";
      echo "</tr>";

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