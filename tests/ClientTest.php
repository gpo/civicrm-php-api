<?php

use Circle\CiviCRM\Client;

test('client getHeadersToSend method appropriately determines "basic" or "bearer" based on authentication type', function () {
    $mockClient = mock_client();
    $authKey = 'testKey';

    $basicClient = new Client($mockClient, 'basic', $authKey);
    $jwtClient = new Client($mockClient,'bearer_jwt', $authKey);

    $reflectionClient = new ReflectionClass($basicClient);
    $getHeadersToSend = $reflectionClient->getMethod('getHeadersToSend');

    expect($getHeadersToSend->isPrivate())->toBeTrue();

    $getHeadersToSend->setAccessible(true);
    $basicString = $getHeadersToSend->invoke($basicClient);
    expect($basicString['X-Civi-Auth'])->toContain('Basic');

    $jwtString = $getHeadersToSend->invoke($jwtClient);
    expect($jwtString['X-Civi-Auth'])->toContain('Bearer');
});

test('client throws exception when given invalid authentication type', function () {
    $mockClient = mock_client();
    $validMethod = 'basic';
    $key = 'testKey';

    $invalidMethod = 'wrong';

    expect(fn() => new Client($mockClient, $validMethod, $key))->not->toThrow(RuntimeException::class);
    expect(fn() => new Client($mockClient, $invalidMethod, $key))->toThrow(RuntimeException::class);
});
