
 <!-- Header -->
<header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <label style="margin-left: 35px;"><?php echo $_SESSION['name'];?></label> 
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
                    <a href="dashboard.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="adminNotifications.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Notifications</span>
                    </a>
                </li>
                
                <!-- ADD Section -->
                <li class="sidebar-section" onclick="toggleSection('add-section')">
                    <i class='bx bxs-plus-circle'></i>
                    <span class="links_name">ADD</span>
                </li>
                <ul id="add-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="add-driver.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Driver</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="assignDriverVehicle.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Assign Vehicle</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="addCustomer.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Customer</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="ad-vehicle.php">
                            <i class='bx bxs-car'></i>
                            <span class="links_name">Add Vehicle</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['role'] != 'Staff'): ?>
                    <li class="sidebar-list-item">
                        <a href="add-staff.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Staff</span>
                        </a>
                    </li>
                    <?php endif ?>
                    <li class="sidebar-list-item">
                        <a href="add-expenses.php">
                            <i class='bx bxs-credit-card'></i>
                            <span class="links_name">Add Expenses</span>
                        </a>
                    </li>
                </ul>

                <!-- VIEW Section -->
                <li class="sidebar-section" onclick="toggleSection('view-section')">
                    <i class='bx bxs-show'></i>
                    <span class="links_name">VIEW</span>
                </li>
                <ul id="view-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="view_users.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Users</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_drivers.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Drivers</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_staff.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">View Staff</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="view_vehicles.php">
                            <i class='bx bxs-car'></i>
                            <span class="links_name">View Vehicles</span>
                        </a>
                    </li>
                </ul>

                <!-- REPORT Section -->
                <li class="sidebar-section" onclick="toggleSection('report-section')">
                    <i class='bx bxs-report'></i>
                    <span class="links_name">REPORT</span>
                </li>
                <ul id="report-section" class="sidebar-sublist">
                <li class="sidebar-list-item">
                        <a href="driverDetails.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Trips</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="monthReports.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Monthly Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="downloadPictures.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Download Pictures</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="admin_income_repor.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Income Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="admin_expense_report.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Expense Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="adminDeposits.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Deposit Report</span> 
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="reports.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Client Report</span>
                        </a>
                    </li>
                   
                   
                </ul>

                <!-- CASH Section -->
                <li class="sidebar-section" onclick="toggleSection('cash-section')">
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="links_name">CASH</span>
                </li>
                <ul id="cash-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="update_office_cash.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Update Office Cash</span>
                        </a>
                    </li>
                   
                </ul>

                <li class="sidebar-list-item">
                    <a href="index.php?logout">
                        <i class='bx bx-log-out'></i>
                        <span class="links_name">Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- End Sidebar -->

        <!-- Main -->
    