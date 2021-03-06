<?php
$ID = safe_input($_POST[PostHandler::ID]);
$title = safe_input($_POST[PostHandler::TITLE]);
$text = $_POST[PostHandler::TEXT];
$state = safe_input($_POST[PostHandler::STATE]);
$userID = safe_input($_POST[PostHandler::USER_ID]);
$imagePath = safe_input($_POST[PostHandler::IMAGE_PATH]);
if (isEmpty($imagePath)) {
    $imagePath = FormHandler::getFormPictureDraftName(PostHandler::IMAGE);
}

if (isEmpty($title) || isEmpty($text)) {
    addErrorMessage("Please fill in required info");
}

if (isNotEmpty($imagePath)) {
    $image2Upload = FormHandler::validateUploadedImage(PostHandler::IMAGE);
}

if (hasErrors()) {
    FormHandler::setSessionForm('updatePostForm', $_POST[FormHandler::PAGE_ID]);
    Redirect(getAdminRequestUri() . PageSections::POSTS . DS . "updatePost");
}

try {
    $imgContent = (isNotEmpty($imagePath) && isNotEmpty($image2Upload)) ? ImageUtil::readImageContentFromFile($image2Upload) : false;

    //Get post from db to edit
    $post = PostHandler::getPostByIDWithDetails($ID);
    $post->setTitle($title)->setFriendlyTitle(transliterateString($title))->setState($state)->setUserId($userID)->setText($text);

    if ($imgContent) {
        //only saving in filesystem for performance reasons
        $post->setImagePath($imagePath);
        //save image content also in blob on db for back up reasons if needed
//        $post->setImagePath($imagePath)->setImage($imgContent);
    }

    $postRes = PostHandler::update($post);
    if ($postRes !== null || $postRes) {
        addSuccessMessage("Post '" . $post->getTitle() . "' successfully updated");
        //save image under id of created post in file system
        if (isNotEmpty($imagePath) && isNotEmpty($image2Upload)) {
            $fileName = basename($image2Upload[ImageUtil::NAME]);
            ImageUtil::saveImageToFileSystem(POSTS_PICTURES_ROOT, $ID, $fileName, $imgContent);
        }
    } else {
        addErrorMessage("Post '" . $post->getTitle() . "' failed to be updated");
    }

} catch (SystemException $ex) {
    logError($ex);
    addErrorMessage(ErrorMessages::GENERIC_ERROR);
}

if (hasErrors()) {
    Redirect(getAdminRequestUri() . PageSections::POSTS . DS . "updatePost" . addParamsToUrl(array('id'), array($ID)));
} else {
    Redirect(getAdminRequestUri() . PageSections::POSTS . DS . "posts");
}