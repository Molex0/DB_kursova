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

   
    if ($type == 'army') {
      $sql = "SELECT sporydi.name AS location
      FROM army
      JOIN brigada ON army.army_id = brigada.army_id
      JOIN chastina ON brigada.brigada_id = chastina.brigada_id
      JOIN sporydi ON chastina.location = sporydi.sporydi_id
      WHERE army.army_id = $id;";
    } elseif ($type == 'brigada') {
      $sql = "SELECT sporydi.name AS location
      FROM brigada
      JOIN chastina ON brigada.brigada_id = chastina.brigada_id
      JOIN sporydi ON chastina.location = sporydi.sporydi_id
      WHERE brigada.brigada_id = $id;";
    } elseif ($type == 'chastina') {
      $sql = "SELECT sporydi.name AS location
      FROM chastina
      JOIN sporydi ON chastina.location = sporydi.sporydi_id
      WHERE chastina.chastina_id = $id;";
    } 
    // Виконання запиту
    $result = mysqli_query($link, $sql);
    $rows = mysqli_num_rows($result);

    echo '<h2>Результати пошуку:</h2>';
    echo "Знайдено записів: " . $rows;
    if ($rows > 0) {
      echo "<table>";
      echo "<th>Локація</th>";
      for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        echo "<tr>";

        for ($j = 0; $j < 1; ++$j) echo "<td>$row[$j]</td>";
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