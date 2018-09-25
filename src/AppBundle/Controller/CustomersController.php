<?php

namespace AppBundle\Controller;

use AppBundle\Service\CacheService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CustomersController extends Controller
{
    /**
     * @Route("/customers/")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAction() : JsonResponse
    {
        /** @var CacheService $cacheService */
        $cacheService = $this->get('cache_service');

        // TODO: Implement logic here
        // fetch customers from the cache service.
        $customers = json_decode($cacheService->get('customers'));

        if (empty($customers)) {
            $database = $this->get('database_service')->getDatabase();
            $customers = $database->customers->find();
            $customers = iterator_to_array($customers);

            // add to the cache.
            $cacheService->set('customers', json_encode($customers));
        }

        return new JsonResponse($customers);
    }

    /**
     * @Route("/customers/")
     * @Method("POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postAction(Request $request) : JsonResponse
    {
        $database = $this->get('database_service')->getDatabase();
        $customers = json_decode($request->getContent());

        if (empty($customers)) {
            return new JsonResponse(['status' => 'No donuts for you'], 400);
        }

        $database->customers->insertMany($customers);

        $this->get('cache_service');

        return new JsonResponse(['status' => 'Customers successfully created']);
    }

    /**
     * @Route("/customers/")
     * @Method("DELETE")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction() : JsonResponse
    {
        $database = $this->get('database_service')->getDatabase();
        $database->customers->drop();

        $this->get('cache_service')->del('customers');

        return new JsonResponse(['status' => 'Customers successfully deleted']);
    }
}
