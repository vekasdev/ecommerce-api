<?php 


namespace App\model;
use App\interfaces\NotificationSender;

class TelegramAdminstrationNotificationService implements NotificationSender {
    private OrderGroupService $orderGroupService;
    function send() :void {
        if(is_null($this->orderGroupService))throw new \RuntimeException("orderGroupService must be provided");
    }
    function setOrderGroupService(orderGroupService $ogs) : void {
        $this->orderGroupService = $ogs;
    }

}