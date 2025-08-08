<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Drive</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Google Fonts (Poppins, Playfair Display, and Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <!-- Font Awesome for social media icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'navigationBar.php' ?> 

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
                    <img src="images/c4.jpg" alt="Image 1">
                </div>
                <div class="item">
                    <img src="images/img-2.png" alt="Image 2">
                </div>
                <div class="item">
                    <img src="images/img-3.jpg" alt="Image 3">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Code with Improved Styling -->
<footer>
    <div class="container">
        <div class="row">
            <!-- Address Section -->
            <div class="col-md-3 col-sm-6 address-section">
                <h4>Address</h4>
                <p>
                    It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters.
                </p>
            </div>

            <!-- Links Section -->
            <div class="col-md-3 col-sm-6 links-section">
                <h4>Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Booking</a></li>
                    <li><a href="#">Gallery</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Why Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <!-- Follow Us Section -->
            <div class="col-md-3 col-sm-6 follow-us-section">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Newsletter Section -->
            <div class="col-md-3 col-sm-6 newsletter-section">
                <h4>Newsletter</h4>
                <form>
                    <input type="email" placeholder="Enter Your Email" class="form-control">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
    </div>

    <!-- All Rights Reserved Section -->
    <div class="footer-bottom">
        <div class="container">
            <p>© 2025 Direct Drive. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize carousel
        $('#myCarousel').carousel({
            interval: 2000 // Set interval to 5 seconds
        });
    });
</script>

