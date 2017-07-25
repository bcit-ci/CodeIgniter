<div class="navbar-wrapper">
    <div class="container-fluid">
        <nav class="navbar navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Logo</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#" class="">Home</a></li>
						<li><a href="<?php echo site_url('events'); ?>" class="">Events</a></li>
						<!--
                        <li class=" dropdown">
                            <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Departments <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class=" dropdown">
                                    <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">View Departments</a>
                                </li>
                                <li><a href="#">Add New</a></li>
                            </ul>
                        </li>
                        <li><a href="addnew.html">Add New</a></li>
                        <li class=" dropdown"><a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Managers <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">View Managers</a></li>
                                <li><a href="#">Add New</a></li>
                            </ul>
                        </li>
                        <li class=" dropdown"><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Staff <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">View Staff</a></li>
                                <li><a href="#">Add New</a></li>
                                <li><a href="#">Bulk Upload</a></li>
                            </ul>
                        </li>
                        <li class=" down"><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">HR <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Change Time Entry</a></li>
                                <li><a href="#">Report</a></li>
                            </ul>
                        </li>
						-->
                    </ul>
                    <ul class="nav navbar-nav pull-right">
						<?php if($this->session->userdata('isUserLoggedIn')){ ?>
						<li><a href="<?php echo site_url('add-event'); ?>">Create Event</a></li>
                        <li class=" dropdown"><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Signed in as  <span class="caret"></span></a>
                            <ul class="dropdown-menu">
								<li><a href="#"><?php echo $this->session->userdata('name'); ?></a></li>
                                <li><a href="<?php echo site_url('change-password'); ?>">Change Password</a></li>
                                <li><a href="<?php echo site_url('profile'); ?>">My Profile</a></li>
                            </ul>
                        </li>
                        <li class=""><a href="logout">Logout</a></li>
						<?php } else { ?>
						<li><a href="<?php echo site_url('login'); ?>" class="">Login</a></li>
						<li><a href="<?php echo site_url('register'); ?>" class="">Register</a></li>
						<?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>