<?php

$arr = [];

$code = <<<CODE
<?php

return {$arr};

CODE;

file_put_contents('ss.php', $code);
