<?php

namespace App\Classes;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\Date_Model as Date_Model;
use ApnsPHP_Push;
use ApnsPHP_Abstract;
use ApnsPHP_Message;

class PushNotification
{
    public static function sendPushNotification($sender_id, $receiver_id, $msg) {
        // $user = $request->user();
        // $user_id = $user->getId();

        $sender = Date_Model::getSenderName($sender_id);
        $badge = Date_Model::countNonReadNotifications($receiver_id);
        $device_token = Date_Model::getDeviceToken($receiver_id);
        if (!$device_token) {
            return;
        }
        // $device_id = "2e42bdc1ca935c6f8cd6ce3e3e8f0c31ba8185bc2dbf16c84e1fca41de712e16";

        // Adjust to your timezone
        date_default_timezone_set('Asia/Hong_Kong');
        // Report all PHP errors
        error_reporting(-1);
        // Instantiate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            '../storage/apple_push_services_production.pem'
        );
        // $push->setRootCertificationAuthority('../storage/entrust_root_certification_authority.pem');
        // Connect to the Apple Push Notification Service
        $push->connect();
        // Instantiate a new Message with a single recipient
        $message = new ApnsPHP_Message((string)$device_token);
        // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
        // over a ApnsPHP_Message object retrieved with the getErrors() message.
        $message->setCustomIdentifier("Message-Badge-1");
        // Set badge icon to "3"
        $message->setBadge($badge);
        // Set a simple welcome text
        $message->setText($sender." ".$msg);
        // Play the default sound
        $message->setSound();
        // Set a custom property
        // $message->setCustomProperty('acme2', array('bang', 'whiz'));
        // Set another custom property
        // $message->setCustomProperty('acme3', array('bing', 'bong'));
        // Set the expiry value to 30 seconds
        $message->setExpiry(30);
        // Add the message to the message queue
        $push->add($message);
        // Send all messages in the message queue
        $push->send();
        // Disconnect from the Apple Push Notification Service
        $push->disconnect();
        // Examine the error message container
        $aErrorQueue = $push->getErrors();
        if (!empty($aErrorQueue)) {
            // var_dump($aErrorQueue);
        }
    }
    public static function notifyQRCodeScanned($date_token) {
        $peacher_id = Date_Model::getPeacherIdByDateToken($date_token);
        if (!$peacher_id) {
            return;
        }
        $device_token = Date_Model::getDeviceToken($peacher_id);
        if (!$device_token) {
            return;
        }

        // Adjust to your timezone
        date_default_timezone_set('Asia/Hong_Kong');
        // Report all PHP errors
        error_reporting(-1);
        // Instantiate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push(
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            '../storage/apple_push_services_production.pem'
        );
        // $push->setRootCertificationAuthority('../storage/entrust_root_certification_authority.pem');
        // Connect to the Apple Push Notification Service
        $push->connect();
        // Instantiate a new Message with a single recipient
        $message = new ApnsPHP_Message((string)$device_token);
        // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
        // over a ApnsPHP_Message object retrieved with the getErrors() message.
        $message->setCustomIdentifier("DateStarted");
        // Set badge icon to "3"
        $message->setBadge(0);
        // Set a custom property
        $message->setCustomProperty('silent', 'qr_code_scanned');
        // Set another custom property
        // $message->setCustomProperty('acme3', array('bing', 'bong'));
        // Set the expiry value to 30 seconds
        $message->setExpiry(30);
        // Add the message to the message queue
        $push->add($message);
        // Send all messages in the message queue
        $push->send();
        // Disconnect from the Apple Push Notification Service
        $push->disconnect();
        // Examine the error message container
        $aErrorQueue = $push->getErrors();
        if (!empty($aErrorQueue)) {
            // var_dump($aErrorQueue);
        }

    }
}
