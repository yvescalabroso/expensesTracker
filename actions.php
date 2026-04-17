<?php

$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {

        $desc     = trim($_POST['description'] ?? '');
        $amount   = (float)($_POST['amount'] ?? 0);
        $txType   = $_POST['tx_type'] ?? 'expense';
        $isIncome = ($txType === 'income');
        // Income transactions get the 'Income' category; expenses must pick one
        $category = $isIncome ? 'Income' : ($_POST['category'] ?? '');
        $date     = $_POST['date'] ?? '';

        $errors = [];
        if (!$desc) {
            $errors[] = 'Please enter a description.';
        }
        if ($amount <= 0) {
            $errors[] = 'Please enter an amount greater than zero.';
        }
        // For expenses, validate against the expense categories (not including 'Income')
        if (!$isIncome && !array_key_exists($category, $GLOBALS['categories'])) {
            $errors[] = 'Please pick a category.';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors[] = 'Please pick a valid date.';
        }

        if ($errors) {
            $alert = ['type' => 'error', 'msg' => implode(' ', $errors)];
        } else {
            addTransaction($desc, $amount, $category, $isIncome, $date);
            $alert = ['type' => 'success', 'msg' => $isIncome ? 'Income added successfully!' : 'Expense saved!'];
        }
    }

    if ($_POST['action'] === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            deleteTransaction($id);
            $alert = ['type' => 'success', 'msg' => 'Transaction removed.'];
        }
    }
}