<html lang='eng'>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="navigationnbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">"
    <title>Direct Drive</title>
</head>

<body>
    <nav>
        <div class="nav_container">
            <div class="logo">Direct Drive</div>
            <div class="menu">
                <ul>
                    <li class="menu_items"><a href="">Home</a></li>
                    <li class="menu_items"><a href="">Services</a></li>
                    <li class="menu_items"><a href="">Booking</a></li>
                    <li class="menu_items"><a href="">Gallery</a></li>
                    <li class="menu_items"><a href="">Blogs</a></li>
                    <li class="menu_items"><a href="">About us</a></li>
                    <li class="menu_items"><a href="">Why us</a></li>
                    <li class="menu_items"><a href="">Booking</a></li>
                    <li class="menu_items"><a href="Contact.html">Contact us</a></li>
                    <li class="menu_items"><a href="">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PICTURE SLIDER SECTION START-->
    <div class="banner_section">
        <div class="container">
            <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="5000">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                    <div class="item active">
                        <img src="b1.jpeg" alt="Image 1">
                    </div>
                    <div class="item">
                        <img src="img-2.png" alt="Image 2">
                    </div>
                    <div class="item">
                        <img src="img-3.jpg" alt="Image 3">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize carousel
            $('#myCarousel').carousel({
                interval: 2000 // Set interval to 5 seconds
            });
        });
    </script>
    <!-- PICTURE SLIDER SECTION End-->

    <!-- About US section -->
    <div class="aboutuscontainer">
        <div class="acontainner">
            <h1 class="main_text">About Us</h1>
            <p class="para">At Direct Drive, we provide premium transport services with a commitment to
                exceptional hospitality and customer care. Whether you're looking for a limo, bus, or SUV,
                our fleet can accommodate any event, from weddings to business logistics.
                Our professionally trained chauffeurs ensure punctuality and top-tier service,
                offering a complete experience beyond simple transportation. With customizable
                packages, we aim to meet all your logistical needs across the UAE.</p>
        </div>
    </div>
    <!-- About Us section end -->
<footer>
<div class="col-1">
            <h3>Useful Links</h3>
            <a href="">Home</a>
            <a href="">Services</a>
            <a href="">Booking</a>
            <a href="">Gallery</a>
            <a href="">Blogs</a>
            <a href="">About us</a>
            <a href="">Why us</a>
            <a href="">Booking</a>
        </div>
        <div class="col-2">
            <h3> REVIEW </h3>
            <form action="">
                <input type="text" placeholder="Your Feedback">
                <button type="submit">Submit</button>
            </form>
        </div>
        <div class="col-3">
            <h3>Follow Us</h3>
            <ul class="socials"></ul>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
            </ul>
        </div>
</footer>
    <!-- Footer Start -->
    <!-- Footer End -->
</body>

</html>