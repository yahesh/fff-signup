<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  $result = null;
  if ("GET" === HTTP_METHOD) {
    $result = get_subscribed($error);

    $countries = [];
    if (is_array($result)) {
      foreach ($result as $result_item) {
        // initialize a new country as an array
        if (!array_key_exists(strtolower(trim($result_item[MAIL_COUNTRY])), $countries)) {
          $countries[strtolower(trim($result_item[MAIL_COUNTRY]))] = trim($result_item[MAIL_COUNTRY]);
        }
      }

      // sort by array keys
      ksort($countries);
    }
  } elseif ("POST" === HTTP_METHOD) {
    $result = send_newsletter($_POST, $error);
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET, POST");
  }

  if ("GET" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <style>input, select { display : block; }</style>
    <title>Filmmakers for Future - Send (GET)</title>
  </head>
  <body>
<?php
    if (is_array($countries)) {
      if (0 < count($countries)) {
?>
    <form action="<?= html($_SERVER['REQUEST_URI']) ?>" method="post">
      <input type="password" name="password" required maxlength="256" placeholder="Password*">
      <select name="country" required>
        <option value="" disabled selected>Choose option...</option>
        <option value="">ALL COUNTRIES</option>
<?php
        foreach ($countries as $key => $value) {
?>
          <option value="<?= html($key) ?>"><?= html($value) ?></option>
<?php
        }
?>
      </select>
      <input type="text" name="subject" required maxlength="256" placeholder="Subject*" value="<?= html(NEWSLETTER_MAIL_SUBJECT) ?>">
      <textarea name="message" required rows="5" placeholder="Message*"><?= html(NEWSLETTER_MAIL_BODY) ?></textarea>
      <input type="submit" value="Send">
    </form>
<?php
      } else {
?>
    There are currently no verified persons subscribed to the newsletter.
<?php
      }
    } else {
?>
    <b>Unfortunately, an error has occured<?=  (array_key_exists(ERROR_OUTPUT, $error)) ? ":</b> ".html($error[ERROR_OUTPUT]) : ".</b>" ?><br>
    Please try again later or <a href="/contact">contact us</a> directly.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
    }
?>
  </body>
</html>
<?php
  } elseif ("POST" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Filmmakers for Future - Send (POST)</title>
    <script>
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
      }
    </script>
  </head>
  <body>
<?php
    if ($result) {
?>
    The newsletter has been sent.
<?php
    } else {
?>
    <b>Unfortunately, an error has occured<?=  (array_key_exists(ERROR_OUTPUT, $error)) ? ":</b> ".html($error[ERROR_OUTPUT]) : ".</b>" ?><br>
    Please try again later or <a href="/contact">contact us</a> directly.
    <?= (array_key_exists(ERROR_ID, $error)) ? "<br>Please provide the following error id when contacting us about this issue: ".html($error[ERROR_ID]) : "" ?>
<?php
    }
?>
  </body>
</html>
<?php
  }

