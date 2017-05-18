<?php
$userName = safe_input($_POST[UserHandler::USERNAME]);
$email = safe_input($_POST[UserHandler::EMAIL]);

if(isEmpty($userName) || isEmpty($email)) {
    //    TODO : add php side form validation message
    //    addInfoMessage("Please fill in required info");
}
$ID = safe_input($_POST[UserHandler::ID]);
$first_name = safe_input($_POST[UserHandler::FIRST_NAME]);
$last_name = safe_input($_POST[UserHandler::LAST_NAME]);
$user_status = safe_input($_POST[UserHandler::USER_STATUS]);
$is_admin = safe_input($_POST[UserHandler::IS_ADMIN]);
$gender = safe_input($_POST[UserHandler::GENDER]);
$link = safe_input($_POST[UserHandler::LINK]);
$phone = safe_input($_POST[UserHandler::PHONE]);
$picture = safe_input($_POST[UserHandler::PICTURE]);

try {
    $user2Update = User::createFullUser($ID, $userName, null, $first_name, $last_name, $email, null, null, $user_status, $is_admin, $gender, $link, $phone, $picture);
    $updateUserRes = UserHandler::updateUser($user2Update);

    if($updateUserRes !== null || $updateUserRes) {
        addSuccessMessage("User " . $user2Update->getUserName() . " successfully updated");
    } else {
        addErrorMessage("User " . $user2Update->getUserName() . " failed to be updated");
    }
} catch(SystemException $ex) {
    logError($ex);
    addErrorMessage(ErrorMessages::GENERIC_ERROR);
}
Redirect(sprintf(getAdminRequestUri() . "updateUser?id=%s", $user2Update->getID()));