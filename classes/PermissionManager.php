<?php
/**
 * Mumble PHP Interface by Kissaki
 * Released under Creative Commons Attribution-Noncommercial License
 * http://creativecommons.org/licenses/by-nc/3.0/
 * @author Kissaki
 */

/**
 * Permissionmanager for managing permissions,
 * asking for permission…
 */
class PermissionManager
{
	private static $instance;
	
	/**
	 * Get the object of the PermissionManager, specific to the section
	 * @return PermissionManager_admin
	 */
	public static function getInstance()
	{
		if(self::$instance == null){
			$section = HelperFunctions::getActiveSection();
			if (class_exists('PermissionManager_' . $section)) {
				eval('self::$instance = new PermissionManager_' . $section . '();');
			} else {
				// TODO: transl
				// TODO: errormsg
				echo 'Unknown Permission Manager';
			}
		}
		return self::$instance;
	}
	
}

/**
 * PermissionManager for admin section
 */
class PermissionManager_admin
{
	private $perms;
	private $isGlobalAdmin;
	
	public function __construct()
	{
		if (SessionManager::getInstance()->isAdmin()) {
			$aid = SessionManager::getInstance()->getAdminID();
			$admin = DBManager::getInstance()->getAdmin($aid);
			$this->isGlobalAdmin = $admin['isGlobalAdmin'];
			$this->perms = DBManager::getInstance()->getAdminGroupPermissionsByAdminID($aid);
		} else {
			$this->isGlobalAdmin = false;
			$this->perms = DBManager::$defaultAdminGroupPerms;
		}
	}
	
	/**
	 * Is global admin?
	 * Can administrate all servers, add and remove virtual servers
	 * @return boolean
	 */
	public function isGlobalAdmin()
	{
		return $this->isGlobalAdmin;
	}
	
	/**
	 * Can start and stop servers?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanStartStop($sid)
	{
		return $this->isGlobalAdmin || $this->perms['startStop'];
	}
	
	/**
	 * Can edit the (virtual) servers (config) settings?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanEditConf($sid)
	{
		return $this->isGlobalAdmin || $this->perms['editConf'];
	}
	
	/**
	 * Can generate a new superuser password?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanGenSuUsPW($sid)
	{
		return $this->isGlobalAdmin || $this->perms['genSuUsPW'];
	}
	
	/**
	 * Can view registrations / accounts on the server?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanViewRegistrations($sid)
	{
		return $this->isGlobalAdmin || $this->perms['viewRegistrations'];
	}
	
	/**
	 * Can edit user accounts?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanEditRegistrations($sid)
	{
		return $this->isGlobalAdmin || $this->perms['editRegistrations'];
	}
	
	/**
	 * Can create channels, Move users?
	 * @return boolean
	 */
	public function serverCanModerate($sid)
	{
		return $this->isGlobalAdmin || $this->perms['moderate'];
	}
	
	/**
	 * Can kick online users?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanKick($sid)
	{
		return $this->isGlobalAdmin || $this->perms['kick'];
	}
	
	/**
	 * Can ban users?
	 * @param $sid
	 * @return boolean
	 */
	public function serverCanBan($sid)
	{
		return $this->isGlobalAdmin || $this->perms['ban'];
	}

	/**
	 * @return boolean
	 */
	public function serverCanEditChannels()
	{
		return $this->isGlobalAdmin || $this->perms['channels'];
	}
	
	/**
	 * @return boolean
	 */
	public function serverCanEditACLs()
	{
		return $this->isGlobalAdmin || $this->perms['acls'];
	}
	
	/**
	 * @return boolean
	 */
	public function serverCanEditAdmins()
	{
		return $this->isGlobalAdmin || $this->perms['admins'];
	}
}

?>