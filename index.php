<?php

use App\Email;
use App\Ldap;

require_once __DIR__ . '/vendor/autoload.php';

try {
    echo "Start testing LDAP\n\n";

    new Ldap();
    new Email();


} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
} finally {
    echo "\nEnd testing LDAP\n";
}