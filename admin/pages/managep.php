<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Management - Zubi Tours Admin</title>
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
            --info-color: #3b82f6;
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
            --hover-overlay: rgba(37, 99, 235, 0.05);
        }

        .dark-mode {
            --card-bg: #1f2937;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --border-color: #374151;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            --hover-overlay: rgba(37, 99, 235, 0.1);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: var(--transition);
            overflow-x: hidden;
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
            width: 6px;
            background: transparent;
        }

        /* .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        } */

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            height: var(--header-height);
            background: var(--bg-secondary);
        }

        .sidebar-logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .sidebar-logo:hover {
            transform: rotate(5deg) scale(1.05);
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
            letter-spacing: -0.5px;
        }

        .sidebar-menu {
            padding: 24px 0;
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .menu-section {
            margin-bottom: 32px;
        }

        .menu-label {
            padding: 0 24px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 16px;
            letter-spacing: 1px;
            font-weight: 600;
            opacity: 0.7;
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
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            transition: var(--transition);
            z-index: -1;
        }

        .menu-item:hover::before,
        .menu-item.active::before {
            width: 100%;
        }

        .menu-item:hover,
        .menu-item.active {
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            margin-right: 16px;
            font-size: 1.3rem;
            transition: var(--transition);
            min-width: 24px;
        }

        .menu-item.active i,
        .menu-item:hover i {
            transform: scale(1.1);
        }

        .menu-item span {
            flex: 1;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .menu-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .admin-avatar:hover {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        .admin-info {
            flex: 1;
            display: block;
            min-width: 0;
        }

        .admin-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            backdrop-filter: blur(10px);
            background: rgba(var(--card-bg-rgb), 0.9);
        }

        .page-title {
            font-size: 1.7rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
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
            border: 2px solid var(--border-color);
            border-radius: 12px;
            width: 280px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            width: 320px;
            background: var(--bg-secondary);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
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
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-3px); }
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
            transform: rotate(15deg);
        }

        .content {
            padding: 32px;
            flex: 1;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.95rem;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.8rem;
            opacity: 0.5;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
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
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #0da674 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        /* Stats Overview */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            transition: var(--transition);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover::after {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: inherit;
            opacity: 0.2;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .packages-icon {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
            color: var(--primary-color);
        }

        .revenue-icon {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: var(--success-color);
        }

        .bookings-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);
            color: var(--warning-color);
        }

        .popular-icon {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.1) 100%);
            color: var(--error-color);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        .stat-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            margin-top: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            gap: 4px;
        }

        .trend-up {
            color: var(--success-color);
        }

        .trend-down {
            color: var(--error-color);
        }

        /* Enhanced Filters Section */
        .filters-section {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters-title i {
            color: var(--primary-color);
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-label i {
            font-size: 0.8rem;
        }

        .filter-select, .filter-input {
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .filter-select:hover, .filter-input:hover {
            border-color: var(--primary-light);
        }

        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: var(--bg-secondary);
        }

        /* Enhanced Packages Table */
        .table-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            overflow: auto;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1000px;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
            background: var(--bg-primary);
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--hover-overlay);
            transform: translateX(5px);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: var(--transition);
        }

        .status-badge i {
            font-size: 0.7rem;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-draft {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .action-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            margin-right: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        .action-btn i {
            font-size: 0.9rem;
        }

        .view-btn {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .view-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .edit-btn {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .edit-btn:hover {
            background: var(--success-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .delete-btn {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .delete-btn:hover {
            background: var(--error-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        /* Enhanced Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 32px;
            flex-wrap: wrap;
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
            font-weight: 500;
        }

        .pagination-btn:hover, .pagination-btn.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0 12px;
        }

        /* Enhanced Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            width: 100%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            border: 1px solid var(--border-color);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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
            position: sticky;
            top: 0;
            background: var(--card-bg);
            z-index: 10;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: var(--error-color);
            color: white;
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .form-input, .form-textarea, .form-select {
            padding: 12px 16px;
            border: 2px solid var(--border-color);
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

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: var(--bg-secondary);
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
            position: sticky;
            bottom: 0;
            background: var(--card-bg);
            z-index: 10;
        }

        /* Enhanced Tabs */
        .tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border-color);
            overflow-x: auto;
            scrollbar-width: none;
        }

        .tabs::-webkit-scrollbar {
            display: none;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-secondary);
            transition: var(--transition);
            position: relative;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab i {
            font-size: 1rem;
        }

        .tab.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .tab.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-content.active {
            display: block;
        }

        /* Enhanced Itinerary Builder */
        .itinerary-builder {
            margin-top: 20px;
        }

        .day-card {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .day-card:hover {
            border-color: var(--primary-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .day-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .day-title input {
            border: none;
            border-bottom: 2px dashed var(--primary-light);
            background: transparent;
            color: var(--text-primary);
            font-size: 1rem;
            padding: 4px;
            flex: 1;
            min-width: 200px;
        }

        .day-title input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .activity-item {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            padding: 12px;
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
            align-items: flex-start;
        }

        .activity-item:hover {
            border-color: var(--primary-light);
            transform: translateX(5px);
        }

        .activity-time {
            min-width: 100px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .activity-time input {
            border: none;
            border-bottom: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-primary);
            font-size: 0.9rem;
            width: 80px;
            padding: 2px;
        }

        .activity-time input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .activity-title input {
            border: none;
            border-bottom: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-primary);
            font-size: 0.95rem;
            flex: 1;
            padding: 2px;
        }

        .activity-title input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .activity-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .activity-description textarea {
            border: none;
            border-bottom: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-secondary);
            font-size: 0.9rem;
            width: 100%;
            resize: vertical;
            min-height: 60px;
            padding: 4px;
            font-family: inherit;
        }

        .activity-description textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .add-activity-btn {
            margin-top: 8px;
            background: var(--primary-light);
            color: var(--primary-color);
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .add-activity-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .add-day-btn {
            width: 100%;
            padding: 16px;
            background: var(--bg-primary);
            color: var(--primary-color);
            border: 2px dashed var(--primary-light);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
            transition: var(--transition);
        }

        .add-day-btn:hover {
            border-color: var(--primary-color);
            background: var(--hover-overlay);
            transform: translateY(-2px);
        }

        /* Loading State */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 3000;
            transform: translateY(100px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast-success {
            background: var(--success-color);
        }

        .toast-error {
            background: var(--error-color);
        }

        .toast-warning {
            background: var(--warning-color);
        }

        .toast-info {
            background: var(--info-color);
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
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                margin-right: 15px;
            }
        }

        @media (max-width: 992px) {
            :root {
                --sidebar-width: 80px;
            }
            
            .sidebar-title, .menu-item span, .menu-label, .admin-info, .menu-badge {
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 0 20px;
                flex-wrap: wrap;
                height: auto;
                min-height: var(--header-height);
                padding: 15px 20px;
            }
            
            .search-box {
                order: 3;
                width: 100%;
                margin-top: 15px;
            }
            
            .search-input {
                width: 100%;
            }
            
            .search-input:focus {
                width: 100%;
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
                justify-content: stretch;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                padding: 16px;
            }
            
            th, td {
                padding: 12px 16px;
            }
            
            .modal-content {
                margin: 0;
                max-height: 100vh;
                border-radius: 0;
            }
        }

        @media (max-width: 576px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
            
            .activity-item {
                flex-direction: column;
                gap: 8px;
            }
            
            .activity-time {
                min-width: auto;
                width: 100%;
            }
            
            .activity-time input {
                width: 100%;
            }
            
            .pagination {
                gap: 4px;
            }
            
            .pagination-btn {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            :root {
                --sidebar-width: 60px;
            }
            
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            
            .sidebar {
                width: 60px;
            }
            
            .sidebar-logo {
                width: 40px;
                height: 40px;
            }
            
            .menu-item {
                padding: 12px;
                margin: 4px 8px;
            }
            
            .menu-item i {
                font-size: 1.3rem;
            }
            
            .admin-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="page-title">Package Management</div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search packages..." id="search-input">
                </div>
                
                <div class="theme-toggle" id="theme-toggle">
                    <i class="ri-moon-line" id="theme-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="dashboard.html"><i class="ri-dashboard-line"></i> Dashboard</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span><i class="ri-briefcase-line"></i> Packages</span>
                </div>
                <div class="action-buttons">
                   
                    <button class="btn btn-primary" id="add-package-btn">
                        <i class="ri-add-line"></i>
                        Add Package
                    </button>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon packages-icon">
                        <i class="ri-briefcase-4-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Total Packages</p>
                        <div class="stat-trend trend-up">
                            <i class="ri-arrow-up-line"></i>
                            <span>4 new this month</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon revenue-icon">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₹12.5L</h3>
                        <p>Package Revenue</p>
                        <div class="stat-trend trend-up">
                            <i class="ri-arrow-up-line"></i>
                            <span>18% increase</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>86</h3>
                        <p>Package Bookings</p>
                        <div class="stat-trend trend-up">
                            <i class="ri-arrow-up-line"></i>
                            <span>12% increase</span>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon popular-icon">
                        <i class="ri-star-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Kashmir Explorer</h3>
                        <p>Most Popular Package</p>
                        <div class="stat-trend trend-up">
                            <i class="ri-arrow-up-line"></i>
                            <span>32 bookings</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <div class="filters-title">
                        <i class="ri-filter-line"></i>
                        Filter Packages
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-secondary" id="reset-filters">
                            <i class="ri-refresh-line"></i>
                            Reset
                        </button>
                        <button class="btn btn-primary" id="apply-filters">
                            <i class="ri-filter-line"></i>
                            Apply
                        </button>
                    </div>
                </div>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="ri-briefcase-line"></i>
                            Package Type
                        </label>
                        <select class="filter-select" id="filter-type">
                            <option value="">All Types</option>
                            <option value="cultural">Cultural</option>
                            <option value="adventure">Adventure</option>
                            <option value="luxury">Luxury</option>
                            <option value="honeymoon">Honeymoon</option>
                            <option value="family">Family</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="ri-time-line"></i>
                            Duration
                        </label>
                        <select class="filter-select" id="filter-duration">
                            <option value="">Any Duration</option>
                            <option value="3-5">3-5 Days</option>
                            <option value="6-8">6-8 Days</option>
                            <option value="9-12">9-12 Days</option>
                            <option value="12+">12+ Days</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="ri-checkbox-circle-line"></i>
                            Status
                        </label>
                        <select class="filter-select" id="filter-status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="ri-map-pin-line"></i>
                            Destination
                        </label>
                        <select class="filter-select" id="filter-destination">
                            <option value="">All Destinations</option>
                            <option value="kashmir">Kashmir</option>
                            <option value="ladakh">Ladakh</option>
                            <option value="jammu">Jammu</option>
                            <option value="leh">Leh</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Packages Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3><i class="ri-briefcase-line"></i> All Packages</h3>
                    <span id="package-count" class="pagination-info">Showing 8 of 24 packages</span>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Destinations</th>
                            <th>Price</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="packages-table-body">
                        <!-- Packages will be loaded here dynamically -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn disabled" id="prev-page">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <button class="pagination-btn active" data-page="1">1</button>
                <button class="pagination-btn" data-page="2">2</button>
                <button class="pagination-btn" data-page="3">3</button>
                <span class="pagination-info">...</span>
                <button class="pagination-btn" data-page="10">10</button>
                <button class="pagination-btn" id="next-page">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
                <span class="pagination-info" id="page-info">Page 1 of 10</span>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Package Modal -->
    <div class="modal" id="package-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ri-briefcase-line"></i>
                    <span id="modal-title">Add New Package</span>
                </h3>
                <button class="modal-close" id="modal-close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="tabs">
                    <button class="tab active" data-tab="basic">
                        <i class="ri-information-line"></i>
                        Basic Info
                    </button>
                    <button class="tab" data-tab="itinerary">
                        <i class="ri-map-2-line"></i>
                        Itinerary
                    </button>
                    <button class="tab" data-tab="pricing">
                        <i class="ri-money-dollar-circle-line"></i>
                        Pricing & Inclusions
                    </button>
                    <button class="tab" data-tab="media">
                        <i class="ri-image-line"></i>
                        Media
                    </button>
                </div>
                
                <div class="tab-content active" id="basic-tab">
                    <form class="modal-form" id="basic-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-briefcase-line"></i>
                                    Package Name
                                </label>
                                <input type="text" class="form-input" placeholder="Enter package name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-tag-line"></i>
                                    Package Type
                                </label>
                                <select class="form-select" required>
                                    <option value="">Select type</option>
                                    <option value="cultural">Cultural</option>
                                    <option value="adventure">Adventure</option>
                                    <option value="luxury">Luxury</option>
                                    <option value="honeymoon">Honeymoon</option>
                                    <option value="family">Family</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-time-line"></i>
                                    Duration (Days)
                                </label>
                                <input type="number" class="form-input" placeholder="Enter duration" min="1" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-group-line"></i>
                                    Max Group Size
                                </label>
                                <input type="number" class="form-input" placeholder="Enter group size" min="1" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-map-pin-line"></i>
                                Destinations
                            </label>
                            <select class="form-select" multiple id="destinations-select">
                                <option value="srinagar">Srinagar</option>
                                <option value="gulmarg">Gulmarg</option>
                                <option value="pahalgam">Pahalgam</option>
                                <option value="sonamarg">Sonamarg</option>
                                <option value="leh">Leh</option>
                                <option value="nubra">Nubra Valley</option>
                                <option value="pangong">Pangong Lake</option>
                            </select>
                            <small style="color: var(--text-secondary); margin-top: 6px; display: block;">Hold Ctrl/Cmd to select multiple destinations</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-file-text-line"></i>
                                Short Description
                            </label>
                            <textarea class="form-textarea" placeholder="Enter short description for listing pages" maxlength="160" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-file-text-line"></i>
                                Full Description
                            </label>
                            <textarea class="form-textarea" placeholder="Enter detailed package description" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="tab-content" id="itinerary-tab">
                    <div class="itinerary-builder">
                        <div class="day-card" data-day="1">
                            <div class="day-header">
                                <div class="day-title">
                                    <span>Day 1:</span>
                                    <input type="text" placeholder="Enter day title" value="Arrival in Srinagar">
                                </div>
                                <button type="button" class="action-btn delete-btn delete-day-btn">
                                    <i class="ri-delete-bin-line"></i>
                                    Remove
                                </button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">
                                    <i class="ri-time-line"></i>
                                    <input type="time" value="12:00">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <input type="text" placeholder="Activity title" value="Airport Pickup">
                                    </div>
                                    <div class="activity-description">
                                        <textarea placeholder="Activity description">Meet and greet at Srinagar International Airport, transfer to hotel</textarea>
                                    </div>
                                </div>
                                <button type="button" class="action-btn delete-btn delete-activity-btn">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">
                                    <i class="ri-time-line"></i>
                                    <input type="time" value="14:00">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <input type="text" placeholder="Activity title" value="Lunch">
                                    </div>
                                    <div class="activity-description">
                                        <textarea placeholder="Activity description">Welcome lunch with authentic Kashmiri cuisine</textarea>
                                    </div>
                                </div>
                                <button type="button" class="action-btn delete-btn delete-activity-btn">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <button class="add-activity-btn">
                                <i class="ri-add-line"></i> Add Activity
                            </button>
                        </div>
                        
                        <div class="day-card" data-day="2">
                            <div class="day-header">
                                <div class="day-title">
                                    <span>Day 2:</span>
                                    <input type="text" placeholder="Enter day title" value="Srinagar Sightseeing">
                                </div>
                                <button type="button" class="action-btn delete-btn delete-day-btn">
                                    <i class="ri-delete-bin-line"></i>
                                    Remove
                                </button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">
                                    <i class="ri-time-line"></i>
                                    <input type="time" value="09:00">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <input type="text" placeholder="Activity title" value="Mughal Gardens Tour">
                                    </div>
                                    <div class="activity-description">
                                        <textarea placeholder="Activity description">Visit Nishat Bagh, Shalimar Bagh, and Chashme Shahi</textarea>
                                    </div>
                                </div>
                                <button type="button" class="action-btn delete-btn delete-activity-btn">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">
                                    <i class="ri-time-line"></i>
                                    <input type="time" value="14:00">
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <input type="text" placeholder="Activity title" value="Shikara Ride">
                                    </div>
                                    <div class="activity-description">
                                        <textarea placeholder="Activity description">Evening shikara ride on Dal Lake</textarea>
                                    </div>
                                </div>
                                <button type="button" class="action-btn delete-btn delete-activity-btn">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <button class="add-activity-btn">
                                <i class="ri-add-line"></i> Add Activity
                            </button>
                        </div>
                        
                        <button class="add-day-btn">
                            <i class="ri-add-line"></i> Add Another Day
                        </button>
                    </div>
                </div>
                
                <div class="tab-content" id="pricing-tab">
                    <form class="modal-form" id="pricing-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-money-dollar-circle-line"></i>
                                    Base Price (₹)
                                </label>
                                <input type="number" class="form-input" placeholder="Enter base price" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-percent-line"></i>
                                    Discount (%)
                                </label>
                                <input type="number" class="form-input" placeholder="Enter discount percentage" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-calendar-line"></i>
                                    Seasonal Pricing
                                </label>
                                <select class="form-select" id="season-pricing">
                                    <option value="peak">Peak Season (April-June, Sept-Oct): +20%</option>
                                    <option value="shoulder">Shoulder Season (July-Aug): +10%</option>
                                    <option value="off" selected>Off Season (Nov-March): Base Price</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-child-line"></i>
                                    Child Discount (%)
                                </label>
                                <input type="number" class="form-input" value="30" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-checkbox-circle-line"></i>
                                Inclusions
                            </label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-top: 8px;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" id="inc-accommodation" checked style="width: 18px; height: 18px;">
                                    <span>Accommodation</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" id="inc-meals" checked style="width: 18px; height: 18px;">
                                    <span>All Meals</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" id="inc-transport" checked style="width: 18px; height: 18px;">
                                    <span>Transportation</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" id="inc-guide" checked style="width: 18px; height: 18px;">
                                    <span>Guide Services</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" id="inc-activities" style="width: 18px; height: 18px;">
                                    <span>Activity Fees</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-close-circle-line"></i>
                                Exclusions
                            </label>
                            <textarea class="form-textarea" placeholder="List what's not included in the package">Airfare, travel insurance, personal expenses, optional activities, tips</textarea>
                        </div>
                    </form>
                </div>
                
                <div class="tab-content" id="media-tab">
                    <form class="modal-form" id="media-form">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-image-line"></i>
                                Cover Image
                            </label>
                            <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 24px; text-align: center; margin-top: 8px;">
                                <i class="ri-upload-cloud-line" style="font-size: 2rem; color: var(--text-secondary); margin-bottom: 12px;"></i>
                                <p style="color: var(--text-secondary); margin-bottom: 12px;">Drag & drop your cover image here or click to browse</p>
                                <input type="file" class="form-input" accept="image/*" required style="padding: 8px; width: 100%;">
                            </div>
                            <small style="color: var(--text-secondary); margin-top: 6px; display: block;">Recommended size: 1200x800 pixels</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-gallery-line"></i>
                                Gallery Images
                            </label>
                            <input type="file" class="form-input" multiple accept="image/*">
                            <small style="color: var(--text-secondary); margin-top: 6px; display: block;">Select multiple images for the package gallery</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-star-line"></i>
                                    Featured
                                </label>
                                <select class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-checkbox-circle-line"></i>
                                    Status
                                </label>
                                <select class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" id="modal-cancel">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
                <button class="btn btn-primary" id="modal-save">
                    <i class="ri-save-line"></i>
                    Save Package
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <script>
        // Enhanced package data with more realistic information
        const packagesData = [
            {
                id: 1,
                name: "Kashmir Valley Explorer",
                type: "Cultural",
                duration: "7 Days",
                destinations: "Srinagar, Gulmarg, Pahalgam",
                price: "₹25,999",
                bookings: 32,
                rating: 4.8,
                status: "active",
                featured: true,
                lastUpdated: "2024-01-15"
            },
            {
                id: 2,
                name: "Ladakh Adventure Expedition",
                type: "Adventure",
                duration: "10 Days",
                destinations: "Leh, Nubra, Pangong",
                price: "₹32,499",
                bookings: 28,
                rating: 4.9,
                status: "active",
                featured: true,
                lastUpdated: "2024-01-10"
            },
            {
                id: 3,
                name: "Kashmir Honeymoon Special",
                type: "Honeymoon",
                duration: "5 Days",
                destinations: "Srinagar, Gulmarg",
                price: "₹38,999",
                bookings: 18,
                rating: 4.7,
                status: "active",
                featured: false,
                lastUpdated: "2024-01-05"
            },
            {
                id: 4,
                name: "Family Kashmir Escape",
                type: "Family",
                duration: "6 Days",
                destinations: "Srinagar, Pahalgam",
                price: "₹22,499",
                bookings: 24,
                rating: 4.6,
                status: "active",
                featured: true,
                lastUpdated: "2024-01-12"
            },
            {
                id: 5,
                name: "Complete Ladakh Experience",
                type: "Adventure",
                duration: "11 Days",
                destinations: "Leh, Nubra, Pangong, Tso Moriri",
                price: "₹35,999",
                bookings: 15,
                rating: 4.5,
                status: "draft",
                featured: false,
                lastUpdated: "2024-01-08"
            },
            {
                id: 6,
                name: "Premium Houseboat Experience",
                type: "Luxury",
                duration: "4 Days",
                destinations: "Dal Lake, Srinagar",
                price: "₹45,999",
                bookings: 12,
                rating: 4.9,
                status: "inactive",
                featured: true,
                lastUpdated: "2024-01-03"
            },
            {
                id: 7,
                name: "Sonamarg Trekking Adventure",
                type: "Adventure",
                duration: "4 Days",
                destinations: "Sonamarg, Thajiwas Glacier",
                price: "₹18,500",
                bookings: 8,
                rating: 4.4,
                status: "active",
                featured: false,
                lastUpdated: "2024-01-14"
            },
            {
                id: 8,
                name: "Pahalgam Retreat",
                type: "Cultural",
                duration: "3 Days",
                destinations: "Pahalgam, Betaab Valley",
                price: "₹15,999",
                bookings: 14,
                rating: 4.3,
                status: "active",
                featured: false,
                lastUpdated: "2024-01-07"
            },
            {
                id: 9,
                name: "Winter Wonderland Kashmir",
                type: "Adventure",
                duration: "6 Days",
                destinations: "Gulmarg, Srinagar",
                price: "₹28,999",
                bookings: 21,
                rating: 4.8,
                status: "active",
                featured: true,
                lastUpdated: "2024-01-09"
            },
            {
                id: 10,
                name: "Spiritual Jammu Journey",
                type: "Cultural",
                duration: "5 Days",
                destinations: "Jammu, Vaishno Devi",
                price: "₹19,999",
                bookings: 16,
                rating: 4.6,
                status: "active",
                featured: false,
                lastUpdated: "2024-01-11"
            },
            {
                id: 11,
                name: "Leh Motorcycle Adventure",
                type: "Adventure",
                duration: "8 Days",
                destinations: "Leh, Khardung La",
                price: "₹29,999",
                bookings: 9,
                rating: 4.9,
                status: "active",
                featured: true,
                lastUpdated: "2024-01-13"
            },
            {
                id: 12,
                name: "Houseboat & Shikara Special",
                type: "Luxury",
                duration: "3 Days",
                destinations: "Dal Lake",
                price: "₹24,999",
                bookings: 7,
                rating: 4.7,
                status: "draft",
                featured: false,
                lastUpdated: "2024-01-06"
            }
        ];

        // DOM Elements
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const searchInput = document.getElementById('search-input');
        const packagesTableBody = document.getElementById('packages-table-body');
        const packageCount = document.getElementById('package-count');
        const addPackageBtn = document.getElementById('add-package-btn');
        const packageModal = document.getElementById('package-modal');
        const modalClose = document.getElementById('modal-close');
        const modalCancel = document.getElementById('modal-cancel');
        const modalSave = document.getElementById('modal-save');
        const modalTitle = document.getElementById('modal-title');
        const applyFiltersBtn = document.getElementById('apply-filters');
        const resetFiltersBtn = document.getElementById('reset-filters');
        const exportBtn = document.getElementById('export-btn');
        const prevPageBtn = document.getElementById('prev-page');
        const nextPageBtn = document.getElementById('next-page');
        const pageInfo = document.getElementById('page-info');
        
        // Filter elements
        const filterType = document.getElementById('filter-type');
        const filterDuration = document.getElementById('filter-duration');
        const filterStatus = document.getElementById('filter-status');
        const filterDestination = document.getElementById('filter-destination');
        
        // Pagination
        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredPackages = [...packagesData];
        
        // Theme Toggle
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            
            if (document.body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('ri-moon-line');
                themeIcon.classList.add('ri-sun-line');
                localStorage.setItem('theme', 'dark');
                showToast('Dark mode enabled', 'success');
            } else {
                themeIcon.classList.remove('ri-sun-line');
                themeIcon.classList.add('ri-moon-line');
                localStorage.setItem('theme', 'light');
                showToast('Light mode enabled', 'success');
            }
        }
        
        themeToggle.addEventListener('click', toggleTheme);
        
        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            themeIcon.classList.remove('ri-moon-line');
            themeIcon.classList.add('ri-sun-line');
        }
        
        // Toast Notification System
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="ri-${getToastIcon(type)}-line"></i>
                    <span>${message}</span>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Remove toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        function getToastIcon(type) {
            switch(type) {
                case 'success': return 'check';
                case 'error': return 'close';
                case 'warning': return 'alert';
                default: return 'information';
            }
        }
        
        // Modal Functionality
        addPackageBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Add New Package';
            packageModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
        
        function closeModal() {
            packageModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        modalClose.addEventListener('click', closeModal);
        modalCancel.addEventListener('click', closeModal);
        
        // Close modal on outside click
        packageModal.addEventListener('click', (e) => {
            if (e.target === packageModal) {
                closeModal();
            }
        });
        
        // Save Package
        modalSave.addEventListener('click', () => {
            const packageName = document.querySelector('#basic-form input[type="text"]').value;
            
            if (!packageName) {
                showToast('Please enter a package name', 'error');
                return;
            }
            
            // Show loading state
            const originalText = modalSave.innerHTML;
            modalSave.innerHTML = '<div class="loading"></div>';
            modalSave.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Add a new package to the table
                const newPackage = {
                    id: packagesData.length + 1,
                    name: packageName || "New Package",
                    type: document.querySelector('#basic-form select').value || "Cultural",
                    duration: document.querySelector('#basic-form input[type="number"]').value + " Days" || "5 Days",
                    destinations: "Srinagar, Gulmarg",
                    price: "₹20,000",
                    bookings: 0,
                    rating: 4.0,
                    status: "active",
                    featured: false,
                    lastUpdated: new Date().toISOString().split('T')[0]
                };
                
                packagesData.unshift(newPackage);
                renderPackages(getPaginatedPackages());
                closeModal();
                showToast('Package saved successfully!', 'success');
                
                // Reset button
                modalSave.innerHTML = originalText;
                modalSave.disabled = false;
            }, 1500);
        });
        
        // Tab Functionality
        const tabs = document.querySelectorAll('.tab');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Hide all tab content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show the corresponding tab content
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
        
        // Enhanced Itinerary Builder
        function setupItineraryBuilder() {
            // Add Day Button
            const addDayBtn = document.querySelector('.add-day-btn');
            
            addDayBtn.addEventListener('click', () => {
                const dayCount = document.querySelectorAll('.day-card').length + 1;
                const newDayCard = createDayCard(dayCount);
                addDayBtn.parentNode.insertBefore(newDayCard, addDayBtn);
                setupDayCardEvents(newDayCard);
            });
            
            // Setup existing day cards
            document.querySelectorAll('.day-card').forEach(setupDayCardEvents);
            
            // Setup existing activity buttons
            document.querySelectorAll('.add-activity-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const activityItem = createActivityItem();
                    this.parentNode.insertBefore(activityItem, this);
                    setupActivityItemEvents(activityItem);
                });
            });
            
            // Setup existing delete buttons
            document.querySelectorAll('.delete-day-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (document.querySelectorAll('.day-card').length > 1) {
                        this.closest('.day-card').remove();
                        renumberDays();
                    } else {
                        showToast('At least one day is required', 'warning');
                    }
                });
            });
        }
        
        function createDayCard(dayNumber) {
            const div = document.createElement('div');
            div.className = 'day-card';
            div.setAttribute('data-day', dayNumber);
            div.innerHTML = `
                <div class="day-header">
                    <div class="day-title">
                        <span>Day ${dayNumber}:</span>
                        <input type="text" placeholder="Enter day title">
                    </div>
                    <button type="button" class="action-btn delete-btn delete-day-btn">
                        <i class="ri-delete-bin-line"></i>
                        Remove
                    </button>
                </div>
                <button class="add-activity-btn">
                    <i class="ri-add-line"></i> Add Activity
                </button>
            `;
            return div;
        }
        
        function createActivityItem() {
            const div = document.createElement('div');
            div.className = 'activity-item';
            div.innerHTML = `
                <div class="activity-time">
                    <i class="ri-time-line"></i>
                    <input type="time" value="09:00">
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        <input type="text" placeholder="Activity title">
                    </div>
                    <div class="activity-description">
                        <textarea placeholder="Activity description"></textarea>
                    </div>
                </div>
                <button type="button" class="action-btn delete-btn delete-activity-btn">
                    <i class="ri-delete-bin-line"></i>
                </button>
            `;
            return div;
        }
        
        function setupDayCardEvents(dayCard) {
            const addActivityBtn = dayCard.querySelector('.add-activity-btn');
            const deleteDayBtn = dayCard.querySelector('.delete-day-btn');
            
            addActivityBtn.addEventListener('click', function() {
                const activityItem = createActivityItem();
                this.parentNode.insertBefore(activityItem, this);
                setupActivityItemEvents(activityItem);
            });
            
            deleteDayBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.day-card').length > 1) {
                    dayCard.remove();
                    renumberDays();
                } else {
                    showToast('At least one day is required', 'warning');
                }
            });
        }
        
        function setupActivityItemEvents(activityItem) {
            const deleteBtn = activityItem.querySelector('.delete-activity-btn');
            deleteBtn.addEventListener('click', function() {
                activityItem.remove();
            });
        }
        
        function renumberDays() {
            document.querySelectorAll('.day-card').forEach((card, index) => {
                const dayNumber = index + 1;
                card.setAttribute('data-day', dayNumber);
                const dayTitle = card.querySelector('.day-title span');
                dayTitle.textContent = `Day ${dayNumber}:`;
            });
        }
        
        // Filter Functionality
        function applyFilters() {
            const typeValue = filterType.value;
            const durationValue = filterDuration.value;
            const statusValue = filterStatus.value;
            const destinationValue = filterDestination.value;
            const searchValue = searchInput.value.toLowerCase();
            
            filteredPackages = packagesData.filter(pkg => {
                const matchesType = !typeValue || pkg.type.toLowerCase() === typeValue;
                const matchesDuration = !durationValue || (
                    (durationValue === '3-5' && (pkg.duration.includes('3') || pkg.duration.includes('4') || pkg.duration.includes('5'))) ||
                    (durationValue === '6-8' && (pkg.duration.includes('6') || pkg.duration.includes('7') || pkg.duration.includes('8'))) ||
                    (durationValue === '9-12' && (pkg.duration.includes('9') || pkg.duration.includes('10') || pkg.duration.includes('11') || pkg.duration.includes('12'))) ||
                    (durationValue === '12+' && parseInt(pkg.duration) > 12)
                );
                const matchesStatus = !statusValue || pkg.status === statusValue;
                const matchesDestination = !destinationValue || pkg.destinations.toLowerCase().includes(destinationValue);
                const matchesSearch = !searchValue || 
                    pkg.name.toLowerCase().includes(searchValue) ||
                    pkg.type.toLowerCase().includes(searchValue) ||
                    pkg.destinations.toLowerCase().includes(searchValue);
                
                return matchesType && matchesDuration && matchesStatus && matchesDestination && matchesSearch;
            });
            
            currentPage = 1;
            renderPackages(getPaginatedPackages());
            updatePagination();
            showToast(`Found ${filteredPackages.length} packages`, 'info');
        }
        
        applyFiltersBtn.addEventListener('click', applyFilters);
        
        // Reset Filters
        resetFiltersBtn.addEventListener('click', () => {
            filterType.value = '';
            filterDuration.value = '';
            filterStatus.value = '';
            filterDestination.value = '';
            searchInput.value = '';
            
            filteredPackages = [...packagesData];
            currentPage = 1;
            renderPackages(getPaginatedPackages());
            updatePagination();
            showToast('Filters reset', 'info');
        });
        
        // Search Functionality
        searchInput.addEventListener('input', debounce(() => {
            applyFilters();
        }, 300));
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Export Functionality
        exportBtn.addEventListener('click', () => {
            // Show loading
            const originalText = exportBtn.innerHTML;
            exportBtn.innerHTML = '<div class="loading"></div>';
            exportBtn.disabled = true;
            
            // Simulate export process
            setTimeout(() => {
                exportBtn.innerHTML = originalText;
                exportBtn.disabled = false;
                showToast('Package data exported to CSV successfully!', 'success');
            }, 1500);
        });
        
        // Pagination Functions
        function getPaginatedPackages() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            return filteredPackages.slice(start, end);
        }
        
        function updatePagination() {
            const totalPages = Math.ceil(filteredPackages.length / itemsPerPage);
            
            // Update page info
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            packageCount.textContent = `Showing ${getPaginatedPackages().length} of ${filteredPackages.length} packages`;
            
            // Update button states
            prevPageBtn.classList.toggle('disabled', currentPage === 1);
            nextPageBtn.classList.toggle('disabled', currentPage === totalPages);
            
            // Update active page button
            document.querySelectorAll('.pagination-btn[data-page]').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.dataset.page) === currentPage);
            });
        }
        
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPackages(getPaginatedPackages());
                updatePagination();
            }
        });
        
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredPackages.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderPackages(getPaginatedPackages());
                updatePagination();
            }
        });
        
        // Page number buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('pagination-btn') && e.target.dataset.page) {
                currentPage = parseInt(e.target.dataset.page);
                renderPackages(getPaginatedPackages());
                updatePagination();
            }
        });
        
        // Action Buttons
        document.addEventListener('click', (e) => {
            const target = e.target.closest('.action-btn') || e.target;
            
            if (target.classList.contains('view-btn')) {
                const packageId = target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                showToast(`Opening details for: ${packageData.name}`, 'info');
                // In real app, would navigate to details page
                
            } else if (target.classList.contains('edit-btn')) {
                const packageId = target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                modalTitle.textContent = `Edit Package: ${packageData.name}`;
                packageModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
            } else if (target.classList.contains('delete-btn') && !target.classList.contains('delete-day-btn') && !target.classList.contains('delete-activity-btn')) {
                const packageId = target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                
                if (confirm(`Are you sure you want to delete "${packageData.name}"? This action cannot be undone.`)) {
                    const index = packagesData.findIndex(pkg => pkg.id == packageId);
                    packagesData.splice(index, 1);
                    applyFilters(); // Reapply filters after deletion
                    showToast(`Package "${packageData.name}" has been deleted.`, 'success');
                }
            }
        });
        
        // Enhanced Render Packages Table
        function renderPackages(packages) {
            packagesTableBody.innerHTML = '';
            
            if (packages.length === 0) {
                packagesTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px 20px;">
                            <i class="ri-search-line" style="font-size: 3rem; color: var(--text-secondary); display: block; margin-bottom: 16px;"></i>
                            <h3 style="color: var(--text-secondary); margin-bottom: 8px;">No packages found</h3>
                            <p style="color: var(--text-secondary); opacity: 0.7;">Try adjusting your filters or search terms</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            packages.forEach(pkg => {
                const statusClass = `status-${pkg.status}`;
                const statusText = pkg.status.charAt(0).toUpperCase() + pkg.status.slice(1);
                const statusIcon = pkg.status === 'active' ? 'ri-checkbox-circle-line' : 
                                  pkg.status === 'draft' ? 'ri-draft-line' : 'ri-close-circle-line';
                
                const featuredIcon = pkg.featured ? '<i class="ri-star-fill" style="color: var(--warning-color); margin-left: 4px;"></i>' : '';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div style="font-weight: 600;">${pkg.name} ${featuredIcon}</div>
                        <small style="color: var(--text-secondary); font-size: 0.85rem;">
                            <i class="ri-star-fill" style="color: var(--warning-color);"></i> ${pkg.rating} • Last updated: ${pkg.lastUpdated}
                        </small>
                    </td>
                    <td>
                        <span class="status-badge" style="background: rgba(37, 99, 235, 0.1); color: var(--primary-color); border-color: rgba(37, 99, 235, 0.2);">
                            ${pkg.type}
                        </span>
                    </td>
                    <td>${pkg.duration}</td>
                    <td>
                        <div style="max-width: 200px;">
                            ${pkg.destinations}
                        </div>
                    </td>
                    <td style="font-weight: 700; color: var(--primary-color);">${pkg.price}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="ri-user-line" style="color: var(--text-secondary);"></i>
                            ${pkg.bookings}
                        </div>
                    </td>
                    <td>
                        <span class="status-badge ${statusClass}">
                            <i class="${statusIcon}"></i>
                            ${statusText}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            <button class="action-btn view-btn" data-id="${pkg.id}">
                                <i class="ri-eye-line"></i>
                                View
                            </button>
                            <button class="action-btn edit-btn" data-id="${pkg.id}">
                                <i class="ri-edit-line"></i>
                                Edit
                            </button>
                            <button class="action-btn delete-btn" data-id="${pkg.id}">
                                <i class="ri-delete-bin-line"></i>
                                Delete
                            </button>
                        </div>
                    </td>
                `;
                
                packagesTableBody.appendChild(row);
            });
        }
        
        // Initialize the page
        function initializePage() {
            renderPackages(getPaginatedPackages());
            updatePagination();
            setupItineraryBuilder();
            
            // Add keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + F to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
                
                // Escape to close modal
                if (e.key === 'Escape' && packageModal.style.display === 'flex') {
                    closeModal();
                }
                
                // Ctrl/Cmd + N to add new package
                if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                    e.preventDefault();
                    addPackageBtn.click();
                }
            });
            
            // Initialize with a welcome toast
            setTimeout(() => {
                showToast('Welcome to Package Management!', 'info');
            }, 1000);
        }
        
        // Initialize when DOM is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializePage);
        } else {
            initializePage();
        }
    </script>
</body>
</html>