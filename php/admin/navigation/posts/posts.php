<?php require(ADMIN_NAV_PATH . "pageHeader.php"); ?>

<?php require(ADMIN_NAV_PATH . "messageSection.php"); ?>

<?php
$posts = PostHandler::fetchAllPosts();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover posts-dataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                /* @var $post Post */
                foreach ($posts as $key => $post) {
                    $oddEvenClass = $key % 2 == 0 ? 'odd' : 'even';
                    $postId = $post->getID();
                    ?>
                    <tr class="<?php echo $oddEvenClass ?>">
                        <td><?php echo $postId; ?></td>
                        <td><?php echo $post->getTitle(); ?></td>
                        <td><?php echo $post->getActivationDate(); ?></td>
                        <td>
                            <?php
                            //Opposite set to '$updatedStatus' so that this gets passed to the db
                            $updatedStatus = $post->getState() ? 0 : 1;
                            $activDeactivText = $post->getState() ? 'Deactivate' : 'Activate';
                            ?>

                            <a type="button"
                               href="<?php echo getAdminActionRequestUri() . "post" . DS . "updatePostStatus" . addParamsToUrl(array('id', 'status'), array($postId, $updatedStatus)); ?>"
                               class="btn btn-default btn-sm" title="<?php echo $activDeactivText ?> Post">
                                <?php $statusClass = $post->getState() ? 'active-item' : 'inactive-item' ?>
                                <span class="glyphicon glyphicon-comment <?php echo $statusClass ?>"
                                      aria-hidden="true"></span>
                            </a>

                            <a type="button"
                               href="<?php echo getAdminActionRequestUri() . "post" . DS . "deletePost" . addParamsToUrl(array('id'), array($postId)); ?>"
                               class="btn btn-default btn-sm" title="Delete Post">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                            </a>

                            <a type="button"
                               href="<?php echo getAdminRequestUri() . DS . PageSections::POSTS . DS . "updatePost" . addParamsToUrl(array('id'), array($postId)); ?>"
                               class="btn btn-default btn-sm" title="Edit Post">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 text-center">
        <a href="<?php echo getAdminRequestUri() . DS . PageSections::POSTS . DS . "updatePost"; ?>" type="button"
           class="btn btn-outline btn-primary">
            Add <span class="fa fa-comment fa-fw" aria-hidden="true"></span>
        </a>
    </div>
</div>