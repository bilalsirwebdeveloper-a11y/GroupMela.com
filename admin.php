<?php
include 'config.php';

// Simple Admin Login (for demo purposes)
// In production, use proper authentication
session_start();
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simple hardcoded credentials (change this!)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $is_logged_in = true;
    } else {
        $login_error = 'Invalid credentials!';
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// If not logged in, show login form
if (!$is_logged_in) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GroupMela</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .login-container h1 {
            text-align: center;
            color: var(--primary-dark);
            margin-bottom: 30px;
        }
        .login-container .logo {
            text-align: center;
            font-size: 2.5rem;
            display: block;
            margin-bottom: 20px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
        }
        .login-form button {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body style="background: #f0f2f5;">
    <div class="login-container">
        <span class="logo"><i class="fab fa-whatsapp"></i> GroupMela</span>
        <h1>Admin Login</h1>
        
        <?php if(isset($login_error)): ?>
            <div class="error"><?php echo $login_error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p style="text-align: center; margin-top: 20px; color: #666;">Default: admin / admin123</p>
    </div>
</body>
</html>
<?php
    exit;
}

// Handle Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($action == 'approve' && $id) {
        $conn->query("UPDATE groups SET status = 'approved' WHERE id = $id");
    }
    elseif ($action == 'reject' && $id) {
        $conn->query("UPDATE groups SET status = 'rejected' WHERE id = $id");
    }
    elseif ($action == 'delete' && $id) {
        $conn->query("DELETE FROM groups WHERE id = $id");
    }
    elseif ($action == 'feature' && $id) {
        $conn->query("UPDATE groups SET featured = NOT featured WHERE id = $id");
    }
    elseif ($action == 'delete_report' && $id) {
        $conn->query("DELETE FROM reports WHERE id = $id");
    }
    
    // Redirect to remove action from URL
    header('Location: admin.php');
    exit;
}

// Get statistics
$total_groups = $conn->query("SELECT COUNT(*) as total FROM groups")->fetch_assoc()['total'];
$pending_groups = $conn->query("SELECT COUNT(*) as total FROM groups WHERE status = 'pending'")->fetch_assoc()['total'];
$approved_groups = $conn->query("SELECT COUNT(*) as total FROM groups WHERE status = 'approved'")->fetch_assoc()['total'];
$total_reports = $conn->query("SELECT COUNT(*) as total FROM reports")->fetch_assoc()['total'];
$total_categories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$total_views = $conn->query("SELECT SUM(views) as total FROM groups")->fetch_assoc()['total'];

