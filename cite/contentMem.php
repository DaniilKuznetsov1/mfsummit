<?php
session_start();
echo '<body>';
echo '<h4 style="width: 90%; text-align: right; cursor: pointer" onclick="exitSession()">Выход</h4>';

if (isset($_SESSION["userName"])) {
  $userName = $_SESSION["userName"]; 
} else {
  $userName = '';
}

echo "<h2 style='width: 90%; text-align: center'>Добро пожаловать, $userName!</h2>";

echo <<<EOT
<script>
  function exitSession() {
    let postData = {username: '$userName'};
    fetch('/control.php', {
      method: 'POST',
      credentials: "include",
      body: JSON.stringify(postData),
      headers: { "Content-Type": "application/json"}
    })
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        console.log(response.status + response.statusText);
      }
    })
    .then((data) => {
      window.location.reload();
    })
  }
</script>
EOT;

echo '</body>';
echo '</html>';