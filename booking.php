<?php include 'navigationBar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="ex_css.css">
  
  <title>Book Your Ride</title>
</head>

<body>
  <div class="container-center">
    <h1 class="b-h1">Book Your <span>Ride</span></h1>
  </div>

  <div class="car1">
    <div class="image1">
      <img src="images/img-3.png">
    </div>
    <div class="image1cont">
      <h1 class="image1Top" >Car for Every Pocket</h1>
      <p>It is a long established fact that a reader will be distracted by the readable content of
        a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
        distribution of letters, as It is a long established fact that a reader will be distracted by the readable c
      </p>
      <a class="book-btn" >Book Now</a>
    </div>
  </div>

  <div class="car2">
    <div class="image2cont">
        <h1 class="image2Top">Secure and Safer Rides</h1>
        <p>
            It is a long established fact that a reader will be distracted by the readable content
            of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
            distribution of letters, as It is a long established fact that a reader will be distracted by the readable.
        </p>
        <a class="book-btn2">Book Now</a>
    </div>
    <div class="image2">
        <img src="images/b2.jpg" alt="Secure and Safer Rides">
    </div>
</div>

<div class="car1">
    <div class="image1">
      <img src="images/img-3.png">
    </div>
    <div class="image1cont">
      <h1 class="image1Top" >Car for Every Pocket</h1>
      <p>It is a long established fact that a reader will be distracted by the readable content of
        a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
        distribution of letters, as It is a long established fact that a reader will be distracted by the readable c
      </p>
      <a class="book-btn" >Book Now</a>
    </div>
  </div>

  <div class="car2">
    <div class="image2cont">
        <h1 class="image2Top">Secure and Safer Rides</h1>
        <p>
            It is a long established fact that a reader will be distracted by the readable content
            of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal
            distribution of letters, as It is a long established fact that a reader will be distracted by the readable.
        </p>
        <a class="book-btn2">Book Now</a>
    </div>
    <div class="image2">
        <img src="images/b2.jpg" alt="Secure and Safer Rides">
    </div>
</div>
<script>
    // Function to add fade-out effect to all paragraphs
    function fadeOutParagraphs() {
        const paragraphs = document.querySelectorAll('.image1cont p, .image2cont p'); // Select paragraphs
        paragraphs.forEach(paragraph => {
            paragraph.classList.add('fade-out'); // Add the fade-out class
        });
    }

    // Example usage: Call the function when a button is clicked
    document.querySelector('.book-btn').addEventListener('click', fadeOutParagraphs);
</script>


</body>
</html>