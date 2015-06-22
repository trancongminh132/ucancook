<?php
global $_CONTROLLERS;

$_CONTROLLERS = array (
	'category' => array ('id' => 1, 'name' =>  'Quản lý danh mục', 'description' => 'Quản lý thông tin danh mục', 'link' => '/adm/category/category-list'),
	'product'  => array ('id' => 2, 'name' =>  'Quản lý sản phẩm',  'description' => 'Quản lý thông tin sản phẩm', 'link' => '/adm/product'),
	'banner'   => array ('id' => 3, 'name' =>  'Quản lý banner', 'description' => 'Quản lý thông tin về banner', 'link' => '/adm/banner'),
	'index'    => array ('id' => 6, 'name' =>  'Dashboard',   'description' => 'Thống kê toàn website', 'link' => '/adm'),
	'attribute'  => array ('id' => 7, 'name' =>  'Quản lý thuộc tính',   'description' => 'Quản lý thông tin thuộc tính', 'link' => '/adm/attribute'),
	'admin'    => array ('id' => 10,'name' =>  'Account Management',  'description' => 'Manage system Admin, User', 'link' => '/adm/admin'),
	'contact'    => array ('id' => 11,'name' =>  'Quản lý liên hệ',  'description' => 'Quản lý thông tin liên hệ', 'link' => '/adm/contact'),
	'article'  => array ('id' => 12,'name' =>  'Article Management',  'description' => 'Manage Info Article, Article Category', 'link' => '/adm/article'),
 );

global $_ACTIONS;

$_ACTIONS = array(
	1 => array(
		'index' => array("name" => "Quản lý danh mục",'link' => '/adm/category'),
		'category-list' => array("name" => "Danh sách danh mục",'link' => '/adm/category/category-list'),
		'add-category' => array('name' => 'Thêm mới danh mục', 'link' => '/adm/category/add-category'),
		'edit-category' => array('name' => 'Cập nhật danh mục', 'link' => '')
	),
	2 => array(
		'index' 		=> array("name" => "Danh sách sản phẩm",'link' => '/adm/product'),
		'product-list'  => array("name" => "Danh sách sản phẩm",'link' => '/adm/product/product-list'),
		'edit-product' => array("name" => "Cập nhật sản phẩm", 'link' => ""),
		'add-product' => array("name" => "Thêm mới sản phẩm", 'link' =>"")
	),
	3 => array(
		'index' => array("name" => "Danh sách banner",'link' => '/adm/banner/index'),
		'banner-list' => array("name" => "Danh sách banner",'link' => '/adm/banner/banner-list'),			
		'add-banner' => array("name" => "Thêm mới banner",'link' => '/adm/banner/add-banner'),
		'edit-banner' => array("name" => "Cập nhật banner",'link' => '/adm/banner/edit-banner')
	),
	7 => array(
		'index' => array("name" => "Danh sách thuộc tính",'link' => '/adm/attribute/index'),
		'attribute-list' => array("name" => "Danh sách thuộc tính",'link' => '/adm/attribute/attribute-list'),			
		'add-attribute' => array("name" => "Thêm mới thuộc tính",'link' => '/adm/attribute/add-attribute'),
		'edit-attribute' => array("name" => "Cập nhật thuộc tính",'link' => '/adm/attribute/edit-attribute')		
	),
	6 => array(
		'index'   => array("name" => "Dashboard",'link' => '/adm/index')	
	),
	10 => array(
		'list' => array('name' => 'Admin Manage', 'link' => 'adm/admin/list'),
		'index' => array('name' => 'Admin Manage', 'link' => 'adm/admin/list'),
		'add' => array('name' => 'Add new Admin', 'link' => 'adm/admin/add'),
		'edit' => array('name' => 'Edit Admin', 'link' => 'adm/admin/edit'),
		'list-user' => array('name' => 'User Manage', 'link' => 'adm/admin/list-user'),
		'edit-user' => array('name' => 'Edit User', 'link' => 'adm/admin/edit-user'),
	),
	11 => array(
		'contact-list' => array('name' => 'Danh sách liên hệ', 'link' => 'adm/contact/contact-list'),
		'index' => array('name' => 'Danh sách liên hệ', 'link' => 'adm/contact/contact-list'),
		'detail' => array('name' => 'Chi tiết liên hệ', 'link' => 'adm/contact/contact-detail')
	),
	12 => array(
		'article-category-list' => array('name' => 'Article Category Manage', 'link' => 'adm/article/article-category-list'),
		'add-article-category' => array('name' => 'Add new Article Category', 'link' => 'adm/article/add-article-category'),
		'edit-article-category' => array('name' => 'Edit Article Category', 'link' => 'adm/article/edit-article-category'),
		'list' => array('name' => 'Article Manage', 'link' => 'adm/article/list'),
		'index' => array('name' => 'Article Manage', 'link' => 'adm/article/list'),
		'add-article' => array('name' => 'Add new Article', 'link' => 'adm/article/add-article'),
		'edit-article' => array('name' => 'Edit Article', 'link' => 'adm/article/edit-article'),
	)
	
);