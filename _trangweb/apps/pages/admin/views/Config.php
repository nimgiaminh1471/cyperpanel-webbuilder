<?php




// Trang tổng quan
$option[]=[
	"title"      => "Tổng quan",
	"type"       => "menu",
	"permission" => "member",
	"icon"       => "fa-dashboard",
	"panel"      => "Dashboard"
];

// Công việc
$myTaskCount = \models\Task::where('status', 0)->where('assign_user_id', user('id') )->total();
$taskPending = \models\Task::where('status', 0)->total();
$option[] = [
	"title"      => "Công việc",
	"permission" => "work",
	"type"       => "sub",
	"icon"       => "fa-briefcase",
	"panel"      => "work",
	"count"      => $myTaskCount + $taskPending,
	"sub"=> array(
		[
			"title"      => "Công việc của tôi",
			"permission" => "work",
			"type"       => "link",
			"icon"       => "fa-check-square-o",
			"panel"      => "Task",
			"count"      => $myTaskCount
		],
		[
			"title"      => "Phân công việc",
			"permission" => "work_manager",
			"type"       => "link",
			"icon"       => "fa-hand-pointer-o",
			"panel"      => "TaskManager",
			"count"      => $taskPending
		]
	),
];

// Khách hàng
$option[] = [
	"title"      => "Người dùng",
	"permission" => "users_manager",
	"type"       => "sub",
	"icon"       => "fa-users",
	"panel"      => "users",
	"sub"=> array(
		[
			"title"      => "Danh sách người dùng",
			"permission" => "users_manager",
			"type"       => "link",
			"icon"       => "fa-address-card-o",
			"panel"      => "UsersList"
		],
		[
			"title"      => "Chăm sóc khách hàng",
			"permission" => "telesales",
			"type"       => "link",
			"icon"       => "fa-volume-control-phone",
			"panel"      => "Telesales"
		]
	),
];


// Lịch sử giao dịch
$option[]=[
	"title"      => "Lịch sử giao dịch",
	"permission" => "member",
	"type"       => "link",
	"icon"       => "fa-credit-card",
	"panel"      => "PaymentHistory",
	"hidden"     => true
];

// Tiến hành thanh toán
    $option[]=[
        "title"      => "Tiến hành thanh toán",
        "permission" => "member",
        "type"       => "link",
        "icon"       => "bx bx-credit-card",
        "panel"      => "Checkout",
        "hidden"     => true
    ];

    // Đơn hàng
    $orders = \models\Order::where('status', 0);
    if( !permission('accountant') ){
        $orders = $orders->where('user_id', user('id') );
    }
    $orders = $orders->total();
    $option[]=[
        "title"      => "Đơn hàng",
        "permission" => "member",
        "type"       => "link",
        "icon"       => "bx bx-note",
        "panel"      => "Order",
        "count" => $orders,
        "count_style" => 'warning'
    ];

    // Tên miền
    $option[]=[
        "title"      => "Tên miền",
        "permission" => "member",
        "icon"       => "bx bx-globe",
        "type"       => "sub",
        "panel"      => "domain",
        "sub"=> array(
            [
                "title"      => "Đăng ký tên miền",
                "permission" => "member",
                "type"       => "link",
                "icon"       => "bx bx-edit",
                "panel"      => "RegisterDomain"
            ],

            [
                "title"      => "Bảng giá tên miền",
                "permission" => "member",
                "type"       => "link",
                "icon"       => "bx bx-list-alt",
                "panel"      => "DomainPricing",
                "hidden" => true
            ],
            [
                "title"      => "Quản lý tên miền",
                "permission" => "member",
                "type"       => "link",
                "icon"       => "bx bx-comments-o",
                "panel"      => "DomainManager"
            ]

        ),


    ];

if( permission('website_manager') ){
	$websiteExpireCount = \models\BuilderDomain::select('id')
	->where('contact_status', 0)
	->where('expired', '<', strtotime('+'.Storage::setting('builder_expiry_date_days').' days') )
	->whereNotNull('package')
	->total(); // Số website sắp hết hạn
}

