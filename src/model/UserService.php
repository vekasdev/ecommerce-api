<?php 

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\exceptions\UserValidationException;
use App\interfaces\CodeValidationSenderInterface;
use App\repositories\CartsRepository;
use App\repositories\DeliveryDataRepository;
use App\repositories\OrderGroupsRepository;
use App\repositories\ProductsRepository;
use Cart;
use DeliveryData;
use Doctrine\ORM\EntityManager;
use OrderGroup;
use Product;
use Symfony\Component\Mime\Email;
use User;
use ValidationCode;

class UserService {
    private $expirationPeriod = 1;

    private OrderGroupsRepository $orderGroupRepo;

    private CartsRepository $cartsRepository;

    private DeliveryDataRepository $deliveryDataRepository;

    private ProductsRepository $productsRepository;

    /**
     * @param GoogleEmailService|CodeValidationSenderInterface $sender
     */
    function __construct(
        private User $user,
        private EntityManager $em,
        private CodeValidationSenderInterface $sender,
        private OrderGroupServiceFactory $orderGroupServiceFactory,
        private CartServiceFactory $cartServiceFactory

    ) {
        $this->orderGroupRepo = $em->getRepository(OrderGroup::class);
        $this->cartsRepository = $em->getRepository(Cart::class);
        $this->deliveryDataRepository = $em->getRepository(DeliveryData::class);
        $this->productsRepository = $em->getRepository(Product::class);
    }

    /**
     * @throws UserValidationException when the user is created and valid
     */
    function generateCode() {
        if($this->isValid()) {
            throw new UserValidationException("the user is created and valid");
        }
        $code  = substr(rand(9000,1000000),0,4);
        $vcode = new ValidationCode;
        $vcode->setCode($code)
        ->setExpire(new \DateTime("+".$this->expirationPeriod." hour"));

        // persisting operation

        $this->user->addCode($vcode);
        $this->em->persist($this->user);
        $this->em->flush();


        // send code

        $this->sendCode($code);


        return $vcode;
  }

  function setExpirationPeriod (int $period) {
    $this->expirationPeriod= $period;
  }

  /**
   * @return User valid user object where $code is right
   * @throws UserValidationException
   */
  function validate(string $code) : bool {
      if($this->isValid()) {
          throw new UserValidationException("the user is already validated");
      }
      $codeObj = $this->user->getCode($code);
      if(!$codeObj) {
          throw new UserValidationException("provided code is wrong");
      }
      else if($codeObj->getExpire() < new \DateTime ) {
          throw new UserValidationException("provided code is expired");
      }
      else if(!$codeObj->isValid()) {
          throw new UserValidationException("provided code is not valid");
      }
      $this->user->setValid(true);
      $this->update();
      return true;
  }
  

    // the content is non pattern , it needs refactor
    // this is for test
    function sendCode(string $code) {
        $email = new Email();
        $email->subject("code validation")
        ->html("<p>the code validation is : </p><h1>$code</h1>")
        ->to($this->user->getEmail())
        ->from("validation@alwaleed.com");
        $this->sender->setEmail($email);
        $this->sender->send();
    }

    private function update(){
        $this->em->persist($this->user);
        $this->em->flush();
    }

    function isValid() : bool {
        return $this->user->isValid();
    }
    
    function isAdmin() : bool {
        return $this->user->isAdmin();
    }

    /**
     * @return OrderGroup order group class with default delivery data set by user or new delivery data added to user
     * and the new Order group class
     */
    private function createOrderGroup() {

        // new order group
        $orderGroup = $this->orderGroupRepo->createOrderGroup();

        // cart
        $orderGroup->setCart($this->getCartService()->getCart());        

        $this->user->addOrderGroup($orderGroup);
        
        // update user in database
        $this->update();

        return $orderGroup;
    }

    function unDefaultDeliveryData() {
        $qb =$this->em->createQueryBuilder(); 
        $results  = $qb->select("dd")
        ->from(DeliveryData::class,"dd")
        ->innerJoin("dd.user","u")
        ->where("u.id = :userId")
        ->setParameter("userId",$this->getUser()->getId())
        ->getQuery()
        ->getResult();

        /** @var DeliveryData $deliveryData */
        foreach($results as $deliveryData) {
            $dd = $deliveryData->setDefaultData(false);
            $this->em->persist($dd);
        }

        $this->em->flush();
    }


    function getDeliveryData() {
        $deliveryData = $this->user->getDefaultDeliveryData();
        
        if( !$deliveryData  ) {
            // if default delivery data not exist
            $deliveryData = $this->deliveryDataRepository->createDeliveryData( $this->user );
        } 
        return $deliveryData;
    }

    function getDefaultDeliveryData() {
        return $this->user->getDefaultDeliveryData() ?? false;
    }


    public function getCartService() : CartService {
        $cart = $this->user->getNonProcessedCart();
        if($cart == null ) $cart = $this->cartsRepository->createCart($this->user);
        return $this->cartServiceFactory->make($cart) ;
    }

    function getOrderGroupService() : OrderGroupService{
        if(! $orderGroup = $this->user->getUnInitializedOrderGroup()){
            $orderGroup = $this->createOrderGroup();
        };

        return $this->orderGroupServiceFactory->make($orderGroup);
    }

    function toggleLikeProduct($_product) {
        if($_product instanceof Product) {
            $product = $_product;
        } else if (is_int($_product)) {
            $product = $this->productsRepository->find($_product);
            if(!$product) throw new EntityNotExistException("product not exist");
        } else {
            throw new \InvalidArgumentException("product parameter must be of type int or Product class");
        }
        if($this->user->getFavorites()->contains($product)) {
            $this->user->removeFavoriteProduct($product);
        } else {
            $this->user->addProductToFavorites($product);
        }
        $this->update();
        return $product;
    }

    function getUser() {
        return $this->user;
    }

    /**
     * @throws EntityNotExistException
     */
    function inTheInterestList($product) {
        if(is_int($product)) {
            $product = $this->productsRepository->find($product);
            if(!$product) throw new EntityNotExistException("product not exist");
        } else if ($product instanceof \Product){

        } else {
            throw new \InvalidArgumentException("\$product parameter must be of int type or Product entity class");
        }
        return $this->user->getFavorites()->contains($product);
    }
}