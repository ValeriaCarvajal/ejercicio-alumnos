<?php
$archivoAlumnos = 'alumnos.json';

function obtenerAlumnos()
{
    global $archivoAlumnos;

    if (file_exists($archivoAlumnos)) {
        $contenido = file_get_contents($archivoAlumnos);
        if (!empty($contenido)) {
            $alumnos = json_decode($contenido, true);
            if ($alumnos !== null) {
                return $alumnos;
            }
        }
    }

    return [];
}

function guardarAlumnos($alumnos)
{
    global $archivoAlumnos;

    $contenido = json_encode($alumnos);
    file_put_contents($archivoAlumnos, $contenido);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST["cedula"];
    $nombre = $_POST["nombre"];
    $matematicas = $_POST["matematicas"];
    $fisica = $_POST["fisica"];
    $programacion = $_POST["programacion"];

    $alumno = array(
        "cedula" => $cedula,
        "nombre" => $nombre,
        "matematicas" => $matematicas,
        "fisica" => $fisica,
        "programacion" => $programacion
    );

    $alumnos = obtenerAlumnos();
    $alumnos[] = $alumno;

    guardarAlumnos($alumnos);

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

$alumnos = obtenerAlumnos();

$promedioMatematicas = 0;
$promedioFisica = 0;
$promedioProgramacion = 0;
$totalAlumnos = count($alumnos);

$aprobadosMatematicas = 0;
$aprobadosFisica = 0;
$aprobadosProgramacion = 0;

$aplazadosMatematicas = 0;
$aplazadosFisica = 0;
$aplazadosProgramacion = 0;

$aprobadosTodasMaterias = 0;
$aprobadosUnaMateria = 0;
$aprobadosDosMaterias = 0;

$maximaNotaMatematicas = 0;
$maximaNotaFisica = 0;
$maximaNotaProgramacion = 0;

foreach ($alumnos as $alumno) {
    $promedioMatematicas += $alumno['matematicas'];
    $promedioFisica += $alumno['fisica'];
    $promedioProgramacion += $alumno['programacion'];

    if ($alumno['matematicas'] >= 10) {
        $aprobadosMatematicas++;
    } else {
        $aplazadosMatematicas++;
    }

    if ($alumno['fisica'] >= 10) {
        $aprobadosFisica++;
    } else {
        $aplazadosFisica++;
    }

    if ($alumno['programacion'] >= 10) {
        $aprobadosProgramacion++;
    } else {
        $aplazadosProgramacion++;
    }

    $cantidadAprobadas = 0;
    if ($alumno['matematicas'] >= 10) {
        $cantidadAprobadas++;
    }
    if ($alumno['fisica'] >= 10) {
        $cantidadAprobadas++;
    }
    if ($alumno['programacion'] >= 10) {
        $cantidadAprobadas++;
    }

    if ($cantidadAprobadas == 3) {
        $aprobadosTodasMaterias++;
    } elseif ($cantidadAprobadas == 1) {
        $aprobadosUnaMateria++;
    } elseif ($cantidadAprobadas == 2) {
        $aprobadosDosMaterias++;
    }

    $maximaNotaMatematicas = max($maximaNotaMatematicas, $alumno['matematicas']);
    $maximaNotaFisica = max($maximaNotaFisica, $alumno['fisica']);
    $maximaNotaProgramacion = max($maximaNotaProgramacion, $alumno['programacion']);
}

$promedioMatematicas /= $totalAlumnos;
$promedioFisica /= $totalAlumnos;
$promedioProgramacion /= $totalAlumnos;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registro y control de datos de estudiantes</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <div class="container mt-4">
    <h2>Formulario de Tarjetas de Estudiantes</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <div class="form-group">
        <label for="cedula"> Numero de Cédula:</label>
        <input type="number" min="0" class="form-control" id="cedula" name="cedula" required>
        <div  class="form-text">Introduzca su cedula</div>
      </div>
      <div class="form-group">
        <label for="nombre"> Alumno:</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
        <div  class="form-text">Introduzca su nombre</div>
      </div>
      <div class="form-group">
        <label for="matematicas"> Ingrese nota de Matemáticas</label>
        <input type="number" min="0" max="20" class="form-control" id="matematicas" name="matematicas" required>

      </div>
      <div class="form-group">
        <label for="fisica"> Ingrese nota de Física:</label>
        <input type="number" min="0" max="20" class="form-control" id="fisica" name="fisica" required>

      </div>
      <div class="form-group">
        <label for="programacion">Ingrese nota de programación:</label>
        <input type="number" min="0" max="20" class="form-control" id="programacion" name="programacion" required>   

      </div>
      <hr>
      <button type="submit" class="btn btn-primary">Guardar</button>
      <hr>
    </form>

    <h2>Datos</h2>
    <br>
    <table class="table">

        <?php
        foreach ($alumnos as $alumno) {
            echo "<tr>";
            echo "<td>" . $alumno['cedula'] . "</td>";
            echo "<td>" . $alumno['nombre'] . "</td>";
            echo "<td>" . $alumno['matematicas'] . "</td>";
            echo "<td>" . $alumno['fisica'] . "</td>";
            echo "<td>" . $alumno['programacion'] . "</td>";
            echo "</tr>";
        }
        ?>
      </tbody>
    </table>

    <h2>Promedio de Materias</h2>
    <hr>
    <p>Promedio de Matemáticas: <?php echo $promedioMatematicas; ?></p>
    <p>Promedio de Física: <?php echo $promedioFisica; ?></p>
    <p>Promedio de Programación: <?php echo $promedioProgramacion; ?></p>

    <h2>Conteo de Alumnos</h2>
    <hr>
    <h3>Cantidad de alumnos aprobados en Matematica</h3>
    <p>Alumnos Aprobados en Matemáticas: <?php echo $aprobadosMatematicas; ?></p>
    <p>Alumnos Aplazados en Matemáticas: <?php echo $aplazadosMatematicas; ?></p>
    <h3>Cantidad de alumnos aprobados en Fisica</h3>
    <p>Alumnos Aprobados en Física: <?php echo $aprobadosFisica; ?></p>
    <p>Alumnos Aplazados en Física: <?php echo $aplazadosFisica; ?></p>
    <h3>Cantidad de alumnos aprobados en Programacion</h3>
    <p>Alumnos Aprobados en Programación: <?php echo $aprobadosProgramacion; ?></p>
    <p>Alumnos Aplazados en Programación: <?php echo $aplazadosProgramacion; ?></p>
    <h3>Analisis Final</h3>
    <p>Alumnos que han Aprobado todas las Materias: <?php echo $aprobadosTodasMaterias; ?></p>
    <p>Alumnos que han Aprobado solo una Materia: <?php echo $aprobadosUnaMateria; ?></p>
    <p>Alumnos que han Aprobado dos Materias: <?php echo $aprobadosDosMaterias; ?></p>

    <h2>Mejores calificaciones</h2>
    <hr>
    <p>Matemáticas: <?php echo $maximaNotaMatematicas; ?></p>
    <p>Física: <?php echo $maximaNotaFisica; ?></p>
    <p>Programación: <?php echo $maximaNotaProgramacion; ?></p>
  </div>
</body>
</html>