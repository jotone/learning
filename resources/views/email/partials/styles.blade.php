<style>
  body, html {background-color: #F4F6FC}

  .email-wrap {
    border-radius: 7px;
    font: 400 12px/24px "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    overflow: hidden;
    width: 100%;
    display: block;
  }

  .email-wrap .logo {
    height: 100px;
    width: auto;
    max-width: 375px;
    margin: 0 auto;
  }

  .email-wrap .logo img {
    height: 100%;
    width: 100%;
    object-fit: contain;
  }

  .top-part-wrap {padding: 10px 10px 30px 10px}

  .top-part-wrap .header-title {
    line-height: 1.25;
    text-align: center;
    text-transform: uppercase;
    width: 60%;
    margin: 0 auto 30px;
  }

  .top-part-wrap .header-title h1 {
    font-size: 24px;
    font-weight: 500;
    color: #000;
  }

  .top-part-wrap .header-text {
    font-size: 16px;
    line-height: 24px;
    margin: 0 auto;
    text-align: center;
    width: 60%;
  }

  .content-wrap {
    background-color: white;
    border: 1px solid #D6E0FF;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
    margin: 0 auto 80px auto;
    padding: 20px 5%;
    width: 75%;
  }

  .content-wrap .content-text, .content-wrap .content-misc {color: #909BBF}

  .content-wrap .content-misc {
    margin: 0 auto;
    position: relative;
    text-align: center;
    width: 75%;
  }

  .content-wrap .content-text a, .content-wrap .content-misc a, .footer-wrap a {color: #1155cc;text-decoration: none}

  .content-wrap .content-text a:hover, .content-wrap .content-misc a:hover {text-decoration: underline}

  .email-wrap .button {
    background-color: #005AFF;
    border: 1px solid #09a59a;
    border-radius: 6px;
    color: white;
    font-size: 14px;
    font-weight: 600;
    padding: 13px 30px;
    text-decoration: none;
  }

  .email-wrap .button.custom {
    background-color: {{ $settings['primary_btn']['value']->normal->{'background-color'} }};
    color: {{ $settings['primary_btn']['value']->normal->{'color'} }};
    border-color: {{ $settings['primary_btn']['value']->normal->{'border-color'} }}
  }

  .content-wrap .button-wrap {padding: 14px 0;text-align: center}

  .footer-wrap {padding: 30px 0;text-align: center;}

  .footer-wrap .address {
    color: #fff;
    margin: 35px 0;
    font-size: 14px;
  }

  .footer-wrap .social-links {display: inline-block;}

  .footer-wrap .extra-links {
    margin-top: 30px;
    color: #fff;
    display: block;
  }

  .footer-wrap .extra-links a {
    text-decoration: none;
    color: #fff;
    font-size: 14px;
  }

  .footer-wrap .social-links .link {
    margin-right: 11px;
    height: 15px;
    width: 15px;
    float: left;
  }

  .footer-wrap .social-links .link a {
    text-decoration: none
  }

  .footer-wrap .social-links .link a svg {
    width: 100%;
    height: 100%;
    display: block;
  }

  .content-wrap .follow-wrap {
    padding: 25px 0;
    text-align: center;
    width: 70%;
    margin: 0 auto;
  }

  .content-wrap .follow-wrap p {
    padding: 0;
    margin: 0;
    color: #909BBF;
  }

  .content-wrap .follow-wrap a {
    word-break: break-all;
  }

  .copyright-wrap {
    font-size: 14px;
    padding: 15px 0;
  }

  .links ul {
    list-style: none;
    margin: 0;
    padding: 0;
    text-align: center;
  }

  .links li {
    display: inline;
    padding: 10px;
  }

  .links li a {text-decoration: none}


  @media only screen and (max-width: 600px) {
    .top-part-wrap .header-text {
      font-size: 16px;
      line-height: 1.25;
      text-align: center;
    }

    .email-wrap .logo {
      height: 100px;
      width: auto;
      max-width: 245px;
      margin: 0 auto;
    }

    .top-part-wrap .header-title {
      line-height: 1.25;
      text-align: center;
      text-transform: uppercase;
      width: 100%;
      margin: 0 auto;
    }

    .top-part-wrap .header-title h1 {font-size: 20px}
  }
</style>