<style>
    /* Force background colors to match the page */
    .banner_section {
        background-color: #1A1A1A !important; /* Match the black background */
        margin: 0 !important;
        padding: 0 !important;
    }

    .container {
        background-color: #1A1A1A !important; /* Match the black background */
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important; /* Ensure it takes full width */
    }

    #myCarousel {
        background-color: #1A1A1A !important; /* Match the black background */
        margin: 0 !important;
        padding: 0 !important;
    }

    .carousel-inner {
        background-color: #1A1A1A !important; /* Match the black background */
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important; /* Prevent any overflow that might cause space */
    }

    .carousel-inner .item {
        background-color: #1A1A1A !important; /* Match the black background */
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important; /* Let the height be determined by the image */
        line-height: 0 !important; /* Remove any line-height spacing */
        overflow: hidden !important; /* Prevent any overflow */
    }

    .carousel-inner .item img {
        width: 100% !important;
        height: auto !important; /* Maintain aspect ratio */
        object-fit: cover !important; /* Ensure the image fills the container */
        display: block !important; /* Remove any inline-block spacing */
        margin: 0 !important;
        padding: 0 !important;
        background-color: #1A1A1A !important; /* Ensure the image background matches */
    }

    .carousel-indicators {
        bottom: 10px !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
    }

    .carousel-indicators li {
        background-color: #fff !important;
        border: none !important;
        width: 12px !important;
        height: 12px !important;
        border-radius: 50% !important;
    }

    .carousel-indicators .active {
        background-color: #FFD700 !important; /* Yellow for active indicator */
        width: 12px !important;
        height: 12px !important;
    }

    /* Override Bootstrap defaults to remove extra space */
    .carousel, .carousel-inner, .carousel-item {
        min-height: 0 !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Ensure the body background matches */
    body {
        background-color: #1A1A1A !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Poppins', sans-serif !important; /* Default font for the page */
    }

    /* Lower the footer slightly by adding margin */
    .banner_section + footer {
        margin-top: 30px !important; /* Add spacing to lower the footer */
        padding-top: 0 !important;
    }

    /* Footer styling with improved aesthetics */
    footer {
        background-color: #1A1A1A !important;
        margin: 0 !important;
        padding: 40px 0 0 0 !important; /* Adjusted padding to accommodate footer-bottom */
        width: 100% !important;
        color: #fff !important;
    }

    footer .container {
        background-color: #1A1A1A !important;
        margin: 0 auto !important;
        padding: 0 15px !important;
        max-width: 1200px !important; /* Limit the container width for better alignment */
    }

    /* Specific styling for Address, Links, Follow Us, and Newsletter sections */
    .address-section, .links-section, .follow-us-section, .newsletter-section {
        font-family: 'Roboto', sans-serif !important; /* Apply Roboto for body text */
    }

    .address-section h4, .links-section h4, .follow-us-section h4, .newsletter-section h4 {
        font-family: 'Playfair Display', serif !important; /* Apply Playfair Display for headings */
        color: #FFD700 !important; /* Yellow color for headings */
        margin-bottom: 20px !important;
        font-size: 22px !important; /* Slightly larger for elegance */
        font-weight: 700 !important; /* Bold headings */
        letter-spacing: 1.5px !important; /* Increased letter spacing for elegance */
        text-transform: capitalize !important; /* Capitalize instead of uppercase for a softer look */
    }

    .address-section p {
        color: #d1d1d1 !important; /* Slightly lighter gray for better contrast */
        font-size: 15px !important; /* Slightly larger for readability */
        line-height: 1.9 !important; /* Increased line height for readability */
        margin: 0 !important;
        padding-right: 15px !important; /* Add some padding for indentation */
        font-weight: 300 !important; /* Lighter weight for elegance */
    }

    .links-section ul {
        list-style: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .links-section ul li {
        margin-bottom: 12px !important; /* Increased spacing between list items */
    }

    .links-section ul li a {
        color: #d1d1d1 !important; /* Slightly lighter gray for better contrast */
        text-decoration: none !important;
        font-size: 15px !important; /* Slightly larger for readability */
        font-weight: 300 !important; /* Lighter weight for elegance */
        transition: color 0.3s ease, padding-left 0.3s ease, text-shadow 0.3s ease !important; /* Smooth transitions */
    }

    .links-section ul li a:hover {
        color: #FFD700 !important;
        padding-left: 5px !important; /* Slight indent on hover */
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.5) !important; /* Subtle glow on hover */
    }

    .follow-us-section .social-icons {
        display: flex !important;
        gap: 20px !important; /* Increased gap for better spacing */
        margin-top: 10px !important;
    }

    .follow-us-section .social-icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 40px !important; /* Larger icons */
        height: 40px !important;
        border-radius: 50% !important;
        background-color: #333 !important; /* Dark background for icons */
        color: #fff !important;
        font-size: 18px !important;
        text-decoration: none !important;
        transition: background-color 0.3s ease, transform 0.3s ease, color 0.3s ease !important; /* Smooth transitions */
    }

    .follow-us-section .social-icon:hover {
        background-color: #FFD700 !important;
        color: #000 !important;
        transform: scale(1.1) !important; /* Slight scale effect on hover */
    }

    .newsletter-section form {
        display: flex !important;
        flex-direction: column !important;
        gap: 15px !important; /* Increased gap for better spacing */
        margin-top: 10px !important;
    }

    .newsletter-section input[type="email"] {
        padding: 12px !important; /* Increased padding for better appearance */
        border: 1px solid #555 !important; /* Darker border */
        background-color: #2a2a2a !important; /* Darker background */
        color: #fff !important;
        font-size: 14px !important;
        font-family: 'Roboto', sans-serif !important; /* Apply Roboto to input */
        font-weight: 300 !important; /* Lighter weight for elegance */
        border-radius: 5px !important; /* Rounded corners */
        transition: border-color 0.3s ease !important;
    }

    .newsletter-section input[type="email"]::placeholder {
        color: #999 !important; /* Lighter placeholder text */
        font-family: 'Roboto', sans-serif !important; /* Apply Roboto to placeholder */
        font-weight: 300 !important;
    }

    .newsletter-section input[type="email"]:focus {
        border-color: #FFD700 !important;
        outline: none !important;
    }

    .newsletter-section .btn-primary {
        background-color: #FFD700 !important;
        border: none !important;
        padding: 12px !important; /* Increased padding */
        color: #000 !important;
        font-family: 'Playfair Display', serif !important; /* Apply Playfair Display to button */
        font-weight: 700 !important; /* Bold for emphasis */
        font-size: 14px !important;
        border-radius: 5px !important; /* Rounded corners */
        cursor: pointer !important;
        transition: background-color 0.3s ease, transform 0.3s ease !important;
        text-transform: capitalize !important; /* Capitalize for consistency with headings */
    }

    .newsletter-section .btn-primary:hover {
        background-color: #e6c200 !important;
        transform: scale(1.05) !important; /* Slight scale effect on hover */
    }

    /* Footer Bottom (All Rights Reserved) */
    .footer-bottom {
        background-color: #1A1A1A !important;
        padding: 20px 0 !important;
        border-top: 1px solid #333 !important; /* Subtle separator */
        margin-top: 30px !important; /* Space between main footer content and bottom */
    }

    .footer-bottom p {
        margin: 0 !important;
        padding: 0 !important;
        color: #999 !important; /* Lighter gray for less emphasis */
        font-size: 13px !important;
        text-align: center !important;
        font-family: 'Poppins', sans-serif !important; /* Keep Poppins for this section */
    }

    /* Responsive adjustments for footer */
    @media (max-width: 768px) {
        footer .col-md-3 {
            margin-bottom: 30px !important; /* Increased spacing for mobile */
        }

        footer .social-icons {
            gap: 15px !important;
        }

        footer .social-icon {
            width: 35px !important;
            height: 35px !important;
            font-size: 16px !important;
        }

        .footer-bottom p {
            font-size: 12px !important;
        }
    }

    @media (max-width: 480px) {
        .address-section h4, .links-section h4, .follow-us-section h4, .newsletter-section h4 {
            font-size: 20px !important;
        }

        .address-section p, .links-section ul li a {
            font-size: 14px !important;
        }

        .newsletter-section input[type="email"], .newsletter-section .btn-primary {
            font-size: 13px !important;
            padding: 10px !important;
        }

        .footer-bottom p {
            font-size: 11px !important;
        }
    }
</style>
</body>
</html>