// Get current tab
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GroupMela</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Admin Panel Specific Styles */
        :root {
            --sidebar-width: 260px;
            --header-height: 70px;
            --admin-primary: #2c3e50;
            --admin-secondary: #34495e;
            --admin-success: #27ae60;
            --admin-danger: #e74c3c;
            --admin-warning: #f39c12;
            --admin-info: #3498db;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            display: flex;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--admin-primary);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
        }

        .sidebar-header h2 i {
            color: #25D366;
            margin-right: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #25D366;
        }

        .menu-item i {
            width: 25px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .menu-item .badge {
            margin-left: auto;
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f5f6fa;
        }

        /* Top Header */
        .admin-header {
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-search {
            display: flex;
            align-items: center;
            background: #f5f6fa;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .header-search i {
            color: #7f8c8d;
            margin-right: 10px;
        }

        .header-search input {
            border: none;
            background: transparent;
            outline: none;
            width: 250px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
        }

        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            font-size: 0.7rem;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .admin-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        /* Content Area */
        .admin-content {
            padding: 30px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-info h3 {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .stat-info .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--admin-primary);
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h2 {
            font-size: 1.3rem;
            color: #2c3e50;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--admin-primary);
            color: white;
        }

        .btn-success {
            background: var(--admin-success);
            color: white;
        }

        .btn-danger {
            background: var(--admin-danger);
            color: white;
        }

        .btn-warning {
            background: var(--admin-warning);
            color: white;
        }

        .btn-info {
            background: var(--admin-info);
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            padding: 15px 10px;
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
        }

        table td {
            padding: 15px 10px;
            border-bottom: 1px solid #ecf0f1;
        }

        table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background: var(--admin-primary);
            color: white;
            border-color: var(--admin-primary);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            width: 500px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }
            .sidebar-header h2 span, .menu-item span {
                display: none;
            }
            .admin-main {
                margin-left: 70px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2><i class="fab fa-whatsapp"></i> <span>GroupMela</span></h2>
        </div>
        
        <div class="sidebar-menu">
            <a href="?tab=dashboard" class="menu-item <?php echo $tab == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
            <a href="?tab=pending" class="menu-item <?php echo $tab == 'pending' ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i> <span>Pending Groups</span>
                <?php if($pending_groups > 0): ?>
                    <span class="badge"><?php echo $pending_groups; ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=approved" class="menu-item <?php echo $tab == 'approved' ? 'active' : ''; ?>">
                <i class="fas fa-check-circle"></i> <span>Approved Groups</span>
            </a>
            <a href="?tab=reports" class="menu-item <?php echo $tab == 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-exclamation-triangle"></i> <span>Reports</span>
                <?php if($total_reports > 0): ?>
                    <span class="badge"><?php echo $total_reports; ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=categories" class="menu-item <?php echo $tab == 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> <span>Categories</span>
            </a>
            <a href="?tab=settings" class="menu-item <?php echo $tab == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
            <a href="index.php" target="_blank" class="menu-item">
                <i class="fas fa-external-link-alt"></i> <span>View Site</span>
            </a>
            <a href="?logout=1" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Header -->
        <div class="admin-header">
            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search groups...">
            </div>
            
            <div class="header-actions">
                <div class="notification-badge">
                    <i class="fas fa-bell"></i>
                    <?php if($pending_groups > 0): ?>
                        <span class="badge"><?php echo $pending_groups; ?></span>
                    <?php endif; ?>
                </div>
                <div class="admin-profile">
                    <i class="fas fa-user-circle" style="font-size: 2rem;"></i>
                    <span>Admin</span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="admin-content">
            <?php if($tab == 'dashboard'): ?>
                <!-- Dashboard -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Groups</h3>
                            <span class="stat-number"><?php echo $total_groups; ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Pending Groups</h3>
                            <span class="stat-number"><?php echo $pending_groups; ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Approved Groups</h3>
                            <span class="stat-number"><?php echo $approved_groups; ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Views</h3>
                            <span class="stat-number"><?php echo number_format($total_views); ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Categories</h3>
                            <span class="stat-number"><?php echo $total_categories; ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-th-large"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Reports</h3>
                            <span class="stat-number"><?php echo $total_reports; ?></span>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Pending Groups -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Recent Pending Groups</h2>
                        <a href="?tab=pending" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Group Name</th>
                                <th>Category</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_pending = $conn->query("
                                SELECT g.*, c.name as cat_name 
                                FROM groups g 
                                LEFT JOIN categories c ON g.category_id = c.id 
                                WHERE g.status = 'pending' 
                                ORDER BY g.created_at DESC 
                                LIMIT 5
                            ");
                            
                            if($recent_pending->num_rows == 0):
                            ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No pending groups</td>
                            </tr>
                            <?php else: ?>
                                <?php while($group = $recent_pending->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $group['id']; ?></td>
                                    <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                                    <td><?php echo $group['cat_name']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($group['created_at'])); ?></td>
                                    <td class="action-buttons">
                                        <a href="?action=approve&id=<?php echo $group['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this group?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="?action=reject&id=<?php echo $group['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this group?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <a href="#" class="btn btn-info btn-sm" onclick="viewGroup(<?php echo $group['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Recent Reports -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Recent Reports</h2>
                        <a href="?tab=reports" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Reason</th>
                                <th>Reported</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_reports = $conn->query("
                                SELECT r.*, g.group_name 
                                FROM reports r 
                                JOIN groups g ON r.group_id = g.id 
                                ORDER BY r.created_at DESC 
                                LIMIT 5
                            ");
                            
                            if($recent_reports->num_rows == 0):
                            ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No reports</td>
                            </tr>
                            <?php else: ?>
                                <?php while($report = $recent_reports->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report['group_name']); ?></td>
                                    <td><?php echo $report['reason']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($report['created_at'])); ?></td>
                                    <td>
                                        <a href="?action=delete_report&id=<?php echo $report['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this report?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif($tab == 'pending'): ?>
                <!-- Pending Groups -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Pending Groups (<?php echo $pending_groups; ?>)</h2>
                        <div class="table-actions">
                            <button class="btn btn-success" onclick="approveAll()">Approve All</button>
                        </div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Group Name</th>
                                <th>WhatsApp Link</th>
                                <th>Category</th>
                                <th>Email</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                            $per_page = 20;
                            $offset = ($page - 1) * $per_page;
                            
                            $pending = $conn->query("
                                SELECT g.*, c.name as cat_name 
                                FROM groups g 
                                LEFT JOIN categories c ON g.category_id = c.id 
                                WHERE g.status = 'pending' 
                                ORDER BY g.created_at DESC 
                                LIMIT $offset, $per_page
                            ");
                            
                            while($group = $pending->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo $group['id']; ?></td>
                                <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                                <td>
                                    <a href="<?php echo $group['whatsapp_link']; ?>" target="_blank" style="color: #25D366;">
                                        <?php echo substr($group['whatsapp_link'], 0, 30); ?>...
                                    </a>
                                </td>
                                <td><?php echo $group['cat_name']; ?></td>
                                <td><?php echo $group['submitter_email']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($group['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=approve&id=<?php echo $group['id']; ?>" class="btn btn-success btn-sm" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="?action=reject&id=<?php echo $group['id']; ?>" class="btn btn-danger btn-sm" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <a href="#" class="btn btn-info btn-sm" title="View" onclick="viewGroup(<?php echo $group['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $group['id']; ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Permanently delete this group?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <?php
                    $total_pages = ceil($pending_groups / $per_page);
                    if($total_pages > 1):
                    ?>
                    <div class="pagination">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?tab=pending&p=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>

            <?php elseif($tab == 'approved'): ?>
                <!-- Approved Groups -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Approved Groups (<?php echo $approved_groups; ?>)</h2>
                        <div class="table-actions">
                            <input type="text" id="searchGroups" placeholder="Search groups..." style="padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <table id="groupsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Group Name</th>
                                <th>Category</th>
                                <th>Views</th>
                                <th>Featured</th>
                                <th>Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $approved = $conn->query("
                                SELECT g.*, c.name as cat_name 
                                FROM groups g 
                                LEFT JOIN categories c ON g.category_id = c.id 
                                WHERE g.status = 'approved' 
                                ORDER BY g.featured DESC, g.views DESC 
                                LIMIT 50
                            ");
                            
                            while($group = $approved->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo $group['id']; ?></td>
                                <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                                <td><?php echo $group['cat_name']; ?></td>
                                <td><?php echo number_format($group['views']); ?></td>
                                <td>
                                    <?php if($group['featured']): ?>
                                        <span class="status-badge" style="background: gold; color: #333;">Featured</span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: #eee;">Regular</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($group['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=feature&id=<?php echo $group['id']; ?>" class="btn btn-warning btn-sm" title="<?php echo $group['featured'] ? 'Remove Featured' : 'Make Featured'; ?>">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="#" class="btn btn-info btn-sm" title="Edit" onclick="editGroup(<?php echo $group['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $group['id']; ?>" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Delete this group?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif($tab == 'reports'): ?>
                <!-- Reports -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Reported Groups (<?php echo $total_reports; ?>)</h2>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Report Reason</th>
                                <th>Reported By IP</th>
                                <th>Reported On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $reports = $conn->query("
                                SELECT r.*, g.group_name, g.whatsapp_link 
                                FROM reports r 
                                JOIN groups g ON r.group_id = g.id 
                                ORDER BY r.created_at DESC
                            ");
                            
                            while($report = $reports->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($report['group_name']); ?></strong><br>
                                    <small><a href="<?php echo $report['whatsapp_link']; ?>" target="_blank">View Link</a></small>
                                </td>
                                <td><?php echo $report['reason']; ?></td>
                                <td><?php echo $report['ip_address']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($report['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=delete_report&id=<?php echo $report['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Mark as resolved?')">
                                        <i class="fas fa-check"></i> Resolve
                                    </a>
                                    <a href="?action=delete&id=<?php echo $report['group_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this group?')">
                                        <i class="fas fa-trash"></i> Delete Group
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif($tab == 'categories'): ?>
                <!-- Categories Management -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Categories</h2>
                        <button class="btn btn-primary" onclick="showAddCategory()">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Icon</th>
                                <th>Category Name</th>
                                <th>Groups Count</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $categories = $conn->query("
                                SELECT c.*, COUNT(g.id) as group_count 
                                FROM categories c 
                                LEFT JOIN groups g ON c.id = g.category_id AND g.status = 'approved'
                                GROUP BY c.id
                                ORDER BY c.name
                            ");
                            
                            while($cat = $categories->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo $cat['id']; ?></td>
                                <td style="font-size: 1.5rem;"><?php echo $cat['icon']; ?></td>
                                <td><?php echo $cat['name']; ?></td>
                                <td><?php echo $cat['group_count']; ?></td>
                                <td><?php echo date('d M Y', strtotime($cat['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-warning btn-sm" onclick="editCategory(<?php echo $cat['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteCategory(<?php echo $cat['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif($tab == 'settings'): ?>
                <!-- Settings -->
                <div class="table-container">
                    <h2>Site Settings</h2>
                    
                    <form method="POST" style="max-width: 600px;">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Site Description</label>
                            <textarea name="site_description" class="form-control" rows="3"><?php echo $settings['site_description']; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Admin Email</label>
                            <input type="email" name="admin_email" value="<?php echo $settings['admin_email']; ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Groups Per Page</label>
                            <input type="number" name="per_page" value="20" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label>Auto-Approve Groups?</label>
                            <select name="auto_approve" class="form-control">
                                <option value="0">No, require admin approval</option>
                                <option value="1">Yes, auto-approve</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Group Modal -->
    <div id="viewGroupModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Group Details</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div id="groupDetails">
                <!-- Load via AJAX -->
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Category</h3>
                <span class="close" onclick="closeModal('addCategoryModal')">&times;</span>
            </div>
            <form method="POST" action="admin_categories.php">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="cat_name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Icon (Emoji)</label>
                    <input type="text" name="cat_icon" placeholder="📚" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchGroups')?.addEventListener('keyup', function() {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll('#groupsTable tbody tr');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        });

        // View Group
        function viewGroup(id) {
            fetch('get_group.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('groupDetails').innerHTML = data;
                    document.getElementById('viewGroupModal').style.display = 'block';
                });
        }

        // Edit Group
        function editGroup(id) {
            window.location.href = 'edit_group.php?id=' + id;
        }

        // Show Add Category Modal
        function showAddCategory() {
            document.getElementById('addCategoryModal').style.display = 'block';
        }

        // Edit Category
        function editCategory(id) {
            window.location.href = 'edit_category.php?id=' + id;
        }

        // Delete Category
        function deleteCategory(id) {
            if(confirm('Are you sure? This will affect all groups in this category.')) {
                window.location.href = '?action=delete_category&id=' + id;
            }
        }

        // Approve All
        function approveAll() {
            if(confirm('Approve all pending groups?')) {
                window.location.href = '?action=approve_all';
            }
        }

        // Close Modal
        function closeModal(modalId = 'viewGroupModal') {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>