<!DOCTYPE HTML>
<html lang="en">

<head>
  <title><?php echo $titleTag; ?></title>
  <meta charset="utf-8" />
  <meta name="msvalidate.01" content="<?php echo $this->Settings->show('Site.Bing.verification_code'); ?>" />
  <meta name="robots" content="NOODP" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php
  echo $this->Html->canonical();
  echo $this->Html->meta('icon');
  echo $this->fetch('meta');
  ?>
  <?php
  if (isset($extraHeaderCode)):
    echo $extraHeaderCode;
  endif;
  ?>
  <style>
    body {
      text-align: center;
      background: #eee;
      padding: 5vw;
      font-family: sans-serif;
    }

    .wrapper {
      width: 100%;
      background: #fff;
      color: #333;
      border-radius: .5em;
      padding: 1.875em;
      margin: 0 auto;
      box-sizing: border-box;
      max-width: 61.25em;
      /* 980/16 */
      border: 1px solid #999;
      box-shadow: 1px 1px 10px 0 rgba(0, 0, 0, .35);
    }

    .content {
      padding: 1.875em;
    }

    p {
      font-size: 1.125em;
      line-height: 1.5em;
      margin: 0 0 1.5em;
    }

    p:last-child {
      margin: 0;
    }

    .contact-info {
      padding: 2.5em 0;
      /* 30/12 */
      border-top: 1px solid #ddd;
      border-bottom: 1px solid #ddd;
    }

    footer {
      padding: 2.5em 0 0;
      /* 30/12 */
      font-size: .75em;
    }

    span {
      display: inline-block;
      vertical-align: middle;
      margin: 0 .5em;
      background: #ddd;
      width: 1px;
      height: 1em;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="content" id="content">
      <img src="/img/admin/company-logo.png" alt="<?php echo $this->Settings->show('Site.name'); ?>">

      <h1>Temporarily Down for Maintenance</h1>

      <p>Sorry for the inconvenience but we are preforming some scheduled maintenance to the website.</p>
      <p>We'll be back online shortly. Thank you for your patience.</p>

      <?php
      if (isset($pageIntro)) {
        echo $pageIntro;
      }
      echo $this->Session->flash();
      echo $this->fetch('content');
      ?>
    </div>

    <div class="contact-info">
      <p>
        <strong><?php echo $this->Settings->show('Site.name'); ?></strong><br><?php echo $this->Settings->show('Site.Contact.address'); ?><span></span><?php echo $this->Settings->show('Site.Contact.city'); ?>,
        <?php echo $this->Settings->show('Site.Contact.province_state'); ?>
        <?php echo $this->Settings->show('Site.Contact.postal_zip'); ?><br>
        <?php
        if ($this->Settings->show('Site.Contact.phone') != "") {
          echo "Phone: " . $this->Settings->show('Site.Contact.phone');
        }

        if ($this->Settings->show('Site.Contact.phone') != "" && $this->Settings->show('Site.Contact.email') != "") {
          echo "<span></span>";
        }

        if ($this->Settings->show('Site.Contact.email') != "") {
          echo "Email: <a href='mailto:" . $this->Settings->show('Site.Contact.email') . "'>" . $this->Settings->show('Site.Contact.email') . "</a>";
        }
        ?>
      </p>
    </div>

    <footer>
      Copyright &copy; <?php echo $this->Copyright->year(); ?> <?php echo $this->Copyright->name(); ?> <span></span> A
      website by
      <?php echo $this->Html->link('Radar Hill Web Design', 'http://www.radarhill.com', array('target' => '_blank', 'title' => 'www.radarhill.com')); ?>
      <span></span> The content of this website is the responsibility of the website owner.
    </footer>
  </div>
  <?php
  if (isset($extraFooterCode)):
    echo $extraFooterCode;
  endif;
  ?>
</body>

</html>