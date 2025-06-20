<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BlogNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Color Palette */
            --primary: #1e293b;
            /* Deep navy blue */
            --secondary: #475569;
            /* Medium slate blue */
            --tertiary: #94a3b8;
            /* Light slate blue */
            --accent: #7c3aed;
            /* Vibrant purple */
            --accent-hover: #6d28d9;
            /* Darker purple */
            --accent-light: #c7d2fe;
            /* Light lavender */
            --card-bg: #ffffff;
            /* Pure white */
            --sidebar-bg: #f8fafc;
            /* Light gray-blue */
            --body-bg: #f1f5f9;
            /* Very light blue-gray */

            /* Status Colors */
            --success: #10b981;
            /* Emerald green */
            --warning: #f59e0b;
            /* Amber */
            --danger: #ef4444;
            /* Red */
            --info: #3b82f6;
            /* Blue */

            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);

            /* Gradients */
            --gradient-accent: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);

            /* Other */
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --card-border: 1px solid rgba(0, 0, 0, 0.03);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: var(--body-bg);
            color: var(--primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Top Navigation Bar */
        .top-bar {
            background-color: white;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .logo {
            font-family: "Poppins", sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            background: var(--gradient-accent);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo span {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-weight: 600;
        }

        /* Main Layout */
        .dashboard-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 2rem;
            padding: 2rem 0;
        }

        /* Sidebar */
        .sidebar {
            position: sticky;
            top: 80px;
            height: calc(100vh - 100px);
            overflow-y: auto;
            background-color: var(--sidebar-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: var(--card-border);
        }

        .sidebar-section {
            margin-bottom: 2rem;
        }

        .sidebar-title {
            font-family: "Poppins", sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            background-color: rgba(0, 0, 0, 0.02);
        }

        .sidebar-title i {
            font-size: 0.9rem;
            color: var(--tertiary);
        }

        .sidebar-list {
            list-style: none;
        }

        .sidebar-list li {
            padding: 0.65rem 0.75rem;
            color: var(--secondary);
            font-size: 0.95rem;
            cursor: pointer;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-list li i {
            font-size: 0.9rem;
            width: 20px;
            color: var(--tertiary);
        }

        .page-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            color: var(--tertiary);
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-btn.disabled:hover {
            background-color: white;
            border-color: rgba(0, 0, 0, 0.1);
            color: inherit;
            transform: none;
            box-shadow: none;
        }

        .sidebar-list li:hover {
            background-color: rgba(124, 58, 237, 0.05);
            color: var(--accent);
        }

        .sidebar-list li:hover i {
            color: var(--accent);
        }

        .sidebar-list li.active {
            background-color: rgba(124, 58, 237, 0.1);
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-list li.active i {
            color: var(--accent);
        }

        /* Main Content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Tabs Navigation */
        .tabs-nav {
            display: flex;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: var(--secondary);
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }

        .tab-btn:hover {
            color: var(--accent);
        }

        .tab-btn.active {
            color: var(--accent);
            border-bottom: 2px solid var(--accent);
            font-weight: 600;
        }

        /* Content Sections */
        .content-section {
            border-radius: var(--border-radius);
        }

        .content-section-wrapper {
            background-color: white;
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
            border: var(--card-border);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-family: "Poppins", sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            position: relative;
            padding-left: 1rem;
        }

        .section-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--gradient-accent);
            border-radius: 4px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: var(--card-border);
        }

        .stat-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            background-color: rgba(124, 58, 237, 0.1);
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.25rem;
        }

        .stat-icon.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-icon.info {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .stat-icon.warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .stat-details {
            flex: 1;
        }

        .stat-label {
            color: var(--tertiary);
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 0.75rem;
            font-weight: 600;
            color: var(--secondary);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .data-table td {
            padding: 0.75rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background-color: rgba(124, 58, 237, 0.03);
        }

        /* User/Avatar Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e9d5ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7e22ce;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--tertiary);
        }

        /* Article Title Cell */
        .article-title {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-primary {
            background-color: var(--accent-light);
            color: var(--accent);
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        /* Charts Container */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .chart-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: var(--card-border);
        }

        .chart-title {
            font-family: "Poppins", sans-serif;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            background: var(--gradient-accent);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--accent-hover) 0%, #9333ea 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        .btn-outline:hover {
            background-color: rgba(124, 58, 237, 0.1);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .action-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .action-btn.edit {
            color: var(--info);
        }

        .action-btn.delete {
            color: var(--danger);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .page-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .page-btn:hover {
            background-color: rgba(124, 58, 237, 0.1);
            border-color: var(--accent);
            color: var(--accent);
        }

        .page-btn.active {
            background-color: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                height: auto;
            }

            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .dashboard-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .sidebar {
                position: static;
                height: auto;
                top: auto;
            }

            .top-bar {
                position: static;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .logo {
                font-size: 1.25rem;
            }

            .user-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .stat-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .stat-icon {
                margin-bottom: 0.5rem;
            }

            .pagination {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .content-section-wrapper {
                padding: 1rem;
            }

            .tab-btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .modal-content {
                width: 95%;
                margin: 0 auto;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .sidebar {
                padding: 1rem;
            }

            .sidebar-list li {
                padding: 0.5rem;
            }
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary);
        }

        @media (max-width: 992px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .sidebar.active {
                display: block;
            }
        }

        /* Responsive Tables */
        @media (max-width: 768px) {
            .data-table {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .data-table thead,
            .data-table tbody,
            .data-table th,
            .data-table td,
            .data-table tr {
                display: block;
            }

            .data-table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            .data-table tr {
                border: 1px solid rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
            }

            .data-table td {
                border: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                position: relative;
                padding-left: 50%;
                white-space: normal;
                text-align: left;
            }

            .data-table td:before {
                position: absolute;
                top: 0.75rem;
                left: 0.75rem;
                width: 45%;
                padding-right: 1rem;
                white-space: nowrap;
                content: attr(data-title);
                font-weight: 600;
                color: var(--secondary);
            }
        }

        /* Table responsive adjustments */
        @media (max-width: 768px) {

            .data-table th,
            .data-table td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }

            .user-cell {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }

            .action-btn {
                width: 100%;
                height: 28px;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .close-modal {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            font-family: inherit;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <div class="top-bar">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-pen-fancy"></i>
                </div>
                Blog<span>Nest</span> Admin
            </div>

            <div class="user-actions">
                <button class="btn" id="newArticleBtn">
                    <i class="fas fa-plus"></i> New Blog
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-container">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </h3>
                    <ul class="sidebar-list">
                        <li class="active" data-tab="dashboard">
                            <i class="fas fa-chart-pie"></i> Overview
                        </li>
                        <li data-tab="authors">
                            <i class="fas fa-users"></i> Authors
                        </li>
                        <li data-tab="articles">
                            <i class="fas fa-newspaper"></i> Articles
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-tags"></i> Settings
                    </h3>
                    <ul class="sidebar-list">
                        <li data-tab="categories">
                            <i class="fas fa-tags"></i> Categories
                        </li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-sign-out-alt"></i> Session
                    </h3>
                    <ul class="sidebar-list">
                        <li id="logoutBtnAdmin"><i class="fas fa-sign-out-alt"></i> Logout</li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Tabs Navigation -->
                <div class="tabs-nav" style="display: none;">
                    <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
                    <button class="tab-btn" data-tab="authors">Authors</button>
                    <button class="tab-btn" data-tab="articles">Articles</button>
                    <button class="tab-btn" data-tab="categories">Categories</button>
                </div>

                <!-- Dashboard Tab Content -->
                <div id="dashboard-tab" class="content-section ">

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-details">
                                    <div class="stat-label">Total Authors</div>
                                    <div class="stat-value" id="stat-author">42</div>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-icon success">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="stat-details">
                                    <div class="stat-label">Total Articles</div>
                                    <div class="stat-value" id="stat-article">156</div>
                                </div>
                            </div>
                        </div>

                        <!-- Di bagian stats-grid, ganti card Total Views dengan ini: -->
                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-icon info">
                                    <i class="fas fa-tags"></i> <!-- Ganti icon dari fa-eye ke fa-tags -->
                                </div>
                                <div class="stat-details">
                                    <div class="stat-label">Total Categories</div>
                                    <div class="stat-value" id="stat-category">0</div> <!-- ID diubah -->
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-content">
                                <div class="stat-icon warning">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="stat-details">
                                    <div class="stat-label">Total Comments</div>
                                    <div class="stat-value">1,245</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-container">
                        <div class="chart-card">
                            <h4 class="chart-title">Authors & Articles</h4>
                            <canvas id="authorsChart" height="300"></canvas>
                        </div>

                        <div class="chart-card">
                            <h4 class="chart-title">Articles Timeline</h4>
                            <canvas id="articlesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <div id="categories-tab" class="content-section content-section-wrapper" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title">Categories Management</h3>
                        <button class="btn" id="add-category-btn">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>

                    <table class="data-table" id="categories-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categories-table-body">
                            <!-- Data akan diisi oleh JavaScript -->
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem;">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i> Loading categories...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Authors Tab Content -->
                <div id="authors-tab" class="content-section content-section-wrapper" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title">Authors Management</h3>

                    </div>

                    <table class="data-table" id="authors-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Author</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody id="authors-table-body">
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem;">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i> Loading authors...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="pagination" id="authors-pagination">
                        <!-- Pagination will be loaded here -->
                    </div>

                    <!-- Author Modal (for add/edit) -->
                    <div id="author-modal" class="modal" style="display: none;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 id="modal-title">Add New Author</h4>
                                <span class="close-modal">&times;</span>
                            </div>
                            <div class="modal-body">
                                <form id="author-form">
                                    <input type="hidden" id="author-id">
                                    <div class="form-group">
                                        <label for="author-name">Name</label>
                                        <input type="text" id="author-name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author-email">Email</label>
                                        <input type="email" id="author-email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="author-bio">Bio</label>
                                        <textarea id="author-bio" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="author-avatar">Avatar URL</label>
                                        <input type="text" id="author-avatar">
                                    </div>
                                    <div class="form-group">
                                        <label for="author-status">Status</label>
                                        <select id="author-status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-outline close-modal">Cancel</button>
                                        <button type="submit" class="btn">Save Author</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Articles Tab Content -->
                <div id="articles-tab" class="content-section content-section-wrapper" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title">Articles Management</h3>
                    </div>

                    <table class="data-table" id="articles-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Words</th>
                            </tr>
                        </thead>
                        <tbody id="articles-table-body">
                            <!-- Data will be loaded here via JavaScript -->
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem;">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i> Loading articles...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="pagination" id="articles-pagination">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="category-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="category-modal-title">Add New Category</h4>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <input type="hidden" id="category-id">
                    <div class="form-group">
                        <label for="category-name">Name</label>
                        <input type="text" id="category-name" placeholder="Category Name" required>
                    </div>
                    <div class="form-group">
                        <label for="category-slug">Slug</label>
                        <input type="text" id="category-slug" placeholder="Slug Description" required>
                    </div>
                    <div class="form-group">
                        <label for="category-description">Description</label>
                        <textarea id="category-description" placeholder="Description" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline close-modal">Cancel</button>
                        <button type="submit" class="btn">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add this function to fetch articles from your AP
        async function fetchArticles(page = 1, perPage = 10) {
            try {
                const response = await fetch(`../php/get_articles.php?page=${page}&per_page=${perPage}`);
                const data = await response.json();

                if (data.success) {
                    renderArticles(data.data);
                    renderArticlePagination(data.meta);
                } else {
                    console.error('Error fetching articles:', data.error);
                    // Error handling
                }
            } catch (error) {
                console.error('Fetch error:', error);
                // Error handling
            }
        }

        function renderArticlePagination(meta) {
            const pagination = document.getElementById('articles-pagination');
            if (!pagination) return;

            let html = '';
            const totalPages = meta.total_pages;
            const currentPage = meta.page;

            // Previous button
            html += `<button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" 
              onclick="fetchArticles(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
              <i class="fas fa-chevron-left"></i>
            </button>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<button class="page-btn active">${i}</button>`;
                } else if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button class="page-btn" onclick="fetchArticles(${i})">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="page-dots">...</span>`;
                }
            }

            // Next button
            html += `<button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" 
              onclick="fetchArticles(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
              <i class="fas fa-chevron-right"></i>
            </button>`;

            pagination.innerHTML = html;
        }


        function renderArticles(articles) {
            const tableBody = document.getElementById('articles-table-body');

            if (articles.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem;">
                            No articles found
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';

            articles.forEach(article => {
                // Format categories
                const categories = article.categories.map(cat =>
                    `<span class="badge badge-primary">${cat.name}</span>`
                ).join(' ');

                // Format status badge
                let statusClass = 'status-published';
                if (article.status === 'draft') statusClass = 'status-draft';
                if (article.status === 'archived') statusClass = 'status-archived';

                // Format date
                const pubDate = article.created_at ? new Date(article.created_at).toLocaleDateString() : '-';

                html += `
                    <tr>
                        <td>#${article.id}</td>
                        <td class="article-title" title="${article.title}">
                            ${article.title}
                        </td>
                        <td>Author ${article.author_id}</td>
                        <td>${categories}</td>
                        <td><span class="badge ${statusClass}">${article.status}</span></td>
                        <td>${pubDate}</td>
                        <td>${article.word_count}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;

            // Add event listeners to action buttons
            document.querySelectorAll('.action-btn.edit').forEach(btn => {
                btn.addEventListener('click', () => editArticle(btn.dataset.id));
            });

            document.querySelectorAll('.action-btn.delete').forEach(btn => {
                btn.addEventListener('click', () => deleteArticle(btn.dataset.id));
            });
        }

        function editArticle(articleId) {
            console.log('Edit article:', articleId);
            // Implement your edit functionality here
        }

        function deleteArticle(articleId) {
            if (confirm('Are you sure you want to delete this article?')) {
                console.log('Delete article:', articleId);
                // Implement your delete functionality here
            }
        }

        // Author Management Functions
        async function fetchAuthors() {
            try {
                const response = await fetch("users.php?role=author");
                const data = await response.json();
                if (data.success) {
                    renderAuthors(data.data);
                }
            } catch (error) {
                console.log(error.message)
            }
        }

        function renderAuthors(authors) {
            const tableBody = document.getElementById('authors-table-body');

            if (authors.length === 0) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">
                        No authors found
                    </td>
                </tr>
            `;
                return;
            }

            let html = '';

            authors.forEach(author => {
                // Get initials for avatar
                const initials = author.username.split(' ').map(n => n[0]).join('').toUpperCase();

                // Format status badge
                let statusClass = '';
                if (author.status === 'active') statusClass = 'badge-success';
                if (author.status === 'inactive') statusClass = 'badge-danger';
                if (author.status === 'pending') statusClass = 'badge-warning';
                const pubDate = author.created_at ? new Date(author.created_at).toLocaleDateString() : '-';

                html += `
                <tr>
                    <td>#${author.id}</td>
                    <td>
                        <div class="user-cell">
                            <div class="user-info">
                                <div class="user-name">${author.username}</div>
                                <div class="user-bio" style="font-size: 0.8rem; color: var(--tertiary); margin-top: 0.25rem;">
                                    ${author.bio ? author.bio.substring(0, 50) + (author.bio.length > 50 ? '...' : '') : ''}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>${author.email}</td>
                    <td><span class="badge ${statusClass}">${author.status}</span></td>
                    <td>${pubDate ?? '-'}</td>
                </tr>
            `;
            });

            tableBody.innerHTML = html;

            // Add event listeners to action buttons
            document.querySelectorAll('.action-btn.edit').forEach(btn => {
                btn.addEventListener('click', () => editAuthor(btn.dataset.id));
            });

            document.querySelectorAll('.action-btn.delete').forEach(btn => {
                btn.addEventListener('click', () => deleteAuthor(btn.dataset.id));
            });
        }

        function getAvatarColor(id) {
            const colors = ['#e9d5ff', '#ccfbf1', '#fef08a', '#bfdbfe', '#f3e8ff'];
            return colors[id % colors.length];
        }

        // Modal Functions
        function openAuthorModal(author = null) {
            const modal = document.getElementById('author-modal');
            const form = document.getElementById('author-form');

            if (author) {
                document.getElementById('modal-title').textContent = 'Edit Author';
                document.getElementById('author-id').value = author.id;
                document.getElementById('author-name').value = author.username;
                document.getElementById('author-email').value = author.email;
                document.getElementById('author-bio').value = author.bio || '';
                document.getElementById('author-avatar').value = author.avatar || '';
                document.getElementById('author-status').value = author.status;
            } else {
                document.getElementById('modal-title').textContent = 'Add New Author';
                form.reset();
            }

            modal.style.display = 'flex';
        }

        function closeAuthorModal() {
            document.getElementById('author-modal').style.display = 'none';
        }

        // Form Submission
        async function handleAuthorSubmit(e) {
            e.preventDefault();

            const form = e.target;
            const authorId = document.getElementById('author-id').value;
            const method = authorId ? 'PUT' : 'POST';
            const url = authorId ? `api/authors.php?id=${authorId}` : 'api/authors.php';

            const authorData = {
                name: document.getElementById('author-name').value,
                email: document.getElementById('author-email').value,
                bio: document.getElementById('author-bio').value,
                avatar: document.getElementById('author-avatar').value,
                status: document.getElementById('author-status').value
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(authorData)
                });

                const data = await response.json();

                if (data.success) {
                    closeAuthorModal();
                    fetchAuthors(); // Refresh the list
                } else {
                    alert(data.error || 'Failed to save author');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // CRUD Operations
        async function editAuthor(id) {
            try {
                const response = await fetch(`api/authors.php?id=${id}`);
                const data = await response.json();

                if (data.success && data.data.length > 0) {
                    openAuthorModal(data.data[0]);
                } else {
                    alert('Author not found');
                }
            } catch (error) {
                alert('Error loading author: ' + error.message);
            }
        }

        async function deleteAuthor(id) {
            if (!confirm('Are you sure you want to delete this author?')) return;

            try {
                const response = await fetch(`api/authors.php?id=${id}`, {
                    method: 'DELETE'
                });

                const data = await response.json();

                if (data.success) {
                    fetchAuthors(); // Refresh the list
                } else {
                    alert(data.error || 'Failed to delete author');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        document.getElementById("logoutBtnAdmin").addEventListener("click", logout);

        function logout() {
            // Hapus data autentikasi dari localStorage
            localStorage.removeItem("authToken");
            localStorage.removeItem("user");

            // Panggil API logout
            fetch("../logout.php", {
                    method: "POST",
                    credentials: "include",
                })
                .then(() => {
                    // Redirect ke halaman login
                    window.location.href = "/hybrid-editor/login.html";
                })
                .catch((error) => {
                    console.error("Logout error:", error);
                    window.location.href = "/hybrid-editor/login.html";
                });
        }

        async function checkAuth(allowedRoles = []) {
            const authToken = localStorage.getItem("authToken");
            const user = JSON.parse(localStorage.getItem("user"));

            if (!authToken || !user) {
                redirectToLogin();
                return false;
            }

            try {
                const response = await fetch("../validate.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        user_id: user.id,
                        token: authToken,
                    }),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.valid) {
                    console.error("Validation failed:", data.error || "Invalid session");
                    redirectToLogin();
                    return false;
                }

                // Validasi role jika ada parameter allowedRoles
                if (allowedRoles.length > 0 && !allowedRoles.includes(data.role)) {
                    console.error("Access denied: Invalid role");
                    redirectToLogin();
                    return false;
                }

                // Update user data in localStorage with role
                if (data.role) {
                    const updatedUser = {
                        ...user,
                        role: data.role
                    };
                    localStorage.setItem("user", JSON.stringify(updatedUser));
                }

                return true;
            } catch (error) {
                console.error("Auth validation error:", error);
                redirectToLogin();
                return false;
            }
        }

        // Fungsi bantu untuk redirect
        function redirectToLogin() {
            // Hapus semua data auth dari localStorage
            localStorage.removeItem("authToken");
            localStorage.removeItem("user");

            window.location.href = `/hybrid-editor/login.html`;
        }

        function fetchDashboardStats() {
            fetch('dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('stat-author').textContent = data.data.totalAuthors;
                        document.getElementById('stat-article').textContent = data.data.totalArticles;
                        document.getElementById('stat-category').textContent = data.data.totalCategories; // Tambah ini
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        }

        // Initialize tab switching
        document.addEventListener('DOMContentLoaded', async function() {
            // First Call Function
            const isAuthenticated = await checkAuth(['superadmin']);
            if (!isAuthenticated) return;

            initializeCharts();
            fetchArticles(1);
            fetchDashboardStats();

            // Add event listener
            document.getElementById('author-form').addEventListener('submit', handleAuthorSubmit);
            document.querySelectorAll('.close-modal').forEach(el => {
                el.addEventListener('click', closeAuthorModal);
            });

            // Close modal when clicking outside
            document.getElementById('author-modal').addEventListener('click', (e) => {
                if (e.target === document.getElementById('author-modal')) {
                    closeAuthorModal();
                }
            });

            // Set up tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabId = btn.getAttribute('data-tab');
                    switchTab(tabId);
                });
            });

            // Set up sidebar navigation
            document.querySelectorAll('.sidebar-list li').forEach(item => {
                if (item.hasAttribute('data-tab')) {
                    item.addEventListener('click', () => {
                        const tabId = item.getAttribute('data-tab');
                        switchTab(tabId);
                        if (tabId === 'articles') {
                            fetchArticles();
                        }

                        if (tabId === 'authors') {
                            fetchAuthors();
                        }
                    });
                }
            });

            // Fungsi untuk memuat categories
            async function fetchCategories() {
                try {
                    const response = await fetch('get_categories.php');
                    const data = await response.json();

                    if (data.success) {
                        renderCategories(data.data);
                    } else {
                        console.error('Error loading categories:', data.error);
                        document.getElementById('categories-table-body').innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--danger);">
                        Error loading categories: ${data.error}
                    </td>
                </tr>
            `;
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    document.getElementById('categories-table-body').innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; color: var(--danger);">
                    Network error: ${error.message}
                </td>
            </tr>
        `;
                }
            }

            // Fungsi untuk render categories di tabel
            function renderCategories(categories) {
                console.log

                const tbody = document.getElementById('categories-table-body');

                if (categories.length === 0) {
                    tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center;">
                    No categories found
                </td>
            </tr>
        `;
                    return;
                }

                let html = '';
                categories.forEach(category => {
                    html += `
            <tr>
                <td>${category.id}</td>
                <td>${category.name}</td>
                <td>${category.slug}</td>
                <td>${category.description || '-'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit" data-id="${category.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" data-id="${category.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
                });

                tbody.innerHTML = html;
            }

            // Modal untuk add/edit category
            const categoryModal = `
    <div id="category-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="category-modal-title">Add New Category</h4>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <input type="hidden" id="category-id">
                    <div class="form-group">
                        <label for="category-name">Name</label>
                        <input type="text" id="category-name" required>
                    </div>
                    <div class="form-group">
                        <label for="category-slug">Slug</label>
                        <input type="text" id="category-slug" required>
                    </div>
                    <div class="form-group">
                        <label for="category-description">Description</label>
                        <textarea id="category-description" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline close-modal">Cancel</button>
                        <button type="submit" class="btn">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
`;


            function switchTab(tabId) {
                // Hide all tab contents
                document.querySelectorAll('.content-section').forEach(content => {
                    content.style.display = 'none';
                });

                // Show the selected tab content
                document.getElementById(tabId + '-tab').style.display = 'block';

                // Load data based on tab
                switch (tabId) {
                    case 'articles':
                        fetchArticles();
                        break;
                    case 'authors':
                        fetchAuthors();
                        break;
                    case 'categories':
                        fetchCategories(); // Tambahkan ini
                        break;
                }

                // Update active state
                document.querySelectorAll('.tab-btn, .sidebar-list li').forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('data-tab') === tabId) {
                        item.classList.add('active');
                    }
                });
            }


            // Tambahkan modal ke body
            document.body.insertAdjacentHTML('beforeend', categoryModal);

            // Event listeners
            // Buka modal tambah kategori
            document.getElementById('add-category-btn').addEventListener('click', function() {
                document.getElementById('category-modal-title').textContent = 'Add New Category';
                document.getElementById('category-form').reset();
                document.getElementById('category-id').value = '';
                document.getElementById('category-modal').style.display = 'flex';
            });

            // Auto-generate slug
            document.getElementById('category-name').addEventListener('input', function() {
                const name = this.value;
                const slug = name.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('category-slug').value = slug;
            });

            // Form submission
            document.getElementById('category-form').addEventListener('submit', async function(e) {
                e.preventDefault();

                const categoryData = {
                    id: document.getElementById('category-id').value || null,
                    name: document.getElementById('category-name').value,
                    slug: document.getElementById('category-slug').value,
                    description: document.getElementById('category-description').value
                };

                try {
                    const url = categoryData.id ?
                        'update_category.php' : 'create_category.php';

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(categoryData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        closeModal();
                        fetchCategories();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to save category'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });

            // Event delegation untuk action buttons
            document.getElementById('categories-table').addEventListener('click', function(e) {
                // Edit button
                if (e.target.closest('.action-btn.edit')) {
                    const categoryId = e.target.closest('.action-btn.edit').dataset.id;
                    editCategory(categoryId);
                }

                // Delete button
                if (e.target.closest('.action-btn.delete')) {
                    const categoryId = e.target.closest('.action-btn.delete').dataset.id;
                    deleteCategory(categoryId);
                }
            });

            // Fungsi edit category
            async function editCategory(id) {
                try {
                    const response = await fetch(`get_category.php?id=${id}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        document.getElementById('category-modal-title').textContent = 'Edit Category';
                        document.getElementById('category-id').value = data.data.id;
                        document.getElementById('category-name').value = data.data.name;
                        document.getElementById('category-slug').value = data.data.slug;
                        document.getElementById('category-description').value = data.data.description || '';
                        document.getElementById('category-modal').style.display = 'flex';
                    } else {
                        alert('Category not found');
                    }
                } catch (error) {
                    alert('Error loading category: ' + error.message);
                }
            }

            // Fungsi delete category
            async function deleteCategory(id) {
                if (!confirm('Are you sure you want to delete this category?')) return;

                try {
                    const response = await fetch(`delete_category.php?id=${id}`, {
                        method: 'DELETE'
                    });

                    const data = await response.json();

                    if (data.success) {
                        fetchCategories();
                    } else {
                        alert(data.message || 'Failed to delete category');
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }

            // Fungsi close modal
            function closeModal() {
                document.getElementById('category-modal').style.display = 'none';
            }

            // Close modal handlers
            document.querySelectorAll('.close-modal').forEach(el => {
                el.addEventListener('click', closeModal);
            });

            document.getElementById('category-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // New article button
            document.getElementById("newArticleBtn").addEventListener("click", () => {
                window.location.href = "/hybrid-editor/editor.html";
            });

            // Initialize Charts
            async function initializeCharts() {
                try {
                    const response = await fetch('get_chart_data.php');
                    const data = await response.json();

                    if (data.success) {
                        // Process data for first chart (Authors & Articles)
                        const authorsData = data.data.authors_chart;
                        const authorsByCategory = {};
                        const uniqueAuthors = [];
                        const uniqueCategories = [];

                        // Organize data
                        authorsData.forEach(item => {
                            if (!uniqueAuthors.includes(item.author)) {
                                uniqueAuthors.push(item.author);
                            }
                            if (!uniqueCategories.includes(item.category || 'Uncategorized')) {
                                uniqueCategories.push(item.category || 'Uncategorized');
                            }

                            const category = item.category || 'Uncategorized';
                            if (!authorsByCategory[category]) {
                                authorsByCategory[category] = {};
                            }
                            authorsByCategory[category][item.author] = item.article_count;
                        });

                        // Prepare datasets for first chart
                        const categoryColors = {
                            'Design': '#e9d5ff',
                            'Development': '#ccfbf1',
                            'Marketing': '#fef08a',
                            'Technology': '#bfdbfe',
                            'Uncategorized': '#d1d5db'
                        };

                        const firstChartDatasets = Object.keys(authorsByCategory).map(category => {
                            return {
                                label: category,
                                data: uniqueAuthors.map(author => authorsByCategory[category][author] || 0),
                                backgroundColor: categoryColors[category] || '#d1d5db',
                                borderColor: '#6b7280',
                                borderWidth: 1
                            };
                        });

                        // Initialize first chart
                        const authorsCtx = document.getElementById('authorsChart').getContext('2d');
                        new Chart(authorsCtx, {
                            type: 'bar',
                            data: {
                                labels: uniqueAuthors,
                                datasets: firstChartDatasets
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Articles by Author and Category'
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true
                                    },
                                    y: {
                                        stacked: true,
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Process data for second chart (Articles Timeline)
                        const timelineData = data.data.timeline_chart;
                        const articlesByMonth = {};
                        const uniqueMonths = [];
                        const timelineCategories = [];

                        // Organize data
                        timelineData.forEach(item => {
                            if (!uniqueMonths.includes(item.month)) {
                                uniqueMonths.push(item.month);
                            }
                            const category = item.category || 'Uncategorized';
                            if (!timelineCategories.includes(category)) {
                                timelineCategories.push(category);
                            }

                            if (!articlesByMonth[item.month]) {
                                articlesByMonth[item.month] = {};
                            }
                            articlesByMonth[item.month][category] = item.article_count;
                        });

                        // Format month labels
                        const monthLabels = uniqueMonths.map(month => {
                            const [year, monthNum] = month.split('-');
                            return new Date(year, monthNum - 1).toLocaleDateString('en-US', {
                                month: 'short',
                                year: 'numeric'
                            });
                        });

                        // Prepare datasets for second chart
                        const secondChartDatasets = timelineCategories.map(category => {
                            return {
                                label: category,
                                data: uniqueMonths.map(month => articlesByMonth[month][category] || 0),
                                backgroundColor: categoryColors[category] || '#d1d5db',
                                borderColor: '#6b7280',
                                borderWidth: 1
                            };
                        });

                        // Initialize second chart
                        const articlesCtx = document.getElementById('articlesChart').getContext('2d');
                        new Chart(articlesCtx, {
                            type: 'bar',
                            data: {
                                labels: monthLabels,
                                datasets: secondChartDatasets
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Articles Published by Month'
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true
                                    },
                                    y: {
                                        stacked: true,
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                    } else {
                        console.error('Error loading chart data:', data.error);
                    }
                } catch (error) {
                    console.error('Error loading charts:', error);
                }
            }

            // // Authors Chart
            // const authorsCtx = document.getElementById('authorsChart').getContext('2d');
            // const authorsChart = new Chart(authorsCtx, {
            //     type: 'bar',
            //     data: {
            //         labels: ['John Doe', 'Alice Smith', 'Bob Johnson', 'Emma Thompson', 'Michael Wilson'],
            //         datasets: [{
            //                 label: 'Design Articles',
            //                 data: [5, 2, 1, 8, 0],
            //                 backgroundColor: '#e9d5ff',
            //                 borderColor: '#7e22ce',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Development Articles',
            //                 data: [10, 8, 3, 12, 6],
            //                 backgroundColor: '#ccfbf1',
            //                 borderColor: '#0d9488',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Marketing Articles',
            //                 data: [4, 5, 6, 7, 1],
            //                 backgroundColor: '#fef08a',
            //                 borderColor: '#854d0e',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Technology Articles',
            //                 data: [5, 3, 2, 5, 1],
            //                 backgroundColor: '#bfdbfe',
            //                 borderColor: '#1d4ed8',
            //                 borderWidth: 1
            //             }
            //         ]
            //     },
            //     options: {
            //         responsive: true,
            //         plugins: {
            //             legend: {
            //                 position: 'top',
            //             },
            //             title: {
            //                 display: true,
            //                 text: 'Articles by Author and Category'
            //             }
            //         },
            //         scales: {
            //             x: {
            //                 stacked: true,
            //             },
            //             y: {
            //                 stacked: true,
            //                 beginAtZero: true
            //             }
            //         }
            //     }
            // });

            // // Articles Chart
            // const articlesCtx = document.getElementById('articlesChart').getContext('2d');
            // const articlesChart = new Chart(articlesCtx, {
            //     type: 'bar',
            //     data: {
            //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            //         datasets: [{
            //                 label: 'Design',
            //                 data: [5, 7, 3, 8, 6, 4],
            //                 backgroundColor: '#e9d5ff',
            //                 borderColor: '#7e22ce',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Development',
            //                 data: [12, 15, 8, 10, 14, 9],
            //                 backgroundColor: '#ccfbf1',
            //                 borderColor: '#0d9488',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Marketing',
            //                 data: [3, 5, 7, 4, 6, 8],
            //                 backgroundColor: '#fef08a',
            //                 borderColor: '#854d0e',
            //                 borderWidth: 1
            //             },
            //             {
            //                 label: 'Technology',
            //                 data: [8, 6, 5, 7, 10, 12],
            //                 backgroundColor: '#bfdbfe',
            //                 borderColor: '#1d4ed8',
            //                 borderWidth: 1
            //             }
            //         ]
            //     },
            //     options: {
            //         responsive: true,
            //         plugins: {
            //             legend: {
            //                 position: 'top',
            //             },
            //             title: {
            //                 display: true,
            //                 text: 'Articles Published by Month'
            //             }
            //         },
            //         scales: {
            //             x: {
            //                 stacked: true,
            //             },
            //             y: {
            //                 stacked: true,
            //                 beginAtZero: true
            //             }
            //         }
            //     }
            // });
        });
    </script>
</body>

</html>