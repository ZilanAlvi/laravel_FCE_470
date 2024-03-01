<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Club Connect Admin Panel</title>
  <link rel="shortcut icon" type="image/png" href="../admin/assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../admin/assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    @include('admin.sidebar')
    <!--  Sidebar End -->

    <!--  Main wrapper -->
    <div class="body-wrapper">

      <!--  Header Start -->
      @include('admin.header')
      <!--  Header End -->
      <div class="container-fluid">


        <!--  START  -->
        <h1>Two Factor Authentication</h1>
        <br>
        <br>

        <form method = "POST" action="/user/two-factor-authentication">
          @csrf

          @if (auth()->user()->two_factor_secret)
            @method('DELETE')

            <div>
              {!!auth()->user()->twoFactorQrCodeSvg()!!}
            </div>
            <br>
            <br>

            <button class="btn btn-danger">Disable</button>
          @else
            <button class="btn btn-primary">Enable</button>
          @endif

        </form>

        
        <!--  END  -->
        

  
  <!--  Header Start -->
      @include('admin.javascript')
      <!--  Header End -->
</body>

</html>