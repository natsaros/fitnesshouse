<?php
//TODO : implement - hint (do it in general way. Class and then call function. Change url from where this has been called)
$id = $_GET['id'];
$status = $_GET['status'];

$updateUserStatusRes = UserFetcher::updateUserStatus($id, $status);

if($updateUserStatusRes == null || !$updateUserStatusRes) {
    addErrorMessage("User status failed to be changed");
} else {
    addSuccessMessage("User status successfully changed");
}
Redirect(getAdminRequestUri() . 'users');