<?php
header('WWW-Authenticate: Basic realm="registro"');
header('HTTP/1.0 401 Unauthorized');
include "testa_html.inc";
?>
<div id="main">
<br />
<br />
<dl><dt>Logout effettuato con successo</dt></dl>
</div>
</body>
</html>

