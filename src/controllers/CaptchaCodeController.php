<?php


namespace App\controllers;

use App\repositories\RegisterCodesRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Gregwar\Captcha\CaptchaBuilder;
use Psr\Container\ContainerInterface;
use RegisterCode;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class CaptchaCodeController {
    private RegisterCodesRepository $registerCodesRepository;
    private int $expireDate;

    function __construct(
        private EntityManager $entityManager,
        private CaptchaBuilder $captchaBuilder,
        private ContainerInterface $container
    ) {
        $this->registerCodesRepository = $entityManager->getRepository(RegisterCode::class);
        $this->expireDate = $container->get("env")["captcha-code-expire"];
    }

    function requestCaptchaCode(ServerRequest $req, Response $res,$args) {
        // clear the expired others
        $this->registerCodesRepository->clearExpired();

        // get the image
        ob_start();
        $this->captchaBuilder->output();
        $codeImg = ob_get_clean();

        // register it in database
        $string  = $this->captchaBuilder->getPhrase();
        $code    = $this->registerCodesRepository->create($string,$this->expireDate);

        // log it
        $res->getBody()->write($codeImg);
        return $res->withHeader("Content-Type","image/jpeg");
    }

}