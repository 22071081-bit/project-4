<?php
$new_password = "@@Admin123"; // Mật khẩu mới
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

echo $hashed_password; // Lấy giá trị này để cập nhật vào database
?>