<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Control Panel - Фактурчо</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
      --primary: #0E2038;
      --secondary: #146DAF;
      --accent: #769F86;
      --light-accent: #7CCC87;
      --dark: #05112C;
      --darker: #061D42;
      --text: #333333;
      --light-text: #FFFFFF;
      --bg: #F8FAFC;
      --card-bg: #FFFFFF;
      --border: #E2E8F0;
    }

    body {
      background-color: var(--bg);
      color: var(--text);
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
      width: 250px;
      background: var(--primary);
      color: var(--light-text);
      transition: all 0.3s;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 1000;
    }

    .sidebar-header {
      padding: 20px;
      background: var(--dark);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-header h2 {
      font-size: 1.5rem;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo-img {
      height: 40px;
      width: auto;
    }

    .sidebar-menu {
      padding: 15px 0;
    }

    .sidebar-menu ul {
      list-style: none;
    }

    .sidebar-menu li {
      margin-bottom: 5px;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: var(--light-text);
      text-decoration: none;
      transition: all 0.3s;
      gap: 10px;
    }

    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: var(--secondary);
    }

    .sidebar-menu i {
      width: 20px;
      text-align: center;
    }

    /* Main Content Styles */
    .main-content {
      flex: 1;
      margin-left: 250px;
      transition: all 0.3s;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background: var(--card-bg);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .search-bar {
      display: flex;
      align-items: center;
      background: var(--bg);
      border-radius: 8px;
      padding: 8px 15px;
      width: 300px;
    }

    .search-bar input {
      border: none;
      background: transparent;
      margin-left: 10px;
      width: 100%;
      outline: none;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--secondary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
    }

    .content {
      padding: 30px;
    }

    .page-title {
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-title h1 {
      font-size: 1.8rem;
      color: var(--primary);
    }

    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: var(--secondary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary);
    }

    .btn-success {
      background: var(--accent);
      color: white;
    }

    .btn-success:hover {
      background: var(--light-accent);
    }

    .btn-warning {
      background: #F59E0B;
      color: white;
    }

    .btn-warning:hover {
      background: #D97706;
    }

    .btn-danger {
      background: #EF4444;
      color: white;
    }

    .btn-danger:hover {
      background: #DC2626;
    }

    /* Dashboard Cards */
    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border-top: 4px solid var(--secondary);
    }

    .stat-card.success {
      border-top-color: var(--accent);
    }

    .stat-card.warning {
      border-top-color: #F59E0B;
    }

    .stat-card.info {
      border-top-color: var(--secondary);
    }

    .stat-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .stat-card-title {
      font-size: 0.9rem;
      color: #64748B;
    }

    .stat-card-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
    }

    .stat-card-change {
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .positive {
      color: #10B981;
    }

    .negative {
      color: #EF4444;
    }

    /* Charts and Tables */
    .dashboard-content {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .chart-container, .recent-activities {
      background: var(--card-bg);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .section-title {
      margin-bottom: 15px;
      font-size: 1.2rem;
      color: var(--primary);
    }

    .activities-list {
      list-style: none;
    }

    .activity-item {
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .activity-icon.upload {
      background: var(--accent);
    }

    .activity-icon.process {
      background: var(--secondary);
    }

    .activity-icon.error {
      background: #EF4444;
    }

    .activity-details {
      flex: 1;
    }

    .activity-title {
      font-weight: 600;
    }

    .activity-time {
      font-size: 0.8rem;
      color: #64748B;
    }

    /* Tables */
    .table-container {
      background: var(--card-bg);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid var(--border);
    }

    th {
      background: var(--primary);
      color: white;
      font-weight: 600;
    }

    tr:hover {
      background: #F8FAFC;
    }

    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status.processed {
      background: #D1FAE5;
      color: #065F46;
    }

    .status.pending {
      background: #FEF3C7;
      color: #92400E;
    }

    .status.error {
      background: #FEE2E2;
      color: #991B1B;
    }

    /* Upload Area */
    .upload-area {
      border: 2px dashed var(--border);
      border-radius: 10px;
      padding: 40px;
      text-align: center;
      margin-bottom: 30px;
      background: var(--card-bg);
      transition: all 0.3s;
      cursor: pointer;
    }

    .upload-area:hover {
      border-color: var(--secondary);
      background: #f8fafc;
    }

    .upload-area.dragover {
      border-color: var(--accent);
      background: #f0f9ff;
    }

    .upload-icon {
      font-size: 3rem;
      color: var(--secondary);
      margin-bottom: 1rem;
    }

    .upload-text {
      margin-bottom: 1rem;
    }

    .file-input {
      display: none;
    }

    .file-list {
      display: grid;
      gap: 15px;
      margin-bottom: 30px;
    }

    .file-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px;
      background: var(--card-bg);
      border-radius: 8px;
      border-left: 4px solid var(--secondary);
    }

    .file-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .file-icon {
      width: 40px;
      height: 40px;
      background: var(--secondary);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .file-details h4 {
      margin-bottom: 5px;
    }

    .file-details p {
      color: #64748B;
      font-size: 0.9rem;
    }

    .file-actions {
      display: flex;
      gap: 10px;
    }

    .file-status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status-pending {
      background: #FEF3C7;
      color: #92400E;
    }

    .status-processing {
      background: #DBEAFE;
      color: #1E40AF;
    }

    .status-completed {
      background: #D1FAE5;
      color: #065F46;
    }

    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .action-card {
      background: var(--card-bg);
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      cursor: pointer;
      border-top: 4px solid var(--secondary);
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .action-card.success {
      border-top-color: var(--accent);
    }

    .action-card.warning {
      border-top-color: #F59E0B;
    }

    .action-icon {
      font-size: 2rem;
      color: var(--secondary);
      margin-bottom: 1rem;
    }

    .action-card.success .action-icon {
      color: var(--accent);
    }

    .action-card.warning .action-icon {
      color: #F59E0B;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--primary);
    }

    .form-control {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--secondary);
      box-shadow: 0 0 0 3px rgba(20, 109, 175, 0.1);
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 2000;
      align-items: center;
      justify-content: center;
    }

    .modal.active {
      display: flex;
    }

    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #64748B;
    }

    /* Page Content */
    .page-content {
      display: none;
    }

    .page-content.active {
      display: block;
    }

    /* Notification */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      background: var(--accent);
      color: white;
      border-radius: 8px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      z-index: 3000;
      transform: translateX(400px);
      transition: transform 0.3s;
    }

    .notification.show {
      transform: translateX(0);
    }

    .notification.error {
      background: #EF4444;
    }

    .notification.info {
      background: var(--secondary);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
      }
      .sidebar-header h2, .sidebar-menu span {
        display: none;
      }
      .sidebar-menu a {
        justify-content: center;
        padding: 15px;
      }
      .main-content {
        margin-left: 70px;
      }
      .dashboard-content {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .stats-cards {
        grid-template-columns: 1fr;
      }
      .search-bar {
        width: 200px;
      }
      .quick-actions {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="logo-container">
        <img src="logo.png" alt="Фактурчо" class="logo-img">
        <h2>Фактурчо</h2>
      </div>
    </div>
    <div class="sidebar-menu">
      <ul>
        <li><a href="#" class="nav-link active" data-page="dashboard"><i class="fas fa-home"></i> <span>Табло</span></a></li>
        <li><a href="#" class="nav-link" data-page="create-invoice"><i class="fas fa-file-invoice-dollar"></i> <span>Създай фактура</span></a></li>
        <li><a href="#" class="nav-link" data-page="new-invoices"><i class="fas fa-plus-circle"></i> <span>Нови Фактури</span></a></li>
        <li><a href="#" class="nav-link" data-page="invoices"><i class="fas fa-file-invoice"></i> <span>Фактури</span></a></li>
        <li><a href="#" class="nav-link" data-page="clients"><i class="fas fa-users"></i> <span>Клиенти</span></a></li>
        <li><a href="#" class="nav-link" data-page="reports"><i class="fas fa-chart-bar"></i> <span>Отчети</span></a></li>
      </ul>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Търсене..." id="searchInput">
      </div>
      <div class="user-info">
        <div class="notifications" onclick="showNotification('Няма нови известия', 'info')">
          <i class="fas fa-bell"></i>
        </div>

        <div class="user-avatar">
          <?php
            // show first two letters of user name as avatar
            $uname = $_SESSION['user_name'] ?? '';
            echo $uname ? strtoupper(substr(htmlspecialchars($uname), 0, 2)) : '??';
          ?>
        </div>

        <div class="user-details">
          <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Потребител'); ?></div>
          <div class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? ''); ?></div>
        </div>

        <a href="logout.php" class="btn" style="margin-left:12px; padding:8px 12px; background:#EF4444; color:#fff; border-radius:8px;">
          <i class="fas fa-sign-out-alt"></i> Изход
        </a>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="content">
      <!-- Dashboard Page -->
      <div class="page-content active" id="dashboard">
        <div class="page-title">
          <h1>Табло за управление</h1>
          <button class="btn btn-primary" onclick="showPage('create-invoice')">
            <i class="fas fa-plus"></i> Нова фактура
          </button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-card-title">Обработени фактури</div>
              <i class="fas fa-file-invoice" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="processedInvoices">36</div>
            <div class="stat-card-change positive">
              <i class="fas fa-arrow-up"></i> <span id="processedChange">12%</span> от миналия месец
            </div>
          </div>

          <div class="stat-card success">
            <div class="stat-card-header">
              <div class="stat-card-title">Автоматично разпознати</div>
              <i class="fas fa-robot" style="color: var(--accent);"></i>
            </div>
            <div class="stat-card-value" id="autoRecognized">94%</div>
            <div class="stat-card-change positive">
              <i class="fas fa-arrow-up"></i> <span id="recognitionChange">3%</span> от миналия месец
            </div>
          </div>

          <div class="stat-card warning">
            <div class="stat-card-header">
              <div class="stat-card-title">Чакащи обработка</div>
              <i class="fas fa-clock" style="color: #F59E0B;"></i>
            </div>
            <div class="stat-card-value" id="pendingInvoices">13</div>
            <div class="stat-card-change negative">
              <i class="fas fa-arrow-down"></i> <span id="pendingChange">5%</span> от миналия месец
            </div>
          </div>

          <div class="stat-card info">
            <div class="stat-card-header">
              <div class="stat-card-title">Спестено време</div>
              <i class="fas fa-stopwatch" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="savedTime">16ч</div>
            <div class="stat-card-change positive">
              <i class="fas fa-arrow-up"></i> <span id="timeChange">8%</span> от миналия месец
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
          <div class="action-card" onclick="showPage('create-invoice')">
            <div class="action-icon">
              <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h3>Създай фактура</h3>
            <p>Създай нова фактура</p>
          </div>
          <div class="action-card" onclick="showPage('new-invoices')">
            <div class="action-icon">
              <i class="fas fa-upload"></i>
            </div>
            <h3>Качи Фактури</h3>
            <p>Добави нови фактури за обработка</p>
          </div>
          <div class="action-card success" onclick="showPage('invoices')">
            <div class="action-icon">
              <i class="fas fa-list"></i>
            </div>
            <h3>Всички Фактури</h3>
            <p>Преглед на обработените фактури</p>
          </div>
          <div class="action-card warning" onclick="showPage('reports')">
            <div class="action-icon">
              <i class="fas fa-chart-bar"></i>
            </div>
            <h3>Генерирай Отчет</h3>
            <p>Създай финансов отчет</p>
          </div>
        </div>

        <!-- Charts and Activities -->
        <div class="dashboard-content">
          <div class="chart-container">
            <h3 class="section-title">Обработка на фактури - последни 30 дни</h3>
            <canvas id="invoiceChart" height="250"></canvas>
          </div>

          <div class="recent-activities">
            <h3 class="section-title">Последни активности</h3>
            <ul class="activities-list" id="activitiesList">
              <li class="activity-item">
                <div class="activity-icon upload">
                  <i class="fas fa-upload"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Качени 5 нови фактури</div>
                  <div class="activity-time">Преди 5 минути</div>
                </div>
              </li>
              <li class="activity-item">
                <div class="activity-icon process">
                  <i class="fas fa-cogs"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Фактура #2457 обработена</div>
                  <div class="activity-time">Преди 12 минути</div>
                </div>
              </li>
              <li class="activity-item">
                <div class="activity-icon error">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Грешка при обработка на фактура #2453</div>
                  <div class="activity-time">Преди 1 час</div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Recent Invoices Table -->
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Номер на фактура</th>
                <th>Клиент</th>
                <th>Дата</th>
                <th>Сума</th>
                <th>Статус</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody id="invoicesTableBody">
              <tr>
                <td>#INV-2023-2457</td>
                <td>БГ Строител ЕООД</td>
                <td>15.11.2023</td>
                <td>2,450.00 лв</td>
                <td><span class="status processed">Обработена</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="viewInvoice('INV-2023-2457')">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>#INV-2023-2456</td>
                <td>Техно Импекс АД</td>
                <td>14.11.2023</td>
                <td>5,780.00 лв</td>
                <td><span class="status processed">Обработена</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="viewInvoice('INV-2023-2456')">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Invoice Page -->
      <div class="page-content" id="create-invoice">
        <div class="page-title">
          <h1>Създаване на фактура</h1>
          <button class="btn btn-primary" onclick="saveInvoice()">
            <i class="fas fa-save"></i> Запази фактура
          </button>
        </div>

        <div class="dashboard-content">
          <!-- Invoice Details -->
          <div class="chart-container">
            <h3 class="section-title">Данни за фактурата</h3>
            
            <div class="form-group">
              <label>Номер на фактура</label>
              <input type="text" class="form-control" id="invoiceNumber" placeholder="Напр. INV-2023-001">
            </div>
            
            <div class="form-group">
              <label>Дата на издаване</label>
              <input type="date" class="form-control" id="invoiceDate">
            </div>
            
            <div class="form-group">
              <label>Крайна дата за плащане</label>
              <input type="date" class="form-control" id="dueDate">
            </div>
            
            <div class="form-group">
              <label>Статус</label>
              <select class="form-control" id="invoiceStatus">
                <option value="draft">Чернова</option>
                <option value="sent">Изпратена</option>
                <option value="paid">Платена</option>
                <option value="overdue">Просрочена</option>
              </select>
            </div>
          </div>

          <!-- Client Selection -->
          <div class="recent-activities">
            <h3 class="section-title">Клиент</h3>
            
            <div class="form-group">
              <label>Избери клиент</label>
              <select class="form-control" id="clientSelect" onchange="updateClientDetails()">
                <option value="">-- Избери клиент --</option>
                <option value="client1">Техно Импекс АД</option>
                <option value="client2">БГ Строител ЕООД</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Име на клиент</label>
              <input type="text" class="form-control" id="invoiceClientName" placeholder="Име на клиента">
            </div>
            
            <div class="form-group">
              <label>Булстат</label>
              <input type="text" class="form-control" id="invoiceClientBulstat" placeholder="Булстат">
            </div>
            
            <div class="form-group">
              <label>Адрес</label>
              <textarea class="form-control" id="clientAddress" rows="3" placeholder="Пълен адрес на клиента"></textarea>
            </div>
            
            <button class="btn btn-primary" onclick="showAddClientModal()">
              <i class="fas fa-plus"></i> Добави нов клиент
            </button>
          </div>
        </div>

        <!-- Invoice Items -->
        <div class="table-container">
          <div class="section-title" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Артикули</h3>
            <button class="btn btn-success" onclick="addInvoiceItem()">
              <i class="fas fa-plus"></i> Добави артикул
            </button>
          </div>
          
          <table>
            <thead>
              <tr>
                <th>Описание</th>
                <th>Количество</th>
                <th>Ед. цена</th>
                <th>ДДС %</th>
                <th>Стойност</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody id="invoiceItems">
              <tr>
                <td><input type="text" class="form-control" placeholder="Описание на артикула"></td>
                <td><input type="number" class="form-control" value="1" min="1" onchange="calculateItemTotal(this)"></td>
                <td><input type="number" class="form-control" value="0.00" step="0.01" onchange="calculateItemTotal(this)"></td>
                <td>
                  <select class="form-control" onchange="calculateItemTotal(this)">
                    <option value="20">20%</option>
                    <option value="9">9%</option>
                    <option value="0">0%</option>
                  </select>
                </td>
                <td class="item-total">0.00 лв</td>
                <td>
                  <button class="btn btn-danger" onclick="removeInvoiceItem(this)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Invoice Summary -->
        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-card-title">Общо без ДДС</div>
              <i class="fas fa-receipt" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="subtotalAmount">0.00 лв</div>
          </div>

          <div class="stat-card success">
            <div class="stat-card-header">
              <div class="stat-card-title">ДДС</div>
              <i class="fas fa-percentage" style="color: var(--accent);"></i>
            </div>
            <div class="stat-card-value" id="vatAmount">0.00 лв</div>
          </div>

          <div class="stat-card info">
            <div class="stat-card-header">
              <div class="stat-card-title">Общо с ДДС</div>
              <i class="fas fa-file-invoice-dollar" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="totalAmount">0.00 лв</div>
          </div>
        </div>

        <!-- Additional Notes -->
        <div class="chart-container">
          <h3 class="section-title">Допълнителни бележки</h3>
          <div class="form-group">
            <textarea class="form-control" id="invoiceNotes" rows="4" placeholder="Допълнителни бележки към фактурата..."></textarea>
          </div>
        </div>
      </div>

      <!-- New Invoices Page -->
      <div class="page-content" id="new-invoices">
        <div class="page-title">
          <h1>Нови Фактури</h1>
          <button class="btn btn-primary" onclick="showPage('invoices')">
            <i class="fas fa-list"></i> Всички Фактури
          </button>
        </div>

        <!-- Upload Area -->
        <div class="upload-area" id="uploadArea">
          <div class="upload-icon">
            <i class="fas fa-cloud-upload-alt"></i>
          </div>
          <div class="upload-text">
            <h3>Провлачете файловете тук или кликнете за да качите</h3>
            <p>Поддържани формати: PDF, JPG, PNG, TIFF (макс. 20MB)</p>
          </div>
          <input type="file" id="fileInput" class="file-input" multiple accept=".pdf,.jpg,.jpeg,.png,.tiff">
          <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-folder-open"></i> Избери Файлове
          </button>
        </div>

        <!-- Recent Uploads -->
        <div class="file-list" id="fileList">
          <div class="file-item">
            <div class="file-info">
              <div class="file-icon">
                <i class="fas fa-file-pdf"></i>
              </div>
              <div class="file-details">
                <h4>Фактура_2457.pdf</h4>
                <p>2.4 MB • Качена преди 5 минути</p>
              </div>
            </div>
            <div class="file-actions">
              <span class="file-status status-completed">Обработена</span>
              <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="viewInvoice('INV-2023-2457')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="file-item">
            <div class="file-info">
              <div class="file-icon">
                <i class="fas fa-file-image"></i>
              </div>
              <div class="file-details">
                <h4>Фактура_2458.jpg</h4>
                <p>1.8 MB • Качена преди 12 минути</p>
              </div>
            </div>
            <div class="file-actions">
              <span class="file-status status-processing">Обработва се</span>
              <button class="btn btn-warning" style="padding: 5px 10px;" onclick="processInvoice('2458')">
                <i class="fas fa-sync"></i>
              </button>
            </div>
          </div>

          <div class="file-item">
            <div class="file-info">
              <div class="file-icon">
                <i class="fas fa-file-pdf"></i>
              </div>
              <div class="file-details">
                <h4>Фактура_2459.pdf</h4>
                <p>3.1 MB • Качена преди 1 час</p>
              </div>
            </div>
            <div class="file-actions">
              <span class="file-status status-pending">Чака обработка</span>
              <button class="btn btn-success" style="padding: 5px 10px;" onclick="startProcessing('2459')">
                <i class="fas fa-play"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Processing Options -->
        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-card-title">Автоматично разпознаване</div>
              <i class="fas fa-robot" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="aiStatus">Включено</div>
            <button class="btn btn-primary" style="margin-top: 10px; padding: 5px 10px;" onclick="toggleAI()">Превключи</button>
          </div>

          <div class="stat-card success">
            <div class="stat-card-header">
              <div class="stat-card-title">Валидация</div>
              <i class="fas fa-check-circle" style="color: var(--accent);"></i>
            </div>
            <div class="stat-card-value" id="validationStatus">Активна</div>
            <button class="btn btn-success" style="margin-top: 10px; padding: 5px 10px;" onclick="toggleValidation()">Превключи</button>
          </div>

          <div class="stat-card info">
            <div class="stat-card-header">
              <div class="stat-card-title">Средно време</div>
              <i class="fas fa-stopwatch" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="avgTime">45 сек</div>
            <button class="btn btn-primary" style="margin-top: 10px; padding: 5px 10px;" onclick="showProcessingStats()">Статистики</button>
          </div>
        </div>
      </div>

      <!-- Invoices Page -->
      <div class="page-content" id="invoices">
        <div class="page-title">
          <h1>Управление на фактури</h1>
          <div>
            <button class="btn btn-primary" onclick="showPage('create-invoice')">
              <i class="fas fa-plus"></i> Създай фактура
            </button>
            <button class="btn btn-success" onclick="exportInvoices()" style="margin-left: 10px;">
              <i class="fas fa-download"></i> Експорт
            </button>
          </div>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Номер</th>
                <th>Доставчик</th>
                <th>Дата</th>
                <th>Крайна дата</th>
                <th>Сума</th>
                <th>Статус</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody id="allInvoicesTable">
              <tr>
                <td>INV-001</td>
                <td>Техно Импекс АД</td>
                <td>2023-11-15</td>
                <td>2023-12-15</td>
                <td>2,450.00 лв</td>
                <td><span class="status processed">Обработена</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="viewInvoice('INV-001')">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn btn-success" style="padding: 5px 10px;" onclick="downloadInvoice('INV-001')">
                    <i class="fas fa-download"></i>
                  </button>
                  <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteInvoice('INV-001', this)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>INV-002</td>
                <td>БГ Строител ЕООД</td>
                <td>2023-11-14</td>
                <td>2023-12-14</td>
                <td>1,780.50 лв</td>
                <td><span class="status pending">Чака обработка</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="viewInvoice('INV-002')">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn btn-warning" style="padding: 5px 10px;" onclick="processInvoice('INV-002', this)">
                    <i class="fas fa-play"></i>
                  </button>
                  <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteInvoice('INV-002', this)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Clients Page -->
      <div class="page-content" id="clients">
        <div class="page-title">
          <h1>Управление на клиенти</h1>
          <button class="btn btn-primary" onclick="showAddClientModal()">
            <i class="fas fa-plus"></i> Добави клиент
          </button>
        </div>

        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-card-title">Общо клиенти</div>
              <i class="fas fa-users" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-card-value" id="totalClients">156</div>
            <div class="stat-card-change positive">
              <i class="fas fa-arrow-up"></i> 8% от миналия месец
            </div>
          </div>

          <div class="stat-card success">
            <div class="stat-card-header">
              <div class="stat-card-title">Активни клиенти</div>
              <i class="fas fa-user-check" style="color: var(--accent);"></i>
            </div>
            <div class="stat-card-value" id="activeClients">142</div>
            <div class="stat-card-change positive">
              <i class="fas fa-arrow-up"></i> 5% от миналия месец
            </div>
          </div>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Име на клиент</th>
                <th>Булстат</th>
                <th>Имейл</th>
                <th>Телефон</th>
                <th>Статус</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody id="clientsTable">
              <tr>
                <td>Техно Импекс АД</td>
                <td>123456789</td>
                <td>office@tehnoimpex.bg</td>
                <td>+359 2 123 456</td>
                <td><span class="status processed">Активен</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="editClient('Техно Импекс АД')">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteClient('Техно Импекс АД', this)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>БГ Строител ЕООД</td>
                <td>987654321</td>
                <td>info@bgstroitel.bg</td>
                <td>+359 2 987 654</td>
                <td><span class="status processed">Активен</span></td>
                <td>
                  <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="editClient('БГ Строител ЕООД')">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteClient('БГ Строител ЕООД', this)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reports Page -->
      <div class="page-content" id="reports">
        <div class="page-title">
          <h1>Отчети и анализи</h1>
          <div>
            <button class="btn btn-primary" onclick="generateReport()">
              <i class="fas fa-plus"></i> Генерирай отчет
            </button>
            <button class="btn btn-success" onclick="exportReports()" style="margin-left: 10px;">
              <i class="fas fa-download"></i> Експорт на отчет
            </button>
          </div>
        </div>

        <div class="dashboard-content">
          <div class="chart-container">
            <h3 class="section-title">Финансови показатели - последни 6 месеца</h3>
            <canvas id="reportsChart" height="250"></canvas>
          </div>

          <div class="recent-activities">
            <h3 class="section-title">Готови отчети</h3>
            <ul class="activities-list" id="reportsList">
              <li class="activity-item">
                <div class="activity-icon process">
                  <i class="fas fa-file-pdf"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Месечен финансов отчет</div>
                  <div class="activity-time">Ноември 2023</div>
                </div>
                <button class="btn btn-success" style="padding: 5px 10px;" onclick="downloadReport('financial_november')">
                  <i class="fas fa-download"></i>
                </button>
              </li>
              <li class="activity-item">
                <div class="activity-icon process">
                  <i class="fas fa-file-excel"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Данъчна декларация</div>
                  <div class="activity-time">Q3 2023</div>
                </div>
                <button class="btn btn-success" style="padding: 5px 10px;" onclick="downloadReport('tax_q3')">
                  <i class="fas fa-download"></i>
                </button>
              </li>
              <li class="activity-item">
                <div class="activity-icon process">
                  <i class="fas fa-chart-bar"></i>
                </div>
                <div class="activity-details">
                  <div class="activity-title">Анализ на разходите</div>
                  <div class="activity-time">Октомври 2023</div>
                </div>
                <button class="btn btn-success" style="padding: 5px 10px;" onclick="downloadReport('expenses_october')">
                  <i class="fas fa-download"></i>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div class="modal" id="addClientModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Добави нов клиент</h3>
        <button class="modal-close" onclick="closeModal('addClientModal')">&times;</button>
      </div>
      <div class="form-group">
        <label>Име на клиент</label>
        <input type="text" class="form-control" id="modalClientName">
      </div>
      <div class="form-group">
        <label>Булстат</label>
        <input type="text" class="form-control" id="modalClientBulstat">
      </div>
      <div class="form-group">
        <label>Имейл</label>
        <input type="email" class="form-control" id="modalClientEmail">
      </div>
      <div class="form-group">
        <label>Телефон</label>
        <input type="tel" class="form-control" id="modalClientPhone">
      </div>
      <button class="btn btn-primary" onclick="addNewClient()">Добави клиент</button>
    </div>
  </div>

  <!-- Notification -->
  <div class="notification" id="notification"></div>

  <script>
    // App State
    let appState = {
      processedInvoices: 1248,
      autoRecognized: 94,
      pendingInvoices: 23,
      savedTime: 156,
      totalClients: 156,
      activeClients: 142,
      aiEnabled: true,
      validationEnabled: true,
      clients: [
        { name: 'Техно Импекс АД', bulstat: '123456789', email: 'office@tehnoimpex.bg', phone: '+359 2 123 456', status: 'active' },
        { name: 'БГ Строител ЕООД', bulstat: '987654321', email: 'info@bgstroitel.bg', phone: '+359 2 987 654', status: 'active' }
      ],
      invoices: [
        { id: 'INV-001', supplier: 'Техно Импекс АД', date: '2023-11-15', dueDate: '2023-12-15', amount: '2,450.00 лв', status: 'processed' },
        { id: 'INV-002', supplier: 'БГ Строител ЕООД', date: '2023-11-14', dueDate: '2023-12-14', amount: '1,780.50 лв', status: 'pending' }
      ]
    };

    // Navigation functionality
    function showPage(pageId) {
      document.querySelectorAll('.page-content').forEach(page => {
        page.classList.remove('active');
      });
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      document.getElementById(pageId).classList.add('active');
      document.querySelector(`[data-page="${pageId}"]`).classList.add('active');
      
      // Initialize create invoice page if needed
      if (pageId === 'create-invoice') {
        showCreateInvoicePage();
      }
    }

    // Notification system
    function showNotification(message, type = 'success') {
      const notification = document.getElementById('notification');
      notification.textContent = message;
      notification.className = `notification show ${type}`;
      setTimeout(() => {
        notification.classList.remove('show');
      }, 3000);
    }

    // Update statistics
    function updateStatistics() {
      document.getElementById('processedInvoices').textContent = appState.processedInvoices.toLocaleString();
      document.getElementById('autoRecognized').textContent = appState.autoRecognized + '%';
      document.getElementById('pendingInvoices').textContent = appState.pendingInvoices;
      document.getElementById('savedTime').textContent = appState.savedTime + 'ч';
      document.getElementById('totalClients').textContent = appState.totalClients;
      document.getElementById('activeClients').textContent = appState.activeClients;
    }

    // Modal functions
    function showAddClientModal() {
      document.getElementById('addClientModal').classList.add('active');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('active');
    }

    // Invoice functions
    function viewInvoice(invoiceId) {
      showNotification(`Преглед на фактура ${invoiceId}`, 'info');
    }

    function downloadInvoice(invoiceId) {
      showNotification(`Изтегляне на фактура ${invoiceId}`, 'success');
    }

    function deleteInvoice(invoiceId, button) {
      if (confirm(`Сигурни ли сте, че искате да изтриете фактура ${invoiceId}?`)) {
        const row = button.closest('tr');
        row.style.opacity = '0';
        setTimeout(() => {
          row.remove();
          appState.pendingInvoices = Math.max(0, appState.pendingInvoices - 1);
          updateStatistics();
          showNotification(`Фактура ${invoiceId} е изтрита`, 'success');
        }, 300);
      }
    }

    function processInvoice(invoiceId, button) {
      if (button) {
        const row = button.closest('tr');
        const statusCell = row.querySelector('.status');
        statusCell.textContent = 'Обработена';
        statusCell.className = 'status processed';
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.className = 'btn btn-success';
        button.onclick = function() { downloadInvoice(invoiceId); };
        
        appState.pendingInvoices = Math.max(0, appState.pendingInvoices - 1);
        appState.processedInvoices++;
        updateStatistics();
      }
      showNotification(`Фактура ${invoiceId} е обработена успешно`, 'success');
    }

    function startProcessing(invoiceId) {
      showNotification(`Стартиране на обработка за фактура ${invoiceId}`, 'success');
      // Simulate processing
      setTimeout(() => {
        processInvoice(invoiceId);
      }, 2000);
    }

    function exportInvoices() {
      showNotification('Експортиране на всички фактури...', 'info');
    }

    // Client functions
    function addNewClient() {
      const name = document.getElementById('modalClientName').value;
      const bulstat = document.getElementById('modalClientBulstat').value;
      const email = document.getElementById('modalClientEmail').value;
      const phone = document.getElementById('modalClientPhone').value;
      
      if (name && bulstat && email && phone) {
        const clientsTable = document.getElementById('clientsTable');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
          <td>${name}</td>
          <td>${bulstat}</td>
          <td>${email}</td>
          <td>${phone}</td>
          <td><span class="status processed">Активен</span></td>
          <td>
            <button class="btn" style="padding: 5px 10px; background: var(--secondary); color: white;" onclick="editClient('${name}')">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteClient('${name}', this)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;
        clientsTable.appendChild(newRow);
        
        // Add to client select dropdown
        const clientSelect = document.getElementById('clientSelect');
        const newOption = document.createElement('option');
        newOption.value = 'client' + (appState.clients.length + 1);
        newOption.textContent = name;
        clientSelect.appendChild(newOption);
        
        appState.totalClients++;
        appState.activeClients++;
        updateStatistics();
        
        showNotification(`Клиент ${name} е добавен успешно`, 'success');
        closeModal('addClientModal');
        
        // Reset form
        document.getElementById('modalClientName').value = '';
        document.getElementById('modalClientBulstat').value = '';
        document.getElementById('modalClientEmail').value = '';
        document.getElementById('modalClientPhone').value = '';
      } else {
        showNotification('Моля, попълнете всички полета', 'error');
      }
    }

    function editClient(clientName) {
      showNotification(`Редактиране на клиент ${clientName}`, 'info');
    }

    function deleteClient(clientName, button) {
      if (confirm(`Сигурни ли сте, че искате да изтриете клиент ${clientName}?`)) {
        const row = button.closest('tr');
        row.style.opacity = '0';
        setTimeout(() => {
          row.remove();
          appState.totalClients = Math.max(0, appState.totalClients - 1);
          appState.activeClients = Math.max(0, appState.activeClients - 1);
          updateStatistics();
          showNotification(`Клиент ${clientName} е изтрит`, 'success');
        }, 300);
      }
    }

    // Report functions
    function generateReport() {
      const reportsList = document.getElementById('reportsList');
      const newReport = document.createElement('li');
      newReport.className = 'activity-item';
      newReport.innerHTML = `
        <div class="activity-icon process">
          <i class="fas fa-file-pdf"></i>
        </div>
        <div class="activity-details">
          <div class="activity-title">Нов финансов отчет</div>
          <div class="activity-time">Току-що генериран</div>
        </div>
        <button class="btn btn-success" style="padding: 5px 10px;" onclick="downloadReport('new_report')">
          <i class="fas fa-download"></i>
        </button>
      `;
      reportsList.insertBefore(newReport, reportsList.firstChild);
      showNotification('Нов отчет е генериран успешно', 'success');
    }

    function exportReports() {
      showNotification('Експортиране на отчети...', 'success');
    }

    function downloadReport(reportId) {
      showNotification(`Изтегляне на отчет ${reportId}`, 'success');
    }

    // Processing functions
    function toggleAI() {
      appState.aiEnabled = !appState.aiEnabled;
      const aiStatus = document.getElementById('aiStatus');
      aiStatus.textContent = appState.aiEnabled ? 'Включено' : 'Изключено';
      showNotification(`AI разпознаването е ${appState.aiEnabled ? 'включено' : 'изключено'}`, 'info');
    }

    function toggleValidation() {
      appState.validationEnabled = !appState.validationEnabled;
      const validationStatus = document.getElementById('validationStatus');
      validationStatus.textContent = appState.validationEnabled ? 'Активна' : 'Неактивна';
      showNotification(`Валидацията е ${appState.validationEnabled ? 'активна' : 'неактивна'}`, 'info');
    }

    function showProcessingStats() {
      const avgTime = document.getElementById('avgTime');
      const newTime = (Math.random() * 20 + 35).toFixed(0);
      avgTime.textContent = newTime + ' сек';
      showNotification('Статистиките са актуализирани', 'info');
    }

    // Create Invoice Functions
    function showCreateInvoicePage() {
      // Set default dates
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('invoiceDate').value = today;
      
      // Set due date to 30 days from now
      const dueDate = new Date();
      dueDate.setDate(dueDate.getDate() + 30);
      document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];
      
      // Calculate initial totals
      calculateInvoiceTotals();
    }

    function updateClientDetails() {
      const clientSelect = document.getElementById('clientSelect');
      const selectedClient = clientSelect.value;
      
      if (selectedClient === 'client1') {
        document.getElementById('invoiceClientName').value = 'Техно Импекс АД';
        document.getElementById('invoiceClientBulstat').value = '123456789';
        document.getElementById('clientAddress').value = 'София, бул. "Цариградско шосе" № 123';
      } else if (selectedClient === 'client2') {
        document.getElementById('invoiceClientName').value = 'БГ Строител ЕООД';
        document.getElementById('invoiceClientBulstat').value = '987654321';
        document.getElementById('clientAddress').value = 'Пловдив, ул. "Главна" № 45';
      } else {
        document.getElementById('invoiceClientName').value = '';
        document.getElementById('invoiceClientBulstat').value = '';
        document.getElementById('clientAddress').value = '';
      }
    }

    function addInvoiceItem() {
      const itemsTable = document.getElementById('invoiceItems');
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
        <td><input type="text" class="form-control" placeholder="Описание на артикула"></td>
        <td><input type="number" class="form-control" value="1" min="1" onchange="calculateItemTotal(this)"></td>
        <td><input type="number" class="form-control" value="0.00" step="0.01" onchange="calculateItemTotal(this)"></td>
        <td>
          <select class="form-control" onchange="calculateItemTotal(this)">
            <option value="20">20%</option>
            <option value="9">9%</option>
            <option value="0">0%</option>
          </select>
        </td>
        <td class="item-total">0.00 лв</td>
        <td>
          <button class="btn btn-danger" onclick="removeInvoiceItem(this)">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      `;
      itemsTable.appendChild(newRow);
    }

    function removeInvoiceItem(button) {
      const row = button.closest('tr');
      row.remove();
      calculateInvoiceTotals();
    }

    function calculateItemTotal(input) {
      const row = input.closest('tr');
      const quantity = parseFloat(row.querySelector('td:nth-child(2) input').value) || 0;
      const price = parseFloat(row.querySelector('td:nth-child(3) input').value) || 0;
      const vatRate = parseFloat(row.querySelector('td:nth-child(4) select').value) || 0;
      
      const subtotal = quantity * price;
      const vatAmount = subtotal * (vatRate / 100);
      const total = subtotal + vatAmount;
      
      row.querySelector('.item-total').textContent = total.toFixed(2) + ' лв';
      
      calculateInvoiceTotals();
    }

    function calculateInvoiceTotals() {
      let subtotal = 0;
      let vatTotal = 0;
      
      const rows = document.querySelectorAll('#invoiceItems tr');
      rows.forEach(row => {
        const quantity = parseFloat(row.querySelector('td:nth-child(2) input').value) || 0;
        const price = parseFloat(row.querySelector('td:nth-child(3) input').value) || 0;
        const vatRate = parseFloat(row.querySelector('td:nth-child(4) select').value) || 0;
        
        const itemSubtotal = quantity * price;
        const itemVat = itemSubtotal * (vatRate / 100);
        
        subtotal += itemSubtotal;
        vatTotal += itemVat;
      });
      
      const total = subtotal + vatTotal;
      
      document.getElementById('subtotalAmount').textContent = subtotal.toFixed(2) + ' лв';
      document.getElementById('vatAmount').textContent = vatTotal.toFixed(2) + ' лв';
      document.getElementById('totalAmount').textContent = total.toFixed(2) + ' лв';
    }

    function saveInvoice() {
      const invoiceNumber = document.getElementById('invoiceNumber').value;
      const clientName = document.getElementById('invoiceClientName').value;
      
      if (!invoiceNumber || !clientName) {
        showNotification('Моля, попълнете номер на фактура и изберете клиент', 'error');
        return;
      }
      
      // Simulate saving
      showNotification(`Фактура ${invoiceNumber} е запазена успешно`, 'success');
      
      // Reset form
      document.getElementById('invoiceNumber').value = '';
      document.getElementById('clientSelect').selectedIndex = 0;
      updateClientDetails();
      document.getElementById('invoiceItems').innerHTML = `
        <tr>
          <td><input type="text" class="form-control" placeholder="Описание на артикула"></td>
          <td><input type="number" class="form-control" value="1" min="1" onchange="calculateItemTotal(this)"></td>
          <td><input type="number" class="form-control" value="0.00" step="0.01" onchange="calculateItemTotal(this)"></td>
          <td>
            <select class="form-control" onchange="calculateItemTotal(this)">
              <option value="20">20%</option>
              <option value="9">9%</option>
              <option value="0">0%</option>
            </select>
          </td>
          <td class="item-total">0.00 лв</td>
          <td>
            <button class="btn btn-danger" onclick="removeInvoiceItem(this)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
      calculateInvoiceTotals();
    }

    // File upload functionality
    document.addEventListener('DOMContentLoaded', function() {
      updateStatistics();
      
      const navLinks = document.querySelectorAll('.nav-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const pageId = this.getAttribute('data-page');
          showPage(pageId);
        });
      });

      // File upload functionality
      const uploadArea = document.getElementById('uploadArea');
      const fileInput = document.getElementById('fileInput');

      uploadArea.addEventListener('click', () => {
        fileInput.click();
      });

      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
      });

      uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles(files);
      });

      fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
      });

      function handleFiles(files) {
        if (files.length > 0) {
          const fileList = document.getElementById('fileList');
          Array.from(files).forEach((file, index) => {
            const fileId = Date.now() + index;
            const newFile = document.createElement('div');
            newFile.className = 'file-item';
            newFile.innerHTML = `
              <div class="file-info">
                <div class="file-icon">
                  <i class="fas fa-file-pdf"></i>
                </div>
                <div class="file-details">
                  <h4>${file.name}</h4>
                  <p>${(file.size / 1024 / 1024).toFixed(1)} MB • Току-що качен</p>
                </div>
              </div>
              <div class="file-actions">
                <span class="file-status status-pending">Чака обработка</span>
                <button class="btn btn-success" style="padding: 5px 10px;" onclick="startProcessing('${fileId}')">
                  <i class="fas fa-play"></i>
                </button>
              </div>
            `;
            fileList.appendChild(newFile);
          });
          
          appState.pendingInvoices += files.length;
          updateStatistics();
          showNotification(`Качени са ${files.length} файл(а) за обработка!`, 'success');
        }
      }

      // Search functionality
      const searchInput = document.getElementById('searchInput');
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          showNotification(`Търсене за: ${this.value}`, 'info');
          this.value = '';
        }
      });

      // Initialize Charts
      const invoiceCtx = document.getElementById('invoiceChart').getContext('2d');
      const invoiceChart = new Chart(invoiceCtx, {
        type: 'line',
        data: {
          labels: ['1 Nov', '5 Nov', '10 Nov', '15 Nov', '20 Nov', '25 Nov', '30 Nov'],
          datasets: [
            {
              label: 'Обработени фактури',
              data: [32, 45, 28, 51, 42, 65, 58],
              borderColor: '#146DAF',
              backgroundColor: 'rgba(20, 109, 175, 0.1)',
              tension: 0.3,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      const reportsCtx = document.getElementById('reportsChart').getContext('2d');
      const reportsChart = new Chart(reportsCtx, {
        type: 'bar',
        data: {
          labels: ['Юни', 'Юли', 'Авг', 'Сеп', 'Окт', 'Ное'],
          datasets: [
            {
              label: 'Приходи',
              data: [12500, 14200, 13800, 15600, 16800, 17500],
              backgroundColor: '#146DAF',
            },
            {
              label: 'Разходи',
              data: [9800, 11200, 10500, 11800, 12400, 13200],
              backgroundColor: '#769F86',
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            }
          }
        }
      });
    });
  </script>
</body>
</html>