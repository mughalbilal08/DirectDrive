<!-- Header -->
<header class="header">
    <div class="menu-icon" onclick="openSidebar()">
        <span class="material-icons-outlined">menu</span>
    </div>
    <div class="header-right">
        <span class="material-icons-outlined">account_circle</span>
        <label style="margin-left: 35px;"><?php echo $_SESSION['name'] ?? 'User';?></label>
    </div>
</header>
<!-- End Header -->

<!-- Sidebar -->
<aside id="sidebar">
    <div class="sidebar-title">
        <div class="sidebar-brand">
            Direct Drive
        </div>
        <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
    </div>
    <ul class="sidebar-list">
        <li class="sidebar-list-item">
            <a href="customer_dashboard.php">
                <i class='bx bx-grid-alt'></i>
                <span class="links_name">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-list-item">
            <a href="customerNotifications.php">
                <i class='bx bx-bell'></i>
                <span class="links_name">Notifications</span>
            </a>
        </li>
        <li class="sidebar-list-item">
            <a href="customer_subscriptions.php">
                <i class='bx bxs-credit-card'></i>
                <span class="links_name">Subscriptions</span>
            </a>
        </li>
       <li class="sidebar-list-item">
            <a href="logout.php">
                <i class='bx bx-log-out'></i>
                <span class="links_name">Logout</span>
            </a>
        </li>
    </ul>
</aside>
<!-- End Sidebar -->