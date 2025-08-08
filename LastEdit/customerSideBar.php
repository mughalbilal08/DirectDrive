<!-- Header -->
<header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <span class="material-icons-outlined">account_circle</span>User
            </div>
        </header>
        <!-- End Header -->

        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-brand">
                    Concord Transport
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
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Notifications</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="customer_dashboard.php?logout">
                        <i class='bx bx-log-out'></i>
                        <span class="links_name">Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- End Sidebar -->

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>CUSTOMER DASHBOARD</h2>
            </div>

            <div class="main-cards">
                
                <a class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h3>Trips Completed</h3>
                    <h1><?php echo $numberofCompletedRides; ?></h1>    
                </div>
                <div class="card-popup">Completed</div>
            </a>

            <a class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h3>Trips Pending</h3>
                    <h1><?php echo $numberofPendingRides; ?></h1>    
                </div>
                <div class="card-popup">Pending</div>
            </a>

                
            </div>