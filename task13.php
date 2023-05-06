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
    <br>
    <select name="type" id="type">
      <option value="brigada">Бригада</option>
      <option value="army">Армія</option>
    </select>
    <br>
    <div>
    <input type="radio" id="count" name="count" value="max">
    <label for="max">Максимум</label>
    </div>
    <br>
    <div>
    <input type="radio" id="count" name="count" value="min">
    <label for="min">Мінімум</label>
    </div>
    <br>
    <input type="submit" name="submit" value="Відправити">
  </form>

  <?php
  // Отримання даних з форми
  require_once 'connection.php';
  $link = mysqli_connect($host, $user, $password, $database)
    or die("Помилка" . mysqli_error($link));

  if (isset($_GET['submit'])) {
    $type = htmlentities(mysqli_real_escape_string($link, $_GET['type']));
    $count = htmlentities(mysqli_real_escape_string($link, $_GET['count']));

    if ($type == 'army') {
      if($count == 'max'){
      $sql =
        "SELECT a.army_id, a.name AS brigada_name, COUNT(c.chastina_id) AS num_chastinas, s.name AS commander_name
        FROM army a
        LEFT JOIN brigada b ON a.army_id = b.army_id
        LEFT JOIN chastina c ON b.brigada_id = c.brigada_id
        LEFT JOIN soldier s ON a.commander_id = s.soldier_id
        GROUP BY a.army_id, a.name, s.name
        HAVING COUNT(c.chastina_id) = (
          SELECT COUNT(c1.chastina_id)
          FROM chastina c1
          JOIN brigada b1 ON c1.brigada_id = b1.brigada_id
          WHERE b1.army_id = a.army_id
          GROUP BY b1.army_id
          ORDER BY COUNT(c1.chastina_id) ASC
          LIMIT 1
        )
        AND COUNT(c.chastina_id) = (
          SELECT MAX(c2.num_chastinas)
          FROM (
            SELECT COUNT(c3.chastina_id) AS num_chastinas
            FROM chastina c3
            JOIN brigada b3 ON c3.brigada_id = b3.brigada_id
            GROUP BY b3.army_id
          ) AS c2
        )
        ORDER BY num_chastinas DESC;";
      }
      elseif($count == 'min')
      $sql =
        "SELECT a.army_id, a.name AS brigada_name, COUNT(c.chastina_id) AS num_chastinas, s.name AS commander_name
        FROM army a
        LEFT JOIN brigada b ON a.army_id = b.army_id
        LEFT JOIN chastina c ON b.brigada_id = c.brigada_id
        LEFT JOIN soldier s ON a.commander_id = s.soldier_id
        GROUP BY a.army_id, a.name, s.name
        HAVING COUNT(c.chastina_id) = (
          SELECT COUNT(c1.chastina_id)
          FROM chastina c1
          JOIN brigada b1 ON c1.brigada_id = b1.brigada_id
          WHERE b1.army_id = a.army_id
          GROUP BY b1.army_id
          ORDER BY COUNT(c1.chastina_id) ASC
          LIMIT 1
        )
        AND COUNT(c.chastina_id) = (
          SELECT MIN(c2.num_chastinas)
          FROM (
            SELECT COUNT(c3.chastina_id) AS num_chastinas
            FROM chastina c3
            JOIN brigada b3 ON c3.brigada_id = b3.brigada_id
            GROUP BY b3.army_id
          ) AS c2
        )
        ORDER BY num_chastinas DESC;";
    }
    elseif($type == 'brigada'){
      {
        if($count == 'min'){
        $sql =
          "SELECT b.brigada_id, b.name AS brigada_name, COUNT(c.chastina_id) AS num_chastinas, s.name AS commander_name
          FROM brigada b
          LEFT JOIN chastina c ON b.brigada_id = c.brigada_id
          LEFT JOIN army a ON b.army_id = a.army_id
          LEFT JOIN soldier s ON b.commander_id = s.soldier_id
          GROUP BY b.brigada_id, b.name, a.name
          HAVING COUNT(c.chastina_id) = (
            SELECT MIN(c1.num_chastinas)
            FROM (
              SELECT COUNT(c2.chastina_id) AS num_chastinas
              FROM chastina c2
              JOIN brigada b2 ON c2.brigada_id = b2.brigada_id
              GROUP BY b2.brigada_id
            ) AS c1
          )
          AND COUNT(c.chastina_id) = (
            SELECT MIN(c2.num_chastinas)
            FROM (
              SELECT COUNT(c3.chastina_id) AS num_chastinas
              FROM chastina c3
              GROUP BY c3.brigada_id
            ) AS c2
          )
          ORDER BY num_chastinas DESC;";
        }
        elseif($count == 'max')
        $sql =
          "SELECT b.brigada_id, b.name AS brigada_name, COUNT(c.chastina_id) AS num_chastinas, s.name AS commander_name
          FROM brigada b
          LEFT JOIN chastina c ON b.brigada_id = c.brigada_id
          LEFT JOIN army a ON b.army_id = a.army_id
          LEFT JOIN soldier s ON b.commander_id = s.soldier_id
          GROUP BY b.brigada_id, b.name, a.name
          HAVING COUNT(c.chastina_id) = (
            SELECT MAX(c1.num_chastinas)
            FROM (
              SELECT COUNT(c2.chastina_id) AS num_chastinas
              FROM chastina c2
              JOIN brigada b2 ON c2.brigada_id = b2.brigada_id
              GROUP BY b2.brigada_id
            ) AS c1
          )
          AND COUNT(c.chastina_id) = (
            SELECT MAX(c2.num_chastinas)
            FROM (
              SELECT COUNT(c3.chastina_id) AS num_chastinas
              FROM chastina c3
              GROUP BY c3.brigada_id
            ) AS c2
          )
          ORDER BY num_chastinas DESC;";
      }
    }
    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>ID</th><th>Підрозділ</th><th>К-ть частин</th><th>Командир</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 4; ++$j) echo "<td>$row[$j]</td>";
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