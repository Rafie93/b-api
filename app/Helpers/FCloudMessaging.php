<?php
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\Topics;

//Mengirim notifikasi ke semua perangkat

 function sendTopic($title,$body)
{

    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($body)
                        ->setSound('default')
                        ->setClickAction("com.zerone.bahtera_mart.fcm_target_notification");

    $notification = $notificationBuilder->build();

    $topic = new Topics();
    $topic->topic('global');

    $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

    $topicResponse->isSuccess();
    $topicResponse->shouldRetry();
    $topicResponse->error();
}
//Multi topic
function sendMultipleTopic($title,$body)
{
    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($body)
                        ->setSound('default')
                        ->setClickAction("com.zerone.bahtera_mart.fcm_target_notification");

    $notification = $notificationBuilder->build();

    $topic = new Topics();
    $topic->topic('global')->andTopic(function($condition) {

        $condition->topic('economic')->orTopic('cultural');

    });

    $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

    $topicResponse->isSuccess();
    $topicResponse->shouldRetry();
    $topicResponse->error();
}
 function sendMessageToDevice($title,$body,$token)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);
    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($body)
                        ->setSound('default')
                        ->setClickAction("com.zerone.bahtera_mart.fcm_target_notification");

    $dataBuilder = new PayloadDataBuilder();

    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

    // return Array - you must remove all this tokens in your database
    $downstreamResponse->tokensToDelete();
    // return Array (key : oldToken, value : new token - you must change the token in your database)
    $downstreamResponse->tokensToModify();
    // return Array - you should try to resend the message to the tokens in the array
    $downstreamResponse->tokensToRetry();
    // return Array (key:token, value:error) - in production you should remove from your database the tokens
    $downstreamResponse->tokensWithError();
}


?>
