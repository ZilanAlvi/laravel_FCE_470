<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ranking</title>
  <link rel="shortcut icon" type="image/png" href="../admin/assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../admin/assets/css/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <style>
    /* Internal CSS for card styling */
    .card {
      border: none;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-img-top {
      max-height: 200px;
      object-fit: cover;
    }

    .card-title {
      margin-bottom: 0.5rem;
      font-size: 1.25rem;
    }

    .card-title i {
      margin-right: 5px; /* Add space between the star and text */
    }

    .rank-text {
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .card-text {
      font-size: 0.9rem;
    }

    /* Flex layout for 3 cards in a row */
    .row {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -15px;
    }

    .col-lg-4 {
      flex: 0 0 calc(33.3333% - 30px);
      max-width: calc(33.3333% - 30px);
      padding: 0 15px;
      margin-bottom: 30px;
    }
  </style>
</head>

