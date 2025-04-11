<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>About Us - Snappy Boys Photography</title>
  <!-- Bootstrap -->
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
  <!-- Custom Style -->
  <link rel="stylesheet" href="assets/css/style.css" type="text/css">
  <!-- FontAwesome Font Style -->
  <link href="assets/css/font-awesome.min.css" rel="stylesheet">

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">

  <style>
    body {
      font-family: 'Lato', sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }

    .errorWrap {
      padding: 10px;
      margin: 0 0 20px 0;
      background: #fff;
      border-left: 4px solid #dd3d36;
      box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
    }

    .succWrap {
      padding: 10px;
      margin: 0 0 20px 0;
      background: #fff;
      border-left: 4px solid #5cb85c;
      box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
    }

    .page-header {
      background: #343a40;
      padding: 50px 0;
      color: #fff;
      text-align: center;
    }

    .page-header h1 {
      font-size: 40px;
      font-weight: 700;
    }

    .about_us {
      padding: 60px 0;
    }

    .about_us h3 {
      font-size: 32px;
      font-weight: 700;
      color: #343a40;
    }

    .about_us p {
      font-size: 16px;
      line-height: 1.6;
      color: #555;
    }

    .about_us h4 {
      font-size: 24px;
      color: #343a40;
      margin-top: 20px;
    }

    .about_us ul {
      list-style: none;
      padding-left: 0;
    }

    .about_us ul li {
      font-size: 16px;
      line-height: 1.6;
      color: #555;
    }

    .about_us ul li strong {
      color: #343a40;
    }

    /* Removing Contact Section */
    .col-md-6.contact_info {
      display: none;
    }

    .dark-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.4);
    }
  </style>
</head>

<body>

  <!-- Start Switcher -->
  <?php include('includes/colorswitcher.php');?>
  <!-- /Switcher -->  

  <!--Header-->
  <?php include('includes/header.php');?>
  <!-- /Header --> 

  <!--Page Header-->
  <section class="page-header aboutus_page">
    <div class="container">
      <div class="page-header_wrap">
        <div class="page-heading">
          <h1>About Us</h1>
        </div>
        <ul class="coustom-breadcrumb">
          <li><a href="#">Home</a></li>
          <li>About Us</li>
        </ul>
      </div>
    </div>
    <!-- Dark Overlay-->
    <div class="dark-overlay"></div>
  </section>
  <!-- /Page Header--> 

  <!--About Us-->
  <section class="about_us section-padding" style="padding: 60px 0; background: url('../images/about-background.jpg') no-repeat center center; background-size: cover; color: #fff;">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h3>Welcome to Snappy Boys Photography</h3>
          <p>At Snappy Boys Photography, every click tells a unique story! We are a team of passionate photographers dedicated to capturing the special moments of your life with an artistic touch. Whether it’s a wedding, a family portrait, a corporate event, or a product shoot, we specialize in creating stunning visuals that reflect the essence of every occasion.</p>

          <h4>Our Story</h4>
          <p>Founded by a group of photography enthusiasts, Snappy Boys Photography started as a small venture with a big dream – to make every moment unforgettable through our lenses. Over the years, we’ve grown into a trusted name in the photography industry, known for our creativity, professionalism, and dedication to our clients.</p>
          
          <h4>What We Do</h4>
          <ul>
            <li><strong>Wedding Photography</strong>: Capturing the magic of your special day with intimate, candid, and beautifully posed moments.</li>
            <li><strong>Event Photography</strong>: Documenting conferences, parties, and corporate events with professional flair.</li>
            <li><strong>Portrait Photography</strong>: From family portraits to professional headshots, we specialize in making you look your best.</li>
            <li><strong>Product Photography</strong>: Showcasing your products in the most captivating way to attract customers and elevate your brand.</li>
            <li><strong>Custom Photography Packages</strong>: Tailored services to meet your specific needs and budget.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- /About Us--> 

  <!--Footer -->
  <?php include('includes/footer.php');?>
  <!-- /Footer--> 

  <!--Back to top-->
  <div id="back-top" class="back-top">
    <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a>
  </div>
  <!--/Back to top--> 

  <!-- Scripts --> 
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/interface.js"></script> 
</body>
</html>
