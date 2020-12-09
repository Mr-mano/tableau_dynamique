<?php
use App\Table;
use App\URLHelper;
use App\TableHelper;
use App\NumberHelper;
use App\QueryBuilder;

define('PER_PAGE', 20);

require '../vendor/autoload.php';

$pdo = new PDO("sqlite:../data.sql", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
//construire le QueryBuilder
$query = (new QueryBuilder($pdo))->from('products');

//recherche par ville
if (!empty($_GET['q'])) {
    $query
        ->where('city LIKE :city')
        ->setParam('city', '%' . $_GET['q'] . '%');
}

$table = (new Table($query, $_GET));
$table->sortable('id', 'name', 'city', 'price');
$table->format('price', function($value){
    return NumberHelper::price($value);
});
$table->columns([
        'id' => 'ID',
        'name' => 'Nom',
        'city' => 'Ville',
        'price' => 'Prix'
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Biens immobiliers</title>
</head>

<body class="m-5">

        <form action="" method="get">
            <div class="d-flex container">
                <div class="form-group">
                    <input type="text" class="form-control" name="q" placeholder="Recherche par ville" value="<?= htmlentities($_GET['q'] ?? null) ?>">
                </div>
                <div class="form-group ml-2">
                    <button class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </form>

        <?= $table->render() ?>
            
</body>
</html>