// Quản lý website
$option[] = [
	"title"       => "Website",
	"permission"  => "create_website",
	"type"        => "sub",
	"icon"        => "fa-globe",
	"panel"       => "website",
	"count"       => $websiteExpireCount ?? 0,
	"count_style" => 'danger',
	"sub"=> array(
		[
			"title"      => "Tạo website",
			"permission" => "create_website",
			"type"       => "link",
			"icon"       => "fa-edit",
			"panel"      => "WebsiteTemplate"
		],
		[
			"title"      => "Quản lý website",
			"permission" => "create_website",
			"type"       => "link",
			"icon"       => "fa-wordpress",
			"panel"      => "WebsiteManager",
			"hidden"     => true
		],
		[
			"title"      => "Quản lý website",
			"permission" => "create_website",
			"type"       => "link",
			"icon"       => "fa-th",
			"panel"      => "WebsiteList"
		],
		[
			"title"       => "Website sắp hết hạn",
			"permission"  => "users_manager",
			"type"        => "link",
			"icon"        => "fa-calendar-times-o",
			"panel"       => "WebsiteExpire",
			"count"       => $websiteExpireCount ?? 0,
			"count_style" => 'danger'
		],
		[
			"title"      => "Quản lý website mẫu",
			"permission" => "website_manager",
			"type"       => "link",
			"icon"       => "fa-dashboard",
			"panel"      => "TemplateManager"
		],
		[
			"title"      => "Sắp xếp website mẫu",
			"permission" => "website_manager",
			"type"       => "link",
			"icon"       => "fa-dashboard",
			"panel"      => "WebsiteTemplateSort",
			"hidden"     => true
		],
		[
			"title"      => "Update phiên bản WP",
			"permission" => "website_manager",
			"type"       => "link",
			"icon"       => "fa-dashboard",
			"panel"      => "UpdateWP",
			//"hidden"     => true
		],
		[
			"title"      => "Công cụ quản lý web",
			"permission" => "admin",
			"type"       => "link",
			"icon"       => "fa-cogs",
			"panel"      => "WebsiteTools"
		],
	),
];
$option[] = [
	"title"      => "Kho ứng dụng",
	"permission" => "app_store",
	"type"       => "link",
	"icon"       => "fa-puzzle-piece",
	"panel"      => "AppStore"
];
$option[] = [
	"title"      => "Lịch sử mua ứng dụng",
	"permission" => "admin",
	"type"       => "link",
	"icon"       => "fa-check",
	"panel"      => "AppStoreManager",
	"hidden"     => true
];
$option[] = [
	"title"      => "Nạp tiền",
	"permission" => "recharge",
	"type"       => "link",
	"icon"       => "fa-credit-card",
	"panel"      => "Recharge"
];
$option[] = [
	"title"      => "Lịch sử nạp tiền",
	"permission" => "recharge",
	"type"       => "link",
	"icon"       => "fa-history",
	"panel"      => "RechargeHistory",
	"hidden"     => true
];

// Quản lý đơn nạp tiền
$ReceiptsCount = \models\CashFlow::where('status', 0)->whereNull('deleted_at')->total();
$option[] = [
	"title"      => "Tài chính",
	"permission" => "accountant",
	"type"       => "sub",
	"icon"       => "fa-money",
	"panel"      => "cashflow",
	'count'      => $ReceiptsCount,
	"sub"=> array(
		[
			"title"      => "Phiếu thu",
			"permission" => "accountant",
			"type"       => "link",
			"icon"       => "fa-calendar-plus-o",
			"panel"      => "Receipts",
			"count"      => $ReceiptsCount
		],
		[
			"title"      => "Phiếu chi",
			"permission" => "accountant",
			"type"       => "link",
			"icon"       => "fa-calendar-minus-o",
			"panel"      => "Payment",
		],
		[
			"title"      => "Báo cáo",
			"permission" => "admin",
			"type"       => "link",
			"icon"       => "fa-bar-chart",
			"panel"      => "Report",
		]
	),
];

//Quản lý files
$option[]=[
		"title"      => "Tệp tin",
		"permission" => "post",
		"type"       => "link",
		"icon"       => "fa-files-o",
		"panel"      => "Gallery"
	];

//Bài đăng
$option[]=[
"title"      => "Bài viết",
"permission" => "post",
"icon"       => "fa-book",
"type"       => "sub",
"panel"      => "post",
"sub"=> array(
	[
		"title"      => "Đăng bài viết",
		"permission" => "post",
		"type"       => "link",
		"icon"       => "fa-edit",
		"panel"      => "PostEditor"
	],

	[
		"title"      => "Quản lý bài viết",
		"permission" => "post",
		"type"       => "link",
		"icon"       => "fa-list-alt",
		"panel"      => "PostsList"
	],
	[
		"title"      => "Bình luận",
		"permission" => "post",
		"type"       => "link",
		"icon"       => "fa-comments-o",
		"panel"      => "PostsComments"
	],
	[
		"title"      => "Chuyên mục",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-server",
		"panel"      => "PostsCategories"
	],

),


];



//Cài đặt
$option[]=[
"title"      => "Cài đặt",
"permission" => "admin",
"icon"       => "fa-cogs",
"type"       => "sub",
"panel"      => "settings",
"sub"=> array(


	//Thiết lập chung
	[
		"title"      => "Tổng quan",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-wrench",
		"panel"      => "General"
	],

	//Quảng cáo
	[
		"title"      => "Quảng cáo",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-buysellads",
		"panel"      => "Advertising"
	],


	//Giao diện
	[
		"title"      => "Giao diện",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-tachometer",
		"panel"      => "Theme"
	],

	//Trình phát media
	[
		"title"      => "Trình phát media",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-play-circle-o",
		"panel"      => "MediaPlayer"
	],

	//Cài đặt admin panel
	[
		"title"      => "Admin panel",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-cog",
		"panel"      => "AdminPanel"
	],

	//Cài đặt admin panel
	[
		"title"      => "Chức vụ & quyền",
		"permission" => "admin",
		"icon"       => "fa-address-card-o",
		"panel"      => "RolesPermissions",
		"type"       => "link"
	],

	//Cài đặt web builder
	[
		"title"      => "Web builder",
		"permission" => "admin",
		"type"       => "link",
		"icon"       => "fa-globe",
		"panel"      => "WebBuilder"
	],
	
	//Cài đặt iNet API
    [
        "title"      => "iNet API",
        "permission" => "admin",
        "type"       => "link",
        "icon"       => "bx bx-globe",
        "panel"      => "iNetAPI"
    ],


),




"header"=>'
	<form action="" method="POST" id="settingsForm">
',
"footer"=>'
	<div class="admin-save-status">
	<span id="adminSaveStatus" class="hidden"></span>
	</div>
	</form>
	<script src="/assets/admin/js/settings.js"></script>
'];
