<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>{{ $settings['site_title']->value }}</title>

  <style>
    .email-wrap {
      border-radius: 7px;
      font: 400 12px/24px "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
      overflow: hidden;
      width: 100%;
    }

    .top-part-wrap {background-color: #363636; padding: 50px 10px 30px 10px;}

    .top-part-wrap .header-title {
      color: white;
      font-size: 32px;
      font-weight: 500;
      line-height: 48px;
      text-align: center;
    }

    .top-part-wrap .header-title h1 {font-size: 1em; font-weight: inherit;}

    .content-wrap {
      background-color: white;
      font-size: 16px;
      font-weight: 400;
      line-height: 24px;
      padding: 20px 5%;
    }

    .content-wrap .content-text p {color: #47505e; text-align: center;}

    .footer-wrap {background-color: #f5f6f7; padding: 30px 0; text-align: center;}

    .copyright-wrap {font-size: 14px; padding: 15px 0;}

    .links ul {
      list-style: none;
      margin: 0;
      padding: 0;
      text-align: center;
    }
    .links li {display: inline; padding: 10px;}
    .links li a {text-decoration: none}
  </style>
</head>
<body>

<div class="email-wrap">
  <div class="top-part-wrap">
    <div class="header-title">
      <h1>Hi, {{ $settings['smtp_username']->value }}</h1>
    </div>
  </div>

  <div class="content-wrap">
    <div class="content-text">
      <p>This is a test email which ensures that your SMTP connection for CopeMember is working.</p>
    </div>
  </div>

  <div class="footer-wrap">
    <div class="copyright-wrap">&copy; <a href="mailto:support@copemember.com">support@copemember.com</a></div>
  </div>
</div>

</body>
</html>
