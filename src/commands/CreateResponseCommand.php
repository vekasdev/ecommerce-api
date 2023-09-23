<?php


namespace Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateResponseCommand extends Command {
    function __construct(private string $responsesPath,private string $suffix,public string $namespace){
        parent::__construct();
    }
    protected static $defaultName = "mkres";
    protected static $defaultDescription = "creating response class";
    protected function configure()
    {
        $this->addOption("name",null,InputOption::VALUE_REQUIRED,"the name of the response");
    }
    function execute(InputInterface $input, OutputInterface $output)
    {
        $response_name = $input->getOption("name");
        $state = file_put_contents($this->responsesPath."/$response_name".$this->suffix.".php",
            $this->getClassContents($response_name)
    );
        return $state ? COMMAND::SUCCESS : COMMAND::FAILURE;
    }

    function getClassContents($className){
        return '<?php
namespace '.$this->namespace.'
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\ResponseFactory;
use Vekas\ResponseManager\AbstractResponseEntry;

class '.$className.$this->suffix.' extends AbstractResponseEntry {
    public ResponseFactory $responseFactory;
    function __construct(ContainerInterface $containerInterface) {
        parent::__construct($containerInterface);
        $this->responseFactory = $this->container->get(ResponseFactory::class);
    }

    function __invoke($data) : ResponseInterface{
        return $this->responseFactory->createResponse(200);
    }
}';
    }
}