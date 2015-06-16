<?php
class ldap {
    private $ldap_host;
    private $ldap_user_domain;
    private $ldap_dn;
    private $ldap;

    function __construct ($ldap_host, $ldap_user_domain, $ldap_dn)
    {
        $this->ldap_host = $ldap_host;
        $this->ldap_dn = $ldap_dn;
        $this->ldap_user_domain = $ldap_user_domain;

        $this->ldap = ldap_connect($this->ldap_host);
    }

    public function authenticate($user, $password) {
        if($this->checkPassword($user, $password)) {
            $authorized_groups = $GLOBALS['config']['ldap_groups'];
            foreach ($authorized_groups as $group)
            {
                if ($this->checkGroupMembership($user, $group))
                {
                    return 'ok';
                }
            }
            return "permission";
        }
        else
        {
            return "password";
        }
    }

    private function checkPassword ($user, $password)
    {
        if($bind = @ldap_bind($this->ldap, $user . $this->ldap_user_domain, $password)) {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function checkGroupMembership ($user, $group)
    {
        $filter = "(sAMAccountName=" . $user . ")";
        $attr = array("memberof");
        $result = ldap_search($this->ldap, $this->ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
        $entries = ldap_get_entries($this->ldap, $result);

        $attr = array("cn", "mail", "sAMAccountName", "name");
        $result2 = ldap_search($this->ldap, $GLOBALS['config']['ldap_dn'], $filter, $attr) or exit("Unable to search LDAP server");
        $entries2 = ldap_get_entries($this->ldap, $result2);

        foreach($entries[0]['memberof'] as $grps) {
            $single_group = explode(',',$grps);

            if (substr($single_group[0],3) == $group)
            {
                $department = substr($group[0], 3);
                $_SESSION['department'] = $department;
                $_SESSION['login'] = true;
                $_SESSION['name'] = $entries2[0]["name"][0];
                return true;
            }
        }
        return false;
    }

    public function getFullName ($user)
    {
        $filter = "(sAMAccountName=" . $user . ")";
        $attr = array("cn", "mail", "sAMAccountName", "name");
        $result = ldap_search($this->ldap, $GLOBALS['config']['ldap_dn'], $filter, $attr) or exit("Unable to search LDAP server");
        $entries = ldap_get_entries($this->ldap, $result);

        return $entries[0]["name"][0];
    }

}
?>