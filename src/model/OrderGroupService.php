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
use DI\Container;
use DiscountCode;
use Doctrine\ORM\NoResultException;
use OrderGroup;
use Psr\Container\ContainerInterface;

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
        private ContainerInterface $container
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
            throw new ElementAlreadyExistsException("discount code is exist, you can't add another one!",321);
        }
        $dcode = $this->discountCodeRepository->getDiscountCode($code);
        if(!$dcode || !$dcode->isValid()) {
            throw new EntityNotExistException("code : ". $code . " not valid",322);
        }
        $this->orderGroup->setDiscountCode($dcode);
        $this->update();
    }

    function setDeliveryData(DeliveryDataDTO $deliveryDataDTO) {
        
        // search in order-group
        if(! $deliveryData = $this->orderGroup->getDeliveryData()) {
            // search in user
            if(! $deliveryData  = $this->getUserService()->getDefaultDeliveryData()) {
                // create an empty delivery data
                $deliveryData = $this->deliveryDataRepository->createDeliveryData($this->orderGroup->getUser());
            }
            // attach new delivery data here to it
            $this->orderGroup->setDeliveryData($deliveryData);
        }

        $updatedDeliveryData = $this->deliveryDataRepository->updateDeliveryData($deliveryData , $deliveryDataDTO);
        $this->update();
        return $updatedDeliveryData;
    }

    private  function getUserService() {
        /** @var UserServiceFactory */
        $factory = $this->container->get(UserServiceFactory::class);
        return $factory->make($this->orderGroup->getUser());
    }

    function validateDeliveryData() {
        $deliveryData= $this->orderGroup->getDeliveryData();

        if( 
            $deliveryData                      == null ||
            $deliveryData->getDeliveryRegion() == null || 
            $deliveryData->getLocation()       == null || 
            $deliveryData->getPhoneNumber()    == null ||
            $deliveryData->getName()           == null ||
            $deliveryData->getPostalCode()     == null 
        )   
        throw new  OrderingProcessException("delivery data must be properly provided ");

        return true;
    }

    function update() {
        $this->em->persist($this->orderGroup);
        $this->em->flush();
    }

    function validateCart() {
        if($this->orderGroup->getCart()->getOrders()->count() < 1  ) {
            // var_dump($this->orderGroup->getCart()->getOrders()->count());exit;
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

    function dismiss() {
        if($this->orderGroup->getStatus() == OrderGroupStatus::NOT_INITIALIZED) {
            return false;
        }
        $this->orderGroup->setStatus(OrderGroupStatus::NOT_INITIALIZED);
        $this->update();
        return true;
    }

    /**
     * @return array | null
     */
    function getDeliveryData() {
        try {
            $data = $this->deliveryDataRepository->getDefaultDeliveryData($this->orderGroup->getUser()->getId());
            return $data;
        } catch (NoResultException $e) {}

        return null;
    }


}