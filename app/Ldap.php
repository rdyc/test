<?php

namespace App;

use Adldap\Connections\Provider;
use Adldap\Models\UserPasswordIncorrectException;
use Adldap\Models\UserPasswordPolicyException;
use Adldap\Objects\AccountControl;

class Ldap
{
    protected $provider;

    function __construct()
    {
        $config = [
            'domain_controllers' => ['172.16.20.15'],
            //'domain_controllers' => ['ieb-jktdc02-dev.ieb.go.id'],
            'base_dn' => 'OU=CCA_USER,DC=ieb,DC=go,DC=id',
            'admin_username' => 'ieb\eko.prayoga',
            'admin_password' => 'EXIMBANK01!',
            'port' => 389,
            //'port' => 636,
            'follow_referrals' => false,
            'use_ssl' => false,
            'use_tls' => false,
            'timeout' => 5,

            // Custom LDAP Options
            'custom_options' => [
                //LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_HARD
            ]
        ];

        $this->provider = new Provider($config);

        $this->connect();
        $this->get();
        //$this->create();
        //$this->change();
    }

    function connect()
    {
        echo "Connecting LDAP server " . implode(",", $this->provider->getConfiguration()->get('domain_controllers')) . " -> ";

        $this->provider->connect();

        echo "Connected " . ($this->provider->getConnection()->isUsingSSL() ? "with" : "without") . " SSL\n";
    }

    function get()
    {
        echo "Getting all AD users -> ";
        $collections = $this->provider->search()->all();

        //print_r($collections);
        $users = [];
        foreach ($collections as $item) {
            $users[] = $item->getAccountName();
        }

        echo "found (" . count($users) . ") users = " . implode(", ", $users) . "\n";
    }

    function create()
    {
        echo "Creating user -> ";

        $user = $this->provider->make()->user();

        // Set the user profile details.
        $user->setDn('CN=test,OU=CCA_USER,DC=ieb,DC=go,DC=id');
        $user->setUserPrincipalName('test@ieb.go.id');
        $user->setCommonName('test');
        $user->setAccountName('test');
        $user->setDisplayName('MasBro');
        $user->setCompany('test Inc. 2017');
        $user->setEmail('test@somewhere.com');

        // Save the new user.
        if ($user->save()) {
            // Enable the new user (using user account control).
            $user->setUserAccountControl(AccountControl::NORMAL_ACCOUNT);

            // Set new user password
            $user->setPassword('Password123!');

            // Riz, gini kali ya ndro..
            try {
                $user->changePassword('Password123!', 'Password123@', true);
            } catch (UserPasswordIncorrectException $e) {
                // Your old password is incorrect
                throw new \Exception($e->getMessage());
            } catch (UserPasswordPolicyException $e) {
                // Your new password does not match the password policy
                throw new \Exception($e->getMessage());
            }


            // Save the user.
            if ($user->save()) {
                $user = $this->provider->search()->findBy('CN', 'test');
                echo $user->getAccountName . "\n";
            }
        }
    }

    function change()
    {
        echo "Change password user -> ";

        $user = $this->provider->search()->findBy('CN', 'test');

        try {
            $user->changePassword('Password123!', 'Password123@', true);

            echo $user->getAccountName . "\n";
        } catch (UserPasswordIncorrectException $e) {
            throw new \Exception($e->getMessage());
        } catch (UserPasswordPolicyException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}