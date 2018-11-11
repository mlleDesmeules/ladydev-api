<?php

$admin = "v1/admin";
$int   = "\\d[\\d,]*";
$slug  = "<slug:([a-z0-9-]+)>";

return [
	""           => "site",

	//  V1 rules
	"v1/doc"     => "v1/default/doc",
	"v1/api"     => "v1/default/api",

	//  categories
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "v1/categories" => "v1/category/category" ],
		"except"     => [ "create", "update", "delete" ],
		"patterns"   => [
			'GET,HEAD {slug}' => 'view',
			'GET,HEAD'        => 'index',
			'{slug}'          => 'options',
			''                => 'options',
		],
		"tokens"     => [ "{slug}" => $slug ],
	], [
		"class"         => 'yii\rest\UrlRule',
		"controller"    => [ "v1/categories/posts" => "v1/category/post" ],
		"except"        => [ "create", "update", "delete" ],
		"extraPatterns" => [ "GET count" => "count", "OPTIONS count" => "options", ],
	], [
		"class"      => 'yii\rest\UrlRule',
		"prefix"     => "v1/categories/<categorySlug:([a-z0-9-]+)>",
		"controller" => [ "posts" => "v1/category/post" ],
		"except"     => [ "view", "create", "update", "delete" ],
	],

	//  tags
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "v1/tag" ],
		"except"     => [ "create", "update", "delete" ],
		"patterns"   => [
			'GET,HEAD {slug}' => 'view',
			'GET,HEAD'        => 'index',
			'{slug}'          => 'options',
			''                => 'options',
		],
		"tokens"     => [ "{slug}" => $slug ],
	],

	//  posts
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "v1/posts" => "v1/post/post" ],
		"except"     => [ "create", "update", "delete" ],
		"patterns"   => [
			'GET,HEAD {slug}' => 'view',
			'GET,HEAD'        => 'index',
			'{slug}'          => 'options',
			''                => 'options',
		],
		"tokens"     => [ "{slug}" => $slug ],
	], [
		"class"      => 'yii\rest\UrlRule',
		"prefix"     => "v1/posts/<postId:$int>",
		"controller" => [ "comments" => "v1/post/comment" ],
		"except"     => [ "view", "update", "delete" ],
	],

	//  communication
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "v1/communication" ],
		"except"     => [ "index", "view", "update", "delete" ],
	],

	//  author
	[
        "class"      => 'yii\rest\UrlRule',
        "pluralize"  => false,
        "controller" => ["v1/author"],
        "except"     => ["view", "create", "update", "delete"],
    ],

	//  V1 Admin rules
	"$admin/doc" => "$admin/default/doc",
	"$admin/api" => "$admin/default/api",

	"OPTIONS $admin/auth" => "$admin/auth/options",
	"POST    $admin/auth" => "$admin/auth/login",
	"DELETE  $admin/auth" => "$admin/auth/logout",

	//  categories
	[ "class" => 'yii\rest\UrlRule', "controller" => [ "$admin/category" ] ],

	//  languages
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "$admin/language" ],
		"except"     => [ "view", "create", "update", "delete" ],
	],

	//  communication
	[
		"class"         => 'yii\rest\UrlRule',
		"controller"    => [ "$admin/communication" ],
		"except"        => [ "create", "delete" ],
		"extraPatterns" => [
			"OPTIONS count" => "options",
			"GET count"     => "count",
		],
	],

	//  posts
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "$admin/posts" => "$admin/post/post" ],
	], [
		"class"      => 'yii\rest\UrlRule',
		"prefix"     => "$admin/posts/<postId:$int>/<langId:$int>",
		"controller" => [ "cover" => "$admin/post/cover" ],
		"except"     => [ "index", "view", "update" ],
	], [
		"class"      => 'yii\rest\UrlRule',
		"prefix"     => "$admin/posts/<postId:$int>",
		"controller" => [ "comments" => "$admin/post/comment" ],
		"except"     => [ "view" ],
	],
	[
		"class"      => 'yii\rest\UrlRule',
		"prefix"     => "$admin/posts/<postId:$int>",
		"controller" => [ "links" => "$admin/post/link" ],
		"patterns"   => [
			'GET,HEAD'            => 'index',
			'GET,HEAD {linkType}' => 'view',
			'POST'                => 'create',
			'PUT {linkType}'      => 'update',
			'DELETE {linkType}'   => 'delete',
			"{linkType}"          => "options",
			""                    => "options",
		],
		"tokens"     => [ "{linkType}" => "<linkType:$int>" ],
	],

	//  post statuses
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "$admin/posts/statuses" => "$admin/post/status" ],
		"except"     => [ "view", "create", "update", "delete" ],
	],

	//  tags
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "$admin/tags" => "$admin/tag/tag" ],
	],

	//  post tags relation
	[
		"class"      => 'yii\rest\UrlRule',
		"controller" => [ "$admin/posts-tags" => "$admin/post/tag" ],
		"except"     => [ "index", "view", "update" ],
		"patterns"   => [
			'POST'   => 'create',
			'DELETE' => 'delete',
			''       => 'options',
		],
	],

	//  user profile
	"OPTIONS $admin/user/me"          => "$admin/user/profile/options",
	"OPTIONS $admin/user/me/password" => "$admin/user/profile/options",
	"OPTIONS $admin/user/me/picture"  => "$admin/user/profile/options",
	"PUT $admin/user/me"              => "$admin/user/profile/update",
	"PUT $admin/user/me/password"     => "$admin/user/profile/update-password",
	"POST $admin/user/me/picture"     => "$admin/user/profile/upload-picture",
];