<?php include 'navigationBar.php' ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ex_css.css">
    <title>About us</title>
</head>
<body>
    <div class="about-heading">
        <h1>About  <span>Us</span> </h1>
    </div>
    <div class="a-container">
        <section class="a-about">
            <div class="a-image">
                <img src="images/matt.jpg" alt="">
            </div>
            <div class="about-content">
              <p>
              We at Direct Drive are dedicated to providing you with the best transport services in the region. Our fleet of vehicles and professional drivers ensure a comfortable and safe journey for all our customers.
              </p>
              <!-- Hidden additional text -->
              <p class="more-text" style="display: none;">
              Direct Drive was founded to offer premium logistical services that come with the highest standards of hospitality at affordable prices for a plethora of purposes.
                  
Most logistical services like Concord rentals are geared more towards the conveyance from a pickup point to a drop-off point and in most cases a to and fro origin or a destination. At Direct Drive, we do not believe that transport services or Concord service should be confined to the conveyance alone. When anyone looks for a Direct Drive company, whether it is for business purposes or for personal purposes, it is natural to expect something more than what is obvious. That is precisely where Concord Traansport not only steps in but excels in unprecedented fashion.

We offer you a complete experience which abides by the highest standards of hospitality and service delivery. Our infrastructure and fleet of Concord ensures that you would always get what you want. You may want a stretch Concord, a limo bus or a limo SUV. We can attend to any need of yours. You may have 10 guests, 20 guests or 40 guests and if you need multiple vehicles, we have the ability to offer you all such Concord services.

Our objective is not just to get a limo drive up to your pickup place on time and drop you at your destination within the promised time. Beyond impeccable punctuality, we offer you chauffeurs who have been trained on customer service. They are professional, polite, helpful and friendly. If you have a question, if you intend to know something and if you need any assistance about anything pertaining to the city, places or something in general, every personnel in our company, from the customer service executives to the chauffeurs, all would be ardent to help you.

Direct Drive caters to myriad requirements of yours. You can take care of your clients, stakeholders of business partners with our Concord service. You can entrust us with the entire logistical requisites of an event. Our Concord can be decked up for weddings, sightseeing tours across Dubai, Sharjah, Abu Dhabi and all the Emirates in UAE. We can offer you specials depending on what you prefer. We also have various types of packages based on the number of hours you need our Concord service for and what kind of vehicles, specials or services that you would want.

At Direct Drive, you would experience the finest Concord service you have ever encountered.
              </p>
              <a href="javascript:void(0)" class="readMore" onclick="toggleText()">Read More</a>
            </div>
        </section>
    </div>
    <?php include 'footerSection.php' ?>

    <!-- JavaScript to toggle the text visibility -->
    <script>
        function toggleText() {
            var moreText = document.querySelector('.more-text');
            var readMoreButton = document.querySelector('.readMore');
            
            if (moreText.style.display === "none") {
                moreText.style.display = "block";
                readMoreButton.textContent = "Read Less"; // Change button text
            } else {
                moreText.style.display = "none";
                readMoreButton.textContent = "Read More"; // Revert button text
            }
        }
    </script>
</body>
</html>
