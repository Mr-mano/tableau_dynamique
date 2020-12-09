<?php
use App\URLHelper;
use App\TableHelper;
use App\NumberHelper;

define('PER_PAGE', 20);

require 'vendor/autoload.php';

$pdo = new PDO("sqlite:./data.sql", null, null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


//début de la requête recherche
$query = "SELECT * FROM products";
//compter le nombre d'article pour la pagination
$queryCount = "SELECT COUNT(id) as count FROM products";

$params = [];
$sortable = ["id", "name", "city", "price", "address"];
//recherche par ville
if (!empty($_GET['q'])) {
    $query .= " WHERE city LIKE :city";
    $queryCount .= " WHERE city LIKE :city"; //gére pagination en cas de recherche
    $params['city'] = '%' . $_GET['q'] . '%';
}

//organisation affichage
//si le terme 'sort' est d'ans l'url
if (!empty($_GET['sort']) && in_array($_GET['sort'], $sortable)) {
    $direction = $_GET['dir'] ?? 'asc';
    if (!in_array($direction, ['asc', 'desc'])){
        $direction = 'asc';
    }
    $query .= " ORDER BY " . $_GET['sort'] . " $direction";
}


// Pagination
$page = (int)($_GET['p'] ?? 1);
$offset = ($page-1) * PER_PAGE;
$query .= " LIMIT " . PER_PAGE . " OFFSET $offset";

//preparation execution de la requete recherche
$statement = $pdo->prepare($query);
$statement->execute($params);
$products = $statement->fetchAll();

//preparation execution de la requete paggination
$statement = $pdo->prepare($queryCount);
$statement->execute($params);
$count = (int)$statement->fetch()['count'];
$pages = ceil($count / PER_PAGE);

//dd(http_build_query(array_merge($_GET, ['p' => 3])));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Biens immobiliers</title>
</head>

<body>
    <div class="container mt-5">
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

        <table class="table table-striped">
            <thead>
                <tr>
                <th><?= TableHelper::sort('id', 'ID', $_GET) ?></th>
                <th><?= TableHelper::sort('name', 'Nom', $_GET) ?></th>
                <th><?= TableHelper::sort('price', 'Prix', $_GET) ?></th>
                <th><?= TableHelper::sort('city', 'Ville', $_GET) ?></th>
                <th><?= TableHelper::sort('address', 'Adresse', $_GET) ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                    <tr>
                        <td>#<?= htmlentities($product['id']) ?></td>
                        <td><?= htmlentities($product['name']) ?></td>
                        <td><?= htmlentities(NumberHelper::price($product['price'])) ?></td>
                        <td><?= htmlentities($product['city']) ?></td>
                        <td><?= htmlentities($product['address']) ?></td>
                        
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php if ( $pages > 1 && $page > 1) :?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page -1 )?>" class="btn btn-primary"> < Page précédente</a>
<?php endif ?>
<?php if ($pages > 1 && $page < $pages): ?>
    <a href="?<?= URLHelper::withParam($_GET, "p", $page + 1) ?>" class="btn btn-primary">Page suivante ></a>
<?php endif ?>
    </div>
</body>
</html>