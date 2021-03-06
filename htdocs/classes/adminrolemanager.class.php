<?php
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class AdminRoleManager {

	private static $roles;

    public function __construct() {

		if (self::$roles == null) {
			$securityConfig = fetchJsonConfig("admin-security.conf");
			self::$roles = $securityConfig['roles'];
		}
	}

	function getRoleName($name) {
		return self::$roles[$name];
	}

	function getRolesStr($roles) {
		$rs = array();
		foreach ($roles as $r) {
			$rs[] = $this->getRoleName($r);
		}
		return implode(', ', $rs);
	}

	function getRoles() {
		return self::$roles;
	}

	function checkAllowed($rolesToCheck, $allowedRoles) {
		if (!is_array($rolesToCheck)) {
			$rolesToCheck = explode(",", $rolesToCheck);
		}
		if (!is_array($allowedRoles)) {
			$allowedRoles = explode(",", $allowedRoles);
		}
		return count(array_intersect($rolesToCheck, $allowedRoles)) > 0;
	}
}
