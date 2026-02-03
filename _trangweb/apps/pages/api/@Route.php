<?php
Route::post("AdsCount_popup", "AdsCount@popup");//Thống kê quảng cáo popup
Route::post("MediaPlayer", "MediaPlayer@func");//Thống kê quảng cáo trong video
Route::link("/api/captcha.png", "Captcha@image");//Tạo ảnh captcha

Route::link("/api/builder", "Builder@func");//Kiểm tra xem có phải admin hay không
Route::link("/api/builder/{domain}", "Builder@expiredNotify");//Kiểm tra xem có phải admin hay không
Route::link("/api/websiteManager/createBackup", "WebsiteManager@createBackup");// Tạo backup
Route::link("/api/websiteManager/updateWebTemplate", "WebsiteManager@updateWebTemplate");// Cập nhật web mẫu
Route::link("/api/websiteManager/websiteUpdateContactStatus", "WebsiteManager@websiteUpdateContactStatus");// Cập nhật trạng thái liên hệ website sắp hết hạn

Route::link("/api/online-chat", "OnlineChat@index");// Nội dung chat
Route::link("/api/contact", "Contact@submit"); // Gửi yêu cầu gọi lại 

Route::link("/api/addRole", "RolesPermissions@addRole"); // Thêm vai trò mới
Route::link("/api/deleteRole", "RolesPermissions@deleteRole"); // Xóa vai trò
Route::link("/api/setPermissions", "RolesPermissions@setPermissions"); // Thiết lập quyền cho vai trò

Route::link("/api/appStore/updateCategory", "AppStoreAPI@updateCategory"); // Cập nhật chuyên mục kho ứng dụng
Route::link("/api/appStore/deleteCategory", "AppStoreAPI@deleteCategory"); // Xóa chuyên mục kho ứng dụng
Route::link("/api/appStore/updateApp", "AppStoreAPI@updateApp"); // Cập nhật ứng dụng
Route::link("/api/appStore/deleteApp", "AppStoreAPI@deleteApp"); // Xóa ứng dụng
Route::link("/api/appStore/installApp", "AppStoreAPI@installApp"); // Cài ứng dụng

Route::link("/api/CashFlowAPI/acceptInvoice", "CashFlowAPI@acceptInvoice"); // Duyệt hoặc xóa phiếu thu, chi
Route::link("/api/CashFlowAPI/getCustomer", "CashFlowAPI@getCustomer"); // Lấy danh sách khách hàng
Route::link("/api/CashFlowAPI/updateInvoice", "CashFlowAPI@updateInvoice"); // Lưu phiếu thu chi
Route::link("/api/CashFlowAPI/updateCategory", "CashFlowAPI@updateCategory"); // Lưu phân loại phiếu thu chi

Route::link("/api/TaskAPI/updateTask", "TaskAPI@updateTask"); // Lưu công việc
Route::link("/api/TaskAPI/updateTaskStatus", "TaskAPI@updateTaskStatus"); // Cập nhật trạng thái công việc
Route::link("/api/TaskAPI/updateCategory", "TaskAPI@updateCategory"); // Lưu phân loại công việc
Route::link("/api/services/check-domain",
        "InetAPIController@check_domain");
    Route::link("/api/services/whois",
        "InetAPIController@whois");
    Route::link("/api/services/add-to-cart",
        "InetAPIController@add_to_cart");
    Route::link("/api/services/checkout",
        "InetAPIController@checkout");
    Route::link("/api/services/checkout-renew",
        "InetAPIController@checkout_renew");
    Route::link("/api/services/cancel-order",
        "InetAPIController@cancel_order");
    Route::link("/api/services/active-order",
        "InetAPIController@active_order");
    Route::link("/api/services/sync-domain",
        "InetAPIController@sync_domain");
    Route::link("/api/services/sync-record",
        "InetAPIController@sync_record");
    Route::link("/api/services/sync-dns",
        "InetAPIController@sync_dns");
    Route::link("/api/services/update-dns",
        "InetAPIController@update_dns");
    Route::link("/api/services/record",
        "InetAPIController@record");
    Route::link("/api/get-user",
        "InetAPIController@get_user");
    Route::link("/api/upload-id-card",
        "InetAPIController@upload_id_card");
    Route::link("/api/get-fund-amount",
        "InetAPIController@get_fund_amount");
    Route::link("/api/get-agency-price",
        "InetAPIController@get_agency_price");