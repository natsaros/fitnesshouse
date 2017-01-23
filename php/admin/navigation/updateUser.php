<?php require("pageHeader.php"); ?>

<?php $currentUser = UserFetcher::getUserById($_GET["id"]); ?>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                User Info
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form role="form" action="<?php echo getAdminActionRequestUri() . "user" . DS . "update"; ?>"
                              method="post">
                            <input type="hidden" name="ID" value="<?php echo $currentUser->getID() ?>">
                            <input type="hidden" name="gender" value="<?php echo $currentUser->getGender() ?>">
                            <input type="hidden" name="isAdmin" value="<?php echo $currentUser->getIsAdmin() ?>">
                            <input type="hidden" name="userStatus" value="<?php echo $currentUser->getUserStatus() ?>">
                            <input type="hidden" name="link" value="<?php echo $currentUser->getLink() ?>">
                            <input type="hidden" name="picture" value="<?php echo $currentUser->getPicture() ?>">
                            <div class="form-group">
                                <label class="control-label" for="username_input">User Name</label>
                                <input class="form-control" placeholder="User Name" name="username" id="username_input"
                                       value="<?php echo $currentUser->getUserName() ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="firstname_input">First Name</label>
                                <input class="form-control" placeholder="First Name" name="firstName"
                                       id="firstname_input"
                                       value="<?php echo $currentUser->getFirstName() ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="lastname_input">Last Name</label>
                                <input class="form-control" placeholder="Last Name" name="lastName" id="lastname_input"
                                       value="<?php echo $currentUser->getLastName() ?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="mail_input">E-mail</label>
                                <input class="form-control" type="email" placeholder="E-mail" name="email"
                                       id="mail_input"
                                       value="<?php echo $currentUser->getEmail() ?>">
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="mail_input">Phone</label>
                                <input class="form-control" type="tel" placeholder="Phone" name="phone" id="phone_input"
                                       value="<?php echo $currentUser->getPhone() ?>">
                            </div>
                            <div class="text-right form-group">
                                <button type="button" class="btn btn-default">Back</button>
                                <input type="submit" name="submit" class="btn btn-primary" value="Save"
                                       placeholder="Save"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
