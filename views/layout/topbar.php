<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand justify-content-between">
            <div class="mobile-toggle-menu font-20 text-white ms-3"><i class='fa-solid fa-bars'></i>
            </div>
            <div class="search-bar flex-grow-1">
                <div class="position-relative">
                </div>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../../public/images/logo.jpg" class="user-img" alt="Imagen de Usuario">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0"><?php echo $_SESSION['user_name'] ?></p>
                        <p class="designattion mb-0"> En línea</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item cursor-pointer" id="darkmode"><i class="bx bx-sun"></i><span>Tema</span></a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li><a class="dropdown-item"  href="../logout.php"><i class='bx bx-log-out-circle'></i><span>Salir</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>