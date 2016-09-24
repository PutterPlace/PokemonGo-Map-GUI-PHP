<?php
include('config.inc.php');
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?> - Statistics</title>

    <!-- Fav- & Apple-Touch-Icons -->
    <!-- Favicon -->
    <link rel="shortcut icon" href="./static/appicons/favicon.ico" type="image/x-icon">
    <!-- non-retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/114x114.png" sizes="57x57">
    <!-- non-retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/144x144.png" sizes="72x72">
    <!-- non-retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/152x152.png" sizes="76x76">
    <!-- retina iPhone pre iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/114x114.png" sizes="114x114">
    <!-- retina iPhone iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/120x120.png" sizes="120x120">
    <!-- retina iPad pre iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/144x144.png" sizes="144x144">
    <!-- retina iPad iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/152x152.png" sizes="152x152">
    <!-- retina iPhone 6 iOS 7 -->
    <link rel="apple-touch-icon" href="./static/appicons/180x180.png" sizes="180x180">

    <link rel="stylesheet" href="./static/dist/css/app.min.css">
    <link rel="stylesheet" href="./static/dist/css/statistics.min.css">
    <script src="./static/js/vendor/modernizr.custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.9.1/polyfill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/skel/3.0.1/skel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
      var centerLat = <?= $startingLat; ?>;
      var centerLng = <?= $startingLng; ?>;
    </script>
    <script src="./static/dist/js/map.min.js"></script>
</head>
<body>
        <!-- Header -->
        <header id="header">
            <a href="#nav">Options</a>
            <h1><a href=".."><?= $title ?></a></h1>
        </header>
        <nav id="nav">
                <div class="form-control">
                  <label for="duration">
                    <h3>Duration</h3>
                    <select id="duration" name="duration" class="input" onChange="window.stop();updateMap();">
                        <option value="1h" >Last Hour</option>
                        <option value="3h" >Last 3 Hours</option>
                        <option value="6h" >Last 6 Hours</option>
                        <option value="12h" >Last 12 Hours</option>
                        <option value="1d" SELECTED>Last Day</option>
                        <option value="7d" >Last 7 Days</option>
                        <option value="14d" >Last 14 Days</option>
                        <option value="1m" >Last Month</option>
                        <option value="3m" >Last 3 Months</option>
                        <option value="6m" >Last 6 Months</option>
                        <option value="1y" >Last Year</option>
                        <option value="all" >Map Lifetime</option>
                    </select>
                  </label>
                </div>
            
                <div class="form-control">
                  <label for="sort">
                    <h3>Sort</h3>
                    <select id="sort" name="sort" class="input" onChange="window.stop();updateMap();">
                        <option value="count" SELECTED>Count</option>
                        <option value="id" >Pokedex Number</option>
                        <option value="name" >Pokemon Name</option>
                    </select>
                  </label>
                </div>
            
                <div class="form-control">
                  <label for="order">
                    <h3>Order</h3>
                    <select id="order" name="order" class="input" onChange="window.stop();updateMap();">
                        <option value="asc" >Ascending</option>
                        <option value="desc" SELECTED>Descending</option>
                    </select>
                  </label>
                </div>
        </nav>
        <div class="totals">
            <h3 id="seen_header"></h3>
            <h1 id="seen_total"></h1>
        </div>
        <div id="loading"><img src="./static/images/loading.gif" alt="Loading"/></div>
        <div class="container" id="seen_container">
        </div>
        <div id="location_details" class="overlay">
            <div class="close" onclick="closeOverlay();"><img src="./static/images/close.png" alt="Close" /></div>
            <div class="location_header"><h3 id="location_header"></h3></div>
            <div class="content">
                <div id="times_list" style="display: none;"></div>
                <div id="location_map">
                    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $gmapsKey ?>&amp;libraries=visualization"></script>
                </div>
            </div>
        </div>
    <script src="./static/dist/js/app.min.js"></script>
    <script src="./static/dist/js/statistics.min.js"></script>
</body>
</html>
