<?php
  require_once(__DIR__."/lib/consts.php");
  require_once(__DIR__."/lib/functs.php");
  require_once(__DIR__."/config/config.php");

  $error  = []; // has to be defined as an array to be used
  $result = null;
  if ("GET" === HTTP_METHOD) {
    $result = get_verified($error);
  } else {
    // unsupported HTTP method
    http_response_code(405);
    header("Allow: GET");
  }

  if ("GET" === HTTP_METHOD) {
?>
<!DOCTYPE html>
<html>
  <head>
    <style>table, th, td {border: 1px solid black; }</style>
    <title>Filmmakers for Future - Verified (GET)</title>
  </head>
  <body>
<?php
    if (is_array($result)) {
      if (0 < count($result)) {
?>
    <table>
      <tr>
        <th>Name</th>
        <th>Job</th>
        <th>Country</th>
        <th>City</th>
        <th>Website</th>
        <th>Type</th>
      </tr>
<?php
        foreach ($result as $result_item) {
?>
      <tr>
        <td><?= html($result_item[MAIL_NAME]) ?></td>
        <td><?= html($result_item[MAIL_JOB]) ?></td>
        <td><?= html($result_item[MAIL_CITY]) ?></td>
        <td><?= html($result_item[MAIL_COUNTRY]) ?></td>
        <td><?= html($result_item[MAIL_WEBSITE]) ?></td>
        <td><?= ($result_item[MAIL_ISCOMPANY]) ? "Company" : "Individual" ?></td>
      </tr>
<?php
        }
?>
    </table>
<?php
      } else {
?>
    There are currently no verified persons.
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
  }

