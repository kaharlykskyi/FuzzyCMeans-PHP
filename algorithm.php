<?php
define('EPLSION', 0.01);
define('MAX_EXECUTION_CYCLES', 300);
define('POINTS_COUNT', 2000);
define('CLUSTERS_NUM', 3);
define('FUZZ', 2);

class Point
{
    public $r;
    public $g;
    public $b;
}

$iterations = 0;
$decisions = [];

// Random values 0 - 1
function random_float ($min,$max) {
    return ($min+lcg_value()*(abs($max-$min)));
}


//  Fuzzy C Means Algorithm
function distributeOverMatrixU($arr, $m, &$centers) {
    $centers = generateRandomPoints(CLUSTERS_NUM);
    $MatrixU = fillUMatrix(count($arr), count($centers));

    $previousDecisionValue = 0;
    $currentDecisionValue = 1;

    for($a = 0; $a < MAX_EXECUTION_CYCLES && (abs($previousDecisionValue - $currentDecisionValue) > EPLSION); $a++) {
        $previousDecisionValue = $currentDecisionValue;
        $centers = calculateCenters($MatrixU, $m, $arr);

        foreach($MatrixU as $key => &$uRow){
            foreach($uRow as $clusterIndex => &$u){
                $distance = evklidDistance3D($arr[$key], $centers[$clusterIndex]);
                $u = prepareU($distance, $m);
            }

            $uRow = normalizeUMatrixRow($uRow);
        }
        $currentDecisionValue = calculateDecisionFunction($arr, $centers, $MatrixU);
    }
    global $iterations;
    $iterations = $a;

    return $MatrixU;
}

function fillUMatrix($pointsCount, $clustersCount) {
    $MatrixU = [];
    for($i = 0; $i < $pointsCount; $i++){
        $MatrixU[$i] = [];
        for($j=0; $j<$clustersCount; $j++){
            $MatrixU[$i][$j] = random_float(0, 1);
        }
        $MatrixU[$i] = normalizeUMatrixRow($MatrixU[$i]);
    }

    return $MatrixU;
}

function calculateCenters($MatrixU, $m, $points)
{
    $MatrixCentroids = [];

    for($clusterIndex=0; $clusterIndex < CLUSTERS_NUM; $clusterIndex++){
        $tempAr = 0;
        $tempBr = 0;
        $tempAg = 0;
        $tempBg = 0;
        $tempAb = 0;
        $tempBb = 0;

        foreach($MatrixU as $key=>$uRow){
            $tempAr += pow($uRow[$clusterIndex],$m);
            $tempBr += pow($uRow[$clusterIndex],$m) * $points[$key]->r;

            $tempAg += pow($uRow[$clusterIndex],$m);
            $tempBg += pow($uRow[$clusterIndex],$m) * $points[$key]->g;

            $tempAb += pow($uRow[$clusterIndex],$m);
            $tempBb += pow($uRow[$clusterIndex],$m) * $points[$key]->b;
        }

        $MatrixCentroids[$clusterIndex] = new Point();
        $MatrixCentroids[$clusterIndex]->r = $tempBr / $tempAr;
        $MatrixCentroids[$clusterIndex]->g = $tempBg / $tempAg;
        $MatrixCentroids[$clusterIndex]->b = $tempBb / $tempAb;
    }

    return $MatrixCentroids;
}

function calculateDecisionFunction($MatrixPointX, $MatrixCentroids, $MatrixU)
{
    $sum = 0;
    foreach($MatrixU as $index => $uRow){
        foreach($uRow as $clusterIndex => $u){
            $sum += $u * evklidDistance3D($MatrixCentroids[$clusterIndex], $MatrixPointX[$index]);
        }
    }

    global $decisions;
    array_push($decisions, $sum);
    return $sum;
}

function evklidDistance3D($pointA, $pointB)
{
    $distance1 = pow(($pointA->r - $pointB->r),2);
    $distance2 = pow(($pointA->g - $pointB->g),2);
    $distance3 = pow(($pointA->b - $pointB->b),2);
    $distance = $distance1 + $distance2 + $distance3;
    return sqrt($distance);
}

function normalizeUMatrixRow($MatrixURow)
{
    $sum = 0;
    foreach($MatrixURow as $u){
        $sum += $u;
    }

    foreach($MatrixURow as &$u){
        $u = $u / $sum;
    }

    return $MatrixURow;
}

function prepareU($distance, $m)
{
    return pow(1/$distance , 2/($m-1));
}


function generateRandomPoints($count) {
    $points = array_fill(0, $count, false);
    array_walk($points, function(&$value, $key){
        $value = new Point();
        $value->r = rand(0, 255);
        $value->g = rand(0, 255);
        $value->b = rand(0, 255);
    });

    return $points;
}

function getRGB(Point $obj) {
    return $obj->r . "," . $obj->g . "," . $obj->b;
}