<div id='header' <?php if(strcmp($_SERVER['PHP-SELF'], 'index.php') != 0) echo "style='background-color:rgba(52, 59, 64, 0.9)'" ?> >
		    <div class='container'>
			    <div id='logo' class='pull-left'>
                    <h1><a href='index.php'>CMDRs For Hire</a></h1>
				</div>
                    <nav id="nav-menu-container">
                        <ul class="nav-menu">
                            <li><a href='index.php'>Home</a></li>
                            <?php
                                if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                                    echo "<li><a style='color:#fff;' onclick=\"$('#loginModal').modal('show');\">Login/Signup</button></a></li>";
                                } else {
                                    echo "<li><a href='messages.php'>Messages</a></li><li class='menu-has-children'><a href='account.php'>" . $_SESSION['username'] . "</a>
                                        <ul>
                                            <li><a href='account.php'>Account</a></li>
                                            <li><a href='index.php?logout=true'>Logout</a></li>
                                        </ul>
                                    </li>";
                                }
                            ?>
                        </ul>
                    </nav>
			</div>
		</div>