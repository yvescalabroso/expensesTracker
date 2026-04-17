<?php
function loadTransactions() {
    global $dbcon;
    $stmt = $dbcon->query("
        SELECT transaction_id AS id,
               description,
               amount,
               category,
               is_income,
               TO_CHAR(date, 'YYYY-MM-DD') AS date
        FROM   transactions
        ORDER  BY date DESC, transaction_id DESC
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as $i => $row) {
        $rows[$i]['amount']    = (float) $row['amount'];
        $rows[$i]['is_income'] = (bool)  $row['is_income'];
    }
    return $rows;
}

function addTransaction($desc, $amount, $category, $isIncome, $date) {
    global $dbcon;
    $stmt = $dbcon->prepare("
        INSERT INTO transactions (description, amount, category, is_income, date)
        VALUES (:desc, :amount, :category, :is_income, :date)
    ");
    $stmt->execute([
        ':desc'      => $desc,
        ':amount'    => $amount,
        ':category'  => $category,
        ':is_income' => $isIncome ? 'true' : 'false',
        ':date'      => $date,
    ]);
}

function deleteTransaction($id) {
    global $dbcon;
    $stmt = $dbcon->prepare("DELETE FROM transactions WHERE transaction_id = :id");
    $stmt->execute([':id' => $id]);
}
function parseMonth($m) {
    $parts = explode('-', $m);
    return [(int)$parts[0], (int)$parts[1]];
}

function monthLabel($m) {
    list($y, $mo) = parseMonth($m);
    return date('F Y', mktime(0, 0, 0, $mo, 1, $y));
}

function prevMonth($m) {
    list($y, $mo) = parseMonth($m);
    return date('Y-m', mktime(0, 0, 0, $mo - 1, 1, $y));
}

function nextMonth($m) {
    list($y, $mo) = parseMonth($m);
    return date('Y-m', mktime(0, 0, 0, $mo + 1, 1, $y));
}

function filterByMonth($transactions, $month) {
    $result = [];
    foreach ($transactions as $t) {
        if (substr($t['date'], 0, 7) === $month) {
            $result[] = $t;
        }
    }
    return $result;
}

function fmt($number) {
    return '₱' . number_format(abs($number), 2);
}

function url($params = []) {
    $base = array_merge(
        ['month' => $GLOBALS['currentMonth'], 'filter' => $GLOBALS['activeFilter']],
        $params
    );
    return '?' . http_build_query($base);
}