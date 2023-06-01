<!doctype html>
<html lang="en">

<head>
    <title>kon3ctados! - Prueba selección</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css">


</head>

<?php
require_once('./db.php');

/*
This section initializes a variable with the existing fields (comunas) in the database records, 
which are necessary for the autocomplete feature in the form.
*/
$query = "SELECT DISTINCT comuna FROM cartera ORDER BY comuna ASC";
$rs = mysqli_query($conn, $query);
echo '<script>';
echo 'const comuna = [';
while ($row = mysqli_fetch_assoc($rs)) {
    echo "'" . $row['comuna'] . "',";
}
echo ']';
echo '</script>';

//This variable stores the number of matches for the filter. 
//A value of -1 means that no search has been performed yet.
$num_rows = -1;

/*
This condition checks if a filter has been previously sent, 
and if so, it verifies if it has content.
*/
if (isset($_POST['filter']) && strlen($_POST['filter']) > 0) {
    $filters = $_POST['filter'];
    //This line replaces the manually entered regions by the user with their exact value in the database.
    $filters = preg_replace(
        [
            '/\bIV\b/',
            '/\bV\b/',
            '/\bVI\b/',
            '/\bVII\b/',
            '/\bVIII\b/',
            '/\bX\b/',
            '/\bXIII\b/',
        ],
        [
            'IV De Coquimbo',
            'V de Valparaíso',
            'VI Del Libertador B. OHiggins',
            'VII Del Maule',
            'VIII Del Bíobío',
            'X De Los Lagos',
            'XIII Metropolitana de Santiago',

        ],
        $filters
    );
    //Next, the aliases are replaced with the column names.
    $filters = str_replace(['amount'], ['total_doc'], $filters);
    //execution of the query
    $query = "SELECT * FROM cartera WHERE " . $filters . ";";
    $rs = mysqli_query($conn, $query);
    //Save the number of matches to display later.
    $num_rows = mysqli_num_rows($rs);

    //The filter entered by the user is saved.
    $query2 = "INSERT INTO filters (value, matches) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query2);
    mysqli_stmt_bind_param($stmt, 'si', $_POST['filter'], $num_rows);
    if (mysqli_stmt_execute($stmt)) {
        echo 'filtro guardado';
    } else {
        echo 'filtro NO guardado';
    }
    mysqli_stmt_close($stmt);
}
?>

<body class="p-0 m-0">
    <header>
        <ul class="nav justify-content-center  bg-dark">
            <li class="nav-item">
                <a class="nav-link active" href="#" aria-current="page">&nbsp;</a>
            </li>
        </ul>
    </header>

    <div class="container p-3 my-3 shadow">
        <div class="row justify-content-center align-items-center">
            <form action="" method="post" class="container d-flex flex-column">
                <h4 class="my-2">Introduzca los filtros.</h4>
                <hr>
                <div>
                    <select name="campos" id="campos"></select>
                    <select name="operaciones" id="operaciones"></select>
                    <input type="text" name="valor" id="valor">
                    <button class="btn btn-dark btn-sm" type="button" id="btnAnhadirCampo">Añadir Campo</button>
                    <button class="btn btn-info btn-sm" type="button" id="btnLimpiarFiltros">Limpiar Filtros</button>
                </div>
                <input type="text" name="filter" id="filter" class="form-control my-2" />
                <input type="submit" value="Ejecutar" name="submit" class="btn btn-dark my-2">
            </form>
        </div>
    </div>
    <?php
    //Display the number of matches.
    if ($num_rows >= 0) {
        echo '<div class="container p-3 my-3 shadow">';
        echo '<div class="row justify-content-center align-items-center">';
        echo '<p><i>Se han encontrado ' . $num_rows . ' registros.</i></p>';
        echo '<hr>';
        echo '<p><small style="font-size:9px">' . $query . '</small></p>';
        echo '</div>';
        echo '</div>';
    }
    ?>

    <div class="container p-3 my-3 shadow">
        <div class="row justify-content-center align-items-center">
            <p>Filtros Previos</p>
            <hr>
            <table class="table table-stripped">
                <thead>
                    <thead>
                        <th>Filtro</th>
                        <th>Coincidencias</th>
                        <th>Acciones</th>
                    </thead>
                </thead>
                <tbody>
                    <?php
                    //This section prints on the screen the previously used filters, which are stored in the database.
                    $rs = mysqli_query($conn, 'SELECT * FROM filters;');
                    while ($row = mysqli_fetch_assoc($rs)) {
                        echo '<tr><td id="' . $row['id'] . '">' . $row['value'] . '</td><td>' . $row['matches'] . '</td><td> <button type="button" class="btn btn-info btn-sm btn-usar-filtro" data-id="' . $row['id'] . '">Usar Filtro</button> </td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>

    <script src="app.js"></script>
</body>

</html>

<?php
//Finally, we close the database connection to free up resources.
mysqli_close($conn);
?>