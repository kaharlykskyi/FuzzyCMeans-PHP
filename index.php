<?php
include "algorithm.php";

$points = generateRandomPoints(POINTS_COUNT);
$centers = [];
$matrixU = distributeOverMatrixU($points, FUZZ, $centers);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
    <title>Fuzzy C-Means</title>
</head>
<body>
    <div class="container">
        <div class="initial-data">
            <div class="row">
                <div class="col-md-3">
                    <h4 class="text-center">Clusters count: <?=CLUSTERS_NUM?></h4>
                </div>
                <div class="col-md-3">
                    <h4 class="text-center">Points count: <?=POINTS_COUNT?></h4>
                </div>
                <div class="col-md-3">
                    <h4 class="text-center">Fuzzy parameter: <?=FUZZ?></h4>
                </div>
                <div class="col-md-3">
                    <h4 class="text-center">Iterations: <?=$iterations?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <table class="table table-condensed table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th class="text-center">RGB</th>
                        <?php for($i = 1; $i <= CLUSTERS_NUM; $i++): ?>
                            <th class="text-center vertical">Cluster <?=$i?> <div class="small-circle vertical" title="<?=getRGB($centers[$i - 1])?>" style="background: rgb(<?=getRGB($centers[$i - 1])?>)"></div></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($matrixU as $i => $row): ?>
                    <tr>
                        <td><div class="circle text-center filterable-cell" title="<?=getRGB($points[$i])?>" style="background-color: rgb(<?=getRGB($points[$i])?>)"></div><?=getRGB($points[$i])?></td>
                        <?php $maxInRow = max($row); ?>
                        <?php foreach ($row as $val): ?>
                            <td class="filterable-cell <?= $val == $maxInRow ? 'green' : ''; ?>"><?=$val?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <h2>Decision function value</h2>
            <table class="table table-condensed table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th>Iteration</th>
                        <th>Value</th>
                        <th>Difference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($decisions as $i => $decision): ?>
                        <tr>
                            <td><?=$i + 1?></td>
                            <td><?=$decision?></td>
                            <td><?php echo $i > 0 ? $decisions[$i - 1] - $decision : ''?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
