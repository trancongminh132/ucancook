<?php

class Permission 
{
    // Article
    const ARTICLE_ADD = 200;
    const ARTICLE_EDIT = 201;
    const ARTICLE_DELETE = 203;
    
    // category
    const CATEGORY_ADD = 101;
    const CATEGORY_EDIT = 102;
    const CATEGORY_DELETE = 103;
    const CATEGORY_VIEW = 104;

    // Name
    const NAME_VIEW = 300;
    const NAME_ADD = 301;
    const NAME_EDIT = 302;
    const NAME_DELETE = 303;
    
    // Tag
    const TAG_VIEW = 400;
    const TAG_ADD = 401;
    const TAG_EDIT = 402;
    const TAG_DELETE = 403;
    
    // user
    const USER_LOCK = 151;
    const USER_UNLOCK = 152;
   
    //admin
    const ADMIN_MANAGE = 500;
    
    // Product
    const PRODUCT_ADD = 600;
    const PRODUCT_EDIT = 601;
    const PRODUCT_LIST = 601;
    const PRODUCT_DELETE = 603;
    
    //BANNER
    const BANNER_ADD = 700;
    const BANNER_EDIT = 701;
    const BANNER_LIST = 702;
    const BANNER_DELETE = 703;
   	
    //ATTRIBUTE
    const ATTRIBUTE_ADD = 800;
    const ATTRIBUTE_EDIT = 801;
    const ATTRIBUTE_LIST = 802;
    const ATTRIBUTE_DELETE = 803;
    
    public static $details = array(      
        'Category' => array(
            self::CATEGORY_VIEW =>  'Xem danh sách / thông tin',
            self::CATEGORY_ADD => 'Thêm',
            self::CATEGORY_EDIT => 'Sửa',
            self::CATEGORY_DELETE => 'Xóa',
        ),
    	'Category' => array(
    		self::ADMIN_MANAGE =>  'Xem danh sách / thông tin',
    	)
    );
    public static $message = "Ban khong co quyen thuc hien lenh nay";
    private static $_list, $_nameList;

    public static function getPermissionList() {
        if (self::$_list == null) {
            $list = array();
            foreach (self::$details as $parent => $sub) {
                $list = array_merge($list, array_keys($sub));
            }
            self::$_list = $list;
        }

        return self::$_list;
    }

    public static function getPermissionNameList()
    {
        if (self::$_nameList == null) {
            $list = array();
            foreach (self::$details as $parent => $sub) {
                foreach ($sub as $id => $name)
                {
                    $list[$id] = $parent . ' - ' . $name;
                }

            }
            self::$_nameList = $list;
        }

        return self::$_nameList;
    }

    public static function getPermissionName($permissionId)
    {
        $nameList = self::getPermissionNameList();
        return $nameList[$permissionId];
    }

    public static function validate($permissions) {
        $list = self::getPermissionList();

        if (is_array($permissions))
        {
            $result = array_diff($permissions, array_diff($permissions, $list));
        }
        else
        {
            $result = in_array($permissions, $list)?$permissions:false;
        }
        return $result;
    }

}