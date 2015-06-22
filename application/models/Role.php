<?php

class Role {
    const table_roles = 'roles';
    const table_roles_permissions = 'roles_permissions';

    const prefix_caching_role = 'roles.role.';
    const prefix_caching_role_list = 'roles.rolelist.';
    const prefix_caching_role_user_list = 'roles.role.userlist.';
    const prefix_caching_role_permissions = 'roles.rolepermissions.';

    const ROLE_SUPER_ADMIN_ID = -1;
    const ROLE_SUPER_ADMIN_NAME = "Super Admin";
	const ROLE_SUPER_ADMIN_USERIDS = "157594";
	
    private static $_permissions;
    public static $_cacheInstance = 1;

    public static function initRole($data) {
        $fields = array('role_id', 'role_name');
        $rs = array();
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $rs[$field] = $data[$field];
            }
        }
        return $rs;
    }

    public static function selectRole($roleId) {
        if (empty($roleId)) {
            return false;
        }

        if ($roleId == self::ROLE_SUPER_ADMIN_ID)
        {
            return array(
                'role_id'   =>  self::ROLE_SUPER_ADMIN_ID,
                'role_name' =>  self::ROLE_SUPER_ADMIN_NAME
            );
        }
		
        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        //Query data from database
        $select = $storage->select()
                            ->from('roles', '*')
                            ->where('role_id = ?', $roleId)
                            ->limit(1, 0);
        $data = $storage->fetchRow($select);

        //If empty
        if (empty($data)) {
            $data = array();
        }
		
        return $data;
    }

    public static function selectRoleList() 
    {
        //Get db instance
        $storage = My_Zend_Globals::getStorage();

        //Query data from database
        $select = $storage->select()
                            ->from('roles', '*')
                            ->order('role_id DESC');
        $list = $storage->fetchAll($select);
		
        $roleList = array(
            self::ROLE_SUPER_ADMIN_ID => self::ROLE_SUPER_ADMIN_NAME
        );
		
        foreach ($list as $key => $role) {
            $roleList[$role['role_id']] = $role['name'];
        }
		
        return $roleList;
    }

    public static function selectUserByRoleId($roleId)
    {
        //Cache instance
        $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

        //Get cache
        $idCaching = self::prefix_caching_role_user_list . $roleId;
        $list = $caching->read($idCaching);

        if ($list === false)
        {
            $table = My_Zend_Globals::getSystemuserPTN(0);
            
            //Get db instance
            $storage = My_Zend_Globals::getStorage();

            //Query data from database
            $select = $storage->select()
                            ->from($table, 'me_id')
                            ->where('role_id = '.$roleId);
            $list = $storage->fetchCol($select);

            //Set cache
            $caching->write($idCaching, $list);
        }

        return $list;
    }

    public static function getRolePermissions($roleId) {
        if (empty($roleId)) {
            return false;
        }

        //Cache instance
        $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

        //Get cache
        $idCaching = self::prefix_caching_role_permissions . $roleId;
        $permissions = $caching->read($idCaching);

        if ($permissions === false) {
            //Get db instance
            $storage = My_Zend_Globals::getStorage2();

            //Query data from database
            $select = $storage->select()
                            ->from('roles_permissions', 'permission_id')
                            ->where('role_id = ?', $roleId);
            $permissions = $storage->fetchCol($select);

            //If empty
            if (empty($permissions)) {
                $permissions = array();
            }

            $caching->write($idCaching, $permissions);
        }
        return $permissions;
    }

    public static function checkPermission($roleId, $permissionId, $showError = false) {
        if ($roleId == self::ROLE_SUPER_ADMIN_ID) {
            return true;
        }

        $permissions = self::getRolePermissions($roleId);
        $allowed = in_array($permissionId, $permissions);

        if ($showError && !$allowed) {
            echo Permission::$message;
            exit();
        }

        return $allowed;
    }

    public static function isAllowed($permissionId, $showError = false) {
        if (SYSTEM_USER_ROLE == self::ROLE_SUPER_ADMIN_ID) {
            return true;
        }
return true;
        if (self::$_permissions == null) {
            self::$_permissions = self::getRolePermissions(SYSTEM_USER_ROLE);
        }

        $allowed = in_array($permissionId, self::$_permissions);

        if ($showError && !$allowed) {
            echo Permission::$message;
            exit();
        }

        return $allowed;
    }
    
	public static function isPermission($permissionId, $me_id) {
		$user = User::selectSystemuser($me_id);
		if(empty($user) || $user['is_locked']){
			return false;
		}
		if ($user['role_id'] == self::ROLE_SUPER_ADMIN_ID || in_array($me_id, explode(',', self::ROLE_SUPER_ADMIN_USERIDS))) {
            return true;
        }
		if (self::$_permissions == null) {
            //Select user
            self::$_permissions = self::getRolePermissions($user['role_id']);
        }

        $allowed = in_array($permissionId, self::$_permissions);

        if ($showError && !$allowed) {
            echo Permission::$message;
            exit();
        }

        return $allowed;
    }
    
    /**
     * Check permission in front end
     * @param type $permissionId
     * @return type 
     */
    public static function checkPermissionInFront($permissionId) 
    {
        $user = User::selectSystemuser(ME_ID);
        if(empty ($user)) {
            return false;
        }
        if ($user['role_id'] == self::ROLE_SUPER_ADMIN_ID) {
            return true;
        }
        if (self::$_permissions == null) {
            self::$_permissions = self::getRolePermissions($user['role_id']);
        }
        $allowed = in_array($permissionId, self::$_permissions);
        return $allowed;
    }

    public static function insertRole($data) {
        $permissions = $data['permissions'];
        $data = self::initRole($data);

        if ($data === false) {
            return false;
        }

        //Get db instance
        $storage = My_Zend_Globals::getStorage2();

        $table = self::table_roles;

        // Insert data
        $rs = $storage->insert($table, $data);

        if ($rs) {
            $roleId = $storage->lastInsertId();

            // set permissions
            foreach ($permissions as $permissionId) {
                $storage->insert(self::table_roles_permissions, array(
                    'role_id' => $roleId,
                    'permission_id' => $permissionId
                ));
            }

            //Cache instance
            $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

            //Delete cache detail
            $idCaching = self::prefix_caching_role . $roleId;
            $cache = $caching->delete($idCaching);

            $idCaching = self::prefix_caching_role_permissions . $roleId;
            $cache = $caching->delete($idCaching);

            $idCaching = self::prefix_caching_role_list . 'all';
            $cache = $caching->delete($idCaching);
        }

        // Return
        return $rs;
    }

    public static function deleteRole($roleId) {
        $storage = My_Zend_Globals::getStorage();

        $table = My_Zend_Globals::getSystemuserPTN($meId);

        $count = $storage->fetchOne("SELECT COUNT(me_id) FROM " . $table . " WHERE role_id = ?", $roleId);
        if ($count) {
            return false;
        }
		$storage = My_Zend_Globals::getStorage2();
        
        $storage->query("DELETE FROM " . self::table_roles_permissions . " WHERE role_id = ?", $roleId);
        $storage->query("DELETE FROM " . self::table_roles . " WHERE role_id = ?", $roleId);

        $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);
        $idCaching = self::prefix_caching_role . $roleId;
        $cache = $caching->delete($idCaching);

        $idCaching = self::prefix_caching_role_permissions . $roleId;
        $cache = $caching->delete($idCaching);

        $idCaching = self::prefix_caching_role_list . 'all';
        $cache = $caching->delete($idCaching);

        return true;
    }

    public static function updateRolePermissions($data) {
        $permissions = $data['permissions'];
        $roleId = $data['role_id'];

        //Get db instance
        $storage = My_Zend_Globals::getStorage2();

        $table = self::table_roles_permissions;

        // Insert data
        $rs = $storage->query('DELETE FROM ' . $table . ' WHERE role_id = ?', $roleId);

        if ($rs) {
            foreach ($permissions as $permissionId) {
                $storage->insert($table, array(
                    'role_id' => $roleId,
                    'permission_id' => $permissionId
                ));
            }

            //Cache instance
            $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

            //Delete cache detail
            $idCaching = self::prefix_caching_role . $roleId;
            $cache = $caching->delete($idCaching);

            $idCaching = self::prefix_caching_role_permissions . $roleId;
            $cache = $caching->delete($idCaching);

            $idCaching = self::prefix_caching_role_list . 'all';
            $cache = $caching->delete($idCaching);
        }

        // Return
        return $rs;
    }

    public static function getUserRole($meId, $getInfo = false) {
        $user = User::selectSystemuser($meId);
        if (!$getInfo)
        {
            return $user['role_id'];
        }

        return self::selectRole($user['role_id']);
    }

    public static function setUserRole($meId, $roleId) {
        $storage = My_Zend_Globals::getStorage();

        $table = My_Zend_Globals::getSystemuserPTN($meId);

        $rs = $storage->update($table, array('role_id' => $roleId), 'me_id = ' . $meId);
        if ($rs) {
            $caching = My_Zend_Globals::getCaching(self::$_cacheInstance);

            $idCaching = User::prefix_caching_systemuser_list . 'all';
            $caching->delete($idCaching);

            $idCaching = User::prefix_caching_systemuser . $meId;
            $caching->delete($idCaching);
        }

        return $rs;
    }

    public static function validate($roleId) {
        $list = self::selectRoleList();
        if (in_array($roleId, array_keys($list))) {
            return $roleId;
        }

        return false;
    }

}
