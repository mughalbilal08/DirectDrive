<?php include 'navigationBar.php' ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ex_css.css">
    <title>Contact Us</title>
</head>
<body class="Mnb">
  <div class="c-container">
    <div class="contact_data2">
      <ul>
        <li>
          <i class="fa-solid fa-envelope"></i>
          <strong>Location:</strong>
          <p>A201 Lahore, Pakistan</p>
        </li>
        <li>
          <i class="fa-solid fa-envelope"></i>
          <strong>Email:</strong>
          <p>directdrive@gmail.com</p>
        </li>
        <li>
          <i class="fa-solid fa-envelope"></i>
          <strong>Call:</strong>
          <p>111 577 577</p>
        </li>
      </ul>
      <div class="map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d108844.56419304041!2d74.33525967444464!3d31.513374530630475!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3919050e085e7535%3A0xfbf788dae725f245!2sLahore%20Cantt.%2C%20Lahore%2C%20Punjab%2C%20Pakistan!5e0!3m2!1sen!2s!4v1678804785529!5m2!1sen!2s"
          width="600" height="450" style="border: 0" allowfullscreen="" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
    <div class="contact_data">
      <h2>Contact Me</h2>
      <form action="#">
        <label for="">Name</label> <!-- TEXT TEXTBOX K UPER AYEGA  -->
        <input type="text" />
        <label for="">Email</label>
        <input type="email" />
        <label for="">Subject</label>
        <input type="text" />
        <label for="">Message</label>
        <textarea name="" id="" cols="30" rows="05"></textarea>
        <button>Send Message</button>
      </form>
    </div>
  </div>
</body>
</html>