<?php

namespace App\Ldap;

use App\Models\User as LocalUser;
use LdapRecord\Models\ActiveDirectory\User as LDAPUser;

class AttributeHandler
{
    public function handle(LDAPUser $ldap, LocalUser $local)
    {
        $local->name = $ldap->getFirstAttribute('cn');
        $local->username = $ldap->getFirstAttribute('samaccountname');
        $local->nopeg = $ldap->getFirstAttribute('samaccountname');
        $local->email = $ldap->getFirstAttribute('mail');
    }
}