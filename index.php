<?php
require __DIR__ . '/config.php';
if (is_dir(SYSTEM_ROOT)) {
	require SYSTEM_ROOT . '/system/core/Load.php';
} else {
	echo 'Thư mục hệ thống không tồn tại: <b>' . SYSTEM_ROOT . '</b>';
}