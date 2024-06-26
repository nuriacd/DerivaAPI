<?php

namespace Proxies\__CG__\App\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Client extends \App\Entity\Client implements \Doctrine\ORM\Proxy\InternalProxy
{
    use \Symfony\Component\VarExporter\LazyGhostTrait {
        initializeLazyObject as __load;
        setLazyObjectAsInitialized as public __setInitialized;
        isLazyObjectInitialized as private;
        createLazyGhost as private;
        resetLazyObject as private;
    }

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'orders' => [parent::class, 'orders', null],
        "\0".'App\\Entity\\User'."\0".'email' => ['App\\Entity\\User', 'email', null],
        "\0".'App\\Entity\\User'."\0".'id' => ['App\\Entity\\User', 'id', null],
        "\0".'App\\Entity\\User'."\0".'name' => ['App\\Entity\\User', 'name', null],
        "\0".'App\\Entity\\User'."\0".'password' => ['App\\Entity\\User', 'password', null],
        "\0".'App\\Entity\\User'."\0".'phone' => ['App\\Entity\\User', 'phone', null],
        "\0".'App\\Entity\\User'."\0".'roles' => ['App\\Entity\\User', 'roles', null],
        'email' => ['App\\Entity\\User', 'email', null],
        'id' => ['App\\Entity\\User', 'id', null],
        'name' => ['App\\Entity\\User', 'name', null],
        'orders' => [parent::class, 'orders', null],
        'password' => ['App\\Entity\\User', 'password', null],
        'phone' => ['App\\Entity\\User', 'phone', null],
        'roles' => ['App\\Entity\\User', 'roles', null],
    ];

    public function __isInitialized(): bool
    {
        return isset($this->lazyObjectState) && $this->isLazyObjectInitialized();
    }

    public function __serialize(): array
    {
        $properties = (array) $this;
        unset($properties["\0" . self::class . "\0lazyObjectState"]);

        return $properties;
    }
}
