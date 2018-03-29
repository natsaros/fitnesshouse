<?php
/**
 * this function updates a draft event on db
 */

$start = $_POST['start'];
$end = $_POST['end'];
$id = $_POST['id'];

try {
    $event = ProgramHandler::fetchEventById($id);
    if (isNotEmpty($event)) {
        $event
            ->setStart($start)
            ->setEnd($end)
            ->setStatus(EventStatus::INACTIVE);
        $updateEventRes = ProgramHandler::updateDBEvent($event);
        if ($updateEventRes !== null || $updateEventRes) {
            echo json_encode($event);
        } else {
            throwJSONError();
        }
    } else {
        throwJSONError();
    }
} catch (SystemException $e) {
    logError($ex);
    throwJSONError();

}

function throwJSONError() {
    $statusCode = 500;
    $status_string = $statusCode . ' ' . 'Internal Server Error';
    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
    echo json_encode(array('message' => 'ERROR', 'code' => 'wroooong'));
}