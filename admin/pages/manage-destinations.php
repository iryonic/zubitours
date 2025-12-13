<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Destination Management - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #93c5fd;
            --secondary-color: #64748b;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --sidebar-width: 280px;
            --header-height: 80px;
            --card-radius: 16px;
            --transition: all 0.3s ease;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --border-color: #e2e8f0;
        }

        .dark-mode {
            --card-bg: #1f2937;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --border-color: #374151;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            color: var(--text-primary);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: var(--shadow);
            border-right: 1px solid var(--border-color);
        }
           .sidebar-menu::-webkit-scrollbar {
            width: 8px;
            background: transparent;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            height: var(--header-height);
        }

        .sidebar-logo {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            transition: var(--transition);
        }

        .sidebar-logo i {
            font-size: 1.5rem;
            color: white;
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-menu {
            padding: 24px 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-section {
            margin-bottom: 32px;
        }

        .menu-label {
            padding: 0 24px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 16px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
            margin: 4px 0;
            border-radius: 0 12px 12px 0;
        }

        .menu-item:hover,
        .menu-item.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            margin-right: 16px;
            font-size: 1.3rem;
            transition: var(--transition);
        }

        .menu-item.active i,
        .menu-item:hover i {
            transform: scale(1.1);
        }

        .menu-item span {
            flex: 1;
            font-weight: 500;
        }

        .menu-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            background: var(--bg-secondary);
        }

        .admin-profile {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .admin-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: 600;
            color: white;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .admin-avatar:hover {
            transform: rotate(10deg);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .admin-info {
            flex: 1;
        }

        .admin-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .admin-role {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: var(--transition);
        }

        .header {
            height: var(--header-height);
            background: var(--bg-secondary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: var(--transition);
        }

        .page-title {
            font-size: 1.7rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            padding: 12px 16px 12px 48px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 280px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            width: 320px;
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .header-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            position: relative;
            cursor: pointer;
            transition: var(--transition);
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        .header-icon:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
            border-color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--error-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            border: 2px solid var(--bg-secondary);
        }

        .theme-toggle {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .content {
            padding: 32px;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-light);
        }

        /* Filters Section */
        .filters-section {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .filter-select,
        .filter-input {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Destinations Grid */
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .destination-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .destination-card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .card-content {
            padding: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .card-description {
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--primary-color);
        }

        .card-actions {
            display: flex;
            gap: 12px;
        }

        .card-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .card-btn-edit {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .card-btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .card-btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }

        .card-btn-delete:hover {
            background: var(--error-color);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
        }

        .pagination-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .pagination-btn:hover,
        .pagination-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.disabled:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-color: var(--border-color);
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
            padding: 20px;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--error-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-input,
        .form-textarea,
        .form-select {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .search-input {
                width: 200px;
            }

            .search-input:focus {
                width: 240px;
            }
        }

        @media (max-width: 992px) {
            :root {
                --sidebar-width: 80px;
            }

            .sidebar-title,
            .menu-item span,
            .menu-label,
            .admin-info,
            .menu-badge {
                display: none;
            }

            .sidebar-header {
                justify-content: center;
                padding: 20px;
            }

            .sidebar-logo {
                margin-right: 0;
            }

            .menu-item {
                justify-content: center;
                padding: 16px;
                border-left: none;
                border-radius: 12px;
                margin: 6px 12px;
            }

            .menu-item i {
                margin-right: 0;
                font-size: 1.5rem;
            }

            .sidebar-footer {
                justify-content: center;
                padding: 16px;
            }

            .admin-avatar {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .destinations-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .destinations-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 0 20px;
            }

            .search-box {
                display: none;
            }

            .content {
                padding: 20px;
            }

            .header-actions {
                gap: 12px;
            }

            .header-icon {
                width: 40px;
                height: 40px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .btn {
                flex: 1;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .card-actions {
                flex-direction: column;
            }

            .modal-content {
                margin: 0;
                border-radius: 0;
                height: 100%;
                overflow-y: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include '../includes/header.php'; ?>

        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="#">Dashboard</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Destinations</span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary" id="add-destination-btn">
                        <i class="ri-add-line"></i>
                        Add Destination
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <div class="filters-title">Filter Destinations</div>
                    <div>
                        <button class="btn btn-secondary" id="apply-filters">
                            <i class="ri-filter-line"></i>
                            Apply Filters
                        </button>
                        <button class="btn btn-secondary" id="reset-filters" style="margin-left:8px;">
                            <i class="ri-refresh-line"></i>
                            Reset Filters
                        </button>
                    </div>
                </div>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Region</label>
                        <select class="filter-select" id="filter-region">
                            <option value="">All Regions</option>
                            <option value="kashmir">Kashmir</option>
                            <option value="ladakh">Ladakh</option>
                            <option value="jammu">Jammu</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select class="filter-select" id="filter-category">
                            <option value="">All Categories</option>
                            <option value="lake">Lake</option>
                            <option value="mountain">Mountain</option>
                            <option value="valley">Valley</option>
                            <option value="religious">Religious</option>
                            <option value="adventure">Adventure</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="filter-select" id="filter-status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" class="filter-input" id="filter-search" placeholder="Enter keyword...">
                    </div>
                </div>
            </div>
            <!-- cards secton -->
            <?php
            include '../includes/connection.php';

            $sql = "SELECT * FROM destinations ORDER BY id DESC";
            $result = $conn->query($sql);
            ?>

            <!-- Destinations Grid -->
            <div class="destinations-grid" id="destinations-grid">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Ensure safe values for data attributes
                        $data_region = htmlspecialchars(strtolower($row['region']));
                        $data_category = htmlspecialchars(strtolower($row['category']));
                        $data_status = htmlspecialchars(strtolower($row['status']));
                        $data_location = htmlspecialchars(strtolower($row['location']));
                        $data_name = htmlspecialchars(strtolower($row['name']));
                ?>
                        <div class="destination-card"
                             data-id="<?php echo htmlspecialchars($row['id']); ?>"
                             data-region="<?php echo $data_region; ?>"
                             data-category="<?php echo $data_category; ?>"
                             data-status="<?php echo $data_status; ?>"
                             data-location="<?php echo $data_location; ?>"
                             data-name="<?php echo $data_name; ?>">
                            <div class="card-image">
                                <input type="hidden" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <img src="../upload/destinations/<?php echo htmlspecialchars($row['image']); ?>"
                                    alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <span class="card-badge"><?php echo htmlspecialchars($row['status']); ?></span>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="card-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="card-meta">
                                    <div class="meta-item">
                                        <i class="ri-map-pin-line"></i>
                                        <span><?php echo htmlspecialchars($row['location']); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="ri-star-line"></i>
                                        <span><?php echo htmlspecialchars($row['rating']); ?></span>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="card-btn card-btn-edit" data-id="<?php echo $row['id']; ?>">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button class="card-btn card-btn-delete delete-btn" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name']); ?>">
                                        <i class="ri-delete-bin-line"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='color:gray;'>No destinations found.</p>";
                }
                ?>
            </div>
            <!-- cards secton -->

            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn disabled">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Add Destination Modal -->
    <div class="modal" id="destination-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Destination</h3>
                <button class="modal-close" id="modal-close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <form class="modal-form" method="post" action="../logic/destinationDetails.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="hidden" name="destination_id">
                            <label class="form-label">Destination Name</label>
                            <input type="text" class="form-input" placeholder="Enter destination name" name="destination_name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Region</label>
                            <select class="form-select" name="region" required>
                                <option value="">Select Region</option>
                                <option value="kashmir">Kashmir</option>
                                <option value="ladakh">Ladakh</option>
                                <option value="jammu">Jammu</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" placeholder="Enter destination description" name="destination_description" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">Select Category</option>
                                <option value="lake">Lake</option>
                                <option value="mountain">Mountain</option>
                                <option value="valley">Valley</option>
                                <option value="religious">Religious</option>
                                <option value="adventure">Adventure</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-input" placeholder="Enter location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rating</label>
                            <input type="number" name="rating" class="form-input" placeholder="0.0" step="0.1" min="0" max="5" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Featured Image</label>
                        <input type="file" class="form-input" name="image" accept="image/*" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="modal-cancel">
                            Cancel
                        </button>
                        <input type="submit" class="btn btn-primary" name='submit' value="Save Destination">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="delete-modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Delete</h3>
                <button class="modal-close" id="delete-modal-close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px 0;">
                    <i class="ri-alert-line" style="font-size: 3rem; color: var(--error-color); margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px; color: var(--text-primary);">Are you sure?</h3>
                    <p style="color: var(--text-secondary);" id="delete-message">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="delete-cancel">
                    Cancel
                </button>
                <a href="#" class="btn btn-primary" id="delete-confirm" style="background: var(--error-color);">
                    <i class="ri-delete-bin-line"></i>
                    Confirm Delete
                </a>
            </div>
        </div>
    </div>

    <!-- error message modle -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="modal" id="message-modal" style="display: flex;">
            <div class="modal-content" style="max-width: 400px;">
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo ucfirst($_SESSION['message']['type']); ?></h3>
                    <button class="modal-close" id="message-modal-close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="text-align: center; padding: 20px 0;">
                        <?php if ($_SESSION['message']['type'] === 'success'): ?>
                            <i class="ri-checkbox-circle-line" style="font-size: 3rem; color: var(--success-color); margin-bottom: 16px;"></i>
                        <?php else: ?>
                            <i class="ri-error-warning-line" style="font-size: 3rem; color: var(--error-color); margin-bottom: 16px;"></i>
                        <?php endif; ?>
                        <p style="color: var(--text-primary);">
                            <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="message-confirm">
                        OK
                    </button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Message modal functionality
            const messageModal = document.getElementById('message-modal');
            if (messageModal) {
                const messageClose = document.getElementById('message-modal-close');
                const messageConfirm = document.getElementById('message-confirm');

                function closeMessageModal() {
                    messageModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }

                if (messageClose) messageClose.addEventListener('click', closeMessageModal);
                if (messageConfirm) messageConfirm.addEventListener('click', closeMessageModal);

                messageModal.addEventListener('click', function(e) {
                    if (e.target === messageModal) {
                        closeMessageModal();
                    }
                });

                // Auto close after 5 seconds
                setTimeout(closeMessageModal, 5000);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark/Light mode toggle
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = themeToggle ? themeToggle.querySelector('i') : null;

            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');

                    if (document.body.classList.contains('dark-mode')) {
                        themeIcon.classList.remove('ri-moon-line');
                        themeIcon.classList.add('ri-sun-line');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        themeIcon.classList.remove('ri-sun-line');
                        themeIcon.classList.add('ri-moon-line');
                        localStorage.setItem('theme', 'light');
                    }
                });

                if (localStorage.getItem('theme') === 'dark') {
                    document.body.classList.add('dark-mode');
                    themeIcon.classList.remove('ri-moon-line');
                    themeIcon.classList.add('ri-sun-line');
                }
            }

            // Modal functionality
            const modal = document.getElementById('destination-modal');
            const addBtn = document.getElementById('add-destination-btn');
            const closeBtn = document.getElementById('modal-close');
            const cancelBtn = document.getElementById('modal-cancel');

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    if (modal) {
                        // reset form for add flow
                        const form = modal.querySelector('form');
                        if (form) form.reset();
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                        document.querySelector('.modal-title').textContent = 'Add Destination';
                    }
                });
            }

            function closeModal() {
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            }

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            // Card actions (edit open same modal, you'll populate fields when implementing edit endpoint)
            const editButtons = document.querySelectorAll('.card-btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (modal) {
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                        document.querySelector('.modal-title').textContent = 'Edit Destination';
                        // populate form when you implement edit endpoint / ajax
                    }
                });
            });

            // Delete stuff (existing)
            const deleteModal = document.getElementById('delete-modal');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteConfirm = document.getElementById('delete-confirm');
            const deleteCancel = document.getElementById('delete-cancel');
            const deleteClose = document.getElementById('delete-modal-close');
            const deleteMessage = document.getElementById('delete-message');

            let currentDeleteId = null;
            let currentDeleteCard = null;

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const destinationId = this.getAttribute('data-id');
                    const destinationName = this.getAttribute('data-name');
                    currentDeleteCard = this.closest('.destination-card');
                    currentDeleteId = destinationId;

                    if (deleteMessage) {
                        deleteMessage.textContent = `Are you sure you want to delete "${destinationName}"? This action cannot be undone.`;
                    }
                    if (deleteModal) {
                        deleteModal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            function closeDeleteModal() {
                if (deleteModal) {
                    deleteModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
                currentDeleteId = null;
                currentDeleteCard = null;
            }

            if (deleteCancel) deleteCancel.addEventListener('click', closeDeleteModal);
            if (deleteClose) deleteClose.addEventListener('click', closeDeleteModal);

            if (deleteConfirm) {
                deleteConfirm.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!currentDeleteId) return;
                    window.location.href = `../logic/deleteDestination.php?id=${currentDeleteId}`;
                });
            }

            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }

            // FILTER FUNCTIONALITY
            const filterRegion = document.getElementById('filter-region');
            const filterCategory = document.getElementById('filter-category');
            const filterStatus = document.getElementById('filter-status');
            const filterSearch = document.getElementById('filter-search');
            const applyFiltersBtn = document.getElementById('apply-filters');
            const resetFiltersBtn = document.getElementById('reset-filters');
            const cardsContainer = document.getElementById('destinations-grid');

            function normalize(value) {
                return (value || '').toString().trim().toLowerCase();
            }

            function cardMatchesFilters(cardEl, region, category, status, searchTerm) {
                const cardRegion = normalize(cardEl.dataset.region);
                const cardCategory = normalize(cardEl.dataset.category);
                const cardStatus = normalize(cardEl.dataset.status);
                const cardName = normalize(cardEl.dataset.name);
                const cardLocation = normalize(cardEl.dataset.location);
                const cardDescription = normalize(cardEl.querySelector('.card-description') ? cardEl.querySelector('.card-description').textContent : '');

                if (region && region !== cardRegion) return false;
                if (category && category !== cardCategory) return false;
                if (status && status !== cardStatus) return false;

                if (searchTerm) {
                    // match against name, description, location
                    const term = searchTerm.toLowerCase();
                    if (!(cardName.includes(term) || cardDescription.includes(term) || cardLocation.includes(term))) {
                        return false;
                    }
                }

                return true;
            }

            function applyFilters() {
                const region = normalize(filterRegion ? filterRegion.value : '');
                const category = normalize(filterCategory ? filterCategory.value : '');
                const status = normalize(filterStatus ? filterStatus.value : '');
                const searchTerm = normalize(filterSearch ? filterSearch.value : '');

                const cards = cardsContainer ? cardsContainer.querySelectorAll('.destination-card') : [];
                cards.forEach(card => {
                    if (cardMatchesFilters(card, region, category, status, searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Apply on change / input
            if (filterRegion) filterRegion.addEventListener('change', applyFilters);
            if (filterCategory) filterCategory.addEventListener('change', applyFilters);
            if (filterStatus) filterStatus.addEventListener('change', applyFilters);
            if (filterSearch) filterSearch.addEventListener('input', function() {
                // debounce basic
                clearTimeout(this._filterTimeout);
                this._filterTimeout = setTimeout(applyFilters, 200);
            });

            if (applyFiltersBtn) applyFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                applyFilters();
            });

            if (resetFiltersBtn) resetFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (filterRegion) filterRegion.value = '';
                if (filterCategory) filterCategory.value = '';
                if (filterStatus) filterStatus.value = '';
                if (filterSearch) filterSearch.value = '';
                applyFilters();
            });
        });
    </script>
</body>
</html>