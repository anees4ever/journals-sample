<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    $templateRoot= App::$config['documentRoot']."/template";
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Journal Entry</title>

    <!-- Bootstrap -->
    <link href="<?=$templateRoot;?>/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?=$templateRoot;?>/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?=$templateRoot;?>/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
   <link href="<?=$templateRoot;?>/vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?=$templateRoot;?>/build/css/custom.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="<?=$templateRoot;?>/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
   <script src="<?=$templateRoot;?>/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <div class="site_title"><i class="fa fa-money"></i> <span>Journals</span></div>
            </div>

            <div class="clearfix"></div>

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>Menu</h3>
                <ul class="nav side-menu">
                  <li class="x-active"><a href="<?=App::$config['documentRoot'];?>/journals"><i class="fa fa-home"></i> Journals</a></li>
                  <li><a href="<?=App::$config['documentRoot'];?>/journals/entry"><i class="fa fa-plus"></i> New Journal</a></li>
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <div class="nav toggle">
                  <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>
                <nav class="nav navbar-nav">
              </nav>
            </div>
          </div>
        <!-- /top navigation -->