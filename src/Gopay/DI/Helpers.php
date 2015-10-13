<?php

namespace Markette\Gopay\DI;

use Markette\Gopay\Form\Binder;
use Markette\Gopay\Service\AbstractPaymentService;
use Nette\DI\Container;
use Nette\Forms\Container as FormContainer;
use ReflectionClass;

/**
 * Extension helpers
 */
class Helpers
{

    /**
     * Registers 'addPaymentButtons' & 'addPaymentButton' methods to form using DI container
     *
     * @param Container $container
     */
    public static function registerAddPaymentButtonsUsingDependencyContainer(Container $container)
    {
        $binder = $container->getByType(Binder::class);
        $services = $container->findByType(AbstractPaymentService::class);

        foreach ($services as $service) {
            self::registerAddPaymentButtons($binder, $service);
        }
    }

    /**
     * Registers 'add*Buttons' & 'add*Button' methods to form
     *
     * @param Binder $binder
     * @param AbstractPaymentService $service
     */
    public static function registerAddPaymentButtons(Binder $binder, AbstractPaymentService $service)
    {
        $class = new ReflectionClass($service);
        $method = lcfirst(str_replace('Service', '', $class->getShortName()));
        FormContainer::extensionMethod('add' . $method . 'Buttons', function ($container, $callbacks) use ($binder, $service) {
            $binder->bindPaymentButtons($service, $container, $callbacks);
        });
        FormContainer::extensionMethod('add' . $method . 'Button', function ($container, $channel, $callback = NULL) use ($binder, $service) {
            return $binder->bindPaymentButton($channel, $container, $callback);
        });
    }

}