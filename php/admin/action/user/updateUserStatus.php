<?php
//TODO : implement - hint (do it in general way. Class and then call function. Change url from where this has been called)
$id = $_GET['id'];
$status = $_GET['status'];
UserFetcher::updateUserStatus($id, $status);
Redirect(getAdminRequestUri() . 'users');