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

        .menu-item:hover, .menu-item.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            margin-right: 16px;
            font-size: 1.3rem;
            transition: var(--transition);
        }

        .menu-item.active i, .menu-item:hover i {
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
            display: block;
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
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
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
        }

        .trend-up {
            color: var(--success-color);
        }

        .trend-down {
            color: var(--error-color);
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

        .filter-select, .filter-input {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Packages Table */
        .table-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            overflow: auto;
            border: 1px solid var(--border-color);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
            background: var(--bg-primary);
            position: sticky;
            top: 0;
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--bg-primary);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .status-draft {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
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
        }

        .view-btn {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .view-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .edit-btn {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .edit-btn:hover {
            background: var(--success-color);
            color: white;
        }

        .delete-btn {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .delete-btn:hover {
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

        .pagination-btn:hover, .pagination-btn.active {
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
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow);
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
            position: sticky;
            top: 0;
            background: var(--card-bg);
            z-index: 10;
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

        .form-input, .form-textarea, .form-select {
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

        .form-input:focus, .form-textarea:focus, .form-select:focus {
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
            position: sticky;
            bottom: 0;
            background: var(--card-bg);
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border-color);
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
            background: var(--primary-color);
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Itinerary Builder */
        .itinerary-builder {
            margin-top: 20px;
        }

        .day-card {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
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
        }

        .activity-item {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            padding: 12px;
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .activity-time {
            min-width: 100px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .activity-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
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
        }

        .add-day-btn {
            width: 100%;
            padding: 12px;
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
        }

        @media (max-width: 768px) {
            .stats-grid {
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
            
            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 8px;
            }
        }

        @media (max-width: 576px) {
            .modal-content {
                margin: 0;
                border-radius: 0;
                height: 100%;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
            
            .activity-item {
                flex-direction: column;
            }
            
            .activity-time {
                min-width: auto;
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
                    <i class="ri-moon-line"></i>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="dashboard.html">Dashboard</a>
                    <i class="ri-arrow-right-s-line"></i>
                    <span>Packages</span>
                </div>
                <div class="action-buttons">
                    
                    <button class="btn btn-primary" id="add-package-btn">
                        <i class="ri-add-line"></i>
                        Add Package
                    </button>
                </div>
            </div>
            
            <!-- Stats Overview -->
            <!-- <div class="stats-grid">
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
                        <h3>₹12,45,800</h3>
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
            </div> -->
            
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <div class="filters-title">Filter Packages</div>
                    <div>
                        <!-- <button class="btn btn-secondary" id="reset-filters">
                            <i class="ri-refresh-line"></i>
                            Reset
                        </button> -->
                        <button class="btn btn-secondary" id="apply-filters">
                            <i class="ri-filter-line"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Package Type</label>
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
                        <label class="filter-label">Duration</label>
                        <select class="filter-select" id="filter-duration">
                            <option value="">Any Duration</option>
                            <option value="3-5">3-5 Days</option>
                            <option value="6-8">6-8 Days</option>
                            <option value="9-12">9-12 Days</option>
                            <option value="12+">12+ Days</option>
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
                        <label class="filter-label">Destination</label>
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
                    <h3>All Packages</h3>
                    <span id="package-count">Showing 8 of 24 packages</span>
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
                        <tr>
                            <td>Kashmir Valley Explorer</td>
                            <td>Cultural</td>
                            <td>7 Days</td>
                            <td>Srinagar, Gulmarg, Pahalgam</td>
                            <td>₹25,999</td>
                            <td>32</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="1">View</button>
                                <button class="action-btn edit-btn" data-id="1">Edit</button>
                                <button class="action-btn delete-btn" data-id="1">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Ladakh Adventure Expedition</td>
                            <td>Adventure</td>
                            <td>10 Days</td>
                            <td>Leh, Nubra, Pangong</td>
                            <td>₹32,499</td>
                            <td>28</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="2">View</button>
                                <button class="action-btn edit-btn" data-id="2">Edit</button>
                                <button class="action-btn delete-btn" data-id="2">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Kashmir Honeymoon Special</td>
                            <td>Honeymoon</td>
                            <td>5 Days</td>
                            <td>Srinagar, Gulmarg</td>
                            <td>₹38,999</td>
                            <td>18</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="3">View</button>
                                <button class="action-btn edit-btn" data-id="3">Edit</button>
                                <button class="action-btn delete-btn" data-id="3">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Family Kashmir Escape</td>
                            <td>Family</td>
                            <td>6 Days</td>
                            <td>Srinagar, Pahalgam</td>
                            <td>₹22,499</td>
                            <td>24</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="4">View</button>
                                <button class="action-btn edit-btn" data-id="4">Edit</button>
                                <button class="action-btn delete-btn" data-id="4">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Complete Ladakh Experience</td>
                            <td>Adventure</td>
                            <td>11 Days</td>
                            <td>Leh, Nubra, Pangong, Tso Moriri</td>
                            <td>₹35,999</td>
                            <td>15</td>
                            <td><span class="status-badge status-draft">Draft</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="5">View</button>
                                <button class="action-btn edit-btn" data-id="5">Edit</button>
                                <button class="action-btn delete-btn" data-id="5">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Premium Houseboat Experience</td>
                            <td>Luxury</td>
                            <td>4 Days</td>
                            <td>Dal Lake, Srinagar</td>
                            <td>₹45,999</td>
                            <td>12</td>
                            <td><span class="status-badge status-inactive">Inactive</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="6">View</button>
                                <button class="action-btn edit-btn" data-id="6">Edit</button>
                                <button class="action-btn delete-btn" data-id="6">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Sonamarg Trekking Adventure</td>
                            <td>Adventure</td>
                            <td>4 Days</td>
                            <td>Sonamarg, Thajiwas Glacier</td>
                            <td>₹18,500</td>
                            <td>8</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="7">View</button>
                                <button class="action-btn edit-btn" data-id="7">Edit</button>
                                <button class="action-btn delete-btn" data-id="7">Delete</button>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Pahalgam Retreat</td>
                            <td>Cultural</td>
                            <td>3 Days</td>
                            <td>Pahalgam, Betaab Valley</td>
                            <td>₹15,999</td>
                            <td>14</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn view-btn" data-id="8">View</button>
                                <button class="action-btn edit-btn" data-id="8">Edit</button>
                                <button class="action-btn delete-btn" data-id="8">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
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
    
    <!-- Add/Edit Package Modal -->
    <div class="modal" id="package-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Add New Package</h3>
                <button class="modal-close" id="modal-close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="tabs">
                    <button class="tab active" data-tab="basic">Basic Info</button>
                    <button class="tab" data-tab="itinerary">Itinerary</button>
                    <button class="tab" data-tab="pricing">Pricing & Inclusions</button>
                    <button class="tab" data-tab="media">Media</button>
                </div>
                
                <div class="tab-content active" id="basic-tab">
                    <form class="modal-form" id="basic-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Package Name</label>
                                <input type="text" class="form-input" placeholder="Enter package name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Package Type</label>
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
                                <label class="form-label">Duration (Days)</label>
                                <input type="number" class="form-input" placeholder="Enter duration" min="1" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Max Group Size</label>
                                <input type="number" class="form-input" placeholder="Enter group size" min="1" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Destinations</label>
                            <select class="form-select" multiple id="destinations-select">
                                <option value="srinagar">Srinagar</option>
                                <option value="gulmarg">Gulmarg</option>
                                <option value="pahalgam">Pahalgam</option>
                                <option value="sonamarg">Sonamarg</option>
                                <option value="leh">Leh</option>
                                <option value="nubra">Nubra Valley</option>
                                <option value="pangong">Pangong Lake</option>
                            </select>
                            <small>Hold Ctrl/Cmd to select multiple destinations</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Short Description</label>
                            <textarea class="form-textarea" placeholder="Enter short description for listing pages" maxlength="160" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-textarea" placeholder="Enter detailed package description" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="tab-content" id="itinerary-tab">
                    <div class="itinerary-builder">
                        <div class="day-card" data-day="1">
                            <div class="day-header">
                                <div class="day-title">Day 1: Arrival in Srinagar</div>
                                <button type="button" class="delete-day-btn">Remove</button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">12:00 PM</div>
                                <div class="activity-content">
                                    <div class="activity-title">Airport Pickup</div>
                                    <div class="activity-description">Meet and greet at Srinagar International Airport, transfer to hotel</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">2:00 PM</div>
                                <div class="activity-content">
                                    <div class="activity-title">Lunch</div>
                                    <div class="activity-description">Welcome lunch with authentic Kashmiri cuisine</div>
                                </div>
                            </div>
                            <button class="add-activity-btn">
                                <i class="ri-add-line"></i> Add Activity
                            </button>
                        </div>
                        
                        <div class="day-card" data-day="2">
                            <div class="day-header">
                                <div class="day-title">Day 2: Srinagar Sightseeing</div>
                                <button type="button" class="delete-day-btn">Remove</button>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">9:00 AM</div>
                                <div class="activity-content">
                                    <div class="activity-title">Mughal Gardens Tour</div>
                                    <div class="activity-description">Visit Nishat Bagh, Shalimar Bagh, and Chashme Shahi</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-time">2:00 PM</div>
                                <div class="activity-content">
                                    <div class="activity-title">Shikara Ride</div>
                                    <div class="activity-description">Evening shikara ride on Dal Lake</div>
                                </div>
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
                                <label class="form-label">Base Price (₹)</label>
                                <input type="number" class="form-input" placeholder="Enter base price" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" class="form-input" placeholder="Enter discount percentage" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Seasonal Pricing</label>
                                <select class="form-select" id="season-pricing">
                                    <option value="peak">Peak Season (April-June, Sept-Oct): +20%</option>
                                    <option value="shoulder">Shoulder Season (July-Aug): +10%</option>
                                    <option value="off" selected>Off Season (Nov-March): Base Price</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Child Discount (%)</label>
                                <input type="number" class="form-input" value="30" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Inclusions</label>
                            <div class="inclusions-list">
                                <div class="form-check">
                                    <input type="checkbox" id="inc-accommodation" checked>
                                    <label for="inc-accommodation">Accommodation</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="inc-meals" checked>
                                    <label for="inc-meals">All Meals</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="inc-transport" checked>
                                    <label for="inc-transport">Transportation</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="inc-guide" checked>
                                    <label for="inc-guide">Guide Services</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="inc-activities">
                                    <label for="inc-activities">Activity Fees</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Exclusions</label>
                            <textarea class="form-textarea" placeholder="List what's not included in the package">Airfare, travel insurance, personal expenses, optional activities, tips</textarea>
                        </div>
                    </form>
                </div>
                
                <div class="tab-content" id="media-tab">
                    <form class="modal-form" id="media-form">
                        <div class="form-group">
                            <label class="form-label">Cover Image</label>
                            <input type="file" class="form-input" accept="image/*" required>
                            <small>Recommended size: 1200x800 pixels</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Gallery Images</label>
                            <input type="file" class="form-input" multiple accept="image/*">
                            <small>Select multiple images for the package gallery</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Featured</label>
                                <select class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
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
                <button class="btn btn-secondary" id="modal-cancel">Cancel</button>
                <button class="btn btn-primary" id="modal-save">Save Package</button>
            </div>
        </div>
    </div>

    <script>
        // Sample package data
        const packagesData = [
            {
                id: 1,
                name: "Kashmir Valley Explorer",
                type: "Cultural",
                duration: "7 Days",
                destinations: "Srinagar, Gulmarg, Pahalgam",
                price: "₹25,999",
                bookings: 32,
                status: "active"
            },
            {
                id: 2,
                name: "Ladakh Adventure Expedition",
                type: "Adventure",
                duration: "10 Days",
                destinations: "Leh, Nubra, Pangong",
                price: "₹32,499",
                bookings: 28,
                status: "active"
            },
            {
                id: 3,
                name: "Kashmir Honeymoon Special",
                type: "Honeymoon",
                duration: "5 Days",
                destinations: "Srinagar, Gulmarg",
                price: "₹38,999",
                bookings: 18,
                status: "active"
            },
            {
                id: 4,
                name: "Family Kashmir Escape",
                type: "Family",
                duration: "6 Days",
                destinations: "Srinagar, Pahalgam",
                price: "₹22,499",
                bookings: 24,
                status: "active"
            },
            {
                id: 5,
                name: "Complete Ladakh Experience",
                type: "Adventure",
                duration: "11 Days",
                destinations: "Leh, Nubra, Pangong, Tso Moriri",
                price: "₹35,999",
                bookings: 15,
                status: "draft"
            },
            {
                id: 6,
                name: "Premium Houseboat Experience",
                type: "Luxury",
                duration: "4 Days",
                destinations: "Dal Lake, Srinagar",
                price: "₹45,999",
                bookings: 12,
                status: "inactive"
            },
            {
                id: 7,
                name: "Sonamarg Trekking Adventure",
                type: "Adventure",
                duration: "4 Days",
                destinations: "Sonamarg, Thajiwas Glacier",
                price: "₹18,500",
                bookings: 8,
                status: "active"
            },
            {
                id: 8,
                name: "Pahalgam Retreat",
                type: "Cultural",
                duration: "3 Days",
                destinations: "Pahalgam, Betaab Valley",
                price: "₹15,999",
                bookings: 14,
                status: "active"
            }
        ];

        // DOM Elements
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
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
        
        // Filter elements
        const filterType = document.getElementById('filter-type');
        const filterDuration = document.getElementById('filter-duration');
        const filterStatus = document.getElementById('filter-status');
        const filterDestination = document.getElementById('filter-destination');
        
        // Theme Toggle
        themeToggle.addEventListener('click', () => {
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
        
        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            themeIcon.classList.remove('ri-moon-line');
            themeIcon.classList.add('ri-sun-line');
        }
        
        // Modal Functionality
        addPackageBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Add New Package';
            packageModal.style.display = 'flex';
        });
        
        modalClose.addEventListener('click', () => {
            packageModal.style.display = 'none';
        });
        
        modalCancel.addEventListener('click', () => {
            packageModal.style.display = 'none';
        });
        
        modalSave.addEventListener('click', () => {
            // In a real app, this would save the package data
            alert('Package saved successfully!');
            packageModal.style.display = 'none';
            
            // Add a new package to the table for demo purposes
            const newPackage = {
                id: packagesData.length + 1,
                name: document.querySelector('#basic-form input[type="text"]').value || "New Package",
                type: document.querySelector('#basic-form select').value || "Cultural",
                duration: document.querySelector('#basic-form input[type="number"]').value + " Days" || "5 Days",
                destinations: "Srinagar, Gulmarg",
                price: "₹20,000",
                bookings: 0,
                status: "active"
            };
            
            packagesData.push(newPackage);
            renderPackages(packagesData);
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
        
        // Itinerary Builder Functionality
        const addDayBtn = document.querySelector('.add-day-btn');
        
        addDayBtn.addEventListener('click', () => {
            const dayCount = document.querySelectorAll('.day-card').length + 1;
            const newDayCard = document.createElement('div');
            newDayCard.className = 'day-card';
            newDayCard.setAttribute('data-day', dayCount);
            newDayCard.innerHTML = `
                <div class="day-header">
                    <div class="day-title">Day ${dayCount}: <input type="text" placeholder="Enter day title" style="border: none; border-bottom: 1px dashed #ccc; background: transparent; padding: 2px 4px;"></div>
                    <button type="button" class="delete-day-btn">Remove</button>
                </div>
                <button class="add-activity-btn">
                    <i class="ri-add-line"></i> Add Activity
                </button>
            `;
            
            addDayBtn.parentNode.insertBefore(newDayCard, addDayBtn);
            
            // Add event listener to the new delete button
            newDayCard.querySelector('.delete-day-btn').addEventListener('click', function() {
                this.closest('.day-card').remove();
            });
            
            // Add event listener to the new add activity button
            newDayCard.querySelector('.add-activity-btn').addEventListener('click', function() {
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-time"><input type="time" value="09:00" style="border: none; background: transparent; width: 80px;"></div>
                    <div class="activity-content">
                        <div class="activity-title"><input type="text" placeholder="Activity title" style="border: none; border-bottom: 1px dashed #ccc; background: transparent; padding: 2px 4px; width: 100%;"></div>
                        <div class="activity-description"><textarea placeholder="Activity description" style="border: none; background: transparent; width: 100%; resize: none; height: 40px;"></textarea></div>
                    </div>
                    <button type="button" class="delete-activity-btn" style="background: none; border: none; color: #ef4444; cursor: pointer;"><i class="ri-delete-bin-line"></i></button>
                `;
                
                this.parentNode.insertBefore(activityItem, this);
                
                // Add event listener to the delete activity button
                activityItem.querySelector('.delete-activity-btn').addEventListener('click', function() {
                    this.closest('.activity-item').remove();
                });
            });
        });
        
        // Add activity button functionality
        document.querySelectorAll('.add-activity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const activityItem = document.createElement('div');
                activityItem.className = 'activity-item';
                activityItem.innerHTML = `
                    <div class="activity-time"><input type="time" value="09:00" style="border: none; background: transparent; width: 80px;"></div>
                    <div class="activity-content">
                        <div class="activity-title"><input type="text" placeholder="Activity title" style="border: none; border-bottom: 1px dashed #ccc; background: transparent; padding: 2px 4px; width: 100%;"></div>
                        <div class="activity-description"><textarea placeholder="Activity description" style="border: none; background: transparent; width: 100%; resize: none; height: 40px;"></textarea></div>
                    </div>
                    <button type="button" class="delete-activity-btn" style="background: none; border: none; color: #ef4444; cursor: pointer;"><i class="ri-delete-bin-line"></i></button>
                `;
                
                this.parentNode.insertBefore(activityItem, this);
                
                // Add event listener to the delete activity button
                activityItem.querySelector('.delete-activity-btn').addEventListener('click', function() {
                    this.closest('.activity-item').remove();
                });
            });
        });
        
        // Delete day button functionality
        document.querySelectorAll('.delete-day-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.day-card').remove();
            });
        });
        
        // Filter Functionality
        applyFiltersBtn.addEventListener('click', () => {
            const typeValue = filterType.value;
            const durationValue = filterDuration.value;
            const statusValue = filterStatus.value;
            const destinationValue = filterDestination.value;
            const searchValue = searchInput.value.toLowerCase();
            
            const filteredPackages = packagesData.filter(pkg => {
                return (
                    (typeValue === '' || pkg.type.toLowerCase() === typeValue) &&
                    (durationValue === '' || pkg.duration.includes(durationValue)) &&
                    (statusValue === '' || pkg.status === statusValue) &&
                    (destinationValue === '' || pkg.destinations.toLowerCase().includes(destinationValue)) &&
                    (searchValue === '' || pkg.name.toLowerCase().includes(searchValue))
                );
            });
            
            renderPackages(filteredPackages);
        });
        
        // Reset Filters
        resetFiltersBtn.addEventListener('click', () => {
            filterType.value = '';
            filterDuration.value = '';
            filterStatus.value = '';
            filterDestination.value = '';
            searchInput.value = '';
            
            renderPackages(packagesData);
        });
        
        // Search Functionality
        searchInput.addEventListener('input', () => {
            const searchValue = searchInput.value.toLowerCase();
            
            if (searchValue === '') {
                renderPackages(packagesData);
                return;
            }
            
            const filteredPackages = packagesData.filter(pkg => {
                return pkg.name.toLowerCase().includes(searchValue) ||
                       pkg.type.toLowerCase().includes(searchValue) ||
                       pkg.destinations.toLowerCase().includes(searchValue);
            });
            
            renderPackages(filteredPackages);
        });
        
        // Export Functionality
        exportBtn.addEventListener('click', () => {
            alert('Exporting package data to CSV...');
            // In a real app, this would generate and download a CSV file
        });
        
        // Action Buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-btn')) {
                const packageId = e.target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                alert(`Viewing package: ${packageData.name}\nThis would open the package details page.`);
            } else if (e.target.classList.contains('edit-btn')) {
                const packageId = e.target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                modalTitle.textContent = `Edit Package: ${packageData.name}`;
                packageModal.style.display = 'flex';
            } else if (e.target.classList.contains('delete-btn')) {
                const packageId = e.target.getAttribute('data-id');
                const packageData = packagesData.find(pkg => pkg.id == packageId);
                
                if (confirm(`Are you sure you want to delete "${packageData.name}"?`)) {
                    const index = packagesData.findIndex(pkg => pkg.id == packageId);
                    packagesData.splice(index, 1);
                    renderPackages(packagesData);
                    alert(`Package "${packageData.name}" has been deleted.`);
                }
            }
        });
        
        // Render Packages Table
        function renderPackages(packages) {
            packagesTableBody.innerHTML = '';
            packageCount.textContent = `Showing ${packages.length} of ${packagesData.length} packages`;
            
            if (packages.length === 0) {
                packagesTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="ri-search-line" style="font-size: 2rem; display: block; margin-bottom: 16px;"></i>
                            No packages found matching your criteria
                        </td>
                    </tr>
                `;
                return;
            }
            
            packages.forEach(pkg => {
                const statusClass = `status-${pkg.status}`;
                const statusText = pkg.status.charAt(0).toUpperCase() + pkg.status.slice(1);
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${pkg.name}</td>
                    <td>${pkg.type}</td>
                    <td>${pkg.duration}</td>
                    <td>${pkg.destinations}</td>
                    <td>${pkg.price}</td>
                    <td>${pkg.bookings}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="action-btn view-btn" data-id="${pkg.id}">View</button>
                        <button class="action-btn edit-btn" data-id="${pkg.id}">Edit</button>
                        <button class="action-btn delete-btn" data-id="${pkg.id}">Delete</button>
                    </td>
                `;
                
                packagesTableBody.appendChild(row);
            });
        }
        
        // Initialize the page
        renderPackages(packagesData);
    </script>
</body>
</html>