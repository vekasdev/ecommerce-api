<?php


namespace App\model;

use App\dtos\DeliveryDataDTO;
use App\dtos\OrderTotalPriceDTO;
use App\exceptions\ElementAlreadyExistsException;
use App\exceptions\EntityNotExistException;
use App\exceptions\OrderingProcessException;
use App\exceptions\ProcessedRequestException;
use App\interfaces\NotificationSender;
use App\repositories\DeliveryDataRepository;
use App\repositories\DiscountCodeRepository;
use DeliveryData;
use Doctrine\ORM\EntityManager;
use App\model\TelegramAdminstrationNotificationService;
use DiscountCode;
use OrderGroup;


class OrderGroupService {

    private DeliveryDataRepository $deliveryDataRepository;

    private DiscountCodeRepository $discountCodeRepository;

    /**
     * @param TelegramAdminstrationNotificationService $notificationSender
     */

    function __construct(
        private OrderGroup $orderGroup,
        private EntityManager $em,
        private NotificationSender $notificationSender,
        private CartServiceFactory $cartServiceFactory,
        )
    {
        $this->deliveryDataRepository = $em->getRepository(DeliveryData::class);
        $this->discountCodeRepository = $em->getRepository(DiscountCode::class);
        $notificationSender->setOrderGroupService($this);
    }

    /**
     * @throws OrderingProcessException if the cart or delivery data are miss provided
     */
    function processOrdering() {

        // validate and throw exception
        $this->validateDeliveryData();
        $this->validateCart();
        
        // confirm the order and change it state to be pending
        $this->toPendingState();

        // send notification to telegram of this order
        $this->notificationSender->send();
    }

    function getTotal() : OrderTotalPriceDTO {
        $discounted = (bool) $this->orderGroup->getDiscountCode();
        $deliveryCost = $this->orderGroup->getDeliveryCost();

        $total = new OrderTotalPriceDTO($discounted,
                        $this->orderGroup->getTotal(),
                        $discounted ? $this->orderGroup->getCart()->getTotal() + $deliveryCost : null);

        return $total;
    }


    function addDiscountCode(string $code) {
        if(!is_null($this->orderGroup->getDiscountCode())) {
            throw new ElementAlreadyExistsException("discount code exist , you cannot add another one !");
        }
        $dcode = $this->discountCodeRepository->getDiscountCode($code);
        if(!$dcode) {
            throw new EntityNotExistException("code : ". $code . " not valid");
        }
        $this->orderGroup->setDiscountCode($dcode);
        $this->update();
    }

    function setDeliveryData(DeliveryDataDTO $deliveryDataDTO) {
        $deliveryData = $this->orderGroup->getDeliveryData();
        return $deliveryData = $this->deliveryDataRepository->updateDeliveryData($deliveryData , $deliveryDataDTO);
    }

    function getDetails() {
        // [orders : [[count,name,total price],...] , total with disount, discount precentage , delivery cost ]
    }

    function validateDeliveryData() {
        $deliveryData= $this->orderGroup->getDeliveryData();
        
        if( $deliveryData->getDeliveryRegion() == null || 
            $deliveryData->getLocation()       == null || 
            $deliveryData->getPhoneNumber()    == null ||
            $deliveryData->getName()           == null ||
            $deliveryData->getPostalCode()     == null 
        )   
        throw new  OrderingProcessException("delivery data must be properly provided , id : ".$deliveryData->getId() );

        return true;
    }

    function update() {
        $this->em->persist($this->orderGroup);
        $this->em->flush();
    }

    function validateCart() {
        if($this->orderGroup->getCart()->getOrders()->count() < 1  ) {
             throw new  OrderingProcessException("cart must have at least one order");
        }
        return true;
    }

    private function toPendingState() {
        $this->orderGroup->setStatus(OrderGroupStatus::PENDING);
        $cart = $this->orderGroup->getCart()->setProcessed(true);
        $this->em->persist($cart);
        $this->em->flush();
    }

    /**
     * @throws ProcessedRequestException
     */
    function toDeliveredState() {
        if($this->orderGroup->getStatus() == OrderGroupStatus::DEVLIVERED) throw new ProcessedRequestException("the status for delivered is already changed");
        $this->orderGroup->setStatus(OrderGroupStatus::DEVLIVERED);
        $this->update();
    }

    function getEntity() {
        return $this->orderGroup;
    }



}