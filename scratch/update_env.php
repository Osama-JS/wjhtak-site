<?php
$envPath = '.env';
$content = file_get_contents($envPath);
$content = preg_replace('/TBO_CLIENT_ID=.*/', 'TBO_CLIENT_ID=WjhatTest', $content);
file_put_contents($envPath, $content);
echo "TBO_CLIENT_ID updated in .env\n";
