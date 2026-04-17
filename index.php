<?php

$categories = [
    'Food'          => 'Food',
    'Transport'     => 'Transport',
    'Housing'       => 'Housing',
    'Health'        => 'Health',
    'Entertainment' => 'Entertainment',
    'Shopping'      => 'Shopping',
    'Education'     => 'Education',
    'Sports'        => 'Sports',
    'Other'         => 'Other'
];

require_once __DIR__ . '/dbcon.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/actions.php';
require_once __DIR__ . '/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expense Tracker</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">

  <header class="app-header">
    <div class="app-title">Expense<span>.</span>Tracker</div>
    <nav class="month-nav">
      <a href="<?= url(['month' => prevMonth($currentMonth), 'filter' => 'all']) ?>">&#8249;</a>
      <div class="month-label"><?= htmlspecialchars(monthLabel($currentMonth)) ?></div>
      <a href="<?= url(['month' => nextMonth($currentMonth), 'filter' => 'all']) ?>">&#8250;</a>
    </nav>
  </header>

  <?php if ($alert): ?>
    <div class="alert alert-<?= $alert['type'] === 'success' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($alert['msg']) ?>
    </div>
  <?php endif; ?>

  <div class="summary-grid">
    <div class="summary-card income">
      <div class="summary-label">Income</div>
      <div class="summary-value"><?= fmt($income) ?></div>
    </div>
    <div class="summary-card spent">
      <div class="summary-label">Spent</div>
      <div class="summary-value"><?= fmt($spent) ?></div>
    </div>
    <div class="summary-card balance">
      <div class="summary-label">Balance</div>
      <div class="summary-value"><?= ($balance < 0 ? '-' : '+') . fmt($balance) ?></div>
    </div>
  </div>

  <div class="main-cols">
    <div class="section-card">
      <div class="section-title">Add Transaction</div>
      <form method="POST" action="<?= url() ?>">
        <input type="hidden" name="action" value="add">

        <div class="type-toggle">
          <label class="type-btn" id="btnExpense">
            <input type="radio" name="tx_type" value="expense"
              <?= ($_POST['tx_type'] ?? 'expense') !== 'income' ? 'checked' : '' ?>>
            ✕ Expense
          </label>
          <label class="type-btn" id="btnIncome">
            <input type="radio" name="tx_type" value="income"
              <?= ($_POST['tx_type'] ?? '') === 'income' ? 'checked' : '' ?>>
            + Income
          </label>
        </div>

        <div class="form-row">
          <label class="form-label" for="description">Description</label>
          <input class="form-control" type="text" id="description" name="description"
            placeholder="e.g. Lunch, Bus fare" maxlength="255"
            value="<?= htmlspecialchars($_POST['description'] ?? '') ?>" autocomplete="off">
        </div>

        <div class="form-row">
          <label class="form-label" for="amount">Amount</label>
          <input class="form-control" type="number" id="amount" name="amount"
            placeholder="0.00" min="0.01" step="0.01"
            value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
        </div>

        <div class="form-row">
          <label class="form-label" for="categorySelect">Category</label>
          <select class="form-control" name="category" id="categorySelect">
            <option value="">-- Pick a category --</option>
            <?php foreach ($categories as $key => $label): ?>
              <option value="<?= htmlspecialchars($key) ?>"
                <?= ($_POST['category'] ?? '') === $key ? 'selected' : '' ?>>
                <?= htmlspecialchars($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <label class="form-label" for="date">Date</label>
          <input class="form-control" type="date" id="date" name="date"
            value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>">
        </div>

        <button class="submit-btn btn-expense" type="submit" id="submitBtn">✕ Add Expense</button>
      </form>
    </div>

    <div class="section-card">
      <div class="section-title">Spending by Category</div>
      <?php if ($categoryTotals): ?>
        <?php foreach ($categoryTotals as $catKey => $total): ?>
          <?php $pct = $maxCategoryTotal > 0 ? round($total / $maxCategoryTotal * 100) : 0; ?>
          <div class="cat-row">
            <div class="cat-header">
              <span class="cat-name"><?= htmlspecialchars($categories[$catKey] ?? $catKey) ?></span>
              <span class="cat-amt"><?= fmt($total) ?></span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-data">No expenses this month yet.</div>
      <?php endif; ?>
    </div>
  </div>

  <div class="section-card">
    <div class="section-title">Transactions</div>

    <div class="filter-tabs">
      <a href="<?= url(['filter' => 'all']) ?>"
         class="filter-tab <?= $activeFilter === 'all' ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $key => $label): ?>
        <?php
          $found = false;
          foreach ($monthTransactions as $t) {
              if ($t['category'] === $key) { $found = true; break; }
          }
          if (!$found) continue;
        ?>
        <a href="<?= url(['filter' => $key]) ?>"
           class="filter-tab <?= $activeFilter === $key ? 'active' : '' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (!$filteredTransactions): ?>
      <div class="no-data" style="padding: 32px 0;">No transactions found. Add one above!</div>
    <?php else: ?>
      <table class="tx-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Category</th>
            <th style="text-align:right">Amount</th>
            <th style="text-align:center">Del</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($filteredTransactions as $tx): ?>
            <?php
              $catLabel = $categories[$tx['category']] ?? $tx['category'];
              $dateStr  = date('M j', strtotime($tx['date']));
            ?>
            <tr>
              <td class="tx-date"><?= htmlspecialchars($dateStr) ?></td>
              <td class="tx-desc"><?= htmlspecialchars($tx['description']) ?></td>
              <td><span class="cat-badge"><?= htmlspecialchars($catLabel) ?></span></td>
              <td class="tx-amount <?= $tx['is_income'] ? 'income' : 'expense' ?>">
                <?= $tx['is_income'] ? '+' : '-' ?><?= fmt($tx['amount']) ?>
              </td>
              <td class="tx-del">
                <form method="POST" action="<?= url() ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$tx['id'] ?>">
                  <button class="del-btn" type="submit"
                    onclick="return confirm('Delete this transaction?')">✕</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>

<script>
  const btnExpense     = document.getElementById('btnExpense');
  const btnIncome      = document.getElementById('btnIncome');
  const submitBtn      = document.getElementById('submitBtn');
  const categorySelect = document.getElementById('categorySelect');
  const expenseRadio   = btnExpense.querySelector('input');
  const incomeRadio    = btnIncome.querySelector('input');

  function updateForm() {
    const isIncome = incomeRadio.checked;
    btnExpense.className = 'type-btn' + (!isIncome ? ' active-expense' : '');
    btnIncome.className  = 'type-btn' + ( isIncome ? ' active-income'  : '');
    submitBtn.textContent = isIncome ? '+ Add Income' : '✕ Add Expense';
    submitBtn.className   = 'submit-btn ' + (isIncome ? 'btn-income' : 'btn-expense');
    categorySelect.disabled = isIncome;
  }

  expenseRadio.addEventListener('change', updateForm);
  incomeRadio.addEventListener('change', updateForm);
  updateForm();
</script>
</body>
</html>