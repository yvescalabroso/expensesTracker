<?php

$currentMonth = $_GET['month']  ?? date('Y-m');
$activeFilter = $_GET['filter'] ?? 'all';

if (!preg_match('/^\d{4}-\d{2}$/', $currentMonth)) {
    $currentMonth = date('Y-m');
}


$validFilters = array_merge(['all'], array_keys($GLOBALS['categories']));
if (!in_array($activeFilter, $validFilters, true)) {
    $activeFilter = 'all';
}

$allTransactions   = loadTransactions();
$monthTransactions = filterByMonth($allTransactions, $currentMonth);

if ($activeFilter === 'all') {
    $filteredTransactions = $monthTransactions;
} else {
    $filteredTransactions = [];
    foreach ($monthTransactions as $t) {
        if ($t['category'] === $activeFilter) {
            $filteredTransactions[] = $t;
        }
    }
}

usort($filteredTransactions, function($a, $b) {
    $dateDiff = strcmp($b['date'], $a['date']);
    return $dateDiff !== 0 ? $dateDiff : $b['id'] - $a['id'];
});

$income = 0;
$spent  = 0;
foreach ($monthTransactions as $t) {
    if ($t['is_income']) {
        $income += $t['amount'];
    } else {
        $spent  += $t['amount'];
    }
}
$balance = $income - $spent;

$categoryTotals = [];
foreach ($monthTransactions as $t) {
    if (!$t['is_income']) {
        $cat = $t['category'];
        if (!isset($categoryTotals[$cat])) {
            $categoryTotals[$cat] = 0;
        }
        $categoryTotals[$cat] += $t['amount'];
    }
}
arsort($categoryTotals);
$maxCategoryTotal = $categoryTotals ? max($categoryTotals) : 1;